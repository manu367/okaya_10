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

$today=date('Y-m-d');
$job_no=$_REQUEST['Compaint_no'];
$result=$_REQUEST['test_result'];
$reason=$_REQUEST['test_reason']; 
$doc_url=$_REQUEST['pdf_url'];  
 
if($job_no!='' && $result!='' && $doc_url!=''){
	
	$result=mysqli_query($conn,"insert into charger_test_result set job_no='".$job_no."',test_result='".$result."',test_reason='".$reason."',doc_url='".$doc_url."',entry_date='".$today."'");
	
	if($result){
	$b["err_flag"]="0";
	$b["err_msg"]="Details Updated";	
	}
	}else{
	$b["err_flag"]="1";
	$b["err_msg"]="Please try again";
	
	}
	#########################

	array_push($a,$b);
echo json_encode($a); 
mysqli_close($conn);
?>