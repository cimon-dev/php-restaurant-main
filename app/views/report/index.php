<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .main-content {
        margin-left: 260px;
        background: #f8f9fa;
    }

    .report-header {
        background: white;
        padding: 2rem;
        margin-bottom: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border-left: 4px solid;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .stat-card.revenue {
        border-left-color: #10b981;
    }

    .stat-card.expense {
        border-left-color: #ef4444;
    }

    .stat-card.profit {
        border-left-color: #667eea;
    }

    .stat-card.orders {
        border-left-color: #f59e0b;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #212529;
    }

    .stat-icon {
        float: right;
        font-size: 2rem;
        opacity: 0.2;
    }

    .filter-section {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .filter-form {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .filter-group {
        flex: 0 1 auto;
    }

    .filter-group label {
        font-size: 0.9rem;
        font-weight: 500;
        display: block;
        margin-bottom: 0.5rem;
        color: #495057;
    }

    .filter-group input {
        border-radius: 6px;
        border: 1px solid #dee2e6;
    }

    .table-section {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .table {
        margin-bottom: 0;
    }

    .table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .table thead th {
        border: none;
        font-weight: 600;
        padding: 1rem 0.75rem;
    }

    .table tbody td {
        padding: 1rem 0.75rem;
        border-color: #e9ecef;
    }

    .table tbody tr {
        border-bottom: 1px solid #e9ecef;
        transition: background 0.2s;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    .table tfoot {
        background: #f8f9fa;
        border-top: 2px solid #dee2e6;
    }

    .table tfoot td {
        font-weight: 600;
        color: #212529;
    }

    .text-success {
        color: #10b981 !important;
    }

    .text-danger {
        color: #ef4444 !important;
    }

    .text-primary {
        color: #667eea !important;
    }

    .text-warning {
        color: #f59e0b !important;
    }
</style>

<div class="main-content">
    <div class="container-fluid mt-4 mb-5">
        <!-- Page Header -->
        <div class="report-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-0" style="font-weight: 700;">
                        <i class="bi bi-bar-chart"></i> Báo cáo doanh thu
                    </h1>
                    <p class="text-muted small mt-1">Thống kê doanh thu, chi phí và lợi nhuận</p>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="stats-grid">
            <div class="stat-card revenue">
                <i class="bi bi-cash-flow stat-icon"></i>
                <div class="stat-label"><i class="bi bi-arrow-up-circle"></i> Tổng doanh thu</div>
                <div class="stat-value text-success"><?php echo number_format($totals['revenue'], 0, ',', '.'); ?>đ</div>
                <small class="text-muted"><?php echo count($period); ?> ngày</small>
            </div>

            <div class="stat-card expense">
                <i class="bi bi-cash-coin stat-icon"></i>
                <div class="stat-label"><i class="bi bi-arrow-down-circle"></i> Tổng chi phí</div>
                <div class="stat-value text-danger"><?php echo number_format($totals['expense'], 0, ',', '.'); ?>đ</div>
                <small class="text-muted">Khác trừ</small>
            </div>

            <div class="stat-card profit">
                <i class="bi bi-graph-up-arrow stat-icon"></i>
                <div class="stat-label"><i class="bi bi-check-circle"></i> Lợi nhuận</div>
                <div class="stat-value text-primary"><?php echo number_format($totals['net'], 0, ',', '.'); ?>đ</div>
                <small class="text-muted">Doanh thu - Chi phí</small>
            </div>

            <div class="stat-card orders">
                <i class="bi bi-receipt stat-icon"></i>
                <div class="stat-label"><i class="bi bi-basket"></i> Đơn hàng</div>
                <div class="stat-value text-warning"><?php echo number_format($totals['orders'], 0); ?></div>
                <small class="text-muted">Tổng số đơn</small>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="get" class="filter-form">
                <div class="filter-group">
                    <label for="start_date">Từ ngày</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start); ?>">
                </div>
                <div class="filter-group">
                    <label for="end_date">Đến ngày</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($end); ?>">
                </div>
                <div class="filter-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Lọc
                    </button>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="table-section">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Ngày</th>
                            <th style="width: 21%; text-align: right;">Doanh thu</th>
                            <th style="width: 21%; text-align: right;">Chi phí</th>
                            <th style="width: 21%; text-align: right;">Lợi nhuận</th>
                            <th style="width: 22%; text-align: center;">Số đơn</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($period as $d):
                            $row = $map[$d];
                            $net = $row['revenue'] - $row['expense'];
                        ?>
                            <tr>
                                <td>
                                    <strong><?php echo date('d/m/Y', strtotime($d)); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php
                                                                $dayOfWeek = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
                                                                echo $dayOfWeek[date('w', strtotime($d))];
                                                                ?></small>
                                </td>
                                <td style="text-align: right;" class="text-success">
                                    <strong><?php echo number_format($row['revenue'], 0, ',', '.'); ?>đ</strong>
                                </td>
                                <td style="text-align: right;" class="text-danger">
                                    <strong><?php echo number_format($row['expense'], 0, ',', '.'); ?>đ</strong>
                                </td>
                                <td style="text-align: right;" class="text-primary">
                                    <strong><?php echo number_format($net, 0, ',', '.'); ?>đ</strong>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge bg-warning text-dark"><?php echo intval($row['orders']); ?> đơn</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <strong>Tổng cộng</strong>
                            </td>
                            <td style="text-align: right;" class="text-success">
                                <strong><?php echo number_format($totals['revenue'], 0, ',', '.'); ?>đ</strong>
                            </td>
                            <td style="text-align: right;" class="text-danger">
                                <strong><?php echo number_format($totals['expense'], 0, ',', '.'); ?>đ</strong>
                            </td>
                            <td style="text-align: right;" class="text-primary">
                                <strong><?php echo number_format($totals['net'], 0, ',', '.'); ?>đ</strong>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge bg-info"><?php echo intval($totals['orders']); ?> đơn</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>