<?php 
header('Access-Control-Allow-Origin: https://beta.okaya.cancrm.in');
/**  * Creates fault detail data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();  

function cleanData($instr) {
    $str=trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($instr))))));
    return $str;
    }

 $reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);  

$users = $db->getPincode_data();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{    

$city_details=mysqli_fetch_array(mysqli_query($conn,"select city,state from city_master where cityid='".$row['cityid']."'"));
             
$b["pincode"] = $row["pincode"];
$b["cityid"] = $row["cityid"];
$b["city_name"] = cleanData($city_details["city"]);
$b["stateid"] = $row["stateid"];
$b["state_name"] = $city_details["state"];
array_push($a,$b);    
}
}  
echo json_encode($a);     
?>