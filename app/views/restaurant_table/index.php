<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .table-status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
    }

    .status-free {
        background-color: #e8f5e9;
        color: #388e3c;
    }

    .status-occupied {
        background-color: #fff3e0;
        color: #f57c00;
    }

    .status-reserved {
        background-color: #e3f2fd;
        color: #1976d2;
    }
</style>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <?php $flash = getFlash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3><i class="bi bi-table me-2"></i>Quản lý bàn ăn</h3>
            <a href="<?php echo BASE_URL; ?>/restaurant_table/create" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Tạo bàn mới
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
                            <th style="width: 15%;">Mã</th>
                            <th style="width: 20%;">Số bàn</th>
                            <th style="width: 30%;">Trạng thái</th>
                            <th style="width: 35%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $it): ?>
                                <?php
                                $statusMap = [
                                    'free' => ['name' => 'Trống', 'class' => 'status-free', 'icon' => 'check-circle'],
                                    'occupied' => ['name' => 'Đang phục vụ', 'class' => 'status-occupied', 'icon' => 'person-fill'],
                                    'reserved' => ['name' => 'Đã đặt trước', 'class' => 'status-reserved', 'icon' => 'calendar-check']
                                ];
                                $status = $statusMap[$it['status']] ?? ['name' => htmlspecialchars($it['status']), 'class' => 'status-free', 'icon' => 'question-circle'];
                                ?>
                                <tr>
                                    <td><strong>#<?php echo $it['id']; ?></strong></td>
                                    <td>
                                        <h6 class="mb-0"><i class="bi bi-table me-2"></i><?php echo htmlspecialchars($it['number']); ?></h6>
                                    </td>
                                    <td>
                                        <span class="table-status-badge <?php echo $status['class']; ?>">
                                            <i class="bi bi-<?php echo $status['icon']; ?> me-1"></i><?php echo $status['name']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>/restaurant_table/edit/<?php echo $it['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil me-1"></i>Sửa
                                        </a>
                                        <?php if ($it['status'] === 'free'): ?>
                                            <a href="<?php echo BASE_URL; ?>/restaurant_table/delete/<?php echo $it['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa bàn này?')">
                                                <i class="bi bi-trash me-1"></i>Xóa
                                            </a>
                                        <?php else: ?>
                                            <span class="btn btn-sm btn-outline-danger disabled">
                                                <i class="bi bi-lock me-1"></i>Không thể xóa
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mt-2">Không tìm thấy bàn ăn nào</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Trạng thái hướng dẫn -->
        <div class="mt-4 p-3 bg-light rounded">
            <h6 class="mb-3"><i class="bi bi-info-circle me-2"></i>Hướng dẫn trạng thái bàn</h6>
            <div class="row">
                <div class="col-md-4">
                    <small>
                        <span class="table-status-badge status-free">
                            <i class="bi bi-check-circle"></i> Trống
                        </span> - Bàn sẵn sàng phục vụ
                    </small>
                </div>
                <div class="col-md-4">
                    <small>
                        <span class="table-status-badge status-occupied">
                            <i class="bi bi-person-fill"></i> Đang phục vụ
                        </span> - Bàn đang có khách
                    </small>
                </div>
                <div class="col-md-4">
                    <small>
                        <span class="table-status-badge status-reserved">
                            <i class="bi bi-calendar-check"></i> Đã đặt trước
                        </span> - Bàn đã được đặt
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>