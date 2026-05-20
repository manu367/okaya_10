<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);     
$users = $db->getstockeng($_REQUEST['eng_id']);     
$a = array();     
$b = array(); 
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{       
     
$p_name=$db->getAnyDetails($row['partcode'],"partcode,customer_price,part_category,model_id,hsn_code,serial_part,part_name","partcode","partcode_master");
 $exp_name=explode("~",$p_name);
$tax_details=mysqli_fetch_array(mysqli_query($conn,"select igst from tax_hsn_master where hsn_code='".$exp_name['3']."' and status='1'"));
//echo $row["partcode"]."-".$tax_details['igst']."-".$exp_name['3']."</br>";
	$taxamt=($exp_name[0] * $tax_details['igst'])/100;
$tot_amt= $exp_name[0]+$taxamt;
 
$b["partcode"] =  $row["partcode"];
$b["partname"] =  $exp_name[6];
$b["sale_price"] =  $exp_name[1];
$b["Category"] =  $exp_name[2];
$b["model_id"] =  $exp_name[3];
$b["Fresh Qty"] = $row["okqty"];
$b["Faulty Qty"] = $row["faulty"];
$b["salepricegst"] = number_format($tot_amt,'2','.','');
$b["stock_report_link"]="https://rv.cancrm.in/beta/excelReports/eng_inventory_report_app.php?location_code=".base64_encode($_REQUEST['eng_id']);
#### If Part serialized 
if($exp_name[5]=='Y'){
$b["part_searilized"] ='Y';	
}
else{
$b["part_searilized"] = 'N';	
}



 array_push($a,$b);        
}          
echo json_encode($a);     
} 
?>