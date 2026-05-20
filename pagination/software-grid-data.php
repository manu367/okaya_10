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
	0 => 'sno', 
	1 => 'product_id',
	2 => 'brand',
	3 => 'model',
	4 => 'remark',
	5 => 'url',
	6 => 'status'

);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM tools_master where ".$status."";
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("software-grid-data.php: get software details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM tools_master where ".$status."";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( model LIKE '".$requestData['search']['value']."%'";    

	$sql.=" OR remark LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("software-grid-data.php: get software details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("software-grid-data.php: get software details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
if($row['status']==2){
	$st="Deactive";
	$url="";
}else{
	$st="Active";
	$url="<div align='center'><a href=".$row['url']." title='View' ><i class='fa fa-download ' title='Download Sotware'></i></a></div>";
	//$url="<div align='center'><a href='$row[url]?op=Edit&id=".base64_encode($row['sno'])."&status=".$_REQUEST['status']."".$pagenav."' title='view'><i class='fa fa-download ' title='Download Sotware'></i></a></div>";
}
	$nestedData=array(); 
    
	$nestedData[] = $j; 
	$nestedData[] =  getAnyDetails($row["product_id"],"product_name","product_id","product_master",$link1);
	$nestedData[] = getAnyDetails($row["brand"],"brand","brand_id","brand_master",$link1);
	$nestedData[] =  getAnyDetails($row["model"],"model","model_id","model_master",$link1);;
	$nestedData[] = $row["remark"];
    $nestedData[] = $row["version"];

	$nestedData[] = $st;
	$nestedData[] = "<div align='center'><a href='add_software.php?op=Edit&id=".base64_encode($row['sno'])."&status=".$_REQUEST['status']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit software details'></i></a></div>";
	$nestedData[] = $url;
	
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
