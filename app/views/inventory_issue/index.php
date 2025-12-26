<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Xuất kho</h3>
            <a href="<?php echo BASE_URL; ?>/inventory_issue/create" class="btn btn-primary">Tạo phiếu xuất</a>
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
                            <th>Loại</th>
                            <th>Trạng thái</th>
                            <th>Người tạo</th>
                            <th>Ghi chú</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $it): ?>
                                <tr>
                                    <td><strong><?php echo $it['id']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($it['issue_date']); ?></td>
                                    <td>
                                        <?php
                                        $typeMap = [
                                            'sale' => 'Bán hàng',
                                            'manual' => 'Thủ công',
                                            'waste' => 'Hết hạn'
                                        ];
                                        echo htmlspecialchars($typeMap[$it['issue_type']] ?? $it['issue_type']);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($it['status'] === 'completed') {
                                            echo '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Hoàn thành</span>';
                                        } else {
                                            echo '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass"></i> Chờ</span>';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($it['creator'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($it['note'] ?? '-'); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>/inventory_issue/edit/<?php echo $it['id']; ?>" class="btn btn-sm btn-outline-secondary">Xem</a>
                                        <?php if ($it['status'] !== 'completed'): ?>
                                            <a href="<?php echo BASE_URL; ?>/inventory_issue/complete/<?php echo $it['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Hoàn thành phiếu xuất này?')">Hoàn thành</a>
                                            <a href="<?php echo BASE_URL; ?>/inventory_issue/delete/<?php echo $it['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                                        <?php else: ?>
                                            <span class="text-muted small">Đã hoàn thành</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">Không tìm thấy phiếu xuất.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        if (isset($pagination)) {
            $paginationVar = $pagination;
            $baseUrlVar = $baseUrl ?? (BASE_URL . '/inventory_issue');
            $pagination = $paginationVar;
            $baseUrl = $baseUrlVar;
            include __DIR__ . '/../layouts/pagination.php';
        }
        ?>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>