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
$columns = array( 
// datatable column index  => database column name
	0 => 'challan_no', 
	1 => 'sale_date',
	2 => 'status',
	3 => 'to_location'
);
// getting total number records without any search
$sql = "SELECT from_location,to_location,challan_no,sale_date,status";
$sql.=" FROM billing_master where from_location in ('".$_SESSION['asc_code']."') and po_type='RETAIL' and ".$status." and ".$daterange."";
$query=mysqli_query($link1, $sql) or die("inv-grid-data.php: get inv1 details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql =  "SELECT from_location,to_location,challan_no,sale_date,status";
$sql.=" FROM billing_master where  from_location in ('".$_SESSION['asc_code']."') and po_type='RETAIL' and ".$status." and ".$daterange."";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (challan_no LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR sale_date LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR to_location LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("inv-grid-data.php: get inv2 details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("inv-grid-data.php: get inv3 details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) { 
/// bill to party
$billto=getAnyDetails($row['to_location'],"locationname,cityid,stateid","location_code","location_master",$link1);
$explodeval=explode("~",$billto);
if($explodeval[0]){ $toparty=$billto; }else{ $toparty=$row['to_location'];}
 // preparing an array
	$nestedData=array(); 
    
	$nestedData[] = $j; 
	$nestedData[] = str_replace("~",",",getAnyDetails($row['from_location'],"locationname,cityid,stateid","location_code","location_master",$link1));
	$nestedData[] = str_replace("~",",",$toparty);
	$nestedData[] = $row['challan_no'];
	$nestedData[] = dt_format($row['sale_date']);
	$nestedData[] = getdispatchstatus($row['status']);
	$nestedData[] = "<div align='center'><a href='billing_invoice_print.php?id=".base64_encode($row['challan_no'])."&daterange=".$_REQUEST['daterange']."&status=".$row['status']."".$pagenav."' target='_blank' title='Print Invoice'><i class='fa fa-print fa-lg faicon' title='Print Invoice'></i></a></div>";
	
	$nestedData[] = "<div align='center'><a href='billing_invoice_view.php?id=".base64_encode($row['challan_no'])."&daterange=".$_REQUEST['daterange']."&status=".$row['status']."".$pagenav."' title='view invoice details'><i class='fa fa-eye fa-lg faicon' title='view invoice details'></i></a></div>";
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
