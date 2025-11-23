<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="mb-3">
            <h3>Tạo người dùng mới</h3>
        </div>

        <?php if ($flash): ?>
            <div class="alert <?php echo $flash['type'] === 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post" action="<?php echo BASE_URL; ?>/user/store">
                    <div class="mb-2">
                        <label class="form-label">Tên đăng nhập</label>
                        <input class="form-control" type="text" name="username" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Mật khẩu</label>
                        <input class="form-control" type="password" name="password" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Họ và tên</label>
                        <input class="form-control" type="text" name="fullname">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Vai trò</label>
                        <select name="role" class="form-select">
                            <option value="admin">Quản trị viên</option>
                            <option value="manager">Quản lý</option>
                            <option value="user">Nhân viên</option>
                        </select>
                    </div>
                    <div class="mb-2 form-check">
                        <input type="checkbox" name="active" class="form-check-input" id="active" checked>
                        <label for="active" class="form-check-label">Kích hoạt</label>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary">Tạo</button>
                        <a href="<?php echo BASE_URL; ?>/user" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>