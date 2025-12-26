<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <p class="text-muted mb-1">Thực đơn</p>
                <h3 class="mb-0 fw-semibold">Món ăn</h3>
            </div>
            <a href="<?php echo BASE_URL; ?>/menu_item/create" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Tạo món mới
            </a>
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
                                <th>Mã</th>
                                <th>Tên</th>
                                <th>Giá</th>
                                <th>Mô tả</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($items)): ?>
                                <?php foreach ($items as $it): ?>
                                    <tr>
                                        <td class="fw-semibold text-primary"><?php echo $it['id']; ?></td>
                                        <td class="text-muted"><?php echo htmlspecialchars($it['code']); ?></td>
                                        <td><?php echo htmlspecialchars($it['name']); ?></td>
                                        <td class="fw-semibold text-success"><?php echo number_format($it['price'], 0, ',', '.'); ?> đ</td>
                                        <td class="text-muted" style="max-width: 320px;"> <?php echo htmlspecialchars($it['description']); ?> </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?php echo BASE_URL; ?>/menu_item/edit/<?php echo $it['id']; ?>" class="btn btn-outline-secondary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/menu_item/delete/<?php echo $it['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <div class="mt-2">Không tìm thấy món.</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php
                if (isset($pagination)) {
                    $paginationVar = $pagination;
                    $baseUrlVar = $baseUrl ?? (BASE_URL . '/menu_item');
                    $pagination = $paginationVar;
                    $baseUrl = $baseUrlVar;
                    include __DIR__ . '/../layouts/pagination.php';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>