<?php
require_once("../includes/config.php");
//$arrstatus = getFullStatus("process",$link1);
$docid=base64_decode($_REQUEST['refid']);
//// job details
$job_sql="SELECT * FROM jobsheet_data where job_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);

$doa_sql="SELECT * FROM dms_repl_api_data where job_no = '".$docid."' and status = 'In Process' ";
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

		if($status=="Rejected"){

			//echo "UPDATE dms_repl_api_data set status='".$status."', approval_by = '".$_SESSION['userid']."', approval_date = '".$today."', approval_time = '".$currtime."', approval_remark = '".$remark."' where job_no='".$docid."' "."<br><br>";

			$dms_upd = mysqli_query($link1,"UPDATE dms_repl_api_data set status='".$status."', approval_by = '".$_SESSION['userid']."', approval_date = '".$today."', approval_time = '".$currtime."', approval_remark = '".$remark."' where job_no='".$docid."' ");
			/// check if query is execute or not//
			if(!$dms_upd){
				$flag = false;
				$err_msg = "Error1". mysqli_error($link1) . ".";
			}

			$flag = callHistory($docid,$job_row['current_location'],"","DMS Request Rejected","DMS Request Rejected",$_SESSION['userid'],"",$remark,"","",$ip,$link1,$flag);

		}else if($status=="Approved"){

			//echo "select replace_serial_no, serial_no, job_no from replacement_data where replace_serial_no='".strtoupper(trim($replace_serial_no))."' and status != '12' "."<br><br>";

			$res_serial = mysqli_query($link1,"select replace_serial_no, serial_no, job_no from replacement_data where replace_serial_no='".strtoupper(trim($replace_serial_no))."' and status != '12' ");
			$count_serial = mysqli_num_rows($res_serial);
			$serial_data=mysqli_fetch_array($res_serial);
			if($count_serial > 0){

				$flag = false;
				$err_msg = "This serial no (".$serial_data['replace_serial_no'].") already replaced in this complaint (".$serial_data['job_no']."). Please use other serial no.";

			}else{
				if($image_wr_card != "" || $image_prd != ""){

					//echo "INSERT INTO image_upload_details set job_no ='".$job_row['job_no']."', activity='DMS Complaint Replacement Approval', img_url='".$image_wr_card."', img_url1='".$image_prd."', upload_date='".$today."',location_code='".$_SESSION['userid']."'"."<br><br>";

					$result_img = mysqli_query($link1,"INSERT INTO image_upload_details set job_no ='".$job_row['job_no']."', activity='DMS Complaint Replacement Approval', img_url='".$image_wr_card."', img_url1='".$image_prd."', upload_date='".$today."',location_code='".$_SESSION['userid']."'");
		
					//// check if query is not executed
					if (!$result_img) {
						$flag = false;
						$error_msg = "Image Upload Problem: " . mysqli_error($link1) . ".";
					}
				}

				//echo "UPDATE dms_repl_api_data set status='".$status."', approval_by = '".$_SESSION['userid']."', approval_date = '".$today."', approval_time = '".$currtime."', approval_remark = '".$remark."' where job_no='".$docid."' "."<br><br>";
	
				$dms_upd = mysqli_query($link1,"UPDATE dms_repl_api_data set status='".$status."', approval_by = '".$_SESSION['userid']."', approval_date = '".$today."', approval_time = '".$currtime."', approval_remark = '".$remark."' where job_no='".$docid."' ");
				/// check if query is execute or not//
				if(!$dms_upd){
					$flag = false;
					$err_msg = "Error1". mysqli_error($link1) . ".";
				}
	
				$stttr = "";
				if($replace_serial_no!=""){
					$stttr = " , replace_serial = '".strtoupper(trim($replace_serial_no))."', replace_model = '".$replace_model_id."' ";
				}else{
					$stttr = "";
				}

				//echo "UPDATE jobsheet_data set status='10',sub_status  = '10', l3_status='10' ".$stttr.", close_date='".$today."', close_time='".$currtime."', pen_status = '6'  where job_no='".$docid."' "."<br><br>";
	
				$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set status='10',sub_status  = '10', l3_status='10' ".$stttr.", close_date='".$repl_serial_date."', close_time='".$repl_serial_time."', pen_status = '6', repl_platform='DMS'  where job_no='".$docid."' ");
				/// check if query is execute or not//
				if(!$jobsheet_upd){
					$flag = false;
					$err_msg = "Error1". mysqli_error($link1) . ".";
				}
				
				//echo "SELECT status,job_id,job_no,current_location,location_code,city_id,model_id,eng_id,product_id,brand_id,customer_name,call_for,vistor_date,close_date,open_date,warranty_status,city_id,state_id,customer_id,imei,contact_no,email,address,pincode,dop,warranty_days,warranty_end_date,ref_no,current_location,balance_warranty_days FROM jobsheet_data WHERE job_no='".$docid."'"."<br><br>";
	
				//////// get job details
				$old_s = mysqli_fetch_array(mysqli_query($link1,"SELECT status,job_id,job_no,current_location,location_code,city_id,model_id,eng_id,product_id,brand_id,customer_name,call_for,vistor_date,close_date,open_date,warranty_status,city_id,state_id,customer_id,imei,contact_no,email,address,pincode,dop,warranty_days,warranty_end_date,ref_no,balance_warranty_days FROM jobsheet_data WHERE job_no='".$docid."'"));

				//echo "select partcode,part_name from partcode_master where model_id='".$replace_model_id."' and part_category='UNIT'"."<br><br>";
	
				//$sql_model=mysqli_fetch_array(mysqli_query($link1,"select partcode,part_name from partcode_master where model_id='".$replace_model_id."' and part_category='UNIT'"));
				$sql_model=mysqli_fetch_array(mysqli_query($link1,"select partcode,part_name from partcode_master where model_id='".$replace_model_id."' and part_category='FG Unit'"));

				//echo "insert into replacement_data set job_no='".$docid."',open_date='".$old_s['open_date']."',brand_id='".$old_s['brand_id']."',product_id='".$old_s['product_id']."',model_id='".$old_s['model_id']."',partcode='',serial_no='".$old_s['imei']."',dop='".$old_s['dop']."',warranty_days='".$old_s['warranty_days']."',warranty_status='".$old_s['warranty_status']."',warranty_end_date='".$old_s['warranty_end_date']."', close_date='".$repl_serial_date."', close_time='".$repl_serial_time."',replace_by='".$old_s['eng_id']."',replace_location='".$old_s['current_location']."',replace_serial_no='".strtoupper(trim($replace_serial_no))."',replace_model_id='".$replace_model_id."',replace_partcode='".$sql_model['partcode']."',entry_time='".$currtime."',entry_date='".$today."',replace_serial_mfg='',replace_serial_mfg_ex='', balance_warranty_days = '".$old_s['balance_warranty_days']."', repl_platform='DMS' "."<br><br>";
	
				$sql_replacement=mysqli_query($link1,"insert into replacement_data set job_no='".$docid."',open_date='".$old_s['open_date']."',brand_id='".$old_s['brand_id']."',product_id='".$old_s['product_id']."',model_id='".$old_s['model_id']."',partcode='',serial_no='".$old_s['imei']."',dop='".$old_s['dop']."',warranty_days='".$old_s['warranty_days']."',warranty_status='".$old_s['warranty_status']."',warranty_end_date='".$old_s['warranty_end_date']."', close_date='".$repl_serial_date."', close_time='".$repl_serial_time."',replace_by='".$old_s['eng_id']."',replace_location='".$old_s['current_location']."',replace_serial_no='".strtoupper(trim($replace_serial_no))."',replace_model_id='".$replace_model_id."',replace_partcode='".$sql_model['partcode']."',entry_time='".$currtime."',entry_date='".$today."',replace_serial_mfg='',replace_serial_mfg_ex='', balance_warranty_days = '".$old_s['balance_warranty_days']."', repl_platform='DMS' ");	
				if(!$sql_replacement) {
					$flag = false;
					$err_msg = "Error repl". mysqli_error($link1) . ".";
				}

				////// insert data in repair table		
				$res_reapirdata = mysqli_query($link1, "INSERT INTO repair_detail SET job_id='".$old_s['job_id']."', job_no ='".$docid."', repair_location='".$old_s['current_location']."', repair_type='Replacement', location_code='".$old_s['location_code']."', model_id='".$old_s['model_id']."', eng_id ='".$old_s['eng_id']."' , status='10', remark='Battery Replaced', repair_code='', partcode='', part_qty='1', fault_code='', rep_lvl='', part_repl='',close_date='".$today."', warranty_status = '".$old_s['warranty_status']."' ,old_challan='', ref_sno='', product_id = '".$old_s['product_id']."', brand_id='".$old_s['brand_id']."', replace_serial='".strtoupper(trim($replace_serial_no))."', repl_model='".$replace_model_id."', old_serial='".$old_s['imei']."', part_cost='".$sql_model['customer_price']."',loc_on_pcb='', pcb_repairable='', app_version='".$app_version."', scan_type='M',img_name='', battery_name='',battery_other='', module = '', old_type = 'M', old_serial_fileName = '' ");
				//// check if query is not executed
				if (!$res_reapirdata){
					$flag = false;
					$err_msg = "Error In repair Details table: " . mysqli_error($link1) . ".";
				}
	
				$flag = callHistory($docid,$job_row['current_location'],"","DMS Request Approved","DMS Request Approved - Serial Replaced",$_SESSION['userid'],"",$remark,"","",$ip,$link1,$flag);
			}	

		}else{
			$flag = false;
			$err_msg = "Please select atleast one option.";
		}	

		////////////////////////////////////////////////////////////////////////////////////////////////////////////
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'], $docid,"",$remark,$_SERVER['REMOTE_ADDR'], $link1, $flag);

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
		header("location:job_list_repl_btr_dms.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
	</head>
	<script>
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
	</script>
<script language="javascript" type="text/javascript">
function validateImage(nam,ind) {
	var err_msg="";
	
    var file = document.getElementById(nam).files[0];
    var t = file.type.split('/').pop().toLowerCase();
	
    if(t != "jpeg" && t != "jpg" && t != "png" && t != "bmp" && t != "gif") {
		err_msg = "<strong>Please select a valid file. <br/></strong>";
		document.getElementById("errmsg"+ind).innerHTML = err_msg;
		document.getElementById(nam).value = '';
        return false;
    }else if(file.size > 2048000){  /**** 204800 ***/
		err_msg = "<strong>Max file size can be 2 MB.<br/></strong>";
		document.getElementById("errmsg"+ind).innerHTML = err_msg;
		document.getElementById(nam).value = '';
        return false;
    }else{
		document.getElementById("errmsg"+ind).innerHTML ="";
	}
    
	return true;
}

function displaySerialDataCheck(){
	var mm_serial =  document.getElementById('replace_serial_no').value;
	if(mm_serial!=""){
		$.ajax({
			type:'post',
			url:'../includes/getAzaxFields.php',
			data:{checkSerialDupliReplBtr:mm_serial},
			success:function(data){
				var data_split=data.split("~");
				//console.log(data_split);
				if(data_split[0]=="0"){
					getSerialdeatils1();
				}else{
					document.getElementById('replace_serial_no').value = "";
					alert('This serial no ('+data_split[3]+') already replaced in this complaint ('+data_split[2]+'). Please use other serial no.');
				}
			}
		});
	}
}

function getSerialdeatils1(){
	var myString =  document.getElementById('replace_serial_no').value;
	if(myString != ""){
		getSerialdeatils(myString,'0');
	}
}	

function getSerialdeatils(myString,ind){
	var mm_serial=myString;
	var myNewString=myString.substr(1);
	var indx=ind;
///// check first character is number or alphabet \\\\\
	var check_f = myString.substr(0,1);
       
	if (Number(check_f)){ 
	//alert(check_f+'isnumber');	
///// if first character is number \\\\\	
///// check first two character are string or number \\\\\
	var check_y = myString.substr(0,2); 	
	if (isNaN(check_y)){
	//alert(check_y+'Not Number');
	var mm_year = myString.substr(0,1); 
	var mm_month = myString.substr(1,1); 
	var mm_type = myString.substr(2,1); 
	var mm_model = myString.substr(3,1); 
	var mm_plant = myString.substr(4,2); 
	var mm_plant_n=myString.substr(4,1);
	var mm_line_n=myString.substr(5,1);
	var mm_component_n=myString.substr(6,2);
	var mm_sno = myString.substr(8);
		} else {
	var mm_year = myString.substr(0,2); 
	var mm_month = myString.substr(2,1); 
	var mm_type = myString.substr(3,1); 
	var mm_model = myString.substr(4,1); 
	var mm_plant = myString.substr(5,2); 
	var mm_plant_n=myString.substr(5,1);
	var mm_line_n=myString.substr(6,1);
	var mm_component_n=myString.substr(7,2);
	var mm_sno = myString.substr(9);
		}
        
	}
///// if first character is alphabet \\\\\\	
else {
///// check first two character are string or number after excluding first Alphabet \\\\\

	var check_y = myNewString.substr(0,2); 	
	if (isNaN(check_y)){
	//alert(check_y);
	var mm_year = myNewString.substr(0,1); 
	var mm_month = myNewString.substr(1,1); 
	var mm_type = myNewString.substr(2,1); 
	var mm_model = myNewString.substr(3,1); 
	var mm_plant = myNewString.substr(4,2); 
	var mm_plant_n=myString.substr(4,1);
	var mm_line_n=myString.substr(5,1);
	var mm_component_n=myString.substr(6,2);
	var mm_sno = myNewString.substr(8);	
		} else {
	var mm_year = myNewString.substr(0,2); 
	var mm_month = myNewString.substr(2,1); 
	var mm_type = myNewString.substr(3,1); 
	var mm_model = myNewString.substr(4,1); 
	var mm_plant = myNewString.substr(5,2); 
	var mm_plant_n=myString.substr(5,1);
	var mm_line_n=myString.substr(6,1);
	var mm_component_n=myString.substr(7,2);
	var mm_sno = myNewString.substr(9);
		}
    
}
////////////////// Year \\\\\\\\\\\\\\\\\\\\\
		if(mm_year=="0" || mm_year=="1" ||mm_year=="2" ||mm_year=="3" ||mm_year=="4" ||mm_year=="5" ||mm_year=="6" ||mm_year=="7" ||mm_year=="7" || mm_year=="8" || mm_year=="9"){
		var mm_yearn=200;}
		else if(mm_year=="10" || mm_year=="11" || mm_year=="12" || mm_year=="13" || mm_year=="14" || mm_year=="15" || mm_year=="16" || mm_year=="17" || mm_year=="18" || mm_year=="19" || mm_year=="20" || mm_year=="21" || mm_year=="22" || mm_year=="23" || mm_year=="24" || mm_year=="25"){
		var mm_yearn=20;}
		else{var mm_yearn='';}
		
////////////////// Month \\\\\\\\\\\\\\\\\\\\\
			 if(mm_month=="A" || mm_month=="a"){
		var mm_monthn='01';}
		else if(mm_month=="B" || mm_month=="b"){
		var mm_monthn='02';}
		else if(mm_month=="C" || mm_month=="c"){
		var mm_monthn='03';}
		else if(mm_month=="D" || mm_month=="d"){
		var mm_monthn='04';}
		else if(mm_month=="E" || mm_month=="e"){
		var mm_monthn='05';}
		else if(mm_month=="F" || mm_month=="f"){
		var mm_monthn='06';}
		else if(mm_month=="G" || mm_month=="g"){
		var mm_monthn='07';}
		else if(mm_month=="H" || mm_month=="h"){
		var mm_monthn='08';}
		else if(mm_month=="I" || mm_month=="i"){
		var mm_monthn='09';}
		else if(mm_month=="J" || mm_month=="j"){
		var mm_monthn='10';}
		else if(mm_month=="K" || mm_month=="k"){
		var mm_monthn='11';}
		else if(mm_month=="L" || mm_month=="l"){
		var mm_monthn='12';}
		else {var mm_monthn='';}
///////////////// MFD Date \\\\\\\\\\\\\\\\		
	var mm_mfd = mm_yearn+mm_year+'-'+mm_monthn;
////////document.getElementById("mfd["+indx+"]").value=mm_mfd;
///////////////// Product \\\\\\\\\\\\\\\\\\\\	
//chkProductSno(mm_type,ind);
///////////////// Model \\\\\\\\\\\\\\\\\\\\	
//chkModelSno(mm_model,mm_type,ind);
///////////////// Plant \\\\\\\\\\\\\\\\\\\\	
//chkPlantSno(mm_plant,ind);
///////////////// Plant_n \\\\\\\\\\\\\\\\\\\\	
//chkPlant_nSno(mm_plant_n,ind);
///////////////// line \\\\\\\\\\\\\\\\\\\\	
//chkLineSno(mm_line_n,mm_plant_n,ind);
///////////////// Component \\\\\\\\\\\\\\\\\\\\	
//chkComponentSno(mm_component_n,mm_model,ind);
///////////////// Sno \\\\\\\\\\\\\\\\\\\\	hold now 
//getWarrantyDataSno(mm_serial,mm_model,mm_mfd,ind,mm_type);
///////////////// Check Serial No \\\\\\\\\\\\\\\\\\\\
//checkSrNoData(mm_serial,mm_model,mm_mfd,ind,mm_type);

checkSrNoDataCRM(mm_serial);

////tttttttttttttttt
//getmapVOC();

};

function checkSrNoDataCRM(mm_serial){ 
	if(mm_serial!=""){		
		var strSubmit = "action=getSrNoWiseDataCRM&mm_serial="+mm_serial;
		var strURL = "../includes/getSerialData_V7.php";
		var strResultFunc="displaySerialDataCRM";
		xmlhttpPost(strURL,strSubmit,strResultFunc);
		return false;	
	}
}

function displaySerialDataCRM(result){
	var res1=result.split("^");
	//console.log(res1);
	getNewWttyBySrCRM(res1[0], res1[2], res1[4]);

}	

function getNewWttyBySrCRM(serial_no, product_id, model_id){
	var strSubmit = "action=getPartSrNoWiseDataCRMREPL&product_id="+product_id+"&model_id="+model_id+"&serial_no="+serial_no;
	var strURL = "../includes/getSerialData_V7.php";
	var strResultFunc="displayPartSerialDataCRM";
	xmlhttpPost(strURL,strSubmit,strResultFunc);
	return false;	
}

function displayPartSerialDataCRM(result){
	var res1=result.split("^");
	//console.log(res1);
	$('#modeldiv').html(res1[0]);
	//$('#prddiv').html(res1[1]);
}	



</script>

<script type="text/javascript" src="../js/ajax.js"></script>

	<body onKeyPress="return keyPressed(event);">

		<div class="container-fluid">
			<div class="row content">
				<?php 
	include("../includes/leftnav2.php");
				?>
				<div class="<?=$screenwidth?>">
					<h2 align="center"><i class="fa fa-list-alt"></i> DMS REPL SERIAL APPROVAL </h2>
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
											<td><label class="control-label">AMC Number</label></td>
											<td><?=$product_det['amc_no']?></td>
											<td><label class="control-label">AMC Expiry Date </label></td>
											<td ><?=dt_format($product_det['amc_end_date'])?></td>

										</tr>
										<tr>
											<td><label class="control-label">Date Of Installation</label></td>
											<td><?=dt_format($product_det['installation_date'])?></td>
											<td><label class="control-label">Entity Name</label></td>
											<td ><?php echo getAnyDetails($job_row['entity_type'],"name","id","entity_type",$link1);?></td>

										</tr>
										<tr>
											<td><label class="control-label">Dealer Name</label></td>
											<td><?=$job_row['dname']?></td>
											<td><label class="control-label">Invoice No</label></td>
											<td><?=$job_row['inv_no']?></td>
										</tr>
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
										<!-------------------------------------------------------------------------------------->

										<?php 
	$image_det = mysqli_query($link1,"SELECT * FROM image_upload_details  where job_no='".$job_row['job_no']."' and activity != 'Image Upload by app - DOPFreez' ");
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
												<a href="<?php echo $row_image['img_url']; ?>" target="_blank" ><img src="<?=$row_image['img_url']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></a>
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
												<a href="<?php echo $row_image['img_url1']; ?>" target="_blank" ><img src="<?=$row_image['img_url1']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></a>
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
												<a href="<?php echo $row_image['img_url2']; ?>" target="_blank" ><img src="<?=$row_image['img_url2']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></a>
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
												<a href="<?php echo $row_image['img_url3']; ?>" target="_blank" ><img src="<?=$row_image['img_url3']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></a>
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
												<a href="<?php echo $row_image['img_url4']; ?>" target="_blank" ><img src="<?=$row_image['img_url4']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></a>
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
													<a href="<?php echo $row_image['img_url6']; ?>" target="_blank" ><img src="<?=$row_image['img_url6']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></a>
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


						<?php $initial_qr = mysqli_query($link1, "SELECT * FROM initial_btr_data where job_no='" . $docid . "' order by id DESC ");
						if (mysqli_num_rows($initial_qr) > 0) { ?>
							<div class="panel panel-info table-responsive">
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
									<td width="10%"><strong>OCV</strong></td>
									<td width="10%"><strong>SG C1</strong></td>
									<td width="10%"><strong>SG C2</strong></td>
									<td width="10%"><strong>SG C3</strong></td>
									<td width="10%"><strong>SG C4</strong></td>
									<td width="10%"><strong>SG C5</strong></td>
									<td width="10%"><strong>SG C6</strong></td>
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
										<td><?= $initial_info['ocv'] ?></td>
										<td><?= $initial_info['sg_c1'] ?></td>
										<td><?= $initial_info['sg_c2'] ?></td>
										<td><?= $initial_info['sg_c3'] ?></td>
										<td><?= $initial_info['sg_c4'] ?></td>
										<td><?= $initial_info['sg_c5'] ?></td>
										<td><?= $initial_info['sg_c6'] ?></td>
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
									<td width="10%"><strong>TOC</strong></td>
									<td width="10%"><strong>OCV</strong></td>
									<td width="10%"><strong>SG C1</strong></td>
									<td width="10%"><strong>SG C2</strong></td>
									<td width="10%"><strong>SG C3</strong></td>
									<td width="10%"><strong>SG C4</strong></td>
									<td width="10%"><strong>SG C5</strong></td>
									<td width="10%"><strong>SG C6</strong></td>
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
										<td><?= $after_info['toc'] ?></td>
										<td><?= $after_info['ocv'] ?></td>
										<td><?= $after_info['sg_c1'] ?></td>
										<td><?= $after_info['sg_c2'] ?></td>
										<td><?= $after_info['sg_c3'] ?></td>
										<td><?= $after_info['sg_c4'] ?></td>
										<td><?= $after_info['sg_c5'] ?></td>
										<td><?= $after_info['sg_c6'] ?></td>
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

						<!--approval for EP-->
						<?php if($job_row['status'] == '84' && $job_row['sub_status'] == '84'){ ?>
						<form id="frm1" name="frm1" method="post" enctype="multipart/form-data" >
							<div class="panel panel-info table-responsive">
								<div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Replacement Request Action</div>
								<div class="panel-body">
									<table class="table table-bordered" width="100%">
										<tbody>
											
											<tr>
												<td width="20%"><label class="control-label">Replace Serial No</label></td>
												<td width="30%"><?php echo $doa_row['repl_serial_no']; ?></td>
												<td width="20%"><label class="control-label">SAP Matterial Code</label></td>
												<td width="30%"><?php echo $doa_row['sap_mat_code']; ?></td>
											</tr>
											<tr>
												<td width="20%"><label class="control-label">REPL Date</label></td>
												<td width="30%"><?php echo $doa_row['repl_serial_date']; ?></td>
												<td width="20%"><label class="control-label">REPL Time</label></td>
												<td width="30%"><?php echo $doa_row['repl_serial_time']; ?></td>
											</tr>
											<tr>
												<td width="20%"><label class="control-label">Product</label></td>
												<td width="30%"><?php 
													$prd_query="SELECT product_id,product_name FROM product_master where product_id = '".$doa_row['product_id']."' ";
													$prd_dept=mysqli_query($link1,$prd_query);
													$product_name = mysqli_fetch_array($prd_dept);
													echo $product_name['product_name']." | ".$doa_row['product_id'];
												?></td>
												<td width="20%"><label class="control-label">Brand</label></td>
												<td width="30%"><?php 
													$brd_query="SELECT brand_id,brand FROM brand_master where brand_id = '".$doa_row['brand_id']."' ";
													$brd_dept=mysqli_query($link1,$brd_query);
													$brd_name = mysqli_fetch_array($brd_dept);
													echo $brd_name['brand']." | ".$doa_row['brand_id'];
												?></td>
											</tr>
											<tr>
												<td width="20%"><label class="control-label">Model</label></td>
												<td width="30%">
													<?php
														$dept_query="SELECT model_id,model,wp FROM model_master where model_id = '".$doa_row['model_id']."' ";
														$check_dept=mysqli_query($link1,$dept_query);
														$model_name = mysqli_fetch_array($check_dept);
													
														echo $model_name['model']." | ".$doa_row['model_id']; 
													?>
												</td>
												
												<td width="20%"><label class="control-label">Status</label></td>
												<?php if($doa_row['repl_serial_no']!='' && $doa_row['status']=='In Process'){$status = "Closed";}else{$status = $doa_row['status'];}?>
												<td width="30%"><?php echo $doa_row['status']; ?></td>
											</tr>	

											<tr>
                                              <td><label class="control-label" >Warranty Card</label></td>
                                              <td>
											  	<span> 
													<img src="<?=$doa_row['warranty_card_path']?>" alt="Warranty Card" height="100" width="100">
												</span>
											  </td>
                                              <td><label class="control-label" >Product Image</label></td>
                                              <td>
												<span>
													<img src="<?=$doa_row['product_path']?>" alt="Product Image" height="100" width="100">
												</span>
											  </td>
                                            </tr>
											
											<tr>
												
												<td width="20%"><label class="control-label">Action</label></td>
												<td width="30%">
													<select id="status" name="status" class="form-control" required >
														<option value="">Please Select</option>
														<option value="Approved" >Approved</option>
														<option value="Rejected" >Rejected</option>
													</select>
												</td>
												<td width="20%"><label class="control-label">Remark</label></td>
												<td width="30%"><textarea id="remark" name="remark" class="form-control" required ></textarea></td>
											</tr>
											
											 
							
											<tr>
												<td width="100%" align="center" colspan="8"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list_repl_btr_dms.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'">
													<input type="submit" id="update" name="update" value="Update" class="btn<?=$btncolor?>">
												</td>

												<input type="hidden" name="replace_serial_no" id="replace_serial_no" value="<?php echo $doa_row['repl_serial_no']; ?>" />
												<input type="hidden" name="sap_mat_code" id="sap_mat_code" value="<?php echo $doa_row['sap_mat_code']; ?>" />
												<input type="hidden" name="replace_model_id" id="replace_model_id" value="<?php echo $doa_row['model_id']; ?>" />
												<input type="hidden" name="repl_serial_date" id="repl_serial_date" value="<?php echo $doa_row['repl_serial_date']; ?>" />
												<input type="hidden" name="repl_serial_time" id="repl_serial_time" value="<?php echo $doa_row['repl_serial_time']; ?>" />
												<input type="hidden" name="image_wr_card" id="image_wr_card" value="<?php echo $doa_row['warranty_card_path']; ?>" />
												<input type="hidden" name="image_prd" id="image_prd" value="<?php echo $doa_row['product_path']; ?>" />
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
		<?php
		include("../includes/footer.php");
		include("../includes/connection_close.php");
		?>
	</body>
</html>