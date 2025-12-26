<?php

/**
 * SaleOrder Controller - manage sale orders and details
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class SaleOrderController extends Controller
{
    private $model;
    private $menuModel;
    private $tableModel;
    private $recipeModel;

    public function __construct()
    {
        $this->model = $this->model('SaleOrder');
        $this->menuModel = $this->model('MenuItem');
        $this->tableModel = $this->model('RestaurantTable');
        $this->recipeModel = $this->model('Recipe');
    }

    public function index()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

        $baseSql = "SELECT o.*, t.number AS table_number FROM sale_order o LEFT JOIN restaurant_table t ON o.table_id = t.id ORDER BY o.order_time DESC";
        $result = $this->model->paginate($baseSql, [], $page, $per);

        $this->view('sale_order/index', [
            'items' => $result['data'],
            'pagination' => $result['pagination'],
            'baseUrl' => BASE_URL . '/sale_order',
            'user' => $user
        ]);
    }

    public function create()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        $tables = $this->tableModel->all('number', 'ASC');
        $menuItems = $this->menuModel->all('name', 'ASC');
        $this->view('sale_order/create', ['tables' => $tables, 'menuItems' => $menuItems]);
    }

    private function normalizeDatetime($value)
    {
        if (strpos($value, 'T') !== false) {
            return str_replace('T', ' ', $value) . ':00';
        }
        return $value;
    }

    public function store()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        $data = $this->getPost();

        $order_time = isset($data['order_time']) ? $this->normalizeDatetime($data['order_time']) : date('Y-m-d H:i:s');

        $qtys = $data['qty'] ?? [];

        // Build order details from qtys
        $details = [];
        $total = 0;

        foreach ($qtys as $menuId => $qty) {
            $qty = (int)$qty;
            if ($qty <= 0) continue;

            $menu = $this->menuModel->find($menuId);
            if (!$menu) continue;

            $price = $menu['price'];
            $lineTotal = $price * $qty;
            $details[] = ['menu_id' => $menuId, 'qty' => $qty, 'price' => $price];
            $total += $lineTotal;
        }

        if (empty($details)) {
            setFlash('error', 'Vui lòng chọn ít nhất một món');
            $this->redirect('sale_order/create');
            return;
        }

        // Check inventory for all menu items
        $inventoryWarnings = [];
        foreach ($details as $d) {
            $check = $this->recipeModel->checkInventoryForMenu($d['menu_id'], $d['qty']);
            if (!$check['sufficient']) {
                $menu = $this->menuModel->find($d['menu_id']);
                $inventoryWarnings[] = [
                    'menu_name' => $menu['name'],
                    'menu_qty' => $d['qty'],
                    'missing' => $check['missing']
                ];
            }
        }

        if (!empty($inventoryWarnings)) {
            // Store the warnings in session for display
            $_SESSION['inventory_warnings'] = $inventoryWarnings;
            setFlash('warning', 'Cảnh báo: Một số nguyên liệu không đủ. Vui lòng kiểm tra lại!');
            $this->redirect('sale_order/create');
            return;
        }

        $order = [
            'table_id' => !empty($data['table_id']) ? $data['table_id'] : null,
            'waiter_id' => $user['id'] ?? null,
            'cashier_id' => null,
            'order_time' => $order_time,
            'status' => $data['status'] ?? 'open',
            'discount' => isset($data['discount']) ? (float)$data['discount'] : 0,
            'vat_rate' => isset($data['vat_rate']) ? (float)$data['vat_rate'] : 0,
            'total_amount' => $total
        ];

        $orderId = $this->model->insert($order);

        // insert details
        $db = getDB();
        $stmt = $db->prepare('INSERT INTO sale_order_detail (sale_order_id, menu_id, qty, price) VALUES (?, ?, ?, ?)');
        foreach ($details as $d) {
            $stmt->execute([$orderId, $d['menu_id'], $d['qty'], $d['price']]);
        }

        // Update table status to occupied if table_id is provided
        if (!empty($order['table_id'])) {
            $this->tableModel->update($order['table_id'], ['status' => 'occupied']);
            logAudit('update', 'restaurant_table', "Bàn #{$order['table_id']} đã bị chiếm dụng bởi đơn hàng #{$orderId}");
        }

        setFlash('success', 'Tạo đơn hàng thành công');
        $this->redirect('sale_order');
    }

    public function edit($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('sale_order');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'Đơn hàng không tồn tại');
            $this->redirect('sale_order');
            return;
        }

        $details = $this->model->getDetails($id);
        $detailMap = [];
        foreach ($details as $d) {
            $detailMap[$d['menu_id']] = $d['qty'];
        }

        $tables = $this->tableModel->all('number', 'ASC');
        $menuItems = $this->menuModel->all('name', 'ASC');

        $this->view('sale_order/edit', ['item' => $item, 'details' => $details, 'detailMap' => $detailMap, 'tables' => $tables, 'menuItems' => $menuItems]);
    }

    public function update($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        if (!$id) {
            $this->redirect('sale_order');
            return;
        }

        $data = $this->getPost();

        $order_time = isset($data['order_time']) ? $this->normalizeDatetime($data['order_time']) : date('Y-m-d H:i:s');
        $qtys = $data['qty'] ?? [];

        $details = [];
        $total = 0;

        foreach ($qtys as $menuId => $qty) {
            $qty = (int)$qty;
            if ($qty <= 0) continue;
            $menu = $this->menuModel->find($menuId);
            if (!$menu) continue;
            $price = $menu['price'];
            $lineTotal = $price * $qty;
            $details[] = ['menu_id' => $menuId, 'qty' => $qty, 'price' => $price];
            $total += $lineTotal;
        }

        if (empty($details)) {
            setFlash('error', 'Vui lòng chọn ít nhất một món');
            $this->redirect('sale_order/edit/' . $id);
            return;
        }

        $order = [
            'table_id' => !empty($data['table_id']) ? $data['table_id'] : null,
            'waiter_id' => $user['id'] ?? null,
            'order_time' => $order_time,
            'status' => $data['status'] ?? 'open',
            'discount' => isset($data['discount']) ? (float)$data['discount'] : 0,
            'vat_rate' => isset($data['vat_rate']) ? (float)$data['vat_rate'] : 0,
            'total_amount' => $total
        ];

        // replace details
        $db = getDB();
        $del = $db->prepare('DELETE FROM sale_order_detail WHERE sale_order_id = ?');
        $del->execute([$id]);

        $stmt = $db->prepare('INSERT INTO sale_order_detail (sale_order_id, menu_id, qty, price) VALUES (?, ?, ?, ?)');
        foreach ($details as $d) {
            $stmt->execute([$id, $d['menu_id'], $d['qty'], $d['price']]);
        }

        $this->model->update($id, $order);

        setFlash('success', 'Cập nhật đơn hàng thành công');
        $this->redirect('sale_order');
    }

    public function delete($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('sale_order');
            return;
        }

        $db = getDB();
        $del = $db->prepare('DELETE FROM sale_order_detail WHERE sale_order_id = ?');
        $del->execute([$id]);

        $this->model->delete($id);
        setFlash('success', 'Xóa đơn hàng thành công');
        $this->redirect('sale_order');
    }

    /**
     * Đánh dấu đơn hàng là hoàn thành (served)
     */
    public function complete($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('sale_order');
            return;
        }

        // Check if order exists
        $order = $this->model->find($id);
        if (!$order) {
            setFlash('error', 'Đơn hàng không tồn tại');
            $this->redirect('sale_order');
            return;
        }

        // Update status to served
        $this->model->update($id, ['status' => 'served']);

        // Auto create inventory_issue (xuất kho) from order details
        $this->createInventoryIssueFromOrder($id, $user);

        // Log audit
        logAudit('update', 'sale_order', "Đánh dấu đơn #$id là hoàn thành");

        setFlash('success', 'Đơn hàng đã được đánh dấu hoàn thành. Chờ thanh toán.');
        $this->redirect('sale_order');
    }

    /**
     * Thanh toán đơn hàng
     */
    public function pay($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('sale_order');
            return;
        }

        // Check if order exists
        $order = $this->model->find($id);
        if (!$order) {
            setFlash('error', 'Đơn hàng không tồn tại');
            $this->redirect('sale_order');
            return;
        }

        // Update status to paid
        $this->model->update($id, [
            'status' => 'paid',
            'cashier_id' => $user['id']
        ]);

        // Free up table if it exists
        if (!empty($order['table_id'])) {
            $this->tableModel->update($order['table_id'], ['status' => 'free']);
            logAudit('update', 'restaurant_table', "Bàn #{$order['table_id']} đã được giải phóng sau thanh toán đơn hàng #{$id}");
        }

        // Log audit
        logAudit('update', 'sale_order', "Thanh toán đơn #$id - Số tiền: " . $order['total_amount']);

        setFlash('success', 'Thanh toán đơn hàng thành công. Cảm ơn quý khách!');
        $this->redirect('sale_order');
    }

    /**
     * Hủy đơn hàng
     */
    public function cancel($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('sale_order');
            return;
        }

        // Check if order exists
        $order = $this->model->find($id);
        if (!$order) {
            setFlash('error', 'Đơn hàng không tồn tại');
            $this->redirect('sale_order');
            return;
        }

        // Check if order is already paid
        if ($order['status'] === 'paid') {
            setFlash('error', 'Không thể hủy đơn hàng đã thanh toán');
            $this->redirect('sale_order');
            return;
        }

        // Update status to cancel
        $this->model->update($id, ['status' => 'cancel']);

        // Free up table if it exists
        if (!empty($order['table_id'])) {
            $this->tableModel->update($order['table_id'], ['status' => 'free']);
            logAudit('update', 'restaurant_table', "Bàn #{$order['table_id']} đã được giải phóng khi hủy đơn hàng #{$id}");
        }

        // Log audit
        logAudit('update', 'sale_order', "Hủy đơn hàng #$id");

        setFlash('success', 'Đơn hàng đã bị hủy');
        $this->redirect('sale_order');
    }

    /**
     * Thêm món ăn vào đơn hàng
     */
    public function addItem($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('sale_order');
            return;
        }

        // Check if order exists
        $order = $this->model->find($id);
        if (!$order) {
            setFlash('error', 'Đơn hàng không tồn tại');
            $this->redirect('sale_order');
            return;
        }

        // Only allow adding items to open orders
        if ($order['status'] !== 'open') {
            setFlash('error', 'Chỉ có thể thêm món ăn vào đơn đang phục vụ');
            $this->redirect('sale_order');
            return;
        }

        $menuItems = $this->menuModel->all('name', 'ASC');
        $this->view('sale_order/addItem', [
            'order' => $order,
            'menuItems' => $menuItems
        ]);
    }

    /**
     * Lưu các món ăn được thêm vào đơn hàng
     */
    public function saveAddItems($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('sale_order');
            return;
        }

        // Check if order exists
        $order = $this->model->find($id);
        if (!$order) {
            setFlash('error', 'Đơn hàng không tồn tại');
            $this->redirect('sale_order');
            return;
        }

        // Get items from request
        $itemsJson = $_POST['items'] ?? '[]';
        $items = json_decode($itemsJson, true);

        if (empty($items)) {
            setFlash('error', 'Không có món ăn nào được chọn');
            $this->redirect('sale_order/addItem/' . $id);
            return;
        }

        try {
            $db = getDB();
            $totalAdded = 0;
            $addedItems = [];

            // Insert each item
            foreach ($items as $item) {
                $stmt = $db->prepare('INSERT INTO sale_order_detail (sale_order_id, menu_id, qty, price, status) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([
                    $id,
                    $item['menu_id'],
                    $item['qty'],
                    $item['price'],
                    'ordered'
                ]);

                $totalAdded += $item['price'] * $item['qty'];
                $addedItems[] = $item['name'] . ' x' . $item['qty'];
            }

            // Update order total
            $newTotal = $order['total_amount'] + $totalAdded;
            $this->model->update($id, ['total_amount' => $newTotal]);

            // Log audit
            logAudit('update', 'sale_order', "Thêm món ăn vào đơn #$id: " . implode(', ', $addedItems));

            setFlash('success', 'Đã thêm ' . count($items) . ' món ăn vào đơn hàng. Tổng: ' . number_format($totalAdded, 0) . ' đ');
            $this->redirect('sale_order');
        } catch (Exception $e) {
            error_log("Error adding items to order: " . $e->getMessage());
            setFlash('error', 'Có lỗi xảy ra khi thêm món ăn');
            $this->redirect('sale_order/addItem/' . $id);
        }
    }

    /**
     * Helper: Automatically create inventory_issue from order details
     * When order is marked as completed (served), deduct ingredients from stock
     */
    private function createInventoryIssueFromOrder($orderId, $user)
    {
        try {
            $db = getDB();

            // Get order details
            $stmt = $db->prepare('SELECT sale_order_detail.*, recipe.ingredient_id, recipe.qty as ingredient_qty 
                                  FROM sale_order_detail
                                  LEFT JOIN recipe ON sale_order_detail.menu_id = recipe.menu_id
                                  WHERE sale_order_detail.sale_order_id = ?');
            $stmt->execute([$orderId]);
            $details = $stmt->fetchAll();

            if (empty($details)) {
                return;
            }

            // Group ingredients by ID and calculate total qty needed
            $ingredientMap = [];
            foreach ($details as $detail) {
                if (!$detail['ingredient_id'] || !$detail['ingredient_qty']) {
                    continue;
                }
                $ing_id = $detail['ingredient_id'];
                $needed_qty = $detail['ingredient_qty'] * $detail['qty']; // qty from recipe * qty ordered

                if (!isset($ingredientMap[$ing_id])) {
                    $ingredientMap[$ing_id] = 0;
                }
                $ingredientMap[$ing_id] += $needed_qty;
            }

            if (empty($ingredientMap)) {
                return;
            }

            // Create inventory_issue record
            $stmt_issue = $db->prepare('INSERT INTO inventory_issue (created_by, issue_type, issue_date, note) 
                                        VALUES (?, ?, ?, ?)');
            $stmt_issue->execute([$user['id'] ?? null, 'sale', date('Y-m-d'), "Auto-generated from sale order #$orderId"]);
            $issueId = $db->lastInsertId();

            // Insert issue details and inventory logs
            $stmt_detail = $db->prepare('INSERT INTO inventory_issue_detail (issue_id, ingredient_id, qty) 
                                         VALUES (?, ?, ?)');
            $stmt_log = $db->prepare('INSERT INTO inventory_log (ingredient_id, qty_change, type, related_id, created_at) 
                                      VALUES (?, ?, ?, ?, NOW())');

            foreach ($ingredientMap as $ing_id => $qty) {
                $stmt_detail->execute([$issueId, $ing_id, $qty]);
                $stmt_log->execute([$ing_id, -$qty, 'issue', $issueId]);
            }

            logAudit('create', 'inventory_issue', "Auto-created phiếu xuất kho #$issueId từ đơn hàng #$orderId");
        } catch (Exception $e) {
            error_log("Error creating inventory issue from order: " . $e->getMessage());
            // Don't throw error, just log it
        }
    }
}
