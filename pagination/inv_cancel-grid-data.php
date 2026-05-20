<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
$columns = array( 
// datatable column index  => database column name
	0 => 'from_location',
	1 => 'to_location ',
	2 => 'challan_no',
	3 => 'sale_date',
	4 => 'status',
	5 => 'po_type'
);

// getting total number records without any search
$sql = "SELECT * FROM billing_master where status != '5' and po_type in ('PO' ,'PNA' ,'LOCAL PURCHASE','CLAIM' ) ";
$query=mysqli_query($link1, $sql) or die("invoice_cancellation.php: get details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

 $sql = "SELECT * FROM billing_master where status != '5' and po_type in ('PO' ,'PNA','LOCAL PURCHASE','CLAIM')";
if( !empty($requestData['search']['value']) ) { 
// if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( challan_no LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR po_type LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("invoice_cancellation.php: get  details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("invoice_cancellation.php: get  details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  
///////////// fetch from location name//////////////////////////////////////////
 $fromlocation = getAnyDetails($row["from_location"],"locationname" ,"location_code","location_master",$link1);

////////////////////////  get status name/////////
if($row["status"] == "1"){$st = "Pending";} elseif($row["status"] == "2") {$st = "Processed";} elseif($row["status"] == "3") {$st = "Dispatched";} elseif ($row["status"] == "4"){$st = "Received";} elseif ($row["status"] == "5"){$st = "Cancel";} else{}

   if($row['po_type']=="CLAIM"  ){
		$div="<div align='center'><a href='inv_cancel_claim.php?op=cancel&refid=".base64_encode($row['challan_no'])."".$pagenav."' title='Cancel'><i class='fa  fa-trash fa-lg faicon' title='Cancel'></i></a></div>";
		} else{
		$div="<div align='center'><a href='inv_cancel.php?op=cancel&refid=".base64_encode($row['challan_no'])."".$pagenav."' title='Cancel'><i class='fa  fa-trash fa-lg faicon' title='Cancel'></i></a></div>";
		}
//////////////////////////
// preparing an array
	$nestedData=array(); 
  
	$nestedData[] = $j; 
	$nestedData[] =  $fromlocation;
	$nestedData[] = getAnyDetails($row["to_location"],"locationname" ,"location_code","location_master",$link1);
	$nestedData[] = $row["challan_no"];
	$nestedData[] = $row["sale_date"];
	$nestedData[] = $st ;
	$nestedData[] = $row["po_type"];
	$nestedData[] = $div;
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
