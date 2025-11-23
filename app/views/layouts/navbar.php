<!-- Navbar -->
<nav class="navbar-custom">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center w-100">
            <?php
            // Determine page title: prefer controller-provided `$pageTitle`, otherwise infer from URL
            if (isset($pageTitle) && $pageTitle) {
                $title = $pageTitle;
            } else {
                $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $basePath = parse_url(BASE_URL, PHP_URL_PATH) ?: '';
                if ($basePath !== '' && strpos($path, $basePath) === 0) {
                    $rel = substr($path, strlen($basePath));
                } else {
                    $rel = $path;
                }
                $segments = array_values(array_filter(explode('/', trim($rel, '/'))));
                $first = isset($segments[0]) && $segments[0] !== '' ? $segments[0] : 'dashboard';

                $routeTitles = [
                    'dashboard' => 'Dashboard',
                    'recipe' => 'Công thức',
                    'ingredient' => 'Nguyên liệu',
                    'menu_item' => 'Món ăn',
                    'sale_order' => 'Đơn bán hàng',
                    'inventory_receipt' => 'Phiếu nhập',
                    'inventory_issue' => 'Phiếu xuất',
                    'expense' => 'Chi phí',
                    'user' => 'Người dùng',
                    'report' => 'Báo cáo',
                    'restaurant_table' => 'Bàn'
                ];

                $title = isset($routeTitles[$first]) ? $routeTitles[$first] : ucwords(str_replace(['_', '-'], ' ', $first));
            }
            // Optional subtitle from second segment
            $subtitle = '';
            if (empty($subtitle)) {
                $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $basePath = parse_url(BASE_URL, PHP_URL_PATH) ?: '';
                if ($basePath !== '' && strpos($path, $basePath) === 0) {
                    $rel = substr($path, strlen($basePath));
                } else {
                    $rel = $path;
                }
                $segments = array_values(array_filter(explode('/', trim($rel, '/'))));
                if (isset($segments[1])) {
                    $s = $segments[1];
                    if ($s === 'create') $subtitle = 'Tạo mới';
                    elseif ($s === 'edit') $subtitle = 'Chỉnh sửa';
                    else $subtitle = ucwords(str_replace(['_', '-'], ' ', $s));
                }
            }
            ?>

            <div>
                <h5 class="mb-0"><?php echo htmlspecialchars($title); ?></h5>
                <?php if ($subtitle): ?>
                    <small class="text-muted d-block"><?php echo htmlspecialchars($subtitle); ?></small>
                <?php endif; ?>
            </div>

            <?php
            // Safe handling when $user is not provided or null
            $fullname = isset($user) && is_array($user) && !empty($user['fullname']) ? $user['fullname'] : null;
            $roleKey = isset($user) && is_array($user) && !empty($user['role']) ? $user['role'] : null;

            $roleName = [
                'admin' => 'Quản trị viên',
                'manager' => 'Quản lý',
                'user' => 'Nhân viên'
            ];
            ?>

            <div class="user-info">
                <?php if ($fullname): ?>
                    <div>
                        <div class="fw-bold" id="userFullname"><?php echo htmlspecialchars($fullname); ?></div>
                        <small class="text-muted" id="userRole">
                            <?php echo isset($roleName[$roleKey]) ? $roleName[$roleKey] : ($roleKey ?? ''); ?>
                        </small>
                    </div>

                    <div class="dropdown">
                        <button class="btn user-avatar dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <?php echo strtoupper(substr($fullname, 0, 1)); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Thông tin</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Cài đặt</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="logout()"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div>
                        <a href="<?php echo BASE_URL; ?>/auth/login" class="btn btn-sm btn-primary">Đăng nhập</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>