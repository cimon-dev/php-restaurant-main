<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Đặt món - Bàn <?php echo htmlspecialchars($table['number']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Bàn <?php echo htmlspecialchars($table['number']); ?></h3>
            <a class="btn btn-outline-secondary" href="<?php echo BASE_URL; ?>/public_order/start?token=<?php echo urlencode($token); ?>">Làm mới</a>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="post" action="<?php echo $orderUrl; ?>">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>" />

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Tên khách</label>
                            <input class="form-control" name="customer_name" placeholder="Tên của bạn (không bắt buộc)" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại</label>
                            <input class="form-control" name="customer_phone" placeholder="Liên hệ (không bắt buộc)" />
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Món</th>
                                    <th class="text-end">Giá</th>
                                    <th width="140" class="text-center">Số lượng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($menus as $m): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($m['name']); ?></strong><br><small class="text-muted"><?php echo htmlspecialchars($m['code']); ?></small></td>
                                        <td class="text-end"><?php echo number_format($m['price'], 0, ',', '.'); ?> đ</td>
                                        <td class="text-center">
                                            <input type="number" min="0" class="form-control" style="width:120px;margin:0 auto" name="items[<?php echo $m['id']; ?>]" value="0" />
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-bag-check"></i> Gửi đơn</button>
                    </div>
                </form>
            </div>
        </div>
        <p class="text-center text-muted">Cảm ơn bạn đã đặt món!</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>