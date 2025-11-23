<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Tạo đặt chỗ</h3>
            <a href="<?php echo BASE_URL; ?>/reservation" class="btn btn-secondary">Quay lại</a>
        </div>

        <?php if ($flash): ?>
            <div class="alert <?php echo $flash['type'] === 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post" action="<?php echo BASE_URL; ?>/reservation/store">
                    <div class="mb-3">
                        <label>Khách hàng</label>
                        <input type="text" name="customer_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Bàn</label>
                        <select name="table_id" class="form-control" required>
                            <option value="">-- Chọn bàn --</option>
                            <?php foreach ($tables as $t): ?>
                                <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['number']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Party size</label>
                        <input type="number" name="party_size" class="form-control" value="1" min="1">
                    </div>

                    <div class="mb-3">
                        <label>Start time</label>
                        <input type="datetime-local" name="start_time" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>End time</label>
                        <input type="datetime-local" name="end_time" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <button class="btn btn-primary">Lưu</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>