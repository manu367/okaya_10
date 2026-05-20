<?php
/* Database connection start */
require_once("../includes/config.php");
////get access brand details
$access_brand = getAccessBrand($_SESSION['userid'],$link1);

/////get status//
$arrstatus = getJobStatus($link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
$columns = array( 
// datatable column index  => database column name
	0 => 'job_id', 
	1 => 'job_no',
	2 => 'imei',
	3 => 'product_id',
	4 => 'brand_id',
	5 => 'model',
	6 => 'open_date',
	7 => 'close_date',
	8 => 'customer_name',
	9 => 'warranty_status',
	10 => 'status'
);

if( !empty($requestData['search']['value']) ) {
$sql.= "SELECT *";
$sql.=" FROM jobsheet_data ";
   // if there is a search parameter, $requestData['search']['value'] contains search parameter
$sql.="where brand_id in (".$access_brand.") and (job_no LIKE '".$requestData['search']['value']."' OR imei LIKE '".$requestData['search']['value']."' OR sec_imei LIKE '".$requestData['search']['value']."' OR contact_no LIKE '".$requestData['search']['value']."')";

$query=mysqli_query($link1, $sql) or die("jobsearch-grid-data.php: get  details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result.//
}

$data = array();
$j=1;
if($totalFiltered>0) { 
while($row=mysqli_fetch_array($query)){
  // preparing an array
	$nestedData=array();	
	$nestedData[] = $j; 
	$nestedData[] = $row["job_no"];
	$nestedData[] = $row["imei"];
	$nestedData[] = getAnyDetails($row["product_id"],"product_name","product_id","product_master",$link1);
	$nestedData[] = getAnyDetails($row["brand_id"],"brand","brand_id","brand_master",$link1);
	$nestedData[] = $row["model"];
	$nestedData[] = $row["open_date"];
	$nestedData[] = $row["close_date"];
	$nestedData[] = $row["customer_name"];
	$nestedData[] = $row["warranty_status"];
	$nestedData[] = $arrstatus[$row["sub_status"]][$row["status"]];
	$nestedData[] = "<div align='center'><a href='jobstatuschange_view.php?refid=".base64_encode($row['job_no'])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view Job Status Change'></i></a></div>";	
	$data[] = $nestedData;
	$j++;

}
}

$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
			);

echo json_encode($json_data);  // send data as json format
?>
