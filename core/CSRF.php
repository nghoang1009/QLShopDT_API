<?php
/**
 * CSRF - Cross-Site Request Forgery Protection
 */

class CSRF {
    private static $tokenName = 'csrf_token';
    
    /**
     * Tạo token mới
     */
    public static function generate() {
        if (!isset($_SESSION[self::$tokenName])) {
            $_SESSION[self::$tokenName] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::$tokenName];
    }
    
    /**
     * Lấy token hiện tại
     */
    public static function token() {
        return self::generate();
    }
    
    /**
     * Tạo hidden input field
     */
    public static function field() {
        $token = self::generate();
        return '<input type="hidden" name="' . self::$tokenName . '" value="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Verify token từ request
     */
    public static function verify($token = null) {
        if ($token === null) {
            $token = $_POST[self::$tokenName] ?? '';
        }
        
        if (empty($_SESSION[self::$tokenName])) {
            return false;
        }
        
        return hash_equals($_SESSION[self::$tokenName], $token);
    }
    
    /**
     * Regenerate token (dùng sau khi verify thành công hoặc khi login)
     */
    public static function regenerate() {
        $_SESSION[self::$tokenName] = bin2hex(random_bytes(32));
        return $_SESSION[self::$tokenName];
    }
    
    /**
     * Xóa token
     */
    public static function clear() {
        unset($_SESSION[self::$tokenName]);
    }
}
