<?php 
/**  * Creates fault detail data as JSON  */ 
include_once 'db_functions.php';  
$db = new DB_Functions();   
$users = $db->getPart($_REQUEST['modelid']);     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{           
$b["partid"] = $row["partcode"];
$b["partname"] = $row["part_name"]; 
$b["productid"] = $row["product_id"];
$b["brandid"] = $row["brand_id"];
$b["modelid"] = $_REQUEST['modelid'];
$b["Category"] =   $row["part_category"];
$b["partcode"] = $row["partcode"];         
$b["saleprice"] = $row["customer_price"];
$b["status"] = $row["status"];   
$b["alternate_partcode"] = $row["alternate_partcode"];  
$p_name=$db->getAnyDetails($_REQUEST['modelid'],"ser_charge,model_id,model,product_id,wp","model_id","model_master");
 $exp_name=explode("~",$p_name);
	   
$tax_name=$db->getAnyDetails( "998716","igst","hsn_code","tax_hsn_master");
$taxamt=( $exp_name[0]* $tax_name)/100;
$tot_amt= $exp_name[0]+$taxamt;
//$b["modelid"] = $exp_name[1];
$b["model"]=$exp_name[2];
//$b["modelcode"] = $data[modelcode]; 
$b["productid"]=$exp_name[3];
$b["wp"]=$exp_name[4];
//$b["servicecost"]=$exp_name[0];
//$b["servicecostgst"]=$tot_amt;   

$b["servicecost"]=500;
$b["servicecostgst"]=590;     

array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>