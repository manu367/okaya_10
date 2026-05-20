<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Date range
$date_range = explode(" - ",$_REQUEST['daterange']);
if($_REQUEST['daterange'] != ""){
	$daterange = "sale_date  >= '".$date_range[0]."' and sale_date  <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}
## selected  status
if($_REQUEST['status'] != ""){
	$status = "status = '".$_REQUEST['status']."'";
}else{
	$status = "1";
}
## selected  location
if($_REQUEST['doc_type'] != ""){
	$doctype = "document_type = '".$_REQUEST['doc_type']."'";
}else{
	$doctype = "1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'from_location', 
	2 => 'challan_no',
	3 => 'sale_date',
	4 => 'status'
);
// getting total number records without any search
$sql = "SELECT id,from_location,challan_no,sale_date,status,po_no,po_type";
$sql.=" FROM billing_master where to_location='".$_SESSION['asc_code']."' and ".$status." and ".$daterange." and  ".$doctype." ";

$query=mysqli_query($link1, $sql) or die("inv-grid-data.php: get stock details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT id,from_location,challan_no,sale_date,status,po_no,po_type";
$sql.=" FROM billing_master where to_location='".$_SESSION['asc_code']."' and ".$status." and ".$daterange." and  ".$doctype." ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (challan_no LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR from_location LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR sale_date LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("inv-grid-data.php: get stock details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("inv-grid-data.php: get stock details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) { 

if($row["status"] == 3 ) {
	if($row["po_type"]=='PICKUP NOTE'){
	$str = "<div align='center'><a href='inventory_stockin_receive_faulty.php?refid=".base64_encode($row['challan_no'])."&doc_type=".$_REQUEST['doc_type']."&status=".$_REQUEST['status']."".$pagenav."' title='view'><i class='fa fa-shopping-bag fa-lg faicon' title='view po details'></i></a></div>";
	}else{
	$str = "<div align='center'><a href='inventory_stockin_receive.php?refid=".base64_encode($row['challan_no'])."&doc_type=".$_REQUEST['doc_type']."&status=".$_REQUEST['status']."".$pagenav."' title='view'><i class='fa fa-shopping-bag fa-lg faicon' title='view po details'></i></a></div>"; 
	}
}else{
	$str="";
}

///////////   updated by priya on 23 june 2018 ( imei receive ) ////////////////////

if($row["status"] == 4) { $imeireceive = "<div align='center'><a href='inventory_stockin_imei.php?refid=".base64_encode($row['challan_no'])."&doc_type=".$_REQUEST['doc_type']."&status=".$_REQUEST['status']."".$pagenav."' title='view'><i class='fa fa-shopping-bag fa-lg faicon' title='view po details'></i></a></div>"; 
}else{
	$imeireceive="";
}

 // preparing an array
	$nestedData=array();
	$nestedData[] = $j; 
	$nestedData[] = getAnyDetails($row["from_location"],"locationname","location_code","location_master",$link1);
	$nestedData[] = $row["challan_no"];
    $nestedData[] = $row["po_no"];
	$nestedData[] = dt_format($row['sale_date']);
	$nestedData[] = getdispatchstatus($row["status"]);
    $nestedData[] = "<div align='center'><a href='invoice_challan_print_po_asp.php?refid=".base64_encode($row['challan_no'])."&daterange=".$_REQUEST['daterange']."&status=".$_REQUEST['status']."&doc_type=".$_REQUEST['doc_type']."".$pagenav."' title='view' target='_blank'><i class='fa fa-eye fa-lg faicon' title='view document details'></i></a></div>";
    $nestedData[] = $str;
    $nestedData[] = $imeireceive;
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