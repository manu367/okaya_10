<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
$today1=date('Y-m-d');
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Date range
$date_range = explode(" - ",$_REQUEST['daterange']);
if($_REQUEST['month'] != ""){
	$month =$_REQUEST['month'];
}else{
	$month = "";
}
## selected  status 
if($_REQUEST['status'] != ""){
	$status = "status = '".$_REQUEST['status']."'";
}else{
	$status = " status in ('2','3','4')";
}
## selected  location
if($_REQUEST['location_code'] != ""){
	$locationcode = "from_location = '".$_REQUEST['location_code']."'";
}else{
	$locationcode = "1";
}
## selected  document type
if($_REQUEST['year'] != ""){
	$year = $_REQUEST['year'];
}else{
	$year = "";
}

$sel_month="$_REQUEST[year]-$_REQUEST[month]";

$columns = array( 
// datatable column index  => database column name
	0 => 'challan_no', 
	1 => 'sale_date',
	2 => 'from_location',
	3 => 'claim_month',
	4 => 'status'
);
// getting total number records without any search
$sql = "SELECT challan_no,sale_date,from_location,disp_rmk,status,claim_month,po_type";
$sql.=" FROM billing_master where  ".$status." and claim_month like '%$sel_month%'  and ".$locationcode." and (po_type='CLAIM' or po_type='TRAVEL CLAIM')";
$query=mysqli_query($link1, $sql) or die("Er1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql =  "SELECT challan_no,sale_date,from_location,disp_rmk,status,claim_month,po_type";
$sql.=" FROM billing_master where  ".$status." and claim_month like '%$sel_month%' and ".$locationcode."   and (po_type='CLAIM' or po_type='TRAVEL CLAIM') ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (challan_no LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR sale_date LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR document_type LIKE '".$requestData['search']['value']."%'";   
	$sql.=" OR po_no LIKE '".$requestData['search']['value']."%'";   
	$sql.=" OR from_location LIKE '".$requestData['search']['value']."%' )";
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

	$nestedData[] = $row["from_location"];
	$nestedData[] = getAnyDetails($row['from_location'],"locationname","location_code","location_master",$link1);
	$nestedData[] = $row["claim_month"];
if ($row['po_type']=="CLAIM"){
	if($row["disp_rmk"]==''){
	

		$nestedData[] = "<div align='center'><a href='claim_release_viewnew.php?id=".base64_encode($row['challan_no'])."&location_code=".$row['from_location']."".$pagenav."'  title='print'><i class='fa fa-eye fa-lg faicon' title='View Details'></i></a></div>";

		
		
	} else {
		$nestedData[] = $row["disp_rmk"];
		}
		
		}else{
		
			if($row["trvel_rmk"]==''){
	

		$nestedData[] = "<div align='center'><a href='claim_release_viewtravel.php?id=".base64_encode($row['challan_no'])."&location_code=".$row['from_location']."".$pagenav."'  title='print'><i class='fa fa-eye fa-lg faicon' title='View Details'></i></a></div>";

		
		
	} else {
		$nestedData[] = $row["disp_rmk"];
		}
		
		}
			$nestedData[] = getdispatchstatus($row["status"]);
			if ($row['po_type']=="CLAIM"){
	$nestedData[] = "<div align='center'><a href='claim_invioce_print.php?id=".base64_encode($row['challan_no'])."".$pagenav."' target='_blank' title='print'><i class='fa fa-print fa-lg faicon' title='print document details'></i></a></div>";
	} else{
	$nestedData[] = "<div align='center'><a href='claim_invioce_print_travel.php?id=".base64_encode($row['challan_no'])."".$pagenav."' target='_blank' title='print'><i class='fa fa-print fa-lg faicon' title='print document details'></i></a></div>";
	}


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
