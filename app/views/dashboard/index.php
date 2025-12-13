<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<!-- Sidebar -->
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-content">
    <!-- Navbar -->
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

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
                <a href="<?php echo BASE_URL; ?>/ingredient" class="text-decoration-none text-dark">
                    <div class="stats-card hoverable">
                        <div class="stats-icon bg-gradient-primary">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($counts['ingredients'] ?? 0); ?></h3>
                        <p>Tổng nguyên liệu</p>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="<?php echo BASE_URL; ?>/menu_item" class="text-decoration-none text-dark">
                    <div class="stats-card hoverable">
                        <div class="stats-icon bg-gradient-success">
                            <i class="bi bi-egg-fried"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($counts['menu_items'] ?? 0); ?></h3>
                        <p>Món ăn</p>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="<?php echo BASE_URL; ?>/restaurant_table" class="text-decoration-none text-dark">
                    <div class="stats-card hoverable">
                        <div class="stats-icon bg-gradient-warning">
                            <i class="bi bi-table"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($counts['tables'] ?? 0); ?></h3>
                        <p>Bàn ăn</p>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="<?php echo BASE_URL; ?>/sale_order" class="text-decoration-none text-dark">
                    <div class="stats-card hoverable">
                        <div class="stats-icon bg-gradient-info">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($todayOrders ?? 0); ?></h3>
                        <p>Đơn hàng hôm nay</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <a href="<?php echo BASE_URL; ?>/report" class="text-decoration-none text-dark">
                    <div class="stats-card hoverable">
                        <div class="stats-icon bg-gradient-success">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <h3><?php echo number_format($todayRevenue, 2); ?></h3>
                        <p>Doanh thu hôm nay</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="<?php echo BASE_URL; ?>/inventory_receipt" class="text-decoration-none text-dark">
                    <div class="stats-card hoverable">
                        <div class="stats-icon bg-gradient-secondary">
                            <i class="bi bi-box-arrow-in-right"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($counts['receipts'] ?? 0); ?></h3>
                        <p>Phiếu nhập</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="<?php echo BASE_URL; ?>/inventory_issue" class="text-decoration-none text-dark">
                    <div class="stats-card hoverable">
                        <div class="stats-icon bg-gradient-danger">
                            <i class="bi bi-box-arrow-right"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($counts['issues'] ?? 0); ?></h3>
                        <p>Phiếu xuất</p>
                    </div>
                </a>
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
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Đơn hàng mới</h6>
                                <?php if (!empty($recentOrders)): ?>
                                    <ul class="list-group">
                                        <?php foreach ($recentOrders as $ro): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>#<?php echo $ro['id']; ?></strong>
                                                    <div class="small text-muted"><?php echo $ro['table_number'] ? 'Bàn ' . htmlspecialchars($ro['table_number']) : 'Không gắn bàn'; ?></div>
                                                </div>
                                                <div class="text-end">
                                                    <div><?php echo number_format($ro['total_amount'], 2); ?></div>
                                                    <div class="small text-muted"><?php echo htmlspecialchars($ro['order_time']); ?></div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="text-muted">Không có đơn hàng gần đây.</div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <h6>Chi phí gần đây</h6>
                                <?php if (!empty($recentExpenses)): ?>
                                    <ul class="list-group">
                                        <?php foreach ($recentExpenses as $re): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div><?php echo htmlspecialchars($re['expense_type']); ?></div>
                                                    <div class="small text-muted"><?php echo htmlspecialchars($re['expense_date']); ?></div>
                                                </div>
                                                <div><?php echo number_format($re['amount'], 2); ?></div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="text-muted">Không có chi phí gần đây.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>