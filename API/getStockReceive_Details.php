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
$user_type=$_REQUEST['u_type'];

if($user_type=='Eng'){	
$users = $db->getStock_Challan($eng_id);     
$a = array();     
$b = array();
$c = array();
     
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{                    
$b["challan_no"] = $row["challan_no"];
$b["challan_date"] = $row["sale_date"];
$b["eng_id"] = $row["to_location"];

if($row['status']=='2' || $row['status']=='3'){
	$status='Processed';
}
else if($row['status']=='4'){
	$status='Received';
}
else{
	$status=$row['status'];
}
$b["status"] = $status;
$b["sap_invoice_no"] = "";
$sql_data=mysqli_query($conn,"select partcode,part_name,qty,okqty,broken,missing,item_total from stn_items where challan_no='".$row["challan_no"]."'");
$myObj = array();
   while($row_data=mysqli_fetch_array($sql_data)){
	   
	// $part_details=mysqli_fetch_array(mysqli_query($conn,"select serial_part from partcode_master where partcode='".$row_data['partcode']."'"));  
	   
	 $c["challan_no"] = $row["challan_no"];
	 $c["partcode"] = $row_data["partcode"];
	 $c["part_name"] = cleanData($row_data["part_name"]);
     $c["assignqty"] = (int)$row_data["qty"];   
	 $c["okqty"] = (int)$row_data["okqty"]; 
	 $c["damageqty"]=(int)$row_data["broken"];
	 $c["shortqty"]=(int)$row_data["missing"];
     $c["receiveqty"]=(int)$row_data["okqty"]+(int)$row_data["broken"];
	 $c["item_total"] = $row_data["item_total"];
	 $c["partSerializ"]="N"; 
	 $c["serial_no"]=""; 
	 $c["sap_inv_no"]=""; 
	 array_push($myObj,$c);  
   }
   /*$audio->dispatchdetails = $myObj;*/ 
   $b["challandetails"] =  $myObj;     
   array_push($a, $b);  
} 

}  ###### END IF for user type
}
else{
$users = $db->getLocStock_Challan($eng_id);     
$a = array();     
$b = array();
$c = array();
     
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{                    
$b["challan_no"] = $row["challan_no"];
$b["challan_date"] = $row["sale_date"];
$b["eng_id"] = $row["to_location"];

if($row['status']=='2' || $row['status']=='3'){
	$status='Processed';
}
else if($row['status']=='4'){
	$status='Received';
}
else{
	$status=$row['status'];
}
$b["status"] = $status;
$b["sap_invoice_no"] = $row["tally_challan_no"];
$sql_data=mysqli_query($conn,"select partcode,part_name,qty,okqty,broken,missing,item_total,imei1,request_no from billing_product_items where challan_no='".$row["challan_no"]."'");
$myObj = array();
   while($row_data=mysqli_fetch_array($sql_data)){
	   
	// $part_details=mysqli_fetch_array(mysqli_query($conn,"select serial_part from partcode_master where partcode='".$row_data['partcode']."'"));  
	   
	 $c["challan_no"] = $row["challan_no"];
	 $c["partcode"] = $row_data["partcode"];
	 $c["part_name"] = cleanData($row_data["part_name"]);
     $c["assignqty"] = (int)$row_data["qty"];   
	 $c["okqty"] = (int)$row_data["okqty"]; 
	 $c["damageqty"]=(int)$row_data["broken"];
	 $c["shortqty"]=(int)$row_data["missing"];
     $c["receiveqty"]=(int)$row_data["okqty"]+(int)$row_data["broken"];
	 $c["item_total"] = $row_data["item_total"];
	 $c["partSerializ"]="N"; 
	 $c["serial_no"]=$row_data["imei1"];
	 $c["sap_inv_no"]=$row_data["request_no"]; 
	 array_push($myObj,$c);  
   }
   /*$audio->dispatchdetails = $myObj;*/ 
   $b["challandetails"] =  $myObj;     
   array_push($a, $b);  
} 	
	
}
}
// Send the JSON
   // $json->send();        
echo json_encode($a);     
?>