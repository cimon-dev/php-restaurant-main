<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="mb-3">
            <h3>Chỉnh sửa người dùng</h3>
        </div>

        <?php if ($flash): ?>
            <div class="alert <?php echo $flash['type'] === 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <?php if (!isset($item)): ?>
            <div class="alert alert-warning">Không tìm thấy người dùng.</div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <form method="post" action="<?php echo BASE_URL; ?>/user/update/<?php echo $item['id']; ?>">
                        <div class="mb-2">
                            <label class="form-label">Tên đăng nhập</label>
                            <input class="form-control" type="text" name="username" required value="<?php echo htmlspecialchars($item['username']); ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Mật khẩu (để trống nếu không đổi)</label>
                            <input class="form-control" type="password" name="password">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Họ và tên</label>
                            <input class="form-control" type="text" name="fullname" value="<?php echo htmlspecialchars($item['fullname']); ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Vai trò</label>
                            <select name="role" class="form-select">
                                <option value="admin" <?php echo ($item['role'] == 'admin') ? 'selected' : ''; ?>>Quản trị viên</option>
                                <option value="manager" <?php echo ($item['role'] == 'manager') ? 'selected' : ''; ?>>Quản lý</option>
                                <option value="user" <?php echo ($item['role'] == 'user') ? 'selected' : ''; ?>>Nhân viên</option>
                            </select>
                        </div>
                        <div class="mb-2 form-check">
                            <input type="checkbox" name="active" class="form-check-input" id="active" <?php echo $item['active'] ? 'checked' : ''; ?>>
                            <label for="active" class="form-check-label">Kích hoạt</label>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-primary">Cập nhật</button>
                            <a href="<?php echo BASE_URL; ?>/user" class="btn btn-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>