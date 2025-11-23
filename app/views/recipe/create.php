<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="mb-3">
            <h3>Tạo công thức mới</h3>
        </div>

        <?php if ($flash): ?>
            <div class="alert <?php echo $flash['type'] === 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/recipe/store" method="post">
                    <div class="mb-2">
                        <label class="form-label">Món</label>
                        <?php if (!empty($selectedMenu)): ?>
                            <input type="hidden" name="menu_id" value="<?php echo $selectedMenu['id']; ?>">
                            <div class="form-control-plaintext py-2"><?php echo htmlspecialchars($selectedMenu['name']); ?></div>
                        <?php else: ?>
                            <select class="form-select" name="menu_id" required>
                                <option value="">-- Chọn món --</option>
                                <?php foreach ($menuItems as $m): ?>
                                    <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Nguyên liệu</label>
                        <select class="form-select" name="ingredient_id" required>
                            <option value="">-- Chọn nguyên liệu --</option>
                            <?php foreach ($ingredients as $ing): ?>
                                <option value="<?php echo $ing['id']; ?>"><?php echo htmlspecialchars($ing['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Số lượng (qty)</label>
                        <input class="form-control" type="number" step="0.001" name="qty" required>
                    </div>

                    <div class="mt-3">
                        <button class="btn btn-primary" type="submit">Lưu</button>
                        <a href="<?php echo BASE_URL; ?>/recipe" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>