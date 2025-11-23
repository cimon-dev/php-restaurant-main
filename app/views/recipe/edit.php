<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="mb-3">
            <h3>Chỉnh sửa công thức</h3>
        </div>

        <?php if ($flash): ?>
            <div class="alert <?php echo $flash['type'] === 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <?php if (!isset($item)): ?>
            <div class="alert alert-warning">Không tìm thấy công thức.</div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>/recipe/update/<?php echo $item['id']; ?>" method="post">
                        <div class="mb-2">
                            <label class="form-label">Món</label>
                            <select class="form-select" name="menu_id" required>
                                <option value="">-- Chọn món --</option>
                                <?php foreach ($menuItems as $m): ?>
                                    <option value="<?php echo $m['id']; ?>" <?php echo $m['id'] == $item['menu_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($m['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Nguyên liệu</label>
                            <select class="form-select" name="ingredient_id" required>
                                <option value="">-- Chọn nguyên liệu --</option>
                                <?php foreach ($ingredients as $ing): ?>
                                    <option value="<?php echo $ing['id']; ?>" <?php echo $ing['id'] == $item['ingredient_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($ing['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Số lượng (qty)</label>
                            <input class="form-control" type="number" step="0.001" name="qty" value="<?php echo $item['qty']; ?>" required>
                        </div>

                        <div class="mt-3">
                            <button class="btn btn-primary" type="submit">Cập nhật</button>
                            <a href="<?php echo BASE_URL; ?>/recipe" class="btn btn-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>