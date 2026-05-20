<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
$arrstatus = getFullStatus("master",$link1);
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
$columns = array( 
// datatable column index  => database column name
	0 => 'id', 
	1 => 'brand_id',
	2 => 'product_id',
	3 => 'level_type',
	4 => 'level',
	5 => 'level_value',
	6 => 'party',
	7 => 'status'	
);

// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM claim_master where 1";
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("claim-master-grid-data.php: get state master");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM claim_master where 1";
if( !empty($requestData['search']['value']) ) { 
// if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( level_type LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR level LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("claim-master-grid-data.php: get state master");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("claim-master-grid-data.php: get state master");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
     
	$nestedData[] = $j; 
	$nestedData[] = getAnyDetails($row["product_id"],"product_name","product_id","product_master",$link1);
	$nestedData[] = getAnyDetails($row["brand_id"],"brand","brand_id","brand_master",$link1);
	$nestedData[] = getAnyDetails($row["level"],"name","id","repair_level",$link1);
	$nestedData[] = getAnyDetails($row["level_type"],"displayname","locationtypeid","location_type_master",$link1);
	$nestedData[] = $row["level_value"];
	$nestedData[] = $row["party"];
	$nestedData[] = $arrstatus[$row["status"]];
	$nestedData[] = "<div align='center'><a href='add_edit_claim_master.php?op=Edit&id=".$row['id']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit state details'></i></a></div>";
	
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
