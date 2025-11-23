<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="mb-3">
            <h3>Chỉnh sửa bàn</h3>
        </div>

        <?php if ($flash): ?>
            <div class="alert <?php echo $flash['type'] === 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <?php if (!isset($item)): ?>
            <div class="alert alert-warning">Không tìm thấy bàn.</div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>/restaurant_table/update/<?php echo $item['id']; ?>" method="post">
                        <div class="mb-2">
                            <label class="form-label">Số bàn (number)</label>
                            <input class="form-control" type="text" name="number" required value="<?php echo htmlspecialchars($item['number']); ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status">
                                <option value="free" <?php echo ($item['status'] == 'free') ? 'selected' : ''; ?>>Free</option>
                                <option value="occupied" <?php echo ($item['status'] == 'occupied') ? 'selected' : ''; ?>>Occupied</option>
                                <option value="reserved" <?php echo ($item['status'] == 'reserved') ? 'selected' : ''; ?>>Reserved</option>
                            </select>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-primary" type="submit">Cập nhật</button>
                            <a href="<?php echo BASE_URL; ?>/restaurant_table" class="btn btn-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>