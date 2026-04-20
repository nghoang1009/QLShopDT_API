<?php
/**
 * SanPham Model - Quản lý sản phẩm
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
require_once BASE_PATH . '/core/Model.php';

class SanPham extends Model {
    protected $table = 'sanpham';
    protected $primaryKey = 'masp';
    
    /**
     * Lấy tất cả sản phẩm với tên danh mục
     */
    public function getAllWithCategory($orderBy = 'masp ASC') {
        $sql = "SELECT sp.*, dm.tendm 
                FROM sanpham sp 
                LEFT JOIN danhmuc dm ON sp.madm = dm.madm 
                ORDER BY $orderBy";
        return $this->db->select($sql);
    }
    
    /**
     * Tìm sản phẩm theo ID với tên danh mục
     */
    public function findWithCategory($masp) {
        $sql = "SELECT sp.*, dm.tendm 
                FROM sanpham sp 
                LEFT JOIN danhmuc dm ON sp.madm = dm.madm 
                WHERE sp.masp = ?";
        $result = $this->db->select($sql, 'i', [$masp]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Lấy sản phẩm theo danh mục
     */
    public function getByCategory($madm) {
        $sql = "SELECT sp.*, dm.tendm 
                FROM sanpham sp 
                LEFT JOIN danhmuc dm ON sp.madm = dm.madm 
                WHERE sp.madm = ? 
                ORDER BY sp.masp DESC";
        return $this->db->select($sql, 'i', [$madm]);
    }
    
    /**
     * Tìm kiếm sản phẩm
     */
    public function search($keyword, $madm = null) {
        $sql = "SELECT sp.*, dm.tendm 
                FROM sanpham sp 
                LEFT JOIN danhmuc dm ON sp.madm = dm.madm 
                WHERE (sp.tensp LIKE ? OR sp.hang LIKE ? OR sp.ghichu LIKE ?)";
        
        $types = 'sss';
        $params = ["%$keyword%", "%$keyword%", "%$keyword%"];
        
        if ($madm && $madm != '0') {
            $sql .= " AND sp.madm = ?";
            $types .= 'i';
            $params[] = $madm;
        }
        
        $sql .= " ORDER BY sp.masp DESC";
        
        return $this->db->select($sql, $types, $params);
    }
    
    /**
     * Thêm sản phẩm mới
     */
    public function add($data) {
        $sql = "INSERT INTO sanpham (tensp, gia, sl, hang, baohanh, ghichu, hinhanh, madm) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        return $this->db->insert($sql, 'sdisisssi', [
            $data['tensp'],
            $data['gia'],
            $data['sl'],
            $data['hang'],
            $data['baohanh'],
            $data['ghichu'] ?? '',
            $data['hinhanh'] ?? '',
            $data['madm']
        ]);
    }
    
    /**
     * Cập nhật sản phẩm
     */
    public function updateProduct($masp, $data) {
        $sql = "UPDATE sanpham 
                SET tensp = ?, gia = ?, sl = ?, hang = ?, baohanh = ?, ghichu = ?, hinhanh = ?, madm = ? 
                WHERE masp = ?";
        
        return $this->db->execute($sql, 'sdisisssi', [
            $data['tensp'],
            $data['gia'],
            $data['sl'],
            $data['hang'],
            $data['baohanh'],
            $data['ghichu'] ?? '',
            $data['hinhanh'] ?? '',
            $data['madm'],
            $masp
        ]);
    }
    
    /**
     * Cập nhật số lượng sản phẩm
     */
    public function updateQuantity($masp, $change) {
        $sql = "UPDATE sanpham SET sl = sl + ? WHERE masp = ? AND sl + ? >= 0";
        return $this->db->execute($sql, 'iii', [$change, $masp, $change]);
    }
    
    /**
     * Kiểm tra sản phẩm tồn tại
     */
    public function exists($masp) {
        $sql = "SELECT 1 FROM sanpham WHERE masp = ?";
        $result = $this->db->select($sql, 'i', [$masp]);
        return !empty($result);
    }
    
    /**
     * Lấy số lượng tồn kho
     */
    public function getStock($masp) {
        $sql = "SELECT sl FROM sanpham WHERE masp = ?";
        $result = $this->db->select($sql, 'i', [$masp]);
        return $result ? (int)$result[0]['sl'] : 0;
    }
    
    /**
     * Lấy sản phẩm mới nhất
     */
    public function getLatest($limit = 12) {
        $sql = "SELECT sp.*, dm.tendm 
                FROM sanpham sp 
                LEFT JOIN danhmuc dm ON sp.madm = dm.madm 
                ORDER BY sp.masp DESC 
                LIMIT ?";
        return $this->db->select($sql, 'i', [$limit]);
    }
    
    /**
     * Tìm sản phẩm theo ID (không kèm danh mục)
     */
    public function findById($masp) {
        $sql = "SELECT * FROM sanpham WHERE masp = ?";
        $result = $this->db->select($sql, 'i', [$masp]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Cập nhật số lượng tồn kho (alias của updateQuantity)
     */
    public function updateStock($masp, $change) {
        return $this->updateQuantity($masp, $change);
    }
    
    /**
     * Lấy tất cả sản phẩm
     */
    public function getAll($orderBy = 'masp ASC') {
        return $this->getAllWithCategory($orderBy);
    }
}
