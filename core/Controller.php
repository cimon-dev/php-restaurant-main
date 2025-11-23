<?php

/**
 * Base Controller
 */

class Controller
{

    /**
     * Load model
     */
    public function model($model)
    {
        $modelFile = BASE_PATH . '/app/models/' . $model . '.php';

        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        }

        die("Model $model not found");
    }

    /**
     * Load view
     */
    public function view($view, $data = [])
    {
        // Ensure $user is always available to views (if not explicitly provided)
        if (!isset($data['user'])) {
            if (file_exists(BASE_PATH . '/helpers/JWT.php')) {
                require_once BASE_PATH . '/helpers/JWT.php';
                $data['user'] = JWT::getCurrentUser();
            } else {
                $data['user'] = null;
            }
        }

        extract($data);

        $viewFile = BASE_PATH . '/app/views/' . $view . '.php';

        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View $view not found");
        }
    }

    /**
     * JSON response
     */
    public function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirect
     */
    public function redirect($url)
    {
        header('Location: ' . BASE_URL . '/' . $url);
        exit;
    }

    /**
     * Check if request is AJAX
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get POST data
     */
    public function getPost($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    /**
     * Get GET data
     */
    public function getGet($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    /**
     * Validate required fields
     */
    public function validateRequired($data, $fields)
    {
        $errors = [];

        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = "Trường $field là bắt buộc";
            }
        }

        return $errors;
    }
}
