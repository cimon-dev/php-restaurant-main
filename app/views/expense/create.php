<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Tạo chi phí mới</h3>
            <a href="<?php echo BASE_URL; ?>/expense" class="btn btn-secondary">Quay lại</a>
        </div>

        <?php if ($flash): ?>
            <div class="alert <?php echo $flash['type'] === 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post" action="<?php echo BASE_URL; ?>/expense/store">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Loại chi phí</label>
                                <input type="text" name="expense_type" class="form-control" placeholder="ví dụ: điện, nước, nguyên liệu" required>
                            </div>

                            <div class="mb-3">
                                <label>Số tiền</label>
                                <input type="number" step="0.01" name="amount" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Ngày</label>
                                <input type="date" name="expense_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Mô tả</label>
                                <textarea name="description" class="form-control"></textarea>
                            </div>

                            <button class="btn btn-primary">Tạo</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>