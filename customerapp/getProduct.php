<?php 
/**  * Creates fault detail data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();     
$users = $db->getProduct();     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{           
$b["product_id"] = $row["productcode"];
$b["product"] = $row["productname"]; 
//$b["price"] = $row["saleprice"]; 
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>