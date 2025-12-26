<?php

/**
 * Báo cáo kho - Tình trạng tồn kho với cảnh báo và lịch sử xuất kho
 */
include BASE_PATH . '/app/views/layouts/header.php';
include BASE_PATH . '/app/views/layouts/sidebar.php';
?>

<style>
    .main-content {
        margin-left: 260px;
        padding: 2rem;
        background: #f8f9fa;
        min-height: 100vh;
    }

    .stat-card {
        border-radius: 12px;
        padding: 1.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
    }

    .nav-tabs {
        border-bottom: none;
        margin-bottom: 0;
    }

    .nav-tabs .nav-link {
        border-radius: 8px 8px 0 0;
        font-weight: 500;
        color: #6c757d;
        border: none;
        margin-right: 0.5rem;
        padding: 12px 20px;
    }

    .nav-tabs .nav-link:hover {
        background: #f8f9fa;
    }

    .nav-tabs .nav-link.active {
        background: white;
        border-bottom: 3px solid #0d6efd;
        color: #0d6efd;
    }

    .tab-content {
        background: white;
        border-radius: 0 8px 8px 8px;
        padding: 0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        padding: 1rem 0.75rem;
        background: white;
    }

    .table tbody td {
        padding: 0.75rem;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge-status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
</style>

<div class="main-content">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-0" style="font-weight: 700;">
                    <i class="bi bi-graph-up"></i> Báo cáo kho
                </h1>
                <p class="text-muted small mt-1">Tình trạng tồn kho, cảnh báo, và lịch sử quản lý</p>
            </div>
            <div>
                <button type="button" class="btn btn-success" id="restockCartBtn" style="display: none;" onclick="goToCreateReceipt()">
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
                <div class="stat-card" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);">
                    <div class="d-flex align-items-center">
                        <div style="font-size: 2rem; color: #dc2626; margin-right: 1rem;">
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
            <div class="col-md-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
                    <div class="d-flex align-items-center">
                        <div style="font-size: 2rem; color: #d97706; margin-right: 1rem;">
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
            <div class="col-md-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);">
                    <div class="d-flex align-items-center">
                        <div style="font-size: 2rem; color: #16a34a; margin-right: 1rem;">
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

        <!-- Tabs -->
        <ul class="nav nav-tabs" id="stockTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="critical-tab" data-bs-toggle="tab" data-bs-target="#critical" type="button" role="tab">
                    <i class="bi bi-exclamation-circle"></i> Hết hàng <span class="badge bg-danger ms-2"><?php echo $total_critical; ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="warning-tab" data-bs-toggle="tab" data-bs-target="#warning" type="button" role="tab">
                    <i class="bi bi-lightning"></i> Sắp hết <span class="badge bg-warning text-dark ms-2"><?php echo $total_warning; ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="normal-tab" data-bs-toggle="tab" data-bs-target="#normal" type="button" role="tab">
                    <i class="bi bi-check-circle"></i> Bình thường <span class="badge bg-success ms-2"><?php echo $total_normal; ?></span>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="stockTabsContent">
            <!-- Tab Hết hàng -->
            <div class="tab-pane fade show active" id="critical" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #fee2e2;">
                            <tr>
                                <th style="color: #dc2626;">Mã</th>
                                <th style="color: #dc2626;">Tên nguyên liệu</th>
                                <th style="color: #dc2626;">Đơn vị</th>
                                <th style="color: #dc2626;" class="text-center">Số lượng</th>
                                <th style="color: #dc2626;" class="text-center">Mức tối thiểu</th>
                                <th style="color: #dc2626;" class="text-end">Giá nhập</th>
                                <th style="color: #dc2626;" class="text-end">Giá trị kho</th>
                                <th style="color: #dc2626;" class="text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($critical)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="bi bi-inbox" style="font-size: 2rem; color: #6c757d;"></i>
                                        <p class="mt-2 text-muted">Không có nguyên liệu nào hết hàng</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($critical as $ing): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($ing['code']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($ing['name']); ?></td>
                                        <td><?php echo htmlspecialchars($ing['unit'] ?? 'cái'); ?></td>
                                        <td class="text-center">
                                            <span class="badge-status" style="background: #fee2e2; color: #dc2626;">
                                                <?php echo (int)$ing['current_qty']; ?>
                                            </span>
                                        </td>
                                        <td class="text-center"><?php echo (int)($ing['min_stock'] ?? 0); ?></td>
                                        <td class="text-end"><?php echo number_format($ing['purchase_price'] ?? 0, 0, ',', '.'); ?> đ</td>
                                        <td class="text-end"><strong><?php echo number_format($ing['cost'] ?? 0, 0, ',', '.'); ?> đ</strong></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-danger" onclick="addRestock(<?php echo $ing['id']; ?>, '<?php echo htmlspecialchars($ing['name']); ?>')">
                                                <i class="bi bi-exclamation-circle"></i> Hết hàng
                                            </button>
                                            <button class="btn btn-sm btn-outline-primary mt-1" onclick="addRestock(<?php echo $ing['id']; ?>, '<?php echo htmlspecialchars($ing['name']); ?>')">
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

            <!-- Tab Sắp hết -->
            <div class="tab-pane fade" id="warning" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #fef3c7;">
                            <tr>
                                <th style="color: #d97706;">Mã</th>
                                <th style="color: #d97706;">Tên nguyên liệu</th>
                                <th style="color: #d97706;">Đơn vị</th>
                                <th style="color: #d97706;" class="text-center">Số lượng</th>
                                <th style="color: #d97706;" class="text-center">Mức tối thiểu</th>
                                <th style="color: #d97706;" class="text-end">Giá nhập</th>
                                <th style="color: #d97706;" class="text-end">Giá trị kho</th>
                                <th style="color: #d97706;" class="text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($warning)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="bi bi-inbox" style="font-size: 2rem; color: #6c757d;"></i>
                                        <p class="mt-2 text-muted">Không có nguyên liệu nào sắp hết</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($warning as $ing): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($ing['code']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($ing['name']); ?></td>
                                        <td><?php echo htmlspecialchars($ing['unit'] ?? 'cái'); ?></td>
                                        <td class="text-center">
                                            <span class="badge-status" style="background: #fef3c7; color: #d97706;">
                                                <?php echo (int)$ing['current_qty']; ?>
                                            </span>
                                        </td>
                                        <td class="text-center"><?php echo (int)($ing['min_stock'] ?? 0); ?></td>
                                        <td class="text-end"><?php echo number_format($ing['purchase_price'] ?? 0, 0, ',', '.'); ?> đ</td>
                                        <td class="text-end"><strong><?php echo number_format($ing['cost'] ?? 0, 0, ',', '.'); ?> đ</strong></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-warning" onclick="addRestock(<?php echo $ing['id']; ?>, '<?php echo htmlspecialchars($ing['name']); ?>')">
                                                <i class="bi bi-lightning"></i> Sắp hết
                                            </button>
                                            <button class="btn btn-sm btn-outline-primary mt-1" onclick="addRestock(<?php echo $ing['id']; ?>, '<?php echo htmlspecialchars($ing['name']); ?>')">
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

            <!-- Tab Bình thường -->
            <div class="tab-pane fade" id="normal" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #dcfce7;">
                            <tr>
                                <th style="color: #16a34a;">Mã</th>
                                <th style="color: #16a34a;">Tên nguyên liệu</th>
                                <th style="color: #16a34a;">Đơn vị</th>
                                <th style="color: #16a34a;" class="text-center">Số lượng</th>
                                <th style="color: #16a34a;" class="text-center">Mức tối thiểu</th>
                                <th style="color: #16a34a;" class="text-end">Giá nhập</th>
                                <th style="color: #16a34a;" class="text-end">Giá trị kho</th>
                                <th style="color: #16a34a;" class="text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($normal)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="bi bi-inbox" style="font-size: 2rem; color: #6c757d;"></i>
                                        <p class="mt-2 text-muted">Không có nguyên liệu nào</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($normal as $ing): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($ing['code']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($ing['name']); ?></td>
                                        <td><?php echo htmlspecialchars($ing['unit'] ?? 'cái'); ?></td>
                                        <td class="text-center">
                                            <span class="badge-status" style="background: #dcfce7; color: #16a34a;">
                                                <?php echo (int)$ing['current_qty']; ?>
                                            </span>
                                        </td>
                                        <td class="text-center"><?php echo (int)($ing['min_stock'] ?? 0); ?></td>
                                        <td class="text-end"><?php echo number_format($ing['purchase_price'] ?? 0, 0, ',', '.'); ?> đ</td>
                                        <td class="text-end"><strong><?php echo number_format($ing['cost'] ?? 0, 0, ',', '.'); ?> đ</strong></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-success" disabled>
                                                <i class="bi bi-check-circle"></i> Đủ hàng
                                            </button>
                                            <button class="btn btn-sm btn-outline-primary mt-1" onclick="addRestock(<?php echo $ing['id']; ?>, '<?php echo htmlspecialchars($ing['name']); ?>')">
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
        </div>

        <!-- Lịch sử xuất kho -->
        <?php if (!empty($recent_issues)): ?>
            <div class="mt-5">
                <h5 class="mb-3" style="font-weight: 600;">
                    <i class="bi bi-clock-history"></i> Lịch sử xuất kho (10 ngày gần đây)
                </h5>
                <div class="row">
                    <?php foreach ($recent_issues as $issue): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?php echo date('d/m/Y', strtotime($issue['issue_date'])); ?></h6>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($issue['issue_type']); ?></span>
                                        </div>
                                        <small class="text-muted"><?php echo htmlspecialchars($issue['created_by'] ?? 'N/A'); ?></small>
                                    </div>
                                    <p class="mt-2 mb-1"><small><?php echo htmlspecialchars($issue['details'] ?? ''); ?></small></p>
                                    <?php if (!empty($issue['note'])): ?>
                                        <p class="mb-0"><small class="text-muted">Ghi chú: <?php echo htmlspecialchars($issue['note']); ?></small></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (isset($pagination)): ?>
                    <?php
                    $paginationVar = $pagination;
                    $baseUrlVar = $baseUrl ?? (BASE_URL . '/report/stock_report');
                    $pagination = $paginationVar;
                    $baseUrl = $baseUrlVar;
                    include BASE_PATH . '/app/views/layouts/pagination.php';
                    ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include BASE_PATH . '/app/views/layouts/footer.php'; ?>

<script>
    // Load cart on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateCartUI();
    });

    function updateCartUI() {
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

    function addRestock(ingredientId, ingredientName) {
        const qty = prompt(`Nhập số lượng muốn bổ sung cho "${ingredientName}":`, '');
        if (qty && !isNaN(qty) && parseFloat(qty) > 0) {
            // Get current cart
            let cart = JSON.parse(sessionStorage.getItem('restockCart') || '[]');

            // Check if item already exists
            const existingIndex = cart.findIndex(item => item.ingredient_id == ingredientId);
            if (existingIndex >= 0) {
                // Update quantity
                cart[existingIndex].qty = parseFloat(cart[existingIndex].qty) + parseFloat(qty);
                alert(`Đã cập nhật số lượng cho "${ingredientName}". Tổng: ${cart[existingIndex].qty}`);
            } else {
                // Add new item
                cart.push({
                    ingredient_id: ingredientId,
                    ingredient_name: ingredientName,
                    qty: parseFloat(qty)
                });
                alert(`Đã thêm "${ingredientName}" với số lượng ${qty} vào danh sách bổ sung`);
            }

            // Save to sessionStorage
            sessionStorage.setItem('restockCart', JSON.stringify(cart));

            // Update UI
            updateCartUI();
        }
    }

    function goToCreateReceipt() {
        const cart = JSON.parse(sessionStorage.getItem('restockCart') || '[]');
        if (cart.length === 0) {
            alert('Danh sách bổ sung trống');
            return;
        }

        // Confirm before redirect
        if (confirm(`Bạn muốn tạo phiếu nhập kho với ${cart.length} nguyên liệu đã chọn?`)) {
            // Redirect to create from restock
            window.location.href = `<?php echo BASE_URL; ?>/inventory_receipt/create_from_restock`;
        }
    }
</script>