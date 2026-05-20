<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  product name


$columns = array( 
// datatable column index  => database column name
    0 => 'ticket_no',
	1 => 'customer_name', 
	2 => 'contact_no',
	3 => 'city_id',
	4 => 'state_id'
	
);
// getting total number records without any search
 $sql = "SELECT *";
 $sql.=" FROM ticket_master where location_code='".$_SESSION['asc_code']."'";
$query=mysqli_query($link1, $sql) or die("job-ticket-data.php: get job details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


 $sql = "SELECT *";
$sql.=" FROM ticket_master where location_code='".$_SESSION['asc_code']."'";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (contact_no LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR customer_name LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("job-ticket-data.php: get job details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("job-ticket-data.php: get job details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
    if($row['job_no']!=""){
		$makejob = $row['job_no'];
	}else{
		$makejob = "<div align='center'><a href='job_create.php?ticket_no=".base64_encode($row['ticket_no'])."&productid=".base64_encode($row['product_id'])."&brandid=".base64_encode($row['brand_id'])."".$pagenav."' title='Make Job'><i class='fa fa-plus fa-lg faicon' title='Make Job'></i></a></div>";
	}
	$nestedData[] = $j; 
	$nestedData[] = $row["ticket_no"];
	$nestedData[] = $row["customer_name"];
	$nestedData[] = $row["contact_no"];
	$nestedData[] = getAnyDetails($row["city_id"],"city","cityid","city_master",$link1);
	$nestedData[] = getAnyDetails($row["state_id"],"state","stateid","state_master",$link1);
	$nestedData[] = $makejob;
	$nestedData[] = "<div align='center'><a href='job_ticket_view.php?ticket_no=".$row['ticket_no']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view ticket details'></i></a></div>";
	
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
