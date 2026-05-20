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
	$daterange = "po_date  >= '".$date_range[0]."' and po_date  <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}
## selected  status 
if($_REQUEST['status'] != ""){
	$status = "status = '".$_REQUEST['status']."'";
}else{
	$status = " status in ('1','6')";
}
## selected PO Type 
if($_REQUEST['po_type'] != ""){
	$po_type = " potype = '".$_REQUEST['po_type']."'";
}else{
	$po_type = " 1";
}
## selected  location
if($_REQUEST['location_code'] != ""){
	$locationcode = "from_code = '".$_REQUEST['location_code']."'";
}else{
	$locationcode = "1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'po_no', 
	1 => 'po_date',
	2 => 'from_code',
	3 => 'status'
);
// getting total number records without any search
$sql = "SELECT po_no,po_date,from_code,from_address,from_state,potype,status";
$sql.=" FROM po_master where  ".$status." and ".$daterange." and ".$locationcode." and ".$po_type." and to_code='".$_SESSION['asc_code']."'";
$query=mysqli_query($link1, $sql) or die("dispatchpopna-grid-data.php: get job details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql =  "SELECT po_no,po_date,from_code,from_address,from_state,potype,status";
$sql.=" FROM po_master where  ".$status." and ".$daterange." and ".$locationcode." and ".$po_type." and to_code='".$_SESSION['asc_code']."'";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (po_no LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR po_date LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR potype LIKE '".$requestData['search']['value']."%'";   
	$sql.=" OR from_code LIKE '".$requestData['search']['value']."%' )";
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
	$nestedData[] = $row["po_no"];
	$nestedData[] = dt_format($row["po_date"]);
	$nestedData[] = $row["potype"];
	$nestedData[] = $row["from_code"];
	$nestedData[] = getAnyDetails($row['from_code'],"locationname","location_code","location_master",$link1);
	$nestedData[] = $row["from_address"];
	$nestedData[] = getdispatchstatus($row["status"]);
	$nestedData[] = "<div align='center'><a href='dispatch_pnapo_view_asp.php?refid=".base64_encode($row['po_no'])."&daterange=".$_REQUEST['daterange']."&status=".$row['status']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view po details'></i></a></div>";
	
	$nestedData[] = "<div align='center'><a href='inventory_po_print.php?refid=".base64_encode($row['po_no'])."&daterange=".$_REQUEST['daterange']."&status=".$row['status']."".$pagenav."' target='_blank' title='print'><i class='fa fa-print fa-lg faicon' title='print po details'></i></a></div>";
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
