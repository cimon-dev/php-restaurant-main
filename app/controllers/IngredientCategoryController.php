<?php

/**
 * IngredientCategory Controller - CRUD for ingredient categories
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class IngredientCategoryController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = $this->model('IngredientCategory');
    }

    public function index()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

        $baseSql = "SELECT * FROM ingredient_category ORDER BY name ASC";
        $result = $this->model->paginate($baseSql, [], $page, $per);

        $this->view('ingredient_category/index', [
            'items' => $result['data'],
            'pagination' => $result['pagination'],
            'baseUrl' => BASE_URL . '/ingredient_category',
            'user' => $user
        ]);
    }

    public function create()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        $this->view('ingredient_category/create');
    }

    public function store()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        $data = $this->getPost();
        $required = ['name'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('ingredient_category/create');
            return;
        }

        if ($this->model->findByName($data['name'])) {
            setFlash('error', 'Loại nguyên liệu đã tồn tại');
            $this->redirect('ingredient_category/create');
            return;
        }

        $this->model->insert(['name' => $data['name'], 'description' => $data['description'] ?? null]);
        setFlash('success', 'Tạo loại nguyên liệu thành công');
        $this->redirect('ingredient_category');
    }

    public function edit($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('ingredient_category');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'Loại nguyên liệu không tồn tại');
            $this->redirect('ingredient_category');
            return;
        }

        $this->view('ingredient_category/edit', ['item' => $item]);
    }

    public function update($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        if (!$id) {
            $this->redirect('ingredient_category');
            return;
        }

        $data = $this->getPost();
        $required = ['name'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('ingredient_category/edit/' . $id);
            return;
        }

        $existing = $this->model->findByName($data['name']);
        if ($existing && $existing['id'] != $id) {
            setFlash('error', 'Loại nguyên liệu đã tồn tại');
            $this->redirect('ingredient_category/edit/' . $id);
            return;
        }

        $this->model->update($id, ['name' => $data['name'], 'description' => $data['description'] ?? null]);
        setFlash('success', 'Cập nhật loại nguyên liệu thành công');
        $this->redirect('ingredient_category');
    }

    public function delete($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('ingredient_category');
            return;
        }

        $this->model->delete($id);
        setFlash('success', 'Xóa loại nguyên liệu thành công');
        $this->redirect('ingredient_category');
    }
}
