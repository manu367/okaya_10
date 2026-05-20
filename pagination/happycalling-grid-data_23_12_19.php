<?php
/* Database connection start */
require_once("../includes/config.php");
/////get status//
$arrstatus = getJobStatus($link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Date range
$date_range = explode(" - ",$_REQUEST['daterange']);
if($_REQUEST['daterange'] != ""){
	$daterange = "open_date >= '".$date_range[0]."' and open_date <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}
## selected  product name
if($_REQUEST['product_name'] != ""){
	$productid = "product_id = '".$_REQUEST['product_name']."'";
}else{
	$productid = "1";
}
## selected  product name
if($_REQUEST['brand'] != ""){
	$brandid = "brand_id = '".$_REQUEST['brand']."'";
}else{
	$brandid = "1";
}
## selected  location
/*if($_REQUEST['location_code'] != ""){
	$locationid = "location_code = '".$_REQUEST['location_code']."'";
}else{
	$locationid = "location_code = '".$_REQUEST['location_code']."'";
}*/
## selected  model
if($_REQUEST['modelid'] != ""){
	$modelid = "model_id = '".$_REQUEST['modelid']."'";
}else{
	$modelid = "1";
}
## selected  Status
if(is_array($_REQUEST['info'])){
	$statusstr="";
	$post_statusarr = $_REQUEST['info'];
	for($i=0; $i<count($post_statusarr); $i++){
		if($statusstr){
			$statusstr .= ",'".$post_statusarr[$i]."'";
		}else{
			$statusstr .= "'".$post_statusarr[$i]."'";
		}
	}
	$status=" status in (".$statusstr.") and hc_feedback='0'";
}else{
	$status=" status = '6' and hc_feedback='0'";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'job_id', 
	1 => 'job_no',
	2 => 'imei',
    3 => 'model',
	4 => 'open_date',
	5 => 'close_date',
	6 => 'customer_name',
	7 => 'status'
);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM jobsheet_data where   location_code ='".$_SESSION['asc_code']."'   and ".$status." and ".$daterange." and ".$productid." and ".$brandid." and ".$modelid;
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("job-grid-data.php: get job details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM jobsheet_data where   location_code ='".$_SESSION['asc_code']."'   and ".$status."  and ".$productid." and ".$brandid." and ".$modelid;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (job_no LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR imei LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR model LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR open_date LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR customer_name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("job-grid-data.php: get job details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("job-grid-data.php: get job details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
    ////// display repair icon in case of open/pna/assign only
/*	if($row["status"]==1 || $row["status"]==2 || $row["status"]==3 || $row["status"]==7 || $row["status"]==5 || $row["sub_status"]=="82" ||  $row["sub_status"]=="83"  ){
		$repair_icon = "<div align='center'><a href='job_repair.php?refid=".base64_encode($row['job_no'])."&daterange=".$_REQUEST['daterange']."&product_name=".$_REQUEST['product_name']."&brand=".$_REQUEST['brand']."&modelid=".$_REQUEST['modelid']."".$pagenav."' title='go to repair'><i class='fa fa-wrench fa-lg faicon' title='go to repair'></i></a></div>";
	}else{
		///// if job status is repair done (6)
		if($row["status"]=="6" || ($row["status"]=="8" && $row["sub_status"]=="8")  ){
			if(($row["warranty_status"]=="OUT" || $row["warranty_status"]=="VOID") && $row['outws_inv']==""){
				$repair_icon = "<span class='alert-danger'>Invoice Pending</span>";	
			}else{
			$repair_icon = "<div align='center'><a href='job_handover.php?refid=".base64_encode($row['job_no'])."&daterange=".$_REQUEST['daterange']."&product_name=".$_REQUEST['product_name']."&brand=".$_REQUEST['brand']."&modelid=".$_REQUEST['modelid']."".$pagenav."' title='go to handover'><i class='fa fa-handshake-o fa-lg faicon' title='go to handover'></i></a></div>";
			}
		}else if($row["status"]=="9" && $row["sub_status"]=="92"){
			$repair_icon = "<div align='center'><a href='doa_handover.php?refid=".base64_encode($row['job_no'])."&daterange=".$_REQUEST['daterange']."&product_name=".$_REQUEST['product_name']."&brand=".$_REQUEST['brand']."&modelid=".$_REQUEST['modelid']."".$pagenav."' title='go to handover'><i class='fa fa-handshake-o fa-lg faicon' title='go to handover'></i></a></div>";
		}else{
			$repair_icon = "";
		}
	}*/
	////// display print icon for DOA
	if(($row["status"]=="9" && $row["sub_status"]=="94") && $row["doa_count"]=="0"){
		$print_icon_cust = "<div align='center'><a href='doa_print.php?refid=".base64_encode($row['job_no'])."' target='_blank' title='take doa print of this jobsheet'><i class='fa fa-print fa-lg faicon' title='take doanprint of this jobsheet' ></i></a></div>";
		$print_icon_loc = "";
		$print_icon_estimate = "";
	}else{
	////// display print icon for cutomer
	$print_icon_cust = "<div align='center'><a href='job_print_customer.php?refid=".base64_encode($row['job_no'])."' target='_blank' title='take print of this jobsheet'><i class='fa fa-print fa-lg faicon' title='take print of this jobsheet' ></i></a></div>";
	////// display print icon for location
	//$print_icon_loc = "<div align='center'><a href='job_print_location.php?refid=".base64_encode($row['job_no'])."' target='_blank' title='take print of this jobsheet for location'><i class='fa fa-print fa-lg faicon' title='take print of this jobsheet for location'></i></a></div>";
	//////////////// display estimate print  details //////////
	if($row["status"]=="5"){
	$print_icon_estimate = "<div align='center'><a href='job_print_estimate.php?refid=".base64_encode($row['job_no'])."' target='_blank' title='take print of estimate details'><i class='fa fa-print fa-lg faicon' title='take print of estimate details'></i></a></div>";
	}
	else{
		$print_icon_estimate ="";
	}
	}
	$nestedData[] = $j; 
	$nestedData[] = $row["job_no"];
	$nestedData[] = $row["customer_id"];
	$nestedData[] = $row["customer_name"];
	$nestedData[] = $row["contact_no"];
	$nestedData[] = getAnyDetails($row["brand_id"],"brand","brand_id","brand_master",$link1);
	$nestedData[] = getAnyDetails($row["product_id"],"product_name","product_id","product_master",$link1);
	
	$nestedData[] = $row["model"];
	$nestedData[] = $row["area_type"];
	$nestedData[] = $row["imei"];
	$nestedData[] = $row["call_for"];

	$nestedData[] = $row["open_date"];
	$nestedData[] = $row["close_date"];
	
	if($arrstatus[$row["sub_status"]][$row["status"]]){
		$nestedData[] = $arrstatus[$row["sub_status"]][$row["status"]];
	}else{
		$nestedData[] = getAnyDetails($row["status"],"display_status","status_id","jobstatus_master",$link1);
	}
	$nestedData[] =  "<div align='center'><a href='happy_calling_action.php?refid=".base64_encode($row['job_no'])."&daterange=".$_REQUEST['daterange']."&product_name=".$_REQUEST['product_name']."&brand=".$_REQUEST['brand']."&modelid=".$_REQUEST['modelid']."".$pagenav."' title='view'><i class='fa fa-volume-control-phone fa-lg faicon' title='happy calling'></i></a></div>";
	$nestedData[] = "<div align='center'><a href='happy_complaint_view.php?refid=".base64_encode($row['job_no'])."&daterange=".$_REQUEST['daterange']."&product_name=".$_REQUEST['product_name']."&brand=".$_REQUEST['brand']."&modelid=".$_REQUEST['modelid']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view complaint details'></i></a></div>";
	
	$data[] = $nestedData;
	$j++;
}



$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
			);

echo json_encode($json_data);  // send data as json format
?>
