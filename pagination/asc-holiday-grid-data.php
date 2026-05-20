<?php
/* Database connection start */
require_once("../includes/config.php");
/////get status//
$arrstatus = getFullStatus("master",$link1);
///// get operation rights
//$get_opr_rgts = getOprRights($_SESSION['userid'],$_REQUEST['pid'],$link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Status
if(!empty($_REQUEST['status'])){
	$status=" and status='".$_REQUEST['status']."'";
}else{
	$status=" ";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'sno',
	1 => 'date', 
	2 => 'description',
	3 => 'status'
);
// getting total number records without any search
$sql = "SELECT sno,date,description,status,weekly,start_time,end_time";
$sql.=" FROM holidays where location_code='".$_SESSION['asc_code']."' $status";

$query=mysqli_query($link1, $sql) or die("holiday-grid-data.php: get holiday details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT sno,date,weekly,start_time,end_time,description,status";
$sql.=" FROM holidays where location_code='".$_SESSION['asc_code']."' $status";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( description LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR date LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("asc-holiday-grid-data.php: get holidays details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("asc-holiday-grid-data.php: get holiday details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) { 
 ////// check this user have right to view the details
    if($row["status"]=="1"){
	$viewicon ="<div align='center'><a href='asc_add_holiday.php?op=Edit&id=".$row['sno']."&status=".$_REQUEST['status']."&p_dop=".$row['date']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit holiday details'></i></a></div>" ;
	  }else{
        $viewicon = "";
    }
	if($row["description"]=='Daily Working Time'){
	$timing_detils=$row["weekly"]."(".$row["start_time"]." TO ".$row["end_time"].")";
	}
	else{
		$timing_detils=$row["date"];
		}
 // preparing an array
	$nestedData=array(); 
    $nestedData[] = $j; 
	$nestedData[] = $timing_detils;
	$nestedData[] = $row["description"];
	$nestedData[] = $arrstatus[$row["status"]];
	//$nestedData[] = $viewicon;
	
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
