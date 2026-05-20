<?php 
include_once 'db_functions.php';

$db = new DB_Functions(); 	
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$today = date("Y-m-j");
$cur_time = date("H:i:s");
$c_time=date("H:i",$time_zone);
function cleanData($instr) {
$str=trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($instr))))));
return $str;
}

$eng_id=$_REQUEST['eng_id'];	  
$users = $db->getFaulty_Challan($eng_id);     
$a = array();     
$b = array();
$c = array();
     
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{                    
$b["challan_no"] = $row["eng_challan_no"];
$b["challan_date"] = $row["eng_challan_date"];
$b["to_location"] = $row["to_location"];
$b["eng_id"] = $row["eng_id"];
$sql_data=mysqli_query($conn,"select job_no,partcode,qty from part_to_credit where eng_challan_no='".$row["eng_challan_no"]."'");
$myObj = array();
   while($row_data=mysqli_fetch_array($sql_data)){
	   
	 $part_data=mysqli_fetch_array(mysqli_query($conn,"select part_name from partcode_master where partcode='".$row_data["partcode"]."'"));  
	   
	 $c["job_no"] = $row_data["job_no"];
	 $c["partcode"] = $row_data["partcode"];
	 $c["part_name"] = $part_data["part_name"];
	 $c["qty"] = $row_data["qty"]; 
	 
	 
	 array_push($myObj,$c);  
   }
   /*$audio->dispatchdetails = $myObj;*/ 
   if($row['eng_status']=='1'){
		 $status="pending";
	 }
	 else if($row['eng_status']=='3'){
		$status='Dispatched';
	 }
	 else if($row['eng_status']=='4'){
		$status='Received';
	 }else{
		$status='';
	 }
	 $b["status"]=$status;
   $b["dispatchdetails"] =  $myObj;     
   array_push($a, $b);  
} 
// Send the JSON
   // $json->send();        
echo json_encode($a);     
} 
?>