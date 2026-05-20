<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
$columns = array( 
// datatable column index  => database column name
	0 => 'partcode', 
	1 => 'part_desc',
	2 => 'model_id',
	3 => 'part_category'
);

// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM partcode_master where part_category in ('BOX' ,'UNIT') and status = '1' ";
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("cannibalize-grid-data.php: get details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM partcode_master where part_category in ('BOX' ,'UNIT') and status = '1' ";
if( !empty($requestData['search']['value']) ) { 
// if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( partcode LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR part_category LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("cannibalize-grid-data.php: get details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("cannibalize-grid-data.php: get details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {
if($row["part_category"] == 'BOX' ) {$str ="<div align='center'><a href='mapModel_box.php?op=Edit&partcode=".$row['partcode']."&model_id=".$row['model_id']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit cannibalize details'></i></a></div>"; }  else {$str ="<div align='center'><a href='mapModel_unit.php?op=Edit&partcode=".$row['partcode']."&model_id=".$row['model_id']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit cannibalize details'></i></a></div>";} 
  // preparing an array
	$nestedData=array(); 
	$nestedData[] = $j; 
	$nestedData[] = $row["partcode"];
	$nestedData[] = $row["part_desc"];
	$nestedData[] = getAnyDetails($row["model_id"],"model","model_id","model_master",$link1);
	$nestedData[] = $row["part_category"];
	$nestedData[] = $str;
	
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
