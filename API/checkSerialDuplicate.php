<?php 
/**  * Creates fault detail data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions(); 

$users = $db->checkSerialDupli($_REQUEST['serialno'],$_REQUEST['jobno']); 
$users_repl = $db->checkSerialDupliRepl($_REQUEST['serialno'],$_REQUEST['jobno']);   
$users_repl_tt_btr = $db->checkSerialDupliReplTTBtr($_REQUEST['serialno'],$_REQUEST['jobno']);    
$users_repl_token_generated = $db->checkReplTokenGenrated($_REQUEST['serialno'],$_REQUEST['jobno']); 
$a = array();     
$b = array();    
if($users != false){   
	if(mysqli_num_rows($users)==0){
		if($users_repl != false){ 
			if(mysqli_num_rows($users_repl)==0){
				if($users_repl_tt_btr != false){ 
					if(mysqli_num_rows($users_repl_tt_btr)==0){
						if($users_repl_token_generated != false){ 
							if(mysqli_num_rows($users_repl_token_generated)==0){
								$b["result_code"]=1;
								$b["result_msg"]='';
							}else{
								$b["result_code"]=0;
								$b["result_msg"]='Replacement token already generated for this BSN.';	
							}	
						}					
					}else{
						$b["result_code"]=0;
						$b["result_msg"]='Serial No. not valid for this complaint';	
					}	
				}
			}else{
				$b["result_code"]=0;
				$b["result_msg"]='Serial No. Already replaced with other serial';	
			}	
		}
	}else{
		$b["result_code"]=0;
		$b["result_msg"]='Complaint is already Pending with this serial no - '.$serialno;		
	}
	array_push($a,$b);   
      
	echo json_encode($a);
	mysqli_close($conn);
} 
?>