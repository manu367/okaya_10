<?php

/* Database connection start */

require_once("../includes/config.php");

/* Database connection end */

// storing  request (ie, get/post) global array to a variable  

$requestData= $_REQUEST;

$columns = array( 

// datatable column index  => database column name

	0 => 'id',

	1 => 'location_code', 

	2 => 'entry_by',

	3 => 'system_ref_no',

	4 => 'entry_date'

);



// getting total number records without any search

$sql = "SELECT location_code,entry_by,system_ref_no,entry_date";

$sql.=" FROM stock_adjust_master where status = 'PROCESSED'  and type = 'admin adjust' ";

$query=mysqli_query($link1, $sql) or die("adminstock_adjustment_admin.php: get  master");

$totalData = mysqli_num_rows($query);

$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.





$sql = "SELECT location_code,entry_by,system_ref_no,entry_date";

$sql.=" FROM stock_adjust_master where status = 'PROCESSED'  and type = 'admin adjust' ";

if( !empty($requestData['search']['value']) ) { 

// if there is a search parameter, $requestData['search']['value'] contains search parameter

	$sql.=" AND ( system_ref_no LIKE '".$requestData['search']['value']."%'";    

	$sql.=" OR entry_by LIKE '".$requestData['search']['value']."%' )";

}

$query=mysqli_query($link1, $sql) or die("adminstock_adjustment_admin.php: get state master");

$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 

$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";



/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	

$query=mysqli_query($link1, $sql) or die("adminstock_adjustment_admin.php: get state master");



$data = array();

$j=1;

while( $row=mysqli_fetch_array($query) ) {



  // preparing an array

	$nestedData=array(); 

	$nestedData[] = $j; 

	$nestedData[] = getAnyDetails($row["location_code"],"locationname" ,"location_code" , "location_master",$link1);

	$nestedData[] = $row["entry_by"];

	$nestedData[] = $row["system_ref_no"];

	$nestedData[] = dt_format($row["entry_date"]);

	$nestedData[] = "<div align='center'><a href='stock_adjust_view_admin.php?refid=".base64_encode($row['system_ref_no'])."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view stock adjust'></i></a></div>";



	

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



