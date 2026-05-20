<?php 
/**  * Creates fault detail data as JSON  */    

$a = array();     
$b = array();    
       
   
$b["batterytype"] = "Inverter Battery";
array_push($a,$b);  
$b["batterytype"] = "SMF Battery";
array_push($a,$b); 
$b["batterytype"] = "Automotive Battery";
array_push($a,$b); 
$b["batterytype"] = "Solar Battery";
array_push($a,$b); 
$b["batterytype"] = "E-Rickshaw Battery";
array_push($a,$b); 
$b["batterytype"] = "Lithium Battery";
array_push($a,$b); 

       
         
echo json_encode($a);     

?>