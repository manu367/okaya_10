<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Date range
$columns = array( 
// datatable column index  => database column name
	0 => 'grn_no', 
	1 => 'po_no',
	2 => 'receive_date'
	
);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM grn_master where location_code='".$_SESSION['asc_code']."' and grn_type='GRN'";
$query=mysqli_query($link1, $sql) or die("status-grn-grid-data.php: get  details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
$sql = "SELECT *";
$sql.=" FROM grn_master where location_code='".$_SESSION['asc_code']."' and  grn_type='GRN'";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (grn_no LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("status-grn-grid-data.php: get  details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("status-grn-grid-data.php: get  details");
$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {
 // preparing an array
	$nestedData=array();
	$nestedData[] = $j;
	$nestedData[] = $row["grn_no"];
	$nestedData[] = $row["receive_date"];
	$nestedData[] = $row["po_no"];
	$nestedData[] = $row["po_date"];
	$nestedData[] = "<div align='center'><a href='statusgrn_view.php?refid=".base64_encode($row['grn_no'])."&gate_entry_no=".$row['request_no']."".$pagenav."' title='print'><i class='fa fa-eye fa-lg faicon' title='view  details'></i></a></div>";
		$nestedData[] = "<div align='center'><a href='grn_status_print.php?refid=".base64_encode($row['grn_no'])."&daterange=".$_REQUEST['daterange']."&status=".$row['status']."".$pagenav."' title='print' target='_blank'><i class='fa fa-print fa-lg faicon' title='print po details'></i></a></div>";
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
