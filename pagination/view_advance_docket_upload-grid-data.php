<?php
 	/* Database connection start */
 	require_once("../includes/config.php");

 	$requestData= $_REQUEST;
		
//////////////////////////////////////////////////////////////////////////////////////30/09/2021

$columns = array( 
	// datatable column index  => database column name
		0 => 'id', 
		1 => 'docket_no',
		2 => 'docket_company',
		3 => 'mode_of_transport',
		4 => 'response_msg'
	);
	
	// getting total number records without any search
	$sql = "SELECT id ";
		$sql.="FROM advance_docket_upload where doc_no='".$_REQUEST['doc_no']."'";
	//echo $sql;
	
	$query=mysqli_query($link1, $sql) or die("job-grid-data.php1: get job details");

	$totalData = mysqli_num_rows($query);

	$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


	$sql = "SELECT *";
	$sql.=" from advance_docket_upload where doc_no='".$_REQUEST['doc_no']."'";
	
	if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter

		$sql.=" AND (mode_of_transport LIKE '".$requestData['search']['value']."%'";    
		$sql.=" OR docket_no LIKE '".$requestData['search']['value']."%'";
        $sql.=" OR docket_company LIKE '".$requestData['search']['value']."%'";
		$sql.=" OR response_msg LIKE '".$requestData['search']['value']."%')";
		
	}
	 //echo $sql;
	$query=mysqli_query($link1, $sql) or die("advance_docket_uploader-grid-data.php: get job details");
	$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
	/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
	// echo $sql;
	$query=mysqli_query($link1, $sql) or die("job-grid-data.php3: get job details");

	$data = array();
	$j=1;
	while( $row=mysqli_fetch_array($query) ) {  // preparing an array
		$nestedData=array(); 
		$nestedData[] = $j; 
		$nestedData[] = $row["docket_no"];
		$nestedData[] = $row["docket_company"];
		$nestedData[] = $row["mode_of_transport"];
		$nestedData[] = $row["response_msg"];		
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