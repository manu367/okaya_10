<?php 

function cleanData($instr) {
$str=trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($instr))))));
return $str;
}

/**  * Creates Customer details data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();     
$list = $db->getToolReqList();     
$a = array();     
$b = array();    
while ($row = mysqli_fetch_array($list))
{	
 $b["id"] = $row["id"];             
 $b["name"] = cleanData($row["part_name"]);
 $b["code"] = $row["partcode"];
array_push($a,$b);         
}        
echo json_encode($a);     
?>