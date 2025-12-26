<?php
// $pagination: ['page','per_page','total','pages']
// $baseUrl: string without page/per_page params included
// Keeps other existing query params
if (!isset($pagination)) return;

$page = (int)($pagination['page'] ?? 1);
$per = (int)($pagination['per_page'] ?? 10);
$total = (int)($pagination['total'] ?? 0);
$pages = (int)($pagination['pages'] ?? 1);

// Build URL helper
function build_page_url($baseUrl, $page, $per)
{
    $sep = strpos($baseUrl, '?') === false ? '?' : '&';
    return $baseUrl . $sep . 'page=' . $page . '&per_page=' . $per;
}
?>
<div class="d-flex justify-content-between align-items-center p-3" style="background:#fff;border-top:1px solid #e9ecef;">
    <div class="d-flex align-items-center" style="gap:8px;">
        <span class="text-muted">Hiển thị mỗi trang:</span>
        <form method="get" class="d-flex align-items-center" style="gap:8px;">
            <?php
            // Preserve existing params except page/per_page
            $params = $_GET ?? [];
            unset($params['page'], $params['per_page']);
            foreach ($params as $k => $v) {
                echo '<input type="hidden" name="' . htmlspecialchars($k) . '" value="' . htmlspecialchars($v) . '">';
            }
            ?>
            <select name="per_page" class="form-select" style="width:auto;">
                <?php foreach ([10, 20, 30, 50, 100] as $opt): ?>
                    <option value="<?php echo $opt; ?>" <?php echo $opt == $per ? 'selected' : ''; ?>><?php echo $opt; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="page" value="1">
            <button class="btn btn-outline-secondary btn-sm" type="submit">
                <i class="bi bi-check2"></i> Áp dụng
            </button>
        </form>
    </div>
    <div class="d-flex align-items-center" style="gap:8px;">
        <a class="btn btn-outline-secondary btn-sm <?php echo $page <= 1 ? 'disabled' : ''; ?>" href="<?php echo $page > 1 ? build_page_url($baseUrl, $page - 1, $per) : '#'; ?>">
            <i class="bi bi-chevron-left"></i> Trước
        </a>
        <span class="text-muted">Trang <?php echo $page; ?> / <?php echo max(1, $pages); ?> (<?php echo number_format($total, 0, ',', '.'); ?> mục)</span>
        <a class="btn btn-outline-secondary btn-sm <?php echo $page >= $pages ? 'disabled' : ''; ?>" href="<?php echo $page < $pages ? build_page_url($baseUrl, $page + 1, $per) : '#'; ?>">
            Sau <i class="bi bi-chevron-right"></i>
        </a>
    </div>
</div>