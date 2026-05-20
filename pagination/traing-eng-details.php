<?php
/* Database connection start */
require_once("../includes/config.php");


/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  product name
if($_REQUEST['location_code'] != ""){
	$locationid = "  user_code = '".$_REQUEST['location_code']."'";
}else{
	$locationid = "1";
}

## selected  location
$columns = array( 
// datatable column index  => database column name
	0 => 'sno', 
	1 => 'type',
	2 => 'tr_desc',
	3 => 't_date',	
	4 => 'e_date'
);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM tech_training where location_code ='".$_SESSION['asc_code']."' and ".$locationid ;

$query=mysqli_query($link1, $sql) or die("traing-eng-details.php: get stock details1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM tech_training where location_code ='".$_SESSION['asc_code']."' and ".$locationid ;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (type LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR tr_desc LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("traing-eng-details.php: get stock details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("traing-eng-details.php: get stock details2");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
    
	$nestedData[] = $j; 
    
$nestedData[] = $row["type"];
	$nestedData[] = $row["tr_desc"];
	$nestedData[] = dt_format($row["t_date"]);
	$nestedData[] = dt_format($row["e_date"]);
	$nestedData[] = $row["score"];
	$nestedData[] = $row["trainername"];
	$nestedData[] = getAnyDetails($row["user_code"],"locusername","userloginid","locationuser_master",$link1);;
	
	
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
