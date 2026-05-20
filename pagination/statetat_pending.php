<?php
/* Database connection start */
require_once("../includes/config.php");
/////get status//
$arrstatus = getJobStatus($link1);
$arrstate = getAccessState($_SESSION['userid'],$link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Date range
$date_range = explode(" - ",$_REQUEST['daterange']);
if($_REQUEST['daterange'] != ""){
	$daterange = "open_date >= '".$date_range[0]."' and open_date <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}
## selected  product name
if($_REQUEST['state'] != ""){
	$state = "stateid = '".$_REQUEST['state']."'";
	$state_id = "state_id = '".$_REQUEST['state']."'";
}else{
	$state = "stateid in (".$arrstate.")";
	$state_id = "state_id in (".$arrstate.")";
}
## selected  product name

function job_state_details($type,$state,$daterange,$link1){
//echo "select count(job_id) as job_count from jobsheet_data where status='".$status."'  and eng_id='".$eng_name."' ";

$date_range = explode(" - ",$daterange);
if($daterange != ""){
	$daterange_open= "open_date >= '".$date_range[0]."' and open_date <= '".$date_range[1]."'";

}else{
	$daterange_open = "1";
	
}
if($type=="COM"){

$status="status in('6','48','49','10','11')";
}else if($type=="OPEN"){

$status="status in('1','55','56')";
}
else if($type=="Assign"){

$status="status in('2')";
}
else if($type=="CANCEL"){

$status="status in('12')";
}
else if($type=="Replacement"){

$status="status in('8')";
}
else if($type=="GT"){

$status="1";
}
else if($type=="PEN"){
$status="status  in('3','5','7','50')";
}

//echo "select count(job_id) as job_count from jobsheet_data where state_id='".$state."' and ".$status." and ".$daterange_open."";

$res_eng_p = mysqli_query($link1,"select count(job_id) as job_count from jobsheet_data where state_id='".$state."' and ".$status." and ".$daterange_open." ");

$row_count = mysqli_fetch_array($res_eng_p);
if($row_count['job_count']!=''){
$count_job=$row_count['job_count'];

}else{
$count_job=0;
}

return $count_job;
}



function job_state_details_tat($type,$state,$daterange,$link1){
$date_range = explode(" - ",$daterange);
if($daterange != ""){
	$daterange_open= "open_date >= '".$date_range[0]."' and open_date <= '".$date_range[1]."'";

}else{
	$daterange_open = "1";
	
}

//echo "select datediff(close_date,open_date) as ageing from jobsheet_data where ".$state_id."  and close_date!='0000-00-00' and ".$daterange."";

//echo "select count(job_id) as job_count from jobsheet_data where state_id='".$state."' and close_date!='0000-00-00' and ".$daterange_open." and  close_tat<=2";
$res_jd = mysqli_query($link1,"select count(job_id) as job_count from jobsheet_data where state_id='".$state."' and close_date!='0000-00-00' and ".$daterange_open." and  close_tat<3  and status!='12'");

$rowcount=mysqli_num_rows($res_jd);

$row_count = mysqli_fetch_array($res_jd);
if($row_count['job_count']!=''){
$count_job=$row_count['job_count'];

}else{
$count_job=0;
}

return $count_job;
}
## selected  model

## selected  Status

$columns = array( 
// datatable column index  => database column name
	0 => 'stateid', 
	1 => 'state'

);
// getting total number records without any search
 $sql = "SELECT *";
 $sql.=" FROM state_master where 1 and ".$state;
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("Err1".mysqli_error($link1));
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT *";
$sql.=" FROM state_master where 1 and ".$state;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (stateid LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR state LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("Err2".mysqli_error($link1));
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("Err3".mysqli_error($link1));

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array


$row1=job_state_details("COM",$row["stateid"],$_REQUEST['daterange'],$link1);
$row2=job_state_details("OPEN",$row["stateid"],$_REQUEST['daterange'],$link1);
$row3=job_state_details("Assign",$row["stateid"],$_REQUEST['daterange'],$link1);
$row4=job_state_details("PEN",$row["stateid"],$_REQUEST['daterange'],$link1);
$row5= job_state_details("CANCEL",$row["stateid"],$_REQUEST['daterange'],$link1);
$row6= job_state_details("Replacement",$row["stateid"],$_REQUEST['daterange'],$link1);
$row7= job_state_details("GT",$row["stateid"],$_REQUEST['daterange'],$link1);
$interval2= job_state_details_tat("GT",$row["stateid"],$_REQUEST['daterange'],$link1);

$row8=$row1+$row6;

if($interval2==0 && $row8==0){
$row_per=0;
}
else if($interval2>0 && $row8>0){

$row_per=($interval2/$row8)*100;
}

else{
$row_per=0;
}
	$nestedData=array();	
	$nestedData[] = $j; 
	$nestedData[] = $row["state"];

	$nestedData[] = $row1;
     $nestedData[] = $row2;
	$nestedData[] = $row3;
    $nestedData[] = $row4;
	$nestedData[] = $row5;
$nestedData[] = $row6;	
$nestedData[] = $interval2;		
$nestedData[] = $row7;		
$nestedData[] = round($row_per);	


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
