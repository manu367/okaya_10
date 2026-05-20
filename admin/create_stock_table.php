<?php
require_once("../includes/dbconnect.php");
$today=date("Y-m-d");

$location_exist_table='client_inventory';
$location_new_table="`client_inventory".$today."`";

$eng_exist_table='user_inventory';
$eng_new_table="`user_inventory".$today."`";


$create=mysqli_query($link1,"CREATE TABLE $location_new_table LIKE $location_exist_table")or die("err-1".mysqli_error($link1));
$insertdata=mysqli_query($link1,"INSERT INTO $location_new_table SELECT * FROM $location_exist_table")or die("err-2".mysqli_error($link1));

$create=mysqli_query($link1,"CREATE TABLE $eng_new_table LIKE $eng_exist_table")or die("err-3".mysqli_error($link1));
$insertdata=mysqli_query($link1,"INSERT INTO $eng_new_table SELECT * FROM $eng_exist_table")or die("err-4".mysqli_error($link1));

?>