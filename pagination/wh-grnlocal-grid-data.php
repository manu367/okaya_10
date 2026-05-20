<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
$date_range = explode(" - ",$_REQUEST['daterange']);
if($_REQUEST['daterange'] != ""){
	$daterange = "receive_date  >= '".$date_range[0]."' and receive_date  <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'party_code', 
	1 => 'location_code',
	2 => 'grn_no',
	3 => 'receive_date',
	4 => 'status'
);

// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM grn_master where remark = 'Add local GRN'  and ".$daterange."";
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("wh-grnlocal-grid-data.php: get state master");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM grn_master where remark = 'Add local GRN'  and ".$daterange."";
if( !empty($requestData['search']['value']) ) { 
// if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( name LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR address LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("wh-grnlocal-grid-data.php: get state master");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("wh-grnlocal-grid-data.php: get state master");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {
if($row["status"] == '4') {$status = "Received" ;}
  // preparing an array
	$nestedData=array(); 
     
	$nestedData[] = $j; 
	$nestedData[] = getAnyDetails($row["party_code"],"name" ,"id" , "vendor_master",$link1);
	$nestedData[] = getAnyDetails($row["location_code"],"locationname" ,"location_code" , "location_master",$link1);
	$nestedData[] = $row["grn_no"];
	$nestedData[] = $row["receive_date"];
	$nestedData[] = $status;
	$nestedData[] = "<div align='center'><a href='wh_localgrn_print.php?refid=".base64_encode($row['grn_no'])."&daterange=".$_REQUEST['daterange']."".$pagenav."' title='print'><i class='fa fa-print fa-lg faicon' title='print local grn details'></i></a></div>";
	$nestedData[] = "<div align='center'><a href='grnvendor_view.php?refid=".base64_encode($row['system_ref_no'])."&daterange=".$_REQUEST['daterange']."&status=".$row['status']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view po details'></i></a></div>";

	
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
