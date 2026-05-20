<?php
/* Database connection start */
require_once("../includes/config.php");
/////get status//
//$arrstatus = getFullStatus("master",$link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Status
if($_REQUEST['locationcode']!=""){
	$loccode="location_code ='".$_REQUEST['locationcode']."'";
}else{
	$loccode="1";
}
if($_REQUEST['auditdate']!=""){
	$auditdate="audit_date ='".$_REQUEST['auditdate']."'";
}else{
	$auditdate="1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'location_code', 
	2 => 'ref_no',
	3 => 'audit_date',
	4 => 'entry_date',
	5 => 'entry_by',
	6 => 'entry_ip'
);

// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM stock_audit_master WHERE ".$loccode." AND ".$auditdate;
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("stockaudit-grid-data.php: get admin users");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM stock_audit_master WHERE ".$loccode." AND ".$auditdate;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( location_code LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR ref_no LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR audit_date LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR entry_date LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR entry_by LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR entry_ip LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("stockaudit-grid-data.php: get admin users");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("stockaudit-grid-data.php: get admin users");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
     //// entry by name fetch
	 $entby = getAnyDetails($row["entry_by"],"name","username","admin_users",$link1);
	 if($entby==""){
	 	$entby = getAnyDetails($row["entry_by"],"locationname","location_code","location_master",$link1);
	 }
	 
	$nestedData[] = $j; 
	$nestedData[] = getAnyDetails($row["location_code"],"locationname","location_code","location_master",$link1);
	$nestedData[] = $row["ref_no"];
	$nestedData[] = $row["audit_date"];
	$nestedData[] = $row["entry_date"];
	$nestedData[] = $entby;
	$nestedData[] = $row["entry_ip"];
	$nestedData[] = "<div align='center'><a href='stock_audit_view_loc.php?refno=".base64_encode($row["ref_no"])."&locationcode=".base64_encode($_REQUEST['locationcode'])."&auditdate=".base64_encode($_REQUEST['auditdate'])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view details'></i></a></div>";
	$nestedData[] = "<div align='center'><a href='stock_audit_print_loc.php?refno=".base64_encode($row["ref_no"])."&locationcode=".base64_encode($_REQUEST['locationcode'])."&auditdate=".base64_encode($_REQUEST['auditdate'])."".$pagenav."' title='Take Print' target='_blank'><i class='fa fa-print fa-lg faicon' title='Take Print'></i></a></div>";
	$nestedData[] = "<div align='center'><a href='excelexport.php?rname=".base64_encode("stockauditloc")."&rheader=".base64_encode("Stock Audit Report")."&refno=".base64_encode($row["ref_no"])."&locationcode=".base64_encode($_REQUEST['locationName'])."&auditdate=".base64_encode($_REQUEST['auditdate'])."' title='Excel Export'><i class='fa fa-file-excel-o fa-lg faicon' title='Excel Export'></i></a></div>";
	
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
