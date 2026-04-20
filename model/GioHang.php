<?php
/**
 * GioHang Model - Quản lý giỏ hàng
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
require_once BASE_PATH . '/core/Model.php';

class GioHang extends Model {
    protected $table = 'giohang';
    protected $primaryKey = 'magio';
    
    /**
     * Lấy tất cả items trong giỏ hàng
     */
    public function getAllItems() {
        $sql = "SELECT gi.maitem, gi.magio, gi.masp, gi.sl,
                sp.tensp, sp.gia, sp.hinhanh, sp.hang, (sp.gia * gi.sl) AS thanhtien
                FROM giohang_item gi
                JOIN sanpham sp ON gi.masp = sp.masp";
        return $this->db->select($sql);
    }
    
    /**
     * Lấy giỏ hàng theo mã khách hàng
     */
    public function getByCustomer($makh) {
        $sql = "SELECT ghi.maitem, ghi.magio, ghi.masp, ghi.sl, 
                sp.tensp, sp.gia, sp.hinhanh, sp.hang, (sp.gia * ghi.sl) AS thanhtien
                FROM giohang gh
                JOIN giohang_item ghi ON gh.magio = ghi.magio
                JOIN sanpham sp ON ghi.masp = sp.masp
                WHERE gh.makh = ?";
        return $this->db->select($sql, 'i', [$makh]);
    }
    
    /**
     * Tìm mã khách hàng theo username
     */
    public function findCustomerByUsername($username) {
        $sql = "SELECT kh.makh
                FROM taikhoan tk
                JOIN khachhang kh ON tk.matk = kh.makh
                WHERE tk.tentk = ?";
        $result = $this->db->select($sql, 's', [$username]);
        return $result ? (int)$result[0]['makh'] : false;
    }
    
    /**
     * Kiểm tra khách hàng có giỏ hàng không
     * @return int|false - Mã giỏ nếu có, false nếu không
     */
    public function getCartId($makh) {
        $sql = "SELECT magio FROM giohang WHERE makh = ?";
        $result = $this->db->select($sql, 'i', [$makh]);
        return $result ? (int)$result[0]['magio'] : false;
    }
    
    /**
     * Tạo giỏ hàng mới
     * @return int|false - Mã giỏ mới
     */
    public function createCart($makh) {
        $sql = "INSERT INTO giohang (makh) VALUES (?)";
        return $this->db->insert($sql, 'i', [$makh]);
    }
    
    /**
     * Kiểm tra sản phẩm có trong giỏ không
     * @return array|false
     */
    public function getCartItem($magio, $masp) {
        $sql = "SELECT ghi.*, sp.sl as sl_kho 
                FROM giohang_item ghi 
                JOIN sanpham sp ON ghi.masp = sp.masp 
                WHERE ghi.magio = ? AND ghi.masp = ?";
        $result = $this->db->select($sql, 'ii', [$magio, $masp]);
        return $result ? $result[0] : false;
    }
    
    /**
     * Cập nhật số lượng sản phẩm trong giỏ
     */
    public function updateItemQuantity($magio, $masp, $newQuantity) {
        $sql = "UPDATE giohang_item SET sl = ? WHERE magio = ? AND masp = ?";
        return $this->db->execute($sql, 'iii', [$newQuantity, $magio, $masp]);
    }
    
    /**
     * Thêm sản phẩm vào giỏ
     */
    public function addItem($magio, $masp, $sl) {
        $sql = "INSERT INTO giohang_item (magio, masp, sl) VALUES (?, ?, ?)";
        return $this->db->insert($sql, 'iii', [$magio, $masp, $sl]);
    }
    
    /**
     * Xóa sản phẩm khỏi giỏ
     */
    public function removeItem($magio, $masp) {
        $sql = "DELETE FROM giohang_item WHERE magio = ? AND masp = ?";
        return $this->db->execute($sql, 'ii', [$magio, $masp]);
    }
    
    /**
     * Xóa tất cả items trong giỏ
     */
    public function clearCart($magio) {
        $sql = "DELETE FROM giohang_item WHERE magio = ?";
        return $this->db->execute($sql, 'i', [$magio]);
    }
    
    /**
     * Tính tổng tiền giỏ hàng
     */
    public function getCartTotal($makh) {
        $sql = "SELECT SUM(sp.gia * ghi.sl) as total
                FROM giohang gh
                JOIN giohang_item ghi ON gh.magio = ghi.magio
                JOIN sanpham sp ON ghi.masp = sp.masp
                WHERE gh.makh = ?";
        $result = $this->db->select($sql, 'i', [$makh]);
        return $result ? (float)$result[0]['total'] : 0;
    }
    
    /**
     * Đếm số sản phẩm trong giỏ
     */
    public function countItems($makh) {
        $sql = "SELECT COUNT(*) as total
                FROM giohang gh
                JOIN giohang_item ghi ON gh.magio = ghi.magio
                WHERE gh.makh = ?";
        $result = $this->db->select($sql, 'i', [$makh]);
        return $result ? (int)$result[0]['total'] : 0;
    }
    
    /**
     * Thêm sản phẩm vào giỏ (xử lý đầy đủ logic)
     */
    public function addToCart($makh, $masp, $quantity) {
        // Kiểm tra số lượng hợp lệ
        if (!is_numeric($quantity) || $quantity <= 0) {
            return ['success' => false, 'message' => 'Số lượng phải là số nguyên lớn hơn 0'];
        }
        
        // Lấy hoặc tạo giỏ hàng
        $magio = $this->getCartId($makh);
        if (!$magio) {
            $magio = $this->createCart($makh);
            if (!$magio) {
                return ['success' => false, 'message' => 'Không thể tạo giỏ hàng'];
            }
        }
        
        // Kiểm tra số lượng tồn kho
        $sanPhamModel = new SanPham();
        $stock = $sanPhamModel->getStock($masp);
        
        if ($stock <= 0) {
            return ['success' => false, 'message' => 'Sản phẩm đã hết hàng'];
        }
        
        // Kiểm tra sản phẩm đã có trong giỏ chưa
        $existingItem = $this->getCartItem($magio, $masp);
        
        if ($existingItem) {
            $newQuantity = $existingItem['sl'] + $quantity;
            
            if ($newQuantity > $stock) {
                return ['success' => false, 'message' => "Số lượng vượt quá tồn kho (còn $stock sản phẩm)"];
            }
            
            $result = $this->updateItemQuantity($magio, $masp, $newQuantity);
        } else {
            if ($quantity > $stock) {
                return ['success' => false, 'message' => "Số lượng vượt quá tồn kho (còn $stock sản phẩm)"];
            }
            
            $result = $this->addItem($magio, $masp, $quantity);
        }
        
        if ($result !== false) {
            return ['success' => true, 'message' => 'Thêm vào giỏ hàng thành công'];
        }
        
        return ['success' => false, 'message' => 'Có lỗi xảy ra'];
    }
}