<?php
/* Database connection start */
require_once("../includes/config.php");
$brandarray = getBrandArray($link1);
$productarray = getProductArray($link1);
/// get access product
$get_accproduct = getAccessProduct($_SESSION['asc_code'],$link1);
/// get access brand
$get_accbrand = getAccessBrand($_SESSION['asc_code'],$link1);

/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  product name
if($_REQUEST['product_name'] != ""){
	$productid = "b.product_id = '".$_REQUEST['product_name']."'";
}else{
	$productid = "b.product_id in (".$get_accproduct.")";
}
## selected  product name
if($_REQUEST['brand'] != ""){
	$brandid = "b.brand_id = '".$_REQUEST['brand']."'";
}else{
	$brandid = "b.brand_id in (".$get_accbrand.")";
}
## selected  location
if($_REQUEST['location_code'] != ""){
	$locationid = "a.location_code = '".$_REQUEST['location_code']."'";
}else{
	$locationid = "1";
}
## selected  model
if($_REQUEST['modelid'] != ""){
	$modelid = "b.model_id like '%".$_REQUEST['modelid']."%'";
}else{
	$modelid = "1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'a.id', 
	1 => 'a.partcode',
	2 => 'b.part_name',
	3 => 'b.product_id',
	4 => 'b.brand_id',
	5 => 'b.model',
	6 => 'a.list_qty',
	7 => 'a.list_price'

);
// getting total number records without any search
$sql = "SELECT a.*, b.part_name, b.product_id, b.brand_id, b.customer_price";
$sql.=" FROM client_inventory a, partcode_master b where a.list_qty > 0 and  ".$locationid." and a.partcode=b.partcode and ".$productid." and ".$brandid." and ".$modelid." group by a.partcode";
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("stock-grid-data-list.php: get stock details1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT a.*, b.part_name, b.product_id, b.brand_id, b.customer_price";
$sql.=" FROM client_inventory a, partcode_master b where a.list_qty >0 and  ".$locationid." and a.partcode=b.partcode and ".$productid." and ".$brandid." and ".$modelid." group by a.partcode";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (a.partcode LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR b.part_name LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("stock-grid-data-list.php: get stock details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("stock-grid-data-list.php: get stock details2");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
    
	$nestedData[] = $j; 
	$nestedData[] = $row["partcode"];
	$nestedData[] = $row["part_name"];
	$nestedData[] = $productarray[$row["product_id"]];
	$nestedData[] = $brandarray[$row["brand_id"]];
	$nestedData[] = "<div align='center'><a href='#' title='view mapped model' onClick=checkMappedModel('".$row["partcode"]."')><i class='fa fa-map-o fa-lg faicon' title='view mapped model'></i></a></div>";
	$nestedData[] = $row["list_qty"];
	$nestedData[] = $row["list_price"];
	
			
	
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
