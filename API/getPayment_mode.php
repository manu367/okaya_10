<?php 
include_once 'db_functions.php';  
$db = new DB_Functions();   
$paymentList = $db->getPaymentList();     
$a = array();     
$b = array();    
if ($paymentList != false){         
while ($row = mysqli_fetch_array($paymentList)) 
{           
$b["payment_mode"] = $row["payment_mode"];
$b["status"] = $row["status"]; 
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>