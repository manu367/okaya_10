<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$a = array();     
$b = array();
$today = date('Y-m-d');



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
		
//Decode JSON into an Array 
$json = $_REQUEST["usersJSON"];

$users = $db->discharger_info($json);    
		if($users !=""){ 
		$row = mysqli_fetch_array($users); 
		###### model_det######################################### 
		$rs_pro=mysqli_query($conn,"select model,mat_group5,modeltype,item_load from model_master where modelcode='".$row['modelid']."'") or die(mysqli_error($conn));
		$row_pro=mysqli_fetch_assoc($rs_pro);
		
		$sql_pro=mysqli_query($conn,"select productname from product_master where productid='".$row_pro['modeltype']."'") or die(mysqli_error($conn));
		$res_pro=mysqli_fetch_assoc($sql_pro);
		##################3 engineer det #############################
		$rs_loc=mysqli_query($conn,"select usercode,username from user_master where userid='".$row['engg_assign']."'") or die(mysqli_error($conn));
		$row_loc=mysqli_fetch_assoc($rs_loc);
		
		################## customer det ###############################################################3
		$row_cust=mysqli_fetch_array(mysqli_query($conn,"select customer_name,address1,mobile from customer_master where id='".$row['customer_id']."'"));
		
		
		$cat_call=mysqli_fetch_array(mysqli_query($conn,"select start_date from warranty_data_battery where serial_no='$row[serial_no]' and start_date!='0000-00-00' and end_date!='0000-00-00' order by sno desc"));
		   
		   $month_used=monthDifference($cat_call[start_date],$today);

$a='[{"DISCHARGER INFO":{"Model":'.$row_pro[model].',"Serial No	":'.$row[serial_no].'},"Service Executive Details":{"Executive Name":'.$row_loc[username].',"EXE ID":'.$row_loc[usercode].'},"Compliant Details":{"Compliant No":'.$row[job_no].',"Date":'.$row[open_date].'},"Customer Details":{"Name":'.$row_cust[customer_name].',"Address":'.$row_cust[address1].',"Mobile No":'.$row_cust[mobile].'},"Product Details":{"Battery Info":{"Type": '.$res_pro['productname'].',"Capacity":'.$row_pro[mat_group5].',"Model":'.$row_pro[model].',"Warranty":'.$row['warranty_status'].',"Sr.No":'.$row[serial_no].',"Service Age":'.$month_used.'},"Test Parameter":{"Test Type":"","End Voltage":"","Test Current":"","WATT Load":'.$row_pro['item_load'].'}}}]';
}

//$jsonval = preg_replace('!\\r?\\n!',"", $a);
//print_r($a);
echo $a; 
mysqli_close($conn);
?>