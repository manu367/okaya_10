<?php
require_once("config.php");
include("check_serial_no_for_job.php");

$serial_no = $_REQUEST['serialno'];
$mfd="";
$mfd_ex="";
$wp_days="";
$product_id = "";
$product_name = "";
$brand_id = "";
$brand_name = "";
$model_id = "";
$model_name = "";
$dwp = "";
$dop = "";
/////check in jobsheet data
$resp_jd = getJobSheetValidate($serial_no,"","","","",$link1);
$decode_jd_resp =json_decode($resp_jd,true);
/////if status success
if($decode_jd_resp['status']=="1"){
    $mfd = $decode_jd_resp['mfd'];
    $mfd_ex = $decode_jd_resp['mfd_ex'];
    $wp_days = $decode_jd_resp['warrenty_day'];
    $dwp = $decode_jd_resp['dwp'];
    $product_id = $decode_jd_resp['product_id'];
    $product_name = $decode_jd_resp['product_name'];
    $brand_id = $decode_jd_resp['brand_id'];
    $brand_name = $decode_jd_resp['brand_name'];
    $model_id = $decode_jd_resp['model_id'];
    $model_name = $decode_jd_resp['model'];
    $dop = $decode_jd_resp['dop'];
}else{
    /////check in DMS data
    $resp_sd = getSaleDataValidate($serial_no,"","","","",$link1);
    $decode_sd_resp =json_decode($resp_sd,true);
    if($decode_sd_resp['status']=="1"){
        $mfd = $decode_sd_resp['mfd'];
        $mfd_ex = $decode_sd_resp['mfd_ex'];
        $wp_days = $decode_sd_resp['warrenty_day'];
        $dwp = $decode_sd_resp['dwp'];
        $product_id = $decode_sd_resp['product_id'];
        $product_name = $decode_sd_resp['product_name'];
        $brand_id = $decode_sd_resp['brand_id'];
        $brand_name = $decode_sd_resp['brand_name'];
        $model_id = $decode_sd_resp['model_id'];
        $model_name = $decode_sd_resp['model'];
        $dop = $decode_sd_resp['dop'];
    }else{
        /////check in Spilt serial data
        $resp_ss = getSerialSplitLogic($serial_no,"","","","",$link1);
        $decode_ss_resp =json_decode($resp_ss,true);
        if($decode_ss_resp['status']=="1"){
            $mfd = $decode_ss_resp['mfd'];
            $mfd_ex = $decode_ss_resp['mfd_ex'];
            $wp_days = $decode_ss_resp['warrenty_day'];
            $dwp = $decode_ss_resp['dwp'];
            $product_id = $decode_ss_resp['product_id'];
            $product_name = $decode_ss_resp['product_name'];
            $brand_id = $decode_ss_resp['brand_id'];
            $brand_name = $decode_ss_resp['brand_name'];
            $model_id = $decode_ss_resp['model_id'];
            $model_name = $decode_ss_resp['model'];
            $dop = $decode_ss_resp['dop'];
        }else{
        
        }
    }
}
echo $model_id."^".$model_name."^".$wp_days."^".$dwp."^".$product_id."^".$brand_id."^".$product_name."^".$brand_name."^".$_REQUEST['target']."^".$dop."^".$mfd."^".$mfd_ex;
?>