<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  status
if($_REQUEST['locationname'] != ""){
	$location = "from_code = '".$_REQUEST['locationname']."'";
}else{
	$location = "1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'po_no', 
	1 => 'po_date',
	2 => 'status',
	3 => 'to_code'
);
// getting total number records without any search
$sql = "SELECT po_no,po_date,status,from_code";
$sql.=" FROM po_master where  $location  and (status = '1' or  status = '6') ";
$query=mysqli_query($link1, $sql) or die("pending_po_cancel.php: get  details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT po_no,po_date,status,from_code";
$sql.=" FROM po_master where  $location  and (status = '1' or  status = '6') ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (po_no LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR from_code LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("pending_po_cancel.php: get  details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("pending_po_cancel.php: get  details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) { 

 // preparing an array
	$nestedData=array(); 
    
	$nestedData[] = $j; 
	$nestedData[] = $row["po_no"];
	$nestedData[] = dt_format($row["po_date"]);
	$nestedData[] = getdispatchstatus($row['status']);
	$nestedData[] = $row["from_code"];
	$nestedData[] = "<div align='center'><a href='inventory_po_cancel.php?op=cancel&refid=".base64_encode($row['po_no'])."".$pagenav."' title='Cancel PO'><i class='fa  fa-trash fa-lg faicon' title='Cancel PO'></i></a></div>";
	$nestedData[] = "<div align='center'><a href='inventory_po_edit.php?op=cancel&refid=".base64_encode($row['po_no'])."".$pagenav."' title='Cancel PO'><i class='fa fa-eye fa-lg faicon' title='Cancel PO'></i></a></div>";
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
