<?php

/**
 * InventoryIssue Controller - manage inventory issues (xuất kho) and details
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class InventoryIssueController extends Controller
{
    private $model;
    private $ingredientModel;

    public function __construct()
    {
        $this->model = $this->model('InventoryIssue');
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
        $this->view('inventory_issue/index', ['items' => $items, 'user' => $user]);
    }

    public function create()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        $ingredients = $this->ingredientModel->all('name', 'ASC');
        $this->view('inventory_issue/create', ['ingredients' => $ingredients]);
    }

    public function store()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        $data = $this->getPost();
        $required = ['issue_date'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('inventory_issue/create');
            return;
        }

        $issue = [
            'created_by' => $user['id'] ?? null,
            'issue_type' => $data['issue_type'] ?? 'manual',
            'issue_date' => $data['issue_date'],
            'note' => $data['note'] ?? null
        ];

        $issueId = $this->model->insert($issue);

        // insert details arrays: ingredient_id[], qty[]
        $ingredientIds = $data['ingredient_id'] ?? [];
        $qtys = $data['qty'] ?? [];

        $db = getDB();
        $stmt = $db->prepare('INSERT INTO inventory_issue_detail (issue_id, ingredient_id, qty) VALUES (?, ?, ?)');

        for ($i = 0; $i < count($ingredientIds); $i++) {
            $ing = $ingredientIds[$i];
            $q = isset($qtys[$i]) ? (int)$qtys[$i] : 0;
            if (empty($ing) || $q <= 0) continue;
            $stmt->execute([$issueId, $ing, $q]);
            // Optionally update ingredient stock or log to inventory_log
        }

        setFlash('success', 'Tạo phiếu xuất kho thành công');
        $this->redirect('inventory_issue');
    }

    public function edit($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('inventory_issue');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'Phiếu không tồn tại');
            $this->redirect('inventory_issue');
            return;
        }

        $details = $this->model->getDetails($id);
        $ingredients = $this->ingredientModel->all('name', 'ASC');

        $this->view('inventory_issue/edit', ['item' => $item, 'details' => $details, 'ingredients' => $ingredients]);
    }

    public function update($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        if (!$id) {
            $this->redirect('inventory_issue');
            return;
        }

        $data = $this->getPost();

        $required = ['issue_date'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('inventory_issue/edit/' . $id);
            return;
        }

        $issue = [
            'issue_type' => $data['issue_type'] ?? 'manual',
            'issue_date' => $data['issue_date'],
            'note' => $data['note'] ?? null
        ];

        $this->model->update($id, $issue);

        // replace details
        $db = getDB();
        $del = $db->prepare('DELETE FROM inventory_issue_detail WHERE issue_id = ?');
        $del->execute([$id]);

        $ingredientIds = $data['ingredient_id'] ?? [];
        $qtys = $data['qty'] ?? [];

        $stmt = $db->prepare('INSERT INTO inventory_issue_detail (issue_id, ingredient_id, qty) VALUES (?, ?, ?)');
        for ($i = 0; $i < count($ingredientIds); $i++) {
            $ing = $ingredientIds[$i];
            $q = isset($qtys[$i]) ? (int)$qtys[$i] : 0;
            if (empty($ing) || $q <= 0) continue;
            $stmt->execute([$id, $ing, $q]);
        }

        setFlash('success', 'Cập nhật phiếu xuất thành công');
        $this->redirect('inventory_issue');
    }

    public function delete($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('inventory_issue');
            return;
        }

        $db = getDB();
        $del = $db->prepare('DELETE FROM inventory_issue_detail WHERE issue_id = ?');
        $del->execute([$id]);

        $this->model->delete($id);
        setFlash('success', 'Xóa phiếu xuất thành công');
        $this->redirect('inventory_issue');
    }

    /**
     * Complete issue and deduct from inventory (create inventory_log entries)
     */
    public function complete($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('inventory_issue');
            return;
        }

        $db = getDB();

        // Get issue
        $issue = $this->model->find($id);
        if (!$issue) {
            setFlash('error', 'Phiếu không tồn tại');
            $this->redirect('inventory_issue');
            return;
        }

        // Get all details
        $details = $this->model->getDetails($id);

        // Create inventory_log entries for each item (negative for issue)
        $stmtLog = $db->prepare('INSERT INTO inventory_log (ingredient_id, qty_change, type, related_id, note, created_by) VALUES (?, ?, ?, ?, ?, ?)');

        $issueTypeMap = [
            'sale' => 'issue',
            'manual' => 'issue',
            'waste' => 'expire'
        ];
        $logType = $issueTypeMap[$issue['issue_type']] ?? 'issue';

        foreach ($details as $detail) {
            $stmtLog->execute([
                $detail['ingredient_id'],
                -(int)$detail['qty'],  // qty_change (negative for issue)
                $logType,              // type (issue or expire)
                $id,                   // related_id (issue id)
                'Xuất kho từ phiếu #' . $id . ' (' . $issue['issue_type'] . ')',
                $user['id'] ?? null
            ]);
        }

        setFlash('success', 'Hoàn thành phiếu xuất kho - Số lượng đã được cập nhật');
        $this->redirect('inventory_issue');
    }
}
