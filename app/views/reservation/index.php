<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Đặt chỗ</h3>
            <a href="<?php echo BASE_URL; ?>/reservation/create" class="btn btn-primary">Tạo đặt chỗ mới</a>
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
                            <th>Bàn</th>
                            <th>Khách</th>
                            <th>Số khách</th>
                            <th>Bắt đầu</th>
                            <th>Kết thúc</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $it): ?>
                                <tr>
                                    <td><?php echo $it['id']; ?></td>
                                    <td><?php echo htmlspecialchars($it['table_id']); ?></td>
                                    <td><?php echo htmlspecialchars($it['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($it['party_size']); ?></td>
                                    <td><?php echo htmlspecialchars($it['start_time']); ?></td>
                                    <td><?php echo htmlspecialchars($it['end_time']); ?></td>
                                    <td><?php echo htmlspecialchars($it['status']); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>/reservation/edit/<?php echo $it['id']; ?>" class="btn btn-sm btn-outline-secondary">Chỉnh sửa</a>
                                        <a href="<?php echo BASE_URL; ?>/reservation/delete/<?php echo $it['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">Không tìm thấy đặt chỗ.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        if (isset($pagination)) {
            $paginationVar = $pagination;
            $baseUrlVar = $baseUrl ?? (BASE_URL . '/reservation');
            $pagination = $paginationVar;
            $baseUrl = $baseUrlVar;
            include __DIR__ . '/../layouts/pagination.php';
        }
        ?>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>