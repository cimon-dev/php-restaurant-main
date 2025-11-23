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

        // Expenses aggregated by day
        $sql2 = "SELECT expense_date AS day, SUM(amount) AS expense
                 FROM expense
                 WHERE expense_date BETWEEN ? AND ?
                 GROUP BY expense_date
                 ORDER BY expense_date ASC";
        $stmt2 = $db->prepare($sql2);
        $stmt2->execute([$start, $end]);
        $expenses = $stmt2->fetchAll();

        // Merge into per-day map and ensure all days in range present
        $map = [];
        foreach ($sales as $s) {
            $d = $s['day'];
            $map[$d] = ['revenue' => (float)$s['total'], 'orders' => (int)$s['orders'], 'expense' => 0.0];
        }
        foreach ($expenses as $e) {
            $d = $e['day'];
            if (!isset($map[$d])) $map[$d] = ['revenue' => 0.0, 'orders' => 0, 'expense' => 0.0];
            $map[$d]['expense'] = (float)$e['expense'];
        }

        $period = [];
        $cur = strtotime($start);
        $endTs = strtotime($end);
        while ($cur <= $endTs) {
            $d = date('Y-m-d', $cur);
            if (!isset($map[$d])) $map[$d] = ['revenue' => 0.0, 'orders' => 0, 'expense' => 0.0];
            $period[] = $d;
            $cur = strtotime('+1 day', $cur);
        }

        $totals = ['revenue' => 0.0, 'expense' => 0.0, 'net' => 0.0, 'orders' => 0];
        foreach ($period as $d) {
            $totals['revenue'] += $map[$d]['revenue'];
            $totals['expense'] += $map[$d]['expense'];
            $totals['net'] += ($map[$d]['revenue'] - $map[$d]['expense']);
            $totals['orders'] += $map[$d]['orders'];
        }

        $this->view('report/index', [
            'user' => $user,
            'period' => $period,
            'map' => $map,
            'totals' => $totals,
            'start' => $start,
            'end' => $end
        ]);
    }
}
