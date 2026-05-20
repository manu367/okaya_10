<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
## selected  status 
if($_REQUEST['status']=="missing"){
	$status = " missing > 0 and (type='PNA' or type='PO')";
}else if($_REQUEST['status']=="broken"){
	$status = " broken > 0 and (type='PNA' or type='PO')";
}else{
}


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
if($_REQUEST['status']!=""){
$sql = "SELECT *";
$sql.=" FROM billing_product_items where to_location = '".$_SESSION['asc_code']."' and ".$status;
$query=mysqli_query($link1, $sql) or die(mysqli_error($link1)."request-mss-dmd-grid-data.php: get details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT *";
$sql.=" FROM billing_product_items where to_location = '".$_SESSION['asc_code']."' and ".$status;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (challan_no LIKE '".$requestData['search']['value']."%')";
}
$query=mysqli_query($link1, $sql) or die("request-mss-dmd-grid-data.php: get  details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" GROUP BY challan_no";
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("request-mss-dmd-grid-data.php: get  details");
$data = array();
$j=1;
while($row=mysqli_fetch_array($query) ) { 
if($_REQUEST['status']=="missing"){
	$mss="<div align='center'><a href='wh_reconcilation_view.php?refid=".base64_encode($row['challan_no'])."".$pagenav."' title='print'><i class='fa fa-eye fa-lg faicon' title='view  details'></i></a></div>";
	if($row['missing_reco']==""){
		$st="Pending For Reconcilation Request";
		$div="<div align='center'><a href='wh_reco_miss_req_view.php?refid=".base64_encode($row['challan_no'])."&f_location=".$row['from_location']."".$pagenav."' title='print'><i class='fa fa-eye fa-lg faicon' title='view  details'></i></a></div>";
	}else if($row['missing_reco']=="Y"){$st="Missing Reconcilation Approved"; $div="";}else if($row['missing_reco']=="N"){$st="Missing Reconcilation Rejected"; $div="";}else if($row['missing_reco']=="R"){$st="Missing Reconcilation Requested"; $div="";}else{$st=""; $div="";}
}else if($_REQUEST['status']=="broken"){
	$mss="";
	if($row['damage_reco']==""){
		$st="Pending For Reconcilation";
		$div="<div align='center'><a href='wh_reco_dmd_req_view.php?refid=".base64_encode($row['challan_no'])."&f_location=".$row['from_location']."".$pagenav."' title='print'><i class='fa fa-eye fa-lg faicon' title='view  details'></i></a></div>";
	}else if($row['damage_reco']=="Y"){$st="Damage Move to Faulty"; $div="";}else{$st=""; $div="";}
}else{
}

 // preparing an array
	$nestedData=array();    
	$nestedData[] = $j; 
	$nestedData[] = getAnyDetails($row["from_location"],"locationname","location_code","location_master",$link1);
	$nestedData[] = getAnyDetails($row["to_location"],"locationname","location_code","location_master",$link1);
	$nestedData[] = $row["challan_no"];	
	$nestedData[] = $st;
	$nestedData[] = "Good Stock Reco";
	$nestedData[] =  $div;
	$nestedData[] =  $mss;//debitAgainstFaultypart_detail.php
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
}
?>
