<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <p class="text-muted mb-1">Kho &amp; Nguyên liệu</p>
                <h3 class="mb-0 fw-semibold">Nguyên liệu</h3>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo BASE_URL; ?>/ingredient_category" class="btn btn-outline-secondary">
                    <i class="bi bi-tags"></i> Loại nguyên liệu
                </a>
                <a href="<?php echo BASE_URL; ?>/ingredient/create" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Tạo nguyên liệu mới
                </a>
            </div>
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
                                <th>Tên</th>
                                <th>Danh mục</th>
                                <th>Đơn vị</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-center">Tồn tối thiểu</th>
                                <th>Giá mua</th>
                                <th>Nhà cung cấp</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($items)): ?>
                                <?php foreach ($items as $it): ?>
                                    <tr>
                                        <td class="fw-semibold text-primary"><?php echo htmlspecialchars($it['code']); ?></td>
                                        <td><?php echo htmlspecialchars($it['name']); ?></td>
                                        <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($it['category']); ?></span></td>
                                        <td><?php echo htmlspecialchars($it['unit']); ?></td>
                                        <td class="text-center">
                                            <?php
                                            $current_qty = (int)($it['current_qty'] ?? 0);
                                            $min_stock = (int)($it['min_stock'] ?? 0);
                                            if ($current_qty <= 0) {
                                                echo '<span class="badge bg-danger-subtle text-danger">Hết (' . $current_qty . ')</span>';
                                            } elseif ($current_qty <= $min_stock) {
                                                echo '<span class="badge bg-warning text-dark">Sắp hết (' . $current_qty . ')</span>';
                                            } else {
                                                echo '<span class="badge bg-success">' . $current_qty . '</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center text-muted"><?php echo $it['min_stock']; ?></td>
                                        <td class="fw-semibold"><?php echo number_format($it['purchase_price'] ?? 0, 0, ',', '.'); ?> đ</td>
                                        <td><?php echo htmlspecialchars($it['main_supplier']); ?></td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?php echo BASE_URL; ?>/ingredient/edit/<?php echo $it['id']; ?>" class="btn btn-outline-secondary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/ingredient/delete/<?php echo $it['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <div class="mt-2">Không tìm thấy nguyên liệu.</div>
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