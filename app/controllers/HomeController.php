<?php

/**
 * Home Controller
 */

require_once BASE_PATH . '/core/Controller.php';

class HomeController extends Controller
{

    public function index()
    {
        // Redirect to login if not authenticated
        $this->redirect('auth/login');
    }
}
