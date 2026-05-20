<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
$columns = array( 
// datatable column index  => database column name
	0 => 'name', 
	1 => 'city',
	2 => 'state',
	3 => 'country',
	4 => 'phone',
	5 => 'email',
	6 => 'address'
);

// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM vendor_master";
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("supplier-grid-data.php: get state master");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM vendor_master";
if( !empty($requestData['search']['value']) ) { 
// if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( name LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR address LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("supplier-grid-data.php: get state master");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("supplier-grid-data.php: get state master");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$city = getAnyDetails($row["city"],"city","cityid","city_master",$link1);
	$stat = getAnyDetails($row["state"],"state","stateid","state_master",$link1);
	if($city){ $cityname=$city;}else{ $cityname=$row["city"];}
	if($stat){ $statename=$stat;}else{ $statename=$row["state"];}
	if($row['vendor_orign']=="Domestic"){ $edit_link="add_localsupplier.php";}else{ $edit_link="add_supplier.php";}
	$nestedData=array(); 
    
	$nestedData[] = $j; 
	$nestedData[] = $row["name"];
	$nestedData[] = $cityname;
	$nestedData[] = $statename;
	$nestedData[] = $row["country"];
	$nestedData[] = $row["phone"];
	$nestedData[] = $row["email"];
	$nestedData[] = $row["address"];
	$nestedData[] = "<div align='center'><a href='".$edit_link."?op=Edit&id=".$row['id']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view/edit supplier details'></i></a></div>";
	
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
