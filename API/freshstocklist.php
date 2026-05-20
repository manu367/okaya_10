<?php 
include_once 'db_functions.php';

$db = new DB_Functions(); 	
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$today = date("Y-m-j");
$cur_time = date("H:i:s");
$c_time=date("H:i",$time_zone);
function cleanData($instr) {
$str=trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($instr))))));
return $str;
}

$eng_id=$_REQUEST['eng_id'];	  
$users = $db->freshstock($eng_id);     
$a = array();     
$b = array();
$c = array();
     
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{                    
$b["challan_no"] = $row["challan_no"];
$b["dispatch_date"] = $row["sale_date"];
$b["to_location"] = $row["to_location"];
$b["eng_id"] = $_REQUEST['eng_id'];
$sql_data=mysqli_query($conn,"select partcode,qty,part_name from stn_items where challan_no='".$row["challan_no"]."'");
$myObj = array();
   while($row_data=mysqli_fetch_array($sql_data)){
	 
	 $c["partcode"] = $row_data["partcode"];
	 $c["qty"] = $row_data["qty"];
	 $c["part_name"] = cleanData($row_data["part_name"]); 
	 array_push($myObj,$c);  
   }
    if($row['status']=='2'){
		 $status="Dispatched";
	 }else if($row['status']=='4'){
		$status='Received';
	 }else{
		$status='';
	 }
	 $b["status"]=$status;
   $b["dispatchdetails"] =  $myObj;     
   array_push($a, $b);  
} 
// Send the JSON
   // $json->send();        
echo json_encode($a);     
} 
?>