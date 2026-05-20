<?php
/* Database connection start */
require_once("../includes/config.php");
/////get status//
$arrstatus = getJobStatus($link1);
////get access product details
$access_product = getAccessProduct($_SESSION['asc_code'],$link1);
////get access brand details
$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Date range
$date_range = explode(" - ",$_REQUEST['daterange']);
if($_REQUEST['daterange'] != ""){
	$daterange = " ( open_date >= '".$date_range[0]."' and open_date <= '".$date_range[1]."' )";
}else{
	$daterange = "1";
}
## selected  product name
if($_REQUEST['product_name'] != ""){
	$productid = "product_id in ('".$_REQUEST['product_name']."') ";
}else{
	$productid = "product_id in ($access_product) ";
}

$prd_maped_qr = mysqli_query($link1, "select mapped_brand from product_master where $productid ");
while($prd_maped_brd = mysqli_fetch_array($prd_maped_qr)){
	$arr .= $prd_maped_brd['mapped_brand'].",";
}
$arr_new = rtrim($arr, ',');
$a = explode(",", $arr_new);
$b = implode("','", $a);

## selected  product name
if($_REQUEST['brand'] != ""){
	$brandid = "brand_id in ('".$_REQUEST['brand']."') ";
}else{
	$brandid = "brand_id in ($access_brand) ";
}

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
	$status=" status in ('10' ) and hc_feedback='0'";
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
$sql.=" FROM jobsheet_data where location_code ='".$_SESSION['asc_code']."' and ".$status." and ".$daterange." and ".$productid." and ".$brandid." and brand_id in ('".$b."') and ".$modelid;

//echo $sql;

$query=mysqli_query($link1, $sql) or die("job-grid-data.php: get job details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM jobsheet_data where   location_code ='".$_SESSION['asc_code']."'   and ".$status." and ".$daterange."  and ".$productid." and ".$brandid." and brand_id in ('".$b."') and ".$modelid;

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

//echo $sql;

$query=mysqli_query($link1, $sql) or die("job-grid-data.php: get job details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 

	////// display print icon for DOA
	if(($row["status"]=="9" && $row["sub_status"]=="94") && $row["doa_count"]=="0"){
		$print_icon_cust = "<div align='center'><a href='doa_print.php?refid=".base64_encode($row['job_no'])."' target='_blank' title='take doa print of this jobsheet'><i class='fa fa-print fa-lg faicon' title='take doanprint of this jobsheet' ></i></a></div>";
		$print_icon_loc = "";
		$print_icon_estimate = "";
	}else{
	////// display print icon for cutomer
	$print_icon_cust = "<div align='center'><a href='job_print_customer.php?refid=".base64_encode($row['job_no'])."' target='_blank' title='take print of this jobsheet'><i class='fa fa-print fa-lg faicon' title='take print of this jobsheet' ></i></a></div>";
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
