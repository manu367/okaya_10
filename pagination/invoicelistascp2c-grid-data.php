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
	$daterange = "sale_date  >= '".$date_range[0]."' and sale_date  <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}
## selected  status 
if($_REQUEST['status'] != ""){
	$status = "status = '".$_REQUEST['status']."'";
}else{
	$status = " 1";
}
## selected  location
if($_REQUEST['location_code'] != ""){
	$locationcode = "to_location = '".$_REQUEST['location_code']."'";
}else{
	$locationcode = "1";
}
## selected  document type

$columns = array( 
// datatable column index  => database column name
	0 => 'challan_no', 
	1 => 'sale_date',
	2 => 'to_location',
	3 => 'status'
);
// getting total number records without any search
$sql = "SELECT challan_no,sale_date,to_location,po_no,document_type,status,courier,docket_no,disp_rmk";
$sql.=" FROM billing_master where  ".$status." and ".$daterange." and ".$locationcode."  and from_location='".$_SESSION['asc_code']."'";
$query=mysqli_query($link1, $sql) or die("Er1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql =  "SELECT challan_no,sale_date,to_location,po_no,document_type,status,courier,docket_no,disp_rmk";
$sql.=" FROM billing_master where   ".$status." and ".$daterange." and ".$locationcode."  and from_location='".$_SESSION['asc_code']."'";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (challan_no LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR sale_date LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR document_type LIKE '".$requestData['search']['value']."%'";   
	$sql.=" OR po_no LIKE '".$requestData['search']['value']."%'";   
	$sql.=" OR to_location LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("Er2");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("Er3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) { 
 // preparing an array
	$nestedData=array();
    
	$nestedData[] = $j; 
	$nestedData[] = $row["challan_no"];
	$nestedData[] = dt_format($row["sale_date"]);
	$nestedData[] = $row["document_type"];
	$nestedData[] = $row["to_location"];
	$nestedData[] = getAnyDetails($row['to_location'],"locationname","location_code","location_master",$link1);
	$nestedData[] = $row["po_no"];
	$nestedData[] = getdispatchstatus($row["status"]);
	$nestedData[] = "<div align='center'><a href='invoice_challan_view_asp.php?refid=".base64_encode($row['challan_no'])."&daterange=".$_REQUEST['daterange']."&status=".$_REQUEST['status']."&doc_type=".$_REQUEST['doc_type']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view document details'></i></a></div>";
	
	$nestedData[] = "<div align='center'><a href='invoice_challan_print_asp.php?id=".base64_encode($row['challan_no'])."".$pagenav."' target='_blank' title='print'><i class='fa fa-print fa-lg faicon' title='print document details'></i></a></div>";
	
	if($row["status"]==2 || $row["status"]==3){
		$nestedData[] = "<div align='center'><a href='#' title='update courier details' onClick=openCourierModel('".$row["challan_no"]."','".base64_encode($row["courier"])."','".base64_encode($row["docket_no"])."','".base64_encode($row["disp_rmk"])."')><i class='fa fa-truck fa-lg faicon' title='update courier details'></i></a></div>";
	}else{
		$nestedData[] = "";
	}
	$nestedData[] = "<div align='center'><a href='invoice_challan_view.php?refid=".base64_encode($row['challan_no'])."&daterange=".$_REQUEST['daterange']."&status=".$_REQUEST['status']."&doc_type=".$_REQUEST['doc_type']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view document details'></i></a></div>";
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
