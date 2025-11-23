<?php

/**
 * InventoryReceipt Model
 */

require_once BASE_PATH . '/core/Model.php';

class InventoryReceipt extends Model
{
    protected $table = 'inventory_receipt';

    public function getAllWithCreator()
    {
        $sql = "SELECT r.*, u.fullname as creator FROM {$this->table} r LEFT JOIN users u ON r.created_by = u.id ORDER BY r.receipt_date DESC, r.created_at DESC";
        return $this->query($sql);
    }

    public function getDetails($receiptId)
    {
        $sql = "SELECT d.*, i.name as ingredient_name FROM inventory_receipt_detail d LEFT JOIN ingredient i ON d.ingredient_id = i.id WHERE d.receipt_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$receiptId]);
        return $stmt->fetchAll();
    }
}
