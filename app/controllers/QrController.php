<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/helpers/JWT.php';

class QrController extends Controller
{
    private $tableModel;

    public function __construct()
    {
        $this->tableModel = $this->model('RestaurantTable');
    }

    public function index()
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }

        $db = getDB();
        $stmt = $db->query("SELECT * FROM restaurant_table ORDER BY number ASC");
        $tables = $stmt->fetchAll();

        $this->view('qr/index', [
            'tables' => $tables,
            'baseUrl' => BASE_URL . '/qr',
            'user' => $user
        ]);
    }

    public function generate($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }
        if (!$id) {
            $this->redirect('qr');
            return;
        }

        $token = bin2hex(random_bytes(16));
        $this->tableModel->update($id, ['order_token' => $token]);
        setFlash('success', 'Đã tạo mã QR cho bàn #' . $id);
        $this->redirect('qr');
    }

    public function clear($id = null)
    {
        $user = JWT::getCurrentUser();
        if (!$user) {
            $this->redirect('auth/login');
            return;
        }
        if (!$id) {
            $this->redirect('qr');
            return;
        }

        $this->tableModel->update($id, ['order_token' => null]);
        setFlash('success', 'Đã xóa mã QR cho bàn #' . $id);
        $this->redirect('qr');
    }

    /**
     * Download QR PNG for a table by proxying the external QR image
     */
    public function download($id = null)
    {
        if (!$id) {
            $this->redirect('qr');
            return;
        }

        $table = $this->tableModel->find($id);
        if (!$table) {
            setFlash('error', 'Bàn không tồn tại');
            $this->redirect('qr');
            return;
        }
        if (empty($table['order_token'])) {
            setFlash('error', 'Bàn chưa có mã QR. Hãy bấm "Sinh QR" trước.');
            $this->redirect('qr');
            return;
        }

        $link = BASE_URL . '/public_order/start?token=' . urlencode($table['order_token']);
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=600x600&data=' . urlencode($link);

        // Fetch PNG
        $png = false;
        if (ini_get('allow_url_fopen')) {
            $png = @file_get_contents($qrUrl);
        }
        if ($png === false) {
            // Fallback to cURL
            if (function_exists('curl_init')) {
                $ch = curl_init($qrUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                $png = curl_exec($ch);
                curl_close($ch);
            }
        }

        if ($png === false) {
            setFlash('error', 'Không tải được QR từ máy chủ.');
            $this->redirect('qr');
            return;
        }

        $filename = 'qr-ban-' . preg_replace('/[^A-Za-z0-9_-]/', '', $table['number'] ?? (string)$table['id']) . '.png';
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($png));
        echo $png;
        exit;
    }
}
