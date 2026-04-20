<?php
/**
 * TaiKhoan Model - Quản lý tài khoản và xác thực
 */
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}require_once BASE_PATH . '/core/Model.php';

class TaiKhoan extends Model {
    protected $table = 'taikhoan';
    protected $primaryKey = 'matk';
    
    /**
     * Đăng nhập
     * @return array|false - Thông tin user nếu thành công
     */
    public function authenticate($username, $password) {
        $sql = "SELECT matk, tentk, mk, role FROM taikhoan WHERE tentk = ?";
        $result = $this->db->select($sql, 's', [$username]);
        
        if (empty($result)) {
            return false;
        }
        
        $user = $result[0];
        
        // Kiểm tra password
        // Hỗ trợ cả plaintext cũ và hash mới
        if (password_verify($password, $user['mk']) || $password === $user['mk']) {
            // Trả về thông tin user (không có password)
            return [
                'matk' => $user['matk'],
                'tentk' => $user['tentk'],
                'role' => (int)$user['role']
            ];
        }
        
        return false;
    }
    
    /**
     * Đăng nhập kèm thông tin khách hàng
     * @return array|false - Thông tin user + khách hàng nếu thành công
     */
    public function loginWithCustomerInfo($username, $password) {
        $sql = "SELECT tk.matk, tk.tentk, tk.mk, tk.role, kh.tenkh, kh.diachi, kh.sdt 
                FROM taikhoan tk 
                LEFT JOIN khachhang kh ON tk.matk = kh.makh 
                WHERE tk.tentk = ?";
        $result = $this->db->select($sql, 's', [$username]);
        
        if (empty($result)) {
            return false;
        }
        
        $user = $result[0];
        
        // Kiểm tra password - hỗ trợ cả plaintext cũ và hash mới
        if (password_verify($password, $user['mk']) || $password === $user['mk']) {
            unset($user['mk']); // Không trả mật khẩu về client
            return $user;
        }
        
        return false;
    }
    
    /**
     * Đăng ký tài khoản mới
     */
    public function register($username, $password, $role = 0) {
        // Kiểm tra username đã tồn tại
        if ($this->usernameExists($username)) {
            return ['success' => false, 'message' => 'Tên đăng nhập đã tồn tại'];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO taikhoan (tentk, mk, role) VALUES (?, ?, ?)";
        $id = $this->db->insert($sql, 'ssi', [$username, $hashedPassword, $role]);
        
        if ($id) {
            return ['success' => true, 'id' => $id, 'message' => 'Đăng ký thành công'];
        }
        
        return ['success' => false, 'message' => 'Đăng ký thất bại'];
    }
    
    /**
     * Kiểm tra username đã tồn tại
     */
    public function usernameExists($username) {
        $sql = "SELECT 1 FROM taikhoan WHERE tentk = ?";
        $result = $this->db->select($sql, 's', [$username]);
        return !empty($result);
    }
    
    /**
     * Lấy thông tin user theo ID
     */
    public function findById($matk) {
        $sql = "SELECT matk, tentk, role FROM taikhoan WHERE matk = ?";
        $result = $this->db->select($sql, 'i', [$matk]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Lấy thông tin user theo username
     */
    public function findByUsername($username) {
        $sql = "SELECT matk, tentk, role FROM taikhoan WHERE tentk = ?";
        $result = $this->db->select($sql, 's', [$username]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Đổi mật khẩu
     */
    public function changePassword($matk, $oldPassword, $newPassword) {
        // Kiểm tra mật khẩu cũ
        $sql = "SELECT mk FROM taikhoan WHERE matk = ?";
        $result = $this->db->select($sql, 'i', [$matk]);
        
        if (empty($result)) {
            return ['success' => false, 'message' => 'Tài khoản không tồn tại'];
        }
        
        $currentHash = $result[0]['mk'];
        
        // Kiểm tra password cũ
        if (!password_verify($oldPassword, $currentHash) && $oldPassword !== $currentHash) {
            return ['success' => false, 'message' => 'Mật khẩu cũ không đúng'];
        }
        
        // Hash và cập nhật password mới
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE taikhoan SET mk = ? WHERE matk = ?";
        $affected = $this->db->execute($sql, 'si', [$newHash, $matk]);
        
        if ($affected !== false) {
            return ['success' => true, 'message' => 'Đổi mật khẩu thành công'];
        }
        
        return ['success' => false, 'message' => 'Đổi mật khẩu thất bại'];
    }
    
    /**
     * Cập nhật role
     */
    public function updateRole($matk, $role) {
        $sql = "UPDATE taikhoan SET role = ? WHERE matk = ?";
        return $this->db->execute($sql, 'ii', [$role, $matk]);
    }
    
    /**
     * Xóa tài khoản
     */
    public function deleteAccount($matk) {
        $sql = "DELETE FROM taikhoan WHERE matk = ?";
        return $this->db->execute($sql, 'i', [$matk]);
    }
    
    /**
     * Lấy tất cả tài khoản (cho admin)
     */
    public function getAllAccounts() {
        $sql = "SELECT matk, tentk, role FROM taikhoan ORDER BY matk ASC";
        return $this->db->select($sql);
    }
}
