<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="mb-3">
            <h3>Tạo bàn mới</h3>
        </div>

        <?php if ($flash): ?>
            <div class="alert <?php echo $flash['type'] === 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/restaurant_table/store" method="post">
                    <div class="mb-2">
                        <label class="form-label">Số bàn (number)</label>
                        <input class="form-control" type="text" name="number" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Trạng thái</label>
                        <select class="form-select" name="status">
                            <option value="free">Free</option>
                            <option value="occupied">Occupied</option>
                            <option value="reserved">Reserved</option>
                        </select>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary" type="submit">Lưu</button>
                        <a href="<?php echo BASE_URL; ?>/restaurant_table" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>