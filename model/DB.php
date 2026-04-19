<?php
class DB {
    private $host = "localhost";
    private $user = "alcen";
    private $pass = "alcenium";
    private $db = "qlshopdienthoai";

    public function getConnection(){
        $conn = new mysqli($this->host, $this->user, $this->pass, $this->db);

        if($conn -> connect_error)
            exit("CONNECT FAILED: " . $conn -> connect_error);

        return $conn;
    }
}
?>