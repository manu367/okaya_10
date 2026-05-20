<?php
/* Database connection start */
require_once("../includes/config.php");
/////get status//

/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Date range
$date_range = explode(" - ",$_REQUEST['daterange']);
if($_REQUEST['daterange'] != ""){
	$daterange = "purchase_date >= '".$date_range[0]."' and purchase_date <= '".$date_range[1]."'";
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

$columns = array( 
// datatable column index  => database column name
	0 => 'sno', 
	1 => 'amcid',
	2 => 'serial_no',
	3 => 'product_id',
	4 => 'brand_id',
	5 => 'model_id',
	6 => 'amc_start_date',
	7 => 'amc_end_date',
	8 => 'customer_name',
	9 => 'contract_no'
);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM amc where  	location_code ='".$_SESSION['asc_code']."'  and ".$daterange." and ".$productid." and ".$brandid." and ".$modelid;
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("amct-grid-data.php: get job details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM amc where  	location_code ='".$_SESSION['asc_code']."' and ".$daterange." and ".$productid." and ".$brandid." and ".$modelid;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (amcid LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR contract_no LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR serial_no LIKE '".$requestData['search']['value']."%'";
     $sql.=" OR customer_name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("amct-grid-data.php: get amc details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("amct-grid-data.php: get amc details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
 	if($row["quotetype"]=='Y'){
	$amcapprove = "<div align='center'><a href='amc_approve_only.php?refid=".base64_encode($row['amcid'])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view amc details'></i></a></div>";
	}else{
	$amcapprove = "";
	}

	$nestedData[] = $j; 
	$nestedData[] = $row["amcid"];
	$nestedData[] = $row["serial_no"];
	$nestedData[] = getAnyDetails($row["product_id"],"product_name","product_id","product_master",$link1);
	$nestedData[] = getAnyDetails($row["brand_id"],"brand","brand_id","brand_master",$link1);
	$nestedData[] = getAnyDetails($row["model_id"],"model","model_id","model_master",$link1);
	$nestedData[] = dt_format($row['amc_start_date']);
	$nestedData[] = dt_format($row['amc_end_date']);
	$nestedData[] = $row["customer_name"];
		$nestedData[] = $row["contract_no"];

	$nestedData[] = "<div align='center'><a href='amc_print.php?refid=".base64_encode($row['amcid'])."' target='_blank' title='take doa print of this jobsheet'><i class='fa fa-print fa-lg faicon' title='take doanprint of this jobsheet' ></i></a></div>";
	$nestedData[] = "<div align='center'><a href='amc_edit.php?refid=".base64_encode($row['amcid'])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view amc details'></i></a></div>";
	$nestedData[] = $amcapprove;
	$nestedData[] = "<div align='center'><a href='amc_view_only.php?refid=".base64_encode($row['amcid'])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view amc details'></i></a></div>";
	
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
