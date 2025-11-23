<?php

/**
 * User Controller - admin-only user management
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class UserController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = $this->model('User');
    }

    private function requireAdmin()
    {
        $user = JWT::getCurrentUser();
        if (!$user || ($user['role'] ?? '') !== 'admin') {
            $this->redirect('dashboard');
            return false;
        }
        return $user;
    }

    public function index()
    {
        $user = $this->requireAdmin();
        if (!$user) return;

        $items = $this->model->all('id', 'DESC');
        $this->view('user/index', ['items' => $items, 'user' => $user]);
    }

    public function create()
    {
        $user = $this->requireAdmin();
        if (!$user) return;

        $this->view('user/create', ['user' => $user]);
    }

    public function store()
    {
        $user = $this->requireAdmin();
        if (!$user) return;

        $data = $this->getPost();
        $required = ['username', 'password', 'role'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('user/create');
            return;
        }

        // prevent duplicate username
        if ($this->model->findByUsername($data['username'])) {
            setFlash('error', 'Username already exists');
            $this->redirect('user/create');
            return;
        }

        $insert = [
            'username' => $data['username'],
            'password' => $data['password'],
            'fullname' => $data['fullname'] ?? null,
            'role' => $data['role'],
            'active' => isset($data['active']) ? 1 : 0
        ];

        $this->model->createUser($insert);
        setFlash('success', 'Tạo người dùng thành công');
        $this->redirect('user');
    }

    public function edit($id = null)
    {
        $user = $this->requireAdmin();
        if (!$user) return;

        if (!$id) {
            $this->redirect('user');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'Người dùng không tồn tại');
            $this->redirect('user');
            return;
        }

        $this->view('user/edit', ['item' => $item, 'user' => $user]);
    }

    public function update($id = null)
    {
        $user = $this->requireAdmin();
        if (!$user) return;

        if (!$id) {
            $this->redirect('user');
            return;
        }

        $data = $this->getPost();
        $required = ['username', 'role'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('user/edit/' . $id);
            return;
        }

        // prevent duplicate username for other users
        $existing = $this->model->findByUsername($data['username']);
        if ($existing && $existing['id'] != $id) {
            setFlash('error', 'Username already exists');
            $this->redirect('user/edit/' . $id);
            return;
        }

        $update = [
            'username' => $data['username'],
            'password' => $data['password'] ?? null,
            'fullname' => $data['fullname'] ?? null,
            'role' => $data['role'],
            'active' => isset($data['active']) ? 1 : 0
        ];

        $this->model->updateUser($id, $update);
        setFlash('success', 'Cập nhật người dùng thành công');
        $this->redirect('user');
    }

    public function delete($id = null)
    {
        $user = $this->requireAdmin();
        if (!$user) return;

        if (!$id) {
            $this->redirect('user');
            return;
        }

        // prevent deleting self
        if ($user['id'] == $id) {
            setFlash('error', 'Bạn không thể xóa chính mình');
            $this->redirect('user');
            return;
        }

        $this->model->delete($id);
        setFlash('success', 'Xóa người dùng thành công');
        $this->redirect('user');
    }
}
