<?php
/* Database connection start */
require_once("../includes/config.php");
include("../includes/brand_access.php");
/////get status//
$arrstatus = getJobStatus($link1);
////get access brand details
$access_brand = getAccessBrand($_SESSION['userid'],$link1);

////get access state details
$access_state = getAccessState($_SESSION['userid'],$link1);

/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Date range
$date_range = explode(" - ",$_REQUEST['daterange']);
if($_REQUEST['daterange'] != ""){
	$daterange = "entry_date >= '".$date_range[0]."' and entry_date <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}
## selected  product name
if($_REQUEST['product_name'] != ""){
	$productid = "product_id = '".$_REQUEST['product_name']."'";
}else{
	$productid = "1";
}
## selected  product name
/*if($_REQUEST['brand'] != ""){
	$brandid = "brand_id = '".$_REQUEST['brand']."'";
}else{
	$brandid = "1";
}*/
## selected  location
if($_REQUEST['location_code'] != ""){
	$locationid = "location_code = '".$_REQUEST['location_code']."'";
}else{
	$locationid = "1";
}
## selected  model
if($_REQUEST['modelid'] != ""){
	$modelid = "model_id = '".$_REQUEST['modelid']."'";
}else{
	$modelid = "1";
}

## selected  Brand
if(is_array($_REQUEST['brdd'])){
	$brddstr="";
	$post_brddarr = $_REQUEST['brdd'];
	for($i=0; $i<count($post_brddarr); $i++){
		if($brddstr){
			$brddstr .= ",'".$post_brddarr[$i]."'";
		}else{
			$brddstr .= "'".$post_brddarr[$i]."'";
		}
	}
	$brdd_n=" brand_id in (".$brddstr.")";
}else{
	$brdd_n=" brand_id in (".$access_brand.") ";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'id', 
	1 => 'job_no',
	2 => 'sap_mat_code',
	3 => 'repl_serial_no',
	4 => 'entry_date',
	5 => 'status',
	6 => 'model_id'
);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM dms_repl_api_data where ".$daterange;
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("Err1".mysqli_error($link1));
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT *";
$sql.=" FROM dms_repl_api_data where ".$daterange;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (job_no LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR repl_serial_no LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("Err2".mysqli_error($link1));
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

//echo $sql;

/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("Err3".mysqli_error($link1));

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array

//if($row["status"]=="In Process"){
	$viewd="<div align='center'><a href='rep-approval-btr-dms.php?refid=".base64_encode($row['job_no'])."&daterange=".$_REQUEST['daterange']."&product_name=".$_REQUEST['product_name']."&brand=".$_REQUEST['brand']."&modelid=".$_REQUEST['modelid']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view Replcament details'></i></a></div>";
//}else{
//	$viewd="";
//}
	$res_st1 = getAnyDetails($row['job_no'], "status", "job_no", "jobsheet_data", $link1);
	if($res_st1 == '84'){
	$res_st = 'In Process';
	}else{
	$res_st = getAnyDetails($res_st1, "display_status", "status_id", "jobstatus_master", $link1);
	}
	if($row['repl_serial_no']!='' && $row['status']=='In Process'){$status = "Closed";}else{$status = $row['status'];}

	$nestedData=array();	
	$nestedData[] = $j; 
	$nestedData[] = $row["job_no"];
	$nestedData[] = $row["sap_mat_code"];
	$nestedData[] = $row["model_id"];
	$nestedData[] = $row["repl_serial_no"];
	$nestedData[] = $row["entry_date"];
	$nestedData[] = $res_st;
	$nestedData[] = $viewd;
	
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
