<?php

/**
 * Report Controller - simple revenue/expense report
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class ReportController extends Controller
{
    public function index()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        // date range from GET or defaults (last 7 days)
        $start = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-6 days'));
        $end = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

        $db = getDB();

        // Sales aggregated by day
        $sql = "SELECT DATE(order_time) AS day, SUM(total_amount) AS total, COUNT(*) AS orders
                FROM sale_order
                WHERE DATE(order_time) BETWEEN ? AND ?
                GROUP BY day
                ORDER BY day ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$start, $end]);
        $sales = $stmt->fetchAll();

        // Fixed expenses (chi phí cố định) aggregated by day
        $sql2 = "SELECT expense_date AS day, SUM(amount) AS fixed_expense
                 FROM expense
                 WHERE expense_date BETWEEN ? AND ?
                 GROUP BY expense_date
                 ORDER BY expense_date ASC";
        $stmt2 = $db->prepare($sql2);
        $stmt2->execute([$start, $end]);
        $expenses = $stmt2->fetchAll();

        // Calculate ingredient costs (chi phí nguyên liệu) for each sale order
        $sql3 = "SELECT DATE(so.order_time) AS day, 
                        SUM(r.qty * i.purchase_price) AS ingredient_cost
                 FROM sale_order so
                 JOIN sale_order_detail sod ON so.id = sod.sale_order_id
                 JOIN recipe r ON r.menu_id = sod.menu_id
                 JOIN ingredient i ON r.ingredient_id = i.id
                 WHERE DATE(so.order_time) BETWEEN ? AND ?
                 GROUP BY day
                 ORDER BY day ASC";
        $stmt3 = $db->prepare($sql3);
        $stmt3->execute([$start, $end]);
        $ingredient_costs = $stmt3->fetchAll();

        // Merge into per-day map and ensure all days in range present
        $map = [];
        foreach ($sales as $s) {
            $d = $s['day'];
            $map[$d] = ['revenue' => (float)$s['total'], 'orders' => (int)$s['orders'], 'fixed_expense' => 0.0, 'ingredient_cost' => 0.0];
        }
        foreach ($expenses as $e) {
            $d = $e['day'];
            if (!isset($map[$d])) $map[$d] = ['revenue' => 0.0, 'orders' => 0, 'fixed_expense' => 0.0, 'ingredient_cost' => 0.0];
            $map[$d]['fixed_expense'] = (float)$e['fixed_expense'];
        }
        foreach ($ingredient_costs as $ic) {
            $d = $ic['day'];
            if (!isset($map[$d])) $map[$d] = ['revenue' => 0.0, 'orders' => 0, 'fixed_expense' => 0.0, 'ingredient_cost' => 0.0];
            $map[$d]['ingredient_cost'] = (float)$ic['ingredient_cost'];
        }

        $period = [];
        $cur = strtotime($start);
        $endTs = strtotime($end);
        while ($cur <= $endTs) {
            $d = date('Y-m-d', $cur);
            if (!isset($map[$d])) $map[$d] = ['revenue' => 0.0, 'orders' => 0, 'fixed_expense' => 0.0, 'ingredient_cost' => 0.0];
            $period[] = $d;
            $cur = strtotime('+1 day', $cur);
        }

        $totals = ['revenue' => 0.0, 'fixed_expense' => 0.0, 'ingredient_cost' => 0.0, 'total_expense' => 0.0, 'net' => 0.0, 'orders' => 0];
        foreach ($period as $d) {
            $totals['revenue'] += $map[$d]['revenue'];
            $totals['fixed_expense'] += $map[$d]['fixed_expense'];
            $totals['ingredient_cost'] += $map[$d]['ingredient_cost'];
            $total_expense = $map[$d]['fixed_expense'] + $map[$d]['ingredient_cost'];
            $totals['total_expense'] += $total_expense;
            $totals['net'] += ($map[$d]['revenue'] - $total_expense);
            $totals['orders'] += $map[$d]['orders'];
        }

        // Apply pagination over the days in period
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
        $totalDays = count($period);
        $pages = max(1, (int)ceil($totalDays / max(1, $per)));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * max(1, $per);
        $pagedPeriod = array_slice($period, $offset, $per);

        // Recompute totals for paged view
        $pagedTotals = ['revenue' => 0.0, 'fixed_expense' => 0.0, 'ingredient_cost' => 0.0, 'total_expense' => 0.0, 'net' => 0.0, 'orders' => 0];
        foreach ($pagedPeriod as $d) {
            $dayFixed = isset($map[$d]['fixed_expense']) ? $map[$d]['fixed_expense'] : 0.0;
            $dayIng = isset($map[$d]['ingredient_cost']) ? $map[$d]['ingredient_cost'] : 0.0;
            $dayRev = isset($map[$d]['revenue']) ? $map[$d]['revenue'] : 0.0;
            $pagedTotals['revenue'] += $dayRev;
            $pagedTotals['fixed_expense'] += $dayFixed;
            $pagedTotals['ingredient_cost'] += $dayIng;
            $totalExp = $dayFixed + $dayIng;
            $pagedTotals['total_expense'] += $totalExp;
            $pagedTotals['net'] += ($dayRev - $totalExp);
            $pagedTotals['orders'] += $map[$d]['orders'] ?? 0;
        }

        $this->view('report/index', [
            'user' => $user,
            'period' => $pagedPeriod,
            'map' => $map,
            'totals' => $pagedTotals,
            'start' => $start,
            'end' => $end,
            'pagination' => [
                'page' => $page,
                'per_page' => $per,
                'total' => $totalDays,
                'pages' => $pages
            ],
            'baseUrl' => BASE_URL . '/report?start_date=' . urlencode($start) . '&end_date=' . urlencode($end)
        ]);
    }

    /**
     * Báo cáo kho - Tình trạng tồn kho, cảnh báo, lịch sử xuất kho
     */
    public function stock_report()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        $db = getDB();

        // Tính toán tồn kho = nhập - xuất
        $sql = "
            SELECT 
                i.id,
                i.code,
                i.name,
                i.unit,
                i.min_stock,
                i.purchase_price,
                COALESCE(SUM(CASE WHEN il.type IN ('receipt','adjust') THEN il.qty_change ELSE 0 END), 0) as total_in,
                COALESCE(SUM(CASE WHEN il.type IN ('issue','expire') THEN il.qty_change ELSE 0 END), 0) as total_out,
                COALESCE(SUM(il.qty_change), 0) as current_qty
            FROM ingredient i
            LEFT JOIN inventory_log il ON i.id = il.ingredient_id
            GROUP BY i.id, i.code, i.name, i.unit, i.min_stock, i.purchase_price
            ORDER BY i.name ASC
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $ingredients = $stmt->fetchAll();

        // Phân loại: sắp hết (quantity <= min_stock), thiếu (quantity <= 0), bình thường
        $critical = [];
        $warning = [];
        $normal = [];

        foreach ($ingredients as $ing) {
            $ing['cost'] = $ing['current_qty'] * ($ing['purchase_price'] ?? 0);
            if ($ing['current_qty'] <= 0) {
                $critical[] = $ing;
            } elseif ($ing['current_qty'] <= ($ing['min_stock'] ?? 0)) {
                $warning[] = $ing;
            } else {
                $normal[] = $ing;
            }
        }

        // Lịch sử xuất kho gần nhất (10 ngày gần đây)
        $sql_issues = "
            SELECT 
                ii.id,
                ii.issue_date,
                ii.issue_type,
                ii.note,
                u.fullname as created_by,
                GROUP_CONCAT(CONCAT(i.name, ' (', iid.qty, ' ', i.unit, ')') SEPARATOR ', ') as details
            FROM inventory_issue ii
            LEFT JOIN inventory_issue_detail iid ON ii.id = iid.issue_id
            LEFT JOIN ingredient i ON iid.ingredient_id = i.id
            LEFT JOIN users u ON ii.created_by = u.id
            WHERE ii.issue_date >= DATE_SUB(CURDATE(), INTERVAL 10 DAY)
            GROUP BY ii.id, ii.issue_date, ii.issue_type, ii.note, u.fullname
            ORDER BY ii.issue_date DESC, ii.id DESC
        ";
        $stmt_issues = $db->prepare($sql_issues);
        $stmt_issues->execute();
        $recent_issues_all = $stmt_issues->fetchAll();

        // paginate recent issues list
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
        $totalIssues = count($recent_issues_all);
        $pages = max(1, (int)ceil($totalIssues / max(1, $per)));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * max(1, $per);
        $recent_issues = array_slice($recent_issues_all, $offset, $per);

        $this->view('report/stock_report', [
            'user' => $user,
            'critical' => $critical,
            'warning' => $warning,
            'normal' => $normal,
            'recent_issues' => $recent_issues,
            'total_critical' => count($critical),
            'total_warning' => count($warning),
            'total_normal' => count($normal),
            'pagination' => [
                'page' => $page,
                'per_page' => $per,
                'total' => $totalIssues,
                'pages' => $pages
            ],
            'baseUrl' => BASE_URL . '/report/stock_report'
        ]);
    }

    /**
     * Thêm xuất kho/adjustment thủ công
     */
    public function add_stock_out()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        // Lấy danh sách nguyên liệu để select
        $db = getDB();
        $sql = "SELECT id, code, name, unit FROM ingredient ORDER BY name ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $ingredients = $stmt->fetchAll();

        $this->view('report/add_stock_out', [
            'user' => $user,
            'ingredients' => $ingredients
        ]);
    }

    /**
     * Lưu xuất kho/adjustment
     */
    public function save_stock_out()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        $data = $this->getPost();
        $db = getDB();

        $issue_type = $data['issue_type'] ?? 'manual'; // waste, manual
        $issue_date = $data['issue_date'] ?? date('Y-m-d');
        $note = $data['note'] ?? '';

        // Insert inventory_issue with status = completed (since we process it immediately)
        $sql = "INSERT INTO inventory_issue (created_by, issue_type, issue_date, status, note) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$user['id'], $issue_type, $issue_date, 'completed', $note]);
        $issue_id = $db->lastInsertId();

        // Insert details từ mảng items
        $items = $data['items'] ?? [];
        $sql_detail = "INSERT INTO inventory_issue_detail (issue_id, ingredient_id, qty) VALUES (?, ?, ?)";
        $stmt_detail = $db->prepare($sql_detail);

        foreach ($items as $item) {
            if (!empty($item['ingredient_id']) && $item['qty'] > 0) {
                $stmt_detail->execute([$issue_id, $item['ingredient_id'], $item['qty']]);

                // Insert inventory_log
                $sql_log = "INSERT INTO inventory_log (ingredient_id, qty_change, type, related_id, created_by) VALUES (?, ?, ?, ?, ?)";
                $stmt_log = $db->prepare($sql_log);
                $stmt_log->execute([$item['ingredient_id'], -$item['qty'], 'issue', $issue_id, $user['id']]);

                logAudit('create', 'inventory_issue', "Xuất kho: {$item['qty']} từ nguyên liệu #{$item['ingredient_id']}");
            }
        }

        setFlash('success', 'Đã ghi nhận xuất kho thành công');
        $this->redirect('report/stock_report');
    }
}
