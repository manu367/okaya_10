<?php 
/**  * Creates fault detail data as JSON  */ 
include_once 'db_functions.php';  
$db = new DB_Functions();   
$users = $db->getmodel();     
$a = array();     
$b = array();    
if ($users != false){       
  while($data=mysqli_fetch_array($users))
  {
   $tax_name=$db->getAnyDetails( "998716","igst","hsn_code","tax_hsn_master");
$taxamt=($data["ser_charge"]* $tax_name)/100;
$tot_amt= $data["ser_charge"]+$taxamt;
$b["modelid"] = $data["model_id"];
$b["model"]=$data["model"];
//$b["modelcode"] = $data[modelcode]; 
$b["productid"]=$data["product_id"];
$b["wp"]=$data["wp"];
$b["servicecost"]=$data["ser_charge"];
$b["servicecostgst"]=$tot_amt;

array_push($a,$b);         
     } 
echo json_encode($a);     
} 
?>