<?php

/**
 * Thêm xuất kho / Điều chỉnh kho
 */
include BASE_PATH . '/app/views/layouts/header.php';
include BASE_PATH . '/app/views/layouts/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid mt-4 mb-5">
        <div class="row mb-4">
            <div class="col-12">
                <a href="<?php echo BASE_URL; ?>/report/stock_report" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-box-seam"></i> Thêm xuất kho</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo BASE_URL; ?>/report/save_stock_out" id="stockOutForm">
                            <!-- Loại xuất kho -->
                            <div class="mb-3">
                                <label for="issueType" class="form-label">Loại xuất kho <span class="text-danger">*</span></label>
                                <select class="form-select" name="issue_type" id="issueType" required>
                                    <option value="waste">Hỏng / Mất / Thất thoát</option>
                                    <option value="manual">Điều chỉnh / Kiểm kê</option>
                                </select>
                                <small class="text-muted d-block mt-2">
                                    <strong>Hỏng / Mất:</strong> Nguyên liệu hỏng do bảo quản, mất do quên, thất thoát<br>
                                    <strong>Điều chỉnh:</strong> Hiệu chỉnh số liệu khi kiểm kê kho không khớp
                                </small>
                            </div>

                            <!-- Ngày xuất kho -->
                            <div class="mb-3">
                                <label for="issueDate" class="form-label">Ngày xuất kho <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="issue_date" id="issueDate" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <!-- Ghi chú -->
                            <div class="mb-3">
                                <label for="note" class="form-label">Ghi chú</label>
                                <textarea class="form-control" name="note" id="note" rows="3" placeholder="VD: Rau muống bị héo do quên trong kho, cá hoen bị hỏng..."></textarea>
                            </div>

                            <!-- Chi tiết xuất kho -->
                            <div class="mb-4">
                                <label class="form-label">Chi tiết xuất kho <span class="text-danger">*</span></label>
                                <div id="itemsContainer">
                                    <div class="row mb-3 item-row">
                                        <div class="col-md-6">
                                            <select class="form-select ingredient-select" name="items[0][ingredient_id]" required>
                                                <option value="">-- Chọn nguyên liệu --</option>
                                                <?php foreach ($ingredients as $ing): ?>
                                                    <option value="<?php echo $ing['id']; ?>" data-unit="<?php echo htmlspecialchars($ing['unit'] ?? 'cái'); ?>">
                                                        <?php echo htmlspecialchars($ing['code'] . ' - ' . $ing['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <input type="number" class="form-control qty-input" name="items[0][qty]" placeholder="Số lượng" min="1" step="0.1">
                                                <span class="input-group-text unit-display">cái</span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-btn" style="display: none;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-outline-primary btn-sm" id="addItemBtn">
                                    <i class="bi bi-plus"></i> Thêm dòng
                                </button>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-check"></i> Lưu xuất kho
                                </button>
                                <a href="<?php echo BASE_URL; ?>/report/stock_report" class="btn btn-secondary">
                                    <i class="bi bi-x"></i> Hủy
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let rowCount = 1;

            // Update unit when ingredient selected
            document.getElementById('itemsContainer').addEventListener('change', function(e) {
                if (e.target.classList.contains('ingredient-select')) {
                    const unit = e.target.options[e.target.selectedIndex].dataset.unit || 'cái';
                    const unitDisplay = e.target.closest('.item-row').querySelector('.unit-display');
                    unitDisplay.textContent = unit;
                }
            });

            // Add new item row
            document.getElementById('addItemBtn').addEventListener('click', function() {
                const container = document.getElementById('itemsContainer');
                const newRow = document.createElement('div');
                newRow.className = 'row mb-3 item-row';
                newRow.innerHTML = `
            <div class="col-md-6">
                <select class="form-select ingredient-select" name="items[${rowCount}][ingredient_id]" required>
                    <option value="">-- Chọn nguyên liệu --</option>
                    <?php foreach ($ingredients as $ing): ?>
                        <option value="<?php echo $ing['id']; ?>" data-unit="<?php echo htmlspecialchars($ing['unit'] ?? 'cái'); ?>">
                            <?php echo htmlspecialchars($ing['code'] . ' - ' . $ing['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <input type="number" class="form-control qty-input" name="items[${rowCount}][qty]" placeholder="Số lượng" min="1" step="0.1">
                    <span class="input-group-text unit-display">cái</span>
                </div>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger btn-sm remove-btn">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
                container.appendChild(newRow);
                rowCount++;
                updateRemoveButtons();
            });

            // Remove item row
            document.getElementById('itemsContainer').addEventListener('click', function(e) {
                if (e.target.closest('.remove-btn')) {
                    e.preventDefault();
                    e.target.closest('.item-row').remove();
                    updateRemoveButtons();
                }
            });

            function updateRemoveButtons() {
                const rows = document.querySelectorAll('.item-row');
                rows.forEach(row => {
                    const removeBtn = row.querySelector('.remove-btn');
                    removeBtn.style.display = rows.length > 1 ? 'block' : 'none';
                });
            }

            // Form validation
            document.getElementById('stockOutForm').addEventListener('submit', function(e) {
                const items = document.querySelectorAll('.item-row');
                let hasValidItem = false;

                items.forEach(row => {
                    const ingredientId = row.querySelector('.ingredient-select').value;
                    const qty = row.querySelector('.qty-input').value;
                    if (ingredientId && qty > 0) {
                        hasValidItem = true;
                    }
                });

                if (!hasValidItem) {
                    e.preventDefault();
                    alert('Vui lòng chọn ít nhất một nguyên liệu để xuất');
                }
            });

            updateRemoveButtons();
        });
    </script>

</div>
</div>
</body>

</html>