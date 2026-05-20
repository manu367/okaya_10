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
	$daterange = "request_date   >= '".$date_range[0]."' and request_date   <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}
## selected  status 
if($_REQUEST['status'] != ""){
	$status = "status = '".$_REQUEST['status']."'";
}else{
	$status = " status in ('1','6')";
}


## selected  location
if($_REQUEST['location_code'] != ""){
	$locationcode = "eng_id = '".$_REQUEST['location_code']."'";
}else{
	$locationcode = "1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'id', 
	1 => 'eng_id'
	
);
// getting total number records without any search
 $sql = "SELECT *";
 $sql.=" FROM part_demand where  ".$status." and ".$daterange." and ".$locationcode." and location_code='".$_SESSION['asc_code']."' group by eng_id ";
$query=mysqli_query($link1, $sql) or die("dispatchpopna-grid-data.php: get job details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql =  "SELECT *";
$sql.=" FROM part_demand where  ".$status." and ".$daterange." and ".$locationcode."  and location_code='".$_SESSION['asc_code']."' group by eng_id";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (eng_id LIKE '".$requestData['search']['value']."%'"; 
     $sql.=" OR partcode  LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("dispatchpopna-grid-data.php: get job details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("dispatchpopna-grid-data.php: get job details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) { 
 // preparing an array
	$nestedData=array(); 
    
	$nestedData[] = $j; 
	$nestedData[] = $row["eng_id"];
		$nestedData[] = getAnyDetails($row["eng_id"],"locusername","userloginid","locationuser_master",$link1);
	$nestedData[] = "<div align='center'><a href='issue_part_dispatch.php?refid=".base64_encode($row['eng_id'])."&daterange=".$_REQUEST['daterange']."&status=".$row['status']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view po details'></i></a></div>";
	

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
