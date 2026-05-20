<?php
/* Database connection start */
require_once("../includes/config.php");
////get access brand details
$access_brand = getAccessBrand($_SESSION['userid'],$link1);

/* Database connection end */
$today1=date('Y-m-d');
// storing  request (ie, get/post) global array to a variable  

$requestData= $_REQUEST;
## selected  Date range
$date_range = explode(" - ",$_REQUEST['daterange']);
if($_REQUEST['daterange'] != ""){
	$daterange = "hand_date  >= '".$date_range[0]."' and  hand_date  <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}
## selected  location
if($_REQUEST['location_code'] != ""){
	$locationid = "action_by  = '".$_REQUEST['location_code']."'";
}else{
	$locationid = "1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'action_by', 
	2 => 'job_no',
	3 => 'rep_lvl'

);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM job_claim_appr  where app_status = '' and ".$daterange." and ".$locationid." and brand_id in (".$access_brand.") ";
$query=mysqli_query($link1, $sql) or die("claim-job-grid-data.php: Er1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM job_claim_appr  where app_status = '' and ".$daterange." and ".$locationid." and brand_id in (".$access_brand.") ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( job_no LIKE '".$requestData['search']['value']."%')";  
}

$query=mysqli_query($link1, $sql) or die("claim-job-grid-data.php: Er2");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("claim-job-grid-data.php: Er3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) { 



 // preparing an array
	$nestedData=array();     
	$nestedData[] = $j; 
	$nestedData[] =  getAnyDetails($row["action_by"],"locationname","location_code","location_master",$link1);
	$nestedData[] = $row["job_no"]."-".$row[id];
	$nestedData[] = getAnyDetails($row["eng_name"],"locusername","userloginid","locationuser_master",$link1);
	$nestedData[] ="<div align='center'> <input type='text'  name='trvl$row[id]' id='trvl$row[id]' value='$row[travel_km]' class='required form-control' required  /> </div>";
	$nestedData[] = $row['claim_tat'];
	$nestedData[] = $row['area_type'];

	$nestedData[] = "<div align='center'>
	  <input id='remark'  type='text'  class=' form-control'  name='remark$row[id]' ></div>";
	  $nestedData[] = "<div align='center'> <input type='radio'  name='appr$row[id]' id='appr$row[id]' value='Y' class='required' required />Approved<input type='radio'  name='appr$row[id]' id='appr$row[id]' value='N' class='required ' required />Reject</div>";
	 	$nestedData[] = "<div align='center'><a href='job_view_claim.php?refid=".base64_encode($row['job_no'])."&page_loc=TC".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view job details'></i></a></div>";
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

