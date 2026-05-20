<?php 
/**  * Creates fault detail data as JSON  */ 
$today=date("Y-m-d");
include_once 'db_functions.php';  
$db = new DB_Functions();   
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$users = $db->ValidateSerialno($result);
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


$b["model_code"]=$row['model'];
$model=$row['model'];
$b["serial_no"]=$row["serial_no"];
$g= mysqli_fetch_array(mysqli_query($conn,"select model,modeltype,mat_group5,warrantymonth from model_master where modelcode='".$model."' "));

$jobdet = mysqli_fetch_array(mysqli_query($conn,"select job_no , customer_id,SCM from complaints_master where serial_no ='".$row['serial_no']."'  and job_no = '".$_REQUEST['job_no']."'"));
$custdet = mysqli_fetch_array(mysqli_query($conn,"select customer_name , mobile from customer_master where id ='".$jobdet['customer_id']."'  "));

$b["job_no"]=$jobdet["job_no"];
$b["customer_name"]=$custdet["customer_name"];
$b["customer_mobile"]=$custdet["mobile"];

$b["model"]=$g["model"];
$b["product_id"]=$g["modeltype"];

#### Fetch Product Name
$sql_prd=mysqli_fetch_array(mysqli_query($conn,"select productname,grace_period from product_master where productid='".$g['modeltype']."'"));

if($sql_prd["productname"] == "SMF 7.2"){
$b["product_name"]="SMF BATTERY";
}else {	
$b["product_name"]=$sql_prd["productname"];
}
	
$cat_call=mysqli_query($conn,"select * from warranty_data_battery where serial_no='".$row['serial_no']."' and start_date!='0000-00-00' and end_date!='0000-00-00' order by sno desc")or die(mysqli_error($conn));
	if (mysqli_num_rows($cat_call)>0){ 
	$indb*=1;
	 $rowcat=mysqli_fetch_array($cat_call);
	// check end date if end date is greater then today date
	//if MFG Code is added and mfg code is grater then warranty End date concider MFG date as warranty end date
	$finalenddate=$rowcat['end_date'];
	if($finalenddate >= $today){
	$warranty="IN";
	}else {
	
		 $grace=$sql_prd['grace_period'];
		 $indb*=0;
	
	$wpdate=date('Y-m-d', strtotime("+".$grace." days", strtotime($finalenddate)));
	if($wpdate>$today) {
	$warranty="IN GP";
		}else {

	$warranty="OUT";
		}
	
	}
	}
	else {}
	

	
	
    $b["warr_month_used"]= monthDifference($rowcat['start_date'],$today);
	$b["end_date"]=$rowcat['end_date'];
	$b["war_status"]=$warranty;
	$b["ah"]=$g['mat_group5'];
	$b["warranty_month"]=$g['warrantymonth'];
	$b["c_sat"]=$jobdet['SCM'];

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