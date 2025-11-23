<?php

/**
 * SaleOrder Model
 */

require_once BASE_PATH . '/core/Model.php';

class SaleOrder extends Model
{
    protected $table = 'sale_order';

    public function getAllWithTable()
    {
        $sql = "SELECT o.*, t.number AS table_number FROM {$this->table} o LEFT JOIN restaurant_table t ON o.table_id = t.id ORDER BY o.order_time DESC";
        return $this->query($sql);
    }

    public function getDetails($orderId)
    {
        $sql = "SELECT d.*, m.name as menu_name FROM sale_order_detail d JOIN menu_item m ON m.id = d.menu_id WHERE d.sale_order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
}
