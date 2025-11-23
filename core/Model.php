<?php
/**
 * Base Model
 */

class Model {
    
    protected $db;
    protected $table;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Get all records
     */
    public function all($orderBy = 'id', $order = 'ASC') {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY {$orderBy} {$order}");
        return $stmt->fetchAll();
    }
    
    /**
     * Find by ID
     */
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Find by field
     */
    public function findBy($field, $value) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$field} = ?");
        $stmt->execute([$value]);
        return $stmt->fetch();
    }
    
    /**
     * Get where
     */
    public function where($conditions = [], $orderBy = 'id', $order = 'ASC') {
        $sql = "SELECT * FROM {$this->table}";
        
        if (!empty($conditions)) {
            $sql .= " WHERE ";
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "$field = :$field";
            }
            $sql .= implode(' AND ', $where);
        }
        
        $sql .= " ORDER BY {$orderBy} {$order}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($conditions);
        return $stmt->fetchAll();
    }
    
    /**
     * Insert
     */
    public function insert($data) {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") 
                VALUES (" . implode(',', $placeholders) . ")";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Update
     */
    public function update($id, $data) {
        $fields = [];
        foreach (array_keys($data) as $field) {
            $fields[] = "$field = ?";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(',', $fields) . " WHERE id = ?";
        
        $values = array_values($data);
        $values[] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Delete
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Count
     */
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        
        if (!empty($conditions)) {
            $sql .= " WHERE ";
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "$field = :$field";
            }
            $sql .= implode(' AND ', $where);
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($conditions);
        $result = $stmt->fetch();
        
        return $result['total'];
    }
    
    /**
     * Query custom SQL
     */
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
