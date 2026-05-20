<?php
/* Database connection start */
require_once("../includes/config.php");

/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
	0 => 'id', 
	1 => 'customer_id',
	2 => 'customer_name',
	3 => 'address1',
	4 => 'stateid',
	5 => 'cityid',
	6 => 'email',
	7 => 'mobile'
);

// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM customer_master where b_cust_id = 'Y' ";
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("blacklistcust-grid-data.php: get categorymaster master");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM customer_master where b_cust_id = 'Y' ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( customer_id LIKE '".$requestData['search']['value']."%'";  
	$sql.=" OR customer_name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR email LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR mobile LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR address1 LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("blacklistcust-grid-data.php: get categorymaster master");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("blacklistcust-grid-data.php: get categorymaster master");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array();  

	$nestedData[] = $j; 
	$nestedData[] = $row["customer_id"];
	$nestedData[] = $row["customer_name"];
	$nestedData[] = $row["address1"];
	$nestedData[] = getAnyDetails($row["stateid"],"state","stateid","state_master",$link1);
	$nestedData[] = getAnyDetails($row["cityid"],"city","cityid","city_master",$link1);
	$nestedData[] = $row["email"];
	$nestedData[] = $row["mobile"];
	
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
