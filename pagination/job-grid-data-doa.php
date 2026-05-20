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
if($_REQUEST['location_code'] != ""){
	$locationid = "location_code = '".$_REQUEST['location_code']."'";
}else{
	$locationid = "1";
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
	$status="status='9' and sub_status in (".$statusstr.")";
}else{
	$status="status='9'";
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
	9 => 'status'
);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM jobsheet_data where ".$locationid." and ".$status." and ".$daterange." and ".$productid." and ".$brandid." and ".$modelid;
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("Err1".mysqli_error($link1));
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT *";
$sql.=" FROM jobsheet_data where ".$locationid." and ".$status." and ".$daterange." and ".$productid." and ".$brandid." and ".$modelid;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (job_no LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR imei LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR model LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR open_date LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR customer_name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("Err2".mysqli_error($link1));
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("Err3".mysqli_error($link1));

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array

if($row["status"]=="9" && $row["sub_status"]=="91"){
	$viewd="<div align='center'><a href='doa-approval.php?refid=".base64_encode($row['job_no'])."&daterange=".$_REQUEST['daterange']."&product_name=".$_REQUEST['product_name']."&brand=".$_REQUEST['brand']."&modelid=".$_REQUEST['modelid']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view DOA details'></i></a></div>";
}else{
	$viewd="";
}

	$nestedData=array();	
	$nestedData[] = $j; 
	$nestedData[] = $row["job_no"];
	$nestedData[] = $row["imei"];
	$nestedData[] = getAnyDetails($row["product_id"],"product_name","product_id","product_master",$link1);
	$nestedData[] = getAnyDetails($row["brand_id"],"brand","brand_id","brand_master",$link1);
	$nestedData[] = $row["model"];
	$nestedData[] = $row["open_date"];
	$nestedData[] = $row["close_date"];
	$nestedData[] = $row["customer_name"];
	$nestedData[] = $arrstatus[$row["sub_status"]][$row["status"]];
	$nestedData[] = $viewd;
	
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
