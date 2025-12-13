<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .receipt-header-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .receipt-info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .receipt-info-label {
        font-size: 0.85rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .receipt-info-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 0.5rem;
    }

    .receipt-form-section {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #667eea;
        color: #667eea;
    }

    .receipt-row {
        background: #f8f9fa;
        padding: 0.75rem;
        border-radius: 6px;
        margin-bottom: 0.75rem;
        transition: background 0.2s;
    }

    .receipt-row:hover {
        background: #e9ecef;
    }

    .receipt-row input,
    .receipt-row select {
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }

    .receipt-total-section {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        border-left: 4px solid #16a34a;
        padding: 1rem;
        border-radius: 6px;
        margin-top: 1rem;
    }

    .total-label {
        font-size: 0.95rem;
        color: #666;
    }

    .total-amount {
        font-size: 2rem;
        font-weight: 700;
        color: #16a34a;
    }

    .action-buttons {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 0.75rem 2rem;
    }

    .btn-submit:hover {
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        color: white;
    }
</style>

<div class="main-content">
    <div class="container-fluid mt-4 mb-5">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0" style="font-weight: 700;">
                    <i class="bi bi-box-arrow-in-right"></i> Tạo phiếu nhập kho
                </h1>
                <p class="text-muted small mt-1">Ghi nhận nguyên liệu từ nhà cung cấp</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/inventory_receipt" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>

        <?php
        $flash = getFlash();
        if ($flash):
        ?>
            <div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <i class="bi bi-<?php echo $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $flash['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo BASE_URL; ?>/inventory_receipt/store" id="receiptForm">
            <!-- Thông tin nhập kho -->
            <div class="receipt-form-section">
                <div class="section-title"><i class="bi bi-info-circle"></i> Thông tin phiếu nhập</div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="supplier" class="form-label"><strong>Nhà cung cấp</strong></label>
                            <input type="text" id="supplier" name="supplier" class="form-control" placeholder="VD: Công ty ABC, Nhà cung cấp XYZ">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="receipt_date" class="form-label"><strong>Ngày nhập</strong></label>
                            <input type="date" id="receipt_date" name="receipt_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="note" class="form-label">Ghi chú (tùy chọn)</label>
                    <textarea id="note" name="note" class="form-control" rows="2" placeholder="Ghi chú thêm về phiếu nhập..."></textarea>
                </div>
            </div>

            <!-- Chi tiết nguyên liệu -->
            <div class="receipt-form-section">
                <div class="section-title"><i class="bi bi-list-check"></i> Chi tiết nguyên liệu</div>

                <!-- Header hàng -->
                <div class="row fw-bold small text-muted mb-2 px-2">
                    <div class="col-md-4">Nguyên liệu</div>
                    <div class="col-md-2 text-end">Số lượng</div>
                    <div class="col-md-3 text-end">Đơn giá (đ)</div>
                    <div class="col-md-2 text-end">Thành tiền (đ)</div>
                    <div class="col-md-1 text-center">Hành động</div>
                </div>

                <div id="receipt-items"></div>

                <button type="button" id="add-item" class="btn btn-outline-primary btn-sm mt-2">
                    <i class="bi bi-plus-circle"></i> Thêm dòng
                </button>
            </div>

            <!-- Tổng tiền -->
            <div class="receipt-total-section">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="total-label">Tổng cộng</div>
                        <div class="total-amount" id="receipt-total">0</div>
                    </div>
                    <div class="text-muted small">
                        <div id="item-count">0 mục</div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button type="submit" class="btn btn-primary btn-submit">
                    <i class="bi bi-check-circle"></i> Tạo phiếu nhập
                </button>
                <a href="<?php echo BASE_URL; ?>/inventory_receipt" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Hủy
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Template cho dòng nhập kho -->
<template id="receipt-row-template">
    <div class="receipt-row row align-items-end">
        <div class="col-md-4">
            <select class="form-control form-control-sm ingredient-select" required>
                <option value="">-- Chọn nguyên liệu --</option>
                <?php foreach ($ingredients as $ing): ?>
                    <option value="<?php echo $ing['id']; ?>" data-price="<?php echo $ing['purchase_price'] ?? 0; ?>">
                        <?php echo htmlspecialchars($ing['code'] . ' - ' . $ing['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" class="form-control form-control-sm text-end qty-input" min="1" value="1" step="0.01" required>
        </div>
        <div class="col-md-3">
            <input type="number" class="form-control form-control-sm text-end price-input" min="0" value="0" step="0.01" required>
        </div>
        <div class="col-md-2">
            <div class="text-end subtotal">0</div>
        </div>
        <div class="col-md-1 text-center">
            <button type="button" class="btn btn-danger btn-sm remove-item" title="Xóa dòng">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
</template>
</div>
</div>

<script>
    (function() {
        const ingredients = <?php echo json_encode($ingredients); ?>;
        const container = document.getElementById('receipt-items');
        const template = document.getElementById('receipt-row-template').content;
        const addBtn = document.getElementById('add-item');
        const form = document.getElementById('receiptForm');
        const fromRestock = <?php echo isset($fromRestock) && $fromRestock ? 'true' : 'false'; ?>;

        function buildOptions(select, selectedId) {
            select.innerHTML = '<option value="">-- Chọn nguyên liệu --</option>';
            ingredients.forEach(i => {
                const opt = document.createElement('option');
                opt.value = i.id;
                opt.textContent = `${i.code} - ${i.name}`;
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

            removeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                row.remove();
                updateCounts();
            });

            // Set default price when ingredient selected
            select.addEventListener('change', function() {
                const ing = ingredients.find(i => i.id == this.value);
                if (ing && ing.purchase_price) {
                    priceInput.value = ing.purchase_price;
                    updateRowSubtotal(row);
                } else if (ing) {
                    priceInput.value = 0;
                    updateRowSubtotal(row);
                }
            });

            // Update totals when quantity or price changes
            qtyInput.addEventListener('input', () => updateRowSubtotal(row));
            priceInput.addEventListener('input', () => updateRowSubtotal(row));

            container.appendChild(row);

            // Calculate subtotal if ingredient and price are set
            if (selectedId && price > 0) {
                updateRowSubtotal(row);
            }

            updateCounts();
        }

        addBtn.addEventListener('click', (e) => {
            e.preventDefault();
            addRow('', 1, 0);
        });

        // Load from restock cart if available
        if (fromRestock) {
            const cart = JSON.parse(sessionStorage.getItem('restockCart') || '[]');
            if (cart.length > 0) {
                cart.forEach(item => {
                    const ing = ingredients.find(i => i.id == item.ingredient_id);
                    if (ing) {
                        addRow(item.ingredient_id, item.qty, ing.purchase_price || 0);
                    }
                });
                sessionStorage.removeItem('restockCart');
            } else {
                addRow('', 1, 0);
            }
        } else if (<?php echo isset($quickIngredient) && $quickIngredient ? 'true' : 'false'; ?>) {
            addRow(<?php echo isset($quickIngredient) ? $quickIngredient['id'] : 'null'; ?>, <?php echo isset($quickQty) ? $quickQty : 1; ?>, <?php echo isset($quickIngredient) ? ($quickIngredient['purchase_price'] ?? 0) : 0; ?>);
        } else {
            addRow('', 1, 0);
        }

        // Initial calculation
        updateTotal();

        // Subtotal & total helpers
        function formatVND(num) {
            return new Intl.NumberFormat('vi-VN', {
                minimumFractionDigits: 0
            }).format(Math.round(num));
        }

        function updateRowSubtotal(row) {
            const q = parseFloat(row.querySelector('.qty-input').value) || 0;
            const p = parseFloat(row.querySelector('.price-input').value) || 0;
            const sub = q * p;
            const subEl = row.querySelector('.subtotal');
            if (subEl) subEl.textContent = formatVND(sub);
            updateTotal();
        }

        function updateTotal() {
            let total = 0;
            const rows = container.querySelectorAll('.receipt-row');
            rows.forEach(r => {
                const q = parseFloat(r.querySelector('.qty-input').value) || 0;
                const p = parseFloat(r.querySelector('.price-input').value) || 0;
                total += q * p;
            });
            const totalEl = document.getElementById('receipt-total');
            if (totalEl) totalEl.textContent = formatVND(total);
        }

        function updateCounts() {
            const rowCount = container.querySelectorAll('.receipt-row').length;
            const countEl = document.getElementById('item-count');
            if (countEl) countEl.textContent = `${rowCount} mục`;
        }

        // Listen for input changes
        container.addEventListener('input', (e) => {
            const row = e.target.closest('.receipt-row');
            if (!row) return;
            if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
                updateRowSubtotal(row);
            }
        });

        // Form submission
        form.addEventListener('submit', (e) => {
            // Clear previous compiled inputs
            const prev = form.querySelectorAll('input.compiled');
            prev.forEach(p => p.remove());

            const rows = container.querySelectorAll('.receipt-row');
            let hasItems = false;

            rows.forEach(r => {
                const sel = r.querySelector('.ingredient-select');
                const id = sel.value;
                const q = r.querySelector('.qty-input').value || 0;
                const p = r.querySelector('.price-input').value || 0;
                if (!id || q <= 0) return;

                hasItems = true;

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

            if (!hasItems) {
                e.preventDefault();
                alert('Vui lòng thêm ít nhất một nguyên liệu');
            }
        });

        // Initialize
        updateCounts();
    })();
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>