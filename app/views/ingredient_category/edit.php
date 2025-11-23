<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <h3>Chỉnh sửa loại</h3>

        <form method="post" action="<?php echo BASE_URL; ?>/ingredient_category/update/<?php echo $item['id']; ?>">
            <div class="form-group">
                <label for="name">Tên</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($item['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Mô tả</label>
                <textarea name="description" id="description" class="form-control"><?php echo htmlspecialchars($item['description']); ?></textarea>
            </div>

            <button class="btn btn-primary">Cập nhật</button>
            <a href="<?php echo BASE_URL; ?>/ingredient_category" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>