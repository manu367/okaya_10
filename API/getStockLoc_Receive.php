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
////// INITIALIZE PARAMETER/////////////////////////
   	mysqli_autocommit($conn, false);
	$flag = true;
	$error_msg = "";	
############################## Parameters of PushData API ##########################################################		
$json = $_POST["stockJSON"];
$z=json_decode($json, true);
//print_r($z);
#### Check APP JSON
$app_json = "INSERT INTO api_json_data SET doc_no='".$z['challan_no']."', data='".$json."', activity='Receive Fresh', ip='".$_SERVER['REMOTE_ADDR']."'";
$sql_json = mysqli_query($conn,$app_json);	
$a = array();	
$succ='';

if($z!=""){		
	 $len=count($z['stockDetail']);
	 $len2=count($z['part_details']);
	$flag=1;
	$eng_id = $z['eng_id'];
	$challan_no = $z['challan_no'];
	### Check CHallan Status
	//echo "SELECT status, from_location FROM stn_master WHERE challan_no='".$challan_no."' AND receive_date='0000-00-00'";
	$res_chk = mysqli_query($conn,"SELECT status, from_location FROM billing_master WHERE challan_no='".$challan_no."' AND receive_date='0000-00-00' and status != '5' ");
	$sql_chk = mysqli_fetch_array($res_chk);
	if($sql_chk['status']=='2' || $sql_chk['status']=='3'){
		##############	
		
		for($i=0;$i<$len;$i++){
			$partcode=$z['stockDetail'][$i]['partcode'];
			$totalqty_app=$z['stockDetail'][$i]['totalqty'];
			$remarks=$z['stockDetail'][$i]['remarks'];
			
			$resitem_chk = mysqli_query($conn,"SELECT id, status, pty_receive_date,SUM(qty) as receive_qty FROM billing_product_items WHERE to_location='".$eng_id."' AND challan_no='".$challan_no."' AND partcode='".$partcode."' AND status not in ('4','5')");
			$sqlitem_chk = mysqli_fetch_array($resitem_chk);
			
			$totalqty=$sqlitem_chk['receive_qty'];
			
			if($eng_id!='' &&  $challan_no!='' && $sqlitem_chk['pty_receive_date']=='0000-00-00' && $sqlitem_chk['status']!='4' && $sqlitem_chk['status']!='5'){
				//print_r('ddddddddd');exit;
				
				$res_stn=mysqli_query($conn,"UPDATE billing_product_items SET status='4', pty_receive_date='".$today."' WHERE to_location='".$eng_id."' AND challan_no='".$challan_no."' AND partcode='".$partcode."' and status != '4' and status != '5' ") or die(mysqli_error($conn));
				
				 //// check if query is not executed
		   		if (!$res_stn) {
	         		$flag = false;
				}

				$res_serial_rec=mysqli_query($conn,"UPDATE imei_details_asp SET status = '1', stock_type = 'OK', receive_date = '".$today."', receive_by = '".$eng_id."', bill_prd_itm_id = '".$sqlitem_chk['id']."' WHERE location_code = '".$eng_id."' AND challan_no = '".$challan_no."' AND status = '0' ") or die(mysqli_error($conn));
				
				//// check if query is not executed
		   		if(!$res_serial_rec){
	         		$flag = false;
				}
				
				$res_partdet = mysqli_query($conn,"SELECT part_name, brand_id, product_id FROM partcode_master WHERE partcode='".$partcode."'");
				$rs_part = mysqli_fetch_array($res_partdet);
				$part_name=cleanData($rs_part['part_name']);
							
        		$res_faulty = mysqli_query($conn,"SELECT id FROM client_inventory WHERE location_code='".$eng_id."' AND partcode='".$partcode."'");
	 			$chk_faulty = mysqli_num_rows($res_faulty);
	 			if($chk_faulty > 0){
					$res_query = mysqli_query($conn,"UPDATE client_inventory SET okqty=okqty+'".$totalqty."' WHERE location_code='".$eng_id."' AND partcode='".$partcode."'");
					
					 //// check if query is not executed
		   		if (!$res_query) {
	         		$flag = false;
				}
	
	   			}else{
        			$res_query2=mysqli_query($conn,"INSERT INTO client_inventory SET location_code='".$eng_id."', okqty='".$totalqty."',partcode='".$partcode."',part_name='".cleanData($part_name)."', brand_id='".$rs_part["brand_id"]."', product_id='".$rs_part["product_id"]."'");
					
					 //// check if query is not executed
		   		if (!$res_query2) {
	         		$flag = false;
				}
				
	   			}
	   			###### Stock Ledger
	   			$res_ledger=mysqli_query($conn,"INSERT INTO stock_ledger SET reference_no='".$challan_no."',reference_date='".$today."',brand_id='".$rs_part['brand_id']."',product_id='".$rs_part['product_id']."',partcode='".$partcode."',from_party='".$sql_chk['from_location']."',to_party='".$eng_id."',owner_code='".$eng_id."',stock_transfer='IN',type_of_transfer='Location RECEIVE',stock_type='OK',action_taken='AGAINST BILLING',qty='".$totalqty."',create_by='".$eng_id."',create_date='".$today."',create_time='".$cur_time."',ip='".$_SERVER['REMOTE_ADDR']."'");
        		$succ='OK';
				
				 //// check if query is not executed
		   		if (!$res_ledger) {
	         		$flag = false;
				}
				
				
			}
		}  ///////////// END FOR LOOP
		
		### Loop for Serial No.
		for($j=0;$j<$len2;$j++){
			$serial_no=$z['part_details'][$j]['serial_no'];
			$serial_no1 = trim($serial_no);
			$part_serial=$z['part_details'][$j]['partcode_serial'];
			$part_img=$z['part_details'][$j]['partimg_name'];
			$type=$z['part_details'][$j]['type'];
			
			$res_engserial=mysqli_query($conn,"insert into imei_details_asp set imei1='".$serial_no1."',partcode='".$part_serial."',location_code='".$eng_id."',status='1',stock_type='OK',entry_date='".$today."',receive_date='".$today."',grn_no='".$challan_no."',rec_type='".$type."',rec_img_name='".$part_img."' ");
			
			$succ='OK';
				
			//// check if query is not executed
			if (!$res_engserial) {
				$flag = false;
				$succ='';
			}
			
		}
		##### END Serial No. Loop
    	if($succ=='OK'){ 
			$result = mysqli_query($conn,"UPDATE billing_master set status='4',receive_date='".$today."' WHERE to_location='".$eng_id."' AND challan_no='".$challan_no."'")or die(mysqli_error($conn));
			
			//// check if query is not executed
		   		if (!$result) {
	         		$flag = false;
				}
        
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
			
					///// check both master and data query are successfully executed
				if ($flag) {
					mysqli_commit($conn);
				} else {
					mysqli_rollback($conn);
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

