<?php 
/**  * Creates fault detail data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();     
$users = $db->getModel();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{           
$b["model_id"] = $row["modelcode"];
$b["model"] = $row["model"]; 
$b["product_id"] = $row["productid"];        
$b["brand_id"] = $row["make"];
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>