<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
$today1=date('Y-m-d');
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Date range
$date_range = explode(" - ",$_REQUEST['daterange']);
if($_REQUEST['daterange'] != ""){
	$daterange = "requestdate  >= '".$date_range[0]."' and requestdate  <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}
## selected  status 

## selected  location
if($_REQUEST['state'] != ""){
	$locationstate = "asc_state = '".$_REQUEST['state']."'";
}else{
	$locationstate = "1";
}
## selected  document type
if($_REQUEST['city'] != ""){
	$city = "asc_city = '".$_REQUEST['city']."'";
}else{
	$city = "1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'request_no', 
	1 => 'requestdate',
	2 => 'asc_state',
	3 => 'status'
);
// getting total number records without any search
$sql = "SELECT sno,request_no,requestdate,status,state,city,name";
$sql.=" FROM asc_appo_request where    ".$locationstate." and ".$city." and ".$daterange." ";
$query=mysqli_query($link1, $sql) or die("Er1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql =  "SELECT sno,request_no,requestdate,status,city,state,name";
$sql.=" FROM asc_appo_request where   ".$locationstate." and ".$city." and ".$daterange." ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (request_no LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR name LIKE '".$requestData['search']['value']."%'";

	$sql.=" OR asc_dis LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("Er2");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("Er3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) { 



 // preparing an array
	$nestedData=array();
    
	$nestedData[] = $j; 

	$nestedData[] = dt_format($row["requestdate"]);
	$nestedData[] = $row["request_no"];
	$nestedData[] = getdispatchstatus($row["status"]);
	$nestedData[] = $row["state"];
	$nestedData[] = $row["city"];
	

	
	$nestedData[] = "<div align='center'><a href='asp_add_appo.php?sno=".$row['sno']."&daterange=".$_REQUEST['daterange']."&status=".$_REQUEST['status']."&doc_type=".$_REQUEST['doc_type']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view document details'></i></a></div>";
	
	$nestedData[] = "<div align='center'><a href='asp_req.php?request_no=".base64_encode($row['request_no'])."".$pagenav."' target='_blank' title='print'><i class='fa fa-eye fa-lg faicon' title='print document details'></i></a></div>";
	

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
