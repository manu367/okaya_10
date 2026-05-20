<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
$columns = array( 
// datatable column index  => database column name
	0 => 'challan_no', 
	1 => 'sale_date', 
	2 => 'to_location',
	3 => 'driver_contact',
	4 => 'total_cost'

);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM billing_master where po_type = 'RETAIL'  and status = '3' and from_location='".$_SESSION['asc_code']."'";
$query=mysqli_query($link1, $sql) or die("payment-customer-data.php: get party details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM billing_master where po_type = 'RETAIL' and status = '3' and from_location='".$_SESSION['asc_code']."'";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (challan_no LIKE '".$requestData['search']['value']."%')";  
}
$query=mysqli_query($link1, $sql) or die("payment-customer-data.php: get party details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("payment-customer-data.php: get party details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) { 

 // preparing an array
	$nestedData=array();     
	$nestedData[] = $j; 
	$nestedData[] = $row["challan_no"];
	$nestedData[] = dt_format($row["sale_date"]);
	$nestedData[] = $row["to_location"];
	$nestedData[] = $row["driver_contact"];
	$nestedData[] = currencyFormat($row["total_cost"]);
	$nestedData[] =  "<div align='center'><a href='receive_customerpayment.php?challan_no=".base64_encode($row['challan_no'])."".$pagenav."' title='view'><i class='fa fa-shopping-bag fa-lg faicon' title='view payment details'></i></a></div>";
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
