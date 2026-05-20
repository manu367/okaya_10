<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$model = mysqli_fetch_array(mysqli_query($link1,"select model_id from model_master where model ='".$requestData['search']['value']."' "));

if($_REQUEST['status'] != ""){
	$status = "status = '".$_REQUEST['status']."'";
}else{
	$status = "status in ('1','2','3')";
}


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
$sql = "SELECT * FROM imei_data_temp where $status ";
$query=mysqli_query($link1, $sql) or die("imei_approve_status.php: get details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT * FROM imei_data_temp where $status ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (imei1 LIKE '".$requestData['search']['value']."%'";   
	$sql.=" OR model_id  = '".$model['model_id']."' " ; 
	$sql.=" OR imei2 LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("imei_approve_status.php: get details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("imei_approve_status.php: get details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {

////////////////////////  get status name/////////
if($row["status"] == "1"){$st = "Pending";} elseif($row["status"] == "2") {$st = "Approved";} elseif($row["status"] == "3") {$st = "Rejected";} else{}

//////////////////////////
// preparing an array
	$nestedData=array();      
	$nestedData[] = $j; 
	$nestedData[] = getAnyDetails($row["model_id"],"model","model_id","model_master",$link1);
	$nestedData[] = $row["imei1"];
	$nestedData[] = $row["imei2"];
	$nestedData[] = $row["import_date"];
	$nestedData[] = $row["date_of_purchase"];
	$nestedData[] = "<div align='center'><a href='".$row['imei_img1']."' title='view'><i class='fa fa-download ' title='Download Image'></i></a></div>";
	$nestedData[] = "<div align='center'><a href='".$row['imei_img2']."' title='view'><i class='fa fa-download ' title='Download Image'></i></a></div>";
	$nestedData[] = $st ;
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
