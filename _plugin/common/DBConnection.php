<?php
class DBConnection {
    public static function getConnection() {
        return mysqli_connect("localhost", "root", "","crm",3306);
    }
}
?>
