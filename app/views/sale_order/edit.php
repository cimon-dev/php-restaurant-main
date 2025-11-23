<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Chỉnh sửa đơn hàng #<?php echo $item['id']; ?></h3>
            <a href="<?php echo BASE_URL; ?>/sale_order" class="btn btn-secondary">Quay lại</a>
        </div>

        <?php if ($flash): ?>
            <div class="alert <?php echo $flash['type'] === 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post" action="<?php echo BASE_URL; ?>/sale_order/update/<?php echo $item['id']; ?>">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Bàn</label>
                                <select name="table_id" class="form-control">
                                    <option value="">-- Không chọn --</option>
                                    <?php foreach ($tables as $t): ?>
                                        <option value="<?php echo $t['id']; ?>" <?php echo $t['id'] == $item['table_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($t['number']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Thời gian</label>
                                <input type="datetime-local" name="order_time" class="form-control" value="<?php echo str_replace(' ', 'T', substr($item['order_time'], 0, 16)); ?>">
                            </div>

                            <div class="mb-3">
                                <label>Trạng thái</label>
                                <select name="status" class="form-control">
                                    <option value="open" <?php echo $item['status'] == 'open' ? 'selected' : ''; ?>>Mở</option>
                                    <option value="served" <?php echo $item['status'] == 'served' ? 'selected' : ''; ?>>Đã phục vụ</option>
                                    <option value="paid" <?php echo $item['status'] == 'paid' ? 'selected' : ''; ?>>Đã thanh toán</option>
                                    <option value="cancel" <?php echo $item['status'] == 'cancel' ? 'selected' : ''; ?>>Hủy</option>
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
                                    const initial = <?php echo json_encode($detailMap ?? []); ?>;
                                    const form = document.currentScript.closest('form');
                                    const container = document.getElementById('order-items');
                                    const template = document.getElementById('order-row-template').content;
                                    const addBtn = document.getElementById('add-item');

                                    function buildOptions(select, selectedId) {
                                        select.innerHTML = '<option value="">-- Chọn món --</option>';
                                        // collect selected ids in other selects
                                        const selectedElsewhere = Array.from(container.querySelectorAll('.menu-select'))
                                            .filter(s => s !== select)
                                            .map(s => s.value)
                                            .filter(v => v);

                                        menuItems.forEach(m => {
                                            if (selectedElsewhere.includes(String(m.id)) && String(m.id) !== String(selectedId)) return;
                                            const opt = document.createElement('option');
                                            opt.value = m.id;
                                            opt.textContent = m.name + ' - ' + Number(m.price).toFixed(2);
                                            if (selectedId && selectedId == m.id) opt.selected = true;
                                            select.appendChild(opt);
                                        });
                                    }

                                    function addRow(selectedId = '', qty = 1) {
                                        const node = document.importNode(template, true);
                                        const row = node.querySelector('.order-row');
                                        const select = row.querySelector('.menu-select');
                                        const qtyInput = row.querySelector('.qty-input');
                                        const removeBtn = row.querySelector('.remove-item');

                                        buildOptions(select, selectedId);
                                        qtyInput.value = qty;

                                        removeBtn.addEventListener('click', () => {
                                            row.remove();
                                        });

                                        select.addEventListener('change', () => {
                                            const rows = container.querySelectorAll('.order-row');
                                            const last = rows[rows.length - 1];
                                            if (last === row && select.value) {
                                                addRow('', 1);
                                            }
                                            refreshAllOptions();
                                        });

                                        container.appendChild(row);
                                    }

                                    // initialize with existing items
                                    let hasAny = false;
                                    Object.keys(initial).forEach(id => {
                                        addRow(id, initial[id]);
                                        hasAny = true;
                                    });

                                    if (!hasAny) addRow('', 1);

                                    addBtn.addEventListener('click', () => addRow('', 1));

                                    function refreshAllOptions() {
                                        const selects = container.querySelectorAll('.menu-select');
                                        selects.forEach(s => {
                                            const current = s.value;
                                            buildOptions(s, current);
                                        });
                                    }

                                    // on submit, compile qty[menuId] hidden inputs
                                    form.addEventListener('submit', (e) => {
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

                    <button class="btn btn-primary">Lưu</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>