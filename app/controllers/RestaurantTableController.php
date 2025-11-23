<?php

/**
 * RestaurantTable Controller - CRUD for restaurant tables
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class RestaurantTableController extends Controller
{

    private $model;

    public function __construct()
    {
        $this->model = $this->model('RestaurantTable');
    }

    public function index()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        $items = $this->model->all('id', 'DESC');
        $this->view('restaurant_table/index', ['items' => $items, 'user' => $user]);
    }

    public function create()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        $this->view('restaurant_table/create');
    }

    public function store()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        $data = $this->getPost();
        $required = ['number'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('restaurant_table/create');
            return;
        }

        if ($this->model->findByNumber($data['number'])) {
            setFlash('error', 'Số bàn đã tồn tại');
            $this->redirect('restaurant_table/create');
            return;
        }

        $insert = [
            'number' => $data['number'],
            'status' => $data['status'] ?? 'free'
        ];

        $this->model->insert($insert);
        setFlash('success', 'Tạo bàn thành công');
        $this->redirect('restaurant_table');
    }

    public function edit($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('restaurant_table');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'Bàn không tồn tại');
            $this->redirect('restaurant_table');
            return;
        }

        $this->view('restaurant_table/edit', ['item' => $item]);
    }

    public function update($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        if (!$id) {
            $this->redirect('restaurant_table');
            return;
        }

        $data = $this->getPost();
        $required = ['number'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('restaurant_table/edit/' . $id);
            return;
        }

        // Check duplicate number
        $existing = $this->model->findByNumber($data['number']);
        if ($existing && $existing['id'] != $id) {
            setFlash('error', 'Số bàn đã tồn tại');
            $this->redirect('restaurant_table/edit/' . $id);
            return;
        }

        $update = [
            'number' => $data['number'],
            'status' => $data['status'] ?? 'free'
        ];

        $this->model->update($id, $update);
        setFlash('success', 'Cập nhật bàn thành công');
        $this->redirect('restaurant_table');
    }

    public function delete($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('restaurant_table');
            return;
        }

        $this->model->delete($id);
        setFlash('success', 'Xóa bàn thành công');
        $this->redirect('restaurant_table');
    }
}
