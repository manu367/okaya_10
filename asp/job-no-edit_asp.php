<?php
require_once("../includes/config.php");
////// final submit form ////
$docid=base64_decode($_REQUEST['refid']);
//// job details
$job_sql="SELECT * FROM jobsheet_data where job_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$ticket_det=mysqli_fetch_assoc($job_res);
$model_det = explode("~",getAnyDetails($ticket_det['model_id'],"out_warranty","model_id","model_master",$link1));
if($_POST['savejob']=='Save'){

	@extract($_POST);

	//// initialize transaction parameters

	$flag = true;

	mysqli_autocommit($link1, false);

	$error_msg="";

	/////update job sheet data

  $sql_inst = "Update jobsheet_data set warranty_status='".$warranty_status."', dop='".$pop_date."',  customer_name='".$customer_name."',  contact_no='".$phone1."', alternate_no='".$phone2."', email='".$email."', address='".$address."', els_status ='".$els_status."',  remark='".$remark."', ip='".$ip."' where job_no='".$docid."'";
	$res_inst = mysqli_query($link1,$sql_inst);

	//// check if query is not executed

	if (!$res_inst) {

		 $flag = false;

		 $error_msg = "Error details2: " . mysqli_error($link1) . ".";

	}

	///// entry in call/job  history

	$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB","EDIT",$_SERVER['REMOTE_ADDR'],$link1,$flag);

	///// check both master and data query are successfully executed

	if ($flag) {

		mysqli_commit($link1);

		////// return message

		$msg="You have successfully Updated Job details";

		$cflag="success";

		$cmsg="Success";

	} else {

		mysqli_rollback($link1);

		$cflag="danger";

		$cmsg="Failed";

		$msg = "Request could not be processed. Please try again. ".$error_msg;

	} 

	mysqli_close($link1);

   ///// move to parent page
  
   header("location:job_list_edit_asp.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav."&smsmsg=".$sms_msg."&to=".$phone1."&status=1");
	exit;

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

 <script>

	$(document).ready(function(){

        $("#frm1").validate();

    });

	$(document).ready(function () {

		$('#pop_date').datepicker({

			format: "yyyy-mm-dd",

			endDate: "<?=$today?>",

			todayHighlight: true,

			autoclose: true,

		}).on('changeDate', function(ev){


			getWarranty();

		})

	});

	

 </script>

 <script language="javascript" type="text/javascript">

/////////////

/// check warrantty on the basis of els status and pop selection

function getWarranty(){

	var sel_pop = $('#pop_date').val();

	var sel_elstatus = $('#els_status').val();

	var post_wsd = "<?=$ticket_det['warranty_days']?>";

	////// check out warranty flag of this model

	if("<?=$model_det[0]?>" == "Y"){

		document.getElementById("warranty_status").value = "OUT";

	}else{

		/////calculate days

		var diffDays = date_difference("<?=$today?>", sel_pop);

		///// calculate warranty

		if(diffDays <= post_wsd){

			document.getElementById("warranty_status").value = "IN";

			if(sel_elstatus != "OK"){

				document.getElementById("warranty_status").value = "VOID";

			}

		}else{

			document.getElementById("warranty_status").value = "OUT";

		}

	}

}

//// date difference

function date_difference(enddate,startdate){

	var end_date = (enddate).split("-");

	var start_date = (startdate).split("-");	

	var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds

	var firstDate = new Date(start_date[0], start_date[1], start_date[2]);

	var secondDate = new Date(end_date[0], end_date[1], end_date[2]);

	/////calculate days

	var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime()) / (oneDay)));

	return diffDays;

}

  </script>

 <script type="text/javascript" src="../js/jquery.validate.js"></script>

 <script type="text/javascript" src="../js/common_js.js"></script>

  <!-- Include Date Picker -->

 <link rel="stylesheet" href="../css/datepicker.css">

 <script src="../js/bootstrap-datepicker.js"></script>


 <style type="text/css">

 .custom_label {

	 text-align:left;

	 vertical-align:middle

 }

 </style>

<body>

<div class="container-fluid">

  <div class="row content">

	<?php 

    include("../includes/leftnav2.php");

    ?>

    <div class="<?=$screenwidth?>">

      <h2 align="center"><i class="fa fa-id-badge"></i> Edit Job Details<br><font color="#FF0000" size="+2"> <?=$docid?></font></h2>

    	<form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">

        <div class="panel-group">

            <div class="panel panel-info">

              <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>

              <div class="panel-body">

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Customer Name <span class="red_small">*</span></label>

                      <div class="col-md-6">

                      	<input name="customer_name" id="customer_name" type="text" value="<?=$ticket_det['customer_name'];?>" class="form-control required"/>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Address <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <textarea name="address" id="address" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"><?=$ticket_det['address'];?></textarea>

                      </div>

                    </div>

                  </div>

                   <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Contact No. <!--<span class="small">(For SMS Update)</span>--> <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <input name="phone1" type="text" class="digits required form-control" required id="phone1" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" value="<?php if($ticket_det['contact_no']!=''){ echo $ticket_det['contact_no'];}else{ echo $_REQUEST['contact_no'];}?>">

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Alternate Contact No.</label>

                      <div class="col-md-6">

                      <input name="phone2" type="text" class="digits form-control" id="phone2" maxlength="10" value="<?=$ticket_det['alternate_no'];?>">

                      </div>

                    </div>

                  </div>

                  <div class="form-group">

                     <div class="col-md-6"><label class="col-md-6 custom_label">Email</label>

                      <div class="col-md-6">

                          <input name="email" type="email" class="email form-control" id="email" value="<?=$ticket_det['email'];?>">

                      </div>

                    </div>
                     <div class="col-md-6"><label class="col-md-6 custom_label"></label>

                      <div class="col-md-6">

                          
                      </div>

                    </div>

                  </div>

                
              </div>

            </div>

        

            <div class="panel panel-info">

              <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Product Details</div>

              <div class="panel-body">
              	<div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Model <span class="red_small">*</span></label>

                      <div class="col-md-6" id="modeldiv">

                         <input name="model" type="text" class="form-control required" readonly id="model" value="<?=$ticket_det['model'];?>">
						 
                        

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label"></label>

                      <div class="col-md-6" id="accdiv">

                        
                      </div>

                    </div>

                  </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">IMEI 1/Serial No. 1 <span class="red_small">*</span></label>

                      <div class="col-md-6">

                      	<input name="imei" id="imei" type="text" value="<?=$ticket_det['imei']?>" class="form-control required" readonly/>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">IMEI 2/Serial No. 2</label>

                      <div class="col-md-6">

                       <input name="sec_imei" id="sec_imei" type="text" value="<?=$ticket_det['sec_imei']?>" <?php if($ticket_det['sec_imei']!=''){?> readonly <?php }else{}?> class="form-control"/>

                      </div>

                    </div>

                  </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Job Type <span class="red_small">*</span></label>

                      <div class="col-md-6">
						<input name="call_type" id="call_type" type="text" value="<?=$ticket_det['call_type']?>" class="form-control required" readonly/>
                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Job For <span class="red_small">*</span></label>

                      <div class="col-md-6">
						<input name="call_for" id="call_for" type="text" value="<?=$ticket_det['call_for']?>" class="form-control required" readonly/>
                        
                      </div>

                    </div>

                  </div>

                 <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Purchase Date</label>

                      <div class="col-md-6">

                        <div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="pop_date"  id="pop_date" style="width:150px;" required value="<?php if($ticket_det['dop']!='' && $ticket_det['dop']!='0000-00-00'){ echo $ticket_det['dop'];}else{ echo $today;}?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>

                  		</div>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Activation Date</label>

                      <div class="col-md-6">

                       <input name="activation_date" id="activation_date" type="text" value="<?=$ticket_det['activation']?>" class="form-control" readonly/>

                      </div>

                    </div>

                  </div> 

              </div>

            </div>

            <div class="panel panel-info">

              <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Observation</div>

              <div class="panel-body">

                <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">ELS Status <span class="red_small">*</span></label>

                      <div class="col-md-6">

                      	 <select name="els_status" id="els_status" class="form-control required" required onChange="getWarranty();">

                          <option value=''>--Select ELS--</option>	

                          <option value='OK' <?php if($ticket_det['els_status']=='OK'){echo "selected";}?>>OK</option>

                          <option value="Physical damaged" <?php if($ticket_det['els_status']=='Physical damaged'){echo "selected";}?>>Physical damaged</option>

                          <option value="Tempered" <?php if($ticket_det['els_status']=='Tempered'){echo "selected";}?>>Tempered</option>

                          <option value="Liquid damaged" <?php if($ticket_det['els_status']=='Liquid damaged'){echo "selected";}?>>Liquid damaged</option>

                          <option value="Electrical malfunctioning" <?php if($ticket_det['els_status']=='Electrical malfunctioning'){echo "selected";}?>>Electrical malfunctioning</option>

                        </select>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Warranty Status <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <input name="warranty_status" id="warranty_status" type="text" value="<?=$ticket_det['warranty_status']?>" class="form-control required" />

                      </div>

                    </div>

                  </div>

                    <div class="form-group">

                    <div class="col-md-12" align="center">

                      <span id="errmsg" class="red_small"></span>

                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list_edit_asp.php?<?=$pagenav?>'">&nbsp;

                      <input type="submit" class="btn<?=$btncolor?>" name="savejob" id="savejob" value="Save" title="Save Job Details" <?php if($_POST['savejob']=='Save'){?>disabled<?php }?>>&nbsp;

                    </div>

                  </div> 

              </div>

            </div><!-- end panal-->

        </div><!-- end panal group-->

        </form>

    </div><!--End col-sm-9-->

  </div><!--End row content-->

</div><!--End container fluid-->

<?php

include("../includes/footer.php");

include("../includes/connection_close.php");

?>

</body>

</html>