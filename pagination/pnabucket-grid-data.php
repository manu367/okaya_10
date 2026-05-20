<?php
/* Database connection start */
require_once("../includes/config.php");
/* Database connection end */
$brand = $_REQUEST['brandid'];

$today1=date('Y-m-d');
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
$columns = array( 
// datatable column index  => database column name
	0 => 'id',
	1 => 'partcode', 
	2 => 'job_no',
	3 => 'request_date'

);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM auto_part_request  where status = '3' and location_code='".$_SESSION['asc_code']."' and brand_id = '".$brand."' ";
$query=mysqli_query($link1, $sql) or die("pna-grid-data.php: Er1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM auto_part_request where status = '3' and location_code='".$_SESSION['asc_code']."'  and brand_id = '".$brand."' ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (partcode LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR job_no LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR request_date LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("pna-grid-data.php: Er2");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("pna-grid-data.php: Er3");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) { 
$st=mysqli_query($link1,"select okqty from client_inventory where location_code='".$_SESSION['asc_code']."' and partcode='".$row["partcode"]."' ");
if(mysqli_num_rows($st)>0){
$st_s=mysqli_fetch_array($st);
$stock=$st_s['0'];
}
else{
	$stock='0';
	}
if($row["remark"] != "Cancelled") { $str = "<div align='center'><a href='#' title='cancel this PNA part' onClick=cancelPNAPart('".$row["id"]."')><i class='fa fa-trash fa-lg faicon' title='cancel this PNA part'></i></a></div>"; }

 // preparing an array
	$nestedData=array();     
	$nestedData[] = $j; 
	$nestedData[] = $row["partcode"] ;
	$nestedData[] = $stock;
	$nestedData[] = getAnyDetails($row["partcode"],"part_name","partcode","partcode_master",$link1);
	$nestedData[] = $row['job_no'];
	$nestedData[] = $row['request_date'];
	$nestedData[] = $str;
	$nestedData[] = "<div align='center'><input id='checkBox'  type='checkbox'    name='pnarow[]'  value='".$row['id']."'>
	  <input id='job_no'  type='hidden'    name='job_no$row[id]'  value='".$row['job_no']."'><input id='partcode'  type='hidden'    name='partcode$row[id]'  value='".$row['partcode']."'><input id='product'  type='hidden'    name='product$row[id]'  value='".$row['product_id']."'><input id='brand'  type='hidden'    name='brand$row[id]'  value='".$row['brand_id']."'><input id='model'  type='hidden'    name='model$row[id]'  value='".$row['model_id']."'></div>";
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

