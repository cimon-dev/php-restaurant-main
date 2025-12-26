<?php

require_once BASE_PATH . '/core/Controller.php';

class PublicOrderController extends Controller
{
    private $tableModel;
    private $menuModel;
    private $orderModel;

    public function __construct()
    {
        $this->tableModel = $this->model('RestaurantTable');
        $this->menuModel = $this->model('MenuItem');
        $this->orderModel = $this->model('SaleOrder');
    }

    // Landing page by token
    public function start()
    {
        $token = $_GET['token'] ?? '';
        if (!$token) {
            $this->view('public_order/error', ['message' => 'Thiếu mã truy cập']);
            return;
        }

        $table = $this->tableModel->findBy('order_token', $token);
        if (!$table) {
            $this->view('public_order/error', ['message' => 'Mã không hợp lệ']);
            return;
        }

        $menus = $this->menuModel->all('name', 'ASC');
        $this->view('public_order/start', [
            'table' => $table,
            'menus' => $menus,
            'token' => $token,
            'orderUrl' => BASE_URL . '/public_order/submit'
        ]);
    }

    // Submit order from public page
    public function submit()
    {
        $token = $_POST['token'] ?? '';
        $name = trim($_POST['customer_name'] ?? '');
        $phone = trim($_POST['customer_phone'] ?? '');
        $items = $_POST['items'] ?? [];

        $table = $this->tableModel->findBy('order_token', $token);
        if (!$table) {
            $this->view('public_order/error', ['message' => 'Mã không hợp lệ']);
            return;
        }

        // Normalize items
        $lineItems = [];
        foreach ($items as $menuId => $qty) {
            $q = (int)$qty;
            if ($q > 0) {
                $lineItems[(int)$menuId] = $q;
            }
        }
        if (empty($lineItems)) {
            $this->view('public_order/error', ['message' => 'Bạn chưa chọn món']);
            return;
        }

        $db = getDB();
        $db->beginTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            $total = 0.0;

            // compute total using menu prices
            $menuIds = array_keys($lineItems);
            if (!empty($menuIds)) {
                $placeholders = implode(',', array_fill(0, count($menuIds), '?'));
                $stmt = $db->prepare("SELECT id, price FROM menu_item WHERE id IN ($placeholders)");
                $stmt->execute($menuIds);
                $prices = [];
                foreach ($stmt->fetchAll() as $row) {
                    $prices[(int)$row['id']] = (float)$row['price'];
                }
                foreach ($lineItems as $mid => $qty) {
                    $total += ($prices[$mid] ?? 0) * $qty;
                }
            }

            // insert order
            $db->prepare("INSERT INTO sale_order (table_id, order_time, status, total_amount, source, customer_name, customer_phone) VALUES (?,?,?,?,?,?,?)")
                ->execute([$table['id'], $now, 'open', $total, 'qr', $name, $phone]);
            $orderId = $db->lastInsertId();

            // insert details
            $insDet = $db->prepare("INSERT INTO sale_order_detail (sale_order_id, menu_id, qty, price, status) VALUES (?,?,?,?, 'ordered')");
            foreach ($lineItems as $mid => $qty) {
                $price = $prices[$mid] ?? 0;
                $insDet->execute([$orderId, $mid, $qty, $price]);
            }

            // mark table occupied
            $this->tableModel->update($table['id'], ['status' => 'occupied']);

            $db->commit();

            $this->view('public_order/success', [
                'orderId' => $orderId,
                'table' => $table
            ]);
        } catch (Exception $e) {
            $db->rollBack();
            $this->view('public_order/error', ['message' => 'Không thể tạo đơn: ' . $e->getMessage()]);
        }
    }
}
