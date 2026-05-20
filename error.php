<?php
session_start();
session_destroy();
$msg='1';
header("Location:index.php?msg=".$msg);
exit;
?>