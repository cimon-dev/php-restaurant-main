<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Tạo đơn hàng</h3>
            <a href="<?php echo BASE_URL; ?>/sale_order" class="btn btn-secondary">Quay lại</a>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'warning' ? 'warning' : 'danger'); ?> alert-dismissible fade show" role="alert">
                <i class="bi bi-<?php echo $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'warning' ? 'exclamation-triangle' : 'exclamation-circle'); ?>"></i>
                <?php echo $flash['message']; ?>

                <?php if ($flash['type'] === 'warning' && isset($_SESSION['inventory_warnings'])): ?>
                    <hr>
                    <div class="mt-2">
                        <strong>Chi tiết nguyên liệu thiếu:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($_SESSION['inventory_warnings'] as $warning): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($warning['menu_name']); ?></strong> (x<?php echo $warning['menu_qty']; ?>):
                                    <ul>
                                        <?php foreach ($warning['missing'] as $missing): ?>
                                            <li>
                                                <span class="text-danger">
                                                    <?php echo htmlspecialchars($missing['ingredient_name']); ?> (<?php echo $missing['code']; ?>):
                                                    Cần <?php echo number_format($missing['needed'], 2); ?>
                                                    <?php echo htmlspecialchars($missing['unit']); ?>,
                                                    còn <?php echo number_format($missing['available'], 2); ?>
                                                    <?php echo htmlspecialchars($missing['unit']); ?>
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['inventory_warnings']); ?>
                <?php endif; ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post" action="<?php echo BASE_URL; ?>/sale_order/store">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label><i class="bi bi-table me-1"></i>Bàn (chỉ bàn trống)</label>
                                <select name="table_id" class="form-control" required>
                                    <option value="">-- Chọn bàn --</option>
                                    <?php
                                    $freeTables = array_filter($tables, function ($t) {
                                        return $t['status'] === 'free';
                                    });
                                    foreach ($freeTables as $t):
                                    ?>
                                        <option value="<?php echo $t['id']; ?>">
                                            <?php echo htmlspecialchars($t['number']); ?> - Trống
                                        </option>
                                    <?php endforeach; ?>
                                    <?php if (empty($freeTables)): ?>
                                        <option value="" disabled>Không có bàn trống</option>
                                    <?php endif; ?>
                                </select>
                                <small class="text-muted">Chỉ hiển thị những bàn đang trống</small>
                            </div>

                            <div class="mb-3">
                                <label><i class="bi bi-people me-1"></i>Số lượng khách</label>
                                <input type="number" name="guest_count" id="guest_count" class="form-control" min="1" value="1" required>
                                <small class="text-muted">Dùng làm số lượng mặc định khi chọn món</small>
                            </div>

                            <div class="mb-3">
                                <label>Thời gian</label>
                                <input type="datetime-local" name="order_time" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label>Trạng thái</label>
                                <select name="status" class="form-control">
                                    <option value="open">Mở</option>
                                    <option value="served">Đã phục vụ</option>
                                    <option value="paid">Đã thanh toán</option>
                                    <option value="cancel">Hủy</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <h5>Danh sách món</h5>

                            <div id="order-items" class="mb-3">
                                <!-- dynamic rows will be inserted here -->
                            </div>

                            <div class="mb-3">
                                <button type="button" id="add-item" class="btn btn-sm btn-outline-primary">Thêm món</button>
                            </div>

                            <template id="order-row-template">
                                <div class="order-row d-flex align-items-center mb-2">
                                    <select class="form-control menu-select me-2">
                                        <option value="">-- Chọn món --</option>
                                    </select>
                                    <input type="number" class="form-control qty-input me-2" min="1" value="1" style="width:100px;">
                                    <button type="button" class="btn btn-danger btn-sm remove-item">Xóa</button>
                                </div>
                            </template>

                            <script>
                                (function() {
                                    const menuItems = <?php echo json_encode($menuItems); ?>;
                                    const form = document.currentScript.closest('form');
                                    const container = document.getElementById('order-items');
                                    const template = document.getElementById('order-row-template').content;
                                    const addBtn = document.getElementById('add-item');
                                    const guestInput = document.getElementById('guest_count');
                                    let guestCount = guestInput ? Math.max(parseInt(guestInput.value, 10) || 1, 1) : 1;

                                    function syncEmptyRowQty() {
                                        const rows = container.querySelectorAll('.order-row');
                                        rows.forEach(r => {
                                            const sel = r.querySelector('.menu-select');
                                            const qtyInput = r.querySelector('.qty-input');
                                            if (!sel.value) {
                                                qtyInput.value = guestCount;
                                            }
                                        });
                                    }

                                    if (guestInput) {
                                        guestInput.addEventListener('input', () => {
                                            const val = Math.max(parseInt(guestInput.value, 10) || 1, 1);
                                            guestCount = val;
                                            guestInput.value = val;
                                            syncEmptyRowQty();
                                        });
                                    }

                                    function buildOptions(select, selectedId) {
                                        select.innerHTML = '<option value="">-- Chọn món --</option>';

                                        // collect selected ids in other selects
                                        const selectedElsewhere = Array.from(container.querySelectorAll('.menu-select'))
                                            .filter(s => s !== select)
                                            .map(s => s.value)
                                            .filter(v => v);

                                        menuItems.forEach(m => {
                                            // skip if selected in another select
                                            if (selectedElsewhere.includes(String(m.id)) && String(m.id) !== String(selectedId)) return;
                                            const opt = document.createElement('option');
                                            opt.value = m.id;
                                            opt.textContent = m.name + ' - ' + Number(m.price).toFixed(2);
                                            if (selectedId && selectedId == m.id) opt.selected = true;
                                            select.appendChild(opt);
                                        });
                                    }

                                    function addRow(selectedId = '', qty) {
                                        const node = document.importNode(template, true);
                                        const row = node.querySelector('.order-row');
                                        const select = row.querySelector('.menu-select');
                                        const qtyInput = row.querySelector('.qty-input');
                                        const removeBtn = row.querySelector('.remove-item');

                                        buildOptions(select, selectedId);
                                        qtyInput.value = qty !== undefined && qty !== null ? qty : guestCount;

                                        removeBtn.addEventListener('click', () => {
                                            row.remove();
                                        });

                                        select.addEventListener('change', () => {
                                            // if last row selected, add a new empty row
                                            const rows = container.querySelectorAll('.order-row');
                                            const last = rows[rows.length - 1];
                                            if (last === row && select.value) {
                                                addRow();
                                            }
                                            // refresh options to remove selected item from other selects
                                            refreshAllOptions();
                                        });

                                        container.appendChild(row);
                                    }

                                    // initialize with one empty row
                                    addRow();

                                    addBtn.addEventListener('click', () => addRow());

                                    function refreshAllOptions() {
                                        const selects = container.querySelectorAll('.menu-select');
                                        selects.forEach(s => {
                                            const current = s.value;
                                            buildOptions(s, current);
                                        });
                                    }

                                    // on submit, compile qty[menuId] hidden inputs
                                    form.addEventListener('submit', (e) => {
                                        // remove previous compiled inputs
                                        const prev = form.querySelectorAll('input.compiled-qty');
                                        prev.forEach(p => p.remove());

                                        const totals = {};
                                        const rows = container.querySelectorAll('.order-row');
                                        rows.forEach(r => {
                                            const sel = r.querySelector('.menu-select');
                                            const q = parseInt(r.querySelector('.qty-input').value) || 0;
                                            const id = sel.value;
                                            if (!id || q <= 0) return;
                                            totals[id] = (totals[id] || 0) + q;
                                        });

                                        // ensure options are refreshed before submit to avoid stale selections
                                        refreshAllOptions();

                                        Object.keys(totals).forEach(id => {
                                            const input = document.createElement('input');
                                            input.type = 'hidden';
                                            input.name = 'qty[' + id + ']';
                                            input.value = totals[id];
                                            input.className = 'compiled-qty';
                                            form.appendChild(input);
                                        });
                                    });
                                })();
                            </script>
                        </div>
                    </div>

                    <button class="btn btn-primary">Tạo</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>