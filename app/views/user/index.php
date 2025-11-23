<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Người dùng</h3>
            <a href="<?php echo BASE_URL; ?>/user/create" class="btn btn-primary">Tạo người dùng mới</a>
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
                            <th>Tên đăng nhập</th>
                            <th>Họ và tên</th>
                            <th>Vai trò</th>
                            <th>Kích hoạt</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $it): ?>
                                <tr>
                                    <td><?php echo $it['id']; ?></td>
                                    <td><?php echo htmlspecialchars($it['username']); ?></td>
                                    <td><?php echo htmlspecialchars($it['fullname']); ?></td>
                                    <td><?php echo htmlspecialchars($it['role']); ?></td>
                                    <td><?php echo $it['active'] ? 'Có' : 'Không'; ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>/user/edit/<?php echo $it['id']; ?>" class="btn btn-sm btn-outline-secondary">Chỉnh sửa</a>
                                        <a href="<?php echo BASE_URL; ?>/user/delete/<?php echo $it['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">Không tìm thấy người dùng.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>