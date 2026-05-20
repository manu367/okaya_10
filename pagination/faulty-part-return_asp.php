<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
$today1=date('Y-m-d');
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Date range

## selected  status 
if($_REQUEST['status'] != ""){
	$status = "eng_status = '".$_REQUEST['status']."'";
}else{
	$status = " eng_status in ('1')";
}


## selected  location
if($_REQUEST['location_code'] != ""){
	$locationcode = "eng_id = '".$_REQUEST['location_code']."'";
}else{
	$locationcode = "1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'sno', 
	1 => 'eng_id'
	
);
// getting total number records without any search
  $sql = "SELECT *";
  $sql.=" FROM part_to_credit where  ".$status." and ".$locationcode." and from_location ='".$_SESSION['asc_code']."'  ";
$query=mysqli_query($link1, $sql) or die("dispatchpopna-grid-data.php: get job details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql =  "SELECT *";
 $sql.=" FROM part_to_credit where  ".$status."  and ".$locationcode."  and from_location ='".$_SESSION['asc_code']."' ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (eng_id LIKE '".$requestData['search']['value']."%'"; 
	$sql.=" OR job_no LIKE '".$requestData['search']['value']."%'"; 
     $sql.=" OR partcode  LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("dispatchpopna-grid-data.php: get job details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("dispatchpopna-grid-data.php: get job details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) { 
 // preparing an array
 
 if($row["eng_status"] == 1) { 
 $str = "<div align='center'><input id='checkBox'  type='checkbox'    name='partrow[]'  value='".$row['sno']."'></div>"; 
 $stock_type= "<div align='center'> <select name='stock_type$row[sno]' id='stock_type$row[sno]' class='form-control' style='width:150px;'><option value='OK'>OK</option><option value='Damage'>Damage</option><option value='Missing'>Missing</option></select></div>";
 $rece_date="";
 }else{
 $str = getdispatchstatus($row['eng_status']); 
 $stock_type= $row["stock_type"];  
  $rece_date=dt_format($row["eng_rec_date"]); 

 }
	$nestedData=array(); 
    
	$nestedData[] = $j; 
	$nestedData[] = $row["partcode"];
	$nestedData[] = getAnyDetails($row['partcode'],"part_name","partcode" ,"partcode_master",$link1);
	$nestedData[] = $row['job_no'];
	$nestedData[] = dt_format($row["consumedate"]);
	$nestedData[] = getAnyDetails($row["eng_id"],"locusername","userloginid","locationuser_master",$link1);
	$nestedData[] = $stock_type;
	$nestedData[] =$rece_date;
	$nestedData[] = $str;
	
	

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
