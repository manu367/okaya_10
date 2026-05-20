<?php
/* Database connection start */
require_once("../includes/config.php");
/////get status//
$arrstatus = getFullStatus("master",$link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Status
if($_REQUEST['status']!=""){
	$status="status='".$_REQUEST['status']."'";
}else{
	$status="1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'id', 
	1 => 'courier_id',
	2 => 'name',
	3 => 'Contact_person',
	4 => 'city',
	5 => 'state',
	6 => 'addrs',
	7 => 'phone',
	8 => 'email',
	9 => 'status'
);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM courier_master where ".$status."";
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("courier-grid-data.php: get courier details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM courier_master where ".$status."";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( courier_id LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR name LIKE '".$requestData['search']['value']."%'";
    $sql.=" OR phone LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR email LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("courier-grid-data.php: get courier details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("courier-grid-data.php: get courier details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
    
	$nestedData[] = $j; 
	$nestedData[] = $row["courier_id"];
	$nestedData[] = $row["name"];
	$nestedData[] = $row["Contact_person"];
	$nestedData[] = $row["addrs"];

	$nestedData[] = getAnyDetails($row["city"],"city","cityid","city_master",$link1);
	$nestedData[] = getAnyDetails($row["state"],"state","stateid","state_master",$link1);
	$nestedData[] = $row["phone"];
	$nestedData[] = $row["email"];
	$nestedData[] = $arrstatus[$row["status"]];
	$nestedData[] = "<div align='center'><a href='add_courier.php?op=Edit&id=".base64_encode($row['courier_id'])."&status=".$_REQUEST['status']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit Courier details'></i></a></div>";
	
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
