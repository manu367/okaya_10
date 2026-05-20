<?php
include_once 'db_functions.php';
$time_zone=time() + 0;	
date_default_timezone_set ("Asia/Calcutta");
$db = new DB_Functions();
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$today = date("Y-m-j");
$cur_time = date("H:i:s");
$c_time=date("H:i",$time_zone);
############################## Parameters of PushData API ##########################################################		
$json = $_REQUEST["FAULTYJSON"];
$z=json_decode($json, true);
$app_json="insert into api_json_data set doc_no='".$z['eng_id']."',data='".$json."',activity='Disptach Faulty',ip='".$_SERVER['REMOTE_ADDR']."'";
$sql_json=mysqli_query($conn,$app_json);
###### CHeck Location Mapping of Eng
$chk_map=mysqli_fetch_array(mysqli_query($conn,"select location_code  from locationuser_master where userloginid='".$z['eng_id']."'"));

	if($z['eng_id']!='' && $chk_map['location_code']!=''){
		///////// Create Document No.
		$row_so=mysqli_fetch_array(mysqli_query($conn,"select max(temp_no) as no from part_to_credit where eng_id='".$z['eng_id']."'"));
		$c_nos=$row_so['no']+1;
		$ch_no="ENG".$z['eng_id'].str_pad($c_nos,3,'0',STR_PAD_LEFT);
	}

  	$len=count($z['jobdetails']);

	////// INITIALIZE PARAMETER/////////////////////////
	mysqli_autocommit($conn, false);
	$flag = true;
	$error_msg = "";
	$a=array();
	for($i=0;$i<$len;$i++){
		$job_no=$z['jobdetails'][$i]['job_no'];
		$id=$z['jobdetails'][$i]['id'];
		$eng_id=$eng_id;
		
		/////////////////	
		$avl_faulty=0;
		$trn_qty=0;
		$trnless_qty=0;
		$trn_avlqty=0;
		if($z['eng_id']!='' && $chk_map['location_code']!='' && $job_no!='' && $id!='')
		{
		
			$sql_row=mysqli_fetch_array(mysqli_query($conn,"select * from part_to_credit where job_no='".$job_no."' and sno='".$id."'"))or die(mysqli_error($conn));
			$sql_feultychk=mysqli_query($conn,"select id,faulty from user_inventory where locationuser_code='".$z['eng_id']."' and partcode='".$sql_row['partcode']."' and faulty >= '".$sql_row['qty']."'");
			$chk_faulty=mysqli_num_rows($sql_feultychk);
			if($chk_faulty > 0){

				$rowchk_faulty=mysqli_fetch_array($sql_feultychk);
		
				$result=mysqli_query($conn,"update part_to_credit set eng_challan_no='".$ch_no."',eng_challan_date='".$today."',to_location='".$chk_map['location_code']."',dispatchstatus='ENGDispatched',temp_no='".$c_nos."',eng_status='3' where job_no='".$job_no."' and sno='".$id."'")or die(mysqli_error($conn));
				//// check if query is not executed
				if(!$result){
					$flag = false;
					$error_msg = "Error details 1 : ".mysqli_error($conn).".";
				}	
		
				$res_query=mysqli_query($conn,"update user_inventory set faulty=faulty-'".$sql_row['qty']."'  where locationuser_code='".$z['eng_id']."' and partcode='".$sql_row['partcode']."'");
				//// check if query is not executed
				if(!$res_query){
					$flag = false;
					$error_msg = "Error details 2 : ".mysqli_error($conn).".";
				}	

				###### Stock Ledger
				$result_ledger=mysqli_query($conn,"insert into stock_ledger set reference_no='".$ch_no."',reference_date='".$today."',brand_id='".$rs_part['brand_id']."',product_id='".$rs_part['product_id']."',partcode='".$sql_row['partcode']."',from_party='".$z['eng_id']."',to_party='".$chk_map['location_code']."',owner_code='".$z['eng_id']."',stock_transfer='OUT',stock_type='Faulty',type_of_transfer='ENG P2C Dispatch',action_taken='ENG P2C Dispatch',qty='".$sql_row['qty']."',create_by='".$z['eng_id']."',create_date='".$today."',create_time='".$cur_time."'");
				//// check if query is not executed
				if(!$result_ledger){
					$flag = false;
					$error_msg = "Error details 3 : ".mysqli_error($conn).".";
				}
		
				### Check Faulty Qty After transtion 12/oct/2022
				$sql_trnchk=mysqli_fetch_array(mysqli_query($conn,"select faulty from user_inventory where locationuser_code='".$z['eng_id']."' and partcode='".$sql_row['partcode']."'"));
				$avl_faulty=$rowchk_faulty['faulty'];
				$trn_qty=$sql_row['qty'];
				$trnless_qty=($avl_faulty-$trn_qty);
				$trn_avlqty=$sql_trnchk['faulty'];
				if($trn_avlqty!=$trnless_qty){
			
					$result_unattempt=mysqli_query($conn,"insert into unattempt_trn_data set activity='APP Faulty Dispacth',file_name='Eng_faultyDispatch',doc_no='".$ch_no."',pre_avl_qty='".$avl_faulty."',trn_qty='".$trn_qty."',after_avl_qty='".$trn_avlqty."',action_by='".$z['eng_id']."',partcode='".$sql_row['partcode']."'");
					//// check if query is not executed
					if(!$result_unattempt){
						$flag = false;
						$error_msg = "Error details 4 : ".mysqli_error($conn).".";
					}
				}
		
				#### END Faulty Check COndition
			}else{
				$flag = false;
				$error_msg = "Faulty Inventory not available eng - ".$z['eng_id'].", partcode ".$sql_row['partcode'].", job - ".$job_no.".";
			}
		}else{
			$flag = false;
			$error_msg = "Some Details are missing.";
		}
	
	}///////////// END FOR LOOP

	///// check both master and data query are successfully executed
	if($flag) {
		mysqli_commit($conn);
		$a["response"]=1;
		$a["challan_no"]=$ch_no;
		$a["eng_id"]=$z['eng_id'];
		$a["msg"]='Challan Created';
	}else{
		mysqli_rollback($conn);
		$a["response"]=2;
		$a["challan_no"]='';
		$a["eng_id"]=$z['eng_id'];
		$a["msg"]='Error Found. '.$error_msg;
	} 
	//	mysqli_close($conn);

	echo json_encode($a);         

/********************
	if($result) {  
	 
     $a["response"]=1;
	 $a["challan_no"]=$ch_no;
	 $a["eng_id"]=$z['eng_id'];
     $a["msg"]='Challan Created';
	 
   echo json_encode($a);         
   
   } else {             
  if( mysqli_errno($conn) == 1062) {                
 
   $a["response"]=2;
   $a["challan_no"]='';
   $a["eng_id"]=$eng_id;
   $a["msg"]='Something Went Wrong 1';
   
    echo json_encode($a);              
   } else {                 
   $a["response"]=1;
   $a["challan_no"]='Error';
   $a["eng_id"]=$eng_id;
   $a["msg"]='Something Went Wrong 2';           
 	 echo json_encode($a);              
                     
 }
   }// end of else

*********************/   
?>

