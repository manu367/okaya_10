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

## selected  status

if($_REQUEST['status'] != ""){

	$status = "status = '".$_REQUEST['status']."'";

}else{

	$status = "1";

}

$columns = array( 

// datatable column index  => database column name
    0 => 'id',
	
	1 => 'party_name', 

	2 => 'system_ref_no',

	3 => 'entry_date',

	4 => 'total_amt',

	5 => 'status'

);

// getting total number records without any search

$sql = "SELECT party_name,system_ref_no,status,total_amt,entry_date";

$sql.=" FROM supplier_po_master where location_code='".$_SESSION['asc_code']."' and ".$status." and ".$daterange." ";

$query=mysqli_query($link1, $sql) or die("grn-vendor-grid-data.php: get  details");

$totalData = mysqli_num_rows($query);

$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.





$sql = "SELECT party_name,system_ref_no,status,total_amt,entry_date";

$sql.=" FROM supplier_po_master where  location_code='".$_SESSION['asc_code']."' and ".$status." and ".$daterange." ";

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter

	$sql.=" AND (system_ref_no LIKE '".$requestData['search']['value']."%'";    

	$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";

}

$query=mysqli_query($link1, $sql) or die("grn-vendor-grid-data.php: get  details");

$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 

$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	

$query=mysqli_query($link1, $sql) or die("grn-vendor-grid-data.php: get  details");



$data = array();

$j=1;

while( $row=mysqli_fetch_array($query) ) { 

 // preparing an array

	$nestedData=array(); 

    

	$nestedData[] = $j; 

	$nestedData[] = getAnyDetails($row["party_name"],"name","id","vendor_master",$link1);

	$nestedData[] = $row["system_ref_no"];

	$nestedData[] = dt_format($row['entry_date']);

	$nestedData[] = $row['total_amt'];

	$nestedData[] = getdispatchstatus($row["status"]);

	$nestedData[] = "<div align='center'><a href='grn_vendor_print.php?refid=".base64_encode($row['system_ref_no'])."&daterange=".$_REQUEST['daterange']."&status=".$row['status']."".$pagenav."' title='print' target='_blank'><i class='fa fa-print fa-lg faicon' title='print po details'></i></a></div>";

	$nestedData[] = "<div align='center'><a href='grnvendor_view.php?refid=".base64_encode($row['system_ref_no'])."&daterange=".$_REQUEST['daterange']."&status=".$row['status']."".$pagenav."' title='view'><i class='fa fa-eye fa-lg faicon' title='view po details'></i></a></div>";

	

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

