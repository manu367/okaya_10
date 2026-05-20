<?php 
/**  * Creates fault detail data as JSON  */ 
include_once 'db_functions.php'; 
        
$eng_id=$_REQUEST['eng_id']; 
$db = new DB_Functions();  

$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);  
$users = $db->getFaulty_Details($eng_id); 

function cleanData($instr) {
$str=trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($instr))))));
return $str;
}
    
$a = array();     
$b = array();    
if ($users != false){
while($row=mysqli_fetch_assoc($users)){
$sql_part="select part_name from partcode_master where partcode='".$row['partcode']."' and status='1'";
$rs_part=mysqli_query($conn,$sql_part) or die(mysqli_error($conn));
$row_part=mysqli_fetch_assoc($rs_part);
$b["id"]=$row["sno"];
$b["job_no"]=$row["job_no"];
$b["partcode"]=$row["partcode"];
$b["partname"]=cleanData($row_part["part_name"]);
$b["qty"]=$row["qty"];
$b["consumption_date"]=$row["consumedate"];
$b["eng_id"]=$eng_id;
$b["status"]=$row["status"];
$b["serial_no"]=cleanData($row["imei"]);
$b["ok2faulty"]=$row["fresh2faulty"];

array_push($a,$b);         
 } }     
echo json_encode($a);     
 
?>