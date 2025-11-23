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

    public function __construct()
    {
        $this->model = $this->model('SaleOrder');
        $this->menuModel = $this->model('MenuItem');
        $this->tableModel = $this->model('RestaurantTable');
    }

    public function index()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        $items = $this->model->getAllWithTable();
        $this->view('sale_order/index', ['items' => $items, 'user' => $user]);
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
}
