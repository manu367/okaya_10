<?php
/* Database connection start */
require_once("../includes/config.php");
////get access brand details
$access_brand = getAccessBrand($_SESSION['userid'],$link1);
// get brands ////
$brandfiltter="and brand_id in (".$access_brand.")";

/////get status//
$arrstatus = getFullStatus("master",$link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Status
if($_REQUEST['status']==''){
	$status="status='1'";
}
else if($_REQUEST['status']=='2'){
	$status="status='2'";
}else{
	$status="1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'crm_id', 
	1 => 'brand_id',
	2 => 'product_id',
	3 => 'stateid',
	4 => 'name',
	5 => 'email',
	6 => 'phone',
	7 => 'level',
	8 => 'status'
);

// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM escalation_master where ".$status."".$brandfiltter."";
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("model-grid-data.php: get model master");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM escalation_master where ".$status."".$brandfiltter."";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( crm_id LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR email LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR phone LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR state LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("model-grid-data.php: get model master");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("model-grid-data.php: get Dept master");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
    $days=$row["days"]." Days";
	$nestedData[] = $j; 

	$nestedData[] = getAnyDetails($row["brand_id"],"brand","brand_id","brand_master",$link1);
	$nestedData[] = getAnyDetails($row["product_id"],"product_name","product_id","product_master",$link1);
	$nestedData[] = $row["state"];
	$nestedData[] = $row["name"];
	$nestedData[] = $row["email"];
	$nestedData[] = $row["phone"];
	$nestedData[] = $row["level"];
	$nestedData[] = $arrstatus[$row["status"]];
	$nestedData[] = $days;
	$nestedData[] = $row["crm_id"];
	$nestedData[] = "<div align='center'><a href='add_escalation_management.php?op=Edit&refid=".base64_encode($row['crm_id'])."&status=".$_REQUEST['status']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit  details'></i></a></div>";
	
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
