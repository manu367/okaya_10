<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();     
$users = $db->getComplaintsMaster($_REQUEST['eid']);     
$a = array();     
$b = array();    
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{       
     $c = array();
$d= array();  
     $e = array();
$f= array();
     $g = array();
$h= array();
 $pro_name=$db->getAnyDetails($row['product_id'],"product_name","product_id","product_master");
$b["job_no"] = $row["job_no"];
$b["open_date"] = $row["open_date"];
$pr_param = $db->getVocmaster($row["cust_problem"],"voc_desc");
$parametername = mysqli_fetch_array($pr_param);


 $getRepair = $db->getRepairDetails($row["job_no"]);  
 	while($row_rep=mysqli_fetch_array($getRepair)){
		$c["part_id"]=$row_rep["partcode"];
		$p_name=$db->getAnyDetails($row_rep['partcode'],"part_name","partcode","partcode_master");
		$c["part_name"]=$p_name;
	    $c["fault_code"]=$row_rep["fault_code"];
		$f_name=$db->getAnyDetails($row_rep['fault_code'],"defect_desc","defect_code","defect_master");
		 $c["fault_name"]=$f_name;
		   $c["repair_code"]=$row_rep["repair_code"];
		$r_name=$db->getAnyDetails($row_rep['repair_code'],"rep_desc","rep_code","repaircode_master");
		 $c["repair_name"]=$r_name;
	     array_push($d,$c);
	}
	 $getpna = $db->getPNADetails($row["job_no"]);  
 	while($row_pna=mysqli_fetch_array($getpna)){
		$e["part_id"]=$row_pna["partcode"];
		$p_name=$db->getAnyDetails($row_pna['partcode'],"part_name","partcode","partcode_master");
		$e["part_name"]=$p_name;
	  
	     array_push($f,$e);
	}
	
	$getep = $db->getEPDetails($row["job_no"]);  
 	while($row_ep=mysqli_fetch_array($getep)){
		$g["part_id"]=$row_ep["partcode"];
		$p_name=$db->getAnyDetails($row_ep['partcode'],"part_name","partcode","partcode_master");
		$g["part_name"]=$p_name;
	  
	     array_push($h,$g);
	}

$b["serial_no"] = $row["imei"];
$b["problem_detail"] = $parametername["voc_desc"];
$b["warranty_status"] = $row["warranty_status"];
$b["model"] = $row["model"];
$b["model_id"] = $row["model_id"];
$b["product_id"] = $row["product_id"];
$b["product_name"] =  $pro_name;
$b["repair_status"] = $row["status"];
$b["status"] = $row["pen_status"];
$b["dop"] = $row["dop"];
$b["customer_id"]=$row["customer_id"];
$b["pending_reason"]=$row["reason"];
$b["closed_reason"]=$row["close_rmk"];
$b["remark"]=$row["remark"];
$b["c_type"] = $row["call_for"];
$b["close_date"] = $row["close_date"];
$b["Request type"] = $row["app_reason"];
$b["Approval"] = $row["doa_approval"];


$b["confirmedBy"] = $row["recipient_name"];
$b["contactNumber"] = $row["recipient_contact"];
$b["customerRemark"] = $row["service_rmak"];
$b["serviceRating"] = $row["rating"];
$b["customerFeedbackDate"] = $row["hand_date"]." ".$row["hand_time"];

$b["RepairList"] =$d;
$b["PNApartList"] =$f;
$b["EPpartList"] =$h;

         
array_push($a,$b);         
}         
echo json_encode($a);     
} 
?>