<?php

/**
 * Báo cáo kho - Tình trạng tồn kho với cảnh báo và lịch sử xuất kho
 */
include BASE_PATH . '/app/views/layouts/header.php';
include BASE_PATH . '/app/views/layouts/sidebar.php';
?>

<style>
    .stock-stat-card {
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .stock-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .stat-icon {
        font-size: 2rem;
        margin-right: 1rem;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
    }

    .nav-tabs .nav-link {
        border-radius: 8px 8px 0 0;
        font-weight: 500;
        color: #6c757d;
        border: none;
        margin-right: 0.5rem;
    }

    .nav-tabs .nav-link.active {
        border-bottom: 3px solid #0d6efd;
        color: #0d6efd;
    }

    .tab-content {
        background: white;
        border-radius: 0 0 8px 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        padding: 1rem 0.75rem;
    }

    .table tbody td {
        padding: 0.75rem;
        vertical-align: middle;
    }

    .table tbody tr {
        transition: background-color 0.2s;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .stock-status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .stock-critical {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .stock-warning {
        background-color: #fef3c7;
        color: #d97706;
    }

    .stock-normal {
        background-color: #dcfce7;
        color: #16a34a;
    }

    .no-data {
        text-align: center;
        padding: 3rem 1rem;
        color: #6c757d;
    }

    .history-section {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .history-item {
        border-left: 4px solid #0d6efd;
        padding: 1rem;
        margin-bottom: 0.75rem;
        background: #f8f9fa;
        border-radius: 4px;
    }

    .history-date {
        font-weight: 600;
        color: #212529;
    }

    .history-type {
        display: inline-block;
        margin: 0.5rem 0;
    }

    .main-content {
        margin-left: 260px;
        padding: 2rem;
        transition: margin-left 0.3s;
    }

    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
        }

        .stat-number {
            font-size: 1.5rem;
        }
    }
</style>

<div class="main-content">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="h2 mb-0" style="font-weight: 700;">
                    <i class="bi bi-graph-up"></i> Báo cáo kho
                </h1>
                <p class="text-muted small mt-1">Tình trạng tồn kho, cảnh báo, và lịch sử quản lý</p>
            </div>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-success" id="restockCartBtn" style="display: none;" onclick="submitRestockCart()">
                    <i class="bi bi-check-circle"></i> Xác nhận bổ sung (<span id="cartCount">0</span>)
                </button>
                <a href="<?php echo BASE_URL; ?>/inventory_receipt" class="btn btn-outline-info">
                    <i class="bi bi-box-arrow-in-right"></i> Nhập kho
                </a>
                <a href="<?php echo BASE_URL; ?>/report/add_stock_out" class="btn btn-warning">
                    <i class="bi bi-box-seam"></i> Thêm xuất kho
                </a>
            </div>
        </div>

        <!-- Thống kê cảnh báo -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card stock-stat-card border-0" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon" style="color: #dc2626;">
                                <i class="bi bi-exclamation-circle"></i>
                            </div>
                            <div>
                                <p class="mb-1" style="color: #7f1d1d; font-weight: 500;">Hết hàng</p>
                                <div class="stat-number" style="color: #dc2626;"><?php echo $total_critical; ?></div>
                                <small style="color: #991b1b;">Số lượng âm</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stock-stat-card border-0" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon" style="color: #d97706;">
                                <i class="bi bi-lightning"></i>
                            </div>
                            <div>
                                <p class="mb-1" style="color: #78350f; font-weight: 500;">Sắp hết</p>
                                <div class="stat-number" style="color: #d97706;"><?php echo $total_warning; ?></div>
                                <small style="color: #92400e;">Dưới mức tối thiểu</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stock-stat-card border-0" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon" style="color: #16a34a;">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div>
                                <p class="mb-1" style="color: #15803d; font-weight: 500;">Bình thường</p>
                                <div class="stat-number" style="color: #16a34a;"><?php echo $total_normal; ?></div>
                                <small style="color: #166534;">Đủ hàng</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Hết hàng / Sắp hết / Bình thường -->
        <div class="mb-5">
            <ul class="nav nav-tabs mb-0" role="tablist" style="border-bottom: none;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="critical-tab" data-bs-toggle="tab" data-bs-target="#critical" type="button" role="tab" aria-controls="critical" aria-selected="true">
                        <i class="bi bi-exclamation-circle"></i> Hết hàng <span class="badge bg-danger ms-2"><?php echo $total_critical; ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="warning-tab" data-bs-toggle="tab" data-bs-target="#warning" type="button" role="tab" aria-controls="warning" aria-selected="false">
                        <i class="bi bi-lightning"></i> Sắp hết <span class="badge bg-warning ms-2 text-dark"><?php echo $total_warning; ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="normal-tab" data-bs-toggle="tab" data-bs-target="#normal" type="button" role="tab" aria-controls="normal" aria-selected="false">
                        <i class="bi bi-check-circle"></i> Bình thường <span class="badge bg-success ms-2"><?php echo $total_normal; ?></span>
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Tab: Hết hàng -->
                <div class="tab-pane fade show active" id="critical" role="tabpanel" aria-labelledby="critical-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background-color: #fee2e2;">
                                <tr>
                                    <th style="color: #dc2626;"><strong>Mã</strong></th>
                                    <th style="color: #dc2626;"><strong>Tên nguyên liệu</strong></th>
                                    <th style="color: #dc2626;"><strong>Đơn vị</strong></th>
                                    <th style="color: #dc2626;" class="text-center"><strong>Số lượng</strong></th>
                                    <th style="color: #dc2626;" class="text-center"><strong>Mức tối thiểu</strong></th>
                                    <th style="color: #dc2626;" class="text-end"><strong>Giá nhập</strong></th>
                                    <th style="color: #dc2626;" class="text-end"><strong>Giá trị kho</strong></th>
                                    <th style="color: #dc2626;" class="text-center"><strong>Trạng thái</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($critical)): ?>
                                    <tr>
                                        <td colspan="8" class="no-data">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Không có nguyên liệu nào hết hàng</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($critical as $ing): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($ing['code']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($ing['name']); ?></td>
                                            <td><?php echo htmlspecialchars($ing['unit'] ?? 'cái'); ?></td>
                                            <td class="text-center"><span class="stock-status-badge stock-critical"><?php echo (int)$ing['current_qty']; ?></span></td>
                                            <td class="text-center"><?php echo (int)($ing['min_stock'] ?? 0); ?></td>
                                            <td class="text-end"><?php echo number_format($ing['purchase_price'] ?? 0, 0, ',', '.'); ?> đ</td>
                                            <td class="text-end"><strong><?php echo number_format($ing['cost'] ?? 0, 0, ',', '.'); ?> đ</strong></td>
                                            <td class="text-center">
                                                <span class="stock-status-badge stock-critical"><i class="bi bi-exclamation-circle"></i> Hết hàng</span>
                                                <br>
                                                <button class="btn btn-sm btn-outline-danger mt-2" onclick="addQuickRestockRequest(<?php echo $ing['id']; ?>, '<?php echo htmlspecialchars($ing['name']); ?>')">
                                                    <i class="bi bi-plus-circle"></i> Bổ sung
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab: Sắp hết -->
                <div class="tab-pane fade" id="warning" role="tabpanel" aria-labelledby="warning-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background-color: #fef3c7;">
                                <tr>
                                    <th style="color: #d97706;"><strong>Mã</strong></th>
                                    <th style="color: #d97706;"><strong>Tên nguyên liệu</strong></th>
                                    <th style="color: #d97706;"><strong>Đơn vị</strong></th>
                                    <th style="color: #d97706;" class="text-center"><strong>Số lượng</strong></th>
                                    <th style="color: #d97706;" class="text-center"><strong>Mức tối thiểu</strong></th>
                                    <th style="color: #d97706;" class="text-end"><strong>Giá nhập</strong></th>
                                    <th style="color: #d97706;" class="text-end"><strong>Giá trị kho</strong></th>
                                    <th style="color: #d97706;" class="text-center"><strong>Trạng thái</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($warning)): ?>
                                    <tr>
                                        <td colspan="8" class="no-data">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Tất cả nguyên liệu đều có đủ hàng</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($warning as $ing): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($ing['code']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($ing['name']); ?></td>
                                            <td><?php echo htmlspecialchars($ing['unit'] ?? 'cái'); ?></td>
                                            <td class="text-center"><span class="stock-status-badge stock-warning"><?php echo (int)$ing['current_qty']; ?></span></td>
                                            <td class="text-center"><?php echo (int)($ing['min_stock'] ?? 0); ?></td>
                                            <td class="text-end"><?php echo number_format($ing['purchase_price'] ?? 0, 0, ',', '.'); ?> đ</td>
                                            <td class="text-end"><strong><?php echo number_format($ing['cost'] ?? 0, 0, ',', '.'); ?> đ</strong></td>
                                            <td class="text-center">
                                                <span class="stock-status-badge stock-warning"><i class="bi bi-lightning"></i> Sắp hết</span>
                                                <br>
                                                <button class="btn btn-sm btn-outline-warning mt-2" onclick="addQuickRestockRequest(<?php echo $ing['id']; ?>, '<?php echo htmlspecialchars($ing['name']); ?>')">
                                                    <i class="bi bi-plus-circle"></i> Bổ sung
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab: Bình thường -->
                <div class="tab-pane fade" id="normal" role="tabpanel" aria-labelledby="normal-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background-color: #dcfce7;">
                                <tr>
                                    <th style="color: #16a34a;"><strong>Mã</strong></th>
                                    <th style="color: #16a34a;"><strong>Tên nguyên liệu</strong></th>
                                    <th style="color: #16a34a;"><strong>Đơn vị</strong></th>
                                    <th style="color: #16a34a;" class="text-center"><strong>Số lượng</strong></th>
                                    <th style="color: #16a34a;" class="text-center"><strong>Mức tối thiểu</strong></th>
                                    <th style="color: #16a34a;" class="text-end"><strong>Giá nhập</strong></th>
                                    <th style="color: #16a34a;" class="text-end"><strong>Giá trị kho</strong></th>
                                    <th style="color: #16a34a;" class="text-center"><strong>Trạng thái</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($normal)): ?>
                                    <tr>
                                        <td colspan="8" class="no-data">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Không có nguyên liệu nào</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($normal as $ing): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($ing['code']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($ing['name']); ?></td>
                                            <td><?php echo htmlspecialchars($ing['unit'] ?? 'cái'); ?></td>
                                            <td class="text-center"><span class="stock-status-badge stock-normal"><?php echo (int)$ing['current_qty']; ?></span></td>
                                            <td class="text-center"><?php echo (int)($ing['min_stock'] ?? 0); ?></td>
                                            <td class="text-end"><?php echo number_format($ing['purchase_price'] ?? 0, 0, ',', '.'); ?> đ</td>
                                            <td class="text-end"><strong><?php echo number_format($ing['cost'] ?? 0, 0, ',', '.'); ?> đ</strong></td>
                                            <td class="text-center"><span class="stock-status-badge stock-normal"><i class="bi bi-check-circle"></i> Bình thường</span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // Render pagination controls for stock tables (place above history section)
        if (isset($pagination)) {
            $paginationVar = $pagination;
            $baseUrlVar = $baseUrl ?? (BASE_URL . '/report/stock_report');
            $pagination = $paginationVar;
            $baseUrl = $baseUrlVar;
            include BASE_PATH . '/app/views/layouts/pagination.php';
        }
        ?>

        <!-- Lịch sử xuất kho gần đây -->
        <div class="mt-4">
            <h5 class="mb-3" style="font-weight: 600;">
                <i class="bi bi-clock-history"></i> Lịch sử xuất kho (10 ngày gần đây)
            </h5>
            <div class="history-section">
                <?php if (empty($recent_issues)): ?>
                    <div class="no-data">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mt-2">Không có lịch sử xuất kho gần đây</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($recent_issues as $issue): ?>
                            <div class="col-md-6">
                                <div class="history-item">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <div class="history-date"><?php echo date('d/m/Y', strtotime($issue['issue_date'])); ?></div>
                                            <small class="text-muted">
                                                <i class="bi bi-person"></i> <?php echo htmlspecialchars($issue['created_by'] ?? 'Hệ thống'); ?>
                                            </small>
                                        </div>
                                        <div class="history-type">
                                            <?php if ($issue['issue_type'] === 'waste'): ?>
                                                <span class="badge bg-danger"><i class="bi bi-trash"></i> Hỏng/Mất</span>
                                            <?php elseif ($issue['issue_type'] === 'manual'): ?>
                                                <span class="badge bg-warning text-dark"><i class="bi bi-pencil"></i> Điều chỉnh</span>
                                            <?php else: ?>
                                                <span class="badge bg-info"><i class="bi bi-box-seam"></i> Xuất</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <small><strong>Chi tiết:</strong></small>
                                        <div style="font-size: 0.9rem; color: #495057; margin-top: 0.25rem;">
                                            <?php echo htmlspecialchars($issue['details'] ?? 'N/A'); ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($issue['note'])): ?>
                                        <div>
                                            <small><strong>Ghi chú:</strong></small>
                                            <div style="font-size: 0.9rem; color: #6c757d; font-style: italic; margin-top: 0.25rem;">
                                                <?php echo htmlspecialchars($issue['note']); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
            if (isset($pagination)) {
                $paginationVar = $pagination;
                $baseUrlVar = $baseUrl ?? (BASE_URL . '/report/stock_report');
                $pagination = $paginationVar;
                $baseUrl = $baseUrlVar;
                include BASE_PATH . '/app/views/layouts/pagination.php';
            }
            ?>
        </div>
    </div>
</div>

<script>
    // Fix Bootstrap tab click issue
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
        tabButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.dataset.bsTarget);
                if (target) {
                    // Remove active class from all panes
                    document.querySelectorAll('.tab-pane').forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });
                    // Remove active class from all buttons
                    tabButtons.forEach(btn => btn.classList.remove('active'));

                    // Add active class to current
                    target.classList.add('show', 'active');
                    this.classList.add('active');
                }
            });
        });

        // Update cart UI on page load
        updateRestockCartUI();
    });

    function updateRestockCartUI() {
        const cart = JSON.parse(sessionStorage.getItem('restockCart') || '[]');
        const btn = document.getElementById('restockCartBtn');
        const count = document.getElementById('cartCount');

        if (cart.length > 0) {
            btn.style.display = 'inline-block';
            count.textContent = cart.length;
        } else {
            btn.style.display = 'none';
            count.textContent = '0';
        }
    }

    // Quick restock request
    function addQuickRestockRequest(ingredientId, ingredientName) {
        const qty = prompt(`Nhập số lượng muốn bổ sung cho "${ingredientName}":`, '');
        if (qty && !isNaN(qty) && qty > 0) {
            // Store in sessionStorage (client-side cart)
            let cart = JSON.parse(sessionStorage.getItem('restockCart') || '[]');

            // Check if already in cart
            const existing = cart.findIndex(item => item.ingredient_id == ingredientId);
            if (existing >= 0) {
                cart[existing].qty = parseInt(cart[existing].qty) + parseInt(qty);
            } else {
                cart.push({
                    ingredient_id: ingredientId,
                    ingredient_name: ingredientName,
                    qty: parseInt(qty)
                });
            }

            sessionStorage.setItem('restockCart', JSON.stringify(cart));
            updateRestockCartUI();
            alert(`Đã thêm "${ingredientName}" vào danh sách bổ sung (${cart.length} mục)`);
        }
    }

    // Show restock cart and go to create receipt
    function submitRestockCart() {
        const cart = JSON.parse(sessionStorage.getItem('restockCart') || '[]');
        if (cart.length === 0) {
            alert('Danh sách bổ sung trống');
            return;
        }

        // Redirect to create with cart data
        window.location.href = `<?php echo BASE_URL; ?>/inventory_receipt/create_from_restock`;
    }
</script>
</body>

</html>