    <div class="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-shop" style="font-size: 40px;"></i>
            <h4>Nhà hàng</h4>
            <small>Hệ thống quản lý</small>
        </div>

        <?php
        // Determine current route to mark active menu
        $current = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
        $parts = $current === '' ? [] : explode('/', $current);
        $first = $parts[0] ?? 'dashboard';
        ?>
        <?php
        require_once BASE_PATH . '/helpers/JWT.php';
        $currentUser = JWT::getCurrentUser();
        ?>

        <div class="sidebar-menu">
            <a href="<?php echo BASE_URL; ?>/dashboard" class="menu-item <?php echo $first === 'dashboard' ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <a href="<?php echo BASE_URL; ?>/ingredient" class="menu-item <?php echo $first === 'ingredient' ? 'active' : ''; ?>">
                <i class="bi bi-box-seam"></i>
                <span>Nguyên liệu</span>
            </a>

            <a href="<?php echo BASE_URL; ?>/ingredient_category" class="menu-item <?php echo $first === 'ingredient_category' ? 'active' : ''; ?>">
                <i class="bi bi-tags"></i>
                <span>Loại nguyên liệu</span>
            </a>

            <a href="<?php echo BASE_URL; ?>/menu_item" class="menu-item <?php echo $first === 'menu_item' ? 'active' : ''; ?>">
                <i class="bi bi-egg-fried"></i>
                <span>Món ăn</span>
            </a>

            <a href="<?php echo BASE_URL; ?>/recipe" class="menu-item <?php echo $first === 'recipe' ? 'active' : ''; ?>">
                <i class="bi bi-journal-bookmark"></i>
                <span>Công thức</span>
            </a>

            <a href="<?php echo BASE_URL; ?>/inventory_receipt" class="menu-item <?php echo $first === 'inventory_receipt' ? 'active' : ''; ?>">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Nhập kho</span>
            </a>

            <a href="<?php echo BASE_URL; ?>/inventory_issue" class="menu-item <?php echo $first === 'inventory_issue' ? 'active' : ''; ?>">
                <i class="bi bi-box-arrow-right"></i>
                <span>Xuất kho</span>
            </a>

            <a href="<?php echo BASE_URL; ?>/restaurant_table" class="menu-item <?php echo $first === 'restaurant_table' ? 'active' : ''; ?>">
                <i class="bi bi-table"></i>
                <span>Quản lý bàn</span>
            </a>


            <a href="<?php echo BASE_URL; ?>/sale_order" class="menu-item <?php echo $first === 'sale_order' ? 'active' : ''; ?>">
                <i class="bi bi-receipt"></i>
                <span>Đơn hàng</span>
            </a>

            <a href="<?php echo BASE_URL; ?>/expense" class="menu-item <?php echo $first === 'expense' ? 'active' : ''; ?>">
                <i class="bi bi-cash-stack"></i>
                <span>Chi phí</span>
            </a>

            <a href="<?php echo BASE_URL; ?>/report" class="menu-item <?php echo $first === 'report' ? 'active' : ''; ?>">
                <i class="bi bi-graph-up"></i>
                <span>Báo cáo</span>
            </a>

            <?php if ($currentUser && ($currentUser['role'] ?? '') === 'admin'): ?>
                <a href="<?php echo BASE_URL; ?>/user" class="menu-item <?php echo $first === 'user' ? 'active' : ''; ?>">
                    <i class="bi bi-people"></i>
                    <span>Người dùng</span>
                </a>
            <?php endif; ?>
        </div>
    </div>