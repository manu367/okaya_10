<?php
/* Database connection start */
//require_once("../includes/config_mis.php");
require_once("../includes/config.php");

$brandarray = getBrandArray($link1);
$productarray = getProductArray($link1);
/// get access product
$get_accproduct = getAccessProduct($_SESSION['asc_code'],$link1);
/// get access brand
$get_accbrand = getAccessBrand($_SESSION['asc_code'],$link1);

// eng intrasit calculation
function loc_2_eng_ok_intransit($location,$part,$link1){
	
		//$po_type="'ISSUE-TO-ENG','ENG-RETURN','RETURN-FROM-ENG'";
	
	$intransitd = mysqli_query($link1,"SELECT SUM(b.qty) AS qty FROM stn_master a, stn_items b WHERE ( a.status =  '2' OR a.status =  '3') AND a.to_location =  '".$location."' AND a.challan_no = b.challan_no AND b.partcode =  '".$part."' AND a.po_type='ISSUE-TO-ENG' GROUP BY b.partcode");
	$intransit_data=mysqli_fetch_array($intransitd);
	if($intransit_data['qty']!=''){  return $intransit_data['qty'];} else {   return 0;}
}

function eng_2_loc_ok_intransit($location,$part,$link1){
	
		$po_type="'ENG-RETURN','RETURN-FROM-ENG'";
	
	$intransitd = mysqli_query($link1,"SELECT SUM(b.qty) AS qty FROM stn_master a, stn_items b WHERE ( a.status =  '2' OR a.status =  '3') AND a.from_location =  '".$location."' AND a.challan_no = b.challan_no AND b.partcode =  '".$part."' AND a.po_type IN (".$po_type.") GROUP BY b.partcode");
	$intransit_data=mysqli_fetch_array($intransitd);
	if($intransit_data['qty']!=''){  return $intransit_data['qty'];} else {   return 0;}
}

//////////////////---------------------------/////////////////////


/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  product name
if($_REQUEST['partcode'] != "All" && $_REQUEST['partcode'] != ""){
	$productid = "partcode = '".$_REQUEST['partcode']."'";
}else{
	$productid = "1";
}
## selected  product name

## selected  location




$columns = array( 
// datatable column index  => database column name
	0 => 'id', 
	1 => 'location_code',
	2 => 'location_name',
	3 => 'serial_no',
	4 => 'partcode',
	5 => 'part_name',
	6 => 'entry_date',
	7 => 'status',
	8 => 'stock_type',
	
);
if($_SESSION['id_type']=='ASP'){
    
    $from ="imei_details_asp";
}else{
    $from="imei_details";
}
// getting total number records without any search
$sql = "SELECT imei1,partcode,entry_date";
$sql.=" FROM $from where status='1' and imei1 != '' and location_code = '".$_SESSION['asc_code']."' and ".$productid."";


//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("stock-grid-data.php: get stock details1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT imei1,partcode,entry_date,location_code";
$sql.=" FROM $from where status='1' and imei1 != '' and location_code = '".$_SESSION['asc_code']."' and ".$productid."";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (partcode LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR imei1 LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("stock-grid-data.php: get stock details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
//  echo $sql;
$query=mysqli_query($link1, $sql) or die("stock-grid-data.php: get stock details2");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
    
	$nestedData[] = $j; 
    
	$nestedData[] = $_SESSION['asc_code'];
    $loc_details=mysqli_fetch_array(mysqli_query($link1,"select locationname from location_master where location_code='".$_SESSION['asc_code']."'"));
	//$eng_details=mysqli_fetch_array(mysqli_query($link1,"select locusername from locationuser_master where userloginid='".$row['location_code']."'"));
	$nestedData[] = $loc_details['locationname'];
	//$nestedData[] = $row['location_code'];
	//$nestedData[] = $eng_details['locusername'];
	$nestedData[] = $row["imei1"];
	$nestedData[] = $row["partcode"];
	$nestedData[] = getAnyDetails($row["partcode"], "part_name", "partcode", "partcode_master", $link1);
	$nestedData[] = $row["entry_date"];
	$nestedData[] = 'Available';
	$nestedData[] = 'OK';
	
	
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
