<?php

/**
 * Reservation Model
 */

require_once BASE_PATH . '/core/Model.php';

class Reservation extends Model
{
    protected $table = 'reservation';

    /**
     * Find overlapping reservations for a table (non-cancelled)
     */
    public function findOverlapping($table_id, $start_time, $end_time, $excludeId = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE table_id = ? AND status != 'cancelled' AND ( (start_time <= ? AND end_time > ?) OR (start_time < ? AND end_time >= ?) OR (start_time >= ? AND start_time < ?) )";

        $params = [$table_id, $start_time, $start_time, $end_time, $end_time, $start_time, $end_time];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
