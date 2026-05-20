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
############################## Parameters of PushData API ##########################################################		
			 $json = $_REQUEST["FAULTYJSON"];
			$z=json_decode($json, true);
			  $app_json="insert into api_json_data set doc_no='".$z['eng_id']."',data='".$json."',activity='Disptach Faulty',ip='".$_SERVER['REMOTE_ADDR']."'";
			$sql_json=mysqli_query($conn,$app_json);
			###### CHeck Location Mapping of Eng
			$chk_map=mysqli_fetch_array(mysqli_query($conn,"select location_code  from locationuser_master where userloginid='".$z['eng_id']."'"));
			
			if($z['eng_id']!='' && $chk_map['location_code']!='')
			{
			///////// Create Document No.
			
	 		$row_so=mysqli_fetch_array(mysqli_query($conn,"select max(temp_no) as no from part_to_credit where eng_id='".$z['eng_id']."'"));
	 		$c_nos=$row_so['no']+1;
	 		 $ch_no="ENG".$z['eng_id'].str_pad($c_nos,3,'0',STR_PAD_LEFT);
			}

  	 $len=count($z['jobdetails']);

	$flag=1;
	$a=array();
	for($i=0;$i<$len;$i++){
	$job_no=$z['jobdetails'][$i]['job_no'];
	$id=$z['jobdetails'][$i]['id'];
	$eng_id=$eng_id;
	if($z['eng_id']!='' && $chk_map['location_code']!='' && $job_no!='' && $id!='')
	{
	
	   $sql_row=mysqli_fetch_array(mysqli_query($conn,"select * from part_to_credit where job_no='".$job_no."' and sno='".$id."'"))or die(mysqli_error($conn));
	
	 $chk_faulty=mysqli_num_rows(mysqli_query($conn,"select id from user_inventory where locationuser_code='".$z['eng_id']."' and partcode='".$sql_row['partcode']."' and faulty >= '".$sql_row['qty']."'"));
	   if($chk_faulty > 0)
	   {
	
	     $result=mysqli_query($conn,"update part_to_credit set eng_challan_no='".$ch_no."',eng_challan_date='".$today."',to_location='".$chk_map['location_code']."',dispatchstatus='ENGDispatched',temp_no='".$c_nos."',eng_status='3' where job_no='".$job_no."' and sno='".$id."'")or die(mysqli_error($conn));
	
	     $res_query=mysqli_query($conn,"update user_inventory set faulty=faulty-'".$sql_row['qty']."'  where locationuser_code='".$z['eng_id']."' and partcode='".$sql_row['partcode']."'");
		 
		  ###### Stock Ledger
	   mysqli_query($conn,"insert into stock_ledger set reference_no='".$ch_no."',reference_date='".$today."',brand_id='".$rs_part['brand_id']."',product_id='".$rs_part['product_id']."',partcode='".$sql_row['partcode']."',from_party='".$z['eng_id']."',to_party='".$chk_map['location_code']."',owner_code='".$z['eng_id']."',stock_transfer='OUT',stock_type='Faulty',type_of_transfer='ENG P2C Dispatch',action_taken='ENG P2C Dispatch',qty='".$sql_row['qty']."',create_by='".$z['eng_id']."',create_date='".$today."',create_time='".$cur_time."'");
	
	   }
	}
	}  ///////////// END FOR LOOP
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
   $a["msg"]='Something Went Wrong';
   
    echo json_encode($a);              
   } else {                 
 
   $a["response"]=0;
   $a["challan_no"]='';
   $a["eng_id"]=$eng_id;
   $a["msg"]='Something Went Wrong';              
 	 echo json_encode($a);              
                     
 }
   }// end of else
?>

