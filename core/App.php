<?php
/**
 * Core App Class - MVC Router
 */

class App {
    
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];
    
    public function __construct() {
        $url = $this->parseUrl();
        
        // Check controller
        if (isset($url[0])) {
            $controllerName = ucfirst($url[0]) . 'Controller';
            $controllerFile = BASE_PATH . '/app/controllers/' . $controllerName . '.php';
            
            if (file_exists($controllerFile)) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }
        
        // Require controller
        require_once BASE_PATH . '/app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;
        
        // Check method
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }
        
        // Get params
        $this->params = $url ? array_values($url) : [];
        
        // Call method with params
        call_user_func_array([$this->controller, $this->method], $this->params);
    }
    
    private function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
