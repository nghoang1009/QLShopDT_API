<?php
/**
 * Model Base - Lớp cơ sở cho tất cả Model
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
require_once BASE_PATH . '/config/database.php';

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Lấy tất cả bản ghi
     */
    public function getAll($orderBy = null) {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        return $this->db->select($sql);
    }
    
    /**
     * Tìm theo ID
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $result = $this->db->select($sql, 'i', [$id]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Tìm theo điều kiện
     * @param array $conditions - ['column' => value]
     */
    public function findWhere($conditions, $orderBy = null) {
        $where = [];
        $types = '';
        $params = [];
        
        foreach ($conditions as $column => $value) {
            $where[] = "$column = ?";
            $types .= is_int($value) ? 'i' : 's';
            $params[] = $value;
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where);
        
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        return $this->db->select($sql, $types, $params);
    }
    
    /**
     * Đếm số bản ghi
     */
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        
        $types = '';
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $column => $value) {
                $where[] = "$column = ?";
                $types .= is_int($value) ? 'i' : 's';
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $result = $this->db->select($sql, $types, $params);
        return $result ? (int)$result[0]['total'] : 0;
    }
    
    /**
     * Thêm bản ghi mới
     * @param array $data - ['column' => value]
     * @return int|false - ID mới hoặc false
     */
    public function create($data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($data), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $types = '';
        $params = array_values($data);
        foreach ($params as $value) {
            $types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
        }
        
        return $this->db->insert($sql, $types, $params);
    }
    
    /**
     * Cập nhật bản ghi
     * @param int $id
     * @param array $data
     * @return int|false - Số dòng bị ảnh hưởng
     */
    public function update($id, $data) {
        $set = [];
        $types = '';
        $params = [];
        
        foreach ($data as $column => $value) {
            $set[] = "$column = ?";
            $types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
            $params[] = $value;
        }
        
        // Thêm ID vào cuối
        $types .= 'i';
        $params[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE {$this->primaryKey} = ?";
        
        return $this->db->execute($sql, $types, $params);
    }
    
    /**
     * Xóa bản ghi
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->execute($sql, 'i', [$id]);
    }
    
    /**
     * Custom query
     */
    public function query($sql, $types = '', $params = []) {
        return $this->db->select($sql, $types, $params);
    }
    
    /**
     * Execute custom query (INSERT/UPDATE/DELETE)
     */
    public function exec($sql, $types = '', $params = []) {
        return $this->db->execute($sql, $types, $params);
    }
}
