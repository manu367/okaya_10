<?php

require_once("../includes/dbconnect.php");
$today=date("Y-m-d");

$res1=mysqli_query($link1,"insert into  date_counter  SET  job_date ='".$today."' ");


?>