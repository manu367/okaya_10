<?php 
/**  * Creates fault detail data as JSON  */ 
$today=date("Y-m-d");
include_once 'db_functions.php';  
$db = new DB_Functions();   
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$users = $db->ValidateNewSerialno($result);
//// get month differance \\\\
function monthDifference($beginDate, $endDate){
if($endDate!='' && $beginDate!=''){
$ts1 = strtotime($beginDate);
$ts2 = strtotime($endDate);
$year1 = date('Y', $ts1);
$year2 = date('Y', $ts2);
$month1 = date('m', $ts1);
$month2 = date('m', $ts2);
$diff = (($year2 - $year1) * 12) + ($month2 - $month1);
}
else {
$diff="NA";
}
return $diff;
}     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{ 


$b["model_code"]=$row['model_code'];
$model=$row['model_code'];
$b["serial_no"]=$row["serial_no"];
$g= mysqli_fetch_array(mysqli_query($conn,"select model,product_id,wp from model_master where modelcode='".$model."' "));

$jobdet = mysqli_fetch_array(mysqli_query($conn,"select job_no,customer_id,h_code,imei from jobsheet_data where job_no = '".$_REQUEST['job_no']."'"));
$custdet = mysqli_fetch_array(mysqli_query($conn,"select customer_name , mobile from customer_master where customer_id ='".$jobdet['customer_id']."'  "));

$b["job_no"]=$jobdet["job_no"];
$b["job_serial_no"]=$jobdet["imei"];	
$b["customer_name"]=$custdet["customer_name"];
$b["customer_mobile"]=$custdet["mobile"];

$b["model"]=$g["model"];
$b["product_id"]=$g["product_id"];

#### Fetch Product Name
$sql_prd=mysqli_fetch_array(mysqli_query($conn,"select product_name,grace_period from product_master where product_id='".$g['product_id']."'"));

if($sql_prd["product_name"] == "SMF 7.2"){
$b["product_name"]="SMF BATTERY";
}else {	
$b["product_name"]=$sql_prd["product_name"];
}

	$finalenddate=$row['end_date'];
	if($finalenddate >= $today){
	$warranty="IN";
	}else {
	$grace=$sql_prd['grace_period'];
	
	$wpdate=date('Y-m-d', strtotime("+".$grace." days", strtotime($finalenddate)));
	if($wpdate>$today) {
	$warranty="IN GP";
		}else {

	$warranty="OUT";
		}
	
	}	
    $b["warr_month_used"]= monthDifference($row['start_date'],$today);
	$b["end_date"]=$row['end_date'];
	$b["war_status"]=$warranty;
	$b["ah"]="";
	$b["warranty_month"]=$g['wp'];
	$b["c_sat"]=$jobdet['h_code'];

array_push($a,$b);         
} 
echo json_encode($b);     
} 
else
{
$b["status"]= '0';
echo json_encode($b); 
mysqli_close($conn);	
}
?>