<?php

/**
 * Dashboard Controller
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class DashboardController extends Controller
{

    public function index()
    {
        // Check authentication
        $user = JWT::getCurrentUser();

        if (!$user) {
            $this->redirect('auth/login');
            return;
        }
        // load models
        $ingredientModel = $this->model('Ingredient');
        $menuModel = $this->model('MenuItem');
        $tableModel = $this->model('RestaurantTable');
        $orderModel = $this->model('SaleOrder');
        $receiptModel = $this->model('InventoryReceipt');
        $issueModel = $this->model('InventoryIssue');
        $expenseModel = $this->model('Expense');
        $userModel = $this->model('User');

        // counts
        $counts = [
            'ingredients' => $ingredientModel->count(),
            'menu_items' => $menuModel->count(),
            'tables' => $tableModel->count(),
            'users' => $userModel->count(),
            'receipts' => $receiptModel->count(),
            'issues' => $issueModel->count(),
            'expenses' => $expenseModel->count()
        ];

        $db = getDB();
        // today's orders count and today's revenue (sum of total_amount)
        $today = date('Y-m-d');
        $stmt = $db->prepare("SELECT COUNT(*) as cnt, IFNULL(SUM(total_amount),0) as revenue FROM sale_order WHERE DATE(order_time) = ?");
        $stmt->execute([$today]);
        $row = $stmt->fetch();
        $todayOrders = (int)($row['cnt'] ?? 0);
        $todayRevenue = (float)($row['revenue'] ?? 0.0);

        // recent activity: latest 5 orders and latest 5 expenses
        $recentOrders = $orderModel->query("SELECT o.id, o.order_time, o.total_amount, t.number as table_number FROM sale_order o LEFT JOIN restaurant_table t ON o.table_id = t.id ORDER BY o.order_time DESC LIMIT 5");
        $recentExpenses = $expenseModel->query("SELECT * FROM expense ORDER BY expense_date DESC LIMIT 5");

        $this->view('dashboard/index', [
            'user' => $user,
            'counts' => $counts,
            'todayOrders' => $todayOrders,
            'todayRevenue' => $todayRevenue,
            'recentOrders' => $recentOrders,
            'recentExpenses' => $recentExpenses
        ]);
    }
}
