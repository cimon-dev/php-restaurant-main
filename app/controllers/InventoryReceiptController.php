<?php

/**
 * InventoryReceipt Controller - manage inventory receipts and details
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class InventoryReceiptController extends Controller
{
    private $model;
    private $ingredientModel;

    public function __construct()
    {
        $this->model = $this->model('InventoryReceipt');
        $this->ingredientModel = $this->model('Ingredient');
    }

    public function index()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        $items = $this->model->getAllWithCreator();
        $this->view('inventory_receipt/index', ['items' => $items, 'user' => $user]);
    }

    public function create()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        $ingredients = $this->ingredientModel->all('name', 'ASC');

        // Quick restock from stock_report
        $quickIngredientId = isset($_GET['ingredient_id']) ? (int)$_GET['ingredient_id'] : null;
        $quickQty = isset($_GET['qty']) ? (int)$_GET['qty'] : null;
        $quickIngredient = null;
        if ($quickIngredientId) {
            $quickIngredient = $this->ingredientModel->find($quickIngredientId);
        }

        $this->view('inventory_receipt/create', [
            'ingredients' => $ingredients,
            'quickIngredient' => $quickIngredient,
            'quickQty' => $quickQty
        ]);
    }

    /**
     * Create receipt from restock cart (multiple items)
     */
    public function create_from_restock()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        $ingredients = $this->ingredientModel->all('name', 'ASC');

        // Restock cart is in sessionStorage (client-side), we'll pass it to view
        // View will pre-populate form from JavaScript/POST

        $this->view('inventory_receipt/create', [
            'ingredients' => $ingredients,
            'fromRestock' => true,
            'quickIngredient' => null,
            'quickQty' => null
        ]);
    }

    public function store()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        $data = $this->getPost();
        $required = ['receipt_date'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('inventory_receipt/create');
            return;
        }

        $receipt = [
            'created_by' => $user['id'] ?? null,
            'supplier' => $data['supplier'] ?? null,
            'receipt_date' => $data['receipt_date'],
            'status' => 'pending',
            'note' => $data['note'] ?? null
        ];

        $receiptId = $this->model->insert($receipt);

        // insert details arrays: ingredient_id[], qty[], unit_price[]
        $ingredientIds = $data['ingredient_id'] ?? [];
        $qtys = $data['qty'] ?? [];
        $unitPrices = $data['unit_price'] ?? [];

        $db = getDB();
        $stmt = $db->prepare('INSERT INTO inventory_receipt_detail (receipt_id, ingredient_id, qty, unit_price) VALUES (?, ?, ?, ?)');

        for ($i = 0; $i < count($ingredientIds); $i++) {
            $ing = $ingredientIds[$i];
            $q = isset($qtys[$i]) ? (int)$qtys[$i] : 0;
            $p = isset($unitPrices[$i]) ? (float)$unitPrices[$i] : 0;
            if (empty($ing) || $q <= 0) continue;
            $stmt->execute([$receiptId, $ing, $q, $p]);
            // Optionally update ingredient purchase_price or stock later
        }

        setFlash('success', 'Tạo phiếu nhập kho thành công');
        $this->redirect('inventory_receipt');
    }

    public function edit($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('inventory_receipt');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'Phiếu không tồn tại');
            $this->redirect('inventory_receipt');
            return;
        }

        $details = $this->model->getDetails($id);
        $ingredients = $this->ingredientModel->all('name', 'ASC');

        $this->view('inventory_receipt/edit', ['item' => $item, 'details' => $details, 'ingredients' => $ingredients]);
    }

    public function update($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        if (!$id) {
            $this->redirect('inventory_receipt');
            return;
        }

        $data = $this->getPost();

        $required = ['receipt_date'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('inventory_receipt/edit/' . $id);
            return;
        }

        $receipt = [
            'supplier' => $data['supplier'] ?? null,
            'receipt_date' => $data['receipt_date'],
            'note' => $data['note'] ?? null
        ];

        $this->model->update($id, $receipt);

        // replace details
        $db = getDB();
        $del = $db->prepare('DELETE FROM inventory_receipt_detail WHERE receipt_id = ?');
        $del->execute([$id]);

        $ingredientIds = $data['ingredient_id'] ?? [];
        $qtys = $data['qty'] ?? [];
        $unitPrices = $data['unit_price'] ?? [];

        $stmt = $db->prepare('INSERT INTO inventory_receipt_detail (receipt_id, ingredient_id, qty, unit_price) VALUES (?, ?, ?, ?)');
        for ($i = 0; $i < count($ingredientIds); $i++) {
            $ing = $ingredientIds[$i];
            $q = isset($qtys[$i]) ? (int)$qtys[$i] : 0;
            $p = isset($unitPrices[$i]) ? (float)$unitPrices[$i] : 0;
            if (empty($ing) || $q <= 0) continue;
            $stmt->execute([$id, $ing, $q, $p]);
        }

        setFlash('success', 'Cập nhật phiếu nhập thành công');
        $this->redirect('inventory_receipt');
    }

    public function delete($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('inventory_receipt');
            return;
        }

        $db = getDB();
        $del = $db->prepare('DELETE FROM inventory_receipt_detail WHERE receipt_id = ?');
        $del->execute([$id]);

        $this->model->delete($id);
        setFlash('success', 'Xóa phiếu nhập thành công');
        $this->redirect('inventory_receipt');
    }

    /**
     * Complete receipt and add to inventory (create inventory_log entries)
     */
    public function complete($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('inventory_receipt');
            return;
        }

        $db = getDB();

        // Get receipt
        $receipt = $this->model->find($id);
        if (!$receipt) {
            setFlash('error', 'Phiếu không tồn tại');
            $this->redirect('inventory_receipt');
            return;
        }

        // If already completed, skip
        if ($receipt['status'] === 'completed') {
            setFlash('warning', 'Phiếu này đã hoàn thành rồi');
            $this->redirect('inventory_receipt');
            return;
        }

        // Get all details
        $details = $this->model->getDetails($id);

        // Create inventory_log entries for each item
        $stmtLog = $db->prepare('INSERT INTO inventory_log (ingredient_id, qty_change, type, related_id, note, created_by) VALUES (?, ?, ?, ?, ?, ?)');

        foreach ($details as $detail) {
            $stmtLog->execute([
                $detail['ingredient_id'],
                (int)$detail['qty'],  // qty_change (positive for receipt)
                'receipt',            // type
                $id,                  // related_id (receipt id)
                'Nhập kho từ phiếu #' . $id,
                $user['id'] ?? null
            ]);
        }

        // Update receipt status to completed
        $this->model->update($id, ['status' => 'completed']);

        setFlash('success', 'Hoàn thành phiếu nhập kho - Số lượng đã được cập nhật');
        $this->redirect('inventory_receipt');
    }
}
