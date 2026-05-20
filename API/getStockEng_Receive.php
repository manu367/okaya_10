<?php
include_once 'db_functions.php';
$db = new DB_Functions();  
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
////////////
$time_zone=time() + 0;	
date_default_timezone_set ("Asia/Calcutta");
$today = date("Y-m-d");
$cur_time = date("H:i:s");
$c_time=date("H:i",$time_zone);
////////////////
function cleanData($instr) {
	$str=trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($instr))))));
	return $str;
}	
############################## Parameters of PushData API ##########################################################		
$json = $_POST["stockJSON"];
$z=json_decode($json, true);
#### Check APP JSON
$app_json = "INSERT INTO api_json_data SET doc_no='".$z['challan_no']."', data='".$json."', activity='Receive Fresh', ip='".$_SERVER['REMOTE_ADDR']."'";
$sql_json = mysqli_query($conn,$app_json);	
$a = array();	
$succ='';
if($z!=""){		
	$len=count($z['stockDetail']);
	$flag=1;
	$eng_id = $z['eng_id'];
	$challan_no = $z['challan_no'];
	### Check CHallan Status
	$res_chk = mysqli_query($conn,"SELECT status, from_location FROM stn_master WHERE challan_no='".$challan_no."' AND receive_date='0000-00-00'");
	$sql_chk = mysqli_fetch_array($res_chk);
	if($sql_chk['status']=='2' || $sql_chk['status']=='3'){
		##############	
		for($i=0;$i<$len;$i++){
			$partcode=$z['stockDetail'][$i]['partcode'];
			$totalqty=$z['stockDetail'][$i]['totalqty'];
			$remarks=$z['stockDetail'][$i]['remarks'];
			$resitem_chk = mysqli_query($conn,"SELECT status, pty_receive_date FROM stn_items WHERE to_location='".$eng_id."' AND challan_no='".$challan_no."' AND partcode='".$partcode."' AND status!='4'");
			$sqlitem_chk = mysqli_fetch_array($resitem_chk);
			if($eng_id!='' &&  $challan_no!='' && $sqlitem_chk['pty_receive_date']=='0000-00-00' && $sqlitem_chk['status']!='4'){
				mysqli_query($conn,"UPDATE stn_items SET status='4', rec_qty='".$totalqty."', pty_receive_date='".$today."', opration_rmk='".$remarks."' WHERE to_location='".$eng_id."' AND challan_no='".$challan_no."' AND partcode='".$partcode."'") or die(mysqli_error($conn));
				
				$res_partdet = mysqli_query($conn,"SELECT part_name, brand_id, product_id FROM partcode_master WHERE partcode='".$partcode."'");
				$rs_part = mysqli_fetch_array($res_partdet);
				$part_name=$rs_part['part_name'];
							
        		$res_faulty = mysqli_query($conn,"SELECT id FROM user_inventory WHERE locationuser_code='".$eng_id."' AND partcode='".$partcode."'");
	 			$chk_faulty = mysqli_num_rows($res_faulty);
	 			if($chk_faulty > 0){
					$res_query = mysqli_query($conn,"UPDATE user_inventory SET okqty=okqty+'".$totalqty."' WHERE locationuser_code='".$eng_id."' AND partcode='".$partcode."'");
	
	   			}else{
        			$res_query=mysqli_query($conn,"INSERT INTO user_inventory SET location_code='".$sql_chk['from_location']."', okqty='".$totalqty."',locationuser_code='".$eng_id."',partcode='".$partcode."',part_name='".cleanData($part_name)."', brand_id='".$rs_part["brand_id"]."', product_id='".$rs_part["product_id"]."'");
	   			}
	   			###### Stock Ledger
	   			mysqli_query($conn,"INSERT INTO stock_ledger SET reference_no='".$challan_no."',reference_date='".$today."',brand_id='".$rs_part['brand_id']."',product_id='".$rs_part['product_id']."',partcode='".$partcode."',from_party='".$sql_chk['from_location']."',to_party='".$eng_id."',owner_code='".$eng_id."',stock_transfer='IN',type_of_transfer='ENG RECEIVE',stock_type='OK',action_taken='AGAINST REQUEST',qty='".$totalqty."',create_by='".$eng_id."',create_date='".$today."',create_time='".$cur_time."',ip='".$_SERVER['REMOTE_ADDR']."'");
        		$succ='OK';
			}
		}  ///////////// END FOR LOOP
    	if($succ=='OK'){ 
			$result = mysqli_query($conn,"UPDATE stn_master set status='4',receive_date='".$today."' WHERE to_location='".$eng_id."' AND challan_no='".$challan_no."'")or die(mysqli_error($conn));
        
			if($result){
				$b["error_flag"]='0';                       
				$b["status"]='Received';
				$b["challan_no"]=$challan_no;
			}
			else{
				$b["error_flag"]='1';
				$b["status"] = 'Not Received';
				$b["challan_no"]=$challan_no;
			}
    	}
        else {
        	$b["error_flag"]='1';
			$b["status"] = 'Not Received';
			$b["challan_no"]=$challan_no;
        }
	} ////////// END CHallan If condition
}else{
	$b["error_flag"]='1';
	$b["status"] = 'Not Received';
	$b["challan_no"]=$challan_no;
}
array_push($a, $b);        
echo json_encode($a);
?>

