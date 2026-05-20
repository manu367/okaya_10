<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
$today1=date('Y-m-d');
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'brand_id'

);
// getting total number records without any search
$sql = "SELECT *, count(id) as jobcounter";
$sql.=" FROM auto_part_request  where status = '3' and location_code='".$_SESSION['asc_code']."' group by brand_id ";
$query=mysqli_query($link1, $sql) or die("pna-grid-data.php: Er1");

$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *,count(id) as jobcounter";
$sql.=" FROM auto_part_request where status = '3' and location_code='".$_SESSION['asc_code']."' ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (brand_id LIKE '".$requestData['search']['value']."%' )";    
}
$query=mysqli_query($link1, $sql) or die("pna-grid-data.php: Er2");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" GROUP BY brand_id ";
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("pna-grid-data.php: Er3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) { 
 // preparing an array
	$nestedData=array();     
	$nestedData[] = "<div align='center'>".$j."</div>"; 
	$nestedData[] = "<div align='center'>".getAnyDetails($row["brand_id"],"brand","brand_id","brand_master",$link1)."</div>";
	$nestedData[] = "<div align='center'>".$row['jobcounter']."</div>";
	$nestedData[] = "<div align='center'><a href='inventory_pna_bucket.php?brand=".base64_encode($row["brand_id"])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view'></i></a></div>";
		
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

