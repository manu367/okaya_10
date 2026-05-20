<?php
/* Database connection start */
require_once("../includes/config.php");
$productarray = getProductArray($link1);
$brandarray = getBrandArray($link1);
/// get access product
$access_brand = getAccessBrand($_SESSION['userid'],$link1);
///////get access state
$arrstate = getAccessState($_SESSION['userid'],$link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  state
if($_REQUEST['statename'] != ""){
	$stt_id = " stateid='".$_REQUEST['statename']."'";
}else{
	$stt_id = $arrstate;
}
## selected  state
## selected  location
if($_REQUEST['location_code'] != ""){
	$locationid = " a.location_code='".$_REQUEST['location_code']."'";
}else{
	$locationid = "a.location_code in (select location_code from location_master where stateid in(".$stt_id." ))";
}
function engStockDetails($location,$part,$type,$link1){
	//echo "SELECT sum($type) as a  user_inventory where location_code='".$location."' and partcode='".$part."' group by partcode";
 $intransitd=mysqli_query($link1,"SELECT sum($type) as a  from user_inventory where location_code='".$location."' and partcode='".$part."' group by partcode");
				$intransit_data=mysqli_fetch_array($intransitd);
if($intransit_data['a']!=''){  return $intransit_data['a'];} else {   return 0;}
}
if($_REQUEST['product_name'] != ""){
	$productid = "b.product_id = '".$_REQUEST['product_name']."'";
}else{
	$productid = "1";
}

if($_REQUEST['brand'] != ""){
	$brandid = "b.brand_id = '".$_REQUEST['brand']."'";
}else{
	$brandid ="b.brand_id in (".$access_brand.")";
}

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
	6 => 'b.customer_price',
	7 => 'a.mount_qty',
	8 => 'a.okqty',
	9 => 'a.faulty',
	10 => 'a.missing',
	11 => 'a.msl_qty',
	12 => 'a.in_transit',
	13 => 'a.repl_qty'
);
// getting total number records without any search
$sql = "SELECT a.*, b.part_name, b.product_id, b.brand_id, b.customer_price,b.customer_partcode";
$sql.=" FROM client_inventory a, partcode_master b where ".$locationid ." and a.partcode=b.partcode and ".$productid." and ".$brandid." and ".$modelid." group by a.partcode";
//echo $sql;
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("stock-grid-data_admin.php: get stock details1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
$sql = "SELECT a.*, b.part_name, b.product_id, b.brand_id, b.customer_price,b.customer_partcode";
$sql.=" FROM client_inventory a, partcode_master b where ".$locationid ." and a.partcode=b.partcode and ".$productid." and ".$brandid." and ".$modelid;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (a.partcode LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR a.location_code LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR b.part_name LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR b.customer_partcode LIKE '".$requestData['search']['value']."%' )";
}
$sql.=" group by a.partcode";
$query=mysqli_query($link1, $sql) or die("stock-grid-data_admin.php: get stock details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."  ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
//echo $sql;
$query=mysqli_query($link1, $sql) or die("stock-grid-data_admin.php: get stock details2");
$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
	$part_n="";
	if($row["customer_partcode"]!=''){
		$part_n=$row["part_name"]."(".$row["customer_partcode"].")";
	}else{
		$part_n=$row["part_name"];
	}
	$nestedData[] = $j; 
	$nestedData[] = $row["partcode"];
	$nestedData[] = $part_n;
	$nestedData[] = $productarray[$row["product_id"]];
	$nestedData[] = $brandarray[$row["brand_id"]];
	$nestedData[] = "<div align='center'><a href='#' title='view mapped model' onClick=checkMappedModel('".$row["partcode"]."')><i class='fa fa-map-o fa-lg faicon' title='view mapped model'></i></a></div>";
	$nestedData[] = $row["customer_price"];
	$nestedData[] = $row["mount_qty"];
	$nestedData[] = engStockDetails($row['location_code'],$row['partcode'],"okqty",$link1);
	$nestedData[] = engStockDetails($row['location_code'],$row['partcode'],"faulty",$link1);
	$nestedData[] = $row["okqty"];
	$nestedData[] = $row["faulty"];
	$nestedData[] = $row["missing"];
	$nestedData[] = $row["msl_qty"];
	$nestedData[] = $row["in_transit"];
	$nestedData[] = $row["repl_qty"];
	$nestedData[] = getAnyDetails($row['location_code'],"locationname","location_code","location_master",$link1);
	
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