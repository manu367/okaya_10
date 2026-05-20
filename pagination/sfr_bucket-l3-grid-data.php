<?php
/* Database connection start */
require_once("../includes/config.php");
/////get status//

/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;




## selected  location
if($_REQUEST['location_code'] != ""){
	$locationid = " and to_location = '".$_REQUEST['location_code']."'";
}else{
	$locationid = "";
}
## selected  Status


$columns = array( 
// datatable column index  => database column name
	0 => 'sid', 
	1 => 'to_location',
	
);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM sfr_repaired_bin where status='417' ".$locationid;
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("sfr_bucket-grid-data.php: get sfr_bucket details1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM sfr_repaired_bin where status='417' ".$locationid;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (to_location LIKE '".$requestData['search']['value']."%')";    
	


}
$query=mysqli_query($link1, $sql) or die("sfr_bucket-l3-grid-data.php: get sfr_bucket_l3 details2");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("sfr_bucket-l3-grid-data.php: get sfr_bucket_l3 details3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
    
	$nestedData[] = $j; 
	
	$nestedData[] = getAnyDetails($row['to_location'],"locationname","location_code","location_master",$link1);
	
	
	$nestedData[] = "<div align='center'><a href='sfr_dispatch_l3.php?to_location=".$row['to_location']."".$pagenav."' title='go to Dispatch'><i class='fa fa-wrench fa-lg faicon' title='go to Dispatch'></i></a></div>";
	
	
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
