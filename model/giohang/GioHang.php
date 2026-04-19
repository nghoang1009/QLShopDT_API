<?php
class GioHang {
    public $magio, $maitem, $masp, $sl;

    /**
     * @param int $magio
     * @param int $maitem
     * @param int $masp
     * @param int $sl
     */
    function __construct($magio, $maitem, $masp, $sl) {
        $this->magio = $magio;
        $this->maitem = $maitem;
        $this->masp = $masp;
        $this->sl = $sl;
    }

    /**
     * @return array - Associative Array chứa thông tin về sản phẩm
     */
    public function get() {
        return [
            "magio" => $this->magio,
            "maitem" => $this->maitem,
            "masp" => $this->masp,
            "sl" => $this->sl
        ];
    }
}


?>