<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
## selected  location
$requestData= $_REQUEST;
 if(is_array($_REQUEST['location'])){
	$locationstr="";
	$post_locationarr = $_REQUEST['location'];
	for($i=0; $i<count($post_locationarr); $i++){
		if($locationstr){
			$locationstr .= ",'".$post_locationarr[$i]."'";
		}else{
			$locationstr .= "'".$post_locationarr[$i]."'";
		}
	}
	$location_code=" location_code in (".$locationstr.")";
}else{
	
}

## selected  state
 if(is_array($_REQUEST['state'])){
	$statestr="";
	$post_statearr = $_REQUEST['state'];
	for($i=0; $i<count($post_statearr); $i++){
		if($statestr){
			$statestr .= ",'".$post_statearr[$i]."'";
		}else{
			$statestr .= "'".$post_statearr[$i]."'";
		}
	}
	$stateid=" stateid in (".$statestr.")";
}else{
	
}


$columns = array( 
// datatable column index  => database column name
	0 => 'location_code', 
	1 => 'total_credit_limit', 
	2 => 'security_amt', 
	3 => 'claim_amt', 
	4 => 'last_updated'
);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM current_cr_status where   ".$location_code."";
$query=mysqli_query($link1, $sql) or die("party-account-data.php: get party details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM current_cr_status where   ".$location_code."";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( location_code LIKE '".$requestData['search']['value']."%')";  
}
$query=mysqli_query($link1, $sql) or die("party-account-data.php: get party details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("party-account-data.php: get party details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) { 
/////////////////// select state, city of location_code ////////////////////////////
$sql = mysqli_fetch_array(mysqli_query($link1,"select stateid, cityid from location_master where location_code = '".$row['location_code']."'  and ".$stateid."  "));

 // preparing an array
	$nestedData=array(); 
    
	$nestedData[] = $j; 
	$nestedData[] = getAnyDetails($sql['stateid'],"state","stateid","state_master",$link1);
	$nestedData[] = getAnyDetails($sql['cityid'],"city","cityid","city_master",$link1);
	$nestedData[] = getAnyDetails($row["location_code"],"locationname","location_code","location_master",$link1);
	$nestedData[] = $row["location_code"];
	$nestedData[] = $row["claim_amt"];
	$nestedData[] = $row["total_credit_limit"];
	$nestedData[] = $row["security_amt"];
		$nestedData[] = "<div align='center'><a href='../excelReports/partyledger_excel_claim.php?location=".$row["location_code"]."".$pagenav."' title='Export Part Ledger in excel'><i class='fa fa-file-excel-o fa-2x faicon' title='Export Part Ledger in excel'></i></a></div>";
		
	$nestedData[] = "<div align='center'><a href='excelexport.php?rname=".base64_encode("partyledger_excel")."&location=".$row["location_code"]."&daterange=".$_REQUEST["daterange"]."".$pagenav."' title='Export Part Ledger in excel'><i class='fa fa-file-excel-o fa-2x faicon' title='Export Part Ledger in excel'></i></a></div>";

			$nestedData[] = "<div align='center'><a href='../excelReports/partyledger_excel_sec.php?location=".$row["location_code"]."".$pagenav."' title='Export Part Ledger in excel'><i class='fa fa-file-excel-o fa-2x faicon' title='Export Part Ledger in excel'></i></a></div>";
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
