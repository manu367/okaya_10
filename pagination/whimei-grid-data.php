<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
if($_REQUEST[status] != '')
{
$status = "status = '".$_REQUEST[status]."' " ;
}
else
{
$status = "1";
}

$columns = array( 
// datatable column index  => database column name
	0 => 'imei1', 
	1 => 'imei2', 
	2 => 'partcode',
	3 => 'model_id',
	4 => 'grn_no'

	
);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM imei_details where $status";
$query=mysqli_query($link1, $sql) or die("whimei-grid-data.php: get party details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.



if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
$sql = "SELECT *";
$sql.=" FROM imei_details   where ";
	$sql.="  (imei1 LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR imei2 LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR grn_no LIKE '".$requestData['search']['value']."%' )";
	$query=mysqli_query($link1, $sql) or die("whimei-grid-data.php: get party details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result.
}
 
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	


$data = array();
$j=1;
if($totalFiltered>0) { 
while( $row=mysqli_fetch_array($query) ) { 
 // preparing an array
	$nestedData=array();    
	$nestedData[] = $j; 
	$nestedData[] = $row["imei1"];

	$nestedData[] = getAnyDetails($row["partcode"],"part_desc","partcode","partcode_master",$link1);
	$nestedData[] = getAnyDetails($row["model_id"],"model","model_id","model_master",$link1);
	$nestedData[] = $row["grn_no"];
	$nestedData[] = "<div align='center'><a href='imeihistory_view.php?refid=".base64_encode($row['grn_no'])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view  details'></i></a></div>";
	$data[] = $nestedData;
	$j++;
}
}



$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
			);

echo json_encode($json_data);  // send data as json format
?>
