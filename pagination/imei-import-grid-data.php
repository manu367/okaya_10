<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
$columns = array( 
// datatable column index  => database column name
	0 => 'imei1', 
	1 => 'imei2', 
	2 => 'msg',
	3 => 'contact_no',
	4 => 'model',
	5=>'sale_location'l
	
);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM imei_data_auto where sale_date  >= '".$_REQUEST[start_date]."' and sale_date  <= '".$_REQUEST[end_date]."' ";
$query=mysqli_query($link1, $sql) or die("imei-detail-data.php: get party details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM imei_data_auto where sale_date  >= '".$_REQUEST[start_date]."' and entry_date  <= '".$_REQUEST[end_date]."' ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (imei1 LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR imei2 LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR contact_no LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR model LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR sale_location LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("imei-detail-data.php: get party details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("imei-detail-data.php: get party details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) { 
 // preparing an array
	$nestedData=array();    
	$nestedData[] = $j; 
	$nestedData[] = $row["imei1"];
	$nestedData[] = $row["imei2"];
	$nestedData[] = $row["msg"];
	$nestedData[] = $row["contact_no"];
	$nestedData[] = $row["model"];
	$nestedData[] = $row["sale_location"];
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
