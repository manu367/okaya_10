<?php
/* Database connection start */
require_once("../includes/config.php");
/////get status//
$arrstatus = getFullStatus("master",$link1);
$arrstatus[99] = "On Hold";
	
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

## selected  Status
/*
if($_REQUEST['status']=='')
{
	$status="status='1'";
}
else if($_REQUEST['status']=="2")
{
	$status="status='2'";
}
//else if($_REQUEST['status']=="99"){
//	$status="status='99'";
//}
else{
	$status="1";
}
*/

if($_REQUEST['status']!="")
{
	if($_REQUEST['status']=='1')
	{
		$status = "status=1";
	}
	elseif($_REQUEST['status']=='2')
	{
		$status = "status=2";
	}
	elseif($_REQUEST['status']=='99')
	{
		$status = "status=99";
	}
}
else
{
	$status = "status IN ('1','2','99')";
}

if($_SESSION['userid']=="test"){
	$checkmainadmin = "";
}else{
	$checkmainadmin = " and username!='test'";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'username', 
	1 => 'name',
	2 => 'utype',
	3 => 'phone',
	4 => 'emailid',
	5 => 'status',
	6 => 'emailid'
);

// getting total number records without any search
$sql = "SELECT sapid, username, name, utype, phone, emailid, status";
$sql.=" FROM admin_users where $status $checkmainadmin";
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";

//exit($sql);

$query=mysqli_query($link1, $sql) or die("admin-grid-data.php: get admin users");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT sapid, username, name, utype, phone, emailid, status";
$sql.=" FROM admin_users where $status $checkmainadmin";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( sapid LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR username LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR name LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR utype LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR phone LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR emailid LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("admin-grid-data.php: get admin users");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("admin-grid-data.php: get admin users");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
     
	$nestedData[] = $j; 
	$nestedData[] = $row["username"];
	$nestedData[] = $row["name"];
	$nestedData[] = $row["utype"];
	$nestedData[] = $row["phone"];
	$nestedData[] = $row["emailid"];
	$nestedData[] = $arrstatus[$row["status"]];
	$nestedData[] = "<div align='center'><a href='addAdminUser.php?op=edit&id=".$row['username']."&status=".$_REQUEST['status']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit details'></i></a></div>";
	
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
