<?php
/* Database connection start */
require_once("../includes/config.php");
/////get status//
$arrstatus = getFullStatus("master",$link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Status
if($_REQUEST['status']!=""){
	$status="status='".$_REQUEST['status']."'";
}else{
	$status="1";
}


$columns = array( 
// datatable column index  => database column name

	1 => 'level',
	2=> 'status'
);

// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM claim_price where ".$status."  ";
//echo $sql;
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("claim-grid-data.php: get Claim master");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM claim_price where ".$status." ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( claim_no LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR barnd_id LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR product_id LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR model_id LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR level LIKE '".$requestData['search']['value']."%'";
	
}
$query=mysqli_query($link1, $sql) or die("claim-grid-data.php: get claim master");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("claim-grid-data.php: get claim master");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {      // preparing an array

			
						
	
	$nestedData=array(); 
     
	$nestedData[] = $j; 
	$nestedData[] = getAnyDetails($row["product_id"],"product_name","product_id","product_master",$link1);
	$nestedData[] = getAnyDetails($row["brand_id"],"brand","brand_id","brand_master",$link1);
	
    $nestedData[] = $row["loc_iw_inst"];
     $nestedData[] = $row["loc_iw_npu"];
	  $nestedData[] = $row["loc_iw_pu"];
    $nestedData[] = $row["gas_iw_npu"];
	$nestedData[] = $row["gas_iw_pu"];
	$nestedData[] = $arrstatus[$row["status"]];
	$nestedData[] = "<div align='center'><a href='edit_claim.php?op=Edit&refid=".base64_encode($row['sno'])."&status=".$_REQUEST['status']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit Claim details'></i></a></div>";
	
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
