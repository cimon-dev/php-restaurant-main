<?php

/**
 * Recipe Controller - CRUD for recipe (menu -> ingredient -> qty)
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class RecipeController extends Controller
{

    private $model;

    public function __construct()
    {
        $this->model = $this->model('Recipe');
    }

    public function index($menu_id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        // Allow menu_id via URL segment or GET param
        if (!$menu_id && isset($_GET['menu_id'])) {
            $menu_id = intval($_GET['menu_id']);
        }

        // If no menu selected yet, show menu list with search
        $menuModel = $this->model('MenuItem');
        if (!$menu_id) {
            $q = isset($_GET['q']) ? trim($_GET['q']) : '';
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $per = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

            if ($q !== '') {
                $baseSql = "SELECT * FROM menu_item WHERE name LIKE :q ORDER BY name ASC";
                $params = ['q' => '%' . $q . '%'];
            } else {
                $baseSql = "SELECT * FROM menu_item ORDER BY name ASC";
                $params = [];
            }

            $result = $menuModel->paginate($baseSql, $params, $page, $per);
            $menuItems = $result['data'];
            $this->view('recipe/select_menu', [
                'menuItems' => $menuItems,
                'user' => $user,
                'q' => $q,
                'pagination' => $result['pagination'],
                'baseUrl' => BASE_URL . '/recipe' . ($q !== '' ? ('?q=' . urlencode($q)) : '')
            ]);
            return;
        }

        // Menu selected: fetch recipes only for that menu
        // Pagination for recipe items of selected menu
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

        $baseSql = "SELECT r.*, m.name AS menu_name, i.name AS ingredient_name
            FROM recipe r
            LEFT JOIN menu_item m ON r.menu_id = m.id
            LEFT JOIN ingredient i ON r.ingredient_id = i.id
            WHERE r.menu_id = " . intval($menu_id) . "
            ORDER BY r.id DESC";

        $result = $this->model->paginate($baseSql, [], $page, $per);
        $items = $result['data'];
        $menu = $menuModel->find($menu_id);

        $this->view('recipe/index', [
            'items' => $items,
            'pagination' => $result['pagination'],
            'baseUrl' => BASE_URL . '/recipe?menu_id=' . intval($menu_id),
            'user' => $user,
            'menu' => $menu,
            'menu_id' => $menu_id
        ]);
    }

    public function create($menu_id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        // Load menu items and ingredients for selects
        $menuModel = $this->model('MenuItem');
        $ingredientModel = $this->model('Ingredient');

        $menuItems = $menuModel->all('name', 'ASC');
        $ingredients = $ingredientModel->all('name', 'ASC');

        // Support menu_id from URL segment or GET
        if (!$menu_id && isset($_GET['menu_id'])) {
            $menu_id = intval($_GET['menu_id']);
        }

        $selectedMenu = null;
        if ($menu_id) {
            $selectedMenu = $menuModel->find($menu_id);
        }

        $this->view('recipe/create', ['menuItems' => $menuItems, 'ingredients' => $ingredients, 'selectedMenu' => $selectedMenu, 'user' => $user]);
    }

    public function store()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        $data = $this->getPost();
        $required = ['menu_id', 'ingredient_id', 'qty'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('recipe/create');
            return;
        }

        // Insert
        $insert = [
            'menu_id' => $data['menu_id'],
            'ingredient_id' => $data['ingredient_id'],
            'qty' => $data['qty']
        ];

        $this->model->insert($insert);
        setFlash('success', 'Tạo công thức thành công');

        // After creating recipe, redirect back to the menu-specific recipe list if possible
        $redirect = 'recipe';
        if (!empty($data['menu_id'])) {
            $redirect = 'recipe?menu_id=' . intval($data['menu_id']);
        }
        $this->redirect($redirect);
    }

    public function edit($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('recipe');
            return;
        }

        $item = $this->model->find($id);
        if (!$item) {
            setFlash('error', 'Công thức không tồn tại');
            $this->redirect('recipe');
            return;
        }

        $menuModel = $this->model('MenuItem');
        $ingredientModel = $this->model('Ingredient');

        $menuItems = $menuModel->all('name', 'ASC');
        $ingredients = $ingredientModel->all('name', 'ASC');

        $this->view('recipe/edit', ['item' => $item, 'menuItems' => $menuItems, 'ingredients' => $ingredients]);
    }

    public function update($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        if (!$id) {
            $this->redirect('recipe');
            return;
        }

        $data = $this->getPost();
        $required = ['menu_id', 'ingredient_id', 'qty'];
        $errors = $this->validateRequired($data, $required);
        if (!empty($errors)) {
            setFlash('error', implode('; ', $errors));
            $this->redirect('recipe/edit/' . $id);
            return;
        }

        $update = [
            'menu_id' => $data['menu_id'],
            'ingredient_id' => $data['ingredient_id'],
            'qty' => $data['qty']
        ];

        $this->model->update($id, $update);
        setFlash('success', 'Cập nhật công thức thành công');

        $redirect = 'recipe';
        if (!empty($update['menu_id'])) {
            $redirect = 'recipe?menu_id=' . intval($update['menu_id']);
        }
        $this->redirect($redirect);
    }

    public function delete($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        if (!$id) {
            $this->redirect('recipe');
            return;
        }

        $this->model->delete($id);
        setFlash('success', 'Xóa công thức thành công');
        $this->redirect('recipe');
    }
}
