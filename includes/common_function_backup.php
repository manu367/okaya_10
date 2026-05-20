<?php
//
////////// function for displaying error message
//
//function errorMsg($errorcode){
//
//	if($errorcode=="1") {
//
//		$msg = "User Id Or Password Wrong! Please Try Again!";
//
//	} else if($errorcode=="2") {
//
//		$msg = "Session Expired! Please Login Again!";
//
//	} else if($errorcode=="3") {
//
//		$msg = "You have Successfuly Logged Out.";
//
//	} else if($errorcode=="4") {
//
//		$msg = "User Id is not found in record.";
//
//	} else{
//
//	    $msg = $errorcode;
//
//	}
//
//	return $msg;
//
//}
//
//function getdispatchstatus($var){
// if($var==1){
//  $status="Pending";
// }else if($var==2){
//  $status="Processed";
// }else if($var==3){
//  $status="Dispatched";
// }else if($var==4){
//  $status="Received";
// }else if($var==5){
//  $status="Cancelled";
// }else if($var==6){
//  $status="Partial Processed";
// }else if($var==7){
//  $status="Pending For Admin Approval";
// }else if($var==8){
//  $status="Pending For Finance Approval";
// }else if($var==9){
//  $status="Pending For Gate Entry";
// }else if($var==10){
//  $status="Partial Received";
// }else if($var==11){
//  $status="Missing";
// }
//	else if($var==12){
//
//		$status="CN Generated";
//
//	}
//	else if($var==13){
//
//		$status="Pending for Receive";
//
//	}
//
//	else{
//
//		$status="-";
//
//	}
//
//	return $status;
//
//}
///////////// function to clean data///////
//
//function cleanData($instr) {
//
//	$str=trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($instr))))));
//
//	return $str;
//
//}
//
////////// set currency format /////
//
//function currencyFormat($amttt){
//
//	return "<i class='fa fa-inr' aria-hidden='true'></i> ".number_format($amttt,'2','.','');
//
//}
//
//function getHoursMinSec($seconds){
//	$H = floor($seconds / 3600);
//	$i = ($seconds / 60) % 60;
//	$s = $seconds % 60;
//	return sprintf("%02d:%02d:%02d", $H, $i, $s);
//}
//////////////////////////////////GET STOCK mOVEMENT//////////////
//
//
//function stock_movement($partcode,$location_code,$type,$tracation_type,$fromdate,$todate,$s_type,$link1){
//
//if($s_type=='opening'){
//$daterange = "create_date < '".$fromdate."' and stock_transfer='".$type."' and type_of_transfer='".$tracation_type."' and stock_type='OK' ";
//}else{
//$daterange = "create_date  >= '".$fromdate."' and create_date  <= '".$todate."'  and stock_transfer='".$type."' and type_of_transfer='".$tracation_type."'  and stock_type='OK'";
//}
//
//  $query="select sum(qty) as tot_qty from stock_ledger where partcode='".$partcode."' and create_by='".$location_code."' and ".$daterange." group by partcode";
//
//	$result=mysqli_query($link1,$query);
//	$row=mysqli_fetch_array($result);
//
//	//// check if query is not executed
//
//    if (mysqli_num_rows($result)==0) {
//
//	  $sum_qty=0;
//	}
//	else{
//	$sum_qty=$row['tot_qty'];
//	}
//
//	return $sum_qty;
//
//}
//
/////////////////// Daily Transaction history
//
//function transactionHistory($ref_no,$ref_date,$entry_date,$entry_by,$location_code,$party_code,$transaction_type,$action_taken,$amount,$crdr,$ac_id,$ac_type,$link1){
//
//	mysqli_query($link1,"INSERT INTO day_book_entries set ref_no='$ref_no', ref_date='$ref_date', entry_date='$entry_date', entry_by='$entry_by', location_code='$location_code', party_code='$party_code', transaction_type='$transaction_type', action_taken='$action_taken', amount='$amount', cr_dr='$crdr', ac_id='$ac_id', ac_type='$ac_type'") or die("Error in saving T.H.".mysql_error());
//
//}
//
/////////////////  date format like DD-MM-YYYY ////////////////
//
//function dt_format($dt_sel){
//
//  return substr($dt_sel,8,2)."-".substr($dt_sel,5,2)."-".substr($dt_sel,0,4);
//
//}
//
/////////////////  date format like MM/DD/YYYY ////////////////
//
//function dt_format2($dt_sel){
//
//  return substr($dt_sel,5,2)."/".substr($dt_sel,8,2)."/".substr($dt_sel,0,4);
//
//}
//
//////////// function to calculate day difference between two dates //////////
//
//function daysDifference($endDate, $beginDate){
//
//	$date_parts1=explode("-", $beginDate); $date_parts2=explode("-", $endDate);
//
//	$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
//
//	$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
//
//	return $end_date - $start_date;
//
//}
//
////////// function to capture daily activities /////
//
//function dailyActivity($uid,$refno,$activityType,$actionTaken,$systemIp,$link1,$errorflag){
//
//	$todayDate=date("Y-m-d");
//
//	$todayTime=date("H:i:s");
//
//	$flag=$errorflag;
//
//	$query="INSERT INTO daily_activities set userid='".$uid."',ref_no='".$refno."',activity_type='".$activityType."',action_taken='".$actionTaken."',update_date='".$todayDate."',update_time='".$todayTime."',system_ip='".$systemIp."'";
//
//	$result=mysqli_query($link1,$query);
//
//	//// check if query is not executed
//
//    if (!$result) {
//
//	     $flag = false;
//
//         echo "Error detailsDA: " . mysqli_error($link1) . ".";
//
//	}
//
//	return $flag;
//
//}
//
////////// function to capture call history /////
//
//function callHistory($job_no,$location_code,$status,$activity,$outcome,$updateby,$warrantystatus,$remark,$travelkm,$travel,$ip,$link1,$errorflag){
//
//	$todayDate=date("Y-m-d");
//
//	$todayTime=date("H:i:s");
//
//	$flag=$errorflag;
//
//	$query="INSERT INTO call_history set job_no='".$job_no."',location_code='".$location_code."',status='".$status."',activity='".$activity."',outcome='".$outcome."',updated_by='".$updateby."', warranty_status='".$warrantystatus."', remark='".$remark."', ip='".$ip."',travel_km='".$travelkm."',travel='".$travel."'";
//
//	$result=mysqli_query($link1,$query);
//
//	//// check if query is not executed
//
//    if (!$result) {
//
//	     $flag = false;
//
//         echo "Error detailsCH: " . mysqli_error($link1) . ".";
//
//	}
//
//	return $flag;
//
//}
//
///////// function to get admin user details
//
//function getAdminDetails($adminid,$fields,$link1){
//
//   $explodee=explode(",",$fields);
//
//   $user_details=mysqli_fetch_array(mysqli_query($link1,"select $fields from admin_users where username='$adminid'"));
//
//   $rtn_str="";
//
//   for($k=0;$k < count($explodee);$k++){
//
//       if($rtn_str==""){
//
//          $rtn_str.=$user_details[$k];
//
//	   }
//
//       else{
//
//          $rtn_str.="~".$user_details[$k];
//
//	   }
//
//   }
//
//   return $rtn_str;
//
//}
//
///////// function to get Location  details
//
//function getLocationDetails($locid,$fields,$link1){
//
//   $explodee=explode(",",$fields);
//
//   $user_details=mysqli_fetch_array(mysqli_query($link1,"select $fields from asc_master where asc_code='$locid'"));
//
//   $rtn_str="";
//
//   for($k=0;$k < count($explodee);$k++){
//
//       if($rtn_str==""){
//
//          $rtn_str.=$user_details[$k];
//
//	   }
//
//       else{
//
//          $rtn_str.="~".$user_details[$k];
//
//	   }
//
//   }
//
//   return $rtn_str;
//
//}
//
///////// function to get Location  details
//
//function getVendorDetails($vendorid,$fields,$link1){
//
//   $explodee=explode(",",$fields);
//
//   $user_details=mysqli_fetch_array(mysqli_query($link1,"select $fields from vendor_master where id='$vendorid'"));
//
//   $rtn_str="";
//
//   for($k=0;$k < count($explodee);$k++){
//
//       if($rtn_str==""){
//
//          $rtn_str.=$user_details[$k];
//
//	   }
//
//       else{
//
//          $rtn_str.="~".$user_details[$k];
//
//	   }
//
//   }
//
//   return $rtn_str;
//
//}
//
////// get access location ////
//
//function getAccessLocation($userid,$link1){
//
//	$loction_str="";
//
//	$res_parent=mysqli_query($link1,"select location_id from access_location where uid='".$userid."' and status='Y'")or die(mysqli_error());
//
//	if(mysqli_num_rows($res_parent)>0){
//
//	while($row_parent=mysqli_fetch_assoc($res_parent)){
//
//	   if($loction_str==""){
//
//		   $loction_str.="'".$row_parent['location_id']."'";
//
//	   }else{
//
//		   $loction_str.=",'".$row_parent['location_id']."'";
//
//	   }
//
//	}
//
//	}else{
//
//		$loction_str="''";
//
//	}
//
//	return $loction_str;
//
//}
//
////// get access state ////
//
//function getAccessState($userid,$link1){
//
//	$state_str="";
//
//	$res_state=mysqli_query($link1,"select stateid from access_region where userid='".$userid."' and status='Y' group by stateid")or die(mysqli_error($link1));
//
//	if(mysqli_num_rows($res_state)>0){
//
//	while($row_state=mysqli_fetch_assoc($res_state)){
//
//	   if($state_str==""){
//
//		   $state_str.="'".$row_state['stateid']."'";
//
//	   }else{
//
//		   $state_str.=",'".$row_state['stateid']."'";
//
//	   }
//
//	}
//
//	}else{
//
//		$state_str="''";
//
//	}
//
//	return $state_str;
//
//}
//
////// get access city ////
//
//function getAccessCity($userid,$link1){
//
//	$city_str="";
//
//	$res_city=mysqli_query($link1,"select cityid from access_region where userid='".$userid."' and status='Y' group by cityid")or die(mysqli_error($link1));
//
//	if(mysqli_num_rows($res_city)>0){
//
//	while($row_city=mysqli_fetch_assoc($res_city)){
//
//	   if($city_str==""){
//
//		   $city_str.="'".$row_city['cityid']."'";
//
//	   }else{
//
//		   $city_str.=",'".$row_city['cityid']."'";
//
//	   }
//
//	}
//
//	}else{
//
//		$city_str="''";
//
//	}
//
//	return $city_str;
//
//}
//
////// get access product ////
//function getAccessProduct($userid,$link1){
//	$product_str="";
//	$res_product=mysqli_query($link1,"select product_id from access_product where location_code in ('".$userid."') and status='Y'")or die(mysqli_error($link1));
//	//if(mysqli_num_rows($res_product)>0){
//	while($row_product=mysqli_fetch_assoc($res_product)){
//	   if($product_str==""){
//		   $product_str.="'".$row_product['product_id']."'";
//	   }else{
//		   $product_str.=",'".$row_product['product_id']."'";
//
//	   }
//	}
//
//	/*}else{
//		$product_str="''";
//	}*/
//	return $product_str;
//}
////// get access brand ////
//function getAccessBrand($userid,$link1){
//	$brand_str="";
//
//	$res_brand=mysqli_query($link1,"select brand_id from access_brand where location_code in ('".$userid."' ) and status='Y'")or die(mysqli_error($link1));
///*	if(mysqli_num_rows($res_brand)>0){*/
//	while($row_brand=mysqli_fetch_assoc($res_brand)){
//	   if($brand_str==""){
//		   $brand_str.="'".$row_brand['brand_id']."'";
//	   }else{
//		   $brand_str.=",'".$row_brand['brand_id']."'";
//	   }
//	}
//	/*}else{
//		$brand_str="''";
//	}*/
//	return $brand_str;
//}
//
//
//
////// get access ASP ////
//function getAccessASP($userid,$link1){
//	$asp_str="";
//
//	$res_asp=mysqli_query($link1,"select location_code from access_asp where cp_code in ('".$userid."' ) and status='Y'")or die(mysqli_error($link1));
///*	if(mysqli_num_rows($res_brand)>0){*/
//	while($row_asp=mysqli_fetch_assoc($res_asp)){
//	   if($asp_str==""){
//		   $asp_str.="'".$row_asp['location_code']."'";
//	   }else{
//		   $asp_str.=",'".$row_asp['location_code']."'";
//	   }
//	}
//	/*}else{
//		$brand_str="''";
//	}*/
//	return $asp_str;
//}
//
//
//////// get excel and cancel process id //
//
//function getExlCnclProcessid($processname,$link1){
//
//	$res_processid=mysqli_query($link1,"select id from excel_cancel_rights where transaction_type='".$processname."' and status='A'")or die(mysqli_error());
//
//	$row_processid=mysqli_fetch_assoc($res_processid);
//
//	if($row_processid['id']){
//
//	    return $row_processid['id'];
//
//	}else{
//
//		return 0;
//
//	}
//
//}
//
////// get access Excel Export rights ////
//
//function getExcelRight($userid,$processid,$link1){
//
//	$excelRightFlag=0;
//
//	$res_exl=mysqli_query($link1,"select sno from excel_export_right where process_id='".$processid."' and user_id='".$userid."' and status='Y'")or die(mysqli_error());
//
//	if(mysqli_num_rows($res_exl)>0){
//
//       $excelRightFlag=1;
//
//	}else{
//
//	   $excelRightFlag=0;
//
//	}
//
//	return $excelRightFlag;
//
//}
//
////// get access Cancel rights ////
//
//function getCancelRight($userid,$processid,$link1){
//
//	$cancelRightFlag=0;
//
//	$res_cancel=mysqli_query($link1,"select id from access_cancel_rights where cancel_type='".$processid."' and uid='".$userid."' and status='Y'")or die(mysqli_error());
//
//	if(mysqli_num_rows($res_cancel)>0){
//
//       $cancelRightFlag=1;
//
//	}else{
//
//	   $cancelRightFlag=0;
//
//	}
//
//	return $cancelRightFlag;
//
//}
//
//////
//
////////// function to capture approval activities /////
//
//function approvalActivity($refno,$refdate,$reqtype,$uid,$actionTaken,$actiondate,$actiontime,$actionrmk,$systemIp,$link1,$errorflag){
//
//	$flag=$errorflag;
//
//	$query="INSERT INTO approval_activities set ref_no='".$refno."',ref_date='".$refdate."',req_type='".$reqtype."',action_by='".$uid."',action_taken='".$actionTaken."',action_date='".$actiondate."',action_time='".$actiontime."',action_remark='".$actionrmk."',action_ip='".$systemIp."'";
//
//	$result=mysqli_query($link1,$query);
//
//	//// check if query is not executed
//
//    if (!$result) {
//
//	     $flag = false;
//
//         echo "Error detailsAA: " . mysqli_error($link1) . ".";
//
//	}
//
//	return $flag;
//
//}
//
////////////////// FUnction for insert into store stock for Stock Leadger/////////////////
//
//function stockLedger($inv_no,$inv_date,$itemcode,$from_party,$to_party,$stock_transfer,$stock_type,$type_name,$action_taken,$qty,$price,$create_by,$createdate,$createtime,$ip,$link1,$errorflag){
//
//	$flag=$errorflag;
//
//    $result=mysqli_query($link1,"insert into stock_ledger set reference_no='".$inv_no."',reference_date='".$inv_date."',partcode='".$itemcode."',from_party='".$from_party."', to_party='".$to_party."',stock_transfer='".$stock_transfer."',stock_type='".$stock_type."',type_of_transfer='".$type_name."',action_taken='".$action_taken."',qty='".$qty."',rate='".$price."',create_by='".$create_by."',create_date='".$createdate."',create_time='".$createtime."',ip='".$ip."'");
//
//	//// check if query is not executed
//
//    if (!$result) {
//
//	     $flag = false;
//
//
//         echo "Error detailsSL: " . mysqli_error($link1) . "";
//
//	}
//
//	return $flag;
//
//}
//
//function stockLedgerO($inv_no,$inv_date,$itemcode,$from_party,$to_party,$stock_transfer,$stock_type,$type_name,$action_taken,$qty,$price,$create_by,$createdate,$createtime,$ip,$link1,$errorflag,$ownercode){
//
//	$flag=$errorflag;
//
//    $result=mysqli_query($link1,"insert into stock_ledger set reference_no='".$inv_no."',reference_date='".$inv_date."',partcode='".$itemcode."',from_party='".$from_party."', to_party='".$to_party."', owner_code='".$ownercode."',stock_transfer='".$stock_transfer."',stock_type='".$stock_type."',type_of_transfer='".$type_name."',action_taken='".$action_taken."',qty='".$qty."',rate='".$price."',create_by='".$create_by."',create_date='".$createdate."',create_time='".$createtime."',ip='".$ip."'");
//
//	//// check if query is not executed
//
//    if (!$result) {
//
//	     $flag = false;
//
//
//         echo "Error detailsSL: " . mysqli_error($link1) . "";
//
//	}
//
//	return $flag;
//
//}
////////////////End of Store Stock Function///////////////////////////////
//
//
////////////////End of Store Stock Function///////////////////////////////
//
/////// append for tracker//////////
//
/////// Function to get status name on the statusid base//
//
//function getFullStatus($gettingfor,$link1){
//
//	$arr_status = array();
//
//	if($gettingfor){ $used_in = " usedin = '".$gettingfor."'";}else{ $used_in = "1";}
//
//	$result_set = mysqli_query($link1,"select statusid, statusname from status_master where ".$used_in."") or die(mysqli_error($link1));
//
//	while($row_set=mysqli_fetch_assoc($result_set)){
//
//		$arr_status[$row_set['statusid']] = $row_set['statusname'];
//
//	}
//
//	return $arr_status;
//
//}
//
/////// Function to get job status name on the statusid base//
//
//function getJobStatus($link1){
//
//	$arr_status = array();
//
//	$result_set = mysqli_query($link1,"select status_id, display_status, main_status_id from jobstatus_master") or die(mysqli_error($link1));
//
//	while($row_set=mysqli_fetch_assoc($result_set)){
//
//		$arr_status[$row_set['status_id']][$row_set['main_status_id']] = $row_set['display_status'];
//
//	}
//
//	return $arr_status;
//
//}
//
/////// Function to get brand name on the brandid base//
//
//function getBrandArray($link1){
//
//	$arr_status = array();
//
//	$result_set = mysqli_query($link1,"select brand_id, brand from brand_master") or die(mysqli_error($link1));
//
//	while($row_set=mysqli_fetch_assoc($result_set)){
//
//		$arr_status[$row_set['brand_id']] = $row_set['brand'];
//
//	}
//
//	return $arr_status;
//
//}
//
/////// Function to get product name on the productid base//
//
//function getProductArray($link1){
//
//	$arr_status = array();
//
//	$result_set = mysqli_query($link1,"select product_id, product_name from product_master") or die(mysqli_error($link1));
//
//	while($row_set=mysqli_fetch_assoc($result_set)){
//
//		$arr_status[$row_set['product_id']] = $row_set['product_name'];
//
//	}
//
//	return $arr_status;
//
//}
//
/////// Function to get status name on the statusid base//
//
//function getCityState($gettingfor,$link1){
//
//	$arr_status = array();
//
//	if($gettingfor){ $used_in = " cityid = '".$gettingfor."'";}else{ $used_in = "1";}
//
//	$result_set = mysqli_query($link1,"select cityid, state, city from city_master where ".$used_in."") or die(mysqli_error($link1));
//
//	while($row_set=mysqli_fetch_assoc($result_set)){
//
//		$arr_status[$row_set['cityid']] = $row_set['state'].",".$row_set['city'];
//
//	}
//
//	return $arr_status;
//
//}
//
/////// Function to get status name on the statusid base//
//
//function getTabRights($uid,$link1){
//
//	$arr_tab = array();
//
//	$result_set = mysqli_query($link1,"select tabid, status from access_tab where userid = '".$uid."'") or die(mysqli_error($link1));
//
//	while($row_set=mysqli_fetch_assoc($result_set)){
//
//		$arr_tab[$row_set['tabid']] = $row_set['status'];
//
//	}
//
//	return $arr_tab;
//
//}
//
/////// get state ////
//
//function getState($link1){
//
// $arr_state = array();
//
// $result_set = mysqli_query($link1,"select stateid, state from state_master order by state") or die(mysqli_error($link1));
//
// while($row_set=mysqli_fetch_assoc($result_set)){
//
//  	$arr_state[$row_set['stateid']] = $row_set['state'];
//
// }
//
// return $arr_state;
//
//}
//
/////// generic function
//
///*function getAnyDetails($keyid,$fields,$lookupname,$tbname,$link1){
//	///// check no. of column
//	$chk_keyword = substr_count($fields, ',');
//   	if($chk_keyword > 0){
//		$explodee = explode(",",$fields);
//   		$tb_details = mysqli_fetch_array(mysqli_query($link1,"select ".$fields." from ".$tbname." where ".$lookupname." = '".$keyid."'"));
//   		$rtn_str = "";
//   		for($k=0;$k < count($explodee);$k++){
//       		if($rtn_str==""){
//          		$rtn_str.= $tb_details[$k];
//	   		}
//       		else{
//          		$rtn_str.= "~".$tb_details[$k];
//			}
//		}
//	}
//	else{
//		$tb_details = mysqli_fetch_array(mysqli_query($link1,"select ".$fields." from ".$tbname." where ".$lookupname." = '".$keyid."'"));
//		$rtn_str = $tb_details[$fields];
//	}
//   return $rtn_str;
//}*/
//
//
//
/////// generic function only for Anee Mobile
//
//function getAnyDetails($keyid,$fld,$lookupname,$tbname,$link1){
//    $chk_keyword = substr_count($fld, ',');
//
//    if($keyid!=''){
//        $part_desc="part_desc";
//        $part_name="part_name";
//        $v_part=",vendor_partcode";
//        if($chk_keyword > 0){
//            if((strpos($fld, 'part_desc') !== false) || (strpos($fld, 'part_name') !== false)){
//                $fields=$fld.$v_part;
//            }else{
//                $fields=$fld;
//            }
//            $explodee = explode(",",$fld);
//		$query1="select ".$fields." from ".$tbname." where ".$lookupname." = '".$keyid."'";
//
//		//print_r($explodee);
//            $res_tb = mysqli_query($link1,$query1) or die("ER1:".mysqli_error($link1));
//   		$tb_details = mysqli_fetch_array($res_tb);
//
//   		$rtn_str = "";
//
//   		for($k=0;$k < count($explodee);$k++){
//			if($explodee[$k]==$part_desc){
//				$rslt=$tb_details[$k];
//			}else if($explodee[$k]==$part_name){
//				$rslt=$tb_details[$k];
//			}else{
//				$rslt=$tb_details[$k];
//			}
//       		if($rtn_str==""){
//          		$rtn_str= $rslt;
//	   		}else{
//          		$rtn_str.= "~".$rslt;
//			}
//		}
//	}else{
//
//		if(($fld==$part_desc) || ($fld==$part_name)){
//			$fields=$fld.",vendor_partcode";
//		}else{
//			$fields=$fld;
//		}
//
////echo "select ".$fields." from ".$tbname." where ".$lookupname." = '".$keyid."'";
//$sql_str=mysqli_query($link1,"select ".$fields." from ".$tbname." where ".$lookupname." = '".$keyid."'");
//		$tb_details = mysqli_fetch_array($sql_str);
//		if($fld==$part_desc){
//			$rtn_str = $tb_details['part_desc'];
//		}else if($fld==$part_name){
//			$rtn_str = $tb_details['part_name'];
//		}else{
//			$rtn_str = $tb_details[$fields];
//		}
//	}
//
//   return $rtn_str;
//}
//}
//
/////// function to get validation from jobsheet data
//
//function getJobValidate($imei_sno,$contact_no,$brand_id,$link1){
//
//	if($contact_no){
//
//		$srch_criteria = "contact_no='".$contact_no."' or alternate_no='".$contact_no."'";
//
//	}else{
//
//		$srch_criteria = "imei='".$imei_sno."' or sec_imei='".$imei_sno."'";
//
//	}
//
//	///// check if any entry is found in JD
//
//	///// make array of job data
//
//	$jobno_arr = array();
//
//	$brand_arr = array();
//
//	$customer_arr = array();
//
//	$opendate_arr = array();
//
//	$closedate_arr = array();
//
//	$modelid_arr = array();
//
//	$model_arr = array();
//
//	$status_arr = array();
//
//	$dop_arr = array();
//
//	$activdate_arr = array();
//
//	$wsd_arr = array();
//
//	$firstimei_arr = array();
//
//	$secimei_arr = array();
//
//	$call_type = array();
//
//	$res_job = mysqli_query($link1,"SELECT job_no,brand_id,customer_name,open_date,imei,sec_imei,close_date,dop,activation,model_id,model,warranty_days,status,call_type FROM jobsheet_data where ".$srch_criteria." order by job_id desc");
//
//	while($row_job = mysqli_fetch_assoc($res_job)){
//
//		$jobno_arr[] = $row_job['job_no'];
//
//		$brand_arr[] = $row_job['brand_id'];
//
//		$customer_arr[] = $row_job['customer_name'];
//
//		$opendate_arr[] = $row_job['open_date'];
//
//		$closedate_arr[] = $row_job['close_date'];
//
//		$modelid_arr[] = $row_job['model_id'];
//
//		$model_arr[] = $row_job['model'];
//
//		$status_arr[] = $row_job['status'];
//
//		$dop_arr[] = $row_job['dop'];
//
//		$activdate_arr[] = $row_job['activation'];
//
//		$wsd_arr[] =  $row_job['warranty_days'];
//
//		$firstimei_arr[] = $row_job['imei'];
//
//		$secimei_arr[] = $row_job['sec_imei'];
//
//		$call_type[] = $row_job['call_type'];
//
//	}
//
//	if(count($status_arr) > 0){
//
//		/////check post brand id is matched with job brand id
//
//		if($brand_arr[0] == $brand_id){
//
//			////check if the latest job pucnh by same credentials and having status handover (statusid = 10) only then job will be re-create
//
//			if($status_arr[0] == 10 || $status_arr[0] == 11 || $status_arr[0] == 13){
//
//				///// job can be re-create
//
//				//return "Y~RepeatCall~".$jobno_arr."~".$customer_arr."~".$opendate_arr."~".$closedate_arr."~".$model_arr."~".$status_arr;
//				if($call_type[0]=="Replacement"){
//
//				$return_msg = "IMEI is already Replace with Job no. <strong>".$jobno_arr[0]."</strong>";
//
//								return array("R",$return_msg,$jobno_arr[0],$customer_arr,$opendate_arr,$closedate_arr,$modelid_arr,$model_arr,$status_arr,$dop_arr,$activdate_arr,$wsd_arr,$firstimei_arr,$secimei_arr);
//				} else {
//
//				return array("Y","RepeatCall",$jobno_arr,$customer_arr,$opendate_arr,$closedate_arr,$modelid_arr,$model_arr,$status_arr,$dop_arr,$activdate_arr,$wsd_arr,$firstimei_arr,$secimei_arr);
//
//				}
//
//			}else if($status_arr[0] == 12){
//
//				$return_msg = "No Job found in Job Data for these credentials.";
//
//				return array("NF",$return_msg,"","","","","","","","","","","");
//
//			}
//			else if($call_type[0]=='Replacement'){
//				$return_msg = "IMEI is already Replace with Job no. <strong>".$jobno_arr[0]."</strong>";
//
//				return array("R",$return_msg,$jobno_arr[0],"","","","","","","","","","");
//
//			}
//
//			else{
//
//				$return_msg = "Job is already in process with Job no. <strong>".$jobno_arr[0]."</strong>";
//
//				return array("N",$return_msg,$jobno_arr[0],"","","","","","","","","","");
//
//			}
//
//		}else{
//
//			$return_msg = "Brand is not matched with this IMEI/Serial No.";
//
//			return array("N",$return_msg,"","","","","","","","","","","");
//
//		}
//
//	}else{
//
//
//		$res_job_rep2 = mysqli_query($link1,"SELECT * FROM repair_detail  where replace_imei1='".$imei_sno."' or replace_imei2='".$imei_sno."'");
//		$rep_rows=mysqli_fetch_array($res_job_rep2);
//		if(mysqli_num_rows($res_job_rep2)>0){
//
//		$job_details = mysqli_fetch_assoc(mysqli_query($link1,"select dop,activation,warranty_days from jobsheet_data where job_no='".$rep_rows['job_no']."'"));
//	$job_model = mysqli_fetch_assoc(mysqli_query($link1,"select model from model_master where model_id='".$rep_rows['repl_model']."'"));
//		  //$rep_date = getAnyDetails($rep_rows['job_no'],"dop","job_no","jobsheet_data",$link1);
//
//			$modelid_arr[] = $rep_rows['repl_model'];
//
//			$firstimei_arr[] = $rep_rows['replace_imei1'];
//
//		$secimei_arr[] = $rep_rows['replace_imei2'];
//			$dop_arr[] = $job_details['dop'];
//			$activdate_arr[] = $job_details['activation'];
//				$model_arr[] = $job_model['model'];
//				$wsd_arr[] =  $job_details['warranty_days'];
//
//
//		return array("R" ,$return_msg,$jobno_arr[0],$customer_arr,$opendate_arr,$closedate_arr,$modelid_arr,$model_arr,$status_arr,$dop_arr,$activdate_arr,$wsd_arr,$firstimei_arr,$secimei_arr);
//
//		}
//else {
//		$return_msg = "No Job found in Job Data for these credentials.";
//
//		return array("NF",$return_msg,"","","","","","","","","","","");
//
//
//	}
//}
//}
/////// function to get validation from jobsheet data for complaint match/////////////////////////////
//function getcomplaintValidate($imei_sno,$contact_no,$custmerid,$link1){
//	if($contact_no){
//		$srch_criteria = "(contact_no='".$contact_no."' or  alternate_no ='".$contact_no."')";
//	}else if($imei_sno) {
//		$srch_criteria = "imei='".$imei_sno."'";
//	}else if($custmerid) {
//		$srch_criteria = "customer_id='".$custmerid."'";
//	}
//
//	///// check if any entry is found in JD
//	///// make array of job data
//	$jobno_arr = array();
//	$brand_arr = array();
//	$customer_arr = array();
//	$opendate_arr = array();
//	$closedate_arr = array();
//	$modelid_arr = array();
//	$model_arr = array();
//	$status_arr = array();
//	$dop_arr = array();
//	$imei_arr = array();
//	$wsd_arr = array();
//	$res_job = mysqli_query($link1,"SELECT job_no,brand_id,imei,customer_name,open_date,close_date,dop,activation,model_id,model,warranty_days,status FROM jobsheet_data where ".$srch_criteria." order by job_id desc");
//	while($row_job = mysqli_fetch_assoc($res_job)){
//		$jobno_arr[] = $row_job['job_no'];
//		$brand_arr[] = $row_job['brand_id'];
//		$customer_arr[] = $row_job['customer_name'];
//		$opendate_arr[] = $row_job['open_date'];
//		$closedate_arr[] = $row_job['close_date'];
//		$modelid_arr[] = $row_job['model_id'];
//		 $model_arr[] = htmlspecialchars($row_job['model'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
//		//print_r($model_arr);
//		$status_arr[] = $row_job['status'];
//		$dop_arr[] = $row_job['dop'];
//		$imei_arr[] = $row_job['imei'];
//		$wsd_arr[] =  $row_job['warranty_days'];
//
//	}
//	//var_dump($model_arr);
//	if(count($status_arr) > 0){
//		/////check post brand id is matched with job brand id
//
//			////check if the latest job pucnh by same credentials and having status handover (statusid = 10) only then job will be re-create
//			if($status_arr[0] == 10){
//				///// job can be re-create
//				//return "Y~RepeatCall~".$jobno_arr."~".$customer_arr."~".$opendate_arr."~".$closedate_arr."~".$model_arr."~".$status_arr;
//				return array("Y","RepeatCall",$jobno_arr,$customer_arr,$opendate_arr,$closedate_arr,$modelid_arr,$model_arr,$status_arr,$dop_arr,$imei_arr,$wsd_arr);
//			}else{
//
//				return array("Y","RepeatCall",$jobno_arr,$customer_arr,$opendate_arr,$closedate_arr,$modelid_arr,$model_arr,$status_arr,$dop_arr,$imei_arr,$wsd_arr);
//			}
//		}
//	else{
//		$return_msg = "No Job found in Job Data for these credentials.";
//		return array("NF",$return_msg,"","","","","","","","","");
//	}
//}
//
//
/////// function to get validation from product Register for complaint match/////////////////////////////
//function getcustomerValidate($imei_sno,$coustmerid,$link1){
//	if($imei_sno){
//		$srch_criteria = "serial_no='".$imei_sno."'";
//	}else{
//		$srch_criteria = "customer_id ='".$coustmerid."'";
//	}
//	///// check if any entry is found in JD
//	///// make array of job data
//	$id_arr = array();
//	$serial_arr = array();
//	$product_arr = array();
//	$customerid_arr = array();
//	$purdate_arr = array();
//	$instaledate_arr = array();
//	$warratydate_arr = array();
//	$modelid_arr = array();
//
//	$res_pro = mysqli_query($link1,"SELECT * FROM product_registered where ".$srch_criteria." order by id desc");
//	while($row_pro = mysqli_fetch_assoc($res_pro)){
//	    $id_arr[] = $row_pro['id'];
//		$serial_arr[] = $row_pro['serial_no'];
//		$product_arr[] = $row_pro['product_id'];
//		$customerid_arr[] = $row_pro['customer_id'];
//		$purdate_arr[] = $row_pro['purchase_date'];
//		$instaledate_arr[] = $row_pro['installation_date'];
//		$modelid_arr[] = $row_pro['model_id'];
//		$warratydate_arr[] = $row_pro['warranty_end_date'];
//
//	}
//	if(count($serial_arr) > 0){
//
//				return array("Y","success",$serial_arr,$product_arr,$customerid_arr,$purdate_arr,$modelid_arr,$instaledate_arr,$warratydate_arr, $id_arr);
//
//		}
//	else{
//		$return_msg = "No Product Found.";
//		return array("NF",$return_msg,"","","","","","","");
//	}
//}
/////// function to get validation from imei import data
//
//function getImeiImportValidate($imei_sno,$link1){
//
//	$res_import = mysqli_query($link1,"SELECT import_date, activation_date, refurb_date, model_id,imei1, imei2  from imei_data_import where imei1='".$imei_sno."' or imei2='".$imei_sno."' order by id desc ");
//
//	$count_import = mysqli_num_rows($res_import);
//
//	if($count_import > 0){
//
//		$row_import = mysqli_fetch_assoc($res_import);
//
//		return "Y~".$row_import['import_date']."~".$row_import['activation_date']."~".$row_import['refurb_date']."~".$row_import['model_id']."~".$row_import['imei1']."~".$row_import['imei2'];
//
//	}else{
//
//		$return_msg = "Not found in database";
//
//		return "N~".$return_msg;
//
//	}
//
//}
//
/////// function to get address //// developed by jitender
//
//function getLocationAddress($locid,$link1){
//
//   $user_details=mysqli_fetch_array(mysqli_query($link1,"select locationname,locationaddress,cityid,emailid,contactno1,contact_person,zipcode from location_master where location_code='$locid'"));
//
//    $user_city=mysqli_fetch_array(mysqli_query($link1,"select city from city_master where cityid='$user_details[cityid]'"));
//
//
//
//
//
//$rtn_str=$user_details['locationname']."</br>".$user_details['locationaddress'].",".$user_city['city'].",".$user_details['zipcode']."</br>".$user_details['emailid']."</br>".$user_details['contactno1']."</br>".$user_details['contact_person'];
//
//   return $rtn_str;
//
//}
//
/////// function to get address ////
//
//function getLocationDispAddress($locid,$link1){
//
//   $user_details=mysqli_fetch_array(mysqli_query($link1,"select locationname,locationaddress,cityid,emailid,contactno1,zipcode,cin from location_master where location_code='".$locid."'"));
//
//   $user_city=mysqli_fetch_array(mysqli_query($link1,"select city from city_master where cityid='".$user_details['cityid']."'"));
//
//$rtn_str=$user_details['locationname']."</br>".$user_details['locationaddress'].",<br/>City:".$user_city['city'].",<br/>Pincode:".$user_details['zipcode']."</br>".$user_details['emailid']."</br>".$user_details['contactno1'];
//
//   return $rtn_str;
//
//}
//
//////// function to get inventory
//
//function getInventory($locationcode,$partcode,$fields,$link1){
//
//	$explodee=explode(",",$fields);
//
//   	$user_details = mysqli_fetch_array(mysqli_query($link1,"select ".$fields." from client_inventory where location_code='".$locationcode."' and partcode='".$partcode."'"));
//
//   	$rtn_str="";
//
//   	for($k=0;$k < count($explodee);$k++){
//
//    	if($rtn_str==""){
//
//          $rtn_str.=$user_details[$k];
//
//	   	}
//
//       	else{
//
//          $rtn_str.="~".$user_details[$k];
//
//	   	}
//
//   	}
//
//   	return $rtn_str;
//
//}
//
//############ Number convert into words ##############
//
//function number_to_words($number){
//
//  if ($number > 999999999){
//
//      throw new Exception("Number is out of range");
//
//  }
//
//	$Cn = floor($number / 10000000); /* Crore () */
//
//	$number -= $Cn * 10000000;
//
//	//$Gn = floor($number / 1000000); /* Millions (giga) */
//
//	//$number -= $Gn * 1000000;
//
//	$ln = floor($number / 100000); /* Lakh () */
//
//	$number -= $ln * 100000;
//
//
//
//	$kn = floor($number / 1000); /* Thousands (kilo) */
//
//	$number -= $kn * 1000;
//
//	$Hn = floor($number / 100); /* Hundreds (hecto) */
//
//	$number -= $Hn * 100;
//
//	$Dn = floor($number / 10); /* Tens (deca) */
//
//	$n = $number % 10; /* Ones */
//
//	$cn = round(($number-floor($number))*100); /* Cents */
//
//	$result = "";
//
//    if ($Cn) { $result .= (empty($result) ? "" : " ") . number_to_words($Cn) . " Crore"; }
//
//    /*if ($Gn){ $result .= number_to_words($Gn) . " Million"; }*/
//
//    if ($ln){ $result .= (empty($result) ? "" : " ") . number_to_words($ln) . " Lakh"; }
//
//    if ($kn){ $result .= (empty($result) ? "" : " ") . number_to_words($kn) . " Thousand"; }
//
//    if ($Hn){ $result .= (empty($result) ? "" : " ") . number_to_words($Hn) . " Hundred"; }
//
//	$ones = array("", "One", "Two", "Three", "Four", "Five", "Six","Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen","Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen","Nineteen");
//
//	$tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty","Seventy", "Eigthy", "Ninety");
//
//
//
//    if ($Dn || $n){
//
//       if (!empty($result)){ $result .= " ";}
//
//       if ($Dn < 2){ $result .= $ones[$Dn * 10 + $n];}
//
//       else{
//
//	      $result .= $tens[$Dn];
//
//          if ($n){ $result .= "-" . $ones[$n];}
//
//	   }
//
//	}
//
//    if ($cn){
//
//       if (!empty($result)){ $result .= ' and ';}
//
//       $title = $cn==1 ? 'paisa': 'paise';
//
//       $result .= strtolower(number_to_words($cn)).' '.$title;
//
//	}
//
//    if (empty($result)){ $result = "zero"; }
//
//    return $result;
//
//}
//
//////////////////////////////////////////////// for color ///////////////// develop by
//
//function cellColor($cells,$color){
//
//    global $objPHPExcel;
//
//
//
//    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
//
//        'type' => PHPExcel_Style_Fill::FILL_SOLID,
//
//        'startcolor' => array(
//
//             'rgb' => $color
//
//        )
//
//    ));
//
//}
//
//////////////////////////////Kush///////////////
//
//function getAccBrand($gettingfor,$link1){
//
//	$arr_status = array();
//	$a=array();
//
//	if($gettingfor){ $used_in = " location_code = '".$gettingfor."'";}else{ $used_in = "1";}
////echo "select brand_id from access_brand where ".$used_in." group by brand_id ";
//	$result_set = mysqli_query($link1,"select brand_id from access_brand where ".$used_in." and status='Y'  group by brand_id ") or die(mysqli_error($link1));
//
//	while($row_set=mysqli_fetch_assoc($result_set)){
//
//		$arr_status[]=getAnyDetails($row_set['brand_id'],"brand","brand_id","brand_master",$link1);
//
//
//
//}
//	return array($arr_status);
//
//}
//
//function getAccPro($gettingfor,$link1){
//
//	$arr_status = array();
//	$a=array();
//
//	if($gettingfor){ $used_in = " location_code = '".$gettingfor."'";}else{ $used_in = "1";}
////echo "select brand_id from access_brand where ".$used_in." group by brand_id ";
//	$result_set = mysqli_query($link1,"select product_id from access_product where ".$used_in." and status='Y' group by product_id ") or die(mysqli_error($link1));
//
//	while($row_set=mysqli_fetch_assoc($result_set)){
//
//		$arr_status[]=getAnyDetails($row_set['product_id'],"product_name","product_id","product_master",$link1);
//
//
//
//}
//	return array($arr_status);
//
//}
////////// function to capture ticket history ///// develop by priya
//
//function ticketHistory($ticket_no,$remark,$updatedate,$ip,$priority,$link1,$errorflag){
//
//	$todayDate=date("Y-m-d");
//
//	$todayTime=date("H:i:s");
//
//	$flag=$errorflag;
//
//	$query="INSERT INTO ticket_history set ticket_no='".$ticket_no."', remark='".$remark."', update_date= '".$updatedate."' , ip='".$ip."' , priority = '".$priority."'   ";
//
//	$result=mysqli_query($link1,$query);
//
//	//// check if query is not executed
//
//    if (!$result) {
//
//	     $flag = false;
//
//         echo "Error details: " . mysqli_error($link1) . ".";
//
//	}
//
//	return $flag;
//
//}
//
//function filo_details_asp($location_code,$partcode,$qty, $link1){
//	$res_challan = mysqli_query($link1,"SELECT id,type,ref_sno,challan_no,partcode,price,sale_date,pty_receive_date,document_type,(okqty- fifi_ty) as qty FROM fifo_list where location_code='".$location_code."' and partcode='".$partcode."' and okqty != fifi_ty ") or die(mysqli_error());
//			$row_challan = mysqli_fetch_assoc($res_challan);
//
//			return 		$return_data = $row_challan['challan_no']."~".$row_challan['qty']."~".$row_challan['price']."~".$row_challan['partcode']."~".$row_challan['pty_receive_date']."~".$row_challan['ref_sno']."~".$row_challan['sale_date']."~".$row_challan['id']."~".$doc_type[$k];
//}
//
//
//
//function filo_details($location_code,$partcode,$qty, $link1){
//	///// check if any entry is found in JD
//	///// make array of challan data qty for fifo
//	$f_challan_no = array(); //// make array for challan no
//	$f_partcode = array();  //// make array for partcode
//	$f_qty = array();  		//// make array for qty
//	$f_price = array();  	//// make array for price
//	$f_date = array();
//	$r_date = array();
//	$r_type = array();
//	$r_old_ref = array();
//	$doc_type = array(); 	//// make array for date
//	$return_data= array();	//// make array final result for return data
//	/// check product item for deassinding order form billing product data
//	//echo ""SELECT id,challan_no,partcode,price,sale_date,(okqty-return_qty_fifo) as qty FROM billing_product_items where to_location='".$location_code."' and partcode='".$partcode."' and status='4' and  stock_type='".$stock_type."' and fifo_apply='Y' order by id desc";
//	$res_challan = mysqli_query($link1,"SELECT id,type,ref_sno,challan_no,partcode,price,sale_date,pty_receive_date,document_type,(okqty- fifi_ty) as qty FROM fifo_list where location_code='".$location_code."' and partcode='".$partcode."' order by id desc") or die(mysqli_error());
//			while($row_challan = mysqli_fetch_assoc($res_challan)){
//			if($row_challan['qty']>0){
//				 $f_challan_no[] = $row_challan['challan_no'];
//				 $f_partcode[] = $row_challan['partcode'];
//				 $f_qty[] = $row_challan['qty'];
//				 $f_price[] = $row_challan['price'];
//				 $f_date[] = $row_challan['sale_date'];
//				 $f_id[] = $row_challan['ref_sno'];
//				  $r_date[] = $row_challan['pty_receive_date'];
//				  $r_type []= $row_challan['type'];
//				  $r_old_ref[]=$row_challan['ref_sno'];
//				  $doc_type[]= $row_challan['document_type'];
//				 }
//			}
//			        /// count all challan for partcode and qty present in  billing product data
//					for($k=0; $k<count($f_challan_no) ; $k++){
//					           ////// asign value in remaning qty from billing product qty
//					           $rem_qty=$f_qty[$k];
//							        /////check post qty is less than present in remaing qty
//									if($rem_qty > $qty){
//										$return_data[] = $f_challan_no[$k]."~".$f_qty[$k]."~".$f_price[$k]."~".$f_partcode[$k]."~".$f_date[$k]."~".$f_id[$k]."~".$r_date[$k]."~".$r_type[$k]."~".$r_old_ref[$k]."~".$doc_type[$k];
//										break;
//									}
//									 /////check post qty is equal present in remaing qty
//									 else if($rem_qty == $qty){
//										$return_data[] = $f_challan_no[$k]."~".$f_qty[$k]."~".$f_price[$k]."~".$f_partcode[$k]."~".$f_date[$k]."~".$f_id[$k]."~".$r_date[$k]."~".$r_type[$k]."~".$r_old_ref[$k]."~".$doc_type[$k];
//										break;
//
//									}else{
//										$return_data[] = $f_challan_no[$k]."~".$f_qty[$k]."~".$f_price[$k]."~".$f_partcode[$k]."~".$f_date[$k]."~".$f_id[$k]."~".$r_date[$k]."~".$r_type[$k]."~".$r_old_ref[$k]."~".$doc_type[$k];
//										$qty = $qty - $rem_qty;
//									}
//
//					}
//					return $return_data;
//}
//
/////// written by shekhar on 19 feb 2019
//function getProcessStatus($sid,$link1){
//	$res = mysqli_query($link1,"select status_name from sf_process_status where id='".$sid."'");
//	$row = mysqli_fetch_assoc($res);
//	if($row['status_name']){
//		return $row['status_name'];
//	}else{
//		return $sid;
//	}
//}
/////// function to  fifo details by sonu start
//
//						function get_status($status_id,$link1)
// {
//	 $status=mysqli_query($link1,"select status_name from sf_status_master where id='".$status_id."'") or die(mysqli_error($link1));
//	 $srow=mysqli_fetch_assoc($status);
//	 $status_type=$srow['status_name'];
//	 return $status_type;
// }
//function againg_filo_details($location_code,$partcode,$qty,$stock_type, $link1){
//	///// check if any entry is found in JD
//	///// make array of challan data qty for fifo
//	$f_challan_no = array(); //// make array for challan no
//	$f_partcode = array();  //// make array for partcode
//	$f_qty = array();  		//// make array for qty
//	$f_price = array();  	//// make array for price
//	$f_date = array();
//	$r_date = array();  	//// make array for date
//	$return_data= array();	//// make array final result for return data
//	/// check product item for deassinding order form billing product data
//	//echo ""SELECT id,challan_no,partcode,price,sale_date,(okqty-return_qty_fifo) as qty FROM billing_product_items where to_location='".$location_code."' and partcode='".$partcode."' and status='4' and  stock_type='".$stock_type."' and fifo_apply='Y' order by id desc";
//	$res_challan = mysqli_query($link1,"SELECT id,challan_no,partcode,price,sale_date,pty_receive_date,okqty FROM billing_product_items where to_location='".$location_code."' and partcode='".$partcode."' and status='4' and  stock_type='".$stock_type."' and fifo_apply='Y' order by sale_date desc") or die(mysqli_error());
//			while($row_challan = mysqli_fetch_assoc($res_challan)){
//			if($row_challan['okqty']>0){
//				 $f_challan_no[] = $row_challan['challan_no'];
//				 $f_partcode[] = $row_challan['partcode'];
//				 $f_qty[] = $row_challan['okqty'];
//				 $f_price[] = $row_challan['price'];
//				 $f_date[] = $row_challan['sale_date'];
//				 $f_id[] = $row_challan['id'];
//				  $r_date[] = $row_challan['pty_receive_date'];
//				 }
//			}
//			        /// count all challan for partcode and qty present in  billing product data
//					for($k=0; $k<count($f_challan_no) ; $k++){
//					           ////// asign value in remaning qty from billing product qty
//					           $rem_qty=$f_qty[$k];
//							        /////check post qty is less than present in remaing qty
//									if($rem_qty > $qty){
//										$return_data[] = $f_challan_no[$k]."~".$f_qty[$k]."~".$f_price[$k]."~".$f_partcode[$k]."~".$f_date[$k]."~".$f_id[$k]."~".$r_date[$k];
//										break;
//									}
//									 /////check post qty is equal present in remaing qty
//									 else if($rem_qty == $qty){
//										$return_data[] = $f_challan_no[$k]."~".$f_qty[$k]."~".$f_price[$k]."~".$f_partcode[$k]."~".$f_date[$k]."~".$f_id[$k]."~".$r_date[$k];
//										break;
//
//									}else{
//										$return_data[] = $f_challan_no[$k]."~".$f_qty[$k]."~".$f_price[$k]."~".$f_partcode[$k]."~".$f_date[$k]."~".$f_id[$k]."~".$r_date[$k];
//										$qty = $qty - $rem_qty;
//									}
//
//					}
//					return $return_data;
//}
////////////// Access Verification of pages By shekhar (OCT 26, 2023) ////////////
//function is_access_allowed_v3($link1, $fun_id, $for, $user, $usertype)
//{
//	$resp = '';
//	if($for == "u")
//	{
//		$sql = "SELECT * FROM access_tab WHERE userid LIKE '".$user."' AND tabid LIKE '".$fun_id."' AND status LIKE '1'";
//	}
//	else
//	{
//		return $resp;
//	}
//	$res = mysqli_query($link1, $sql);
//	if($res)
//	{
//		if(mysqli_num_rows($res) > 0)
//		{
//			$resp = true;
//		}
//		else
//		{
//			$resp = false;
//		}
//	}
//	return $resp;
//}
//function access_check_v3($link1, $fun_arr, $userid='', $usertype)
//{
//	$resp = false;
//	foreach($fun_arr as $for => $fun_ids)
//	{
//        foreach($fun_ids as $fun_id)
//        {
//            $ac_ver = is_access_allowed_v3($link1, $fun_id, $for, $userid, $usertype);
//            if($ac_ver === true)
//            {
//                $resp = true;
//                break;
//            }
//        }
//	}
//	if($resp === false)
//	{
//		//echo '<div style="text-align:center;color:#ff4e4e;margin:60px 0px;"><h2>CAUTION!</h2><br>Our system has detected you are trying to do an unauthorised activity. Please don\'t do this again otherwise your id will be block for next 365 days.<br><br><span style="color:#756e6e;">User: '.$_SESSION["userid"].' | IP: '.$_SERVER['REMOTE_ADDR'].' | Activity Time: '.date("Y-m-d H:i:s").'</span></div>';
//		echo '<div style="text-align:center;color:#ff4e4e;margin:60px 0px;"><h2>CAUTION!</h2><br>Our system has detected you are trying to do an unauthorised activity. Please don\'t do this again. your id is deactivate now so please contact to administration to activate your id.<br><br><span style="color:#756e6e;">User: '.$_SESSION["userid"].' | IP: '.$_SERVER['REMOTE_ADDR'].' | Activity Time: '.date("Y-m-d H:i:s").'</span></div>';
//		$req_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//		$res_log = mysqli_query($link1, "INSERT INTO log_unauth_act SET userid = '".$_SESSION["userid"]."', url = '".$req_link."', datetime = '".date("Y-m-d H:i:s")."', ip='".$_SERVER['REMOTE_ADDR']."', browser = '".$_SERVER['HTTP_USER_AGENT']."'");
//		///// deactivate the id
//		if($_SESSION["stype"]=="1"){
//			$res_deact = mysqli_query($link1, "UPDATE admin_users SET status='2' WHERE username = '".$_SESSION["userid"]."'");
//		}else if($_SESSION["stype"]=="2"){
//			$res_deact = mysqli_query($link1, "UPDATE location_master SET statusid='2' WHERE location_code = '".$_SESSION["userid"]."'");
//		}else if($_SESSION["stype"]=="3"){
//			$res_deact = mysqli_query($link1, "UPDATE locationuser_master SET statusid='2' WHERE userloginid = '".$_SESSION["userid"]."'");
//		}else{
//		}
//		$query="INSERT INTO daily_activities set userid='".$_SESSION["userid"]."',ref_no='".$_SESSION["userid"]."',activity_type='Unauthorised Page Access',action_taken='Deactive',update_date='".date("Y-m-d")."',update_time='".date("H:i:s")."',system_ip='".$_SERVER['REMOTE_ADDR']."'";
//		$result=mysqli_query($link1,$query);
//		session_destroy();
//	}
//	return $resp;
//}
//
//#### Send SMS
//function sendSMSByURL($mobile_no,$msg,$templateid){
//	$curl = curl_init();
//	curl_setopt_array($curl, array(
//
//	CURLOPT_URL =>'https://api.voicensms.in/SMSAPI/webresources/CreateSMSCampaignGet?ukey=AmhARjaEoQqeSrlALCXfpplKl&msisdn='.$mobile_no.'&language=0&credittype=7&senderid=OKAYAE&templateid='.$templateid.'&message='.urlencode($msg).'&filetype=2',
//
//	  CURLOPT_RETURNTRANSFER => true,
//	  CURLOPT_ENCODING => '',
//	  CURLOPT_MAXREDIRS => 10,
//	  CURLOPT_TIMEOUT => 0,
//	  CURLOPT_FOLLOWLOCATION => true,
//	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//	  CURLOPT_CUSTOMREQUEST => 'GET',
//	));
//	$response = curl_exec($curl);
//	curl_close($curl);
//
//	return $response;
//}
//function sendSMSByURL1($mobile_no,$msg,$template_id){
////print_r($mobile_no);exit;
//							  $curl = curl_init();
//							  curl_setopt_array($curl, array(
//							  CURLOPT_URL => 'https://smsapi2.one97.net/BulkPush/api/sendsms',
//							  CURLOPT_RETURNTRANSFER => true,
//							  CURLOPT_ENCODING => '',
//							  CURLOPT_MAXREDIRS => 10,
//							  CURLOPT_TIMEOUT => 0,
//							  CURLOPT_FOLLOWLOCATION => true,
//							  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//							  CURLOPT_CUSTOMREQUEST => 'POST',
//							  //Using the CURLOPT_HTTPHEADER set the Content-Type to application/json
//							  CURLOPT_HTTPHEADER=> array('Content-Type:application/json'),
//							  CURLOPT_POSTFIELDS => json_encode(array('sender' => 'OKAYAG','receiver' => $mobile_no,'content' =>$msg,'ref_id' => '','pe_id' => '1701159118771676055','template_id' => $template_id,'msg_type' => 'TEXT','dlt_chain'=> '1701159118771676055,1402567490000015616')),
//							  CURLOPT_HTTPHEADER => array(
//								'Access-Token: 6f42c52373509a543d6744153c8b4be2c23819a70484d109d0f555754f489bad',
//								'Username: Okayapowertr',
//								'Request-Time: 2023-08-11 18:39:40',
//								'Content-Type: application/json'
//							  ),
//								));
//
//	$response = curl_exec($curl);
//
//	curl_close($curl);
//	echo $response;
//
//}
//function getRandomString($n){
//    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
//    $randomString = '';
//
//    for ($i = 0; $i < $n; $i++) {
//        $index = rand(0, strlen($characters) - 1);
//        $randomString .= $characters[$index];
//    }
//
//    return $randomString;
//}
//?>