<?php
/* Database connection start */
require_once("../includes/config.php");
/////get status//
$arrstatus = getJobStatus($link1);
////// get access products
$access_product = getAccessProduct($_SESSION['asc_code'],$link1);
////get access brand details
$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);

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

## selected brand
if($_REQUEST['product_name']!=""){
	$product_id = " product_id in ('".$_REQUEST['product_name']."') ";
}else{
	$product_id = " product_id in ($access_product) ";
}

$prd_maped_qr = mysqli_query($link1, "select mapped_brand from product_master where $product_id ");
while($prd_maped_brd = mysqli_fetch_array($prd_maped_qr)){
	$arr .= $prd_maped_brd['mapped_brand'].",";
}
$arr_new = rtrim($arr, ',');
$a = explode(",", $arr_new);
$b = implode("','", $a);

## selected  product name
if($_REQUEST['brand'] != ""){
	$brand_id = "brand_id = '".$_REQUEST['brand']."' and brand_id in ('".$b."') ";
}else{
	$brand_id = "brand_id in ($access_brand) and brand_id in ('".$b."') ";
}

## selected  model
if($_REQUEST['modelid']!=""){
	$model_id=" model_id  = '".$_REQUEST['modelid']."' ";
}else{
	$model_id=" 1";
}

$columns = array( 
// datatable column index  => database column name
	0 => 'job_id', 
	1 => 'job_no',
	2 => 'imei',
	3 => 'product_id',
	4 => 'brand_id',
	5 => 'model',
	6 => 'open_date',
	7 => 'close_date',
	8 => 'customer_name',
	9 => 'status',
	9 => 'area_type'
);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM jobsheet_data where location_code ='".$_SESSION['asc_code']."' and ".$product_id." and ".$brand_id." and " .$model_id;
//echo $sql;
$query=mysqli_query($link1, $sql) or die("job-grid-data.php: get job details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

   // if there is a search parameter, $requestData['search']['value'] contains search parameter
$sql = "SELECT *";
$sql.=" FROM jobsheet_data where location_code ='".$_SESSION['asc_code']."'  and ".$product_id." and  ".$brand_id." and ".$model_id;
if( !empty($requestData['search']['value']) ) {
	$sql.=" AND (job_no LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR imei LIKE '".$requestData['search']['value']."%'";
    //$sql.=" OR customer_name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR contact_no LIKE '".$requestData['search']['value']."%')";
	//$sql.=" OR b_cust_id LIKE '".$requestData['search']['value']."%'";
	//$sql.=" OR ticket_no LIKE '".$requestData['search']['value']."%'";
	//$sql.=" OR customer_id LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("job-grid-data.php: get job details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

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
	
	$nestedData[] = getAnyDetails($row["model_id"],"model","model_id","model_master",$link1);
	$nestedData[] = $row["area_type"];
	$nestedData[] = $row["imei"];
	$nestedData[] = $row["call_for"];

	$nestedData[] = dt_format($row["open_date"]);
	$nestedData[] =dt_format($row["close_date"]);

	if($arrstatus[$row["sub_status"]][$row["status"]]){
		$nestedData[] = $arrstatus[$row["sub_status"]][$row["status"]];
	}else{
		$nestedData[] = getAnyDetails($row["status"],"display_status","status_id","jobstatus_master",$link1);
	}
	$nestedData[] = "<div style='display:inline-block;float:left'>".$print_icon_cust."</div><div style='display:inline-block;float:right'>".$print_icon_loc."</div><div style='display:inline-block;float:left'>".$print_icon_estimate."</div>";
	$nestedData[] =  getAnyDetails($row["current_location"],"locationname","location_code","location_master",$link1);;
	$nestedData[] = "<div align='center'><a href='complaint_view.php?refid=".base64_encode($row['job_no'])."&daterange=".$_REQUEST['daterange']."&product_name=".$_REQUEST['product_name']."&brand=".$_REQUEST['brand']."&modelid=".$_REQUEST['modelid']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view job details'></i></a></div>";
	
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
