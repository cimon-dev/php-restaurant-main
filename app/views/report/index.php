<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <?php include_once __DIR__ . '/../layouts/navbar.php'; ?>

    <div class="content-area">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Báo cáo doanh thu</h3>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="get" class="row g-2">
                    <div class="col-auto">
                        <label class="form-label">Từ</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start); ?>">
                    </div>
                    <div class="col-auto">
                        <label class="form-label">Đến</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($end); ?>">
                    </div>
                    <div class="col-auto align-self-end">
                        <button class="btn btn-primary">Lọc</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Ngày</th>
                            <th>Doanh thu</th>
                            <th>Chi phí</th>
                            <th>Lợi nhuận</th>
                            <th>Đơn</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($period as $d):
                            $row = $map[$d];
                            $net = $row['revenue'] - $row['expense'];
                        ?>
                            <tr>
                                <td><?php echo $d; ?></td>
                                <td><?php echo number_format($row['revenue'], 2); ?></td>
                                <td><?php echo number_format($row['expense'], 2); ?></td>
                                <td><?php echo number_format($net, 2); ?></td>
                                <td><?php echo intval($row['orders']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td>Tổng</td>
                            <td><?php echo number_format($totals['revenue'], 2); ?></td>
                            <td><?php echo number_format($totals['expense'], 2); ?></td>
                            <td><?php echo number_format($totals['net'], 2); ?></td>
                            <td><?php echo intval($totals['orders']); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>