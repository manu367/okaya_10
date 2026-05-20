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
	$daterange = "challan_date >= '".$date_range[0]."' and challan_date <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}


## selected  location
if($_REQUEST['location_code'] != ""){
	$locationid = "from_location = '".$_REQUEST['location_code']."'";
}else{
	$locationid = "1";
}
## selected  Status
if($_REQUEST['status'] != ""){
	$status = "status = '".$_REQUEST['status']."'";
}else{
	$status = "1";
}
## selected  Status

$columns = array( 
// datatable column index  => database column name
	0 => 'id', 
	1 => 'challan_no',
	2 => 'challan_date',
	3 => 'from_location',
	4 => 'courier',
	5 => 'docket_no',
    6 => 'status'
);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM sfr_challan where to_location='".$_SESSION['asc_code']."' and   ".$status." and ".$daterange." and ".$locationid;
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("sfr_in_asc-grid-data.php: get sfr_in_asc details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM sfr_challan where to_location='".$_SESSION['asc_code']."' and   ".$status." and ".$daterange." and ".$locationid;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (challan_no LIKE '".$requestData['search']['value']."%'";    
	
	$sql.=" OR docket_no LIKE '".$requestData['search']['value']."%'";

}
$query=mysqli_query($link1, $sql) or die("sfr_in_asc-grid-data.php: get sfr_in_asc details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("sfr_in_asc-grid-data.php: get sfr_in_asc details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
    
	$nestedData[] = $j; 
	$nestedData[] = $row["challan_no"];
	$nestedData[] = $row["challan_date"];
	$nestedData[] = getAnyDetails($row['from_location'],"locationname","location_code","location_master",$link1);
	
	$nestedData[] = $row["courier"];
	$nestedData[] = $row["docket_no"];

	$nestedData[] = getdispatchstatus($row["status"]);
	$nestedData[] = "<div align='center'><a href='sfr_receive_asp.php?challan_no=".$row['challan_no']."&daterange=".$_REQUEST['daterange']."&product_name=".$_REQUEST['product_name']."&brand=".$_REQUEST['brand']."&modelid=".$_REQUEST['modelid']."".$pagenav."' title='go to repair'><i class='fa fa-wrench fa-lg faicon' title='go to repair'></i></a></div>";
	$nestedData[] = "<div align='center'><a href='sfr_out_view.php?challan_no=".$row['challan_no']."&daterange=".$_REQUEST['daterange']."&product_name=".$_REQUEST['product_name']."&brand=".$_REQUEST['brand']."&modelid=".$_REQUEST['modelid']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view job details'></i></a></div>";
	
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
