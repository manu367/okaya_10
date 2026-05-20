<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
$columns = array( 
// datatable column index  => database column name
	0 => 'req_by',
	1 => 'model',
	2 => 'imei1',
	3 => 'imei2',
	4 => 'import_date',
	5 => 'imei_img1',
	6 => 'imei_img2',
	7 => 'status',
);

// getting total number records without any search
$sql = "SELECT * FROM imei_data_temp where status in ('1','2','3') ";
$query=mysqli_query($link1, $sql) or die("imei-appr-grid-data.php: get details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT * FROM imei_data_temp where status in('1','2','3')";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( imei1 LIKE '".$requestData['search']['value']."%',"; 
	$sql.=" OR imei2 LIKE '".$requestData['search']['value']."%',"; 
	$sql.=" OR req_by LIKE '".$requestData['search']['value']."%')";   
}
$query=mysqli_query($link1, $sql) or die("imei-appr-grid-data.php: get details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("imei-appr-grid-data.php: get details");
//echo $sql;

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {
	
///////////// fetch from location name//////////////////////////////////////////
 	$fromlocation = getAnyDetails($row["req_by"],"locationname" ,"location_code","location_master",$link1);
 //if($fromlocation==""){
	// $fromlocation = getAnyDetails($row["req_by"],"username" ,"username","admin_users",$link1);
// }
 if($row["status"]=="1"){
	$btn="<div align='center'><a href='update_imei.php?op=Y&refid=".base64_encode($row['id'])."".$pagenav."' title='Approve' target='_blank'><i class='fa  fa-check fa-lg faicon' title='Approve' v></i></a></div>";
	$btn2="<div align='center'><a href='update_imei.php?op=N&refid=".base64_encode($row['id'])."".$pagenav."' title='Reject' target='_blank'><i class='fa  fa-remove fa-lg faicon' title='Reject'></i></a></div>";
 }else{
	 $btn="";
	 $btn2="";
 }
////////////////////////  get status name/////////
if($row["status"] == "1"){$st = "Pending";} elseif($row["status"] == "2") {$st = "Approved";} elseif($row["status"] == "3") {$st = "Rejected";} else{}

//////////////////////////
// preparing an array
	$nestedData=array(); 
     
	$nestedData[] = $j; 
	$nestedData[] = $fromlocation;
	$nestedData[] = getAnyDetails($row["model_id"],"model","model_id","model_master",$link1);
	$nestedData[] = $row["imei1"];
	$nestedData[] = $row["imei2"];
	$nestedData[] = $row["import_date"];
	$nestedData[] = "<div align='center'><a href='".$row['imei_img1']."' title='view'><i class='fa fa-download ' title='Download Image'></i></a></div>";
	$nestedData[] = "<div align='center'><a href='".$row['imei_img2']."' title='view'><i class='fa fa-download ' title='Download Image'></i></a></div>";
	$nestedData[] = $st ;
	$nestedData[] = $btn;
	$nestedData[] = $btn2;
	
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
