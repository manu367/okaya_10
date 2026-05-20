<?php
require_once("../includes/config.php");
//require_once("../FCM/PHP7/firebase.cm.php");
//$arrstatus = getFullStatus("process",$link1);
$docid=base64_decode($_REQUEST['refid']);
//print_r($_REQUEST['pid']);exit;
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
		

		/////update jobsheet data  table
		if($partner_type=="Distributor"){
		$typ = "1";
	}else if($partner_type=="Direct dealer"){
		$typ = "2";
	}else if($partner_type=="Retailer"){
		$typ = "3";
	}else{
		$typ = "";
	}
			
			$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set partner_id = '".$partner_sap."', partner_type = '".$typ."' where job_no='".$docid."' ");
			/// check if query is execute or not//
			if(!$jobsheet_upd){
				$flag = false;
				$err_msg = "Error1". mysqli_error($link1) . ".";
			}	
		
		if($job_row['repl_appr_no']!=''){
		        $req = '';
				$req = '{
					"partner_code":"'.$partner_sap.'",
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
					"replacement_approval_date": "'.$job_row['repl_appr_date'].'",
					"tag_no": "'.$job_row['tag_no'].'",
					"replacement_approval_no": "'.$job_row['repl_appr_no'].'",
					"status": "Partner Update",
					"product_id": "'.$job_row['product_id'].'",
					"model_id": "'.$job_row['model_id'].'"
				}';
			
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
			    //print_r($array);exit;
				$sr_array = array();
				$status_code = $array['statusCode'];
				$status_message = $array['statusMessage'];
				////////////// type='L1-upd'

				$res_store = mysqli_query($link1,"INSERT INTO repl_api_json_data SET job_no='".$docid."', req_by='".$_SESSION["userid"]."', ip='".$_SERVER['REMOTE_ADDR']."', type='L1-upd', response='".$response."', request='".$req."' ");

				if($status_code!="200"){
					$flag = false;
					$err_msg = "Error Found ".$status_message."";
				}
			
			
			
			
		
		
		}
		
		
		
		
		
            $oldpartnerid = $job_row['partner_id'];
			$flag = callHistory($docid,$job_row['location_code'],$status,"partner change","partner change (old partner id-$oldpartnerid and new partner id-$partner_sap)",$_SESSION['userid'],"",$remark,"","",$ip,$link1,$flag);
		
		

		///////////////////////// entry in call history table ///////////////////////////////////////	

		////////////////////////////////////////////////////////////////////////////////////////////////////////////
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'], $docid, $status,$remark,$_SERVER['REMOTE_ADDR'], $link1, $flag);

		

		

		///// send SMS through curl
		

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
		//header("location:job_list_repl_btr-goodwill.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		if($_REQUEST['pid']=='450'){
		header("location:job_list_repl_btr_sr.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit;
		}else{
		header("location:job_list_repl_btr_goodwill.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit;
		}
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
		<link href="../css/loader.css" rel="stylesheet"/>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	</head>
	<style>
	
	</style>

	<script>

		$(document).ready(function(){
			//$("#frm1").validate();
			var spinner = $('#loader');
			/*$("#frm1").validate({
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
			});*/
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
		
$(document).ready(function() {
$("#partner_state").select2({
});
});
function getlocation(val){
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{partner_state:val},
		success:function(data){
      
	    $('#partner_loc1').html(data);
			$("#partner_loc").select2({});
	   }
	  });
}
function getpartner(val){
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{partner_loc:val},
		success:function(data){
	
	    $('#partner_sap1').html(data);
				$("#partner_sap").select2({
});
	   }
	  });
}
		
function getpartnertype(val){
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{partner_sap:val},
		success:function(data){
	//alert(data);
	    document.getElementById("partner_type").value=data;
	
	   }
	  });
}
	</script>

	<script type="text/javascript" src="../js/jquery.validate.js"></script>
<!-- Include multiselect -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
	<body onKeyPress="return keyPressed(event);">

		<div class="container-fluid">
			<div class="row content">
				<?php 
	include("../includes/leftnav2.php");
				?>
				<div class="<?=$screenwidth?>">
					<h2 align="center"><i class="fa fa-list-alt"></i> Change Partner Detail </h2>
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
											<td><label class="control-label"><!---Invoice No----></label></td>
											<td><?php /*$job_row['inv_no']*/ ?></td>
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
										<td><label class="control-label">Actual Aging</label></td>
										<td><?php 
										$aging_data = $job_row['ext7'];;
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
										<!----
										<tr>
										<td><label class="control-label">Complaint Attend Name</label></td>
										<td><?= $job_row['partner_met_name'] ?></td>
										<td><label class="control-label">Complaint Attend No</label></td>
										<td><?= $job_row['comp_attend_detail'] ?></td>
										</tr>---->
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
													<img src="<?=$row_image['img_url']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100">
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
													<img src="<?=$row_image['img_url1']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100">
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
													<img src="<?=$row_image['img_url2']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100">
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
													<img src="<?=$row_image['img_url3']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100">
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
													<img src="<?=$row_image['img_url4']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100">
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
														<img src="<?=$row_image['img_url6']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100">
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
						<?php //if($job_row['status'] == '81' && $job_row['sub_status'] == '81'){ ?>
						<form id="frm1" name="frm1" method="post" >
							<div class="panel panel-info table-responsive">
								<div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Partner SAP Change Action</div>
								<div class="panel-body">
									<table class="table table-bordered" width="100%">
										<tbody>
											
											<tr>
												<td width="20%"><label class="control-label">Partner State</label><span class="red_small">*</span></td>
												<td width="30%">
													<select id="partner_state" name="partner_state" class="form-control select2 required" onchange="getlocation(this.value);"  required>
														<option value="" >--Please select Partner State--</option>
														<?php
	
											$partner_state_query="SELECT state,stateid FROM state_master where state!='' group by state order by state ";
	//print_r($_REQUEST['partner_sap']);exit;
															$check_partner_state=mysqli_query($link1,$partner_state_query);
															while($partner_state = mysqli_fetch_array($check_partner_state)){
																
											?>
														
														<option value="<?=$partner_state['stateid']?>" <?php if($_REQUEST['partner_state']==$partner_state['stateid']){echo "selected";}else{echo "";}?>><?=$partner_state['state']?></option>
														<?php }  ?>
														
													</select>
												</td>
												
												<td width="20%"><label class="control-label">Location</label><span class="red_small">*</span></td>
												<td width="30%" id="partner_loc1">
													<select id="partner_loc" name="partner_loc" class="form-control select2 required" required>
														<option value="" >--Please select Location--</option>
														
													</select>
												</td>
												
												
											</tr>
											
											<tr>
												
												<td width="20%"><label class="control-label">Partner SAP</label><span class="red_small">*</span></td>
												<td width="30%" id='partner_sap1'>
													<select id="partner_sap" name="partner_sap" class="form-control select2 required" required>
														<option value="" >--Please select Partner--</option>
													
	
														
													</select>
												</td>
												<td width="20%"><label class="control-label">Partner Type</label><span class="red_small">*</span></td>
												
												
												<td width="30%"><input type="text" name="partner_type" id="partner_type" class="form-control required" value="" required readonly/></td>
																
							
												
											</tr>	
											<tr>
												
													<td width="20%"><label class="control-label">Remark</label><span class="red_small">*</span></td>
												<td width="30%"><textarea id="remark" name="remark" class="form-control" required></textarea></td>
												<td width="20%"><label class="control-label"></label></td>
												<td width="30%">
													<!--<select id="status" name="status" class="form-control" required onchange="check_reject_reason(this.value);" >
														<option value="">Please Select</option>
														<option value="82" >Approved</option>
														<option value="83" >Rejected</option>
														<option value="85" >Same Back-Battery found okay on back up test</option>
													</select>-->
												</td>
											</tr> 

											<!--<tr>
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
											</tr> -->

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
							<?php if($_REQUEST['pid']=='450'){ ?>
											<tr>
												<td width="100%" align="center" colspan="8"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list_repl_btr_sr.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'"> <input type="submit" id="update" name="update" value="Update" class="btn<?=$btncolor?>"></td>
											</tr>  
											<?php }else{ ?>
											<tr>
												<td width="100%" align="center" colspan="8"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list_repl_btr_goodwill.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'"> <input type="submit" id="update" name="update" value="Update" class="btn<?=$btncolor?>"></td>
											</tr>
											<?php } ?>
											     
										</tbody>
									</table>
								</div><!--close panel body-->
							</div><!--close panel-->
						</form>
						<?php //} ?>
						
					</div><!--close panel group-->
				</div><!--close col-sm-9-->
			</div><!--close row content-->
		</div><!--close container-fluid-->
		<div id="loader"></div>
		<?php
		include("../includes/footer.php");
		include("../includes/connection_close.php");
		?>
	</body>
</html>