<?php
/* Database connection start */
require_once("../includes/config.php");
/////// get Access brand////////////////////////
$arrbrand = getAccessBrand($_SESSION['asc_code'],$link1);
$samebrandasp = " and location_code in(SELECT  location_code FROM  access_brand WHERE  brand_id in  (".$arrbrand.") and status = 'Y' ) ";

/////get status//
//$arrstatus = getFullStatus("master",$link1);
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  Status
//////All selected but ZIp insert////
if($_REQUEST['brand']!="" && $_REQUEST['statename']!="" && $_REQUEST['city']!="" && $_REQUEST['product_name']!="" && $_REQUEST['srch']=='' ){
$status="location_code in(SELECT  `location_code` 
FROM  `access_brand` 
WHERE  `brand_id` LIKE  '".$_REQUEST['brand']."') and  stateid= '".$_REQUEST['statename']."' and cityid='".$_REQUEST['city']."' ";
}

//////Brand not select but Not ZIp insert////
else if($_REQUEST['brand']=="" && $_REQUEST['statename']!="" && $_REQUEST['city']!="" && $_REQUEST['product_name']!="" && $_REQUEST['srch']==''  ) {
	$status=" location_code in(SELECT location_code
FROM  `access_product` 
WHERE  `product_id` =  '".$_REQUEST['product_name']."') and  stateid= '".$_REQUEST['statename']."' and cityid='".$_REQUEST['city']."' ";
}

//////Product Brand not select but Not ZIp insert////
 else if($_REQUEST['brand']=="" && $_REQUEST['statename']!="" && $_REQUEST['city']!="" && $_REQUEST['product_name']=="" && $_REQUEST['srch']==''  ) {
	$status=" stateid= '".$_REQUEST['statename']."' and cityid='".$_REQUEST['city']."'  ";
}
//////Product Brand not select but ZIp insert////
 else if($_REQUEST['brand']=="" && $_REQUEST['statename']!="" && $_REQUEST['city']!="" && $_REQUEST['product_name']=="" && $_REQUEST['srch']!=''  ) {
	$status=" stateid= '".$_REQUEST['statename']."' and cityid='".$_REQUEST['city']."'  and location_code in(SELECT location_code from location_pincode_access where pincode='".$_REQUEST['srch']."' and statusid='1' GROUP by pincode )";
}
//////Product Brand, city not select but ZIp insert////
 else if($_REQUEST['brand']=="" && $_REQUEST['statename']!="" && $_REQUEST['city']=="" && $_REQUEST['product_name']=="" && $_REQUEST['srch']!=''  ) {
	$status=" stateid= '".$_REQUEST['statename']."'  and location_code in(SELECT location_code from location_pincode_access where pincode='".$_REQUEST['srch']."' and statusid='1' GROUP by pincode )";
}

//////Product,Brand,city not select but Not ZIp insert////
else if( $_REQUEST['statename']!="" && $_REQUEST['city']==""  && $_REQUEST['srch']==''  ) {
	$status=" stateid= '".$_REQUEST['statename']."'  ";
}

else if( $_REQUEST['statename']==""  && $_REQUEST['product_name']=="" && $_REQUEST['srch']!=''  ) {
	$status=" location_code in(SELECT location_code from location_pincode_access where pincode='".$_REQUEST['srch']."' and statusid='1' GROUP by pincode )";
}else{
	$status="1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'location_code', 
	1 => 'locationname',
	2 => 'locationtype',
	3 => 'districtid',
	4 => 'cityid',
	5 => 'stateid',
	6 => 'contactno1',
	7 => 'emailid',
	8 => 'statusid',
	9 => 'zipcode'
);
// getting total number records without any search
$sql = "SELECT *";
$sql.=" FROM location_master where ".$status." ".$samebrandasp." and locationtype='ASP'";
//echo $sql;
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("loc-grid-data.php: get employee details");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT *";
$sql.=" FROM location_master where  ".$status." ".$samebrandasp." and locationtype='ASP'";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND location_code in(SELECT location_code FROM `location_pincode_access` WHERE `pincode` like '".$requestData['search']['value']."%' and statusid='1' GROUP by pincode  )";
}
//echo $sql;
$query=mysqli_query($link1, $sql) or die("loc-grid-data.php: get employee details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($link1, $sql) or die("loc-grid-data.php: get employee details");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) { 

$time_details=mysqli_query($link1,"SELECT weekly,start_time,end_time  FROM holidays where location_code='".$row['location_code']."' and type='ASC Working Time'");
$timeing_detials=mysqli_fetch_array($time_details);
$time_update=mysqli_num_rows($time_details);
if($time_update>0){
	$det=$timeing_detials['start_time']." To ".$timeing_detials['end_time']."<br/><br/><b>Holiday-</b>&nbsp;&nbsp;".$timeing_detials['weekly'];
}
else{
	$det="";
}
$pin_det=mysqli_query($link1,"SELECT pincode FROM `location_pincode_access` WHERE `location_code` = '".$row['location_code']."'  and statusid='1' GROUP by pincode" );
$pin_details="";
$p=0;
while($pin=mysqli_fetch_array($pin_det)){
	if(($p%7)== 0)
	{ 
		$pin_details.="<br>";
	}else{
		$pin_details.=$pin['pincode'].",";
	}
	$p++;
}


$add_de=mysqli_query($link1,"SELECT date,description  FROM holidays where location_code='".$row['location_code']."' and type='Additional Holiday'");
$add_dates="";
$i=1;
while($addi_detials=mysqli_fetch_array($add_de)){
          $add_dates.=$addi_detials['date']."<br/><br/><b>Description-</b>&nbsp;&nbsp;".$addi_detials['description']."<br><br>";
	$i++;
	}
 // preparing an array
	$nestedData=array(); 
    
	$nestedData[] = $j; 
	$nestedData[] = $row["locationname"];
	$nestedData[] = $row["locationaddress"]."<br/>".getAnyDetails($row["stateid"],"state","stateid","state_master",$link1)."<br>".getAnyDetails($row["cityid"],"city","cityid","city_master",$link1);
	$nestedData[] = $row["contact_person"]."<br>".$row["emailid"]."<br>".$row["contactno1"]." <br/><br/> <a href='#'>Track on Map</a>";
	$nestedData[] =	$pin_details;
	/*$nestedData[] = getAccBrand($row["location_code"],$link1);*/
	$nestedData[] = getAccPro($row["location_code"],$link1);
	$nestedData[] = $add_dates;
	$nestedData[] = $det;
	
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
