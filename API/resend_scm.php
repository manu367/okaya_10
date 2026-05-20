<?php 
/**  * Creates Customer details data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db); 

$a=array();
$b=array();  
    
$job_no=$_REQUEST['job_no'];    
$sql_cust="select customer_id,h_code from jobsheet_data where job_no='".$job_no."'";
$rs_cust=mysqli_query($conn,$sql_cust) or die(mysqli_error($conn));
if(mysqli_num_rows($rs_cust)>0)
{
	$row_cust=mysqli_fetch_assoc($rs_cust); 
	$sql_c="select mobile from customer_master where customer_id='".$row_cust['customer_id']."'";
	$rs_c=mysqli_query($conn,$sql_c) or die(mysqli_error($conn));
	$row_c=mysqli_fetch_assoc($rs_c);
	$tosms=$row_c[mobile];
	$scmsms=$row_cust['h_code'];
	if($tosms!='')
	{
        //$message = urlencode("Dear Customer, Complaint no-".$job_no." has registered of Microtek UPS-.Pls share this code ".$scmsms." to ENG, after your repair complete.");
		$message = "";
 		
 		$url="http://www.smsjust.com/sms/user/urlsms.php?username=microtek&pass=saloni19&senderid=MtekIn&dest_mobileno=$tosms&message=$message&response=Y";
		 
		 $return = file_get_contents($url);
		
		
	 $b["scm"]=$scmsms; 
	 $b["err_msg"]='Message sent successfully'; 	
	}     
                
 else{
		$b["scm"]=$scmsms;
		$b["err_msg"]='Customer Mobile No. is not available';  	
	 }
}

array_push($a,$b);         
       
echo json_encode($a);     
 
?>