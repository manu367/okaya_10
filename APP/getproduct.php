<?php 
/**  * Creates Customer details data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();     
$users = $db->getProduct();     
$a = array();     
$b = array();    
if ($users != false){ 
while ($row = mysqli_fetch_array($users)) 
{   
		                
$b["productid"] = $row["product_id"];             
$b["productname"] = $row["product_name"];


$i++;
array_push($a,$b);         
}        
echo json_encode($a);     
} 
?>