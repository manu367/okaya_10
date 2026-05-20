<?php

require_once('../includes/config.php');

////get access product details

$access_product = getAccessProduct($_SESSION['asc_code'],$link1);

////get access brand details

$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);

if(base64_decode($_REQUEST['productid'])!='' && base64_decode($_REQUEST['productid'])!=0){

	$sel_product = "'".base64_decode($_REQUEST['productid'])."'";

	$sel_brand = "'".base64_decode($_REQUEST['brandid'])."'";

	$blank_op = "";

}else{

	$sel_product = $access_product;

	$sel_brand = $access_brand;

	$blank_op = "<option value=''>--Select Product--</option>";

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

   <!-- Include Date Picker -->



 <link rel="stylesheet" href="../css/datepicker.css">



 <script src="../js/bootstrap-datepicker.js"></script>



 <style type="text/css">

	.modal-bodyTH{

		max-height: calc(100vh - 212px);

		overflow-y: auto;

	}

</style>

  <script>

	$(document).ready(function(){

        $("#frm1").validate();

    });

 </script>

 <script language="javascript" type="text/javascript">

 $(document).ready(function() {

	 /////// if user enter imei or serial no. then contact no. field should be disabled

	 $("#imei_serial").keyup(function() {

		if($("#imei_serial").val()!=""){ 

        	$("#contact_no").attr("disabled",true);

			$("#Submit").attr("disabled",false);

		}else{

			$("#contact_no").attr("disabled",false);

			$("#Submit").attr("disabled",true);

		}

    });

    /////// if user enter contact no. then imei or serial no. field should be disabled

	 $("#contact_no").keyup(function() {

		 if($("#contact_no").val()!=""){ 

        	$("#imei_serial").attr("disabled",true);

			$("#Submit").attr("disabled",false);

		 }else{

			 $("#imei_serial").attr("disabled",false);

			 $("#Submit").attr("disabled",true);

		 }

    });

 });

 ////// function for open model to see the job details

function viewJobDetails(jobno){

	$.get('job_view_only.php?refid=' + jobno, function(html){

		 $('#viewJob .modal-body').html(html);

		 $('#viewJob').modal({

			show: true,

			backdrop:"static"

		});

	 });

}

/*/////////// function to get model on the basis of brand

  $(document).ready(function(){

	$('#brand').change(function(){

	  var brandid=$('#brand').val();

	  $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{jobcreatebrand:brandid},

		success:function(data){

	    $('#modeldiv').html(data);

	    }

	  });

    });

  });*/

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



///// check function if imei or serial not found in import data then we have to check in model master that check_serimei flag Y/N

function chk_serimei(val){

	//alert(val);

	var mod_val=document.getElementById("modelid").value;

	var splitval = mod_val.split("~");

	//var today=Date();

	var today = document.getElementById("todaydt").value;

	var purchase_dt=document.getElementById("pop_date").value;

	var doa_days=splitval[6];

	var repl_days=splitval[8];

	var warr_days=splitval[9];

	var days_diff=date_difference(today,purchase_dt);



	//alert(splitval[0]+" "+splitval[2]+" "+splitval[3]+" "+splitval[4]+" "+splitval[5]+" "+splitval[6]+" "+splitval[7]+" "+splitval[8]+" "+splitval[9]+" "+days_diff);

	if(splitval[2] == "Y" || days_diff>warr_days){

		document.getElementById("job_warr").value="OUT";

	}else{

		document.getElementById("job_warr").value="IN";

	}

	document.getElementById("p_dop").value=purchase_dt;

	if(val=="N" || val==""){

		if(splitval[3] == "Y"){

		if(days_diff<=warr_days){///// check if make job flag is Y

			document.getElementById("makejob").style.display="";

			document.getElementById("errmsg3").innerHTML = "";

		}else{

			document.getElementById("errmsg3").innerHTML = "This model is not eligible to make Repair job.";

			document.getElementById("makejob").style.display="none";

		}

		}

		if(splitval[5] == "Y"){

		if(days_diff<=doa_days && splitval[2] == "N"){

			document.getElementById("makedoa").style.display="";

			document.getElementById("errmsg1").innerHTML = "";

		}else{

			document.getElementById("errmsg1").innerHTML = "This model is not eligible to make DOA job.";

			document.getElementById("makedoa").style.display="none";

		}}

		if(splitval[7] == "Y" ){

		if(days_diff<=repl_days && splitval[2] == "N"){

			document.getElementById("makerepl").style.display="";

			document.getElementById("errmsg2").innerHTML = "";

		}else{

			document.getElementById("errmsg2").innerHTML = "This model is not eligible to make Replacement job.";

			document.getElementById("makerepl").style.display="none";

		}

		}

		

	}else{

		document.getElementById("errmsg").innerHTML = "IMEI/Serial no. not found in database. To make job please contact to HO co-ordinator.";

		document.getElementById("dispmakejob"+val).style.display="none";

	}

}

 </script>

 <script>



	$(document).ready(function(){



        $("#frm1").validate();



    });



	<?php if($_REQUEST['p_dop']!='' && $_REQUEST['p_dop']!='0000-00-00'){?>



    $(document).ready(function () {



	  $('#pop_date').attr('readonly', true);



	});



	<?php }else{?>



	$(document).ready(function () {



		$('#pop_date').datepicker({



			format: "yyyy-mm-dd",



			endDate: "<?=$today?>",



			todayHighlight: true,



			autoclose: true,



		}).on('changeDate', function(ev){



    		//checkJobType();



			//getWarranty();



		})



	});



	<?php }?>



 </script>

 <script type="text/javascript" src="../js/jquery.validate.js"></script>

</head>

<body>

<div class="container-fluid">

  <div class="row content">

	<?php 

    include("../includes/leftnavemp2.php");

    ?>

    <div class="<?=$screenwidth?>">

      <h2 align="center"><i class="fa fa-plus"></i> New Job</h2><br/><br/>

      

      	<div class="form-group"  id="page-wrap" style="margin-left:10px;">

			<form id="frm1" name="frm1" class="form-horizontal" action="" method="post">

             <div class="form-group">

                <div class="col-md-10"><label class="col-md-4 control-label">Product <span class="red_small">*</span></label>

                  <div class="col-md-6">

                  	<select name="product_name" id="product_name" class="form-control required" required>

                      <?=$blank_op?>

                      <?php

                        $dept_query="SELECT * FROM product_master where status = '1' and product_id in (".$sel_product.") order by product_name";

                        $check_dept=mysqli_query($link1,$dept_query);

                        while($br_dept = mysqli_fetch_array($check_dept)){

                      ?>

                      <option value="<?=$br_dept['product_id']?>"<?php if($_REQUEST['product_name'] == $br_dept['product_id']){ echo "selected";}?>><?php echo $br_dept['product_name']?></option>

                    <?php }?>	

                    </select>

                  </div>

                </div>

              </div>

              <div class="form-group">

                <div class="col-md-10"><label class="col-md-4 control-label">Brand <span class="red_small">*</span></label>

                  <div class="col-md-6">

                  	<select name="brand" id="brand" class="form-control required" required>

                      <?=$blank_op?>

                      <?php

                        $dept_query="SELECT * FROM brand_master where status = '1' and brand_id in (".$sel_brand.") order by brand";

                        $check_dept=mysqli_query($link1,$dept_query);

                        while($br_dept = mysqli_fetch_array($check_dept)){

                      ?>

                      <option value="<?=$br_dept['brand_id']?>"<?php if($_REQUEST['brand'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>

                    <?php }?>	

                    </select>

                  </div>

                </div>

              </div>

             <div class="form-group">

                <div class="col-md-10"><label class="col-md-4 control-label">Enter IMEI/Serial No.<span class="red_small">*</span></label>

                  <div class="col-md-6">

                     <input type="text" name="imei_serial" class="form-control required" maxlength="15" required id="imei_serial" value="<?=$_REQUEST['imei_serial']?>" placeholder="Enter only IMEI/Serial No."/>

                  </div>

                </div>

              </div>

             <!-- <div class="form-group">

                <div class="col-md-10"><label class="col-md-4 control-label">OR</label>

                  <div class="col-md-6">

                    

                  </div>

                </div>

              </div>-->

              <!--<div class="form-group">

                <div class="col-md-10"><label class="col-md-4 control-label">Contact No.</label>

                  <div class="col-md-6">

                     <input type="text" name="contact_no" class="digits form-control" id="contact_no" value="<?=$_REQUEST['contact_no']?>" placeholder="Enter only Contact No."/>

                  </div>

                </div>

              </div>-->

               <div class="form-group">

                <div class="col-md-10"><label class="col-md-4 control-label"></label>

                  <div class="col-md-6">

                     <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="Submit" value="Search" title="Search" disabled>

                  </div>

                </div>

              </div>

          	</form>

            <?php if($_POST['Submit']=="Search" && ($_POST['imei_serial']!='' || $_POST['contact_no']!='') ){

										

			?>

            <div class="panel panel-info">

              <div class="panel-heading" align="center"><?php if($_SESSION['id_type']!='ASP' && $_SESSION['id_type']!='L3'){?>IMEI/Serial No. History<?php }?></div>

              <div class="panel-body">

			  	<?php 

					if($_POST['imei_serial']){ echo "Your searched criteria <strong>IMEI/Serial No. :- </strong>".$_POST['imei_serial'];}

				if($_POST['contact_no']){ echo "Your searched criteria <strong>Contact No. :- </strong>".$_POST['contact_no'];}

					///// check in jobsheet data

					//echo getJobValidate($_POST['imei_serial'],$_POST['contact_no'],$link1);

					//$jd_result = explode("~",getJobValidate($_POST['imei_serial'],$_POST['contact_no'],$link1));

					

					$post_dop = "";

					$post_activation = "";

					$post_importdate = "";

					$post_refurbdate = "";

					$post_modelcode = "";

					$post_model = "";

					$post_imei1 = "";

					$post_imei2 = "";

					

					list($flag,$msg,$jobno_arr,$customer_arr,$opendate_arr,$closedate_arr,$modelid_arr,$model_arr,$status_arr,$dop_arr,$activdate_arr,$wsd_arr,$firstimei_arr,$secimei_arr) = getJobValidate($_POST['imei_serial'],$_POST['contact_no'],$_POST['brand'],$link1);

					/////if data found in JD

					if($flag=="Y" || $flag=="R" ){

						/////check if the makeJob flag should be Y for this model

						$is_makejob = explode("~",getAnyDetails($modelid_arr[0],"make_job,status,out_warranty,replacement,replace_days,wp","model_id","model_master",$link1));

						/*if($is_makejob[1] != 1){

							echo "<br/><span class='red_small'>This model is not eligible to make job.</span><br/>";

						}*/

						if($is_makejob[0] == "Y" && $is_makejob[1] == 1){?>

                	<table class="table table-bordered" width="100%">

                    	<?php if($_SESSION['id_type']!='ASP' && $_SESSION['id_type']!='L3'){?>

                        <thead>

                        	<tr>

                            	<td><strong>S.No.</strong></td>

                                <td><strong>Job No.</strong></td>

                                <td><strong>Customer Name</strong></td>

                                <td><strong>Open Date</strong></td>

                                <td><strong>Close Date</strong></td>

                                <td><strong>Model</strong></td>

                                <td><strong>Job Status</strong></td>

                                <td><strong>View</strong></td>

                            </tr>

                        </thead><?php }?>

                        <tbody>

                        <?php

						$post_dop = $dop_arr[0];

						$post_activation = $activdate_arr[0];

						$post_modelcode = $modelid_arr[0];

						$post_model = $model_arr[0];

						$post_imei1 = $firstimei_arr[0];

						$post_imei2 = $secimei_arr[0];

						$arrstatus = getJobStatus($link1);

						$warr= daysDifference($today,$dop_arr[0]);

						if($warr>$is_makejob[5] || $is_makejob[2]=='Y' ){

							$job_warr_st='OUT';

						}else{

							$job_warr_st='IN';

						}

						if($_SESSION['id_type']!='ASP' && $_SESSION['id_type']!='L3'){

						for($j=0; $j<count($jobno_arr); $j++){

						?>

                        	<tr>

                            	<td><?=$j+1;?></td>

                                <td><?=$jobno_arr[$j]?></td>

                                <td><?=$customer_arr[$j]?></td>

                                <td><?=dt_format($opendate_arr[$j])?></td>

                                <td><?=dt_format($closedate_arr[$j])?></td>

                                <td><?=$model_arr[$j]?></td>

                                <td><?=$arrstatus[$status_arr[$j]][$status_arr[$j]]?></td>

                                <td><div align="center"><a href='#' title='view job details' onClick='viewJobDetails("<?=base64_encode($jobno_arr[$j])?>");'><i class='fa fa-eye fa-lg faicon' title='view job details'></i></a></div></td>

                            </tr>

						<?php

						}}

                        ?>

                        </tbody>

                    </table>

                    <div align="center">

                    <form id="frm4" name="frm4" class="form-horizontal" action="job_make.php" method="post">

                    <input name="imei_serial" id="imei_serial" type="hidden" value="<?=$post_imei1?>"/>

                     <input name="imei_serial2" id="imei_serial2" type="hidden" value="<?=$post_imei2?>"/>

                    <input name="contact_no" id="contact_no" type="hidden" value="<?=$_POST['contact_no']?>"/>

                    <input name="p_dop" id="p_dop" type="hidden" value="<?=$post_dop?>"/>

                    <input name="p_activation" id="p_activation" type="hidden" value="<?=$post_activation?>"/>

                    <input name="p_modelcode" id="p_modelcode" type="hidden" value="<?=$post_modelcode?>"/>

                    <input name="p_model" id="p_model" type="hidden" value="<?=$post_model?>"/>

                     <input name="job_warr" id="job_warr" type="hidden" value="<?=$job_warr_st?>"/>

                    <input name="p_wsd" id="p_wsd" type="hidden" value="<?=$wsd_arr[0]?>"/>

                    <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>

                    <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>

                    <input name="ticket_no" id="ticket_no" type="hidden" value="<?=$_REQUEST['ticket_no']?>"/>
				  <div class="form-group">	  <div class="col-md-10"><label class="col-md-4 control-label">Purchase / Bill Date <span class="red_small">*</span></label>
					  <?php if($post_dop!='0000-00-00' && $post_dop !=''){?>
                           
							 <div class="col-md-6" ><div style="display:inline-block;float:left;"><?=$post_dop;?><input type="hidden" class="form-control required" name="pop_date"  id="pop_date" style="width:150px;" value="<?=$post_dop ;?>" readonly ></div></div>
							 <?php }else{ ?><div class="col-md-6" ><div style="display:inline-block;float:left;"><input type="text" class="form-control required" name="pop_date"  id="pop_date" style="width:150px;" value="0000-00-00" onChange="chk_serimei('');"></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>
                          </div> <?php } ?></div> </div>

                    <input title="Make Repair Job" type="submit" id="makejob" name="makejob" class="btn<?=$btncolor?>" value="Make Repair Job">
<?php if ($flag !="R"){?>
                    <input title="Make DOA Job" type="submit" id="makedoa" name="makedoa"  style="display:none"  class="btn<?=$btncolor?>" value="Make DOA Job">
					<?php } else {
						echo "<br/><span class='red_small'>This IMEI is not eligible to make DOA job.</span><br/>";
					}

                  if(($warr>$is_makejob[4] || $is_makejob[3]!='Y') && $flag!="R" ){?><input title="Make Replacement Job" type="submit" id="makerepl" name="makerepl" class="btn<?=$btncolor?>" value="Make Replacement Job"><?php }
					else if ($flag=="R"){
					echo "<br/><span class='red_small'>This IMEI is not eligible to make Replacement job.</span><br/>";
					}
					else{echo "<br/><span class='red_small'>This model is not eligible to make Replacement job.</span><br/>";}?>

                    </form>

                    </div>

				<?php

						}else{

							echo "<br/><span class='red_small'>This model is not eligible to make job $flag $modelid_arr[0] .</span><br/>";	

						}

					}else if($flag=="NF"){///// check in activation data and import data

						$import_result = explode("~",getImeiImportValidate($_POST['imei_serial'],$link1));

						///// if imei/serial no. found in import data then carry on

						if($import_result[0]=="Y"){

							/////check if the makeJob flag should be Y for this model

							$is_makejob = explode("~",getAnyDetails($import_result[4],"make_job,status,out_warranty,replacement,replace_days,wp","model_id","model_master",$link1));

							if($is_makejob[0] == "Y" && $is_makejob[1] == 1){

							$post_dop = "";

							$post_importdate = $import_result[1];

							$post_activation = $import_result[2];

							$post_modelcode = $import_result[4];

							$post_imei1 = $import_result[5];

 							$post_imei2 = $import_result[6];

							///// check if product or brand access is matched with searched model

							///// so first we have to find product id and brand id of the searched model

							$model_detail = explode("~",getAnyDetails($post_modelcode,"model_id,model,out_warranty,make_job,chk_serimei,make_doa,doa_days,replacement,replace_days,wp,product_id,brand_id","model_id","model_master",$link1));

							/////make product id variable and brand id variable to search in both access strings 

							$find_product = "'".$model_detail[10]."'";

							$find_brand = "'".$model_detail[11]."'";

							///// check in access string

							$pos_product = strpos($access_product, $find_product);

							$pos_brand = strpos($access_brand, $find_brand);

							if($pos_product === false || $pos_brand === false){

								echo "<br/><span class='red_small'>You are not authorized for this model.</span><br/>";	

							}else{

							?>

                            <div align="center">

                            <form id="frm3" name="frm3" class="form-horizontal" action="job_make.php" method="post" onMouseOver="chk_serimei('');">

                            <div class="form-group">

                        <div class="col-md-10"><label class="col-md-4 control-label">Purchase / Bill Date <span class="red_small">*</span></label>

                         <input name="modelid" id="modelid" type="hidden" value="<?=$model_detail[0]."~".$model_detail[1]."~".$model_detail[2]."~".$model_detail[3]."~".$model_detail[4]."~".$model_detail[5]."~".$model_detail[6]."~".$model_detail[7]."~".$model_detail[8]."~".$model_detail[9]?>"/>
							
                            <?php if($post_activation!='0000-00-00' && $post_activation!=''){?>
                           
							 <div class="col-md-6" ><div style="display:inline-block;float:left;"><?=$post_activation;?><input type="hidden" class="form-control required" name="pop_date"  id="pop_date" style="width:150px;" value="<?=$post_activation;?>" readonly ></div></div>
							 <?php }else{ ?><div class="col-md-6" ><div style="display:inline-block;float:left;"><input type="text" class="form-control required" name="pop_date"  id="pop_date" style="width:150px;" value="" onChange="chk_serimei('');"></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>
                          </div> <?php } ?>
                        </div>

                      </div>

                       <div class="form-group">

                        <div class="col-md-10"><label class="col-md-4 control-label"></label>

                          <div class="col-md-6" id="dispmakejobY">

                          <input name="todaydt" id="todaydt" type="hidden" value="<?=$today?>"/>

                            <input name="imei_serial" id="imei_serial" type="hidden" value="<?=$post_imei1?>"/>

                     		<input name="imei_serial2" id="imei_serial2" type="hidden" value="<?=$post_imei2?>"/>

                            <input name="contact_no" id="contact_no" type="hidden" value="<?=$_POST['contact_no']?>"/>

                            <input name="p_activation" id="p_activation" type="hidden" value="<?=$post_activation?>"/>

                            <input name="p_modelcode" id="p_modelcode" type="hidden" value="<?=$post_modelcode?>"/>

                            <input name="p_model" id="p_model" type="hidden" value="<?=$post_model?>"/>

                            <input name="p_wsd" id="p_wsd" type="hidden" value="<?=$model_detail[9]?>"/>

                            <input name="p_dop" id="p_dop" type="hidden" value=""/>

                             <input name="job_warr" id="job_warr" type="hidden" value=""/>

                            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>

               		        <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>

                            <input name="ticket_no" id="ticket_no" type="hidden" value="<?=$_REQUEST['ticket_no']?>"/>

                            <input title="Make Repair Job" type="submit" id="makejob" name="makejob" style="display:none" class="btn<?=$btncolor?>" value="Make Repair Job">

                    		<input title="Make DOA Job" type="submit" id="makedoa" name="makedoa" style="display:none" class="btn<?=$btncolor?>" value="Make DOA Job">

                   			<input title="Make Replacement Job" type="submit" id="makerepl" name="makerepl" style="display:none" class="btn<?=$btncolor?>" value="Make Replacement Job">

                            </div>

                         </div>

                      </div>

                      <span id="errmsg" class="red_small"></span>

                      <span id="errmsg1" class="red_small"></span>

                      <span id="errmsg2" class="red_small"></span>

                      <span id="errmsg3" class="red_small"></span>

                            </form>

                            </div>	

							<?php 

							}

							}else{

								echo "<br/><span class='red_small'>This model is not eligible to make job.</span><br/>";

							}

						}else{

							echo "<br/><span class='red_small'>".$import_result[1]."</span><br/>";

				?>	

                   <form id="frm2" name="frm2" class="form-horizontal" action="job_make.php" method="post">		

                      <div class="form-group">

                        <div class="col-md-10"><label class="col-md-4 control-label">Model <span class="red_small">*</span></label>

                          <div class="col-md-6" id="modeldiv">

                             <select  name='modelid' id='modelid' class='form-control required' required >

                             	<option value=''>--Please Select--</option>

								 <?php 

                                 $model_query="SELECT * FROM model_master where brand_id='".$_POST['brand']."' and chk_serimei!='Y' and status=1 order by model";

                                 $model_res=mysqli_query($link1,$model_query);

                                 while($row_model = mysqli_fetch_array($model_res)){

                                 ?>

           						<option value="<?=$row_model['model_id']."~".$row_model['model']."~".$row_model['out_warranty']."~".$row_model['make_job']."~".$row_model['chk_serimei']."~".$row_model['make_doa']."~".$row_model['doa_days']."~".$row_model['replacement']."~".$row_model['replace_days']."~".$row_model['wp']?>"><?=$row_model['model']?></option>

	 							<?php }?>

                            </select>

                          </div>

                        </div>

                      </div>

                       <div class="form-group">

                        <div class="col-md-10"><label class="col-md-4 control-label">Purchase / Bill Date <span class="red_small">*</span></label>

                          <div class="col-md-6" ><div style="display:inline-block;float:left;"><input type="text" class="form-control required" name="pop_date"  id="pop_date" style="width:150px;" value="" onchange='chk_serimei("N");'></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>

                          </div>

                        </div>

                      </div>

                      <div class="form-group">

                        <div class="col-md-10"><label class="col-md-4 control-label"></label>

                          <div class="col-md-6" id="dispmakejobN">

                  			<input title="Make Repair Job" type="submit" id="makejob" name="makejob" style="display:none" class="btn<?=$btncolor?>" value="Make Repair Job">

                    <input title="Make DOA Job" type="submit" id="makedoa" name="makedoa" style="display:none" class="btn<?=$btncolor?>" value="Make DOA Job">

                    <input title="Make Replacement Job" type="submit" id="makerepl" style="display:none" name="makerepl" class="btn<?=$btncolor?>" value="Make Replacement Job"> <input name="todaydt" id="todaydt" type="hidden" value="<?=$today?>"/>

                            <input name="imei_serial" id="imei_serial" type="hidden" value="<?=$_POST['imei_serial']?>"/>

                            <input name="p_dop" id="p_dop" type="hidden" value=""/>

                            <input name="contact_no" id="contact_no" type="hidden" value="<?=$_POST['contact_no']?>"/>

                             <input name="job_warr" id="job_warr" type="hidden" value=""/>

                            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>

               		        <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>

                            <input name="ticket_no" id="ticket_no" type="hidden" value="<?=$_REQUEST['ticket_no']?>"/>

                          </div>

                        </div>

                      </div>

                      <span id="errmsg" class="red_small"></span>

                      <span id="errmsg1" class="red_small"></span>

                      <span id="errmsg2" class="red_small"></span>

                      <span id="errmsg3" class="red_small"></span>

                   </form>   	

				<?php		}

					}else{///// proceed anyway

						echo "<br/><span class='red_small'>".$msg."</span>";

					}

			  	?>

                  <!-- Start Model Mapped Modal -->

                  <div class="modal modalTH fade" id="viewJob" role="dialog">

                    <div class="modal-dialog modal-lg">

                    

                      <!-- Modal content-->

                      <div class="modal-content">

                        <div class="modal-header">

                          <button type="button" class="close" data-dismiss="modal">&times;</button>

                          <!--<h4 class="modal-title" align="center">Job Details</h4>-->

                        </div>

                        <div class="modal-body modal-bodyTH">

                         <!-- here dynamic task details will show -->

                        </div>

                        <div class="modal-footer">

                          <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>

                        </div>

                      </div>

                    </div>

                  </div>

                  <!--close Model Mapped modal-->

              </div>

            </div>

            <?php } ?>

		</div>

	</div>

  </div>

</div>

</body>

</html>

<?php

include("../includes/footer.php");

include("../includes/connection_close.php");

?>