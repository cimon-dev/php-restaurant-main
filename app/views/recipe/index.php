<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Công thức <?php echo isset($menu) ? 'cho: ' . htmlspecialchars($menu['name']) : ''; ?></h3>
            <?php if (!empty($menu_id)): ?>
                <div>
                    <a href="<?php echo BASE_URL; ?>/recipe/create?menu_id=<?php echo $menu_id; ?>" class="btn btn-primary">Tạo công thức mới</a>
                    <a href="<?php echo BASE_URL; ?>/recipe" class="btn btn-secondary">Chọn món khác</a>
                </div>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/recipe" class="btn btn-primary">Chọn món</a>
            <?php endif; ?>
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
                            <th>Món</th>
                            <th>Nguyên liệu</th>
                            <th>Số lượng</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $it): ?>
                                <tr>
                                    <td><?php echo $it['id']; ?></td>
                                    <td><?php echo htmlspecialchars($it['menu_name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($it['ingredient_name'] ?? ''); ?></td>
                                    <td><?php echo $it['qty']; ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>/recipe/edit/<?php echo $it['id']; ?>" class="btn btn-sm btn-outline-secondary">Chỉnh sửa</a>
                                        <a href="<?php echo BASE_URL; ?>/recipe/delete/<?php echo $it['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">Không tìm thấy công thức.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>