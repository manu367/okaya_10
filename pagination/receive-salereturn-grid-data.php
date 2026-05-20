<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
$columns = array( 
// datatable column index  => database column name
	0 => 'from_location', 
	1 => 'to_location',
	2 => 'from_party_name',
	3 => 'challan_no',
	4 => 'sale_date',
	5 => 'status',
	6 => 'po_type'
);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM billing_master where to_location = '".$_SESSION['asc_code']."' and (po_type = 'Sale Return'  or po_type = 'P2C'  or po_type = 'Stock Transfer' or po_type = 'STN') ";
$query=mysqli_query($link1, $sql) or die("receive-salereturn-grid-data.php: get  details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM billing_master where  to_location = '".$_SESSION['asc_code']."' and  (po_type = 'Sale Return'  or po_type = 'P2C' or   po_type = 'Stock Transfer' or po_type = 'STN') ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (challan_no LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR from_location LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("receive-salereturn-grid-data.php: get  details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("receive-salereturn-grid-data.php: get  details");

$data = array();
$j=1;
while($row=mysqli_fetch_array($query) ) { 
if($row["status"] == "2") {$st = "Processed" ;}

 // preparing an array
	$nestedData=array();    
	$nestedData[] = $j; 
	$nestedData[] = getAnyDetails($row["from_location"],"locationname","location_code","location_master",$link1);
	$nestedData[] = getAnyDetails($row["to_location"],"locationname","location_code","location_master",$link1);
	$nestedData[] = $row["challan_no"];	
	$nestedData[] = $row["sale_date"];
	$nestedData[] = getdispatchstatus($row["status"]);
	$nestedData[] = $row["po_type"];
	$nestedData[] = "<div align='center'><a href='invoice_challan_print_srn_wh.php?id=".base64_encode($row['challan_no'])."".$pagenav."' target='_blank' title='print'><i class='fa fa-print fa-lg faicon' title='print document details'></i></a></div>";
	if($row["status"]==2 || $row["status"]==3){
		if($row["po_type"]=='Sale Return' || $row["po_type"] == 'Stock Transfer' || $row["po_type"] == 'STN'){
	$nestedData[] = "<div align='center'><a href='receivesalereturn_view.php?refid=".base64_encode($row['challan_no'])."".$pagenav."' title='print'><i class='fa fa-eye fa-lg faicon' title='view  details'></i></a></div>";
	
		}else{
	
				$nestedData[] = "<div align='center'><a href='receivefaultypart_view.php?refid=".base64_encode($row['challan_no'])."".$pagenav."' title='print'><i class='fa fa-eye fa-lg faicon' title='view  details'></i></a></div>";
				
			}
		}else{
		$nestedData[] = "";
	}

	$nestedData[] = "<div align='center'><a href='salereturn_view.php?refid=".base64_encode($row['challan_no'])."".$pagenav."' title='print'><i class='fa fa-eye fa-lg faicon' title='view  details'></i></a></div>";
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
