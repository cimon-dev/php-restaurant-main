<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Tạo phiếu xuất</h3>
            <a href="<?php echo BASE_URL; ?>/inventory_issue" class="btn btn-secondary">Quay lại</a>
        </div>

        <?php if ($flash): ?>
            <div class="alert <?php echo $flash['type'] === 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post" action="<?php echo BASE_URL; ?>/inventory_issue/store">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Loại</label>
                                <select name="issue_type" class="form-select">
                                    <option value="sale">sale</option>
                                    <option value="manual">manual</option>
                                    <option value="waste">waste</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Ngày</label>
                                <input type="date" name="issue_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Ghi chú</label>
                                <textarea name="note" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <h5>Chi tiết</h5>

                            <div class="mb-1 d-flex fw-bold small">
                                <div style="width:60%;">Nguyên liệu</div>
                                <div style="width:120px;text-align:right;">Số lượng</div>
                                <div style="width:80px;text-align:center;">Hành động</div>
                            </div>

                            <div id="issue-items"></div>
                            <template id="issue-row-template">
                                <div class="issue-row d-flex align-items-center mb-2">
                                    <select class="form-control ingredient-select me-2" style="width:60%;">
                                        <option value="">-- Chọn nguyên liệu --</option>
                                    </select>
                                    <input type="number" class="form-control qty-input me-2" min="1" value="1" style="width:120px;text-align:right;">
                                    <button type="button" class="btn btn-danger btn-sm remove-item" style="width:80px;">Xóa</button>
                                </div>
                            </template>

                            <div class="mb-3 mt-2">
                                <button type="button" id="add-issue-item" class="btn btn-sm btn-outline-primary">Thêm dòng</button>
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
        const container = document.getElementById('issue-items');
        const template = document.getElementById('issue-row-template').content;
        const addBtn = document.getElementById('add-issue-item');
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

        function addRow(selectedId = '', qty = 1) {
            const node = document.importNode(template, true);
            const row = node.querySelector('.issue-row');
            const select = row.querySelector('.ingredient-select');
            const qtyInput = row.querySelector('.qty-input');
            const removeBtn = row.querySelector('.remove-item');

            buildOptions(select, selectedId);
            qtyInput.value = qty;

            removeBtn.addEventListener('click', () => row.remove());

            container.appendChild(row);
        }

        addBtn.addEventListener('click', () => addRow('', 1));
        // start with one row
        addRow('', 1);

        form.addEventListener('submit', () => {
            // compile arrays into inputs: ingredient_id[], qty[]
            const prev = form.querySelectorAll('input.compiled');
            prev.forEach(p => p.remove());

            const rows = container.querySelectorAll('.issue-row');
            rows.forEach(r => {
                const sel = r.querySelector('.ingredient-select');
                const id = sel.value;
                const q = r.querySelector('.qty-input').value || 0;
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
            });
        });
    })();
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>