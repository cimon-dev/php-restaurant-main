<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content" style="margin-left:260px;padding:2rem;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0"><i class="bi bi-qr-code"></i> Quản lý QR Code đặt món</h2>
        <a class="btn btn-outline-secondary" href="<?php echo BASE_URL; ?>/restaurant_table">
            <i class="bi bi-table"></i> Quản lý bàn
        </a>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Bàn</th>
                        <th>Trạng thái</th>
                        <th>Token</th>
                        <th>Link</th>
                        <th>QR</th>
                        <th width="220">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tables as $t):
                        $token = $t['order_token'] ?? null;
                        $link = $token ? (BASE_URL . '/public_order/start?token=' . urlencode($token)) : '';
                    ?>
                        <tr>
                            <td><strong>Bàn <?php echo htmlspecialchars($t['number']); ?></strong></td>
                            <?php
                            $st = $t['status'] ?? 'free';
                            // Map to Bootstrap contextual colors
                            // free -> success (green), occupied -> warning (orange), reserved -> info (blue)
                            $stClass = $st === 'free' ? 'success' : ($st === 'occupied' ? 'warning' : 'info');
                            $stName = $st === 'free' ? 'Trống' : ($st === 'occupied' ? 'Đang phục vụ' : 'Đã đặt trước');
                            ?>
                            <td><span class="badge bg-<?php echo $stClass; ?>"><?php echo $stName; ?></span></td>
                            <td style="font-family:monospace;">
                                <?php echo $token ? substr($token, 0, 8) . '…' : '<span class="text-muted">(chưa có)</span>'; ?>
                            </td>
                            <td>
                                <?php if (!empty($link)): ?>
                                    <a target="_blank" href="<?php echo $link; ?>">Mở link</a>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($link)): ?>
                                    <img alt="qr" style="width:80px;height:80px" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($link); ?>" />
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (empty($token)): ?>
                                    <a class="btn btn-sm btn-primary" href="<?php echo BASE_URL; ?>/qr/generate/<?php echo $t['id']; ?>"><i class="bi bi-magic"></i> Sinh QR</a>
                                <?php else: ?>
                                    <a class="btn btn-sm btn-outline-primary" href="<?php echo BASE_URL; ?>/qr/generate/<?php echo $t['id']; ?>"><i class="bi bi-arrow-clockwise"></i> Tạo lại</a>
                                    <a class="btn btn-sm btn-outline-success" href="<?php echo BASE_URL; ?>/qr/download/<?php echo $t['id']; ?>"><i class="bi bi-download"></i> Tải QR</a>
                                    <a class="btn btn-sm btn-outline-danger" href="<?php echo BASE_URL; ?>/qr/clear/<?php echo $t['id']; ?>" onclick="return confirm('Xóa QR của bàn này?');"><i class="bi bi-trash"></i> Xóa</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>