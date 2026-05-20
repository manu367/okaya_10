<?php
require_once("../includes/config.php");
////decode post job no.
$jobno = base64_decode($_POST['postjobno']);
if($_POST['savejob']=="Save" && $jobno!='' && $_SESSION['asc_code']!=''){
	//// initialize transaction parameters
	$flag = true;
	mysqli_autocommit($link1, false);
	$error_msg = "";
	/////// fetch current job details
	$job_details = mysqli_fetch_assoc(mysqli_query($link1,"select * from jobsheet_data where job_no='".$jobno."'"));
	///////// save the job details
	$upd_str = "";
	$close_tat = "";
	
	
	////////////////////////////////check ENGineer Assign///////////////////
	
	if($job_details['eng_id']==''){
	$eng_name=$_POST['eng_name'];
	
	}
	else{
	$eng_name=$job_details['eng_id'];
	}
	### Start SFR CASE ########## if repair status select as SFR
	if($_POST['jobstatus'] == "4"){
	///////////////  update by priya on 19 july to block multiple job creation ////////////////////////////////////////////////////////
	$messageIdent_sfr = md5($_SESSION['asc_code'] . $jobno . $_POST['jobstatus']);
		//and check it against the stored value:
   		$sessionMessageIdent_sfr = isset($_SESSION['messageIdent_sfr'])?$_SESSION['messageIdent_sfr']:'';
		if($messageIdent_sfr!=$sessionMessageIdent_sfr){//if its different:          
				//save the session var:
			$_SESSION['messageIdent_sfr'] = $messageIdent_sfr;
   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//// inser SFR details in sfr bin table
		$res_sfrbin = mysqli_query($link1,"INSERT INTO sfr_bin set location_code='".$_SESSION['asc_code']."', to_location='".$_POST['send_for']."', job_no='".$jobno."', imei='".$job_details['imei']."', model_id='".$job_details['model_id']."', partcode='".$job_details['partcode']."', qty='1', entry_date='".$today."', status='4'");
		//// check if query is not executed
		if (!$res_sfrbin) {
			 $flag = false;
			 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
		}
		///// entry in call/job  history
		$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],"Job Esclated","Send For Repair",$eng_name,$job_details['warranty_status'],$_POST['rep_remark'],$_POST['travel'],"Y",$ip,$link1,$flag);
				/////////////   update by priya on 14 august  /////////////////////////////////////////
		
		$repairdetail = mysqli_query($link1,"INSERT INTO repair_detail set location_code='".$_SESSION['asc_code']."', repair_location='".$_SESSION['asc_code']."', job_no='".$jobno."', model_id='".$job_details['model_id']."', partcode='".$job_details['partcode']."',rep_lvl = '1.00' ,status='4', repair_code = '".$_POST['repair_code_sfr']."' , fault_code ='".$_POST['fault_code_sfr']."',eng_id='".$eng_name."' ");

		//// check if query is not executed

		if (!$repairdetail) {

			 $flag = false;

			 $error_msg = "Error details3: " . mysqli_error($link1) . ".";

		}
		//////////////////////////////////////////////image Upload//////////////////////////////////////////
			$folder1="handset_image";
	if(($_FILES['handset_img']["name"]!='') && ($_FILES["handset_img"]["size"] < 2000000)){
		 $file_name =$_FILES['handset_img']['name'];
		 $file_tmp =$_FILES['handset_img']['tmp_name'];
		  $file_path="../".$folder1."/".time()."INV".$file_name;
		 $img_upld1 = move_uploaded_file($file_tmp,$file_path);
		 
		  $file_name2 =$_FILES['handset_img2']['name'];
		 $file_tmp2 =$_FILES['handset_img2']['tmp_name'];
		  $file_path2="../".$folder1."/".time()."INV".$file_name2;
		 $img_upld12 = move_uploaded_file($file_tmp2,$file_path2);
		 
		  $file_name3=$_FILES['handset_img3']['name'];
		 $file_tmp3 =$_FILES['handset_img3']['tmp_name'];
		  $file_path3="../".$folder1."/".time()."INV".$file_name3;
		 $img_upld13 = move_uploaded_file($file_tmp3,$file_path3);
		 
		 
		  $file_name4=$_FILES['handset_img4']['name'];
		 $file_tmp4 =$_FILES['handset_img4']['tmp_name'];
		  $file_path4="../".$folder1."/".time()."INV".$file_name4;
		 $img_upld14= move_uploaded_file($file_tmp4,$file_path4);
		 
		  $file_name5=$_FILES['handset_img5']['name'];
		 $file_tmp5 =$_FILES['handset_img5']['tmp_name'];
		  $file_path5="../".$folder1."/".time()."INV".$file_name5;
		 $img_upld15= move_uploaded_file($file_tmp5,$file_path5);
		 
		 if($img_upld1 != "" ||  $img_upld12!="" ||  $img_upld13!="" ||  $img_upld14!="" ||  $img_upld15!="" ){
		//echo "INSERT INTO image_upload_details set job_no ='".$jobno."', activity='JOB CREATE',img_url='".$file_path."', upload_date='".$today."',location_code='".$_SESSION['asc_code']."'";
		 	$result = mysqli_query($link1,"INSERT INTO image_upload_details set job_no ='".$jobno."', activity='Send For Central Repair',img_url='".$file_path."',img_url1='".$file_path2."',img_url2='".$file_path3."' ,img_url3='".$file_path4."',img_url4='".$file_path5."', upload_date='".$today."',location_code='".$_SESSION['asc_code']."'");
	  		//// check if query is not executed
		 	if (!$result) {
				$flag = false;
				$error_msg = "Image Upload Problem: " . mysqli_error($link1) . ".";
			}
		 }else{
		 	$flag = false;
			$error_msg = "Invoice Image can not upload on server due to size or image type issue " . mysqli_error($link1) . ".";
		 }
	}
		
		
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB REPAIR at ".$_SESSION['asc_code'],"SFR",$ip,$link1,$flag);
		}
		else{
			$flag = false;
			$error_msg = "Refresh or Re-Submittion is not Allowed: " . mysqli_error($link1) . ".";
		}
	}
	########## End SFR CASE
	
	### Start PNA CASE ########## if repair status select as PNA
	else if($_POST['jobstatus'] == "3"){
///////////////  update by priya on 19 july to block multiple job creation ////////////////////////////////////////////////////////
	$messageIdent_pna = md5($_SESSION['asc_code'] . $jobno . $_POST['jobstatus']);
		//and check it against the stored value:
		$sessionMessageIdent_pna = isset($_SESSION['messageIdent_pna'])?$_SESSION['messageIdent_pna']:'';
		if($messageIdent_pna!=$sessionMessageIdent_pna){//if its different:          
				//save the session var:
				$_SESSION['messageIdent_pna'] = $messageIdent_pna;
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		///// Insert in item data by picking each data row one by one
		$pna_partarr = $_POST['pending_part'];
		foreach($pna_partarr as $k => $val){ 
			
			//// insert PNA details in auto part requset table
			$expld_pnapart = explode("~",$pna_partarr[$k]);
			if($expld_pnapart[0]!=""){
		
			$res_autopartreq = mysqli_query($link1,"INSERT INTO auto_part_request set location_code='".$_SESSION['asc_code']."', to_location='', job_no='".$jobno."', product_id='".$job_details['product_id']."', brand_id='".$job_details['brand_id']."', model_id='".$job_details['model_id']."', partcode='".$expld_pnapart[0]."', part_category='".$expld_pnapart[1]."' , qty='1', status='3', request_date='".$today."'");
			//// check if query is not executed
			if (!$res_autopartreq) {
				 $flag = false;
				 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
			}
			
			$res_autopartreqeng = mysqli_query($link1,"INSERT INTO part_demand set location_code='".$_SESSION['asc_code']."', to_location='', job_no='".$jobno."', product_id='".$job_details['product_id']."', brand_id='".$job_details['brand_id']."', model_id='".$job_details['model_id']."', partcode='".$expld_pnapart[0]."', part_category='".$expld_pnapart[1]."' , qty='1', status='1', request_date='".$today."',eng_id='".$eng_name."'");
			//// check if query is not executed
			if (!$res_autopartreqeng) {
				 $flag = false;
				 $error_msg = "Error details2eng: " . mysqli_error($link1) . ".";
			}
			} // check partcode not blank
		}/////end foreach loop
		///// entry in call/job  history

		$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],"Job PNA","Part Not Available",$eng_name,$job_details['warranty_status'],$_POST['rep_remark'],$_POST['travel'],"Y",$ip,$link1,$flag);
		////// insert in activity table////
		
		
				//////////////////////////////////////////////image Upload//////////////////////////////////////////
			$folder1="handset_image";
	if(($_FILES['handset_img']["name"]!='') && ($_FILES["handset_img"]["size"] < 2000000)){
		 $file_name =$_FILES['handset_img']['name'];
		 $file_tmp =$_FILES['handset_img']['tmp_name'];
		  $file_path="../".$folder1."/".time()."INV".$file_name;
		 $img_upld1 = move_uploaded_file($file_tmp,$file_path);
		 
		  $file_name2 =$_FILES['handset_img2']['name'];
		 $file_tmp2 =$_FILES['handset_img2']['tmp_name'];
		  $file_path2="../".$folder1."/".time()."INV".$file_name2;
		 $img_upld12 = move_uploaded_file($file_tmp2,$file_path2);
		 
		  $file_name3=$_FILES['handset_img3']['name'];
		 $file_tmp3 =$_FILES['handset_img3']['tmp_name'];
		  $file_path3="../".$folder1."/".time()."INV".$file_name3;
		 $img_upld13 = move_uploaded_file($file_tmp3,$file_path3);
		 
		 
		  $file_name4=$_FILES['handset_img4']['name'];
		 $file_tmp4 =$_FILES['handset_img4']['tmp_name'];
		  $file_path4="../".$folder1."/".time()."INV".$file_name4;
		 $img_upld14= move_uploaded_file($file_tmp4,$file_path4);
		 
		  $file_name5=$_FILES['handset_img5']['name'];
		 $file_tmp5 =$_FILES['handset_img5']['tmp_name'];
		  $file_path5="../".$folder1."/".time()."INV".$file_name5;
		 $img_upld15= move_uploaded_file($file_tmp5,$file_path5);
		 
		 if($img_upld1 != "" ||  $img_upld12!="" ||  $img_upld13!="" ||  $img_upld14!="" ||  $img_upld15!="" ){
		//echo "INSERT INTO image_upload_details set job_no ='".$jobno."', activity='JOB CREATE',img_url='".$file_path."', upload_date='".$today."',location_code='".$_SESSION['asc_code']."'";
		 	$result = mysqli_query($link1,"INSERT INTO image_upload_details set job_no ='".$jobno."', activity='Part Not Available',img_url='".$file_path."',img_url1='".$file_path2."',img_url2='".$file_path3."' ,img_url3='".$file_path4."',img_url4='".$file_path5."', upload_date='".$today."',location_code='".$_SESSION['asc_code']."'");
	  		//// check if query is not executed
		 	if (!$result) {
				$flag = false;
				$error_msg = "Image Upload Problem: " . mysqli_error($link1) . ".";
			}
		 }else{
		 	$flag = false;
			$error_msg = "Invoice Image can not upload on server due to size or image type issue " . mysqli_error($link1) . ".";
		 }
	}
		$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB REPAIR at ".$_SESSION['asc_code'],"PNA",$ip,$link1,$flag);
		}
		else{
			$flag = false;
			$error_msg = "Refresh or Re-Submittion is not Allowed: " . mysqli_error($link1) . ".";
		}
	}
	
	
	
	
		### Start Part Demand CASE ########## if repair status select as PNA
	else if($_POST['jobstatus'] == "54"){
///////////////  update by priya on 19 july to block multiple job creation ////////////////////////////////////////////////////////
	$messageIdent_pna = md5($_SESSION['asc_code'] . $jobno . $_POST['jobstatus']);
		//and check it against the stored value:
		$sessionMessageIdent_pna = isset($_SESSION['messageIdent_pna'])?$_SESSION['messageIdent_pna']:'';
		if($messageIdent_pna!=$sessionMessageIdent_pna){//if its different:          
				//save the session var:
				$_SESSION['messageIdent_pna'] = $messageIdent_pna;
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		///// Insert in item data by picking each data row one by one
		$pna_partarr = $_POST['dmd_part'];
		foreach($pna_partarr as $k => $val){ 
			
			//// insert PNA details in auto part requset table
			$expld_pnapart = explode("~",$pna_partarr[$k]);
			if($expld_pnapart[0]!=""){

			$res_autopartreq = mysqli_query($link1,"INSERT INTO part_demand set location_code='".$_SESSION['asc_code']."', to_location='', job_no='".$jobno."', product_id='".$job_details['product_id']."', brand_id='".$job_details['brand_id']."', model_id='".$job_details['model_id']."', partcode='".$expld_pnapart[0]."', part_category='".$expld_pnapart[1]."' , qty='1', status='1', request_date='".$today."',eng_id='".$eng_name."'");
			//// check if query is not executed
			if (!$res_autopartreq) {
				 $flag = false;
				 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
			}
			} // check partcode not blank
		}/////end foreach loop
		///// entry in call/job  history

		$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],"Job Part Demand","Part Demand",$eng_name,$job_details['warranty_status'],$_POST['rep_remark'],$_POST['travel'],"Y",$ip,$link1,$flag);
		////// insert in activity table////
		
		
				//////////////////////////////////////////////image Upload//////////////////////////////////////////
			$folder1="handset_image";
	if(($_FILES['handset_img']["name"]!='') && ($_FILES["handset_img"]["size"] < 2000000)){
		 $file_name =$_FILES['handset_img']['name'];
		 $file_tmp =$_FILES['handset_img']['tmp_name'];
		  $file_path="../".$folder1."/".time()."INV".$file_name;
		 $img_upld1 = move_uploaded_file($file_tmp,$file_path);
		 
		 if($img_upld1 != ""){
		//echo "INSERT INTO image_upload_details set job_no ='".$jobno."', activity='JOB CREATE',img_url='".$file_path."', upload_date='".$today."',location_code='".$_SESSION['asc_code']."'";
		 	$result = mysqli_query($link1,"INSERT INTO image_upload_details set job_no ='".$jobno."', activity='Part Demand',img_url='".$file_path."',img_url1='".$file_path2."',img_url2='".$file_path3."' ,img_url3='".$file_path4."',img_url4='".$file_path5."', upload_date='".$today."',location_code='".$_SESSION['asc_code']."'");
	  		//// check if query is not executed
		 	if (!$result) {
				$flag = false;
				$error_msg = "Image Upload Problem: " . mysqli_error($link1) . ".";
			}
		 }else{
		 	$flag = false;
			$error_msg = "Invoice Image can not upload on server due to size or image type issue " . mysqli_error($link1) . ".";
		 }
	}
		$flag = dailyActivity($_SESSION['userid'],$jobno,"Part Demand at ".$_SESSION['asc_code'],"PNA",$ip,$link1,$flag);
		}
		else{
			$flag = false;
			$error_msg = "Refresh or Re-Submittion is not Allowed: " . mysqli_error($link1) . ".";
		}
	}
	
	
	
	//////////////////////////////////Cancel Status//////////////////////////////////
	
	
		else if($_POST['jobstatus'] == "12"){
	///////////////  update by priya on 19 july to block multiple job creation ////////////////////////////////////////////////////////
	$messageIdent_pna = md5($_SESSION['asc_code'] . $jobno . $_POST['jobstatus']);
		//and check it against the stored value:
		$sessionMessageIdent_pna = isset($_SESSION['messageIdent_pna'])?$_SESSION['messageIdent_pna']:'';
		if($messageIdent_pna!=$sessionMessageIdent_pna){//if its different:          
				//save the session var:
				$_SESSION['messageIdent_pna'] = $messageIdent_pna;
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		///// Insert in item data by picking each data row one by one
		$upd_str = " ,close_date='".$today."',close_time='".$currtime."'";	
		///// entry in call/job  history

		$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],"Job Status Change","Cancel",$eng_name,$job_details['warranty_status'],$_POST['rep_remark'],$_POST['travel'],"Y",$ip,$link1,$flag);
		////// insert in activity table////

		$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB Cancel at ".$_SESSION['asc_code'],"Job cancel",$ip,$link1,$flag);
		}
		else{
			$flag = false;
			$error_msg = "Refresh or Re-Submittion is not Allowed: " . mysqli_error($link1) . ".";
		}
	}
	///////////////////////////////////////////////////////RWR ///////////////////////////
			else if($_POST['jobstatus'] == "11"){
	///////////////  update by priya on 19 july to block multiple job creation ////////////////////////////////////////////////////////
	$messageIdent_pna = md5($_SESSION['asc_code'] . $jobno . $_POST['jobstatus']);
		//and check it against the stored value:
		$sessionMessageIdent_pna = isset($_SESSION['messageIdent_pna'])?$_SESSION['messageIdent_pna']:'';
		if($messageIdent_pna!=$sessionMessageIdent_pna){//if its different:          
				//save the session var:
				$_SESSION['messageIdent_pna'] = $messageIdent_pna;
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		///// Insert in item data by picking each data row one by one
	
		///// entry in call/job  history
	
			$end_date =$datetime;
	$start_date= $job_details['open_date']." ".$job_details['open_time'];
		
	/////////// holidays count between close_tat //////////
	$tot_holiday = mysqli_fetch_array(mysqli_query($link1, "select count(sno) from holidays where date between '".$job_details['open_date']."' and '".$today."' "));
	
	$clstat = daysDifference($today,$job_details['open_date']);	
	if($clstat>=$tot_holiday[0]){
		$close_tat = ($clstat - $tot_holiday[0]);
	}else{
		$close_tat = $clstat;
	}
	
	  
   $seconds = strtotime($end_date) - strtotime($start_date);
   
   $gethrs=getHoursMinSec($seconds);
		
		
		$upd_str = " ,close_date='".$today."',close_time='".$currtime."'";	
		$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],"CWR","Closed Without Repair",$eng_name,$job_details['warranty_status'],$_POST['rep_remark'],$_POST['travel'],"Y",$ip,$link1,$flag);
		////// insert in activity table////
		
		$sql_rep=mysqli_query($link1,"select sum(travel_km) as a,updated_by from call_history where job_no ='".$jobno."' and travel='Y' group by updated_by ");

while($row_max=mysqli_fetch_array($sql_rep)){



$max_rep=mysqli_query($link1,"insert into job_claim_appr set job_no='".$jobno."',brand_id='".$job_details['brand_id']."',action_by='".$_SESSION['asc_code']."',rep_lvl ='".$row_level['b']."',model='".$job_details['model_id']."',product_id='".$job_details['product_id']."',entity_type='".$job_details['entity_type']."',eng_name='".$row_max['updated_by']."',hand_date='".$today."',travel_km='".$row_max[a]."',area_type='".$job_details['area_type']."',claim_tat='".$close_tat."',hrs_tat='".$gethrs."',job_count='".$job_details['job_count']."' ");
			//// check if query is not executed
		if (!$max_rep) {
			 $flag = false;
			 $error_msg = "Error details3: " . mysqli_error($link1) . ".";
		}	
}

		$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB CWR at ".$_SESSION['asc_code'],"Job cancel",$ip,$link1,$flag);
		}
		else{
			$flag = false;
			$error_msg = "Refresh or Re-Submittion is not Allowed: " . mysqli_error($link1) . ".";
		}
	}
	
	
	
	
		### Start WIP CASE ########## if repair status select as WIP
	else if($_POST['jobstatus'] == "7"){
	///////////////  update by priya on 19 july to block multiple job creation ////////////////////////////////////////////////////////
	$messageIdent_pna = md5($_SESSION['asc_code'] . $jobno . $_POST['jobstatus']);
		//and check it against the stored value:
		$sessionMessageIdent_pna = isset($_SESSION['messageIdent_pna'])?$_SESSION['messageIdent_pna']:'';
		if($messageIdent_pna!=$sessionMessageIdent_pna){//if its different:          
				//save the session var:
				$_SESSION['messageIdent_pna'] = $messageIdent_pna;
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		///// Insert in item data by picking each data row one by one
	
		///// entry in call/job  history

		$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],"Job Pending",$_POST['pending_reason'],$eng_name,$job_details['warranty_status'],$_POST['rep_remark'],$_POST['travel'],"Y",$ip,$link1,$flag);
		////// insert in activity table////
				//////////////////////////////////////////////image Upload//////////////////////////////////////////
			$folder1="handset_image";
	if(($_FILES['handset_img']["name"]!='') && ($_FILES["handset_img"]["size"] < 2000000)){
		 $file_name =$_FILES['handset_img']['name'];
		 $file_tmp =$_FILES['handset_img']['tmp_name'];
		  $file_path="../".$folder1."/".time()."INV".$file_name;
		 $img_upld1 = move_uploaded_file($file_tmp,$file_path);
	  $file_name2 =$_FILES['handset_img2']['name'];
		 $file_tmp2 =$_FILES['handset_img2']['tmp_name'];
		  $file_path2="../".$folder1."/".time()."INV".$file_name2;
		 $img_upld12 = move_uploaded_file($file_tmp2,$file_path2);
		 
		  $file_name3=$_FILES['handset_img3']['name'];
		 $file_tmp3 =$_FILES['handset_img3']['tmp_name'];
		  $file_path3="../".$folder1."/".time()."INV".$file_name3;
		 $img_upld13 = move_uploaded_file($file_tmp3,$file_path3);
		 
		 
		  $file_name4=$_FILES['handset_img4']['name'];
		 $file_tmp4 =$_FILES['handset_img4']['tmp_name'];
		  $file_path4="../".$folder1."/".time()."INV".$file_name4;
		 $img_upld14= move_uploaded_file($file_tmp4,$file_path4);
		 
		  $file_name5=$_FILES['handset_img5']['name'];
		 $file_tmp5 =$_FILES['handset_img5']['tmp_name'];
		  $file_path5="../".$folder1."/".time()."INV".$file_name5;
		 $img_upld15= move_uploaded_file($file_tmp5,$file_path5);
		 
		 if($img_upld1 != "" ||  $img_upld12!="" ||  $img_upld13!="" ||  $img_upld14!="" ||  $img_upld15!="" ){
		//echo "INSERT INTO image_upload_details set job_no ='".$jobno."', activity='JOB CREATE',img_url='".$file_path."', upload_date='".$today."',location_code='".$_SESSION['asc_code']."'";
		 	$result = mysqli_query($link1,"INSERT INTO image_upload_details set job_no ='".$jobno."', activity='Job Pending',img_url='".$file_path."',img_url1='".$file_path2."',img_url2='".$file_path3."' ,img_url3='".$file_path4."',img_url4='".$file_path5."', upload_date='".$today."',location_code='".$_SESSION['asc_code']."'");
	  		//// check if query is not executed
		 	if (!$result) {
				$flag = false;
				$error_msg = "Image Upload Problem: " . mysqli_error($link1) . ".";
			}
		 }else{
		 	$flag = false;
			$error_msg = "Invoice Image can not upload on server due to size or image type issue " . mysqli_error($link1) . ".";
		 }
	}
		$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB REPAIR at ".$_SESSION['asc_code'],"Job WIP",$ip,$link1,$flag);
		}
		else{
			$flag = false;
			$error_msg = "Refresh or Re-Submittion is not Allowed: " . mysqli_error($link1) . ".";
		}
	}
	########## End WIP CASE
			### Start Request For Approval  CASE ########## if repair status select as Request For Approval
	else if($_POST['jobstatus'] == "50"){
	///////////////  update by priya on 19 july to block multiple job creation ////////////////////////////////////////////////////////
	$messageIdent_pna = md5($_SESSION['asc_code'] . $jobno . $_POST['jobstatus']);
		//and check it against the stored value:
		$sessionMessageIdent_pna = isset($_SESSION['messageIdent_pna'])?$_SESSION['messageIdent_pna']:'';
		if($messageIdent_pna!=$sessionMessageIdent_pna){//if its different:          
				//save the session var:
				$_SESSION['messageIdent_pna'] = $messageIdent_pna;
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		///// Insert in item data by picking each data row one by one
	//	echo "UPDATE jobsheet_data set status='".$_POST['jobstatus']."', sub_status='".$_POST['jobstatus']."', app_reason='".$_POST['app_req']."' where job_no='".$jobno."'";
		$res_jobsheet = mysqli_query($link1,"UPDATE jobsheet_data set status='".$_POST['jobstatus']."', sub_status='".$_POST['jobstatus']."', app_reason='".$_POST['app_req']."' where job_no='".$jobno."'");
	//// check if query is not executed
	if (!$res_jobsheet) {
		 $flag = false;
		 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
	}

		///// entry in call/job  history
		$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],"Pending For Approval",$_POST['app_req'],$eng_name,$job_details['warranty_status'],$_POST['rep_remark'],$_POST['travel'],"Y",$ip,$link1,$flag);
		////// insert in activity table////
		
						//////////////////////////////////////////////image Upload//////////////////////////////////////////
			$folder1="handset_image";
	if(($_FILES['handset_img']["name"]!='') && ($_FILES["handset_img"]["size"] < 2000000)){
		 $file_name =$_FILES['handset_img']['name'];
		 $file_tmp =$_FILES['handset_img']['tmp_name'];
		  $file_path="../".$folder1."/".time()."INV".$file_name;
		 $img_upld1 = move_uploaded_file($file_tmp,$file_path);
	  $file_name2 =$_FILES['handset_img2']['name'];
		 $file_tmp2 =$_FILES['handset_img2']['tmp_name'];
		  $file_path2="../".$folder1."/".time()."INV".$file_name2;
		 $img_upld12 = move_uploaded_file($file_tmp2,$file_path2);
		 
		  $file_name3=$_FILES['handset_img3']['name'];
		 $file_tmp3 =$_FILES['handset_img3']['tmp_name'];
		  $file_path3="../".$folder1."/".time()."INV".$file_name3;
		 $img_upld13 = move_uploaded_file($file_tmp3,$file_path3);
		 
		 
		  $file_name4=$_FILES['handset_img4']['name'];
		 $file_tmp4 =$_FILES['handset_img4']['tmp_name'];
		  $file_path4="../".$folder1."/".time()."INV".$file_name4;
		 $img_upld14= move_uploaded_file($file_tmp4,$file_path4);
		 
		  $file_name5=$_FILES['handset_img5']['name'];
		 $file_tmp5 =$_FILES['handset_img5']['tmp_name'];
		  $file_path5="../".$folder1."/".time()."INV".$file_name5;
		 $img_upld15= move_uploaded_file($file_tmp5,$file_path5);
		 
		 if($img_upld1 != "" ||  $img_upld12!="" ||  $img_upld13!="" ||  $img_upld14!="" ||  $img_upld15!="" ){
		//echo "INSERT INTO image_upload_details set job_no ='".$jobno."', activity='JOB CREATE',img_url='".$file_path."', upload_date='".$today."',location_code='".$_SESSION['asc_code']."'";
		 	$result = mysqli_query($link1,"INSERT INTO image_upload_details set job_no ='".$jobno."', activity='Pending For Approval',img_url='".$file_path."',img_url1='".$file_path2."',img_url2='".$file_path3."' ,img_url3='".$file_path4."',img_url4='".$file_path5."', upload_date='".$today."',location_code='".$_SESSION['asc_code']."'");
	  		//// check if query is not executed
		 	if (!$result) {
				$flag = false;
				$error_msg = "Image Upload Problem: " . mysqli_error($link1) . ".";
			}
		 }else{
		 	$flag = false;
			$error_msg = "Invoice Image can not upload on server due to size or image type issue " . mysqli_error($link1) . ".";
		 }
	}
		$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB Approval at ".$_SESSION['asc_code'],"Pending For Approval",$ip,$link1,$flag);
		}
		else{
			$flag = false;
			$error_msg = "Refresh or Re-Submittion is not Allowed: " . mysqli_error($link1) . ".";
		}
	}
	########## End Request For Approval CASE
	
	### Start EP CASE ########## if repair status select as EP
	else if($_POST['jobstatus'] == "5"){
		////pick max count of a location
		$res_maxcount = mysqli_query($link1,"SELECT COUNT(eid) as maxcnt FROM estimate_master where location_code='".$_SESSION['asc_code']."'");
		$row_maxcount = mysqli_fetch_assoc($res_maxcount);
		//// next estimate no.
		$next_no = $row_maxcount['maxcnt']+1;
		$estimate_no = $jobno."E".$next_no;
		///// get addressess for the parties
		$location_addrs = getAnyDetails($_SESSION['asc_code'],"locationaddress","location_code","location_master",$link1);
		////// insert in estimate master
		$res_estimaster = mysqli_query($link1,"INSERT INTO estimate_master set estimate_no='".$estimate_no."', estimate_date='".$today."', location_code='".$_SESSION['asc_code']."', from_address='".$location_addrs."', to_address='".$job_details['address']."', estimate_amount='".$_POST['ep_new_es']."' , entry_by='".$_SESSION['userid']."', entry_ip='".$_SERVER['REMOTE_ADDR']."', status='5',job_no='".$jobno."'");
		//// check if query is not executed
		if (!$res_estimaster) {
			 $flag = false;
			 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
		}
		///// Insert in item data by picking each data row one by one
		
		/////initialize post array variables
		$ep_partarr = $_POST['esti_part'];
		$ep_hsncode = $_POST['ep_hsn_code'];
		$ep_basicamt = $_POST['ep_cost'];
		$ep_taxper = $_POST['ep_taxper'];
		$ep_taxamt = $_POST['ep_taxamt'];
		$ep_totamt = $_POST['ep_totalamt'];
		foreach($ep_partarr as $k => $val){
			///// get addressess for the parties
			$partdetail = getAnyDetails($ep_partarr[$k],"part_name","partcode","partcode_master",$link1); 
			//// insert in estimate data
			$res_estidata = mysqli_query($link1,"INSERT INTO estimate_items set estimate_no='".$estimate_no."', partcode='".$ep_partarr[$k]."', hsn_code='".$ep_hsncode[$k]."', part_name='".$partdetail."', basic_amount='".$ep_basicamt[$k]."', tax_per='".$ep_taxper[$k]."', tax_amt='".$ep_taxamt[$k]."' , tax_name='', total_amount='".$ep_totamt[$k]."',job_no='".$jobno."'");
			//// check if query is not executed
			if (!$res_estidata) {
				 $flag = false;
				 $error_msg = "Error details3: " . mysqli_error($link1) . ".";
			}
		}/////end foreach loop
		//// check if any service charge is applicable then we have to insert one more entry in estimate items
		$res_servcharge = mysqli_query($link1,"INSERT INTO estimate_items set estimate_no='".$estimate_no."', partcode='SERV001', hsn_code='".$_POST['ser_tax_hsn']."', part_name='Service Charge', basic_amount='".$_POST['ser_charge']."', tax_per='".$_POST['ser_tax_per']."', tax_amt='".$_POST['ser_tax_amt']."' , tax_name='', total_amount='".$_POST['total_ser_tax_amt']."',job_no='".$jobno."'");
		//// check if query is not executed
		if (!$res_servcharge) {
			 $flag = false;
			 $error_msg = "Error details4: " . mysqli_error($link1) . ".";
		}
		

		///// entry in call/job  history
		$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],"Job EP","Parts Estimation",$eng_name,$job_details['warranty_status'],$_POST['rep_remark'],$_POST['travel'],"Y",$ip,$link1,$flag);
		////// insert in activity table////
		
		
								//////////////////////////////////////////////image Upload//////////////////////////////////////////
			$folder1="handset_image";
	if(($_FILES['handset_img']["name"]!='') && ($_FILES["handset_img"]["size"] < 2000000)){
		 $file_name =$_FILES['handset_img']['name'];
		 $file_tmp =$_FILES['handset_img']['tmp_name'];
		  $file_path="../".$folder1."/".time()."INV".$file_name;
		 $img_upld1 = move_uploaded_file($file_tmp,$file_path);
		 
		  $file_name2 =$_FILES['handset_img2']['name'];
		 $file_tmp2 =$_FILES['handset_img2']['tmp_name'];
		  $file_path2="../".$folder1."/".time()."INV".$file_name2;
		 $img_upld12 = move_uploaded_file($file_tmp2,$file_path2);
		 
		  $file_name3=$_FILES['handset_img3']['name'];
		 $file_tmp3 =$_FILES['handset_img3']['tmp_name'];
		  $file_path3="../".$folder1."/".time()."INV".$file_name3;
		 $img_upld13 = move_uploaded_file($file_tmp3,$file_path3);
		 
		 
		  $file_name4=$_FILES['handset_img4']['name'];
		 $file_tmp4 =$_FILES['handset_img4']['tmp_name'];
		  $file_path4="../".$folder1."/".time()."INV".$file_name4;
		 $img_upld14= move_uploaded_file($file_tmp4,$file_path4);
		 
		  $file_name5=$_FILES['handset_img5']['name'];
		 $file_tmp5 =$_FILES['handset_img5']['tmp_name'];
		  $file_path5="../".$folder1."/".time()."INV".$file_name5;
		 $img_upld15= move_uploaded_file($file_tmp5,$file_path5);
		 
		 if($img_upld1 != "" ||  $img_upld12!="" ||  $img_upld13!="" ||  $img_upld14!="" ||  $img_upld15!="" ){
		//echo "INSERT INTO image_upload_details set job_no ='".$jobno."', activity='JOB CREATE',img_url='".$file_path."', upload_date='".$today."',location_code='".$_SESSION['asc_code']."'";
		 	$result = mysqli_query($link1,"INSERT INTO image_upload_details set job_no ='".$jobno."', activity='Parts Estimation',img_url='".$file_path."',img_url1='".$file_path2."',img_url2='".$file_path3."' ,img_url3='".$file_path4."',img_url4='".$file_path5."', upload_date='".$today."',location_code='".$_SESSION['asc_code']."'");
	  		//// check if query is not executed
		 	if (!$result) {
				$flag = false;
				$error_msg = "Image Upload Problem: " . mysqli_error($link1) . ".";
			}
		 }else{
		 	$flag = false;
			$error_msg = "Invoice Image can not upload on server due to size or image type issue " . mysqli_error($link1) . ".";
		 }
	}
		$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB REPAIR at ".$_SESSION['asc_code'],"EP",$ip,$link1,$flag);
	}
	########## End EP CASE
	
	/**********************************************************************************************************
	### Start Repair Done CASE ########## if Repair Done at field select as Repair Done
	else if($_POST['jobstatus'] == "58"){
	$messageIdent_con = md5($_SESSION['asc_code'] . $jobno . $_POST['jobstatus']);
			//and check it against the stored value:
			$sessionMessageIdent_con = isset($_SESSION['messageIdent_con'])?$_SESSION['messageIdent_con']:'';
			if($messageIdent_con!=$sessionMessageIdent_con){//if its different:          
				//save the session var:
				$_SESSION['messageIdent_con'] = $messageIdent_con;
		/////initialize post array variables
		if($job_details["call_for"]!="Workshop" ){
			/////////// holidays count between close_tat //////////
			$tot_holiday = mysqli_fetch_array(mysqli_query($link1, "select count(sno) from holidays where date between '".$job_details['vistor_date']."' and '".$today."' "));
			
			$clstat = daysDifference($today,$job_details['vistor_date']);	
			if($clstat>=$tot_holiday[0]){
				$close_tat = ($clstat - $tot_holiday[0]);
			}else{
				$close_tat = $clstat;
			}
		}else{
			/////////// holidays count between close_tat //////////
			$tot_holiday = mysqli_fetch_array(mysqli_query($link1, "select count(sno) from holidays where date between '".$job_details['open_date']."' and '".$today."' "));
			
			$clstat = daysDifference($today,$job_details['open_date']);
			if($clstat>=$tot_holiday[0]){
				$close_tat = ($clstat - $tot_holiday[0]);
			}else{
				$close_tat = $clstat;
			}
		}
		$upd_str = " ,close_date='".$today."',close_time='".$currtime."'";	

	$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],"Job Repair in Field",$_POST['close_reason'],$eng_name,$job_details['warranty_status'],$_POST['rep_remark'],$_POST['travel'],"Y",$ip,$link1,$flag);


$sql_rep=mysqli_query($link1,"select sum(travel_km) as a,updated_by from call_history where job_no ='".$jobno."' and travel='Y'  group by updated_by ");

while($row_max=mysqli_fetch_array($sql_rep)){



$max_rep=mysqli_query($link1,"insert into job_claim_appr set job_no='".$jobno."',brand_id='".$job_details['brand_id']."',action_by='".$_SESSION['asc_code']."',rep_lvl ='".$row_level['b']."',model='".$job_details['model_id']."',product_id='".$job_details['product_id']."',entity_type='".$job_details['entity_type']."',eng_name='".$row_max['updated_by']."',hand_date='".$today."',travel_km='".$row_max[a]."',area_type='".$job_details['area_type']."',claim_tat='".$close_tat."'");
			//// check if query is not executed
		if (!$max_rep) {
			 $flag = false;
			 $error_msg = "Error details3: " . mysqli_error($link1) . ".";
		}	
}
			////////image Upload//////////////////////////////////////////
			$folder1="handset_image";
	if(($_FILES['handset_img']["name"]!='') && ($_FILES["handset_img"]["size"] < 2000000)){
		 $file_name =$_FILES['handset_img']['name'];
		 $file_tmp =$_FILES['handset_img']['tmp_name'];
		  $file_path="../".$folder1."/".time()."INV".$file_name;
		 $img_upld1 = move_uploaded_file($file_tmp,$file_path);
		 
	  $file_name2 =$_FILES['handset_img2']['name'];
		 $file_tmp2 =$_FILES['handset_img2']['tmp_name'];
		  $file_path2="../".$folder1."/".time()."INV".$file_name2;
		 $img_upld12 = move_uploaded_file($file_tmp2,$file_path2);
		 
		  $file_name3=$_FILES['handset_img3']['name'];
		 $file_tmp3 =$_FILES['handset_img3']['tmp_name'];
		  $file_path3="../".$folder1."/".time()."INV".$file_name3;
		 $img_upld13 = move_uploaded_file($file_tmp3,$file_path3);
		 
		 
		  $file_name4=$_FILES['handset_img4']['name'];
		 $file_tmp4 =$_FILES['handset_img4']['tmp_name'];
		  $file_path4="../".$folder1."/".time()."INV".$file_name4;
		 $img_upld14= move_uploaded_file($file_tmp4,$file_path4);
		 
		  $file_name5=$_FILES['handset_img5']['name'];
		 $file_tmp5 =$_FILES['handset_img5']['tmp_name'];
		  $file_path5="../".$folder1."/".time()."INV".$file_name5;
		 $img_upld15= move_uploaded_file($file_tmp5,$file_path5);
		 
		 if($img_upld1 != "" ||  $img_upld12!="" ||  $img_upld13!="" ||  $img_upld14!="" ||  $img_upld15!="" ){
		 	$result = mysqli_query($link1,"INSERT INTO image_upload_details set job_no ='".$jobno."', activity='Job Repair',img_url='".$file_path."', img_url1='".$file_path2."',img_url2='".$file_path3."' ,img_url3='".$file_path4."',img_url4='".$file_path5."',upload_date='".$today."',location_code='".$_SESSION['asc_code']."'");
	  		//// check if query is not executed
		 	if (!$result) {
				$flag = false;
				$error_msg = "Image Upload Problem: " . mysqli_error($link1) . ".";
			}
		 }else{
		 	$flag = false;
			$error_msg = "Invoice Image can not upload on server due to size or image type issue " . mysqli_error($link1) . ".";
		 }
	}
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB REPAIR in Field at ".$_SESSION['asc_code'],"Repair Done",$ip,$link1,$flag);
		}
		else{
			$flag = false;
			$error_msg = "Refresh or Re-Submittion is not Allowed: " . mysqli_error($link1) . ".";
		}

	}
	************************************************************************************************************/
	
	### Start Gas Charging CASE ########## if repair status select as Gas charging
	else if($_POST['jobstatus'] == "58"){
		$messageIdent_con = md5($_SESSION['asc_code'] . $jobno . $_POST['jobstatus']);
		//and check it against the stored value:
		$sessionMessageIdent_con = isset($_SESSION['messageIdent_con'])?$_SESSION['messageIdent_con']:'';
		if($messageIdent_con!=$sessionMessageIdent_con){//if its different:          
		//save the session var:
		$_SESSION['messageIdent_con'] = $messageIdent_con;
		/////initialize post array variables
	
		$upd_str = " ,close_date='".$today."',close_time='".$currtime."'";
		
		$rd_con= $_POST['con_code'];
		$rd_sym = $_POST['sym_code'];
		$rd_sec = $_POST['sec_code'];
		$rd_faultarr = $_POST['fault_code'];
		$rd_repairarr = $_POST['repair_code'];
		$rd_repairlvlarr = $_POST['repair_level'];
		$rd_partarr = $_POST['part'];
		$rd_partpricearr = $_POST['part_price'];
		$swap_imei1 = $_POST['swap_imei1'];
		$swap_imei2= $_POST['swap_imei2'];
		$part_warranty = daysDifference($job_details['open_date'],$job_details['dop']);
		
		$rp_fg = 1;
		foreach($rd_faultarr as $k => $val){
			////// insert in repair details
			$partsplit = explode("^",$rd_partarr[$k]);
			if($job_details['customer_type']=="Dealer"){
				$dwp="dwp";
			}else{
				$dwp="wp";
			}
			
			if($partsplit[0]!=""){
				$rp_fg *= 0;
			}else{
				$rp_fg *= 1;
			}
			
			$wp_part =getAnyDetails($partsplit[0],$dwp,"partcode","partcode_master",$link1);
			if($rd_partarr[$k]){ 
				$part_replc = "Y";
				if($wp_part> $part_warranty ){
					$w_period="IN";
				}else{
					$w_period="OUT";
				}
			}else{ 
				$part_replc = "N";
			}
			
			//echo "INSERT INTO repair_detail set job_id='".$job_details['job_id']."', job_no ='".$jobno."', repair_location='".$_SESSION['asc_code']."', repair_type='', location_code='".$job_details['location_code']."', model_id='".$job_details['model_id']."',eng_id='".$eng_name."', bin_id='' , status='6', remark='".$_POST['rep_remark']."', fault_code='".$rd_faultarr[$k]."', rep_lvl='".$rd_repairlvlarr[$k]."', part_repl='".$part_replc."', repair_code='".$rd_repairarr[$k]."', partcode='".$partsplit[0]."', part_qty='1', part_cost='".$rd_partpricearr[$k]."',close_date='".$today."',replace_imei1='".$swap_imei1[$k]."',replace_imei2='".$swap_imei2[$k]."',warranty_status='".$w_period."'"."<br><br>";
			
			$res_reapirdata = mysqli_query($link1,"INSERT INTO repair_detail set job_id='".$job_details['job_id']."', job_no ='".$jobno."', repair_location='".$_SESSION['asc_code']."', repair_type='', location_code='".$job_details['location_code']."', model_id='".$job_details['model_id']."',eng_id='".$eng_name."', bin_id='' , status='6', remark='".$_POST['rep_remark']."', fault_code='".$rd_faultarr[$k]."', rep_lvl='".$rd_repairlvlarr[$k]."', part_repl='".$part_replc."', repair_code='".$rd_repairarr[$k]."', partcode='".$partsplit[0]."', part_qty='1', part_cost='".$rd_partpricearr[$k]."',close_date='".$today."',replace_imei1='".$swap_imei1[$k]."',replace_imei2='".$swap_imei2[$k]."',warranty_status='".$w_period."'");
			//// check if query is not executed
			if(!$res_reapirdata) {
				$flag = false;
				$error_msg = "Error details2: " . mysqli_error($link1) . ".";
			}
			///// extra field of jobsheet data which is to be update
			///// if part is consumed
			if($partsplit[0]){
				//// update inventory as user consume part
				$fifo_result = filo_details_asp($_SESSION['asc_code'],$partsplit[0],1,$link1);
				
				$splitted_fifo_data = explode("~",$fifo_result);
				$return_fifo = "UPDATE  fifo_list set fifi_ty =fifi_ty + 1 where  partcode='" .$partsplit[0]. "' and ref_sno='".$splitted_fifo_data[5]."'"; 
				
				//echo $return_fifo."<br><br>";
											
				$queryfifo_return = mysqli_query($link1, $return_fifo);
				//// check if query is not executed
				if(!$queryfifo_return) {
					$flag = false;
					$err_msg = "Fifo Return query Check Code10: ".mysqli_error($link1);
				}
				
				$bill_fifo = "UPDATE  billing_product_items set return_qty_fifo=return_qty_fifo+1 where  partcode='" .$partsplit[0]. "' and id='".$splitted_fifo_data[5]."'";

				//echo $bill_fifo."<br><br>";
																					
				$queryfifo_bill = mysqli_query($link1, $bill_fifo);
				if(!$queryfifo_bill) {
					$flag = false;
					$err_msg = "Fifo Return query Check Code11: ".mysqli_error($link1);
				}
				
				///// entry in stock ledger
				$flag = stockLedger($jobno,$today,$partsplit[0],$_SESSION['asc_code'],$job_details['customer_name'],"OUT","OK","JOB REPAIR","Repair Done","1",$rd_partpricearr[$k],$eng_name,$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);
				
				//echo "UPDATE client_inventory set mount_qty = mount_qty-'1' where location_code='".$_SESSION['asc_code']."' and partcode='".$partsplit[0]."' and mount_qty >0"."<br><br>";
				
				/*$res_invt = mysqli_query($link1, "UPDATE client_inventory set mount_qty = mount_qty-'1' where location_code='".$_SESSION['asc_code']."' and partcode='".$partsplit[0]."' and mount_qty >0");
				//// check if query is not executed
				if(!$res_invt) {
					$flag = false;
					$error_msg = "Error details3: " . mysqli_error($link1) . ".";
				}*/
				
				//echo "UPDATE user_inventory set okqty = okqty-'1' where location_code='".$_SESSION['asc_code']."' and partcode='".$partsplit[0]."' and   	locationuser_code ='".$eng_name."' and okqty >0"."<br><br>";
				
				$res_invt_user = mysqli_query($link1, "UPDATE user_inventory set okqty = okqty-'1' where location_code='".$_SESSION['asc_code']."' and partcode='".$partsplit[0]."' and   	locationuser_code ='".$eng_name."' and okqty >0");
				//// check if query is not executed
				if(!$res_invt_user) {
					$flag = false;
					$error_msg = "Error detailsuser: " . mysqli_error($link1) . ".";
				}
				
				/////////////////////////////////// asc part Request/////////////////////////For P2C
				if($w_period=='IN'){
					
					//echo "UPDATE user_inventory set faulty = faulty+'1' where location_code='".$_SESSION['asc_code']."' and partcode='".$partsplit[0]."' and 	locationuser_code ='".$eng_name."'"."<br><br>";
					
					$res_faulty_user = mysqli_query($link1, "UPDATE user_inventory set faulty = faulty+'1' where location_code='".$_SESSION['asc_code']."' and partcode='".$partsplit[0]."' and 	locationuser_code ='".$eng_name."'");
					//// check if query is not executed
					if(!$res_faulty_user) {
						$flag = false;
						$error_msg = "Error detailsuserfauty: " . mysqli_error($link1) . ".";
					}
					
					//echo "INSERT INTO part_to_credit set job_no ='".$jobno."', imei='".$job_details['imei']."',from_location='".$_SESSION['asc_code']."', partcode='".$partsplit[0]."', qty='1', price='".$splitted_fifo_data[2]."',cost='".$splitted_fifo_data[2]."',consumedate='".$today."',model_id='".$job_details['model_id']."',status ='4', product_id='".$job_details['product_id']."', brand_id='".$job_details['brand_id']."',type='EP2C',eng_id='".$eng_name."',eng_status='1',old_challan='".$splitted_fifo_data[0]."',ref_sno='".$splitted_fifo_data[5]."'"."<br><br>";
				
					$res_p2cdata = mysqli_query($link1, "INSERT INTO part_to_credit set job_no ='".$jobno."', imei='".$job_details['imei']."',from_location='".$_SESSION['asc_code']."', partcode='".$partsplit[0]."', qty='1', price='".$splitted_fifo_data[2]."',cost='".$splitted_fifo_data[2]."',consumedate='".$today."',model_id='".$job_details['model_id']."',status ='4', product_id='".$job_details['product_id']."', brand_id='".$job_details['brand_id']."',type='EP2C',eng_id='".$eng_name."',eng_status='1',old_challan='".$splitted_fifo_data[0]."',ref_sno='".$splitted_fifo_data[5]."'");
					
					if(!$res_p2cdata){
						$flag = false;
						$error_msg = "Error details21: " . mysqli_error($link1) . ".";
					}
				}
					
				if($swap_imei1[$k]!=''){
					$sql_ref = "INSERT INTO imei_details_asp(imei1,imei2,partcode , model_id,location_code,status , entry_date,stock_type,job_no) VALUES ('".$swap_imei1[$k]."','".$swap_imei2[$k]."','".$_POST['stock_type']."','".$job_details['model_id']."','".$_SESSION['asc_code']."','1','".$today."','okqty','".$jobno."')";
					
					//echo $sql_ref."<br><br>";
					
					$result_ref =	mysqli_query($link1,$sql_ref);
					if(!$result_ref) {
						$flag = false;
						$error_msg = "Error details reg imei: " . mysqli_error($link1) . ".";
					}
					
					//echo "INSERT INTO imei_history set imei1='".$swap_imei1[$k]."',imei2='".$swap_imei2[$k]."',partcode='".$_POST['stock_type']."',transaction_no='".$jobno."',remark='Stock In' ,location_code='".$_SESSION['asc_code']."' "."<br><br>";
					
					$result222 = mysqli_query($link1, "INSERT INTO imei_history set imei1='".$swap_imei1[$k]."',imei2='".$swap_imei2[$k]."',partcode='".$_POST['stock_type']."',transaction_no='".$jobno."',remark='Stock In' ,location_code='".$_SESSION['asc_code']."' "); 
					//// check if query is not executed
					if (!$result222) {
						$flag = false;
						$error_msg = "Error detailsimei history: " . mysqli_error($link1) . ".";
					}
				}
			}
		}/////end foreach loop
		///// entry in call/job  history
		
		$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],"Gas Charging Done",$_POST['close_reason'],$eng_name,$job_details['warranty_status'],$_POST['rep_remark'],$_POST['travel'],"Y",$ip,$link1,$flag);

		$end_date =$datetime;
		$start_date= $job_details['open_date']." ".$job_details['open_time'];
		
		/////////// holidays count between close_tat //////////
		$tot_holiday = mysqli_fetch_array(mysqli_query($link1, "select count(sno) from holidays where date between '".$job_details['open_date']."' and '".$today."' "));
		
		$clstat = daysDifference($today,$job_details['open_date']);
		if($clstat>=$tot_holiday[0]){
			$close_tat = ($clstat - $tot_holiday[0]);
		}else{
			$close_tat = $clstat;
		}	
		  
		$seconds = strtotime($end_date) - strtotime($start_date);
		$gethrs=getHoursMinSec($seconds);
   
		/////// replacement flag ///////////
		if($rp_fg == 1){
			$replace_flag = 'N';
		}else{
			$replace_flag = 'Y';
		}
		
		//echo "select sum(travel_km) as a,updated_by from call_history where job_no ='".$jobno."' and travel='Y' group by updated_by "."<br><br>";
		
		$sql_rep=mysqli_query($link1, "select sum(travel_km) as a,updated_by from call_history where job_no ='".$jobno."' and travel='Y' group by updated_by ");

		while($row_max=mysqli_fetch_array($sql_rep)){
			
			//echo "insert into job_claim_appr set job_no='".$jobno."',brand_id='".$job_details['brand_id']."',action_by='".$_SESSION['asc_code']."',rep_lvl ='".$row_level['b']."',model='".$job_details['model_id']."',product_id='".$job_details['product_id']."',entity_type='".$job_details['entity_type']."',eng_name='".$row_max['updated_by']."',hand_date='".$today."',travel_km='".$row_max[a]."',area_type='".$job_details['area_type']."',claim_tat='".$close_tat."',hrs_tat='".$gethrs."',job_count='".$job_details['job_count']."' , part_repl = '".$replace_flag."', status='".$_POST['jobstatus']."' "."<br><br>";
			
			$max_rep=mysqli_query($link1, "insert into job_claim_appr set job_no='".$jobno."',brand_id='".$job_details['brand_id']."',action_by='".$_SESSION['asc_code']."',rep_lvl ='".$row_level['b']."',model='".$job_details['model_id']."',product_id='".$job_details['product_id']."',entity_type='".$job_details['entity_type']."',eng_name='".$row_max['updated_by']."',hand_date='".$today."',travel_km='".$row_max[a]."',area_type='".$job_details['area_type']."',claim_tat='".$close_tat."',hrs_tat='".$gethrs."',job_count='".$job_details['job_count']."' , part_repl = '".$replace_flag."', status='".$_POST['jobstatus']."' ");
						
			//// check if query is not executed
			if(!$max_rep){
				$flag = false;
				$error_msg = "Error details3: " . mysqli_error($link1) . ".";
			}	
		}

		////////////////image Upload//////////////////////////////////////////
		$folder1="handset_image";
		if(($_FILES['handset_img']["name"]!='') && ($_FILES["handset_img"]["size"] < 2000000)){
			$file_name =$_FILES['handset_img']['name'];
			$file_tmp =$_FILES['handset_img']['tmp_name'];
			$file_path="../".$folder1."/".time()."INV".$file_name;
			$img_upld1 = move_uploaded_file($file_tmp,$file_path);
		 
			$file_name2 =$_FILES['handset_img2']['name'];
			$file_tmp2 =$_FILES['handset_img2']['tmp_name'];
			$file_path2="../".$folder1."/".time()."INV".$file_name2;
			$img_upld12 = move_uploaded_file($file_tmp2,$file_path2);
		 
			$file_name3=$_FILES['handset_img3']['name'];
			$file_tmp3 =$_FILES['handset_img3']['tmp_name'];
			$file_path3="../".$folder1."/".time()."INV".$file_name3;
			$img_upld13 = move_uploaded_file($file_tmp3,$file_path3);
		 
			$file_name4=$_FILES['handset_img4']['name'];
			$file_tmp4 =$_FILES['handset_img4']['tmp_name'];
			$file_path4="../".$folder1."/".time()."INV".$file_name4;
			$img_upld14= move_uploaded_file($file_tmp4,$file_path4);
		 
			$file_name5=$_FILES['handset_img5']['name'];
			$file_tmp5 =$_FILES['handset_img5']['tmp_name'];
			$file_path5="../".$folder1."/".time()."INV".$file_name5;
			$img_upld15= move_uploaded_file($file_tmp5,$file_path5);
		 
			if($img_upld1 != "" ||  $img_upld12!="" ||  $img_upld13!="" ||  $img_upld14!="" ||  $img_upld15!="" ){
				
				//echo "INSERT INTO image_upload_details set job_no ='".$jobno."', activity='Job Repair',img_url='".$file_path."', img_url1='".$file_path2."',img_url2='".$file_path3."' ,img_url3='".$file_path4."',img_url4='".$file_path5."',upload_date='".$today."',location_code='".$_SESSION['asc_code']."'"."<br><br>";
				
				$result = mysqli_query($link1, "INSERT INTO image_upload_details set job_no ='".$jobno."', activity='Job Repair',img_url='".$file_path."', img_url1='".$file_path2."',img_url2='".$file_path3."' ,img_url3='".$file_path4."',img_url4='".$file_path5."',upload_date='".$today."',location_code='".$_SESSION['asc_code']."'");
				//// check if query is not executed
				if (!$result) {
					$flag = false;
					$error_msg = "Image Upload Problem: " . mysqli_error($link1) . ".";
				}
			}else{
				$flag = false;
				$error_msg = "Invoice Image can not upload on server due to size or image type issue " . mysqli_error($link1) . ".";
			}
		}
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB REPAIR at ".$_SESSION['asc_code'],"Repair Done",$ip,$link1,$flag);
	}
	else{
		$flag = false;
		$error_msg = "Refresh or Re-Submittion is not Allowed: " . mysqli_error($link1) . ".";
	}

}
########## End Gas Charging CASE
	
	
	### Start Repair Done CASE ########## if repair status select as Repair Done
	else if($_POST['jobstatus'] == "6"){
	$messageIdent_con = md5($_SESSION['asc_code'] . $jobno . $_POST['jobstatus']);
			//and check it against the stored value:
			$sessionMessageIdent_con = isset($_SESSION['messageIdent_con'])?$_SESSION['messageIdent_con']:'';
			if($messageIdent_con!=$sessionMessageIdent_con){//if its different:          
				//save the session var:
				$_SESSION['messageIdent_con'] = $messageIdent_con;
		/////initialize post array variables
	
		

$upd_str = " ,close_date='".$today."',close_time='".$currtime."'";
				
		
		$rd_con= $_POST['con_code'];
		$rd_sym = $_POST['sym_code'];
		$rd_sec = $_POST['sec_code'];
		$rd_faultarr = $_POST['fault_code'];
		$rd_repairarr = $_POST['repair_code'];
		$rd_repairlvlarr = $_POST['repair_level'];
		$rd_partarr = $_POST['part'];
		$rd_partpricearr = $_POST['part_price'];
		$swap_imei1 = $_POST['swap_imei1'];
		$swap_imei2= $_POST['swap_imei2'];
		$part_warranty = daysDifference($job_details['open_date'],$job_details['dop']);
		
	
		$rp_fg = 1;
		foreach($rd_faultarr as $k => $val){
			////// insert in repair details
			$partsplit = explode("^",$rd_partarr[$k]);
			if($job_details['customer_type']=="Dealer"){
			$dwp="dwp";
			}else{
			$dwp="wp";
			}
			
			if($partsplit[0]!=""){
				$rp_fg *= 0;
			}else{
				$rp_fg *= 1;
			}
			
		 $wp_part =getAnyDetails($partsplit[0],$dwp,"partcode","partcode_master",$link1);
			if($rd_partarr[$k]){ 
			$part_replc = "Y";
			if($wp_part> $part_warranty ){
			$w_period="IN";
			
			}else{
				$w_period="OUT";
			}
			
			}else{ $part_replc = "N";
			
			
			}
			
				
			$res_reapirdata = mysqli_query($link1,"INSERT INTO repair_detail set job_id='".$job_details['job_id']."', job_no ='".$jobno."', repair_location='".$_SESSION['asc_code']."', repair_type='', location_code='".$job_details['location_code']."', model_id='".$job_details['model_id']."',eng_id='".$eng_name."', bin_id='' , status='6', remark='".$_POST['rep_remark']."', fault_code='".$rd_faultarr[$k]."', rep_lvl='".$rd_repairlvlarr[$k]."', part_repl='".$part_replc."', repair_code='".$rd_repairarr[$k]."', partcode='".$partsplit[0]."', part_qty='1', part_cost='".$rd_partpricearr[$k]."',close_date='".$today."',replace_imei1='".$swap_imei1[$k]."',replace_imei2='".$swap_imei2[$k]."',warranty_status='".$w_period."'");
			//// check if query is not executed
			if (!$res_reapirdata) {
				 $flag = false;
				 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
			}
			///// extra field of jobsheet data which is to be update
			//$upd_str = " ,close_date='".$today."',close_time='".$currtime."'";
			///// if part is consumed
			if($partsplit[0]){
				//// update inventory as user consume part
				 $fifo_result = filo_details_asp($_SESSION['asc_code'],$partsplit[0],1,$link1);
				
			//	print_r($fifo_result);
					$splitted_fifo_data = explode("~",$fifo_result);
					$return_fifo = "UPDATE  fifo_list set fifi_ty =fifi_ty + 1 where  partcode='" .$partsplit[0]. "' and ref_sno='".$splitted_fifo_data[5]."'"; 
											
											
												$queryfifo_return = mysqli_query($link1, $return_fifo);
										//// check if query is not executed
												if (!$queryfifo_return) {
													$flag = false;
													$err_msg = "Fifo Return query Check Code10: ".mysqli_error($link1);
												}
										$bill_fifo = "UPDATE  billing_product_items set return_qty_fifo=return_qty_fifo+1 where  partcode='" .$partsplit[0]. "' and id='".$splitted_fifo_data[5]."'"; 
																					
												$queryfifo_bill = mysqli_query($link1, $bill_fifo);
							if (!$queryfifo_bill) {
													$flag = false;
													$err_msg = "Fifo Return query Check Code11: ".mysqli_error($link1);
												}
				
				///// entry in stock ledger
				$flag = stockLedger($jobno,$today,$partsplit[0],$_SESSION['asc_code'],$job_details['customer_name'],"OUT","OK","JOB REPAIR","Repair Done","1",$rd_partpricearr[$k],$eng_name,$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);
				
										/*$res_invt = mysqli_query($link1,"UPDATE client_inventory set mount_qty = mount_qty-'1' where location_code='".$_SESSION['asc_code']."' and partcode='".$partsplit[0]."' and mount_qty >0");
				//// check if query is not executed
				if (!$res_invt) {
					 $flag = false;
					 $error_msg = "Error details3: " . mysqli_error($link1) . ".";
				}*/
				
				$res_invt_user = mysqli_query($link1,"UPDATE user_inventory set okqty = okqty-'1' where location_code='".$_SESSION['asc_code']."' and partcode='".$partsplit[0]."' and   	locationuser_code ='".$eng_name."' and okqty >0");
				//// check if query is not executed
				if (!$res_invt_user) {
					 $flag = false;
					 $error_msg = "Error detailsuser: " . mysqli_error($link1) . ".";
				}
				
				
				
				
				/////////////////////////////////// asc part Request/////////////////////////For P2C
				if($w_period=='IN'){
					//echo "INSERT INTO part_to_credit set job_no ='".$jobno."', imei='".$job_details['imei']."',from_location='".$_SESSION['asc_code']."', partcode='".$partsplit[0]."', qty='1', price='".$rd_partpricearr[$k]."',cost='".$rd_partpricearr[$k]."',consumedate='".$today."',model_id='".$job_details['model_id']."',status ='1', product_id='".$job_details['product_id']."', brand_id='".$job_details['brand_id']."'";

		
						$res_faulty_user = mysqli_query($link1,"UPDATE user_inventory set faulty = faulty+'1' where location_code='".$_SESSION['asc_code']."' and partcode='".$partsplit[0]."' and 	locationuser_code ='".$eng_name."'");
				//// check if query is not executed
				if (!$res_faulty_user) {
					 $flag = false;
					 $error_msg = "Error detailsuserfauty: " . mysqli_error($link1) . ".";
				}
				
							$res_p2cdata = mysqli_query($link1,"INSERT INTO part_to_credit set job_no ='".$jobno."', imei='".$job_details['imei']."',from_location='".$_SESSION['asc_code']."', partcode='".$partsplit[0]."', qty='1', price='".$splitted_fifo_data[2]."',cost='".$splitted_fifo_data[2]."',consumedate='".$today."',model_id='".$job_details['model_id']."',status ='4', product_id='".$job_details['product_id']."', brand_id='".$job_details['brand_id']."',type='EP2C',eng_id='".$eng_name."',eng_status='1',old_challan='".$splitted_fifo_data[0]."',ref_sno='".$splitted_fifo_data[5]."'");
								if (!$res_p2cdata) {
				 $flag = false;
				 $error_msg = "Error details21: " . mysqli_error($link1) . ".";
			}
				}
					
				
						if($swap_imei1[$k]!=''){
						  $sql_ref = "INSERT INTO imei_details_asp(imei1,imei2,partcode , model_id,location_code,status , entry_date,stock_type,job_no) VALUES ('".$swap_imei1[$k]."','".$swap_imei2[$k]."','".$_POST['stock_type']."','".$job_details['model_id']."','".$_SESSION['asc_code']."','1','".$today."','okqty','".$jobno."')";
			$result_ref =	mysqli_query($link1,$sql_ref);
				if (!$result_ref) {
				 $flag = false;
				 $error_msg = "Error details reg imei: " . mysqli_error($link1) . ".";
			}
			$result222 = mysqli_query($link1,"INSERT INTO imei_history set imei1='".$swap_imei1[$k]."',imei2='".$swap_imei2[$k]."',partcode='".$_POST['stock_type']."',transaction_no='".$jobno."',remark='Stock In' ,location_code='".$_SESSION['asc_code']."' "); 
				//// check if query is not executed
				if (!$result222) {
					$flag = false;
					$error_msg = "Error detailsimei history: " . mysqli_error($link1) . ".";
				}
				}
			}
		}/////end foreach loop
		///// entry in call/job  history
		
			$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],"Job Repair Done",$_POST['close_reason'],$eng_name,$job_details['warranty_status'],$_POST['rep_remark'],$_POST['travel'],"Y",$ip,$link1,$flag);

	$end_date =$datetime;
	$start_date= $job_details['open_date']." ".$job_details['open_time'];
		
	/////////// holidays count between close_tat //////////
	$tot_holiday = mysqli_fetch_array(mysqli_query($link1, "select count(sno) from holidays where date between '".$job_details['open_date']."' and '".$today."' "));
	
	$clstat = daysDifference($today,$job_details['open_date']);
	if($clstat>=$tot_holiday[0]){
		$close_tat = ($clstat - $tot_holiday[0]);
	}else{
		$close_tat = $clstat;
	}	
	  
   $seconds = strtotime($end_date) - strtotime($start_date);
   
   $gethrs=getHoursMinSec($seconds);
   
   /////// replacement flag ///////////
	if($rp_fg == 1){
		$replace_flag = 'N';
	}else{
		$replace_flag = 'Y';
	}
				
$sql_rep=mysqli_query($link1,"select sum(travel_km) as a,updated_by from call_history where job_no ='".$jobno."' and travel='Y' group by updated_by ");


while($row_max=mysqli_fetch_array($sql_rep)){

$max_rep=mysqli_query($link1,"insert into job_claim_appr set job_no='".$jobno."',brand_id='".$job_details['brand_id']."',action_by='".$_SESSION['asc_code']."',rep_lvl ='".$row_level['b']."',model='".$job_details['model_id']."',product_id='".$job_details['product_id']."',entity_type='".$job_details['entity_type']."',eng_name='".$row_max['updated_by']."',hand_date='".$today."',travel_km='".$row_max[a]."',area_type='".$job_details['area_type']."',claim_tat='".$close_tat."',hrs_tat='".$gethrs."',job_count='".$job_details['job_count']."' , part_repl = '".$replace_flag."', status='".$_POST['jobstatus']."' ");
			//// check if query is not executed
		if (!$max_rep) {
			 $flag = false;
			 $error_msg = "Error details3: " . mysqli_error($link1) . ".";
		}	
}

		/////////////////image Upload//////////////////////////////////////////
			$folder1="handset_image";
	if(($_FILES['handset_img']["name"]!='') && ($_FILES["handset_img"]["size"] < 2000000)){
		 $file_name =$_FILES['handset_img']['name'];
		 $file_tmp =$_FILES['handset_img']['tmp_name'];
		  $file_path="../".$folder1."/".time()."INV".$file_name;
		 $img_upld1 = move_uploaded_file($file_tmp,$file_path);
		 
	  $file_name2 =$_FILES['handset_img2']['name'];
		 $file_tmp2 =$_FILES['handset_img2']['tmp_name'];
		  $file_path2="../".$folder1."/".time()."INV".$file_name2;
		 $img_upld12 = move_uploaded_file($file_tmp2,$file_path2);
		 
		  $file_name3=$_FILES['handset_img3']['name'];
		 $file_tmp3 =$_FILES['handset_img3']['tmp_name'];
		  $file_path3="../".$folder1."/".time()."INV".$file_name3;
		 $img_upld13 = move_uploaded_file($file_tmp3,$file_path3);
		 
		 
		  $file_name4=$_FILES['handset_img4']['name'];
		 $file_tmp4 =$_FILES['handset_img4']['tmp_name'];
		  $file_path4="../".$folder1."/".time()."INV".$file_name4;
		 $img_upld14= move_uploaded_file($file_tmp4,$file_path4);
		 
		  $file_name5=$_FILES['handset_img5']['name'];
		 $file_tmp5 =$_FILES['handset_img5']['tmp_name'];
		  $file_path5="../".$folder1."/".time()."INV".$file_name5;
		 $img_upld15= move_uploaded_file($file_tmp5,$file_path5);
		 
		 if($img_upld1 != "" ||  $img_upld12!="" ||  $img_upld13!="" ||  $img_upld14!="" ||  $img_upld15!="" ){
		//echo "INSERT INTO image_upload_details set job_no ='".$jobno."', activity='JOB CREATE',img_url='".$file_path."', upload_date='".$today."',location_code='".$_SESSION['asc_code']."'";
		 	$result = mysqli_query($link1,"INSERT INTO image_upload_details set job_no ='".$jobno."', activity='Job Repair',img_url='".$file_path."', img_url1='".$file_path2."',img_url2='".$file_path3."' ,img_url3='".$file_path4."',img_url4='".$file_path5."',upload_date='".$today."',location_code='".$_SESSION['asc_code']."'");
	  		//// check if query is not executed
		 	if (!$result) {
				$flag = false;
				$error_msg = "Image Upload Problem: " . mysqli_error($link1) . ".";
			}
		 }else{
		 	$flag = false;
			$error_msg = "Invoice Image can not upload on server due to size or image type issue " . mysqli_error($link1) . ".";
		 }
	}
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB REPAIR at ".$_SESSION['asc_code'],"Repair Done",$ip,$link1,$flag);
		}
		else{
			$flag = false;
			$error_msg = "Refresh or Re-Submittion is not Allowed: " . mysqli_error($link1) . ".";
		}

	}
	########## End Repair Done CASE
		
	### Start Demo/instalaltion Done CASE ########## if repair status select as Demo/instalaltion
	else if($_POST['jobstatus'] == "48" || $_POST['jobstatus'] == "49" ){
	$messageIdent_con = md5($_SESSION['asc_code'] . $jobno . $_POST['jobstatus']);
			//and check it against the stored value:
			$sessionMessageIdent_con = isset($_SESSION['messageIdent_con'])?$_SESSION['messageIdent_con']:'';
			if($messageIdent_con!=$sessionMessageIdent_con){//if its different:          
				//save the session var:
				$_SESSION['messageIdent_con'] = $messageIdent_con;
		/////initialize post array variables
$end_date =$datetime;
	$start_date= $job_details['open_date']." ".$job_details['open_time'];
		
	/////////// holidays count between close_tat //////////
	$tot_holiday = mysqli_fetch_array(mysqli_query($link1, "select count(sno) from holidays where date between '".$job_details['open_date']."' and '".$today."' "));
	
	$clstat = daysDifference($today,$job_details['open_date']);
	if($clstat>=$tot_holiday[0]){
		$close_tat = ($clstat - $tot_holiday[0]);
	}else{
		$close_tat = $clstat;
	}
		  
   $seconds = strtotime($end_date) - strtotime($start_date);
   
   $gethrs=getHoursMinSec($seconds);
		

		

				$res_product= mysqli_query($link1,"UPDATE product_registered set serial_no ='".$_POST['serial_no']."' where job_no='".$jobno."'");
	
	//// check if query is not executed
	if (!$res_product) {
		 $flag = false;
		 $error_msg = "Error detailsProducgt: " . mysqli_error($link1) . ".";
	}
		$rd_partarr = $_POST['req_part'];
		if($_POST['jobstatus']==48){
		$repair_type="Installation";
		}else{
		$repair_type="Demo";
		}
		
		foreach($rd_partarr as $k => $val){
			////// insert in repair details
			
			
			$res_reapirdata = mysqli_query($link1,"INSERT INTO repair_detail set job_id='".$job_details['job_id']."', job_no ='".$jobno."', repair_location='".$_SESSION['asc_code']."', repair_type='".$repair_type."', location_code='".$job_details['location_code']."', model_id='".$job_details['model_id']."', eng_id ='".$eng_name."', bin_id='' , status='".$_POST['jobstatus']."', remark='".$_POST['rep_remark']."', repair_code='R0090', partcode='".$rd_partarr[$k]."', part_qty='1',close_date='".$today."',rep_lvl='1'");
			//// check if query is not executed
			if (!$res_reapirdata) {
				 $flag = false;
				 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
			}
			///// extra field of jobsheet data which is to be update
			$upd_str = " ,close_date='".$today."',close_time='".$currtime."'";
			///// if part is consumed
			
				//// update inventory as user consume part
			
				///// entry in stock ledger
				$flag = stockLedger($jobno,$today,$rd_partarr[$k],$_SESSION['asc_code'],$job_details['customer_name'],"OUT","OK",$repair_type,"Repair Done","1",$rd_partpricearr[$k],$eng_name,$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);
				
			/*$res_invt = mysqli_query($link1,"UPDATE client_inventory set mount_qty = mount_qty-'1' where location_code='".$_SESSION['asc_code']."' and partcode='".$rd_partarr[$k]."' and okqty >0");
				//// check if query is not executed
				if (!$res_invt) {
					 $flag = false;
					 $error_msg = "Error details3: " . mysqli_error($link1) . ".";
				}*/
						
				$res_invt_user = mysqli_query($link1,"UPDATE user_inventory set okqty = okqty-'1' where location_code='".$_SESSION['asc_code']."' and partcode='".$rd_partarr[$k]."' and   	locationuser_code ='".$eng_name."' and okqty >0");
				//// check if query is not executed
				if (!$res_invt_user) {
					 $flag = false;
					 $error_msg = "Error detailsuser: " . mysqli_error($link1) . ".";
				}
				
					
				
		
		}/////end foreach loop
		///// entry in call/job  history
		$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],$repair_type,$repair_type,$eng_name,$job_details['warranty_status'],$_POST['rep_remark'],$_POST['travel'],"Y",$ip,$link1,$flag);
		
		$sql_lvel=mysqli_query($link1,"select max(rep_lvl) as b from repair_detail where job_no ='".$jobno."' and repair_location='".$_SESSION['asc_code']."'");

$row_level=mysqli_fetch_array($sql_lvel);

$sql_rep=mysqli_query($link1,"select sum(travel_km) as a,updated_by from call_history where job_no ='".$jobno."' and travel='Y' group by updated_by ");

while($row_max=mysqli_fetch_array($sql_rep)){

$max_rep=mysqli_query($link1,"insert into job_claim_appr set job_no='".$jobno."',brand_id='".$job_details['brand_id']."',action_by='".$_SESSION['asc_code']."',rep_lvl ='".$row_level['b']."',model='".$job_details['model_id']."',product_id='".$job_details['product_id']."',entity_type='".$job_details['entity_type']."',eng_name='".$row_max['updated_by']."',hand_date='".$today."',travel_km='".$row_max[a]."',area_type='".$job_details['area_type']."',claim_tat='".$close_tat."',hrs_tat='".$gethrs."',job_count='".$job_details['job_count']."' ");
			//// check if query is not executed
		if (!$max_rep) {
			 $flag = false;
			 $error_msg = "Error details3: " . mysqli_error($link1) . ".";
		}	
}
				
								//////////////////////////////////////////////image Upload//////////////////////////////////////////
			$folder1="handset_image";
	if(($_FILES['handset_img']["name"]!='') && ($_FILES["handset_img"]["size"] < 2000000)){
		 $file_name =$_FILES['handset_img']['name'];
		 $file_tmp =$_FILES['handset_img']['tmp_name'];
		  $file_path="../".$folder1."/".time()."INV".$file_name;
		 $img_upld1 = move_uploaded_file($file_tmp,$file_path);
		 
		   $file_name2 =$_FILES['handset_img2']['name'];
		 $file_tmp2 =$_FILES['handset_img2']['tmp_name'];
		  $file_path2="../".$folder1."/".time()."INV".$file_name2;
		 $img_upld12 = move_uploaded_file($file_tmp2,$file_path2);
		 
		  $file_name3=$_FILES['handset_img3']['name'];
		 $file_tmp3 =$_FILES['handset_img3']['tmp_name'];
		  $file_path3="../".$folder1."/".time()."INV".$file_name3;
		 $img_upld13 = move_uploaded_file($file_tmp3,$file_path3);
		 
		 
		  $file_name4=$_FILES['handset_img4']['name'];
		 $file_tmp4 =$_FILES['handset_img4']['tmp_name'];
		  $file_path4="../".$folder1."/".time()."INV".$file_name4;
		 $img_upld14= move_uploaded_file($file_tmp4,$file_path4);
		 
		  $file_name5=$_FILES['handset_img5']['name'];
		 $file_tmp5 =$_FILES['handset_img5']['tmp_name'];
		  $file_path5="../".$folder1."/".time()."INV".$file_name5;
		 $img_upld15= move_uploaded_file($file_tmp5,$file_path5);
		 
		 if($img_upld1 != "" ||  $img_upld12!="" ||  $img_upld13!="" ||  $img_upld14!="" ||  $img_upld15!="" ){
		//echo "INSERT INTO image_upload_details set job_no ='".$jobno."', activity='JOB CREATE',img_url='".$file_path."', upload_date='".$today."',location_code='".$_SESSION['asc_code']."'";
		 	$result = mysqli_query($link1,"INSERT INTO image_upload_details set job_no ='".$jobno."', activity='".$repair_type."',img_url='".$file_path."',img_url1='".$file_path2."',img_url2='".$file_path3."' ,img_url3='".$file_path4."',img_url4='".$file_path5."', upload_date='".$today."',location_code='".$_SESSION['asc_code']."'");
	  		//// check if query is not executed
		 	if (!$result) {
				$flag = false;
				$error_msg = "Image Upload Problem: " . mysqli_error($link1) . ".";
			}
		 }else{
		 	$flag = false;
			$error_msg = "Invoice Image can not upload on server due to size or image type issue " . mysqli_error($link1) . ".";
		 }
	}
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$jobno,$repair_type."at".$_SESSION['asc_code'],"Repair Done",$ip,$link1,$flag);
	
		}else{
			$flag = false;
			$error_msg = "Refresh or Re-Submittion is not Allowed: " . mysqli_error($link1) . ".";
		}

	}
	########## End Installation/Demo Done CASE
	
	
	### Start Replacement CASE ########## if repair status select as Replacement
	else if($_POST['jobstatus'] == "8"){
	///////////////////////////  update by priya 0n 19 july to block multiple job creation  ////////////////////////////////////////////
	$messageIdent_repl = md5($_SESSION['asc_code'] . $jobno . $_POST['jobstatus']);
		//and check it against the stored value:
   		$sessionMessageIdent_repl = isset($_SESSION['messageIdent_repl'])?$_SESSION['messageIdent_repl']:'';
		if($messageIdent_repl!=$sessionMessageIdent_repl){//if its different:          
					//save the session var:
			$_SESSION['messageIdent_repl'] = $messageIdent_repl;
	$end_date =$datetime;
	$start_date= $job_details['open_date']." ".$job_details['open_time'];
	
	/////////// holidays count between close_tat //////////
	$tot_holiday = mysqli_fetch_array(mysqli_query($link1, "select count(sno) from holidays where date between '".$job_details['open_date']."' and '".$today."' "));
	
	$clstat = daysDifference($today,$job_details['open_date']);
	if($clstat>=$tot_holiday[0]){
		$close_tat = ($clstat - $tot_holiday[0]);
	}else{
		$close_tat = $clstat;
	}	
		  
   $seconds = strtotime($end_date) - strtotime($start_date);
   
   $gethrs=getHoursMinSec($seconds);
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		if($_POST['fault_code_replace']!='' && $_POST['repair_code_replace']!='' ){
			$expld_repcode = explode("~",$_POST['repair_code_replace']);
			////// insert in repair details
			$res_replcedata = mysqli_query($link1,"INSERT INTO repair_detail set job_id='".$job_details['job_id']."', job_no ='".$jobno."', repair_location='".$_SESSION['asc_code']."', repair_type='', location_code='".$job_details['location_code']."', model_id='".$job_details['model_id']."',eng_id='".$eng_name."' , bin_id='' , status='8', remark='".$_POST['rep_remark']."', fault_code='".$_POST['fault_code_replace']."', rep_lvl='".$expld_repcode[1]."', part_repl='Y', repair_code='".$expld_repcode[0]."', partcode='".$_POST['rep_part']."', part_qty='1', part_cost='0.00', replace_imei1='".$_POST['rep_tagno']."',repl_model = '".$_POST['replace_model']."' , replace_imei2='".$_POST['new_imei2']."', replace_serial=''");
			//// check if query is not executed
			if (!$res_replcedata) {
				 $flag = false;
				 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
			}
			//// update inventory as user consume part
	///// entry in stock ledger
	
			$flag = stockLedger($jobno,$today,$_POST['rep_part'],$_SESSION['asc_code'],$job_details['customer_name'],"OUT","OK",$repair_type,"Repair Done","1",$rd_partpricearr[$k],$eng_name,$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);
				
			/*$res_invt = mysqli_query($link1,"UPDATE client_inventory set mount_qty = mount_qty-'1' where location_code='".$_SESSION['asc_code']."' and partcode='".$_POST['rep_part']."' and mount_qty >0");
				//// check if query is not executed
				if (!$res_invt) {
					 $flag = false;
					 $error_msg = "Error details3: " . mysqli_error($link1) . ".";
				}*/
						
				$res_invt_user = mysqli_query($link1,"UPDATE user_inventory set okqty = okqty-'1' where location_code='".$_SESSION['asc_code']."' and partcode='".$_POST['rep_part']."' and   	locationuser_code ='".$eng_name."' and okqty >0");
				//// check if query is not executed
				if (!$res_invt_user) {
					 $flag = false;
					 $error_msg = "Error detailsuser: " . mysqli_error($link1) . ".";
				}
			//	echo "update imei_details_eng set job_no ='".$jobno."',dis_date='".$today."',status ='3' where (imei1='".$_POST['rep_tagno']."' ) and location_code='".$_SESSION['asc_code']."' and stock_type='ok' and status ='1'";
		$res_p2chset = mysqli_query($link1,"update imei_details_eng set job_no ='".$jobno."',dis_date='".$today."',status ='3' where (imei1='".$_POST['rep_tagno']."' ) and location_code='".$_SESSION['asc_code']."' and stock_type='ok' and status ='1'");
											
								if (!$res_p2chset) {
				 $flag = false;
				 $error_msg = "Error detailsImei Details: " . mysqli_error($link1) . ".";
		
					}
					$row_hset= mysqli_affected_rows($link1);
					if($row_hset==0){
							 $flag = false;
				 $error_msg = "No Row Affected";
						
						}
						
						$result222 = mysqli_query($link1,"INSERT INTO imei_history set imei1='".$_POST['rep_tagno']."',imei2='".$_POST['new_imei2']."',partcode='".$_POST['rep_part']."',transaction_no='".$jobno."',remark='Tag Replace' ,location_code='".$_SESSION['asc_code']."' "); 
				//// check if query is not executed
				if (!$result222) {
					$flag = false;
					$error_msg = "Error detailsimei history: " . mysqli_error($link1) . ".";
				}
		}
			$upd_str = " ,close_date='".$today."',close_time='".$currtime."'";
		///// entry in call/job  history
		$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],"Job Replacement","Replacement",$eng_name,$job_details['warranty_status'],$_POST['rep_remark'],$_POST['travel'],"Y",$ip,$link1,$flag);
		////// insert in activity table////
		
										//////////////////////////////////////////////image Upload//////////////////////////////////////////
			$folder1="handset_image";
	if(($_FILES['handset_img']["name"]!='') && ($_FILES["handset_img"]["size"] < 2000000)){
		 $file_name =$_FILES['handset_img']['name'];
		 $file_tmp =$_FILES['handset_img']['tmp_name'];
		  $file_path="../".$folder1."/".time()."INV".$file_name;
		 $img_upld1 = move_uploaded_file($file_tmp,$file_path);
		 
		   $file_name2 =$_FILES['handset_img2']['name'];
		 $file_tmp2 =$_FILES['handset_img2']['tmp_name'];
		  $file_path2="../".$folder1."/".time()."INV".$file_name2;
		 $img_upld12 = move_uploaded_file($file_tmp2,$file_path2);
		 
		  $file_name3=$_FILES['handset_img3']['name'];
		 $file_tmp3 =$_FILES['handset_img3']['tmp_name'];
		  $file_path3="../".$folder1."/".time()."INV".$file_name3;
		 $img_upld13 = move_uploaded_file($file_tmp3,$file_path3);
		 
		 
		  $file_name4=$_FILES['handset_img4']['name'];
		 $file_tmp4 =$_FILES['handset_img4']['tmp_name'];
		  $file_path4="../".$folder1."/".time()."INV".$file_name4;
		 $img_upld14= move_uploaded_file($file_tmp4,$file_path4);
		 
		  $file_name5=$_FILES['handset_img5']['name'];
		 $file_tmp5 =$_FILES['handset_img5']['tmp_name'];
		  $file_path5="../".$folder1."/".time()."INV".$file_name5;
		 $img_upld15= move_uploaded_file($file_tmp5,$file_path5);
		 
		 if($img_upld1 != "" ||  $img_upld12!="" ||  $img_upld13!="" ||  $img_upld14!="" ||  $img_upld15!="" ){
		//echo "INSERT INTO image_upload_details set job_no ='".$jobno."', activity='JOB CREATE',img_url='".$file_path."', upload_date='".$today."',location_code='".$_SESSION['asc_code']."'";
		 	$result = mysqli_query($link1,"INSERT INTO image_upload_details set job_no ='".$jobno."', activity='Replacement',img_url='".$file_path."',img_url1='".$file_path2."',img_url2='".$file_path3."' ,img_url3='".$file_path4."',img_url4='".$file_path5."', upload_date='".$today."',location_code='".$_SESSION['asc_code']."'");
	  		//// check if query is not executed
		 	if (!$result) {				$flag = false;
				$error_msg = "Image Upload Problem: " . mysqli_error($link1) . ".";
			}
		 }else{
		 	$flag = false;
			$error_msg = "Invoice Image can not upload on server due to size or image type issue " . mysqli_error($link1) . ".";
		 }
	}

		$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB REPAIR at ".$_SESSION['asc_code'],"Replacement",$ip,$link1,$flag);
			}
		else{
			$flag = false;
			$error_msg = "Refresh or Re-Submittion is not Allowed: " . mysqli_error($link1) . ".";
		}
		
		
$sql_rep=mysqli_query($link1,"select sum(travel_km) as a,updated_by from call_history where job_no ='".$jobno."' and travel='Y' group by updated_by ");



while($row_max=mysqli_fetch_array($sql_rep)){
	
$max_rep=mysqli_query($link1,"insert into job_claim_appr set job_no='".$jobno."',brand_id='".$job_details['brand_id']."',action_by='".$_SESSION['asc_code']."',rep_lvl ='".$row_level['b']."',model='".$job_details['model_id']."',product_id='".$job_details['product_id']."',entity_type='".$job_details['entity_type']."',eng_name='".$row_max['updated_by']."',hand_date='".$today."',travel_km='".$row_max[a]."',area_type='".$job_details['area_type']."',claim_tat='".$close_tat."',hrs_tat='".$gethrs."',job_count='".$job_details['job_count']."' ");
			//// check if query is not executed
		if (!$max_rep) {
			 $flag = false;
			 $error_msg = "Error details3: " . mysqli_error($link1) . ".";
		}	
}
	}
	########## End Replacement CASE
	
	########## Otherwise go to default case
	else{
		//// nothing to do
	}
	//// update status and repair remark in job sheet
	if($job_details['call_for']=="Installation"){
	$inslladate = " installation_date='".$today."',";
}else{
	$inslladate="";
	}
	$res_product= mysqli_query($link1,"UPDATE product_registered set  ".$inslladate."  serial_no ='".$_POST['serial_no']."' where job_no='".$jobno."'");
	
	//// check if query is not executed
	if (!$res_product) {
		 $flag = false;
		 $error_msg = "Error detailsProducgt: " . mysqli_error($link1) . ".";
	}
	
	if($_POST['jobstatus']==6 || $_POST['jobstatus']==8 || $_POST['jobstatus']==48 || $_POST['jobstatus']==49 || $_POST['jobstatus']==11 || $_POST['jobstatus']==12  ){
	$st_status=6;
	
	}else{
	$st_status=2;
	
	}

	$res_jobsheet = mysqli_query($link1,"UPDATE jobsheet_data set imei='".$_POST['serial_no']."',status='".$_POST['jobstatus']."', sub_status='".$_POST['jobstatus']."' ".$upd_str.",new_part='".$_POST['stock_type']."',reason='".$_POST['pending_reason']."',close_rmk='".$_POST['close_reason']."',close_tat='".$close_tat."',warranty_status='".$_POST['warranty_status']."',warranty_card='".$_POST['warrantycard']."',invoice_no='".$_POST['invoice_no']."',manufacter_date ='".$_POST['manufacterdate']."',doc_type='".$_POST['closetype']."',customer_satif='".$_POST['custumer_pay']."' ,".$inslladate." pen_status='".$st_status."' where job_no='".$jobno."'");
	
	//// check if query is not executed
	if (!$res_jobsheet) {
		 $flag = false;
		 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
	}
	///// check if all query are successfully executed
	if ($flag) {
		mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
		$msg = "Repair action has been taken successfully.";
		
		mysqli_close($link1);
   ///// move to parent page
   $sms_msg="Dear ".$job_details['customer_name']."Your Complaint No".$jobno." has been closed";
		if($_POST['call_for']!='Workshop'){
   header("location:job_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav."&smsmsg=".base64_encode($sms_msg)."&to=".$job_details['contact_no']."&status=".$_POST['jobstatus']."");
		}
		else {
		header("location:job_list_asp.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav."&smsmsg=".base64_encode($sms_msg)."&to=".$job_details['contact_no']."&status=".$_POST['jobstatus']."");
		}
    exit;
	} else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	header("location:complaint_repair.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav."&smsmsg=".$sms_msg."&refid=".base64_encode($jobno)."&status=".$_POST['jobstatus']."");
    exit;
	} 

}
?>