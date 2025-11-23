<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="mb-3">
            <h3>Chỉnh sửa món</h3>
        </div>

        <?php if ($flash): ?>
            <div class="alert <?php echo $flash['type'] === 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <?php if (!isset($item)): ?>
            <div class="alert alert-warning">Không tìm thấy món.</div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>/menu_item/update/<?php echo $item['id']; ?>" method="post">
                        <div class="mb-2">
                            <label class="form-label">Mã (code)</label>
                            <input class="form-control" type="text" name="code" required value="<?php echo htmlspecialchars($item['code']); ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Tên</label>
                            <input class="form-control" type="text" name="name" required value="<?php echo htmlspecialchars($item['name']); ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Giá</label>
                            <input class="form-control" type="number" step="0.01" name="price" required value="<?php echo $item['price']; ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="description"><?php echo htmlspecialchars($item['description']); ?></textarea>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-primary" type="submit">Cập nhật</button>
                            <a href="<?php echo BASE_URL; ?>/menu_item" class="btn btn-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>