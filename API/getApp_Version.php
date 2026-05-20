<?php 
/**  * Creates fault detail data as JSON  */    
include_once 'db_functions.php';         

$a = array();    
$b = array();    
                    
$b["ver_code"] = '1';
$b["ver_name"] = '1.0';
       
array_push($a,$b);         
       
echo json_encode($a);     

?>