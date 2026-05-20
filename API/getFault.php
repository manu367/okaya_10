<?php 

/**  * Creates fault detail data as JSON  */ 
include_once 'db_functions.php';  
$db = new DB_Functions();   
$users = $db->getFault($_REQUEST['product_id']);     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{           
$b["id"] = $row["id"];
$b["symp_code"] = $row["defect_code"]; 
$b["product_id"] = $row["product_id"]; 
$b["details"] = $row["defect_desc"]."[".$row["defect_code"]."]";
array_push($a,$b);         
}         
echo json_encode($a);     
} 

?>