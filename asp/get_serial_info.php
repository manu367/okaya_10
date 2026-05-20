<?php
require_once("../includes/config.php");
include("../includes/check_serial_no_for_job.php");

$serial_no = $_REQUEST['serialno'];
/////check in jobsheet data
$resp_jd = getJobSheetValidate($serial_no,"","","","",$link1);
echo "JD Response: ";
echo "<pre>";
print_r(json_decode($resp_jd,JSON_PRETTY_PRINT));
echo "</pre>";
/////check in DMS data
$resp_sd = getSaleDataValidate($serial_no,"","","","",$link1);
echo "<br/>";
echo "DMS Response: ";
echo "<pre>";
print_r(json_decode($resp_sd,JSON_PRETTY_PRINT));
echo "</pre>";
/////check in Spilt serial data
$resp_ss = getSerialSplitLogic($serial_no,"","","","",$link1);
echo "<br/>";
echo "Split Response: ";
echo "<pre>";
print_r(json_decode($resp_ss,JSON_PRETTY_PRINT));
echo "</pre>";
?>