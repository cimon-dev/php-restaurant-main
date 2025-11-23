<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="mb-3">
            <h3>Tạo nguyên liệu mới</h3>
        </div>

        <?php if ($flash): ?>
            <div class="alert <?php echo $flash['type'] === 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/ingredient/store" method="post">
                    <div class="mb-2">
                        <label class="form-label">Mã (code)</label>
                        <input class="form-control" type="text" name="code" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Tên</label>
                        <input class="form-control" type="text" name="name" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Danh mục</label>
                        <select name="category" class="form-control">
                            <option value="">-- Select category --</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?php echo htmlspecialchars($c['name']); ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Đơn vị</label>
                        <input class="form-control" type="text" name="unit">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Giá mua</label>
                        <input class="form-control" type="number" step="0.01" name="purchase_price">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Tồn tối thiểu</label>
                        <input class="form-control" type="number" name="min_stock" value="0">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Nhà cung cấp chính</label>
                        <input class="form-control" type="text" name="main_supplier">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description"></textarea>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary" type="submit">Lưu</button>
                        <a href="<?php echo BASE_URL; ?>/ingredient" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>