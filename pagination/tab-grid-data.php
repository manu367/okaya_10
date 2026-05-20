<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Status
if($_REQUEST['status']!=""){
	$status="status='".$_REQUEST['status']."'";
}else{
	$status="1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'subtabname', 
	1 => 'subtabicon',
	2 => 'subtabseq',
	3 => 'maintabname',
	4 => 'maintabicon',
	5 => 'maintabseq',
	6 => 'status'
);

// getting total number records without any search
$sql = "SELECT subtabname, subtabicon, subtabseq, maintabname, maintabicon, maintabseq, status";
$sql.=" FROM tab_master where $status";
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("tab-grid-data.php: get tab");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT subtabname, subtabicon, subtabseq, maintabname, maintabicon, maintabseq, status";
$sql.=" FROM tab_master where $status";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( subtabname LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR maintabname LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("tab-grid-data.php: get tab");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("tab-grid-data.php: get tab");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
     
	$nestedData[] = $j; 
	$nestedData[] = $row["subtabname"];
	$nestedData[] = $row["subtabicon"];
	$nestedData[] = $row["subtabseq"];
	$nestedData[] = $row["maintabname"];
	$nestedData[] = $row["maintabicon"];
	$nestedData[] = $row["maintabseq"];
	$nestedData[] = $row["status"];
	$nestedData[] = "<div align='center'><a href='addTab.php?op=edit&id=".$row['username']."&status=".$_REQUEST['status']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view details'></i></a></div>";
	
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
