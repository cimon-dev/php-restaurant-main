<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Chỉnh sửa phiếu nhập #<?php echo $item['id']; ?></h3>
            <a href="<?php echo BASE_URL; ?>/inventory_receipt" class="btn btn-secondary">Quay lại</a>
        </div>

        <?php if ($flash): ?>
            <div class="alert <?php echo $flash['type'] === 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post" action="<?php echo BASE_URL; ?>/inventory_receipt/update/<?php echo $item['id']; ?>">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Nhà cung cấp</label>
                                <input type="text" name="supplier" class="form-control" value="<?php echo htmlspecialchars($item['supplier']); ?>">
                            </div>

                            <div class="mb-3">
                                <label>Ngày</label>
                                <input type="date" name="receipt_date" class="form-control" value="<?php echo htmlspecialchars($item['receipt_date']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Ghi chú</label>
                                <textarea name="note" class="form-control"><?php echo htmlspecialchars($item['note']); ?></textarea>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <h5>Chi tiết</h5>
                            <div id="receipt-items"></div>
                            <template id="receipt-row-template">
                                <div class="receipt-row d-flex align-items-center mb-2">
                                    <select class="form-control ingredient-select me-2">
                                        <option value="">-- Chọn nguyên liệu --</option>
                                    </select>
                                    <input type="number" class="form-control qty-input me-2" min="1" value="1" style="width:100px;">
                                    <input type="number" step="0.01" class="form-control price-input me-2" min="0" value="0" style="width:140px;">
                                    <button type="button" class="btn btn-danger btn-sm remove-item">Xóa</button>
                                </div>
                            </template>

                            <div class="mb-3 mt-2">
                                <button type="button" id="add-item" class="btn btn-sm btn-outline-primary">Thêm dòng</button>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary">Lưu</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const ingredients = <?php echo json_encode($ingredients); ?>;
        const initial = <?php echo json_encode($details); ?>;
        const container = document.getElementById('receipt-items');
        const template = document.getElementById('receipt-row-template').content;
        const addBtn = document.getElementById('add-item');
        const form = addBtn.closest('form');

        function buildOptions(select, selectedId) {
            select.innerHTML = '<option value="">-- Chọn nguyên liệu --</option>';
            ingredients.forEach(i => {
                const opt = document.createElement('option');
                opt.value = i.id;
                opt.textContent = i.name;
                if (selectedId && selectedId == i.id) opt.selected = true;
                select.appendChild(opt);
            });
        }

        function addRow(selectedId = '', qty = 1, price = 0) {
            const node = document.importNode(template, true);
            const row = node.querySelector('.receipt-row');
            const select = row.querySelector('.ingredient-select');
            const qtyInput = row.querySelector('.qty-input');
            const priceInput = row.querySelector('.price-input');
            const removeBtn = row.querySelector('.remove-item');

            buildOptions(select, selectedId);
            qtyInput.value = qty;
            priceInput.value = price;

            removeBtn.addEventListener('click', () => row.remove());

            container.appendChild(row);
        }

        // initialize with existing details
        if (initial && initial.length) {
            initial.forEach(d => addRow(d.ingredient_id, d.qty, d.unit_price));
        } else {
            addRow('', 1, 0);
        }

        addBtn.addEventListener('click', () => addRow('', 1, 0));

        // subtotal & total helpers
        function updateRowSubtotal(row) {
            const q = parseFloat(row.querySelector('.qty-input').value) || 0;
            const p = parseFloat(row.querySelector('.price-input').value) || 0;
            const sub = q * p;
            // if subtotal element exists, set it (edit template may not have one)
            const subEl = row.querySelector('.subtotal');
            if (subEl) subEl.textContent = sub.toFixed(2);
            updateTotal();
        }

        function updateTotal() {
            let total = 0;
            const rows = container.querySelectorAll('.receipt-row');
            rows.forEach(r => {
                const s = parseFloat(r.querySelector('.subtotal')?.textContent || 0) || 0;
                total += s;
            });
            let totalEl = document.getElementById('receipt-total');
            if (!totalEl) {
                // create total display if missing
                const parent = container.parentElement;
                const div = document.createElement('div');
                div.className = 'd-flex justify-content-end mt-2';
                div.innerHTML = '<div class="fw-bold me-3">Tổng:</div><div id="receipt-total" class="fw-bold">0.00</div>';
                parent.appendChild(div);
                totalEl = document.getElementById('receipt-total');
            }
            totalEl.textContent = total.toFixed(2);
        }

        // listen for qty/price input changes at container level
        container.addEventListener('input', (e) => {
            const row = e.target.closest('.receipt-row');
            if (!row) return;
            if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
                updateRowSubtotal(row);
            }
        });

        // initialize subtotals for existing rows
        (function initSubtotals() {
            const rows = container.querySelectorAll('.receipt-row');
            rows.forEach(r => {
                // ensure subtotal element exists in template - if not, add it
                if (!r.querySelector('.subtotal')) {
                    const priceInput = r.querySelector('.price-input');
                    priceInput.insertAdjacentHTML('afterend', '<div class="subtotal me-2" style="width:120px;text-align:right;">0.00</div>');
                }
                updateRowSubtotal(r);
            });
        })();

        form.addEventListener('submit', () => {
            const prev = form.querySelectorAll('input.compiled');
            prev.forEach(p => p.remove());

            const rows = container.querySelectorAll('.receipt-row');
            rows.forEach(r => {
                const sel = r.querySelector('.ingredient-select');
                const id = sel.value;
                const q = r.querySelector('.qty-input').value || 0;
                const p = r.querySelector('.price-input').value || 0;
                if (!id || q <= 0) return;

                const i1 = document.createElement('input');
                i1.type = 'hidden';
                i1.name = 'ingredient_id[]';
                i1.value = id;
                i1.className = 'compiled';
                form.appendChild(i1);
                const i2 = document.createElement('input');
                i2.type = 'hidden';
                i2.name = 'qty[]';
                i2.value = q;
                i2.className = 'compiled';
                form.appendChild(i2);
                const i3 = document.createElement('input');
                i3.type = 'hidden';
                i3.name = 'unit_price[]';
                i3.value = p;
                i3.className = 'compiled';
                form.appendChild(i3);
            });
        });
    })();
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>