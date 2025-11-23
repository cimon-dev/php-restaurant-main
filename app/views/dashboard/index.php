<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Restaurant Management</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            color: white;
            z-index: 1000;
            transition: all 0.3s;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h4 {
            margin: 0;
            font-weight: 600;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-item {
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }
        
        .menu-item:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .menu-item.active {
            background: rgba(255,255,255,0.2);
            border-left: 4px solid white;
        }
        
        .menu-item i {
            width: 25px;
            margin-right: 10px;
            font-size: 18px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        /* Navbar */
        .navbar-custom {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 15px 30px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        /* Content Area */
        .content-area {
            padding: 30px;
        }
        
        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            margin-bottom: 15px;
        }
        
        .stats-card h3 {
            font-size: 32px;
            font-weight: 700;
            margin: 10px 0;
            color: #333;
        }
        
        .stats-card p {
            color: #999;
            margin: 0;
            font-size: 14px;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .bg-gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .bg-gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .bg-gradient-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        /* Dropdown */
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 10px;
        }
        
        .dropdown-item {
            padding: 10px 20px;
        }
        
        .dropdown-item:hover {
            background: #f8f9fa;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-shop" style="font-size: 40px;"></i>
            <h4>Restaurant</h4>
            <small>Management System</small>
        </div>
        
        <div class="sidebar-menu">
            <a href="<?php echo BASE_URL; ?>/dashboard" class="menu-item active">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="#" class="menu-item">
                <i class="bi bi-box-seam"></i>
                <span>Nguyên liệu</span>
            </a>
            
            <a href="#" class="menu-item">
                <i class="bi bi-egg-fried"></i>
                <span>Món ăn</span>
            </a>
            
            <a href="#" class="menu-item">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Nhập kho</span>
            </a>
            
            <a href="#" class="menu-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Xuất kho</span>
            </a>
            
            <a href="#" class="menu-item">
                <i class="bi bi-table"></i>
                <span>Quản lý bàn</span>
            </a>
            
            <a href="#" class="menu-item">
                <i class="bi bi-receipt"></i>
                <span>Đơn hàng</span>
            </a>
            
            <a href="#" class="menu-item">
                <i class="bi bi-cash-stack"></i>
                <span>Chi phí</span>
            </a>
            
            <a href="#" class="menu-item">
                <i class="bi bi-graph-up"></i>
                <span>Báo cáo</span>
            </a>
            
            <a href="#" class="menu-item">
                <i class="bi bi-people"></i>
                <span>Người dùng</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <nav class="navbar-custom">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h5 class="mb-0">Dashboard</h5>
                    
                    <div class="user-info">
                        <div>
                            <div class="fw-bold" id="userFullname"><?php echo $user['fullname']; ?></div>
                            <small class="text-muted" id="userRole">
                                <?php 
                                $roleName = [
                                    'admin' => 'Quản trị viên',
                                    'manager' => 'Quản lý',
                                    'user' => 'Nhân viên'
                                ];
                                echo $roleName[$user['role']] ?? $user['role']; 
                                ?>
                            </small>
                        </div>
                        
                        <div class="dropdown">
                            <button class="btn user-avatar dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <?php echo strtoupper(substr($user['fullname'], 0, 1)); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Thông tin</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Cài đặt</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="logout()"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Welcome -->
            <div class="mb-4">
                <h2>Chào mừng, <?php echo $user['fullname']; ?>! 👋</h2>
                <p class="text-muted">Đây là tổng quan hệ thống quản lý nhà hàng của bạn.</p>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-icon bg-gradient-primary">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <h3 id="totalIngredients">0</h3>
                        <p>Tổng nguyên liệu</p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-icon bg-gradient-success">
                            <i class="bi bi-egg-fried"></i>
                        </div>
                        <h3 id="totalMenuItems">0</h3>
                        <p>Món ăn</p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-icon bg-gradient-warning">
                            <i class="bi bi-table"></i>
                        </div>
                        <h3 id="totalTables">10</h3>
                        <p>Bàn ăn</p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-icon bg-gradient-info">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <h3 id="totalOrders">0</h3>
                        <p>Đơn hàng hôm nay</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Hoạt động gần đây</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size: 48px;"></i>
                                <p class="mt-3">Chưa có hoạt động nào</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
        
        // Logout function
        function logout() {
            if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
                // Clear localStorage
                localStorage.removeItem('jwt_token');
                localStorage.removeItem('user');
                
                // Redirect to logout
                window.location.href = `${BASE_URL}/auth/logout`;
            }
        }
        
        // Load dashboard stats (example)
        async function loadStats() {
            // TODO: Implement API calls to get real stats
            // For now, using placeholder values
        }
        
        // Verify token on page load
        const token = localStorage.getItem('jwt_token');
        if (!token) {
            window.location.href = `${BASE_URL}/auth/login`;
        }
        
        // Load stats
        loadStats();
    </script>
</body>
</html>
