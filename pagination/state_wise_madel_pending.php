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
## selected  Date range
$date_range = explode(" - ",$_REQUEST['daterange']);
if($_REQUEST['daterange'] != ""){
	$daterange = "open_date >= '".$date_range[0]."' and open_date <= '".$date_range[1]."'";
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
if($_REQUEST['brand'] != ""){
	$brandid = "brand_id = '".$_REQUEST['brand']."'";
}else{
	$brandid = "brand_id in (".$access_brand.")";
}
function job_model_details($model,$state,$daterange,$link1){
//echo "select count(job_id) as job_count from jobsheet_data where status='".$status."'  and eng_id='".$eng_name."' ";

$date_range = explode(" - ",$daterange);
if($daterange != ""){
	$daterange_open= "open_date >= '".$date_range[0]."' and open_date <= '".$date_range[1]."'";

}else{
	$daterange_open = "1";
	
}

//echo "select count(job_id) as job_count from jobsheet_data where state_id='".$state."' and model_id='".$model."' and close_date='0000-00-00' and ".$daterange_open."";
$res_eng_p = mysqli_query($link1,"select count(job_id) as job_count from jobsheet_data where state_id='".$state."' and model_id='".$model."' and close_date='0000-00-00' and ".$daterange_open." ");

$row_count = mysqli_fetch_array($res_eng_p);
if($row_count['job_count']!=''){
$count_job=$row_count['job_count'];

}else{
$count_job=0;
}

return $count_job;
}

## selected  model
if($_REQUEST['modelid'] != ""){
	$modelid = "model_id = '".$_REQUEST['modelid']."'";
}else{
	$modelid = "1";
}
## selected  Status

$columns = array( 
// datatable column index  => database column name
	0 => 'id', 
	1 => 'model'

);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM model_master where 1 and ".$productid." and ".$brandid." and ".$modelid;
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("Err1".mysqli_error($link1));
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT *";
$sql.=" FROM model_master where 1 and ".$productid." and ".$brandid." and ".$modelid;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (model LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("Err2".mysqli_error($link1));
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("Err3".mysqli_error($link1));

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array




	$nestedData=array();	
	$nestedData[] = $j; 
	$nestedData[] = $row["model"];
$nestedData[] = getAnyDetails($row["product_id"],"product_name","product_id","product_master",$link1);
	$nestedData[] = job_model_details($row["model_id"],'15',$_REQUEST['daterange'],$link1);
	$nestedData[] = job_model_details($row["model_id"],'16',$_REQUEST['daterange'],$link1);
		$nestedData[] = job_model_details($row["model_id"],'8',$_REQUEST['daterange'],$link1);
		$nestedData[] = job_model_details($row["model_id"],'3',$_REQUEST['daterange'],$link1);
			$nestedData[] = job_model_details($row["model_id"],'9',$_REQUEST['daterange'],$link1);
			$nestedData[] = job_model_details($row["model_id"],'22',$_REQUEST['daterange'],$link1);
					$nestedData[] = job_model_details($row["model_id"],'10',$_REQUEST['daterange'],$link1);
			$nestedData[] = job_model_details($row["model_id"],'19',$_REQUEST['daterange'],$link1);
				$nestedData[] = job_model_details($row["model_id"],'20',$_REQUEST['daterange'],$link1);
					$nestedData[] = job_model_details($row["model_id"],'4',$_REQUEST['daterange'],$link1);
						$nestedData[] = job_model_details($row["model_id"],'7',$_REQUEST['daterange'],$link1);
							$nestedData[] = job_model_details($row["model_id"],'33',$_REQUEST['daterange'],$link1);
								$nestedData[] = job_model_details($row["model_id"],'1',$_REQUEST['daterange'],$link1);
									$nestedData[] = job_model_details($row["model_id"],'6',$_REQUEST['daterange'],$link1);
										$nestedData[] = job_model_details($row["model_id"],'18',$_REQUEST['daterange'],$link1);

		                               $nestedData[] = job_model_details($row["model_id"],'14',$_REQUEST['daterange'],$link1);
										$nestedData[] = job_model_details($row["model_id"],'5',$_REQUEST['daterange'],$link1);
										$nestedData[] = job_model_details($row["model_id"],'34',$_REQUEST['daterange'],$link1);
										$nestedData[] = job_model_details($row["model_id"],'2',$_REQUEST['daterange'],$link1);
										$nestedData[] = job_model_details($row["model_id"],'27',$_REQUEST['daterange'],$link1);
											$nestedData[] = job_model_details($row["model_id"],'24',$_REQUEST['daterange'],$link1);
												$nestedData[] = job_model_details($row["model_id"],'35',$_REQUEST['daterange'],$link1);
												
												$nestedData[] = job_model_details($row["model_id"],'17',$_REQUEST['daterange'],$link1);
										$nestedData[] = job_model_details($row["model_id"],'13',$_REQUEST['daterange'],$link1);
										$nestedData[] = job_model_details($row["model_id"],'12',$_REQUEST['daterange'],$link1);
										$nestedData[] = job_model_details($row["model_id"],'26',$_REQUEST['daterange'],$link1);
										$nestedData[] = job_model_details($row["model_id"],'28',$_REQUEST['daterange'],$link1);
											$nestedData[] = job_model_details($row["model_id"],'29',$_REQUEST['daterange'],$link1);
												$nestedData[] = job_model_details($row["model_id"],'30',$_REQUEST['daterange'],$link1);
													$nestedData[] = job_model_details($row["model_id"],'32',$_REQUEST['daterange'],$link1);
														$nestedData[] = job_model_details($row["model_id"],'21',$_REQUEST['daterange'],$link1);
															$nestedData[] = job_model_details($row["model_id"],'11',$_REQUEST['daterange'],$link1);
															$nestedData[] = job_model_details($row["model_id"],'25',$_REQUEST['daterange'],$link1);
															$nestedData[] = job_model_details($row["model_id"],'31',$_REQUEST['daterange'],$link1);


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
