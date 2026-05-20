<?php 
/**  * Creates fault detail data as JSON  */ 
include_once 'db_functions.php';  
$db = new DB_Functions();
////// make clone of db connection
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn = $private_variable->getValue($db);
   
$users = $db->getAlter_Partcode($_REQUEST['partcode']);   
//print_r($users);exit;
function cleanData($instr) {
$str=trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($instr))))));
return $str;
}	
  
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{ 
	
if($row["alternate_partcode"]!=''){
	$app_partcode=$row["alternate_partcode"];
}
else{
	$app_partcode=$row["partcode"];
}
$part_details=mysqli_fetch_array(mysqli_query($conn,"select partcode,part_name from partcode_master where partcode='".$app_partcode."'"));
          
$b["partcode"] = $part_details["partcode"];
$b["partname"] = cleanData($part_details["part_name"]); 

array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>