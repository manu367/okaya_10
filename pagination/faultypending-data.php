<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Status
if($_REQUEST['locationcode']!=""){
	$loc="from_location='".$_REQUEST['locationcode']."' ";
}else{
	$loc="1";
}
if($_REQUEST['status']!=""){
	$status="status='".$_REQUEST['status']."' ";
}else{
	$status="1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'from_location', 
	1 => 'job_no',
	2 => 'imei',
	3 => 'model_id',
	4 => 'partcode'
);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM part_to_credit where status ='1' and $loc   ";
$query=mysqli_query($link1, $sql) or die("faultypending-data.php: get  details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM part_to_credit where status ='1' and $loc   ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( job_no LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR imei LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("faultypending-data.php: get  details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("faultypending-data.php: get  details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
    
	$nestedData[] = $j; 
	$nestedData[] =getAnyDetails($row["from_location"],"locationname","location_code","location_master",$link1);
	$nestedData[] = $row["job_no"];
	$nestedData[] = $row["imei"];
	$nestedData[] = getAnyDetails($row["model_id"],"model","model_id","model_master",$link1);
	$nestedData[] = getAnyDetails($row["partcode"],"part_name","partcode","partcode_master",$link1);	
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
