<?php 
include_once 'db_functions.php';

$db = new DB_Functions(); 	
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$bonn=$private_variable->getValue($db);
$today = date("Y-m-j");
$bur_time = date("H:i:s");
$b_time=date("H:i",$time_zone);
function cleanData($instr) {
$str=trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($instr))))));
return $str;
}

$eng_id=$_REQUEST['eng_id'];	  
$users = $db->toolkitstock($eng_id);     
$a = array();     
$b = array();
//$b = array();
     
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{
	if($row['status']=='1'){ $st='Pending'; }else if($row['status']=='2'){ $st='Dispatched'; }                    
$b["request_id"] = $row["job_no"];
$b["request_date"] = $row["request_date"];
$b["eng_id"] = $eng_id;
$b["status"] = $st;
//$sql_data=mysqli_query($bonn,"select partcode,qty,part_category from part_demand where job_no='".$row["job_no"]."'");
//$myObj = array();
   //while($row_data=mysqli_fetch_array($sql_data)){
	// $b["request_id"] = $row["job_no"];
	 $part=mysqli_fetch_assoc(mysqli_query($bonn,"select partcode,part_name from partcode_master where partcode='".$row['partcode']."'"));
	 $b["partcode"] = $part["partcode"];
	 $b["part_name"] = $part["part_name"];
	 $b["request_qty"]=$row["qty"];
	 $b["part_type"]=$row["part_category"];
	 
	// array_push($myObj,$b);  
 //  }
   /*$audio->dispatchdetails = $myObj;*/ 
 //  $b["challandetails"] =  $myObj;     
   array_push($a, $b);  
} 
// Send the JSON
   // $json->send();        
echo json_encode($a);     
} 
?>