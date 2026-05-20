<?php
/* Database connection start */
require_once("../includes/config.php");
/////get status//
$arrstatus = getFullStatus("master",$link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Status
if($_REQUEST['status']==''){
	$status="statusid='1'";
}
else if($_REQUEST['status']=='2'){
	$status="statusid='2'";
}else{
	$status="1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'location_code', 
	1 => 'locationname',
	2 => 'locationtype',
	3 => 'districtid',
	4 => 'cityid',
	5 => 'stateid',
	6 => 'contactno1',
	7 => 'emailid',
	8 => 'statusid'
);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM location_master where ".$status."";
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("loc-grid-data.php: get employee details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM location_master where ".$status."";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( location_code LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR locationname LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR locationtype LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR contactno1 LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR emailid LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR statusid LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("loc-grid-data.php: get employee details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("loc-grid-data.php: get employee details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
    
	$nestedData[] = $j; 
	$nestedData[] = $row["location_code"];
	$nestedData[] = $row["locationname"];
	$nestedData[] = $row["locationtype"];
	//$nestedData[] = getAnyDetails($row["districtid"],"city","cityid","city_master",$link1);
	$nestedData[] = getAnyDetails($row["cityid"],"city","cityid","city_master",$link1);
	$nestedData[] = getAnyDetails($row["stateid"],"state","stateid","state_master",$link1);
	$nestedData[] = $row["contactno1"];
	$nestedData[] = getAccBrand($row["location_code"],$link1);
	$nestedData[] = getAccPro($row["location_code"],$link1);
	$nestedData[] = $row["emailid"];
	$nestedData[] = $arrstatus[$row["statusid"]];
	
	$nestedData[] = "<div align='center'><a href='edit_location.php?id=".base64_encode($row['locationid'])."&status=".$_REQUEST['status']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit ".$locationstr." details'></i></a></div>";
	
	$nestedData[] = "<div align='center'><a href='excelexport.php?rname=".base64_encode("asppin_code_mapping")."&rheader=".base64_encode('PinCode ASP Mapping')."&locationcode=".$row['location_code']."&status=".base64_encode($_GET['status'])." title='Export employees details in excel'><i class='fa fa-file-excel-o fa-2x faicon' title='Export employees details in excel'></i></a>
	</div>";
	
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
