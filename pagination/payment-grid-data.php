<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
$columns = array( 
// datatable column index  => database column name
	0 => 'id', 
	1 => 'to_location', 
	2 => 'challan_no',
	3 => 'entry_date',
	4 => 'pay_mode',
	5 => 'amount',
	6 => 'status'

);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM payment_details where from_location ='".$_SESSION['asc_code']."'";
$query=mysqli_query($link1, $sql) or die("payment-grid-data.php: get party details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM payment_details  where from_location ='".$_SESSION['asc_code']."'";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( to_location LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR challan_no LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR pay_mode LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR amount LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR entry_date LIKE '".$requestData['search']['value']."%')";
}
$query=mysqli_query($link1, $sql) or die("payment-grid-data.php: get party details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("payment-grid-data.php: get party details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) { 
if($row['status']  == '1') {$str = "Pending" ;}

 // preparing an array
	$nestedData=array(); 
    
	$nestedData[] = $j; 
	$nestedData[] = getAnyDetails($row["to_location"],"locationname","location_code","location_master",$link1);
	$nestedData[] = $row["challan_no"];
	$nestedData[] = $row["entry_date"];
	$nestedData[] = $row["pay_mode"];
	$nestedData[] = $row["amount"];
	$nestedData[] = $str;
	$nestedData[] = $row["attachment"];
	$nestedData[] = "<div align='center'><a href='myaccount_payment_detail.php?op=Edit&id=".$row['id']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit  details'></i></a></div>";
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
