<?php
/* Database connection start */
require_once("../includes/config.php");
/////get status//
$arrstatus = getFullStatus("master",$link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Status
$columns = array( 
// datatable column index  => database column name
	0 => 'sno', 
	1 => 'location_code',
	2 => 'user_code',
	3 => 'trainername',
	4 => 'type',
	5 => 'tr_desc',
	6 => 't_date',
	7 => 'e_date',
	8 => 'score'
);

// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM tech_training where 1 ";
$query=mysqli_query($link1, $sql) or die("tec-grid-data.php: get tec master");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM tech_training where 1 ";
//echo $sql;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( type LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR trainername LIKE '".$requestData['search']['value']."% )";
}
$query=mysqli_query($link1, $sql) or die("tec-grid-data.php: get tec master");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("tec-grid-data.php: get tec master");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
  
	$nestedData[] = $j; 
	$nestedData[] = getAnyDetails($row["location_code"],"locationname","location_code","location_master",$link1);
	$nestedData[] = getAnyDetails($row["user_code"],"locusername","userloginid","locationuser_master",$link1);
	$nestedData[] = $row["trainername"];
	$nestedData[] = $row["type"];
	$nestedData[] = $row["tr_desc"];
	$nestedData[] = dt_format($row["t_date"]);
	$nestedData[] = dt_format($row["e_date"]);
	$nestedData[] = $row["score"];
	$nestedData[] = "<div align='center'><a href='add_training.php?op=Edit&refid=".base64_encode($row['sno'])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit Traing details'></i></a></div>";
	
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
