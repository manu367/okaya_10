<?php
/* Database connection start */
require_once("../includes/config.php");
/////get status//
$arrstatus = getFullStatus("master",$link1);
$arrstatus[99] = "On Hold";

$requestData= $_REQUEST;

$statusbox = [ "1"=> "Active", "2"=>"Deactive", "99"=>"On Hold" ];

$columns = array( 
// datatable column index  => database column name
	0 => 'id'
);

// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM status_log WHERE 1";
$query = mysqli_query($link1, $sql) or die("admin-grid-data.php: get admin users");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT *";
$sql.=" FROM status_log WHERE 1";
if(!empty($requestData['search']['value']))
{
	$sql.=" AND (userid LIKE '".$requestData['search']['value']."%')";
}
$query=mysqli_query($link1, $sql) or die("admin-grid-data.php: get admin users");
$totalFiltered = mysqli_num_rows($query);

$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query=mysqli_query($link1, $sql) or die("admin-grid-data.php: get admin users");

$data = array();
$j=1;
while($row=mysqli_fetch_array($query))
{
	$nestedData=array(); 
     
	$nestedData[] = $row["id"]; 
	$nestedData[] = $row["userid"];
	$nestedData[] = $statusbox[$row["status"]];
	$nestedData[] = $row["create_by"];
	$nestedData[] = $row["create_dt"];

	$data[] = $nestedData;
	$j++;
}

$json_data = array(
	"draw"            => intval( $requestData['draw'] ),
	"recordsTotal"    => intval( $totalData ),
	"recordsFiltered" => intval( $totalFiltered ),
	"data"            => $data
);
exit(json_encode($json_data));
?>
