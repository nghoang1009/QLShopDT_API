<?php
/**
 * Database - Lớp kết nối cơ sở dữ liệu thống nhất
 */

require_once dirname(__DIR__) . '/config/config.php';

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->conn->connect_error) {
            die("Kết nối thất bại: " . $this->conn->connect_error);
        }
        
        $this->conn->set_charset("utf8mb4");
    }

    /**
     * Singleton pattern - Lấy instance duy nhất
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Lấy connection
     */
    public function getConnection() {
        return $this->conn;
    }

    /**
     * Prepared statement helper
     * @param string $sql - Câu SQL với placeholder ?
     * @param string $types - Loại tham số 
     * @param array $params - Mảng các tham số
     * @return mysqli_stmt|false
     */
    public function prepare($sql, $types = '', $params = []) {
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
            return false;
        }
        
        if (!empty($types) && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        return $stmt;
    }

    /**
     * Thực thi SELECT và trả về kết quả
     * @return array|false
     */
    public function select($sql, $types = '', $params = []) {
        $stmt = $this->prepare($sql, $types, $params);
        
        if ($stmt === false) {
            return false;
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        return $data;
    }

    /**
     * Thực thi INSERT và trả về ID mới
     * @return int|false
     */
    public function insert($sql, $types = '', $params = []) {
        $stmt = $this->prepare($sql, $types, $params);
        
        if ($stmt === false) {
            return false;
        }
        
        $result = $stmt->execute();
        $insertId = $this->conn->insert_id;
        $stmt->close();
        
        return $result ? $insertId : false;
    }

    /**
     * Thực thi UPDATE/DELETE và trả về số dòng bị ảnh hưởng
     * @return int|false
     */
    public function execute($sql, $types = '', $params = []) {
        $stmt = $this->prepare($sql, $types, $params);
        
        if ($stmt === false) {
            return false;
        }
        
        $result = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        
        return $result ? $affected : false;
    }

    /**
     * Lấy ID cuối cùng được insert
     */
    public function lastInsertId() {
        return $this->conn->insert_id;
    }

    /**
     * Lấy thông báo lỗi
     */
    public function error() {
        return $this->conn->error;
    }

    /**
     * Escape string
     */
    public function escape($value) {
        return $this->conn->real_escape_string($value);
    }

    /**
     * Đóng kết nối
     */
    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Biến global cho tương thích ngược (sẽ bị loại bỏ dần)
$conn = Database::getInstance()->getConnection();
