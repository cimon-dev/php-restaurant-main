<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
        display: inline-block;
    }

    .status-open {
        background-color: #e3f2fd;
        color: #1976d2;
    }

    .status-serving {
        background-color: #fff3e0;
        color: #f57c00;
    }

    .status-completed {
        background-color: #e8f5e9;
        color: #388e3c;
    }

    .status-paid {
        background-color: #f3e5f5;
        color: #7b1fa2;
    }

    .status-cancel {
        background-color: #ffebee;
        color: #c62828;
    }

    .action-buttons {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .btn-action-sm {
        padding: 5px 10px;
        font-size: 12px;
        border-radius: 4px;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }

    .btn-action-sm:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3><i class="bi bi-receipt me-2"></i>Danh sách đơn hàng</h3>
            <a href="<?php echo BASE_URL; ?>/sale_order/create" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Tạo đơn mới
            </a>
        </div>

        <?php if ($flash): ?>
            <div class="alert <?php echo $flash['type'] === 'success' ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                <i class="bi bi-info-circle me-2"></i><?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%;">Mã đơn</th>
                            <th style="width: 12%;">Bàn</th>
                            <th style="width: 18%;">Thời gian</th>
                            <th style="width: 15%;">Trạng thái</th>
                            <th style="width: 15%;">Tổng tiền</th>
                            <th style="width: 30%;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $it): ?>
                                <?php
                                // Map status to Vietnamese display name
                                $statusMap = [
                                    'open' => ['name' => 'Đang phục vụ', 'class' => 'status-open'],
                                    'served' => ['name' => 'Hoàn thành', 'class' => 'status-completed'],
                                    'paid' => ['name' => 'Đã thanh toán', 'class' => 'status-paid'],
                                    'cancel' => ['name' => 'Đã hủy', 'class' => 'status-cancel']
                                ];
                                $status = $statusMap[$it['status']] ?? ['name' => htmlspecialchars($it['status']), 'class' => 'status-open'];
                                ?>
                                <tr>
                                    <td><strong>#<?php echo $it['id']; ?></strong></td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="bi bi-table me-1"></i><?php echo htmlspecialchars($it['table_number'] ?? '-'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($it['order_time'])); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $status['class']; ?>">
                                            <?php echo $status['name']; ?>
                                        </span>
                                    </td>
                                    <td><strong><?php echo number_format($it['total_amount'], 0, ',', '.'); ?> đ</strong></td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if ($it['status'] !== 'paid' && $it['status'] !== 'cancel'): ?>
                                                <!-- Xem chi tiết -->
                                                <a href="<?php echo BASE_URL; ?>/sale_order/edit/<?php echo $it['id']; ?>"
                                                    class="btn-action-sm btn-outline-primary" title="Xem chi tiết">
                                                    <i class="bi bi-eye"></i> Xem
                                                </a>

                                                <!-- Thêm món ăn -->
                                                <?php if ($it['status'] === 'open'): ?>
                                                    <a href="<?php echo BASE_URL; ?>/sale_order/addItem/<?php echo $it['id']; ?>"
                                                        class="btn-action-sm btn-outline-info" title="Thêm món ăn">
                                                        <i class="bi bi-plus"></i> Thêm
                                                    </a>
                                                <?php endif; ?>

                                                <!-- Thanh toán -->
                                                <?php if ($it['status'] === 'served'): ?>
                                                    <a href="<?php echo BASE_URL; ?>/sale_order/pay/<?php echo $it['id']; ?>"
                                                        class="btn-action-sm btn-outline-success"
                                                        onclick="return confirm('Xác nhận thanh toán đơn hàng #<?php echo $it['id']; ?>?')">
                                                        <i class="bi bi-check-circle"></i> Thanh toán
                                                    </a>
                                                <?php endif; ?>

                                                <!-- Hoàn thành phục vụ -->
                                                <?php if ($it['status'] === 'open'): ?>
                                                    <a href="<?php echo BASE_URL; ?>/sale_order/complete/<?php echo $it['id']; ?>"
                                                        class="btn-action-sm btn-outline-success"
                                                        onclick="return confirm('Đánh dấu đơn #<?php echo $it['id']; ?> là hoàn thành?')">
                                                        <i class="bi bi-check2-all"></i> Hoàn thành
                                                    </a>
                                                <?php endif; ?>

                                                <!-- Hủy đơn -->
                                                <a href="<?php echo BASE_URL; ?>/sale_order/cancel/<?php echo $it['id']; ?>"
                                                    class="btn-action-sm btn-outline-danger"
                                                    onclick="return confirm('Xác nhận hủy đơn hàng #<?php echo $it['id']; ?>?')">
                                                    <i class="bi bi-x-circle"></i> Hủy
                                                </a>
                                            <?php else: ?>
                                                <!-- Xem chi tiết (read-only) -->
                                                <a href="<?php echo BASE_URL; ?>/sale_order/edit/<?php echo $it['id']; ?>"
                                                    class="btn-action-sm btn-outline-secondary" title="Xem chi tiết">
                                                    <i class="bi bi-eye"></i> Xem
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mt-2">Không có đơn hàng nào</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Hướng dẫn trạng thái -->
        <div class="mt-4 p-3 bg-light rounded">
            <h6 class="mb-3"><i class="bi bi-info-circle me-2"></i>Hướng dẫn trạng thái đơn hàng</h6>
            <div class="row">
                <div class="col-md-6">
                    <small>
                        <span class="status-badge status-open">Đang phục vụ</span> - Đơn mới, đang chuẩn bị<br>
                        <span class="status-badge status-completed">Hoàn thành</span> - Đã phục vụ, chờ thanh toán<br>
                    </small>
                </div>
                <div class="col-md-6">
                    <small>
                        <span class="status-badge status-paid">Đã thanh toán</span> - Thanh toán xong<br>
                        <span class="status-badge status-cancel">Đã hủy</span> - Đơn bị hủy
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>