<?php

/**
 * Reservation Controller - CRUD for reservations
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class ReservationController extends Controller
{
    private $model;
    private $tableModel;

    public function __construct()
    {
        $this->model = $this->model('Reservation');
        $this->tableModel = $this->model('RestaurantTable');
    }

    public function index()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        $items = $this->model->all('start_time', 'DESC');
        $this->view('reservation/index', ['items' => $items, 'user' => $user]);
    }

    public function create()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        $tables = $this->tableModel->all('number', 'ASC');
        $this->view('reservation/create', ['tables' => $tables]);
    }

    private function normalizeDatetime($value)
    {
        // convert HTML datetime-local format to MySQL DATETIME
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
        $required = ['table_id', 'customer_name', 'start_time', 'end_time'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('reservation/create');
            return;
        }

        $start = $this->normalizeDatetime($data['start_time']);
        $end = $this->normalizeDatetime($data['end_time']);

        if (strtotime($end) <= strtotime($start)) {
            setFlash('error', 'Thời gian kết thúc phải lớn hơn thời gian bắt đầu');
            $this->redirect('reservation/create');
            return;
        }

        // check overlap
        $overlaps = $this->model->findOverlapping($data['table_id'], $start, $end);
        if (!empty($overlaps)) {
            setFlash('error', 'Bàn đã có đặt chỗ trùng thời gian');
            $this->redirect('reservation/create');
            return;
        }

        $insert = [
            'table_id' => $data['table_id'],
            'customer_name' => $data['customer_name'],
            'party_size' => $data['party_size'] ?? 1,
            'start_time' => $start,
            'end_time' => $end,
            'status' => $data['status'] ?? 'pending',
            'created_by' => $user['id'] ?? null
        ];

        $this->model->insert($insert);
        setFlash('success', 'Tạo đặt chỗ thành công');
        $this->redirect('reservation');
    }

    public function edit($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('reservation');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'Đặt chỗ không tồn tại');
            $this->redirect('reservation');
            return;
        }

        $tables = $this->tableModel->all('number', 'ASC');
        $this->view('reservation/edit', ['item' => $item, 'tables' => $tables]);
    }

    public function update($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        if (!$id) {
            $this->redirect('reservation');
            return;
        }

        $data = $this->getPost();
        $required = ['table_id', 'customer_name', 'start_time', 'end_time'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('reservation/edit/' . $id);
            return;
        }

        $start = $this->normalizeDatetime($data['start_time']);
        $end = $this->normalizeDatetime($data['end_time']);

        if (strtotime($end) <= strtotime($start)) {
            setFlash('error', 'Thời gian kết thúc phải lớn hơn thời gian bắt đầu');
            $this->redirect('reservation/edit/' . $id);
            return;
        }

        // check overlap excluding current
        $overlaps = $this->model->findOverlapping($data['table_id'], $start, $end, $id);
        if (!empty($overlaps)) {
            setFlash('error', 'Bàn đã có đặt chỗ trùng thời gian');
            $this->redirect('reservation/edit/' . $id);
            return;
        }

        $update = [
            'table_id' => $data['table_id'],
            'customer_name' => $data['customer_name'],
            'party_size' => $data['party_size'] ?? 1,
            'start_time' => $start,
            'end_time' => $end,
            'status' => $data['status'] ?? 'pending'
        ];

        $this->model->update($id, $update);
        setFlash('success', 'Cập nhật đặt chỗ thành công');
        $this->redirect('reservation');
    }

    public function delete($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('reservation');
            return;
        }

        $this->model->delete($id);
        setFlash('success', 'Xóa đặt chỗ thành công');
        $this->redirect('reservation');
    }
}
