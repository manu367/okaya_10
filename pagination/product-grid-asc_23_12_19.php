<?php
/* Database connection start */
require_once("../includes/config.php");
/////get status//
$arrstatus = getFullStatus("master",$link1);
$access_product = getAccessProduct($_SESSION['asc_code'],$link1);

////get access brand details

$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Status


 if($_REQUEST['product']!='' && $_REQUEST['brand']=='' && $_REQUEST['model']==""){
	$mod_id =  "product_id='".$_REQUEST['product']."'";
	}else if($_REQUEST['product']!='' && $_REQUEST['brand']!='' && $_REQUEST['model']==""){
		$mod_id =  "product_id='".$_REQUEST['product']."' and brand_id  = '".$_REQUEST['brand']."'";
		}else if($_REQUEST['product']!='' && $_REQUEST['brand']!='' && $_REQUEST['model']!=""){
			$mod_id =  "product_id='".$_REQUEST['product']."' and brand_id  = '".$_REQUEST['brand']."' and model_id  Like '%".$_REQUEST['model']."%'";
		}else if($_REQUEST['product']=='' && $_REQUEST['brand']!='' && $_REQUEST['model']!=""){
			$mod_id =  " brand_id  = '".$_REQUEST['brand']."' and model_id  Like '%".$_REQUEST['model']."%'";
		}else if($_REQUEST['product']=='' && $_REQUEST['brand']!='' && $_REQUEST['model']==""){
			$mod_id =  " brand_id  = '".$_REQUEST['brand']."' ";
		}else if($_REQUEST['product']=='' && $_REQUEST['brand']=='' && $_REQUEST['model']==""){
			$mod_id =  "1 ";
		}else{
			$mod_id= " 1";
			}


$columns = array( 
// datatable column index  => database column name
	0 => 'model_id', 
	1 => 'amc_amount',
	2 => 'amc_days',
	3 => 'wp',
	4 => 'status'
);

// getting total number records without any search
 $sql = "SELECT *";
$sql.=" FROM model_master where  brand_id in (".$access_brand.")  and  ".$mod_id;
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
//echo $sql;
$query=mysqli_query($link1, $sql) or die("partcode-grid-data.php: get partcode master");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter

 $sql = "SELECT *";
$sql.=" FROM model_master where brand_id in (".$access_brand.")  and ".$mod_id;

	$sql.=" AND ( model_id LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR model LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
	$query=mysqli_query($link1, $sql) or die("partcode-grid-data.php: get partcode master");
	$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 

}
//echo $sql;
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("partcode-grid-data.php: get partcode master");

$data = array();
$j=1;
//echo mysqli_num_rows($query)."dadat";
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

	$nestedData[] = $row["wp"];
	
	$nestedData[] = $row["amc_desc"];
	
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
