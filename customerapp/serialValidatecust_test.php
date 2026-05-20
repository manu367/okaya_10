<?php 
/**  * Creates fault detail data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions(); 
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);

if($_REQUEST[type]=='nasaka')
  {  
$users = $db->ValidateSerialnonasaka();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{           
$b["serial_no"] = $row["serial_no"];

$model=$row["model"];
$m= mysqli_fetch_array(mysqli_query($conn,"SELECT model FROM model_master WHERE modelcode = '$model'"));
$b["modelid"]=$row["model"];
$b["model"]=$m["model"];
//$b["category"]=$row["product_name"];
//$b["install_date"]=$row["date_intallation"];

$cat_call=mysqli_query($conn,"select * from warranty_data_wp where serial_no='$row[serial_no]'  order by sno desc")or die(mysqli_error($conn));
		  $rowcat=mysqli_fetch_array($cat_call);
	if (mysqli_num_rows($cat_call)>0){ 
	$indb*=1;
	$model=$rowcat[model];
	$sql_chk="select date_intallation from installation_master where serial_no='$row[serial_no]'";
	$rs_chk=mysqli_query($conn,$sql_chk) or die(mysqli_error($conn));
	if(mysqli_num_rows($rs_chk)>0) {
	$row_chk=mysqli_fetch_assoc($rs_chk);
	$start_date=strtotime($row_chk[date_intallation]);
	
	//$start_date='2018-07-27';
   $enddate=strtotime('+1 years',$start_date);
   $enddate=date('Y-m-d',($enddate));  
  
   if($enddate >= $today){
     $warranty="IN";
     }}else {  
     $sql_chk="select amc_end_date from amc where serial_no='$row[serial_no]'";
	$rs_chk=mysqli_query($conn,$sql_chk) or die(mysqli_error($conn));
	if(mysqli_num_rows($rs_chk)>0){
	$row_chk=mysqli_fetch_assoc($rs_chk);
	$enddate=$row_chk[amc_end_date];

	if($enddate >= $today){
     $warranty="IN";
     }}else{
	// check end date if end date is greater then today date
	//if MFG Code is added and mfg code is grater then warranty End date concider MFG date as warranty end date
	$enddate=$rowcat[end_date];
	if($rowcat[end_date] > $enddate ){$finalenddate=$rowcat[end_date];} else {$finalenddate=$enddate;}
	if($finalenddate >= $today){
	$warranty="IN";
	}else {
		$indb*=0;
	$warranty="OUT";
		}}
	
	}}
	


$b["warranty_end_date"]=$enddate;
$b["warranty_status"]=$warranty;
$b[status] = 1;
array_push($a,$b);         
}         
echo json_encode($a);     
} }elseif($_REQUEST[type]=='battery') {
	
$users = $db->ValidateSerialnobattery();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{           
$b["serial_no"] = $row["serial_no"];

$model=$row["model"];
$m= mysqli_fetch_array(mysqli_query($conn,"SELECT model FROM model_master WHERE modelcode = '$model'"));
$b["modelid"]=$row["model"];
$b["model"]=$m["model"];
//$b["category"]=$row["product_name"];
//$b["install_date"]=$row["date_intallation"];

$cat_call=mysqli_query($conn,"select * from warranty_data_battery where serial_no='$row[serial_no]'  order by sno desc")or die(mysqli_error($conn));
		  $rowcat=mysqli_fetch_array($cat_call);
	if (mysqli_num_rows($cat_call)>0){ 
	$finalenddate=$rowcat[end_date];
	if($finalenddate >= $today){
	$warranty="IN";
	}else {
		$indb*=0;
	$warranty="OUT";
		}}

$b["warranty_end_date"]=$finalenddate;
$b["warranty_status"]=$warranty;
$b[status] = 1;
array_push($a,$b);         
}         
echo json_encode($a);
}}	
	
	
	
	
	
	
	
	
	
?>