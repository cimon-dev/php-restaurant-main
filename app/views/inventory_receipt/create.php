<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Tạo phiếu nhập</h3>
            <a href="<?php echo BASE_URL; ?>/inventory_receipt" class="btn btn-secondary">Quay lại</a>
        </div>

        <?php if ($flash): ?>
            <div class="alert <?php echo $flash['type'] === 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post" action="<?php echo BASE_URL; ?>/inventory_receipt/store">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Nhà cung cấp</label>
                                <input type="text" name="supplier" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label>Ngày</label>
                                <input type="date" name="receipt_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Ghi chú</label>
                                <textarea name="note" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <h5>Chi tiết</h5>

                            <div class="mb-1 d-flex fw-bold small">
                                <div style="width:40%;">Nguyên liệu</div>
                                <div style="width:100px;text-align:right;">Số lượng</div>
                                <div style="width:140px;text-align:right;">Đơn giá</div>
                                <div style="width:120px;text-align:right;">Thành tiền</div>
                                <div style="width:80px;text-align:center;">Hành động</div>
                            </div>

                            <div id="receipt-items"></div>
                            <template id="receipt-row-template">
                                <div class="receipt-row d-flex align-items-center mb-2">
                                    <select class="form-control ingredient-select me-2" style="width:40%;">
                                        <option value="">-- Chọn nguyên liệu --</option>
                                    </select>
                                    <input type="number" class="form-control qty-input me-2" min="1" value="1" style="width:100px;text-align:right;">
                                    <input type="number" step="0.01" class="form-control price-input me-2" min="0" value="0" style="width:140px;text-align:right;">
                                    <div class="subtotal me-2" style="width:120px;text-align:right;">0.00</div>
                                    <button type="button" class="btn btn-danger btn-sm remove-item" style="width:80px;">Xóa</button>
                                </div>
                            </template>

                            <div class="mb-3 mt-2">
                                <button type="button" id="add-item" class="btn btn-sm btn-outline-primary">Thêm dòng</button>
                            </div>

                            <div class="d-flex justify-content-end mt-2">
                                <div class="fw-bold me-3">Tổng:</div>
                                <div id="receipt-total" class="fw-bold">0.00</div>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary">Tạo phiếu</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const ingredients = <?php echo json_encode($ingredients); ?>;
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

        addBtn.addEventListener('click', () => addRow('', 1, 0));
        // start with one row
        addRow('', 1, 0);

        // subtotal & total helpers
        function updateRowSubtotal(row) {
            const q = parseFloat(row.querySelector('.qty-input').value) || 0;
            const p = parseFloat(row.querySelector('.price-input').value) || 0;
            const sub = q * p;
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
            const totalEl = document.getElementById('receipt-total');
            if (totalEl) totalEl.textContent = total.toFixed(2);
        }

        // listen for qty/price input changes at container level
        container.addEventListener('input', (e) => {
            const row = e.target.closest('.receipt-row');
            if (!row) return;
            if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
                updateRowSubtotal(row);
            }
        });

        // initialize subtotals
        (function initSubtotals() {
            const rows = container.querySelectorAll('.receipt-row');
            rows.forEach(r => updateRowSubtotal(r));
        })();

        form.addEventListener('submit', () => {
            // compile arrays into inputs: ingredient_id[], qty[], unit_price[]
            // remove previous compiled
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