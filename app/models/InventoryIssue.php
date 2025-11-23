<?php

/**
 * InventoryIssue Model
 */

require_once BASE_PATH . '/core/Model.php';

class InventoryIssue extends Model
{
    protected $table = 'inventory_issue';

    public function getAllWithCreator()
    {
        $sql = "SELECT i.*, u.fullname as creator FROM {$this->table} i LEFT JOIN users u ON i.created_by = u.id ORDER BY i.issue_date DESC, i.created_at DESC";
        return $this->query($sql);
    }

    public function getDetails($issueId)
    {
        $sql = "SELECT d.*, ing.name as ingredient_name FROM inventory_issue_detail d LEFT JOIN ingredient ing ON d.ingredient_id = ing.id WHERE d.issue_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$issueId]);
        return $stmt->fetchAll();
    }
}
