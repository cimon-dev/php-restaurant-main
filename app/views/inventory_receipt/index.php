<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Nhập kho</h3>
            <a href="<?php echo BASE_URL; ?>/inventory_receipt/create" class="btn btn-primary">Tạo phiếu nhập</a>
        </div>

        <?php if ($flash): ?>
            <div class="alert <?php echo $flash['type'] === 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Ngày</th>
                            <th>Nhà cung cấp</th>
                            <th>Người tạo</th>
                            <th>Ghi chú</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $it): ?>
                                <tr>
                                    <td><?php echo $it['id']; ?></td>
                                    <td><?php echo htmlspecialchars($it['receipt_date']); ?></td>
                                    <td><?php echo htmlspecialchars($it['supplier']); ?></td>
                                    <td><?php echo htmlspecialchars($it['creator'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($it['note']); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>/inventory_receipt/edit/<?php echo $it['id']; ?>" class="btn btn-sm btn-outline-secondary">Chỉnh sửa</a>
                                        <a href="<?php echo BASE_URL; ?>/inventory_receipt/delete/<?php echo $it['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">Không tìm thấy phiếu nhập.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>