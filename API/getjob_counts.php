<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php'; 
 $datetime = date('Y-m-d H:i:s');     
$db = new DB_Functions(); 
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$battery_jobs=$db->getJobSum();
$install_jobs=$db->getJobSuminstall();
$assigned_amc=$db->getAssignedAMC();  
$closed_amc = $db->getClosedAMC();
$a = array();     
$b = array();        
$row1=mysqli_fetch_array($battery_jobs);
if($row1["open"]!=''){$open_count=$row1["open"];}else{$open_count='0';}
if($row1["pending"]!=''){$pending_count=$row1["pending"];}else{$pending_count='0';}
if($row1["pna"]!=''){$pna_count=$row1["pna"];}else{$pna_count='0';}
if($row1["ep"]!=''){$ep_count=$row1["ep"];}else{$ep_count='0';}
if($row1["wip"]!=''){$wip_count=$row1["wip"];}else{$wip_count='0';} 
if($row1["closed"]!=''){$closed_btrcount=$row1["closed"];}else{$closed_btrcount='0';}
if($row1["pfa"]!=''){$pfa_btrcount=$row1["pfa"];}else{$pfa_btrcount='0';} 
if($row1["repl_approved"]!=''){$replapproved_count=$row1["repl_approved"];}else{$replapproved_count='0';}
if($row1["repl_request"]!=''){$replrequest_count=$row1["repl_request"];}else{$replrequest_count='0';}    

//print_r($row1);exit;
$pfa_count = $pfa_btrcount+$replrequest_count;
$row2=mysqli_fetch_array($install_jobs);
if($row2["inst_pending"]!=''){$pending_install=$row2["inst_pending"];}else{$pending_install='0';} 
if($row2["inst_done"]!=''){$closed_install=$row2["inst_done"];}else{$closed_install='0';}   
 
 
$pending_jobs=($open_count+$pending_count+$pna_count+$ep_count+$wip_count+$replapproved_count);
$b["pending_jobs"]="".$pending_jobs."";

$b["closed_jobs"]="".$closed_btrcount."";

$b["pfa_jobs"]="".$pfa_count."";
$b["pna_jobs"]="".$pna_count."";
$b["pending_install"]="".$pending_install."";
$b["closed_install"]="".$closed_install."";
$b["repl_approved"]="".$replapproved_count."";

$row_amc=mysqli_fetch_array($assigned_amc);
if($row_amc["amc_assigned"]!=''){$assignedAMC=$row_amc["amc_assigned"];}else{$assignedAMC='0';} 
$b["assigned_amc"]="".$assignedAMC."";
$row_closed_amc=mysqli_fetch_array($closed_amc);
if($row_closed_amc["amc_closed"]!=''){$closed_amc=$row_closed_amc["amc_closed"];}else{$closed_amc='0';} 
$b["closed_amc"]="".$closed_amc."";


array_push($a,$b);  
           
echo json_encode($b); 
mysqli_close($conn);
?>