<?php

/**
 * MenuItem Controller - CRUD for menu items
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class MenuItemController extends Controller
{

    private $model;

    public function __construct()
    {
        $this->model = $this->model('MenuItem');
    }

    public function index()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        $items = $this->model->all('id', 'DESC');
        $this->view('menu_item/index', ['items' => $items, 'user' => $user]);
    }

    public function create()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        $this->view('menu_item/create');
    }

    public function store()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        $data = $this->getPost();
        $required = ['code', 'name', 'price'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('menu_item/create');
            return;
        }

        if ($this->model->findByCode($data['code'])) {
            setFlash('error', 'Mã món đã tồn tại');
            $this->redirect('menu_item/create');
            return;
        }

        $insert = [
            'code' => $data['code'],
            'name' => $data['name'],
            'price' => $data['price'],
            'description' => $data['description'] ?? null
        ];

        $this->model->insert($insert);
        setFlash('success', 'Tạo món ăn thành công');
        $this->redirect('menu_item');
    }

    public function edit($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('menu_item');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'Món không tồn tại');
            $this->redirect('menu_item');
            return;
        }

        $this->view('menu_item/edit', ['item' => $item]);
    }

    public function update($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        if (!$id) {
            $this->redirect('menu_item');
            return;
        }

        $data = $this->getPost();
        $required = ['code', 'name', 'price'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('menu_item/edit/' . $id);
            return;
        }

        $existing = $this->model->findByCode($data['code']);
        if ($existing && $existing['id'] != $id) {
            setFlash('error', 'Mã món đã tồn tại');
            $this->redirect('menu_item/edit/' . $id);
            return;
        }

        $update = [
            'code' => $data['code'],
            'name' => $data['name'],
            'price' => $data['price'],
            'description' => $data['description'] ?? null
        ];

        $this->model->update($id, $update);
        setFlash('success', 'Cập nhật món ăn thành công');
        $this->redirect('menu_item');
    }

    public function delete($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('menu_item');
            return;
        }

        $this->model->delete($id);
        setFlash('success', 'Xóa món ăn thành công');
        $this->redirect('menu_item');
    }
}
