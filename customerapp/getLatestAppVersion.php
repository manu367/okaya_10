<?php 
/**  * Creates fault detail data as JSON  */    

$a = array();     
$b = array();    
       
   
$b["versionCode"] = "3";
//array_push($a,$b);  
$b["versionName"] = "1.2";
//array_push($a,$b); 

array_push($a,$b); 

       
         
echo json_encode($a);     

?>