<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h3>Danh sách món ăn</h3>
            <a href="<?php echo BASE_URL; ?>/recipe/create" class="btn btn-primary">Tạo công thức mới</a>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="get" action="<?php echo BASE_URL; ?>/recipe" class="mb-2" id="menu-search-form">
                    <div class="row g-2">
                        <div class="col-md-8">
                            <input type="search" name="q" id="menu-search" class="form-control" placeholder="Tìm theo tên món..." value="<?php echo isset($q) ? htmlspecialchars($q) : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary">Tìm</button>
                            <a href="<?php echo BASE_URL; ?>/recipe" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="mt-3">
                    <div id="menu-list" class="list-group">
                        <?php if (!empty($menuItems)): ?>
                            <?php foreach ($menuItems as $m): ?>
                                <a href="<?php echo BASE_URL; ?>/recipe?menu_id=<?php echo $m['id']; ?>" class="list-group-item list-group-item-action menu-item-row">
                                    <div class="d-flex justify-content-between">
                                        <div><?php echo htmlspecialchars($m['name']); ?></div>
                                        <div class="text-muted">ID: <?php echo $m['id']; ?></div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-3">Không tìm thấy món nào.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
    // Client-side quick filter: hides items that don't match the input as you type
    ;
    (function() {
        var input = document.getElementById('menu-search');
        if (!input) return;
        var list = document.getElementById('menu-list');
        var rows = list ? Array.prototype.slice.call(list.querySelectorAll('.menu-item-row')) : [];

        input.addEventListener('input', function(e) {
            var v = (e.target.value || '').toLowerCase().trim();
            if (v === '') {
                rows.forEach(function(r) {
                    r.style.display = '';
                });
                return;
            }
            rows.forEach(function(r) {
                var text = r.textContent.toLowerCase();
                r.style.display = text.indexOf(v) >= 0 ? '' : 'none';
            });
        });
    })();
</script>