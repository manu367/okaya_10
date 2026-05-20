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
if($_REQUEST['state'] == ""){
	$state = "stateid in (".$arrstate.")";
}else{
	$state = "stateid ='".$_REQUEST['state']."'";
}
## selected  product name

function job_state_details($type,$loc,$daterange,$link1){
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

$res_eng_p = mysqli_query($link1,"select count(job_id) as job_count from jobsheet_data where current_location='".$loc."' and ".$status." and ".$daterange_open." ");

$row_count = mysqli_fetch_array($res_eng_p);
if($row_count['job_count']!=''){
$count_job=$row_count['job_count'];

}else{
$count_job=0;
}

return $count_job;
}


function job_asp_aging($type,$loc,$daterange,$tdat,$link1){
//echo "select count(job_id) as job_count from jobsheet_data where status='".$status."'  and eng_id='".$eng_name."' ";

$date_range = explode(" - ",$daterange);
if($daterange != ""){
	$daterange_open= "open_date >= '".$date_range[0]."' and open_date <= '".$date_range[1]."'";

}else{
	$daterange_open = "1";
	
}
$p_interval1 = 0;
$p_interval2 = 0;
$p_interval3 = 0;
$p_interval4 = 0;
$p_interval5 = 0;

$res_jd_p = mysqli_query($link1,"select datediff(close_date,open_date) as ageing from jobsheet_data where  current_location='".$loc."' and close_date!='0000-00-00' and status!='12' and ".$daterange_open." ");
while($row_jd_p = mysqli_fetch_assoc($res_jd_p)){
	if($row_jd_p["ageing"] >= 0 && $row_jd_p["ageing"] <= 1){
		$p_interval1 ++;
	}else if($row_jd_p["ageing"] > 1 && $row_jd_p["ageing"] <= 2){
		$p_interval2 ++;
	}else if($row_jd_p["ageing"] > 2 && $row_jd_p["ageing"] <= 3){
		$p_interval3 ++;
	}else if($row_jd_p["ageing"] > 3 && $row_jd_p["ageing"] <= 4){
		$p_interval4 ++;
	}else{
		$p_interval5 ++;
	}
	
}
return $p_interval1 ."~". $p_interval2 ."~". $p_interval3 ."~". $p_interval4 ."~". $p_interval5;
}
## selected  model

## selected  Status

$columns = array( 
// datatable column index  => database column name
	0 => 'location_code', 
	1 => 'locationname'

);
// getting total number records without any search
$sql = "SELECT *";
 $sql.=" FROM location_master where locationtype='ASP' and ".$state;
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("Err1".mysqli_error($link1));
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

 $sql = "SELECT *";
 $sql.=" FROM location_master where locationtype='ASP'   and ".$state;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (locationname  LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR location_code LIKE '".$requestData['search']['value']."%' )";
}
$query=mysqli_query($link1, $sql) or die("Err2".mysqli_error($link1));
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("loc-grid-data.php: get employee details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array


$row1=job_state_details("COM",$row["location_code"],$_REQUEST['daterange'],$link1);
$row2=job_state_details("OPEN",$row["location_code"],$_REQUEST['daterange'],$link1);
$row3=job_state_details("Assign",$row["location_code"],$_REQUEST['daterange'],$link1);
$row4=job_state_details("PEN",$row["location_code"],$_REQUEST['daterange'],$link1);
$row5= job_state_details("CANCEL",$row["location_code"],$_REQUEST['daterange'],$link1);
$row6= job_state_details("Replacement",$row["location_code"],$_REQUEST['daterange'],$link1);
$row7= job_state_details("GT",$row["location_code"],$_REQUEST['daterange'],$link1);

$row8=$row1+$row6;




$row9= job_asp_aging("ageing",$row["location_code"],$_REQUEST['daterange'],$today,$link1);

$ageing_day=explode("~",$row9);
$interval2=$ageing_day[0]+$ageing_day[1];
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
	$nestedData[] = getAnyDetails($row['stateid'],"state","stateid","state_master",$link1);
		$nestedData[] = $row["locationname"];
			$nestedData[] = $row["location_code"];
	$nestedData[] = $row7;
     $nestedData[] =$row8;
	$nestedData[] = $row5;
    $nestedData[] = $row2;
	$nestedData[] = $row3;
	$nestedData[] = $row4;
	
$nestedData[] = $ageing_day[0];		
$nestedData[] = $ageing_day[1];
$nestedData[] = $ageing_day[2];		
$nestedData[] = $ageing_day[3];
$nestedData[] = $ageing_day[4];

	
$nestedData[] = round($row_per,2);	


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
