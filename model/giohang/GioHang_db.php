<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/model/DB.php');

class GioHang_db {
    /**
     * @param String $username - Tên tài khoản
     * @return false|int - Trả về mã khách hàng nếu tìm thấy, nếu không trả về false
     */
    public static function timMaKH($username) {
        $db = new DB();
        $conn = $db->getConnection();

        $sql = "SELECT kh.makh
                FROM taikhoan tk
                JOIN khachhang kh ON tk.matk = kh.makh
                WHERE tk.tentk = '$username'";
        $result = $conn->query($sql);

        if (!$result)
            exit("Lỗi: " . $conn->error);

        if (mysqli_num_rows($result) == 0) {
            return false;
        }

        return $result->fetch_assoc()['makh'];
    }


    /**
     * @param int $makh - Mã của khách hàng cần tìm
     * @return int|false - Trả về mã giỏ nếu giỏ hàng tồn tại, ngược lại trả về false
     */
    public static function giohangTonTai($makh) {
        $db = new DB();
        $conn = $db->getConnection();

        $sql = "SELECT magio FROM giohang WHERE makh = '$makh'";
        $result = $conn->query($sql);

        if (!$result)
            exit("Lỗi: " . $conn->error);

        if (mysqli_num_rows($result) == 0) {
            return false;
        }
        return $result->fetch_assoc()['magio'];
    }


    /**
     * @param int $magio
     * @param int $masp
     * @return array|false - Trả về mảng chứa các sản phẩm nếu sản phẩm tồn tại trong csdl. False nếu ngược lại
     */
    public static function sanphamTonTai($magio, $masp){
        $db = new DB();
        $conn = $db->getConnection();

        $sql = "SELECT * FROM giohang_item WHERE magio = '$magio' AND masp = '$masp'";
        $result = $conn->query($sql);

        if (!$result)
            exit("Lỗi: " . $conn->error);

        if ($result->num_rows == 0)
            return false;

        return $result->fetch_all(MYSQLI_ASSOC);
    }


    /**
     * @param int $makh - Mã của khách hàng cần tạo giỏ
     * @return int|false - Trả về mã giỏ được tạo tự động nếu thêm thành công, ngược lại trả về false
     */
    public static function taoGioHang($makh) {
        $db = new DB();
        $conn = $db->getConnection();

        $sql = "INSERT INTO giohang (magio, makh) VALUES (NULL, '$makh')";
        $result = $conn->query($sql);

        if (!$result)
            return false;

        $magio = $conn->insert_id;
        return $magio;
    }

    public static function suaSoLuong($magio, $masp, $soLuongMoi){
        $db = new DB();
        $conn = $db->getConnection();

        $sql = "UPDATE giohang_item SET sl = '$soLuongMoi' WHERE magio = '$magio' AND masp = '$masp'";
        $result = $conn->query($sql);

        if (!$result)
            return false;
        return true;
    }

    public static function themSanPham($magio, $masp, $sl){
        $db = new DB();
        $conn = $db->getConnection();

        $sql = "INSERT INTO giohang_item (maitem, magio, masp, sl) 
                       VALUES (NULL, '$magio', '$masp', '$sl')";
        $result = $conn->query($sql);

        if (!$result)
            return false;
        return true;
    }
}
?>