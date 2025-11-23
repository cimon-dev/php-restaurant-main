<?php
/**
 * Dashboard Controller
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class DashboardController extends Controller {
    
    public function index() {
        // Check authentication
        $user = JWT::getCurrentUser();
        
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }
        
        $this->view('dashboard/index', ['user' => $user]);
    }
}
