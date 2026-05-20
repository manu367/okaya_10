<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();     
$users = $db->getstockeng($_REQUEST['eid']);     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{       
     
$p_name=$db->getAnyDetails($row['partcode'],"part_name,vendor_partcode,customer_price,part_category,model_id,hsn_code","partcode","partcode_master");
 $exp_name=explode("~",$p_name);
  $tax_name=$db->getAnyDetails( $exp_name['5'],"igst","hsn_code","tax_hsn_master");
$taxamt=($exp_name[2]* $tax_name)/100;
$tot_amt= $exp_name[2]+$taxamt;
 
$b["part_id"] = $row["partcode"];
$b["partcode"] =  $exp_name[1];
$b["name"] =  $exp_name[0];
$b["sale_price"] =  $exp_name[2];
$b["Category"] =  $exp_name[3];
$b["model_id"] =  $exp_name[4];
$b["qty"] = $row["okqty"];
$b["salepricegst"] = $tot_amt;


         
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>