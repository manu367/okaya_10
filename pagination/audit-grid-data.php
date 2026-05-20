<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Date range
$date_range = explode(" - ",$_REQUEST['daterange']);
if($_REQUEST['daterange'] != ""){
	$daterange = "audit_date  >= '".$date_range[0]."' and audit_date  <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}
## selected  status
if($_REQUEST['status']!=""){
	$status="status='".$_REQUEST['status']."'";
}else{
	$status="1";
}

## selected  visit type
if($_REQUEST['visit_type']!=""){
	$type="type='".$_REQUEST['visit_type']."'";
}else{
	$type="1";
}

$columns = array( 
// datatable column index  => database column name
	0 => 'audit_date', 
	1 => 'request_no', 
	2 => 'requestdate',
	3 => 'type',
	4 => 'name',
	5 => 'stateid',
	6 => 'cityid'
	
);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM audit_details where ".$status."  and  ".$daterange." and  ".$type." ";
$query=mysqli_query($link1, $sql) or die("audit-grid-data.php: get  details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM audit_details where ".$status."  and  ".$daterange." and  ".$type." ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( location_code LIKE '".$requestData['search']['value']."%')";  
}
$query=mysqli_query($link1, $sql) or die("audit-grid-data.php: get details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("audit-grid-data.php: get  details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) { 

/////////////perivious details of location code
$perv_code="select * from audit_details where location_code= '".$row['location_code']."' and sno < '".$row['sno']."'";
$result_perv=mysqli_query($link1,$perv_code);
$perv_result=mysqli_fetch_array($result_perv);

$str = "<div><a href='auditDetails.php?request_no=".base64_encode($perv_result['request_no'])."" .$pagenav."''>".$perv_result['request_no']."</a></div> ";

 // preparing an array
	$nestedData=array();     
	$nestedData[] = $j; 
	$nestedData[] = dt_format($row["audit_date"]);
	$nestedData[] = $row["request_no"];
	$nestedData[] =$str;
	$nestedData[] = dt_format($row["requestdate"]);
	$nestedData[] = $row["type"];
	$nestedData[] = $row["name"];
	$nestedData[] = getAnyDetails($row["stateid"],"state","stateid","state_master",$link1);
	$nestedData[] = getAnyDetails($row["cityid"],"city","cityid","city_master",$link1);
	$nestedData[] = "<div align='center'><a href='location_feedback.php?id=".$row['sno']."&request_no=".$row['request_no']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit  details'></i></a></div>" ;
	$nestedData[] ="<div align='center'><a href='view_audit_details.php?request_no=".base64_encode($row['request_no'])."" .$pagenav."'' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit  details'></i></a></div>" ;
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
