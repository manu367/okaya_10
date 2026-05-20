<?php
/* Database connection start */
require_once("../includes/config.php");

$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
	0 => 'id'
);

// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM permission_log WHERE 1";
$query = mysqli_query($link1, $sql) or die("admin-grid-data.php: get admin users");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT *";
$sql.=" FROM permission_log WHERE 1";
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
	$nestedData[] = $row["type"];
	$nestedData[] = $row["create_by"];
	$nestedData[] = $row["create_dt"];
	$nestedData[] = $row["create_ip"];
	$nestedData[] = '<div style="text-align:center;cursor:pointer;"><a onclick="loadItem(\''.base64_encode($row["type"]).'\', \''.$row["userid"].'\', \''.base64_encode($row["id"]).'\', \''.base64_encode($row["create_dt"]).'\')"><i class="fa fa-eye" aria-hidden="true"></i></a></div>';

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
