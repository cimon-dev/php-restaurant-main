<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Đặt món thành công</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <div class="display-6 mb-3">🎉 Đặt món thành công!</div>
                <p class="lead">Mã đơn của bạn: <strong>#<?php echo (int)$orderId; ?></strong></p>
                <p>Bàn: <strong><?php echo htmlspecialchars($table['number']); ?></strong>. Nhân viên sẽ tiếp nhận và phục vụ sớm.</p>
                <a class="btn btn-primary mt-3" href="<?php echo BASE_URL; ?>/public_order/start?token=<?php echo urlencode($table['order_token']); ?>">Đặt thêm món</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>