<?php
require_once("../includes/config.php");
//require_once("../FCM/PHP7/firebase.cm.php");
//$arrstatus = getFullStatus("process",$link1);
$docid=base64_decode($_REQUEST['refid']);
//// job details
$job_sql="SELECT * FROM jobsheet_data where job_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
$doa_sql="SELECT * FROM doa_data where job_no='".$docid."'";
$doa_res=mysqli_query($link1,$doa_sql);
$doa_row=mysqli_fetch_assoc($doa_res);

$image_det1 = mysqli_query($link1, "SELECT * FROM dop_serial_change_request  where old_job_no = '".$docid."' order by id DESC ");
$dop_serial_change_row=mysqli_fetch_assoc($image_det1);

@extract($_POST);
////// if we hit process button
if ($_POST) {
	if ($_POST['update'] == 'Update') {
		mysqli_autocommit($link1, false);
		$flag = true;
		$err_msg = "";
		$currtime=date("H:i:s");
		$apprno="";
		$apprtagno = "";

		/////update jobsheet data  table
		if($status=='82' || $status=='99' || $status=='100'){
			#### Replacement Tag no No.
			$strr = "";
			if($job_row['tag_no']==""){
				$repl_tag_count = mysqli_query($link1,"SELECT max(tag_count) as count1 from jobsheet_data where 1 ");
				$rowr_tag_count = mysqli_fetch_assoc($repl_tag_count);
			//print_r($job_row);exit;

				///// make job sequence
				if($rowr_tag_count['count1']=="" || $rowr_tag_count['count1']==0){
					$nextreplnotag = 1;
				}else{
					$nextreplnotag = $rowr_tag_count['count1'] + 1;
				}
				
				$apprtagno="M".str_pad($nextreplnotag,6,0,STR_PAD_LEFT);

				$strr = ", tag_count = '".$nextreplnotag."', tag_no = '".$apprtagno."' ";
			}else{
				$strr = "";
				$apprtagno = $job_row['tag_no'];
			}
			

			#### Replacement APproval No.
			$repl_count = mysqli_query($link1,"SELECT repl_counter from job_counter where location_code='".$job_row['current_location']."'");
			$rowr_count = mysqli_fetch_assoc($repl_count);
			///// make job sequence
			$nextreplno = $rowr_count['repl_counter'] + 1;
			////// get 2 char random no //////
			$rflg = getRandomString(2);
			$apprno="MIRPL".$rflg.date('md').date('His').str_pad($nextreplno,4,0,STR_PAD_LEFT);
			//// first update the job count
			$res_upd = mysqli_query($link1,"UPDATE job_counter set repl_counter='".$nextreplno."' where location_code='".$job_row['current_location']."'");
			
			
			
			
			
			
			
			
			$sql_btr="select id from initial_btr_data where job_no='".$docid."'";
					$result_btr=mysqli_query($link1,$sql_btr);
					$sql_btr_f="select id from final_btr_data where job_no='".$docid."'";
					$result_btr_f=mysqli_query($link1,$sql_btr_f);
					if((mysqli_num_rows($result_btr)>0)){
					 $btr_upd="UPDATE initial_btr_data set c1='".$_POST['c1_befor']."',c2='".$_POST['c2_befor']."',c3='".$_POST['c3_befor']."',c4='".$_POST['c4_befor']."',c5='".$_POST['c5_befor']."',c6='".$_POST['c6_befor']."',ocv='".$_POST['ocv_befor']."',eng_id='' where job_no='".$docid."'";	
				 $res_updt=mysqli_query($link1,$btr_upd);
					 if (!$res_updt) {
					 $flag = false;
					 $error_msg = "Error before Btr details: " . mysqli_error($link1) . ".";
				   }	
					}
					if((mysqli_num_rows($result_btr_f)>0)){
					$btr_upd_f="UPDATE final_btr_data set c1='".$_POST['c1_aftr']."',c2='".$_POST['c2_aftr']."',c3='".$_POST['c3_aftr']."',c4='".$_POST['c4_aftr']."',c5='".$_POST['c5_aftr']."',c6='".$_POST['c6_aftr']."',toc='".$_POST['toc']."',charging_hour='".$_POST['tot_chrg_hr']."',backup_load='".$bkp_load."',backup_time='".$_POST['backup_time']."',eng_id='',dischrg_current='".$_POST['dischrg_current']."',test_result='".$_POST['test_result']."',load_in_watt='".$_POST['load_in_watt']."',cut_off_volt='".$_POST['c_off_volt']."',pcv1='".$_POST['pcv1']."',pcv2='".$_POST['pcv2']."',pcv3='".$_POST['pcv3']."',pcv4='".$_POST['pcv4']."',pcv5='".$_POST['pcv5']."',pcv6='".$_POST['pcv6']."',pcv7='".$_POST['pcv7']."',invt_load_tst_reslt='".$_POST['invt_load_tst_reslt']."',ip='".$ip."',ocv='".$_POST['ocv_aftr']."',use_load='".$use_load."',eng_detection='".$eng_detection."' where job_no='".$docid."'";
					$res_updt_f=mysqli_query($link1,$btr_upd_f);
					if (!$res_updt_f) {
				    $flag = false;
				    $error_msg = "Error After Btr details: " . mysqli_error($link1) . ".";
			       }	
					}
					
					if($status=='82'){
						$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set status='84',sub_status  = '84', l3_status='84' ,doa_remark = '".$remark."',doa_approval= 'Y',doa_ar_by='".$_SESSION['userid']."',doa_ar_dt='".$today."', doa_ar_time='".$currtime."',repl_appr_no='".$apprno."', repl_appr_date = '".$today."', repl_appr_time = '".$currtime."',  sr_repl_by_flag = '".$sr_repl_by."' ".$strr." where job_no='".$docid."' ");
				$flag = callHistory($docid,$job_row['location_code'],$status,"Request Approved","Request Approved",$_SESSION['userid'],"",$remark,"","",$ip,$link1,$flag);
					}
					if($status=='99'){
					$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set status='84',sub_status  = '84', l3_status='84' ,doa_remark = '".$remark."',doa_approval= 'Y',doa_ar_by='".$_SESSION['userid']."',doa_ar_dt='".$today."', doa_ar_time='".$currtime."',repl_appr_no='".$apprno."', repl_appr_date = '".$today."', repl_appr_time = '".$currtime."',  sr_repl_by_flag = '".$sr_repl_by."' ".$strr." where job_no='".$docid."' ");
				$flag = callHistory($docid,$job_row['location_code'],$status,"Request Approved(Tested Ok)","Request Approved",$_SESSION['userid'],"",$remark,"","",$ip,$link1,$flag);
					}
					if($status=='100'){
					$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set status='84',sub_status  = '84', l3_status='84' ,doa_remark = '".$remark."',doa_approval= 'Y',doa_ar_by='".$_SESSION['userid']."',doa_ar_dt='".$today."', doa_ar_time='".$currtime."',repl_appr_no='".$apprno."', repl_appr_date = '".$today."', repl_appr_time = '".$currtime."',  sr_repl_by_flag = '".$sr_repl_by."' ".$strr." where job_no='".$docid."' ");
				$flag = callHistory($docid,$job_row['location_code'],$status,"Request Approved(Goodwill Approve)","Request Approved",$_SESSION['userid'],"",$remark,"","",$ip,$link1,$flag);
					}
					
			
			//$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set status='84',sub_status  = '84', l3_status='84' ,doa_remark = '".$remark."',doa_approval= 'Y',doa_ar_by='".$_SESSION['userid']."',doa_ar_dt='".$today."', doa_ar_time='".$currtime."',repl_appr_no='".$apprno."', repl_appr_date = '".$today."', repl_appr_time = '".$currtime."',  sr_repl_by_flag = '".$sr_repl_by."' ".$strr." where job_no='".$docid."' ");
			/// check if query is execute or not//
			if(!$jobsheet_upd){
				$flag = false;
				$err_msg = "Error1". mysqli_error($link1) . ".";
			}	

			//////// get job details
			$old_s = mysqli_fetch_array(mysqli_query($link1,"SELECT status,job_id,job_no,current_location,location_code,city_id,model_id,eng_id,product_id,brand_id,customer_name,call_for,vistor_date,close_date,open_date,warranty_status,city_id,state_id,customer_id,imei,contact_no,email,address,pincode,dop,warranty_days,warranty_end_date,ref_no,current_location FROM jobsheet_data WHERE job_no='".$docid."'"));
			
			
			
			
			
			
			
			
			
			
			
			
			
			

			//$fg_code_model=mysqli_fetch_array(mysqli_query($link1,"select partcode from model_master where model_id='".$old_s['model_id']."' "));

			/*$sql_reverse_logistic=mysqli_query($link1,"insert into reverse_logistic set job_no='".$docid."', job_open_date='".$old_s['open_date']."', repl_appr_no='".$apprno."', repl_appr_date='".$today."', tag_no='".$apprtagno."', model_id='".$old_s['model_id']."', material_fg_code='".$fg_code_model['partcode']."', faulty_sr_no='".$old_s['imei']."', entry_by='".$_SESSION['userid']."', entry_date='".$today."', entry_time='".$currtime."', ip='".$ip."' ");	
			if(!$sql_reverse_logistic) {
				$flag = false;
				$err_msg = "Error reverse logistic". mysqli_error($link1) . ".";
			}*/

			
			/*
			$sql_model=mysqli_fetch_array(mysqli_query($link1,"select partcode,part_name from partcode_master where model_id='".$repl_model."' and part_category='UNIT'"));

			$sql_replacement=mysqli_query($link1,"insert into replacement_data set job_no='".$docid."',open_date='".$old_s['open_date']."',brand_id='".$old_s['brand_id']."',product_id='".$old_s['product_id']."',model_id='".$old_s['model_id']."',partcode='',serial_no='".$old_s['imei']."',dop='".$old_s['dop']."',warranty_days='".$old_s['warranty_days']."',warranty_status='".$old_s['warranty_status']."',warranty_end_date='".$old_s['warranty_end_date']."',close_date='".$today."',replace_by='".$old_s['eng_id']."',replace_location='".$old_s['current_location']."',replace_serial_no='".strtoupper(trim($replace_serial_no))."',replace_model_id='".$replace_model_id."',replace_partcode='".$sql_model['partcode']."',entry_time='".$currtime."',entry_date='".$today."',replace_serial_mfg='',replace_serial_mfg_ex=''");	
			if(!$sql_replacement) {
				$flag = false;
				$err_msg = "Error repl". mysqli_error($link1) . ".";
			}*/

			$flag = callHistory($docid,$job_row['current_location'],$status,"Request Approved","Request Approved",$_SESSION['userid'],"",$remark,"","",$ip,$link1,$flag);

		}
		
		if($status=='83'){

			$rejection_reason_val = "";
			if($status=='83'){
				$rejection_reason_val = $rejection_reason;
			}else{
				$rejection_reason_val = "";
			}	

			//echo "UPDATE jobsheet_data set  status='10',sub_status  = '10', l3_status='".$status."' ,doa_remark = '".$remark."',doa_approval= 'N' ,doa_ar_by='".$_SESSION['userid']."',doa_ar_dt='".$today."', doa_ar_time='".$currtime."',close_date='".$today."',close_time='".$currtime."', repl_appr_no='', repl_appr_date = '".$today."', repl_appr_time = '".$currtime."', pen_status = '6', ext8 = 'L1', ext10 = '".$rejection_reason_val."' where job_no='".$docid."' "."<br><br>";
			
			
			
				$sql_btr="select id from initial_btr_data where job_no='".$docid."'";
					$result_btr=mysqli_query($link1,$sql_btr);
					$sql_btr_f="select id from final_btr_data where job_no='".$docid."'";
					$result_btr_f=mysqli_query($link1,$sql_btr_f);
					if((mysqli_num_rows($result_btr)>0)){
					 $btr_upd="UPDATE initial_btr_data set c1='".$_POST['c1_befor']."',c2='".$_POST['c2_befor']."',c3='".$_POST['c3_befor']."',c4='".$_POST['c4_befor']."',c5='".$_POST['c5_befor']."',c6='".$_POST['c6_befor']."',ocv='".$_POST['ocv_befor']."',eng_id='' where job_no='".$docid."'";	
				 $res_updt=mysqli_query($link1,$btr_upd);
					 if (!$res_updt) {
					 $flag = false;
					 $error_msg = "Error before Btr details: " . mysqli_error($link1) . ".";
				   }	
					}
					if((mysqli_num_rows($result_btr_f)>0)){
					$btr_upd_f="UPDATE final_btr_data set c1='".$_POST['c1_aftr']."',c2='".$_POST['c2_aftr']."',c3='".$_POST['c3_aftr']."',c4='".$_POST['c4_aftr']."',c5='".$_POST['c5_aftr']."',c6='".$_POST['c6_aftr']."',toc='".$_POST['toc']."',charging_hour='".$_POST['tot_chrg_hr']."',backup_load='".$bkp_load."',backup_time='".$_POST['backup_time']."',eng_id='',dischrg_current='".$_POST['dischrg_current']."',test_result='".$_POST['test_result']."',load_in_watt='".$_POST['load_in_watt']."',cut_off_volt='".$_POST['c_off_volt']."',pcv1='".$_POST['pcv1']."',pcv2='".$_POST['pcv2']."',pcv3='".$_POST['pcv3']."',pcv4='".$_POST['pcv4']."',pcv5='".$_POST['pcv5']."',pcv6='".$_POST['pcv6']."',pcv7='".$_POST['pcv7']."',invt_load_tst_reslt='".$_POST['invt_load_tst_reslt']."',ip='".$ip."',ocv='".$_POST['ocv_aftr']."',use_load='".$use_load."',eng_detection='".$eng_detection."' where job_no='".$docid."'";
					$res_updt_f=mysqli_query($link1,$btr_upd_f);
					if (!$res_updt_f) {
				    $flag = false;
				    $error_msg = "Error After Btr details: " . mysqli_error($link1) . ".";
			       }	
					}	
			
			
			
			
			
			
			
			
			//echo "UPDATE jobsheet_data set  status='10',sub_status  = '10', l3_status='".$status."' ,doa_remark = '".$remark."',doa_approval= 'N' ,doa_ar_by='".$_SESSION['userid']."',doa_ar_dt='".$today."', doa_ar_time='".$currtime."',close_date='".$today."',close_time='".$currtime."', repl_appr_no='', repl_appr_date = '".$today."', repl_appr_time = '".$currtime."', pen_status = '6', ext8 = 'L1', ext10 = '".$rejection_reason_val."' where job_no='".$docid."' ";exit;
			
				$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set  status='10',sub_status  = '10', l3_status='".$status."' ,doa_remark = '".$remark."',doa_approval= 'N' ,doa_ar_by='".$_SESSION['userid']."',doa_ar_dt='".$today."', doa_ar_time='".$currtime."',close_date='".$today."',close_time='".$currtime."', repl_appr_no='', repl_appr_date = '".$today."', repl_appr_time = '".$currtime."', pen_status = '6' where job_no='".$docid."' ");
			/// check if query is execute or not//
			if(!$jobsheet_upd){
				$flag = false;
				$err_msg = "Error1". mysqli_error($link1) . ".";
			}	
//print_r('dddddddddddd');exit;
			$flag = callHistory($docid,$job_row['location_code'],$status,"Request Rejected - Complaint Closed","Request Rejected",$_SESSION['userid'],"",$rejection_reason_val." - ".$remark,"","",$ip,$link1,$flag);
		}

		if($status=='85'){
			
			
			
			// Btr process
					$sql_btr="select id from initial_btr_data where job_no='".$docid."'";
					$result_btr=mysqli_query($link1,$sql_btr);
					$sql_btr_f="select id from final_btr_data where job_no='".$docid."'";
					$result_btr_f=mysqli_query($link1,$sql_btr_f);
					if((mysqli_num_rows($result_btr)>0)){
					 $btr_upd="UPDATE initial_btr_data set c1='".$_POST['c1_befor']."',c2='".$_POST['c2_befor']."',c3='".$_POST['c3_befor']."',c4='".$_POST['c4_befor']."',c5='".$_POST['c5_befor']."',c6='".$_POST['c6_befor']."',ocv='".$_POST['ocv_befor']."',eng_id='' where job_no='".$docid."'";	
				 $res_updt=mysqli_query($link1,$btr_upd);
					 if (!$res_updt) {
					 $flag = false;
					 $error_msg = "Error before Btr details: " . mysqli_error($link1) . ".";
				   }	
					}
					if((mysqli_num_rows($result_btr_f)>0)){
					$btr_upd_f="UPDATE final_btr_data set c1='".$_POST['c1_aftr']."',c2='".$_POST['c2_aftr']."',c3='".$_POST['c3_aftr']."',c4='".$_POST['c4_aftr']."',c5='".$_POST['c5_aftr']."',c6='".$_POST['c6_aftr']."',toc='".$_POST['toc']."',charging_hour='".$_POST['tot_chrg_hr']."',backup_load='".$bkp_load."',backup_time='".$_POST['backup_time']."',eng_id='',dischrg_current='".$_POST['dischrg_current']."',test_result='".$_POST['test_result']."',load_in_watt='".$_POST['load_in_watt']."',cut_off_volt='".$_POST['c_off_volt']."',pcv1='".$_POST['pcv1']."',pcv2='".$_POST['pcv2']."',pcv3='".$_POST['pcv3']."',pcv4='".$_POST['pcv4']."',pcv5='".$_POST['pcv5']."',pcv6='".$_POST['pcv6']."',pcv7='".$_POST['pcv7']."',invt_load_tst_reslt='".$_POST['invt_load_tst_reslt']."',ip='".$ip."',ocv='".$_POST['ocv_aftr']."',use_load='".$use_load."',eng_detection='".$eng_detection."' where job_no='".$docid."'";
					$res_updt_f=mysqli_query($link1,$btr_upd_f);
					if (!$res_updt_f) {
				    $flag = false;
				    $error_msg = "Error After Btr details: " . mysqli_error($link1) . ".";
			       }	
					}
			
			
			
			$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set  status='10',sub_status  = '10', l3_status='".$status."' ,doa_remark = '".$remark."',doa_approval= 'B' ,doa_ar_by='".$_SESSION['userid']."',doa_ar_dt='".$today."', doa_ar_time='".$currtime."',close_date='".$today."',close_time='".$currtime."', repl_appr_no='', repl_appr_date = '".$today."', repl_appr_time = '".$currtime."', pen_status = '6' where job_no='".$docid."' ");
			/// check if query is execute or not//
			if(!$jobsheet_upd){
				$flag = false;
				$err_msg = "Error1". mysqli_error($link1) . ".";
			}	

			$flag = callHistory($docid,$job_row['location_code'],$status,"Same Back-Battery found okay on back up test","Same Back-Battery found okay on back up test",$_SESSION['userid'],"",$remark,"","",$ip,$link1,$flag);
		}

		///////////////////////// entry in call history table ///////////////////////////////////////	

		////////////////////////////////////////////////////////////////////////////////////////////////////////////
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'], $docid, $status,$remark,$_SERVER['REMOTE_ADDR'], $link1, $flag);

		/*if($flag){
			if($status=='82'){
				$req = '';
				$req = '{
					"partner_code":"'.$job_row['partner_id'].'",
					"job_no": "'.$job_row['job_no'].'",
					"customer_name": "'.cleanData($job_row['customer_name']).'",
					"contact_no": "'.$job_row['contact_no'].'",
					"address": "'.cleanData($job_row['address']).'",
					"eng_id":"'.$job_row['eng_id'].'",
					"locusername": "'.cleanData(getAnyDetails($job_row['eng_id'], "locusername", "userloginid", "locationuser_master", $link1)).'",
					"contactmo": "'.getAnyDetails($job_row['eng_id'], "contactmo", "userloginid", "locationuser_master", $link1).'",
					"partcode":"'.getAnyDetails($job_row['model_id'], "partcode", "model_id", "model_master", $link1).'",
					"product": "'.cleanData(getAnyDetails($job_row['product_id'], "product_name", "product_id", "product_master", $link1)).'",
					"model": "'.cleanData(getAnyDetails($job_row['model_id'], "model", "model_id", "model_master", $link1)).'",
					"imei": "'.$job_row['imei'].'",
					"replacement_approval_date": "'.$today.'",
					"tag_no": "'.$apprtagno.'",
					"replacement_approval_no": "'.$apprno.'",
					"status": "Approved",
					"product_id": "'.$job_row['product_id'].'",
					"model_id": "'.$job_row['model_id'].'"
				}';
				//print_r($req);exit;
				$curl = curl_init();
				curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://microtek.abacusdesk.com/index.php/ProductReplacement/pushReplacementRequestIntoDms',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $req,
				CURLOPT_HTTPHEADER => array(
					'Content-Type: text/plain',
					'Cookie: PHPSESSID=04bdd40e1dffe3f309a6a043a18cf194'
				),
				));

				$response = curl_exec($curl);
				curl_close($curl);
				
				/////////////
				$array = json_decode($response, true);
				$sr_array = array();
				$status_code = $array['statusCode'];
				$status_message = $array['statusMessage'];
				//////////////

				$res_store = mysqli_query($link1,"INSERT INTO repl_api_json_data SET job_no='".$docid."', req_by='".$_SESSION["userid"]."', ip='".$_SERVER['REMOTE_ADDR']."', type='L1', response='".$response."', request='".$req."' ");

				if($status_code!="200"){
					$flag = false;
					$err_msg = "Error Found ".$status_message."";
				}
			}
		}*/

		///// send SMS through curl
		/*if($flag){
			$phone1=$cust_mob;
			$phone2 = getAnyDetails($job_row['partner_id'], "mobile", "sap_id", "retailer_distibuter_master", $link1);
			$sms_msg="";
			
			if($status=='82'){
				if(($job_row['partner_type']=="1" || $job_row['partner_type']=="3") && $phone2!=""){
					$sms_msg ="Dear MICROTEK Partner Product S No ".$job_row['imei']." is approved for replacement. Pls replace the product to Customer pls download MICROTEK PARTNER App from the link below; For andoird:- bit.ly/42fPoUG, For IOS:- bit.ly/4ajWKbA";
					$sms_resp = explode("~",sendSMSByURL($phone2,$sms_msg));
					if($sms_resp[0]=="1"){
						//// insert into sms table
						$res_sms = mysqli_query($link1,"INSERT INTO sms_send_response SET ref_no='".$docid."', ref_type='REPL APPROVAL DLR',mobile_no='".$phone1."', msg='".$sms_msg."', status='1',resp_msg='".$sms_resp[1]."', location_code='".$_SESSION['userid']."', insert_by='".$_SESSION["userid"]."', insert_date='".$datetime."', insert_ip='".$_SERVER['REMOTE_ADDR']."'");
					}else{
						//// insert into sms table
						$res_sms = mysqli_query($link1,"INSERT INTO sms_send_response SET ref_no='".$docid."', ref_type='REPL APPROVAL DLR',mobile_no='".$phone1."', msg='".$sms_msg."', status='0',resp_msg='".$sms_resp[1]."', location_code='".$_SESSION['userid']."', insert_by='".$_SESSION["userid"]."', insert_date='".$datetime."', insert_ip='".$_SERVER['REMOTE_ADDR']."'");
					}
				}else{

				}

				$sms_msg ="Dear MICROTEK Customer, Batt S No ".$job_row['imei']." of SR No ".$docid." is approved for replacement. You will receive a link of WRN. Pl contact the dealer for replacement. https://microtek.cancrm.in/MtekIn/wrn_p.php?refid=".base64_encode($docid);
				$sms_resp = explode("~",sendSMSByURL($phone1,$sms_msg));
				if($sms_resp[0]=="1"){
					//// insert into sms table
					$res_sms = mysqli_query($link1,"INSERT INTO sms_send_response SET ref_no='".$docid."', ref_type='REPL APPROVAL CUST',mobile_no='".$phone1."', msg='".$sms_msg."', status='1',resp_msg='".$sms_resp[1]."', location_code='".$_SESSION['userid']."', insert_by='".$_SESSION["userid"]."', insert_date='".$datetime."', insert_ip='".$_SERVER['REMOTE_ADDR']."'");
				}else{
					//// insert into sms table
					$res_sms = mysqli_query($link1,"INSERT INTO sms_send_response SET ref_no='".$docid."', ref_type='REPL APPROVAL CUST',mobile_no='".$phone1."', msg='".$sms_msg."', status='0',resp_msg='".$sms_resp[1]."', location_code='".$_SESSION['userid']."', insert_by='".$_SESSION["userid"]."', insert_date='".$datetime."', insert_ip='".$_SERVER['REMOTE_ADDR']."'");
				}
			}else if($status=='83'){	
				$sms_msg ="Dear Customer, Your SR No ".$docid." has been closed as the product is not under purview of warranty. For further details, please contact team Microtek";
				$sms_resp = explode("~",sendSMSByURL($phone1,$sms_msg));
				if($sms_resp[0]=="1"){
					//// insert into sms table
					$res_sms = mysqli_query($link1,"INSERT INTO sms_send_response SET ref_no='".$docid."', ref_type='REPL APPROVAL',mobile_no='".$phone1."', msg='".$sms_msg."', status='1',resp_msg='".$sms_resp[1]."', location_code='".$_SESSION['userid']."', insert_by='".$_SESSION["userid"]."', insert_date='".$datetime."', insert_ip='".$_SERVER['REMOTE_ADDR']."'");
				}else{
					//// insert into sms table
					$res_sms = mysqli_query($link1,"INSERT INTO sms_send_response SET ref_no='".$docid."', ref_type='REPL APPROVAL',mobile_no='".$phone1."', msg='".$sms_msg."', status='0',resp_msg='".$sms_resp[1]."', location_code='".$_SESSION['userid']."', insert_by='".$_SESSION["userid"]."', insert_date='".$datetime."', insert_ip='".$_SERVER['REMOTE_ADDR']."'");
				}
			}else if($status=='85'){		
				$sms_msg ="Dear Customer, SR No ".$docid." has been closed with Same back. Please visit our website or whatsapp for maintenance tips and dos and do not. team Microtek";
				$sms_resp = explode("~",sendSMSByURL($phone1,$sms_msg));
				if($sms_resp[0]=="1"){
					//// insert into sms table
					$res_sms = mysqli_query($link1,"INSERT INTO sms_send_response SET ref_no='".$docid."', ref_type='REPL APPROVAL',mobile_no='".$phone1."', msg='".$sms_msg."', status='1',resp_msg='".$sms_resp[1]."', location_code='".$_SESSION['userid']."', insert_by='".$_SESSION["userid"]."', insert_date='".$datetime."', insert_ip='".$_SERVER['REMOTE_ADDR']."'");
				}else{
					//// insert into sms table
					$res_sms = mysqli_query($link1,"INSERT INTO sms_send_response SET ref_no='".$docid."', ref_type='REPL APPROVAL',mobile_no='".$phone1."', msg='".$sms_msg."', status='0',resp_msg='".$sms_resp[1]."', location_code='".$_SESSION['userid']."', insert_by='".$_SESSION["userid"]."', insert_date='".$datetime."', insert_ip='".$_SERVER['REMOTE_ADDR']."'");
				}
			}else{
			}	
			//echo $status."<br><br>";
			//echo $sms_msg."<br><br>";
		}*/
		///// check both master and data query are successfully executed

		///// send SMS through curl
		if($flag){
			$sql_uid=mysqli_fetch_array(mysqli_query($link1,"select device_token from locationuser_master where userloginid='".$job_row['eng_id']."'"));
					
			$deviceToken=$sql_uid['device_token'];
			//echo $deviceToken."<br><br>";
			if($deviceToken != ""){
				$title = '';
				$message = '';
				if($status=='82'){	
					$title = 'Replacement Request Status';
					$message = "Complaint No. ".$docid.". Replacement Request Approved";
				}else if($status=='83'){
					$title = 'Replacement Request Status';
					$message = "Complaint No. ".$docid.". Request Rejected - Complaint Closed";
				}else{
					$title = 'Replacement Request Status';
					$message = "Complaint No. ".$docid.". Same Back-Battery found okay on back up test";
				}	

				/// add below code anywhere
				$notification = [ "title"=>$title, "body"=>$message ];

				//var_dump($notification);

				$target = [ "process"=>"", "id"=>"" ];
				$data = [ "banner"=>"", "link"=>json_encode($target) ];
				//$result = pushMessage($deviceToken,$notification,$data);
				//echo '</pre></p><h3>Response </h3><p><pre>';
				//exit(json_encode($result));
				//echo '</pre></p>';	
			}
		}	

		///// check both master and data query are successfully executed
		if ($flag) {
			mysqli_commit($link1);
			$msg = "Successfully done with ref. no. " . $docid;
			$cflag = "success";
			$cmsg = "Success";
		} else {
			mysqli_rollback($link1);
			$msg = "Request could not be processed " . $err_msg . ". Please try again.";
			$cflag = "danger";
			$cmsg = "Failed";
		}
		mysqli_close($link1);
		///// move to parent page
		header("location:job_list_repl_btr.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit;
	}	               
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?=siteTitle?></title>
		<link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
		<script src="../js/jquery.js"></script>
		<link href="../css/font-awesome.min.css" rel="stylesheet">
		<link href="../css/abc.css" rel="stylesheet">
		<script src="../js/bootstrap.min.js"></script>
		<link href="../css/abc2.css" rel="stylesheet">
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<!--<link href="../css/loader.css" rel="stylesheet"/>-->
	</head>
	<script>

		$(document).ready(function(){
			//$("#frm1").validate();
			var spinner = $('#loader');
			$("#frm1").validate({
				submitHandler: function (form){
					if(!this.wasSent){
						this.wasSent = true;
						$(':submit', form).val('Please wait...')
								.attr('disabled', 'disabled')
								.addClass('disabled');
						spinner.show();
						form.submit();
					}else{
						return false;
					}
				}
			});
		});
		
		function bigImg(x) {
			x.style.height = "300px";
			x.style.width = "300px";
		}

		function normalImg(x) {
			x.style.height = "100px";
			x.style.width = "100px";
		}

		function check_appr_div(actionid){
			if(actionid=="82"){
				//document.getElementById("appr_div").style.display="block";
				//document.getElementById("appr_div").style.display="block";
				document.getElementById("replace_serial_no").style.display="block";
				document.getElementById("replace_model_id").style.display="block";
				document.getElementById("replace_serial_no").required = true;
				document.getElementById("replace_model_id").required = true;
				document.getElementById("rpl1_flg").style.display="block";
				document.getElementById("rpl2_flg").style.display="block";
			}else{
				//document.getElementById("appr_div").style.display="none";
				document.getElementById("replace_serial_no").value="";
				document.getElementById("replace_model_id").value="";
				document.getElementById("replace_serial_no").style.display="none";
				document.getElementById("replace_model_id").style.display="none";
				document.getElementById("replace_serial_no").required = false;
				document.getElementById("replace_model_id").required = false;
				document.getElementById("rpl1_flg").style.display="none";
				document.getElementById("rpl2_flg").style.display="none";
			}
		}

		function check_reject_reason(str){
			//alert(str+' str ');
			document.getElementById("rejection_reason").value = "";
			if(str=="83"){
				document.getElementById("rr_flag").style.display = "block";
				document.getElementById("rejection_reason").style.display = "block";
				document.getElementById("rejection_reason").required = true; 
			}else{
				document.getElementById("rr_flag").style.display = "none";
				document.getElementById("rejection_reason").style.display = "none";
				document.getElementById("rejection_reason").required = false; 
			}
		}
	
	</script>
	<style>
		.form-group {
		margin-bottom: 0px;
		margin-top: 10px;
        }
		body{
		line-height: 2.428571;
		}
	</style>
	<script type="text/javascript" src="../js/jquery.validate.js"></script>
	
	<!--<body onKeyPress="return keyPressed(event);">-->
	<body onload="getEngineerDetection1();">	

		<div class="container-fluid">
			<div class="row content">
				<?php 
	include("../includes/leftnav2.php");
				?>
				<div class="<?=$screenwidth?>">
					<h2 align="center"><i class="fa fa-list-alt"></i> Complaint View </h2>
					<h4 align="center">Job No.- <?=$docid?></h4>
					<div class="panel-group">
						<div class="panel panel-info table-responsive">
							<div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>
							<div class="panel-body">
								<table class="table table-bordered" width="100%">
									<tbody>
										<tr>
											<td width="20%"><label class="control-label">Customer Name</label></td>
											<td width="30%"><?php echo $job_row['customer_name'];?></td>
											<td width="20%"><label class="control-label">Address</label></td>
											<td width="30%"><?php echo $job_row['address'];?></td>
										</tr>
										<tr>
											<td><label class="control-label">Contact No.</label><br/><span class="red_small">(For SMS Update)</span></td>
											<td><?php echo $job_row['contact_no'];?></td>
											<td><label class="control-label">Alternate Contact No.</label></td>
											<td><?php echo $job_row['alternate_no'];?></td>
										</tr>
										<tr>
											<td><label class="control-label">State</label></td>
											<td><?php echo getAnyDetails($job_row["state_id"],"state","stateid","state_master",$link1);?></td>
											<td><label class="control-label">Email</label></td>
											<td><?php echo $cust_det[1];?></td>
										</tr>
										<tr>
											<td><label class="control-label">City</label></td>
											<td><?php echo getAnyDetails($job_row["city_id"],"city","cityid","city_master",$link1);?></td>
											<td><label class="control-label">Pincode</label></td>
											<td><?php echo $job_row['pincode'];?></td>
										</tr>
										<tr>
											<td><label class="control-label">Customer Category</label></td>
											<td><?php echo $job_row['customer_type'];?></td>
											<td><label class="control-label">Residence No</label></td>
											<td><?php echo $cust_det[2];?></td>
										</tr>
										<tr>
											<td><label class="control-label">Landmarks</label></td>
											<td><?php echo $cust_det[0];?></td>
											<td><label class="control-label"></label></td>
											<td><?php ?></td>
										</tr>
									</tbody>
								</table>
							</div><!--close panel body-->
						</div><!--close panel-->
						<div class="panel panel-info table-responsive">
							<div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Product Details</div>
							<div class="panel-body">
								<table class="table table-bordered" width="100%">
									<tbody>
										<tr>
											<td width="20%"><label class="control-label">Product</label></td>
											<td width="30%"><?php echo getAnyDetails($job_row["product_id"],"product_name","product_id","product_master",$link1);?></td>
											<td width="20%"><label class="control-label">Brand</label></td>
											<td width="30%"><?php echo getAnyDetails($job_row["brand_id"],"brand","brand_id","brand_master",$link1);?></td>
										</tr>
										<tr>
											<td><label class="control-label">Model</label></td>
											<td><?=$job_row['model']?></td>
											<td><label class="control-label">Date Of Installation</label></td>
											<td><?=dt_format($product_det['installation_date'])?></td>
										</tr>
										<tr>
											<td><label class="control-label"><?php echo SERIALNO ?></label></td>
											<td><?=$job_row['imei']?></td>
											<td><label class="control-label">Escalations From</label></td>
											<td><?=$job_row['call_type']?></td>
										</tr>
										<tr>
											<td><label class="control-label">Warranty Status</label></td>
											<td><?=$job_row['warranty_status']?></td>
											<td><label class="control-label">Job For</label></td>
											<td><?=$job_row['call_for']?></td>
										</tr>
										<tr>
											<td><label class="control-label">Purchase Date</label></td>
											<td><?=dt_format($job_row['dop'])?></td>
											<td><label class="control-label">Warranty End Date</label></td>
											<td><?=dt_format($job_row['warranty_end_date'])?></td>
										</tr>
										<tr>
										<td><label class="control-label">Manufacturing Date</label></td>
										<td><?= dt_format($job_row['manufacturing_date']); ?></td>
										<td><label class="control-label">Primary Date of billing</label></td>
										<td><?= dt_format($job_row['primary_sale_date']); ?></td>
										</tr>

										<tr>
										<td><label class="control-label">Secondary Date of billing</label></td>
										<td><?= dt_format($job_row['secondary_sale_date']); ?></td>
										<td><label class="control-label">Tertiary Date</label></td>
										<td><?= dt_format($job_row['tertiary_sale_date']); ?></td>
										</tr>
										<tr>
											<td><label class="control-label">Re-Validation Flag</label></td>
											<td></td>
											<td><label class="control-label">AMC Number</label></td>
											<td><?=$product_det['amc_no']?></td>
											

										</tr>
										<tr>
											<td><label class="control-label">AMC Expiry Date </label></td>
											<td ><?=dt_format($product_det['amc_end_date'])?></td>
											<td><label class="control-label">Date Of Installation</label></td>
											<td><?=dt_format($product_det['installation_date'])?></td>
											

										</tr>
										<tr>
											<td><label class="control-label">Entity Name</label></td>
											<td ><?php echo getAnyDetails($job_row['entity_type'],"name","id","entity_type",$link1);?></td>
											<td><label class="control-label">Dealer Name</label></td>
											<td><?=$job_row['dname']?></td>
											<!--<td><label class="control-label">Invoice No</label></td>
											<td><?php /*$job_row['inv_no']*/ ?></td>-->
										</tr>

										<tr>
										<td><label class="control-label">Warranty Source Type</label></td>
										<td><?= $wrnty_src_type_amc; ?></td>
										</tr>

										<tr>
										<td><label class="control-label">Complaint Attend Point</label></td>
										<td><?= $job_row['comp_attend']; ?></td>
										<td><label class="control-label">Product Status</label></td>
										<td><?= $job_row['sold_unsold']; ?></td>
										</tr>

										<tr>
										<td><label class="control-label">Attend Person Name </label></td>
										<td><?= $job_row['partner_name']; ?></td>
										<td><label class="control-label">Attend Person Mobile</label></td>
										<td><?= $job_row['partner_mobile']; ?></td>
										</tr>

										<tr>
										<td><label class="control-label">Attend Person Location </label></td>
										<td><?=getAnyDetails($job_row['partner_location'], "city", "cityid", "city_master", $link1); ?></td>
										<td><label class="control-label"></label></td>
										<td></td>
										</tr>

										<tr>
										<td><label class="control-label">Partner SAP Code</label></td>
										<td><?= $job_row['partner_id']; ?></td>
										<td><label class="control-label">Partner Type</label></td>
										<td><?php 
											if($job_row['partner_type']=="1"){
											echo "Distributor";
											} else if($job_row['partner_type']=="2"){
											echo "Direct dealer";
											}else if($job_row['partner_type']=="3"){
											echo "Retailer";
											}else{
											echo "";
											}
										
										?></td>
										</tr>

										<?php
										$rdm_sql = "SELECT * FROM retailer_distibuter_master where sap_id='".$job_row['partner_id']."'";
										$rdm_res = mysqli_query($link1, $rdm_sql);
										$rdm_row = mysqli_fetch_assoc($rdm_res);
										?>

										<tr>
										<td><label class="control-label">Partner Name</label></td>
										<td><?= cleanData($rdm_row['name']); ?></td>
										<td><label class="control-label">Partner State</label></td>
										<td><?= cleanData($rdm_row['state']); ?></td>
										</tr>

                    					<tr>
										<td><label class="control-label">Partner District</label></td>
										<td><?= cleanData($rdm_row['district']); ?></td>
										<td><label class="control-label">Partner Street</label></td>
										<td><?= cleanData($rdm_row['street']); ?></td>
										</tr>

                    					<tr>
										<td><label class="control-label">Partner Pincode</label></td>
										<td><?= $rdm_row['pincode']; ?></td>
										<td><label class="control-label">Partner Mobile</label></td>
										<td><?= $rdm_row['mobile']; ?></td>
										</tr>

										

										<tr>
										<td><label class="control-label">Actual Aging</label></td>
										<td><?php 
										$aging_data = $job_row['ext7'];
										/*if($job_row['dop']!="" && $job_row['dop']!="0000-00-00" && $job_row['primary_sale_date']!="" && $job_row['primary_sale_date']!="0000-00-00"){
											if($job_row['dop'] >= $job_row['primary_sale_date']){
												$aging_data = daysDifference($job_row['dop'], $job_row['primary_sale_date']);
											}else{
												$aging_data = "";
											}
										}else{
											$aging_data = "";
										}*/
										echo $aging_data;
										
										?></td>
										<td><label class="control-label">Recommended Ageing</label></td>
										<td><?php echo getAnyDetails($job_row["product_id"],"recc_aging","product_id","product_master",$link1);?></td>
										</tr>

										<tr>
										<td><label class="control-label">Calulation Source</label></td>
										<td><?= $job_row['modifDone']; ?></td>
										<td><label class="control-label"></label></td>
										<td></td>
										</tr>

										<?php if($job_row['replace_serial']!=""){ ?>
										<tr>
										<td><label class="control-label">Replaced Serial No.</label></td>
										<td><?= $job_row['replace_serial'] ?></td>
										<td><label class="control-label">Replaced Model ID</label></td>
										<td><?=getAnyDetails($job_row["replace_model"], "model", "model_id", "model_master", $link1)." | ".$job_row["replace_model"]; ?></td>
										</tr>
										<?php } ?>

										<?php if($job_row['product_id'] =="50" || $job_row['product_id'] =="46" || $job_row['product_id'] =="11"){ ?>
										<tr>
										<td><label class="control-label">Physical condition</label></td>
										<td><?= getAnyDetails($job_row["phy_cond"], "name", "id", "physical_condition", $link1) ?></td>
										<td><label class="control-label">Complaint attend point</label></td>
										<td><?= $job_row['comp_attend'] ?></td>
										</tr>
										<!---
										<tr>
										<td><label class="control-label">Complaint Attend Name</label></td>
										<td><?= $job_row['partner_met_name'] ?></td>
										<td><label class="control-label">Complaint Attend No</label></td>
										<td><?= $job_row['comp_attend_detail'] ?></td>
										</tr>--->
										<tr>
										<td><label class="control-label"><!---Partner Location----></label></td>
										<td></td>
										<td><label class="control-label">Sold/Unsold</label></td>
										<td><?= $job_row['sold_unsold'] ?></td>
										</tr>
										<tr>
										<td><label class="control-label">Backup In Hours</label></td>
										<td><?= $job_row['backup_in_hours'] ?></td>
										<td><label class="control-label">Backup In Mint.</label></td>
										<td><?= $job_row['backup_in_min'] ?></td>
										</tr>
										<tr>
										<td><label class="control-label">Invoice no</label></td>
										<td><?= $job_row['invoice_no'] ?></td>
										<td><label class="control-label">Top Lid</label></td>
										<td><?= $job_row['top_lid'] ?></td>
										</tr>
										<tr>
										<td><label class="control-label">Container</label></td>
										<td><?= $job_row['containter'] ?></td>
										<td><label class="control-label">Terminals</label></td>
										<td><?= $job_row['terminal'] ?></td>
										</tr>
										 <tr>
										<td><label class="control-label"> Problem Observed </label></td>
										<td><?= getAnyDetails($job_row['ext6'], "defect_desc", "defect_code", "defect_master", $link1); ?></td>
										<td><label class="control-label">Solution Given</label></td>
										<td><?= getAnyDetails($job_row['problem_detect'], "rep_desc", "rep_code", "repaircode_master", $link1); ?></td>
                                        </tr>  
										<tr>
												<td><label class="control-label">Charger Installed</label></td>
												<td><?= $job_row['charger_installed'] ?></td>
												<td><label class="control-label">Charger Installed Date</label></td>
												<td><?= $job_row['dt1']." ".$job_row['tim1'] ?></td>
										  </tr>

										  <tr>
												<td><label class="control-label">Charger Removed</label></td>
												<td><?= $job_row['charger_removed'] ?></td>
												<td><label class="control-label">Charger Removed Date</label></td>
												<td><?= $job_row['charger_remove_dt']." ".$job_row['charger_remove_time'] ?></td>
                      						</tr>
										
										<?php } ?>
                                           <tr>
												<td><label class="control-label"> ENG Name </label></td>
												<td><?= getAnyDetails($job_row['eng_id'], "locusername", "userloginid", "locationuser_master", $link1); ?></td>
												<td><label class="control-label">ENG Mobile No</label></td>
												<td><?= getAnyDetails($job_row['eng_id'], "contactmo", "userloginid", "locationuser_master", $link1); ?></td>
                                           </tr>
									</tbody>
								</table>
							</div><!--close panel body-->
						</div><!--close panel-->


						<div class="panel panel-info table-responsive">
							<div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Observation</div>
							<div class="panel-body">
								<table class="table table-bordered" width="100%">
									<tbody>

										<tr>
											<td width="26%"><label class="control-label">Assign Location</label></td>
											<td  colspan="3"><?php echo getAnyDetails($job_row["current_location"],"locationname","location_code","location_master",$link1);?></td>

										</tr>
										<tr>
											<td><label class="control-label">VOC</label></td>
											<td><?php echo getAnyDetails($job_row["cust_problem"],"voc_desc","voc_code","voc_master",$link1);?></td>
											<td><?php 	$voc= explode(",",$job_row['cust_problem2']); 
												$vocpresent   = count($voc);
												if($vocpresent == '1'){
													$name = getAnyDetails($voc[0],"voc_desc","voc_code","voc_master",$link1 );
												}
												else if($vocpresent >1){
													$name ='';
													for($i=0 ; $i<$vocpresent; $i++){					 
														$name.=  getAnyDetails($voc[$i],"voc_desc","voc_code","voc_master",$link1 ).",";
													}} echo $name;?></td>
											<td><?=$job_row['cust_problem3']?></td>
										</tr>
										<tr>
											<td><label class="control-label">Remark </label></td>
											<td colspan=""><?=$job_row['remark']?></td>
											<td><label class="control-label">Request Remarks </label></td>
											<td colspan="3"><?=$job_row['app_reason']?></td>
										</tr>

										<!-------------------------------------------------------------------------------------->
										<tr>
											<td><label class="control-label">Serial/Warranty Images</label></td>
											<td colspan="3">
											
													<div style="float:left;"><div style="width: 100px; float:left; text-align:center; font-weight: bold;">Product Image</div><div style="width: 105px; float:left; text-align:center; font-weight: bold;">Invoice Image</div><div style="width: 105px; float:left; text-align:center; font-weight: bold;">Serial /Warranty Image</div></div>  
													
													<div style="clear: both;">
													<div style="float:left;">
												<?php
													$image_det1 = mysqli_query($link1, "SELECT * FROM product_registered  where serial_no = '" . $job_row['imei'] . "' order by id DESC ");
													while ($row_image1 = mysqli_fetch_array($image_det1)) { 
												?>
									
											
													<span>
									<a href="<?php echo $row_image1['product_img']; ?>" target="_blank"><img src="<?= $row_image1['product_img'] ?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></a>
													</span>
													
													<span>
									<a href="<?php echo $row_image1['invoice_img']; ?>" target="_blank"><img src="<?= $row_image1['invoice_img'] ?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></a>
													</span>
													
													<span>
									<a href="<?php echo $row_image1['serial_img']; ?>" target="_blank"><img src="<?= $row_image1['serial_img'] ?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></a>
													</span>
									
									</div>
									</div>

												<?php							
													}
												?>
								
											</td>
											</tr>
										<?php /********************** ?>
										<tr>
											<td><label class="control-label" style="color:#fb5a0c; font-weight: bold;">Serial/Warranty Images</label></td>
											<td colspan="3">
												
											<div style="float:left;"><div style="width: 100px; float:left; text-align:center; font-weight: bold;">Product Image</div><div style="width: 105px; float:left; text-align:center; font-weight: bold;">Invoice Image</div><div style="width: 105px; float:left; text-align:center; font-weight: bold;">Serial /Warranty Image</div></div>  
                              
                              <div style="clear: both;">
                              <div style="float:left;">
												
													<span>
									<a href="<?php echo $dop_serial_change_row['product_img']; ?>" target="_blank"><img src="<?= $dop_serial_change_row['product_img'] ?>" alt="Smiley face" height="100" width="100" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" ></a>
													</span>
													
													<span>
									<a href="<?php echo $dop_serial_change_row['invoice_img']; ?>" target="_blank"><img src="<?= $dop_serial_change_row['invoice_img'] ?>" alt="Smiley face" height="100" width="100" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" ></a>
													</span>
													
													<span>
									<a href="<?php echo $dop_serial_change_row['serial_img']; ?>" target="_blank"><img src="<?= $dop_serial_change_row['serial_img'] ?>" alt="Smiley face" height="100" width="100" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" ></a>
													</span>
												
													</div>
              										</div>
												
											</td>
										</tr>
										<?php **********************/ ?>
										<!-------------------------------------------------------------------------------------->


										<?php 
	$image_det = mysqli_query($link1,"SELECT * FROM image_upload_details  where job_no='".$job_row['job_no']."'  and activity != 'Image Upload by app - DOPFreez' ");
												while($row_image=mysqli_fetch_array($image_det)){?>  
										<tr>
											<td><label class="control-label"><?=$row_image['activity']?></label></td>
											<td colspan="3"  >
											<?php if($row_image['activity'] == "Image Upload by app"){ ?>
											<div style=""><div style="width: 100px; float:left; text-align:center; font-weight: bold;">Wty Card</div><div style="width: 105px; float:left; text-align:center; font-weight: bold;">Invoice</div><div style="width: 105px; float:left; text-align:center; font-weight: bold;">Charger Install</div><div style="width: 105px; float:left; text-align:center; font-weight: bold;">Serial /Warranty Images</div><div style="width: 105px; float:left; text-align:center; font-weight: bold;">Customer Signature</div><div style="width: 105px; float:left; text-align:center; font-weight: bold;">OCV Reading</div></div>
											<?php } ?>
												<div style="clear: both;"></div>
											<div style="float:left;">

												<?php if ($row_image['img_url']!=""){
													$four_str = substr($row_image['img_url'], -4);
													//echo "<br><br>";
													$five_str = substr($row_image['img_url'], -5);
													//echo "<br><br>";
													$four_str_ext = substr($four_str, 0, 1);
													//echo "<br><br>";
													$five_str_ext = substr($five_str, 0, 1);
													//echo "<br><br>";
													if($four_str_ext == "." || $five_str_ext == "." ) {
												?>
												<?php if($four_str == ".doc" || $four_str == ".pdf" || $five_str == ".docx"){ ?>
												<span>
													<a href="<?php echo $row_image['img_url']; ?>" target="_blank" > <u>Download Jobsheet</u>  </a>
												</span>
												<?php }else{ ?>
												<span> 
												<a href="<?php echo $row_image['img_url']; ?>" target="_blank"><img src="<?=$row_image['img_url']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></a>
												</span>
												<?php } ?>
												<?php 
													} else {
														echo "";
													}
												?>

												<?php } if($row_image['img_url1']!="") {

													$four_str = substr($row_image['img_url1'], -4);
													//echo "<br><br>";
													$five_str = substr($row_image['img_url1'], -5);
													//echo "<br><br>";
													$four_str_ext = substr($four_str, 0, 1);
													//echo "<br><br>";
													$five_str_ext = substr($five_str, 0, 1);
													//echo "<br><br>";
													if($four_str_ext == "." || $five_str_ext == "." ) {
												?>
												<?php if($four_str == ".doc" || $four_str == ".pdf" || $five_str == ".docx"){ ?>
												<span>
													<a href="<?php echo $row_image['img_url1']; ?>" target="_blank" > <u>Download Warranty Card</u>  </a>
												</span>
												<?php }else{ ?>
												<span> 
												<a href="<?php echo $row_image['img_url1']; ?>" target="_blank"><img src="<?=$row_image['img_url1']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></a>
												</span>
												<?php } ?>
												<?php 
													} else {
														echo "";
													}
												?>	
												<?php } if($row_image['img_url2']!="") {
													$four_str = substr($row_image['img_url2'], -4);
													//echo "<br><br>";
													$five_str = substr($row_image['img_url2'], -5);
													//echo "<br><br>";
													$four_str_ext = substr($four_str, 0, 1);
													//echo "<br><br>";
													$five_str_ext = substr($five_str, 0, 1);
													//echo "<br><br>";
													if($four_str_ext == "." || $five_str_ext == "." ) {
												?>
												<span> 
												<a href="<?php echo $row_image['img_url2']; ?>" target="_blank"><img src="<?=$row_image['img_url2']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></a>
												</span>
												<?php 
														} else {
														echo "";
													}
												?>		
												<?php } if($row_image['img_url3']!="") {
													$four_str = substr($row_image['img_url3'], -4);
													//echo "<br><br>";
													$five_str = substr($row_image['img_url3'], -5);
													//echo "<br><br>";
													$four_str_ext = substr($four_str, 0, 1);
													//echo "<br><br>";
													$five_str_ext = substr($five_str, 0, 1);
													//echo "<br><br>";
													if($four_str_ext == "." || $five_str_ext == "." ) {
												?>
												<span> 
												<a href="<?php echo $row_image['img_url3']; ?>" target="_blank"><img src="<?=$row_image['img_url3']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></a>
												</span>
												<?php 
														} else {
														echo "";
													}
												?>			
												<?php } if($row_image['img_url4']!="") {
													$four_str = substr($row_image['img_url4'], -4);
													//echo "<br><br>";
													$five_str = substr($row_image['img_url4'], -5);
													//echo "<br><br>";
													$four_str_ext = substr($four_str, 0, 1);
													//echo "<br><br>";
													$five_str_ext = substr($five_str, 0, 1);
													//echo "<br><br>";
													if($four_str_ext == "." || $five_str_ext == "." ) {
												?>
												<span> 
												<a href="<?php echo $row_image['img_url4']; ?>" target="_blank"><img src="<?=$row_image['img_url4']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></a>
												</span>
												<?php 
														} else {
														echo "";
													}
												?>				
												<?php } if($row_image['img_url6']!="") {
													$four_str = substr($row_image['img_url6'], -4);
													//echo "<br><br>";
													$five_str = substr($row_image['img_url6'], -5);
													//echo "<br><br>";
													$four_str_ext = substr($four_str, 0, 1);
													//echo "<br><br>";
													$five_str_ext = substr($five_str, 0, 1);
													//echo "<br><br>";
													if($four_str_ext == "." || $five_str_ext == "." ) {
												?>
													<span> 
													<a href="<?php echo $row_image['img_url6']; ?>" target="_blank"><img src="<?=$row_image['img_url6']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></a>
													</span>
												<?php 
													} else {
													echo "";
												}
												}
												?>	
												</div>	
											</td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div><!--close panel body-->
						</div><!--close panel-->

						<?php $repair_history = mysqli_query($link1, "SELECT * FROM repair_detail where job_no='" . $docid . "'");
						if (mysqli_num_rows($repair_history) > 0) { ?>
							<div class="panel panel-info table-responsive">
							<div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;&nbsp;Repair Detail</div>
							<div class="panel-body">
								<table class="table table-bordered" width="100%">
								<thead>
									<tr>
									<td width="15%"><strong>Condition</strong></td>
									<td width="15%"><strong>Symptom</strong></td>
									<td width="10%"><strong>Section</strong></td>
									<td width="15%"><strong>Repair Location</strong></td>
									<td width="15%"><strong>Defect Name</strong></td>
									<td width="10%"><strong>Solution Given Name</strong></td>
									<td width="10%"><strong>Partcode</strong></td>
									<td width="10%"><strong>Engineer Name</strong></td>
									<td width="10%"><strong>New Serial</strong></td>
									<td width="10%"><strong>Old Serial</strong></td>
									<td width="10%"><strong>Remark</strong></td>
									<td width="10%"><strong>Update Date</strong></td>
									<td width="10%"><strong>Partcode (Repaired)</strong></td>
									<td width="10%"><strong>Partcode (Faulty Created)</strong></td>
									</tr>
								</thead>
								<tbody>
									<?php

									while ($repair_info = mysqli_fetch_assoc($repair_history)) {
									?>
									<tr>
										<td><?= getAnyDetails($repair_info['condition_code'], "condition_desc", "condition_code", "condition_master", $link1); ?></td>
										<td><?= getAnyDetails($repair_info['symptom_code'], "symp_desc", "symp_code", "symptom_master", $link1); ?></td>
										<td><?= getAnyDetails($repair_info['section_code'], "section_desc", "section_code", "section_master", $link1); ?></td>

										<td><?= getAnyDetails($repair_info['repair_location'], "locationname", "location_code", "location_master", $link1); ?></td>
										<td><?= getAnyDetails($repair_info['fault_code'], "defect_desc", "defect_code", "defect_master", $link1); ?></td>
										<td><?= getAnyDetails($repair_info['repair_code'], "rep_desc", "rep_code", "repaircode_master", $link1); ?></td>
										<td><?= getAnyDetails($repair_info['partcode'], "part_name", "partcode", "partcode_master", $link1); ?></td>
										<td><?= getAnyDetails($repair_info['eng_id'], "locusername", "userloginid", "locationuser_master", $link1); ?></td>
										<td><?= $repair_info['replace_serial'] ?></td>
										<td><?= $repair_info['old_serial'] ?></td>
										<td><?= $repair_info['remark'] ?></td>
										<td><?= $repair_info['update_date'] ?></td>
										<td><?= $repair_info['partcode'] ?></td>
										<td><?php if($repair_info['module']==""){ echo $repair_info['partcode']; }else{ echo $repair_info['module']; } ?></td>
									</tr>
									<?php
									}
									?>
								</tbody>
								</table>
							</div>
							<!--close panel body-->
							</div>
							<!--close panel-->

						<?php } ?>

						


						<div class="panel panel-info table-responsive">
							<div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;&nbsp;History</div>
							<div class="panel-body">
								<table class="table table-bordered" width="100%">
									<thead>	
										<tr>
											<td width="15%"><strong>Location</strong></td>
											<td width="10%"><strong>Activity</strong></td>
											<td width="15%"><strong>Outcome</strong></td>
											<td width="10%"><strong>Warranty</strong></td>
											<td width="10%"><strong>Status</strong></td>
											<td width="10%"><strong>Update By</strong></td>
											<td width="25%"><strong>Remark</strong></td>
											<td width="15%"><strong>Update on</strong></td>
										</tr>
									</thead>
									<tbody>
										<?php
										$res_jobhistory = mysqli_query($link1,"SELECT * FROM call_history where job_no='".$docid."'");
										while($row_jobhistory = mysqli_fetch_assoc($res_jobhistory)){
										?>
										<tr>
											<td><?=$row_jobhistory['location_code']?></td>
											<td><?=$row_jobhistory['activity']?></td>
											<td><?=$row_jobhistory['outcome']?></td>
											<td><?=$row_jobhistory['warranty_status']?></td>
											<td><?=$row_jobhistory['status']?></td>
											<td><?=$row_jobhistory['updated_by']?></td>
											<td><?=$row_jobhistory['remark']?></td>
											<td><?=$row_jobhistory['update_date']?></td>
										</tr>
										<?php
											}
										?>
										<?php   if($job_row['status'] == '9' && $job_row['sub_status'] != '91')  {?>
										<tr>
											<td width="100%" align="center" colspan="8"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list_doa.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'"></td>
										</tr>
										<?php  } ?>
									</tbody>
								</table>
							</div><!--close panel body-->
						</div><!--close panel-->



						
          <?php $initial_qr = mysqli_query($link1, "SELECT * FROM initial_btr_data where job_no='" . $docid . "' order by id DESC ");
						if (mysqli_num_rows($initial_qr) > 0) { ?>
							<!--<div class="panel panel-info table-responsive">
							<div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;&nbsp;REPORT BEFORE CHARGING</div>
							<div class="panel-body">
								<table class="table table-bordered" width="100%">
								<thead>
									<tr>
									<td width="15%"><strong>C1 Voltage</strong></td>
									<td width="15%"><strong>C2 Voltage</strong></td>
									<td width="10%"><strong>C3 Voltage</strong></td>
									<td width="15%"><strong>C4 Voltage</strong></td>
									<td width="15%"><strong>C5 Voltage</strong></td>
									<td width="10%"><strong>C6 Voltage</strong></td>
                  <td width="10%"><strong>C7 Voltage</strong></td>
									<td width="10%"><strong>OCV</strong></td>
									<td width="10%"><strong>SG C1</strong></td>
									<td width="10%"><strong>SG C2</strong></td>
									<td width="10%"><strong>SG C3</strong></td>
									<td width="10%"><strong>SG C4</strong></td>
									<td width="10%"><strong>SG C5</strong></td>
									<td width="10%"><strong>SG C6</strong></td>
                  <td width="10%"><strong>Temperature</strong></td>
                  <td width="10%"><strong>MET Status</strong></td>
									</tr>
								</thead>
								<tbody>
									<?php

									while($initial_info = mysqli_fetch_assoc($initial_qr)) {
									?>
									<tr>
										<td><?= $initial_info['c1'] ?></td>
										<td><?= $initial_info['c2'] ?></td>
										<td><?= $initial_info['c3'] ?></td>
										<td><?= $initial_info['c4'] ?></td>
										<td><?= $initial_info['c5'] ?></td>
										<td><?= $initial_info['c6'] ?></td>
                    <td><?= $initial_info['c7'] ?></td>
										<td><?= $initial_info['ocv'] ?></td>
										<td><?= $initial_info['sg_c1'] ?></td>
										<td><?= $initial_info['sg_c2'] ?></td>
										<td><?= $initial_info['sg_c3'] ?></td>
										<td><?= $initial_info['sg_c4'] ?></td>
										<td><?= $initial_info['sg_c5'] ?></td>
										<td><?= $initial_info['sg_c6'] ?></td>
                    <td><?= $initial_info['temperature'] ?></td>
                    <td><?= $initial_info['met_status'] ?></td>
                    
									</tr>
									<?php
									}
									?>
								</tbody>
								</table>
							</div>
							
							</div>
						

						<?php } ?>

						<?php $after_qr = mysqli_query($link1, "SELECT * FROM final_btr_data where job_no='" . $docid . "' order by id DESC ");
						if (mysqli_num_rows($after_qr) > 0) { ?>
							<div class="panel panel-info table-responsive">
							<div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;&nbsp;REPORT AFTER CHARGING</div>
							<div class="panel-body">
								<table class="table table-bordered" width="100%">
								<thead>
									<tr>
									<td width="15%"><strong>C1 Voltage</strong></td>
									<td width="15%"><strong>C2 Voltage</strong></td>
									<td width="10%"><strong>C3 Voltage</strong></td>
									<td width="15%"><strong>C4 Voltage</strong></td>
									<td width="15%"><strong>C5 Voltage</strong></td>
									<td width="10%"><strong>C6 Voltage</strong></td>
                  <td width="10%"><strong>C7 Voltage</strong></td>
									<td width="10%"><strong>TOC</strong></td>
									<td width="10%"><strong>OCV</strong></td>
									<td width="10%"><strong>SG C1</strong></td>
									<td width="10%"><strong>SG C2</strong></td>
									<td width="10%"><strong>SG C3</strong></td>
									<td width="10%"><strong>SG C4</strong></td>
									<td width="10%"><strong>SG C5</strong></td>
									<td width="10%"><strong>SG C6</strong></td>
                  <td width="10%"><strong>Temperature</strong></td>
									</tr>
								</thead>
								<tbody>
									<?php

									while($after_info = mysqli_fetch_assoc($after_qr)) {
									?>
									<tr>
										<td><?= $after_info['c1'] ?></td>
										<td><?= $after_info['c2'] ?></td>
										<td><?= $after_info['c3'] ?></td>
										<td><?= $after_info['c4'] ?></td>
										<td><?= $after_info['c5'] ?></td>
										<td><?= $after_info['c6'] ?></td>
                    <td><?= $after_info['c7'] ?></td>
										<td><?= $after_info['toc'] ?></td>
										<td><?= $after_info['ocv'] ?></td>
										<td><?= $after_info['sg_c1'] ?></td>
										<td><?= $after_info['sg_c2'] ?></td>
										<td><?= $after_info['sg_c3'] ?></td>
										<td><?= $after_info['sg_c4'] ?></td>
										<td><?= $after_info['sg_c5'] ?></td>
										<td><?= $after_info['sg_c6'] ?></td>
                    <td><?= $after_info['temperature'] ?></td>
									</tr>
									<?php
									}
									?>
								</tbody>
								</table>
							</div>
							
							</div>-->
							<!--close panel-->

						<?php } ?>
						
						
						<!-- MET Data -->
	
		<!--<div class="panel panel-info table-responsive">
            <div class="panel-heading">
              <i class="fa fa-history fa-lg"></i>&nbsp;&nbsp;MET Data (Job No - <?php echo $docid; ?>)
				<b style="float: right;">Summery Data <a href="../excelReports/met_summery_report_excel.php?rname=<?=base64_encode("met_summery_report_excel")?>&rheader=<?=base64_encode("MET Summery Report")?>&job_no=<?=base64_encode($docid)?>" title="Export details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export details in excel"></i></a></b>

        <b style="float: right;">Get Raw Data <a href="../excelReports/get_raw_data_report_excel.php?rname=<?=base64_encode("get_raw_data_report_excel")?>&rheader=<?=base64_encode("Get Raw Data Report")?>&jobno=<?=base64_encode($docid)?>" title="Export details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export details in excel"></i></a></b>

				  </div>
            <div class="panel-body">
              <table class="table table-bordered" width="100%">
                <thead>
                  <tr>
                    <td width="15%"><strong>Cycle</strong></td>
					<td width="15%"><strong>Cycle No</strong></td> 
                    <td width="10%"><strong>Start (date/time)</strong></td>
                    <td width="10%"><strong>End (date/time)</strong></td>
                   
                    <td width="10%"><strong>Total Time in Hrs :Min</strong></td>
                    <td width="10%"><strong>Start Voltage</strong></td>
					<td width="10%"><strong>Highest Voltage</strong></td>
					<td width="10%"><strong>Lowest Voltage</strong></td> 
					<td width="10%"><strong>Average Current</strong></td>
                    <td width="15%"><strong>AH in/Out</strong></td>
					
					<td width="15%"><strong>Temperature (°C)</strong></td>
					
					  <td width="15%"><strong>Start SOC% <p style="font-size: 10px;">( % when Device is connected(@ No Load, No charge)</p></strong></td>
					  <td width="15%"><strong>End SOC% <p style="font-size: 10px;">( % when Device is connected(@ No Load, No charge)</p></strong></td>
                    <td width="10%"><strong>Update (date/time)</strong></td>
                  </tr>
                </thead>
                <tbody>
                  <?php

                  $res_group = mysqli_query($link1, "SELECT * FROM met_data where job_no='" . $docid . "' and type!='summery' group by cycle_no ORDER BY id ASC");
                  while($row_group = mysqli_fetch_assoc($res_group)) {

                  $res_jobhistory = mysqli_query($link1, "SELECT * FROM met_data where job_no='" . $docid . "' and cycle_no = '".$row_group['cycle_no']."' and cycle='Charge' and type!='summery' ORDER BY id DESC LIMIT 1 "); ///LIMIT 1
				  								 
                  while ($row_jobhistory = mysqli_fetch_assoc($res_jobhistory)) {
                  ?>
                    <tr>
                      <td><?= $row_jobhistory['cycle'] ?></td>
					  <td><?= $row_jobhistory['cycle_no'] ?></td>	
                      <td><?= $row_jobhistory['start_date'] ?> <?= $row_jobhistory['start_time'] ?></td>
                      
                      <td><?= $row_jobhistory['end_date'] ?> <?= $row_jobhistory['end_time'] ?></td>
                     
                      <td><?= $row_jobhistory['total_time'] ?></td>
                      <td><?= $row_jobhistory['Start_Voltage_V'] ?></td>
					  <td><?= $row_jobhistory['Highest_Voltage_V'] ?></td>
					  <td><?= $row_jobhistory['Lowest_Voltage_V'] ?></td>	
					  <td><?= $row_jobhistory['Average_Current_A'] ?></td>	
                      <td><?= $row_jobhistory['ah'] ?></td>
					  
						<?php
					    $measured_ocv = $row_jobhistory['Start_Voltage_V'];
					    $measured_ocv_end = $row_jobhistory['Lowest_Voltage_V'];
					  //$jobsheet = mysqli_fetch_array(mysqli_query($link1,"select ext21 from jobsheet_data where job_no ='".$docid."'"));
					    $temprature = 0;
						$sumof_sg_befor = mysqli_fetch_array(mysqli_query($link1, "SELECT SUM(sg_c1+sg_c2+sg_c3+sg_c4+sg_c5+sg_c6) as total_sg FROM initial_btr_data where job_no='" . $docid . "' order by id DESC "));

            $sumof_sg_befor1 = mysqli_fetch_array(mysqli_query($link1, "SELECT temperature FROM initial_btr_data where job_no='" . $docid . "' order by id DESC "));

            if($sumof_sg_befor1['temperature']!=""){
              $temprature = $sumof_sg_befor1['temperature'];
            }else{
              $temprature = 0;
            }
            

					    $measured_sg1 = $sumof_sg_befor['total_sg']/6;
					    $measured_sg = number_format($measured_sg1,3);
                        $Corrected_OCV1 = $measured_ocv + 0.012*(25-$temprature);
					    $Corrected_OCV = number_format($Corrected_OCV1,3);
					    $Corrected_SG1 = $measured_sg+0.0007*(25-$temprature);
					    $Corrected_SG = number_format($Corrected_SG1,4);
                        $SOC_from_OCV1 = ($Corrected_OCV - 10.6) / 2.05 * 100;
					    $SOC_from_OCV = number_format($SOC_from_OCV1,1);
                        $SOC_from_SG1 = ($Corrected_SG-1.15)/0.11*100;
					    $SOC_from_SG=number_format($SOC_from_SG1,1);
                        $Average_SOC = ($SOC_from_OCV+$SOC_from_SG)/2;
//print_r(number_format($Average_SOC,1));exit;
					  
					    $Corrected_OCV_end1 = $measured_ocv_end + 0.012*(25-$temprature);
					    $Corrected_OCV_end = number_format($Corrected_OCV_end1,3);
					    $SOC_from_OCV_end1 = ($Corrected_OCV_end - 10.6) / 2.05 * 100;
					    $SOC_from_OCV_end = number_format($SOC_from_OCV_end1,1);
                        $Average_SOC_end = ($SOC_from_OCV_end+$SOC_from_SG)/2;

					  
					  
					  
					  
					  /*$measured_sg = $sumof_sg_befor['total_sg']/6;
					    $Corrected_SG = $measured_sg+0.0007*(25-$temprature);
					    $SOC_from_SG_per = ($Corrected_SG-1.15)/0.11*100;
					  
					  //print_r($sumof_sg['total_sg']);
					  
					  
					  $Corrected_OCV_v = $measured_ocv + 0.012*(25-$temprature);
					  
					  $Corrected_SG_after = $measured_sg_after+0.0007*(25-$temprature);
					  
					  $SOC_from_OCV_per = ($Corrected_OCV_v - 10.6) / 2.05 * 100;
					  $SOC_from_SG_per_after = ($Corrected_SG_after-1.15)/0.11*100;
					  
					  $Average_SOC_per = ($Corrected_OCV_v+$Corrected_SG)/2;
					  $Average_SOC_per_after = ($Corrected_OCV_v+$Corrected_SG_after)/2;*/
						?>
						

						<td><?=$temprature;?></td>

            <?php
            $Average_SOC_val = "";
            $Average_SOC_end_val = "";
            if(number_format($Average_SOC,2)>100){
              $Average_SOC_val = 100;
            }else if(number_format($Average_SOC,2)<0){
              $Average_SOC_val = 0;
            }else{
              $Average_SOC_val = number_format($Average_SOC,2);
            }

            if(number_format($Average_SOC_end,2)>100){
              $Average_SOC_end_val = 100;
            }else if(number_format($Average_SOC_end,2)<0){
              $Average_SOC_end_val = 0;
            }else{
              $Average_SOC_end_val = number_format($Average_SOC_end,2);
            }

            ?>

						<td><?=$Average_SOC_val?></td>
						<td><?=$Average_SOC_end_val?></td>
						
					 
                      <td><?= $row_jobhistory['update_date'] ?></td>
                    </tr>
                  <?php
                  }

                  $res_jobhistory1 = mysqli_query($link1, "SELECT * FROM met_data where job_no='" . $docid . "' and cycle_no = '".$row_group['cycle_no']."' and cycle='Discharge' and type!='summery' ORDER BY id DESC LIMIT 1");		//LIMIT 1	

				while ($row_jobhistory = mysqli_fetch_assoc($res_jobhistory1)) {
                  ?>
                    <tr>
                      <td><?= $row_jobhistory['cycle'] ?></td>
					  <td><?= $row_jobhistory['cycle_no'] ?></td>	
                      <td><?= $row_jobhistory['start_date'] ?> <?= $row_jobhistory['start_time'] ?></td>
                      
                      <td><?= $row_jobhistory['end_date'] ?> <?= $row_jobhistory['end_time'] ?></td>
                     
                      <td><?= $row_jobhistory['total_time'] ?></td>
                      <td><?= $row_jobhistory['Start_Voltage_V'] ?></td>
					  <td><?= $row_jobhistory['Highest_Voltage_V'] ?></td>
					  <td><?= $row_jobhistory['Lowest_Voltage_V'] ?></td>	
					  <td><?= $row_jobhistory['Average_Current_A'] ?></td>
                      <td><?= $row_jobhistory['ah'] ?></td>
					  
						<?php
					    $measured_ocv = $row_jobhistory['Start_Voltage_V'];
						$measured_ocv_end = $row_jobhistory['Lowest_Voltage_V'];
					    $temprature1 = 0;
					  	$sumof_sg_after = mysqli_fetch_array(mysqli_query($link1, "SELECT SUM(sg_c1+sg_c2+sg_c3+sg_c4+sg_c5+sg_c6) as total_sg FROM final_btr_data where job_no='" . $docid . "' order by id DESC "));

              $sumof_sg_after1 = mysqli_fetch_array(mysqli_query($link1, "SELECT temperature FROM final_btr_data where job_no='" . $docid . "' order by id DESC "));

              if($sumof_sg_after1['temperature']!=""){
                $temprature1 = $sumof_sg_after1['temperature'];
              }else{
                $temprature1 = 0;
              }

						$measured_sg1 = $sumof_sg_after['total_sg']/6;
					    $measured_sg = number_format($measured_sg1,3);
                        $Corrected_OCV1 = $measured_ocv + 0.012*(25-$temprature1);
					    $Corrected_OCV = number_format($Corrected_OCV1,3);
					    $Corrected_SG1 = $measured_sg+0.0007*(25-$temprature1);
					    $Corrected_SG = number_format($Corrected_SG1,4);
                        $SOC_from_OCV1 = ($Corrected_OCV - 10.6) / 2.05 * 100;
					    $SOC_from_OCV = number_format($SOC_from_OCV1,1);
                        $SOC_from_SG1 = ($Corrected_SG-1.15)/0.11*100;
					    $SOC_from_SG=number_format($SOC_from_SG1,2);
                        $Average_SOC = ($SOC_from_OCV+($SOC_from_SG))/2;
					//print_r($Average_SOC);exit;
					
						$Corrected_OCV_end1 = $measured_ocv_end + 0.012*(25-$temprature1);
					    $Corrected_OCV_end = number_format($Corrected_OCV_end1,3);
					    $SOC_from_OCV_end1 = ($Corrected_OCV_end - 10.6) / 2.05 * 100;
					    $SOC_from_OCV_end = number_format($SOC_from_OCV_end1,1);
                        $Average_SOC_end = ($SOC_from_OCV_end+$SOC_from_SG)/2;
						?>
						
						
            <td><?=$temprature1;?></td>

            <?php
            $Average_SOC_val = "";
            $Average_SOC_end_val = "";
            if(number_format($Average_SOC,2)>100){
              $Average_SOC_val = 100;
            }else if(number_format($Average_SOC,2)<0){
              $Average_SOC_val = 0;
            }else{
              $Average_SOC_val = number_format($Average_SOC,2);
            }

            if(number_format($Average_SOC_end,2)>100){
              $Average_SOC_end_val = 100;
            }else if(number_format($Average_SOC_end,2)<0){
              $Average_SOC_end_val = 0;
            }else{
              $Average_SOC_end_val = number_format($Average_SOC_end,2);
            }

            ?>

						<td><?=$Average_SOC_val?></td>
						<td><?=$Average_SOC_end_val?></td>
						
					
                      <td><?= $row_jobhistory['update_date'] ?></td>
                    </tr>
                  <?php
                  }		
                }									 
                  ?>
                </tbody>
              </table>
            </div>
          
          </div>-->

						

						<!--approval for EP-->
						<?php if($job_row['status'] == '81' && $job_row['sub_status'] == '8'){ ?>
						<form id="frm1" name="frm1" method="post" >
							
							
							
							
							
							
							
							
							
							
							<div id="btr_form" >	  
		<?php 
					 // $product_name_btr = getAnyDetails($job_row['product_id'],"product_name","product_id","product_master",$link1);
					  // $btr_product =  mysqli_num_rows(mysqli_query($link1,"select * from product_master where product_name in ('".$product_name_btr."') and status= '1' "));
					  //print_r($job_row);
					  if($job_row['product_id']==4 || $job_row['product_id']==5 || $job_row['product_id']==9 || $job_row['product_id']==10){
					  $sql_btr="select * from initial_btr_data where job_no='".$docid."' order by id desc";
	                  $result_btr_initial=mysqli_fetch_array(mysqli_query($link1,$sql_btr));
					  $sql_btr_f="select * from final_btr_data where job_no='".$docid."' order by id desc";
	                  $result_btr_final=mysqli_fetch_array(mysqli_query($link1,$sql_btr_f));
					  ?>			  
					  
		<div class="panel panel-info" style="margin-top: 25px;">

              <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Battery Inspection Details(Before)</div>

              <div class="panel-body">

              

              	<div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 10px;"> OCV <span class="red_small">*</span></label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="ocv_befor" id="ocv_befor" type="text" value="<?=$result_btr_initial['ocv']?>" class="required form-control  " required maxlength="5" style="width:62px;">
   

                      </div>

                    </div>

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 10px;">C1 + Ve <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="c1_befor" id="c1_befor" type="text" value="<?=$result_btr_initial['c1']?>" class="required form-control  " required min="1.100" max="1.300" maxlength="5" style="width:62px;">
   
                      </div>

                    </div>
					 <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 10px;">C2 + Ve <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="c2_befor" id="c2_befor" type="text" value="<?=$result_btr_initial['c2']?>" class="required form-control  " required min="1.100" max="1.300" maxlength="5" style="width:62px;">
   
                      </div>

                    </div>

                  </div>

                  	<div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 10px;"> C3 + Ve <span class="red_small">*</span></label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="c3_befor" id="c3_befor" type="text" value="<?=$result_btr_initial['c3']?>" class="required form-control  " required min="1.100" max="1.300" maxlength="5" style="width:62px;">
   

                      </div>

                    </div>

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 10px;">C4 + Ve <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="c4_befor" id="c4_befor" type="text" value="<?=$result_btr_initial['c4']?>" class="required form-control  " required min="1.100" max="1.300" maxlength="5" style="width:62px;">
   
                      </div>

                    </div>
					 <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 10px;">C5 + Ve <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="c5_befor" id="c5_befor" type="text" value="<?=$result_btr_initial['c5']?>" class="required form-control  " required min="1.100" max="1.300" maxlength="5" style="width:62px;">
   
                      </div>

                    </div>

                  </div>
				  
				  	<div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 10px;"> C6 + Ve <span class="red_small">*</span></label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="c6_befor" id="c6_befor" type="text" value="<?=$result_btr_initial['c6']?>" class="required form-control  " required min="1.100" max="1.300" maxlength="5" style="width:62px;">
   

                      </div>

                    </div>

                   <!-- <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">C4 + Ve <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="imei_serial1" id="imei_serial1" type="text" value="" class="required form-control  " onkeyup="getdate4();">
   
                      </div>

                    </div>
					 <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">C5 + Ve <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="imei_serial1" id="imei_serial1" type="text" value="" class="required form-control  " onkeyup="getdate4();">
   
                      </div>

                    </div>-->

                  </div>


				  
              </div>

            </div>

<div class="panel panel-info" style="margin-top: 25px;">

              <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Battery Inspection Details(After)</div>

              <div class="panel-body">

              

              	<div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;width: fit-content;"> Total Charging Hours </label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="tot_chrg_hr" id="tot_chrg_hr1" type="text" value="<?=$result_btr_final['charging_hour']?>" class=" form-control  "  placeholder="HH:MM" style="width:62px;">
   

                      </div>

                    </div>

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">TOC  </label>

                      <div class="col-md-4">
	<input name="toc" id="toc" type="text" value="<?=$result_btr_final['toc']?>" class=" form-control  "  style="width:62px;">
   
                      </div>

                    </div>
					 <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">OCV  </label>

                      <div class="col-md-4">
	<input name="ocv_aftr" id="ocv_aftr" type="text" value="<?=$result_btr_final['ocv']?>" class=" form-control  "  style="width:62px;">
   
                      </div>

                    </div>

                  </div>

                  	<div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;"> C1 + Ve </label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="c1_aftr" id="c1_aftr" type="text" value="<?=$result_btr_final['c1']?>" class=" form-control  "  min="1.100" max="1.300" maxlength="5" style="width:62px;">
   

                      </div>

                    </div>

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">C2 + Ve  </label>

                      <div class="col-md-4">
	<input name="c2_aftr" id="c2_aftr" type="text" value="<?=$result_btr_final['c2']?>" class=" form-control  "  min="1.100" max="1.300" maxlength="5" style="width:62px;">
   
                      </div>

                    </div>
					 <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">C3 + Ve  </label>

                      <div class="col-md-4">
	<input name="c3_aftr" id="c3_aftr" type="text" value="<?=$result_btr_final['c3']?>" class=" form-control  "  min="1.100" max="1.300" maxlength="5" style="width:62px;">
   
                      </div>

                    </div>

                  </div>
				  
				  	<div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;"> C4 + Ve </label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="c4_aftr" id="c4_aftr" type="text" value="<?=$result_btr_final['c4']?>" class=" form-control  "  min="1.100" max="1.300" maxlength="5" style="width:62px;">
   

                      </div>

                    </div>

                  <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">C5 + Ve  </label>

                      <div class="col-md-4">
	<input name="c5_aftr" id="c5_aftr" type="text" value="<?=$result_btr_final['c5']?>" class=" form-control  "  min="1.100" max="1.300" maxlength="5" style="width:62px;">
   
                      </div>

                    </div>
					 <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">C6 + Ve  </label>

                      <div class="col-md-4">
	<input name="c6_aftr" id="c6_aftr" type="text" value="<?=$result_btr_final['c6']?>" class=" form-control  "  min="1.100" max="1.300" maxlength="5" style="width:62px;">
   
                      </div>

                    </div>

                  </div>


				  
              </div>

            </div>
					  
			<div class="panel panel-info" style="margin-top: 25px;">

              <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Battery Test Result</div>

              <div class="panel-body">

              

              	<div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label" style="padding-left: 13px;"> Discharging Current <span class="red_small">*</span></label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="dischrg_current" id="dischrg_current" type="text" value="<?=$result_btr_final['dischrg_current']?>" class="required form-control  " required  style="width: 61px;">
   

                      </div>

                    </div>

                    
					 

                  </div>
				  <div class="form-group" style="margin-bottom:10px;">

                   

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 10px;width: fit-content;">Back Up Time <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="backup_time" id="backup_time" type="text" value="<?=$result_btr_final['backup_time']?>" class="required form-control  " required style="width: 61px;">
   
                      </div>

                    </div>
					 

                  </div>

                  	<div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;width: fit-content;">Test Result <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="test_result" id="test_result" type="text" maxlength="2" value="<?=$result_btr_final['test_result']?>" class="required form-control  " required>
   
                      </div>

                    </div>

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;width: fit-content;">Load In Watt <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="load_in_watt" id="load_in_watt" type="text" value="<?=$result_btr_final['load_in_watt']?>" class="required form-control  " required  maxlength="3" style="width:62px;">
   
                      </div>

                    </div>
					 <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 10px;width: fit-content;">Cut off voltage <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="c_off_volt" id="c_off_volt" type="text" value="<?=$result_btr_final['cut_off_volt']?>" class="required form-control  " required  maxlength="5" style="width:62px;">
   
                      </div>

                    </div>

                  </div>
				  
				  	<div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;"> PCV 1 <span class="red_small">*</span></label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="pcv1" id="pcv1" type="text" value="<?=$result_btr_final['pcv1']?>" class="required form-control  " required  maxlength="5" min="0.000" max="1.999" style="width:62px;">
   

                      </div>

                    </div>

                  <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">PCV 2 <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="pcv2" id="pcv2" type="text" value="<?=$result_btr_final['pcv2']?>" class="required form-control  " required  maxlength="5" style="width:62px;" min="0.000" max="1.999">
   
                      </div>

                    </div>
					 <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">PCV 3 <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="pcv3" id="pcv3" type="text" value="<?=$result_btr_final['pcv3']?>" class="required form-control  " required  maxlength="5" style="width:62px;" min="0.000" max="1.999">
   
                      </div>

                    </div>

                  </div>
<div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;"> PCV 4 <span class="red_small">*</span></label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="pcv4" id="pcv4" type="text" value="<?=$result_btr_final['pcv4']?>" class="required form-control  " required  maxlength="5" style="width:62px;" min="0.000" max="1.999">
   

                      </div>

                    </div>

                  <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">PCV 5 <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="pcv5" id="pcv5" type="text" value="<?=$result_btr_final['pcv5']?>" class="required form-control  " required  maxlength="5" style="width:62px;" min="0.000" max="1.999">
   
                      </div>

                    </div>
					 <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">PCV 6 <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="pcv6" id="pcv6" type="text" value="<?=$result_btr_final['pcv6']?>" class="required form-control  " required maxlength="5" style="width:62px;" min="0.000" max="1.999">
   
                      </div>

                    </div>

                  </div>
				  <div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;"> PCV 7 <span class="red_small">*</span></label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="pcv7" id="pcv7" type="text" value="<?=$result_btr_final['pcv7']?>" class="required form-control  " required  maxlength="5" style="width:62px;" min="0.000" max="1.999">
   

                      </div>

                    </div>

					 

                  </div>
				  <div class="form-group">

                   

                  <div class="col-md-6"><label class="col-md-6 custom_label" style="padding-left: 7px;">Invertor Load Test Result <span class="red_small">*</span> </label>

                      <div class="col-md-6">
	<!--<input name="invt_load_tst_reslt" id="invt_load_tst_reslt" type="text" value="<?=$result_btr_final['invt_load_tst_reslt']?>" class="required form-control  " required  maxlength="5" style="width:62px;">-->
						  <select name="invt_load_tst_reslt" id="invt_load_tst_reslt1"  class="  form-control" style="width:250px;" onchange="getEngineerDetection(this.value)">
                      <option value="" <?php if($result_btr_final['invt_load_tst_reslt']==''){echo "selected";}?>>--Select --</option>
                      <option value="TEST FAIL" <?php if($result_btr_final['invt_load_tst_reslt']=='TEST FAIL'){echo "selected";}?>> TEST FAIL </option>
					  <option value="TEST PASS" <?php if($result_btr_final['invt_load_tst_reslt']=='TEST PASS'){echo "selected";}?>> TEST PASS </option>	  
                      <option value="RECHARGE" <?php if($result_btr_final['invt_load_tst_reslt']=='RECHARGE'){echo "selected";}?>> RECHARGE </option>
					  <option value="REJECT" <?php if($result_btr_final['invt_load_tst_reslt']=='REJECT'){echo "selected";}?>> REJECT </option>                    
                      						
				
				
                    </select>
   
                      </div>

                    </div>
					                   <div class="col-md-6" id="eng_detection1" style="display:none;"><label class="col-md-6 custom_label" style="padding-left: 7px;" >Engineer Detection <span class="red_small">*</span> </label>

                      <div class="col-md-6">
	<!--<input name="invt_load_tst_reslt" id="invt_load_tst_reslt" type="text" value="<?=$result_btr_final['invt_load_tst_reslt']?>" class="required form-control  " required  maxlength="5" style="width:62px;">-->
						  <select name="eng_detection" id="eng_detection"  class="  form-control" style="width:250px;">
                      <option value="" >--Select --</option>
                      <option value="TD" <?php if($result_btr_final['eng_detection']=='TD'){echo "selected";}?>> TD </option>
					  <option value="DAMAGE" <?php if($result_btr_final['eng_detection']=='DAMAGE'){echo "selected";}?>> DAMAGE </option>	  
                      <option value="Plate shedding" <?php if($result_btr_final['eng_detection']=='Plate shedding'){echo "selected";}?>> Plate shedding </option>
					  <option value="REJECT" <?php if($result_btr_final['eng_detection']=='Single cell Short'){echo "selected";}?>> Single cell Short </option>                    
							   <option value="Bulge" <?php if($result_btr_final['eng_detection']=='Bulge'){echo "selected";}?>> Bulge </option>
							   <option value="Burst" <?php if($result_btr_final['eng_detection']=='Burst'){echo "selected";}?>> Burst </option>
							   <option value="Heat seal leakage" <?php if($result_btr_final['eng_detection']=='Heat seal leakage'){echo "selected";}?>> Heat seal leakage </option>
							   <option value="Battery Use in wrong application" <?php if($result_btr_final['eng_detection']=='Battery Use in wrong application'){echo "selected";}?>> Battery Use in wrong application </option>
							   <option value="Pole Disconnection (Internally)" <?php if($result_btr_final['eng_detection']=='Pole Disconnection (Internally)'){echo "selected";}?>> Pole Disconnection (Internally) </option>
							   <option value="Continunos Charging" <?php if($result_btr_final['eng_detection']=='Continunos Charging'){echo "selected";}?>> Continunos Charging </option>
                      						
				
				
                    </select>
   
                      </div>

                    </div>

                  </div>


				  
              </div>

            </div>		  
					  
					  
					  
<?php } ?>
				  </div>
							
							
							
							
							
							
							
							
							
							
							
							
							
							
							<div class="panel panel-info table-responsive">
								<div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Replacement Request Action</div>
								<div class="panel-body">
									<table class="table table-bordered" width="100%">
										<tbody>
											<tr>
												<td width="20%"><label class="control-label">Call Type</label></td>
												<td width="30%"><?php echo $job_row['repeatcall']; ?></td>
												<td width="20%"><label class="control-label">Serial Replaced By</label></td>
												<td width="30%">
													<select id="sr_repl_by" name="sr_repl_by" class="form-control" >
														<!--<option value="Partner" >Partner</option>-->
														<option value="Self" >Self (Location)</option>
													</select>
												</td>
											</tr>	
											<tr>
												<td width="20%"><label class="control-label">Action</label></td>
												<td width="30%">
													<select id="status" name="status" class="form-control" required onchange="check_reject_reason(this.value);" >
														<option value="">Please Select</option>
														<option value="82" >Approved</option>
														<option value="83" >Rejected</option>
														  <option value="99" >Tested Ok</option>
				                                          <option value="100" >Goodwill Approve</option>
														<option value="85" >Same Back-Battery found okay on back up test</option>
													</select>
												</td>
												<td width="20%"><label class="control-label">Remark</label></td>
												<td width="30%"><textarea id="remark" name="remark" class="form-control" required></textarea></td>
											</tr>
											
											<tr>
												<td ><label class="control-label" id="rr_flag" style="display: none;" >Rejection Reason</label></td>
												<td >
													<select id="rejection_reason" name="rejection_reason" class="form-control" style="display: none;" >
														<option value="">Please Select</option>
														<option value="Blur Image" >Blur Image</option>
														<option value="Incorrect Sr No" >Incorrect Sr No</option>
														<option value="Bill Not Valid" >Bill Not Valid</option>
														<option value="Bill Overwriting" >Bill Overwriting</option>
														<option value="Bill Without Stamp/Signature" >Bill Without Stamp/Signature</option>
														<option value="Product is Out of Warranty" >Product is Out of Warranty</option>
														<option value="Original Warranty Card Required" >Original Warranty Card Required</option>
													</select>
												</td>
												<td ><label class="control-label"></label></td>
												<td ></td>
											</tr> 

											<!----
											<tr width="100%">
												<td width="20%"><label class="control-label" id="rpl1_flg" style="display: none;" >Replace Serial No</label></td>
												<td width="30%">
													<input type="text" name="replace_serial_no" id="replace_serial_no" class="form-control alphanumeric" minlength="10" maxlength="18" style="display: none;" />
												</td>
												<td width="20%"><label class="control-label" id="rpl2_flg" style="display: none;" >Replace Model</label></td>
												<td width="30%">
													<select id="replace_model_id" name="replace_model_id" class="form-control"  style="display: none;" >
														<option value="">Please Select</option>
														<?php 
															$dept_query="SELECT model_id,model,wp FROM model_master where status = '1' and product_id in ('11','46','50') order by model ";
															$check_dept=mysqli_query($link1,$dept_query);
															while($model_name = mysqli_fetch_array($check_dept)){
														?>
															<option value="<?php echo $model_name['model_id']; ?>"><?php echo $model_name['model']." | ".$model_name['model_id']; ?></option>
														<?php } ?>														
													</select>
												</td>
											</tr>---->

											<input type="hidden" name="cust_mob" id="cust_mob" value="<?php echo $job_row['contact_no']; ?>" />
							
											<tr>
												<td width="100%" align="center" colspan="8"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list_repl_btr.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'"> <input type="submit" id="update" name="update" value="Update" class="btn<?=$btncolor?>"></td>
											</tr>     
										</tbody>
									</table>
								</div><!--close panel body-->
							</div><!--close panel-->
						</form>
						<?php } ?>
						
					</div><!--close panel group-->
				</div><!--close col-sm-9-->
			</div><!--close row content-->
		</div><!--close container-fluid-->
		
		<script>
		function getEngineerDetection(val){
	
		var data1 = "<?=$result_btr_final['invt_load_tst_reslt']?>";
			//alert(data1);
		if(val=='TEST FAIL'){
		document.getElementById("eng_detection1").style.display = "";
		}else{
			
		document.getElementById("eng_detection1").style.display = "none";
		}
		/*if(data1=='TEST FAIL'){
			
		document.getElementById("eng_detection1").style.display = "";
		}	*/
	}
		function getEngineerDetection1(){
	
		var data1 = "<?=$result_btr_final['invt_load_tst_reslt']?>";
			//alert(data1);
		
		if(data1=='TEST FAIL'){
			
		document.getElementById("eng_detection1").style.display = "";
		}	
	}	
			
		</script>
		<div id="loader"></div>
		
		<?php
		include("../includes/footer.php");
		include("../includes/connection_close.php");
		?>
	</body>
</html>