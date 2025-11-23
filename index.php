<?php

/**
 * Main Index File - Entry point of the application
 */

// Load config
require_once __DIR__ . '/config/config.php';

// Load core files
require_once BASE_PATH . '/core/App.php';
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/Model.php';

// Initialize App
$app = new App();
