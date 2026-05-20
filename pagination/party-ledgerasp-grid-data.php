<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Date range
$date_range = explode(" - ",$_REQUEST['daterange']);
if($_REQUEST['daterange'] != ""){
	$daterange = "entry_date  >= '".$date_range[0]."' and entry_date  <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}

## selected  location
if($_REQUEST['location']!=""){
	$location_code="location_code='".$_REQUEST['location']."'";
}else{
	$location_code="1";
}

$columns = array( 
// datatable column index  => database column name
	0 => 'location_code', 
	1 => 'transaction_type', 
	2 => 'crdr',
	3 => 'entry_date',
	4 => 'remark'
	
);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM location_account_ledger where ".$location_code."  and  ".$daterange." ";
$query=mysqli_query($link1, $sql) or die("party-ledger-grid-data.php: get party details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM location_account_ledger where ".$location_code."  and  ".$daterange." ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( location_code LIKE '".$requestData['search']['value']."%')";  
}
$query=mysqli_query($link1, $sql) or die("party-ledger-grid-data.php: get party details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("party-ledger-grid-data.php: get party details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) { 
//////////////////// get amount on basis on cr/dr ////////////////////////////`
if ($row[crdr] == "CR" ) { 
$cr_amt = $row["amount"];  $dr_amt = "0" ;}
else { $dr_amt = $row["amount"];  $cr_amt = "0";  }

 // preparing an array
	$nestedData=array(); 
    
	$nestedData[] = $j; 
	$nestedData[] = $row["remark"];
	$nestedData[] = $row["transaction_type"];
	$nestedData[] = $row["entry_date"];
	$nestedData[] = $cr_amt;
	$nestedData[] = $dr_amt;
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
