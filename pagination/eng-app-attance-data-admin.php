<?php
/* Database connection start */
require_once("../includes/config.php");
/////// get Access state////////////////////////
$arrstate = getAccessState($_SESSION['userid'],$link1);

$requestData= $_REQUEST;
## selected location
$loc=base64_decode($_POST['loc']);
if($loc == "All"){
	$loc_str = "1";
}else{
	$loc_str = $loc;
}
## selected eng
$eng=base64_decode($_POST['eng']);
if($eng == ""){
	if($loc_str == "1"){
		$eng_str = " user_id in (select userloginid from locationuser_master where location_code in (select location_code from location_master where stateid in ($arrstate) )) ";
	}else{
		$eng_str = " user_id in (select userloginid from locationuser_master where location_code = '".$loc_str."') ";
	}
}else{
	$eng_str = " user_id = '".$eng."'";
}

if($loc=="" && $eng==""){
	$eng_str = " user_id in (select userloginid from locationuser_master where location_code in (select location_code from location_master where stateid in ($arrstate) )) ";
} 

$columns = array( 
// datatable column index  => database column name
	0 => 'id', 
	1 => 'in_datetime',
	2 => 'out_datetime'
);

// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM mic_attendence_data where ".$eng_str." ";
$query=mysqli_query($link1, $sql) or die("bank-grid-data.php: get bank master");

//echo $sql;

$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM mic_attendence_data where ".$eng_str." ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( address_in 	 LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR address_out  LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("bank-grid-data.php: get bank master");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

//echo $sql;

$query=mysqli_query($link1, $sql) or die("bank-grid-data.php: get bank master");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
     
	$nestedData[] = $j; 
	$nestedData[] = getAnyDetails($row["user_id"],"locusername","userloginid","locationuser_master",$link1);
	$nestedData[] = $row["in_datetime"];
	$nestedData[] = $row["address_in"];
	$nestedData[] = $row["out_datetime"];
	$nestedData[] = $row["address_out"];

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
