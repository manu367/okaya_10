<?php
	/* Database connection start */
	require_once("../includes/config.php");
	/////get status//
	$arrstatus = getJobStatus($link1);
	/* Database connection end */
	// storing  request (ie, get/post) global array to a variable  
	$requestData= $_REQUEST;
	## selected  Date range
	//$date_range = explode(" - ",$_REQUEST['daterange']);

	//////// get date /////////////////////////
	if ($_REQUEST['daterange'] != ""){
		$seldate = explode(" - ",$_REQUEST['daterange']);
		$fromdate = $seldate[0];
		$todate = $seldate[1];
		$daterange=" (DATE(doc_date) >= '".$fromdate."' and DATE(doc_date) <='".$todate."') ";
	}else{
		$seldate = $today;
		$fromdate = $today;
		$todate = $today;
		$daterange=" (DATE(doc_date) >= '".$fromdate."' and DATE(doc_date) <='".$todate."') ";
	}
	if($_REQUEST['status']!=""){

		$status="status='".$_REQUEST['status']."'";
	
	}else{
	
		$status="1";
	
	}
	

	
////////////////////////////////////////////////////30/09/2021/////////////////////////////////////////
		## selected  asp name
		if($_REQUEST['assign_to'] != ""){
			$aspname="assign_to='".$_REQUEST['assign_to'] ."'";
			//$aspname = "1";
		}else{
			$aspname = "1";
		}
		
//////////////////////////////////////////////////////////////////////////////////////30/09/2021

$columns = array( 
	// datatable column index  => database column name
		0 => 'id', 
		1 => 'assign_from',
		2 => 'assign_to',
		3 => 'doc_no',
		4 => 'doc_date',
		5 => 'status'
	
	);
	
	// getting total number records without any search
	$sql = "SELECT id";
		$sql.=" FROM advance_docket_assign where  1  and ".$daterange." and ".$aspname." and ".$status;
	
	
	$query=mysqli_query($link1, $sql) or die("job-grid-data.php1: get job details");

	$totalData = mysqli_num_rows($query);

	$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


	$sql = "SELECT *";
	$sql.=" FROM advance_docket_assign where  1  and ".$daterange." and ".$aspname." and ".$status;
	
	if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter

		$sql.=" AND (assign_from LIKE '".$requestData['search']['value']."%'";    
		$sql.=" OR assign_to LIKE '".$requestData['search']['value']."%'";
		$sql.=" OR doc_date LIKE '".$requestData['search']['value']."%'";
		$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
	}
	// echo $sql;
	$query=mysqli_query($link1, $sql) or die("advance_docket_uploader-grid-data.php2: get job details");
	$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
	/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
	// echo $sql;
	$query=mysqli_query($link1, $sql) or die("job-grid-data.php3: get job details");

	$data = array();
	$j=1;
	while( $row=mysqli_fetch_array($query) ) {  // preparing an array
		$nestedData=array(); 
		$whname = getAnyDetails($row["assign_from"],"locationname","location_code","location_master",$link1);
		$aspname = getAnyDetails($row["assign_to"],"locationname","location_code","location_master",$link1);
		if($aspname){}else{ $aspname = $row["assign_to"];}

		$nestedData[] = $j; 
		$nestedData[] = $whname." ".$row["assign_from"];
		$nestedData[] = $aspname." ".$row["assign_to"];
		$nestedData[] = $row["doc_no"];
		$nestedData[] = $row["doc_date"];
		$nestedData[] = $row["status"];
		$nestedData[] = "<div align='center'><a href='advance_docket_print.php?refid=".base64_encode($row['id'])."&daterange=".$_REQUEST['daterange']."&status=".$_REQUEST['status']."&assign_to=".$_REQUEST['assign_to']."".$pagenav."' title='print' target='_blank'><i class='fa fa-print fa-lg faicon' title='print advance docket details'></i></a></div>";
		
		$nestedData[] = "<div align='center'><a href='view_advance_docket_upload.php?refid=".base64_encode($row['id'])."&daterange=".$_REQUEST['daterange']."&status=".$_REQUEST['status']."&assign_to=".$_REQUEST['assign_to']."".$pagenav."' title='view uploaded details'><i class='fa fa-eye fa-lg faicon' title='view uploaded details'></i></a></div>";		
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