<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <p class="text-muted mb-1">Công thức</p>
                <h3 class="mb-0 fw-semibold">Công thức <?php echo isset($menu) ? 'cho: ' . htmlspecialchars($menu['name']) : ''; ?></h3>
            </div>
            <?php if (!empty($menu_id)): ?>
                <div class="d-flex gap-2">
                    <a href="<?php echo BASE_URL; ?>/recipe/create?menu_id=<?php echo $menu_id; ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Tạo công thức mới
                    </a>
                    <a href="<?php echo BASE_URL; ?>/recipe" class="btn btn-outline-secondary">Chọn món khác</a>
                </div>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/recipe" class="btn btn-primary">
                    <i class="bi bi-search"></i> Chọn món
                </a>
            <?php endif; ?>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'warning' ? 'warning' : 'danger'); ?> shadow-sm">
                <i class="bi bi-info-circle me-1"></i><?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã</th>
                                <th>Món</th>
                                <th>Nguyên liệu</th>
                                <th>Số lượng</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($items)): ?>
                                <?php foreach ($items as $it): ?>
                                    <tr>
                                        <td class="fw-semibold text-primary"><?php echo $it['id']; ?></td>
                                        <td><?php echo htmlspecialchars($it['menu_name'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($it['ingredient_name'] ?? ''); ?></td>
                                        <td class="fw-semibold"><?php echo $it['qty']; ?></td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?php echo BASE_URL; ?>/recipe/edit/<?php echo $it['id']; ?>" class="btn btn-outline-secondary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/recipe/delete/<?php echo $it['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <div class="mt-2">Không tìm thấy công thức.</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>