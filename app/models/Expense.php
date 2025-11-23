<?php

/**
 * Expense Model
 */

require_once BASE_PATH . '/core/Model.php';

class Expense extends Model
{
    protected $table = 'expense';

    public function getAllWithCreator()
    {
        $sql = "SELECT e.*, u.fullname as creator FROM {$this->table} e LEFT JOIN users u ON e.created_by = u.id ORDER BY e.expense_date DESC, e.id DESC";
        return $this->query($sql);
    }
}
