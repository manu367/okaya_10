<?php
require_once("../includes/config.php");
////// final submit form ////

if($_POST){
@extract($_POST);
	if($_POST['saveticket']=='Save'){
		//// initialize transaction parameters
		$flag = true;
		mysqli_autocommit($link1, false);
		$error_msg="";
		//// pick max count of ticket
		$res_jobcount = mysqli_query($link1,"SELECT ticket_count from job_counter where location_code='".$_SESSION['asc_code']."'");
		$row_jobcount = mysqli_fetch_assoc($res_jobcount);
		///// make ticket sequence
		$nextticketno = $row_jobcount['ticket_count'] + 1;
		$ticketno = $_SESSION['asc_code']."T".str_pad($nextticketno,4,0,STR_PAD_LEFT);
		//// first update the TICKET count
		$res_upd = mysqli_query($link1,"UPDATE job_counter set ticket_count='".$nextticketno."' where location_code='".$_SESSION['asc_code']."'");
		//// check if query is not executed
		if (!$res_upd) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		} 
		/////// entry in ticket master table//////////////////////////////////////////
		$modelname  = getAnyDetails($model,"model","model_id","model_master",$link1);		
		
	  $sql_inst = "INSERT INTO ticket_master set ticket_no='".$ticketno."', location_code='".$_SESSION['asc_code']."', city_id='".$locationcity."', state_id='".$locationstate."', pincode='".$pincode."', product_id='".$product_name."', brand_id='".$brand."', customer_type='".$customer_type."', model_id='".$model."', model='".$modelname."', sno='', open_date='".$today."', open_time='".$currtime."', dname='".$dealer_name."', customer_name='".$customer_name."',  contact_no='".$phone1."', alternate_no='".$phone2."', email='".$email."', address='".$address."', cust_problem='".$voc1."',created_by='".$_SESSION['userid']."', remark='".$remark."',ticket_type='".$ticket_type."'";

    	$res_inst = mysqli_query($link1,$sql_inst);
		//// check if query is not executed
		ticketHistory($ticketno,'Ticket Created',$today,$_SERVER['REMOTE_ADDR'],$proiority,$link1,"");
		if (!$res_inst) {
			 $flag = false;
			 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
		}
		/////////////////////  entry in call history table ///////////////////////////
		$flag = ticketHistory($ticketno,$remark,$datetime,$_SERVER['REMOTE_ADDR'],"" ,$link1,$flag);
			
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$ticketno,"Ticket","CREATE",$_SERVER['REMOTE_ADDR'],$link1,$flag);
		///// check both master and data query are successfully executed
		if ($flag) {
			mysqli_commit($link1);
			$msg="You have successfully created a ticket like ".$ticketno;
			$cflag="success";
			$cmsg="Success";
		} else {
			mysqli_rollback($link1);
			$cflag="danger";
			$cmsg="Failed";
			$msg = "Request could not be processed. Please try again. ".$error_msg;
		} 
		mysqli_close($link2);
		///// move to parent page
		header("location:job_ticket_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit;
	}
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////


############################################ Symptom Code #######################################################
$rs_symp=mysqli_query($link1,"select * from voc_master where status='1' order by voc_desc ")or die("Error-> in symptom code".mysqli_error($link1));
if(mysqli_num_rows($rs_symp)>0){
	$symp_arr[][]=array();
	$j=0;
	while($row_symp=mysqli_fetch_array($rs_symp)){
		$symp_arr[$j][0]=$row_symp['voc_code'];
		$symp_arr[$j][1]=$row_symp['voc_desc'];
		$j++;
	}
}else{}
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
 </script>
 <script language="javascript" type="text/javascript">
 /////////// function to get city on the basis of state
 function get_citydiv(){
	  var name=$('#locationstate').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{state:name},
		success:function(data){
	    $('#citydiv').html(data);
	    }
	  });
   
 }
 
   //////////////////////// function to get model on basis of model dropdown selection///////////////////////////
 function getmodel(){
	  var brand=$('#brand').val();
	  var product=$('#product_name').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{brandinfo:brand,productinfo:product},
		success:function(data){
		 $('#modeldiv').html(data);
	    }
	  });
  }
  
//////////////////////// function to get voc on basis of product and brand dropdown selection///////////////////////////

function getvoc(){
	  var brand=$('#brand').val();
	  var product=$('#product_name').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{getbrand:brand,getproduct:product},
		success:function(data){
		 $('#vocdiv').html(data);
	    }
	  });
  }



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
  </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
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
    include("../includes/leftnavemp2.php");
    ?>
     <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-ticket"></i> Enter Details for Ticket</h2>
		<form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
        <div class="panel-group">
            <div class="panel panel-info">
              <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>
              <div class="panel-body">
              	  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">Customer Type <span class="red_small">*</span></label>
                      <div class="col-md-6">
                      	<select name="customer_type" id="customer_type" class="required form-control">
                          <option value='Customer' selected>Customer</option>
                          <option value='Dealer'>Dealer</option>
                          <option value='Distributor'>Distributor</option>	
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">Ticket Type</label>
                      <div class="col-md-6">
                      	<select name="ticket_type" id="ticket_type" class="required form-control">
                          <option value='Customer General Query' selected>Customer General Query</option>
                          <option value='Customer Escalations'>Customer Escalations </option>
                          <option value='Customer Satisfaction Survey'>Customer Satisfaction Survey</option>	
                          <option value='Trade Satisfaction Survey'>Trade Satisfaction Survey</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">Customer Name <span class="red_small">*</span></label>
                      <div class="col-md-6">
                      	<input name="customer_name" id="customer_name" type="text" value="" class="form-control required"/>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">Address <span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <textarea name="address" id="address" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"></textarea>
                      </div>
                    </div>
                  </div>
                   <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">Contact No. <span class="small">(For SMS Update)</span> <span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <input name="phone1" type="text" class="digits required form-control" required id="phone1" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" value="<?=base64_decode($_REQUEST['contact_no'])?>">
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">Alternate Contact No.</label>
                      <div class="col-md-6">
                      <input name="phone2" type="text" class="digits form-control" id="phone2" maxlength="10" value="">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">State <span class="red_small">*</span></label>
                      <div class="col-md-6">
                         <select name="locationstate" id="locationstate" class="form-control required"  onchange="get_citydiv();" required>
                          <option value=''>--Please Select--</option>
                          <?php 
						 $state_query="select stateid, state from state_master where countryid='1' order by state";
						 $state_res=mysqli_query($link1,$state_query);
						 while($row_res = mysqli_fetch_array($state_res)){?>
						   <option value="<?=$row_res['stateid']?>"<?php if($row_locdet['stateid']==$row_res['stateid']){ echo "selected";}?>><?=$row_res['state']?></option>
						 <?php }?> 	
                        </select>               
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">Email</label>
                      <div class="col-md-6">
                          <input name="email" type="email" class="email form-control" id="email" value="">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">City <span class="red_small">*</span></label>
                        <div class="col-md-6" id="citydiv">
                       <select name="locationcity" id="locationcity" class="form-control required" required>
                       <option value=''>--Please Select-</option>
                       </select>
                      </div>
                    </div>
                   <div class="col-md-6"><label class="col-md-6 custom_label">Pincode</label>
                      <div class="col-md-6">
                        <input name="pincode" type="text" class="digits form-control" id="pincode" value="">
                      </div>
                    </div>
                  </div>
              </div>
            </div>
        
            <div class="panel panel-info">
              <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Product Details</div>
              <div class="panel-body">
              	<div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">Product </label>
                      <div class="col-md-6">
                         <select name="product_name" id="product_name" class="form-control " >
                          <option value=''>--Select Product--</option>
                          <?php
							$dept_query="SELECT * FROM product_master where status = '1' order by product_name";
							$check_dept=mysqli_query($link1,$dept_query);
							while($br_dept = mysqli_fetch_array($check_dept)){
						  ?>
						  <option value="<?=$br_dept['product_id']?>"<?php if($sel_result['product_id'] == $br_dept['product_id']){ echo "selected";}?>><?php echo $br_dept['product_name']?></option>
						<?php }?>	
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">Brand</label>
                      <div class="col-md-6">
                       <select name="brand" id="brand" class="form-control required" onChange="getmodel(),getvoc();" >
                          <option value=''>--Select Brand--</option>
                          <?php
							$dept_query="SELECT * FROM brand_master where status = '1' order by brand";
							$check_dept=mysqli_query($link1,$dept_query);
							while($br_dept = mysqli_fetch_array($check_dept)){
						  ?>
						  <option value="<?=$br_dept['brand_id']?>"<?php if($sel_result['brand_id'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>
						<?php }?>	
                        </select>
                      </div>
                    </div>
                  </div>
              	<div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">Model </label>
                      <div class="col-md-6" id="modeldiv">
                        <select name="model" id="model" class="form-control "   >
                          <option value=''>--Select Model--</option>	
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label"></label>
                      <div class="col-md-6" id="accdiv">
                  
                      </div>
                    </div>
                  </div>
              </div>
            </div>     
            <div class="panel panel-info">
              <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Observation</div>
              <div class="panel-body">      
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">VOC <span class="red_small">*</span></label>
                      <div class="col-md-6" id="vocdiv">
                      <select name="voc1" id="voc1" class="form-control required"  required >
                      <option value="" selected="selected">Select VOC</option>
                            <?php  $z=0; while($z<count($symp_arr)){?>
                                    <option value="<?=$symp_arr[$z][0]?>"><?=$symp_arr[$z][1]?> (<?=$symp_arr[$z][0]?>)</option>
                                  <?php $z++;}?>
                        </select>       
                      </div>
                    </div>
                    <div class="col-md-6">
                    	<div class="col-md-6">
                            
                        </div>
                      	<div class="col-md-6">
                        
                      	</div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-12"><label class="col-md-3 custom_label">Remark <span class="red_small">*</span></label>
                      <div class="col-md-9">
                      <textarea name="remark" id="remark" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:none"></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-12" align="center">
                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_ticket_create.php?<?=$pagenav?>'">&nbsp;
                       <input type="submit" class="btn<?=$btncolor?>" name="saveticket" id="saveticket" value="Save" title="Save Ticket Details" <?php if($_POST['saveticket']=='Save'){?>disabled<?php }?>>&nbsp;
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