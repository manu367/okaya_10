<?php 

/**  * Creates fault detail data as JSON  */ 
include_once 'db_functions.php'; 
$today=date("Y-m-d"); 
$db = new DB_Functions();   
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$users = $db->ValidateSerialNakasa($result);     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{ 
if($row["model_id"]!='') {   
$b["model_id"]=$row["model_id"]; 
}else {
$b["model_id"]=$row["model"];
}	
$b["serial_no"]=$row["serial_no"];

$sql_chk="select customer_id,customer_name,mobile from serial_data_wp where serial_no='$row[serial_no]'";
$rs_chk=mysqli_query($conn,$sql_chk) or die(mysqli_error($conn));
if(mysqli_num_rows($rs_chk)>0) {
$row_chk=mysqli_fetch_assoc($rs_chk);

$b["customer_id"]=$row_chk["customer_id"];
$b["customer_name"]=$row_chk["customer_name"];
$b["mobile"]=$row_chk["mobile"];

}






$g= mysqli_fetch_array(mysqli_query($conn,"select model from model_master where (modelid='$row[model_id]' or modelcode='$row[model]' or modelcode='$row[model_id]') "));
$b["model"]=$g["model"]; 
$b["status"]= '1';

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
}  else {$enddate='0000-00-00';}
   if($enddate >= $today){
     $warranty="IN";
     }else {  
     $sql_chk="select amc_end_date from amc where serial_no='$row[serial_no]'";
	$rs_chk=mysqli_query($conn,$sql_chk) or die(mysqli_error($conn));
	if(mysqli_num_rows($rs_chk)>0){
	$row_chk=mysqli_fetch_assoc($rs_chk);
	$enddate=$row_chk[amc_end_date];
}else { $enddate='0000-00-00';}
	if($enddate >= $today){
     $warranty="IN";
     }else{
	// check end date if end date is greater then today date
	//if MFG Code is added and mfg code is grater then warranty End date concider MFG date as warranty end date
	if($rowcat[end_date] > $enddate ){$finalenddate=$rowcat[end_date];} else {$finalenddate=$enddate;}
	if($finalenddate >= $today){
	$warranty="IN";
	}else {
		$indb*=0;
	$warranty="OUT";
		}}
	
	}}


$b["war_status"]= $warranty;


array_push($a,$b);         
} 
echo json_encode($b);     
} 
else
{
$b["status"]= '0';
echo json_encode($b);  
}
?>