<?php
/* Database connection start */
require_once("../includes/config.php");
/////get status//
$arrstatus = getFullStatus("master",$link1);
$access_brand = getAccessBrand($_SESSION['userid'],$link1);

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
if($_REQUEST['model']!=""){
	$model_id="model_id  like '%".$_REQUEST['model']."%'";
}else{
	$model_id="1";
}

$columns = array( 
// datatable column index  => database column name
	0 => 'partcode', 
	1 => 'hsn_code',
	2 => 'part_name',
	3 => 'part_category',
	4 => 'status'
);

// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM partcode_master where brand_id in (".$access_brand.")  and ".$status." and  ".$model_id." ";
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("partcode-grid-data.php: get partcode master");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM partcode_master where brand_id in (".$access_brand.") and ".$status." and  ".$model_id."  ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( partcode LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR hsn_code LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR part_name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR part_category LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("partcode-grid-data.php: get partcode master");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("partcode-grid-data.php: get partcode master");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {      // preparing an array
	$part= explode(",",$row['model_id']); 
			           $partpresent   = count($part);
					   if($partpresent == '1'){
					   $name = getAnyDetails($part[0],"model","model_id","model_master",$link1 );
					   }
					   else if($partpresent >1){
					     $name ='';
					   for($i=0 ; $i<$partpresent; $i++){					 
			 			$name.=  getAnyDetails($part[$i],"model","model_id","model_master",$link1 ).",";
			 			}}
			
						
	
	$nestedData=array(); 
     
	$nestedData[] = $j; 
	$nestedData[] = getAnyDetails($row["brand_id"],"brand","brand_id","brand_master",$link1);
	$nestedData[] = getAnyDetails($row["product_id"],"product_name","product_id","product_master",$link1);
	$nestedData[] = $name;
	$nestedData[] = $row["partcode"];
	$nestedData[] = $row["hsn_code"];
	$nestedData[] =  $row["part_name"];
	$nestedData[] = $row["part_category"];
	$nestedData[] = $arrstatus[$row["status"]];
	$nestedData[] = "<div align='center'><a href='edit_partcode.php?op=Edit&refid=".base64_encode($row['partcode'])."&status=".$_REQUEST['status']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit partcode details'></i></a></div>";
	
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
