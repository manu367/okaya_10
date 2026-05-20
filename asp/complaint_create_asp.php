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

        	$("#mobileno").attr("disabled",true);
			$("#customer_id").attr("disabled",true);
				$("#email_id").attr("disabled",true);

			$("#Submit").attr("disabled",false);

		}else{

				$("#mobileno").attr("disabled",true);
			$("#customer_id").attr("disabled",true);
			$("#email_id").attr("disabled",true);

			$("#Submit").attr("disabled",true);

		}

    });

    /////// if user enter contact no. then imei or serial no. field should be disabled

	 $("#mobileno").keyup(function() {

		 if($("#mobileno").val()!=""){ 

        	$("#imei_serial").attr("disabled",true);
			$("#customer_id").attr("disabled",true);
			$("#email_id").attr("disabled",true);
            $("#Submit").attr("disabled",false);

		 }else{
             
	       	$("#imei_serial").attr("disabled",true);
			$("#customer_id").attr("disabled",true);
            $("#email_id").attr("disabled",true);
			 $("#Submit").attr("disabled",true);

		 }

    });
	   /////// if user enter customer id. then other  field should be disabled
	 $("#customer_id").keyup(function() {

		if($("#customer_id").val()!=""){ 

        	$("#mobileno").attr("disabled",true);
			$("#imei_serial").attr("disabled",true);
             $("#email_id").attr("disabled",true);
			$("#Submit").attr("disabled",false);

		}else{

				$("#mobileno").attr("disabled",true);
			$("#imei_serial").attr("disabled",true);
               $("#email_id").attr("disabled",true);
			$("#Submit").attr("disabled",true);

		}

    });
		   /////// if user enter email id. then other  field should be disabled
	 $("#email_id").keyup(function() {

		if($("#email_id").val()!=""){ 

        	$("#mobileno").attr("disabled",true);
			$("#imei_serial").attr("disabled",true);
             $("#customer_id").attr("disabled",true);
			$("#Submit").attr("disabled",false);

		}else{

				$("#mobileno").attr("disabled",true);
			$("#imei_serial").attr("disabled",true);
               $("#customer_id").attr("disabled",true);
			$("#Submit").attr("disabled",true);

		}

    });

 });

 











 </script>

 <script>



	$(document).ready(function(){



        $("#frm1").validate();



    });







	



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

      <h2 align="center"><i class="fa fa-plus"></i> Customer Registration/Call Booking </h2>
      <br/><br/>

      

      	<div class="form-group"  id="page-wrap" style="margin-left:10px;">

			<form id="frm1" name="frm1" class="form-horizontal" action="" method="post"  autocomplete="off">
			  <div class="form-group">

                <div class="col-md-10"><label class="col-md-4 control-label">Mobile Number <?=$_REQUEST['msg']?></label>

                  <div class="col-md-6">

                    <input type="text" name="mobileno" class="digits  form-control"  id="mobileno" minlength="10" maxlength="10" value="<?=$_REQUEST['mobileno']?>" placeholder="Enter Mobile Number"/>

                  </div>

                </div>

              </div>
			               <div class="form-group">

                <div class="col-md-10"><label class="col-md-4 control-label">Customer ID</label>

                  <div class="col-md-6">

                  	  <input type="text" name="customer_id" class="form-control"  maxlength="30"  id="customer_id" value="<?=$_REQUEST['customer_id']?>" placeholder="Enter Customer Id"/>

                  </div>

                </div>

              </div>
			    <div class="form-group">

                <div class="col-md-10"><label class="col-md-4 control-label">Email ID</label>

                  <div class="col-md-6">

                  	  <input type="text" name="email_id" class="email form-control"  maxlength="30"  id="email_id" value="<?=$_REQUEST['email_id']?>" placeholder="Enter Email id."/>

                  </div>

                </div>

              </div>


			        

            

             <div class="form-group">

                <div class="col-md-10"><label class="col-md-4 control-label"><?php echo SERIALNO ?>.</label>

                  <div class="col-md-6">

                     <input type="text" name="imei_serial" class="form-control " maxlength="20"  id="imei_serial" value="<?=$_REQUEST['imei_serial']?>" placeholder="Enter only <?php echo SERIALNO ?>"/>

                  </div>

                </div>

              </div>

   

               <div class="form-group">

                <div class="col-md-10"><label class="col-md-4 control-label"></label>

                  <div class="col-md-6">

                     <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="Submit" value="Search" title="Search" disabled>

                  </div>

                </div>

              </div>

          	</form>

            <?php if(($_REQUEST['imei_serial']!='' || $_REQUEST['mobileno']!='' ||  $_REQUEST['customer_id']!='' ||  $_REQUEST['email_id']!='' ) ){
$cust_id=getAnyDetails($_REQUEST['imei_serial'],"customer_id","serial_no","product_registered",$link1);
			if($_REQUEST['mobileno']){
$srch_criteria = "(mobile = '".$_REQUEST['mobileno']."'  or  alt_mobile  = '".$_REQUEST['mobileno']."') ";
}else if($_REQUEST['email_id']){
$srch_criteria = "email = '".$_REQUEST['email_id']."'";
}else if($_REQUEST['customer_id']){
$srch_criteria = "customer_id = '".$_REQUEST['customer_id']."'";
}
/*else if($cust_id){
$srch_criteria = "customer_id = '".$cust_id."'";
}*/
else{
$srch_criteria = "customer_id = '".$cust_id."'";
}							
?>
		
			

			  <div class="panel panel-info">

              <div class="panel-heading" align="center"> Customer Details</div>

              <div class="panel-body">
			 <table class="table table-bordered" width="100%">

                    	

                        <thead>

                        	<tr>

                            	<td><strong>S.No.</strong></td>

                                <td><strong> ID</strong></td>
								 <td><strong> Customer Category</strong></td>

				                 <td><strong>Name</strong></td>
								 <td><strong>Address</strong></td>
                                <td><strong>State</strong></td>
                                 <td><strong>City</strong></td>
								 <td><strong>Email</strong></td>
								  <td><strong>Mobile No.</strong></td>

                                <td><strong>Residence No</strong></td>
								<td><strong>Edit</strong></td>

                            </tr>

                        </thead>

                        <tbody>
						

		
		 			<?php 
					

					$sql_cust	= mysqli_query($link1, "select  *  from customer_master  where ".$srch_criteria."    order by id desc");

$post_customerid="";
					
while($row_customer=mysqli_fetch_array($sql_cust)){

						?> <tr> 
                            	<td><?=$k+1;?></td>

                                <td><?=$row_customer['customer_id']?></td>
								   <td><?=$row_customer['type']?></td>

                                <td><?=$row_customer['customer_name']?></td>

                                <td><?=$row_customer['address1']?></td>

                                 <td><?php echo getAnyDetails($row_customer["stateid"],"state","stateid","state_master",$link1);?></td>
								<td><?php echo getAnyDetails($row_customer["cityid"],"city","cityid","city_master",$link1);?></td>

                                <td><?=$row_customer['email']?></td>
							
								 <td><?=$row_customer['mobile']?></td>
								  <td><?=$row_customer['phone']?></td>

  <td><div align="center"><a href="customer_edit.php?customer_id=<?=$row_customer['customer_id']?>"".$pagenav." title='view'><i class='fa fa-eye fa-lg faicon' title='view job details'></i></a></div></td
                            ></tr>

						<?php
$post_customerid=$row_customer['customer_id'];
						}

                        ?>

                        </tbody>

                    </table> 
			  
			  </div>
			   </div>
			  
			
			
			     <div class="panel panel-info">

              <div class="panel-heading" align="center">Registered Product Details 
			  </div>

              <div class="panel-body">
			  
			    	<table class="table table-bordered" width="100%">

                    	

                        <thead>

                        	<tr>

                            	<td><strong>S.No.</strong></td>

                                <td><strong><?php echo SERIALNO ?></strong></td>

				                 <td><strong>Product</strong></td>
								 <td><strong>Model</strong></td>

                                <td><strong>Purchase Date</strong></td>

                                <td><strong>Installation Date</strong></td>

                               

                                <td><strong>Warranty End Date</strong></td>
								 <td><strong>Warranty Status</strong></td>
								 

                              <td><strong>Call Log</strong></td>
								 <!--  <td><strong>AMC</strong></td>-->

                            </tr>

                        </thead>

                        <tbody>
						

		
		 			<?php 
					

					list($flag1,$msg,$serial_arr,$product_arr,$customerid_arr,$purdate_arr,$modelid_arr,$instaledate_arr,$warratydate_arr,$id_arr) = getcustomerValidate($_POST['imei_serial'],$post_customerid,$link1);
	if($flag1=='Y'){				
for($k=0; $k<count($serial_arr); $k++){

						?> <tr> 
                            	<td><?=$k+1;?></td>

                                <td><?=$serial_arr[$k]?></td>
                              <td><?=getAnyDetails($product_arr[$k],"product_name","product_id","product_master",$link1);?></td>
                                  <td><?=getAnyDetails($modelid_arr[$k],"model","model_id","model_master",$link1);?></td>

                                <td><?=dt_format($purdate_arr[$k])?></td>
								<td><?=dt_format($instaledate_arr[$k])?></td>

                                <td><?=dt_format($warratydate_arr[$k])?></td>
								<td>
										<?php if($today<=$warratydate_arr[$k] ){

							$product_warr_st='IN';

						}else{

							$product_warr_st='OUT';

						}
						echo $product_warr_st;?></td>
<td><div align="center"><a href="complaint_make_asp.php?customer_id=<?=$customerid_arr[$k]?>&mobileno=<?=$_REQUEST['mobileno']?>&email_id=<?=$_REQUEST['email_id']?>&imei_serial=<?=$serial_arr[$k]?>&id=<?=$id_arr[$k]?>"".$pagenav." title='view'><i class='fa fa-eye fa-lg faicon' title='view job details'></i></a></div></td>
       <!--  <td><div align="center"><a href="amc_make.php?customer_id=<?=$customerid_arr[$k]?>&mobileno=<?=$_REQUEST['mobileno']?>&email_id=<?=$_REQUEST['email_id']?>&imei_serial=<?=$serial_arr[$k]?>"".$pagenav." title='view'><i class='fa fa-eye fa-lg faicon' title='view job details'></i></a></div></td>-->

                            </tr>

						<?php

						}}

                        ?>

                        </tbody>

                    </table>
			
			</div>
			</div>
			</div>

            <div class="panel panel-info">

              <div class="panel-heading" align="center">Complaint History</div>

              <div class="panel-body">

			  	<?php 



					

					$post_dop = "";

					$post_activation = "";

					$post_importdate = "";

					$post_refurbdate = "";

					$post_modelcode = "";

					$post_model = "";

					$post_imei1 = "";

					$post_imei2 = "";

					

					list($flag,$msg,$jobno_arr,$customer_arr,$opendate_arr,$closedate_arr,$modelid_arr,$model_arr,$status_arr,$dop_arr,$imei_arr,$wsd_arr) = getcomplaintValidate($_REQUEST['imei_serial'],$_REQUEST['mobileno'],$_REQUEST['customer_id'],$link1);

					/////if data found in JD

					if($flag=="Y" || $flag=="R" ){

						/////check if the makeJob flag should be Y for this model

						$is_makejob = explode("~",getAnyDetails($modelid_arr[0],"make_job,status,out_warranty,replacement,replace_days,wp,product_id","model_id","model_master",$link1));

						/*if($is_makejob[1] != 1){

							echo "<br/><span class='red_small'>This model is not eligible to make job.</span><br/>";

						}*/

						?>

                	<table class="table table-bordered" width="100%">

                    	

                        <thead>

                        	<tr>

                            	<td><strong>S.No.</strong></td>

                                <td><strong>Job No.</strong></td>

                              
								 <td><strong>Product</strong></td>
								  <td><strong>Model</strong></td>
                                   <td><strong><?php echo SERIALNO ?></strong></td>
                                <td><strong>Open Date</strong></td>

                                <td><strong>Close Date</strong></td>

                              

                                <td><strong>Status</strong></td>
								 

                                <td><strong>View</strong></td>
								
                            </tr>

                        </thead>

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

				

						for($j=0; $j<count($jobno_arr); $j++){
$is_makejob_cret = explode("~",getAnyDetails($modelid_arr[$j],"make_job,status,out_warranty,replacement,replace_days,wp,product_id","model_id","model_master",$link1));
						?>

                        	<tr>

                            	<td><?=$j+1;?></td>

                                <td><?=$jobno_arr[$j]?></td>

                              
								
								 <td><?=getAnyDetails($is_makejob_cret[6],"product_name","product_id","product_master",$link1);?></td>
								 <td><?=$model_arr[$j]?></td>
								  <td><?=$imei_arr[$j]?></td>

                                <td><?=dt_format($opendate_arr[$j])?></td>

                                <td><?=dt_format($closedate_arr[$j])?></td>

                                

                                <td><?=$arrstatus[$status_arr[$j]][$status_arr[$j]]?></td>
								

                                <td><div align="center"><a href="complaint_view_only.php?refid=<?=base64_encode($jobno_arr[$j])?>."".$pagenav." title='view'><i class='fa fa-eye fa-lg faicon' title='view job details'></i></a></div></td>
								 

                            </tr>

						<?php

						}

                        ?>

                        </tbody>

                    </table>
	<?php

						}else{

							echo "<br/><span class='red_small'>No Record found</span><br/>";	
 
						}

					
			  	?>
                    <div align="center">

                    <form id="frm4" name="frm4" class="form-horizontal" action="" method="">

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
					
					  <input name="customer_id" id="customer_id" type="hidden" value="<?=$_REQUEST['customer_id']?>"/>

                    <input name="email_id" id="email_id" type="hidden" value="<?=$_REQUEST['email_id']?>"/>
					
					 <input name="mobileno" id="mobileno" type="hidden" value="<?=$_REQUEST['mobileno']?>"/>

                    <input name="ticket_no" id="ticket_no" type="hidden" value="<?=$_REQUEST['ticket_no']?>"/>
				

                    <input title="Make complaint with serial" type="button" id="makecomplaint" name="makecomplaint" class="btn<?=$btncolor?>" value="Make Complaint/Installation with serial"  onClick="window.location.href='complaint_make_test.php?<?=$pagenav?>&mobileno=<?=$_REQUEST['mobileno']?>&customer_id=<?=$_REQUEST['customer_id']?>&email_id=<?=$_REQUEST['email_id']?>&imei_serial=<?=$_REQUEST['imei_serial']?>'">
						
					<input title="Make complaint" type="button" id="makecomplaint" name="makecomplaint" class="btn<?=$btncolor?>" value="Make Complaint/Installation"  onClick="window.location.href='complaint_make_asp.php?<?=$pagenav?>&mobileno=<?=$_REQUEST['mobileno']?>&customer_id=<?=$_REQUEST['customer_id']?>&email_id=<?=$_REQUEST['email_id']?>&imei_serial=<?=$_REQUEST['imei_serial']?>'">

                 <!--   <input title="Make AMC" type="button" id="amc" name="amc"   class="btn<?=$btncolor?>" onClick="window.location.href='amc_make.php?<?=$pagenav?>&mobileno=<?=$_REQUEST['mobileno']?>&customer_id=<?=$_REQUEST['customer_id']?>&email_id=<?=$_REQUEST['email_id']?>&imei_serial=<?=$_REQUEST['imei_serial']?>'" value="Make AMC">-->
					
					
					<!--  <input title="Customer/Product registration" type="button" id="Customer" name="Customer"   class="btn<?=$btncolor?>" onClick="window.location.href='customer_add.php?<?=$pagenav?>&mobileno=<?=$_REQUEST['mobileno']?>&customer_id=<?=$_REQUEST['customer_id']?>&email_id=<?=$_REQUEST['email_id']?>&imei_serial=<?=$_REQUEST['imei_serial']?>'" value="Customer/Product  Registration">-->
					  
					<!--    <input title="Accessory Sale" type="button" id="Accessory" name="Accessory"   class="btn<?=$btncolor?>" onClick="window.location.href='acc_make.php?<?=$pagenav?>&mobileno=<?=$_REQUEST['mobileno']?>&customer_id=<?=$_REQUEST['customer_id']?>&email_id=<?=$_REQUEST['email_id']?>&imei_serial=<?=$_REQUEST['imei_serial']?>'" value="Accessory Sale">-->


                    </form>

                    </div>

			

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