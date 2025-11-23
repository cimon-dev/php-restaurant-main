<?php

/**
 * Expense Controller - CRUD for expenses
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class ExpenseController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = $this->model('Expense');
    }

    public function index()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        $items = $this->model->getAllWithCreator();
        $this->view('expense/index', ['items' => $items, 'user' => $user]);
    }

    public function create()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        $this->view('expense/create', ['user' => $user]);
    }

    public function store()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        $data = $this->getPost();
        $required = ['expense_type', 'amount', 'expense_date'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('expense/create');
            return;
        }

        $insert = [
            'expense_type' => $data['expense_type'],
            'amount' => (float)$data['amount'],
            'description' => $data['description'] ?? null,
            'created_by' => $user['id'] ?? null,
            'expense_date' => $data['expense_date']
        ];

        $this->model->insert($insert);
        setFlash('success', 'Tạo chi phí thành công');
        $this->redirect('expense');
    }

    public function edit($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('expense');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'Chi phí không tồn tại');
            $this->redirect('expense');
            return;
        }

        $this->view('expense/edit', ['item' => $item, 'user' => $user]);
    }

    public function update($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        if (!$id) {
            $this->redirect('expense');
            return;
        }

        $data = $this->getPost();
        $required = ['expense_type', 'amount', 'expense_date'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('expense/edit/' . $id);
            return;
        }

        $update = [
            'expense_type' => $data['expense_type'],
            'amount' => (float)$data['amount'],
            'description' => $data['description'] ?? null,
            'expense_date' => $data['expense_date']
        ];

        $this->model->update($id, $update);
        setFlash('success', 'Cập nhật chi phí thành công');
        $this->redirect('expense');
    }

    public function delete($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('expense');
            return;
        }

        $this->model->delete($id);
        setFlash('success', 'Xóa chi phí thành công');
        $this->redirect('expense');
    }
}
