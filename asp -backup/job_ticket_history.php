<?php
require_once("../includes/config.php");
$sql = mysqli_fetch_array(mysqli_query($link1,"select * from ticket_master where contact_no = '".$_REQUEST['contact']."' "));
////// final submit form ////
if($_POST){
@extract($_POST);
	if($_POST['saveticket']=='Save'){	
		$flag = true;
		/////////////////////  entry in call history table ///////////////////////////
		ticketHistory($_POST['ticketno'],$remark,$today,$_SERVER['REMOTE_ADDR'],$proiority,$link1,"");
		
		mysqli_query($link1,"update ticket_master set  status='$status' where ticket_no='".$_POST['ticketno']."'");
			
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$_POST['ticketno'],"Ticket","Priority",$_SERVER['REMOTE_ADDR'],$link1,"");
	///// check both master and data query are successfully executed
	if ($flag) {
		$msg = "Sucessfully set Priority of ticket no.".$_POST['ticketno'];
		$cflag="success";
		$cmsg="Success";
	} else {
		$cflag="danger";
		$cmsg="Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
	
   ///// move to parent page
 header("location:job_ticket_create.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
 exit;
}
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
      <h2 align="center"><i class="fa fa-ticket"></i> Details of Ticket</h2>
		<form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
        <div class="panel-group">
            <div class="panel panel-info">
              <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>
              <div class="panel-body">
              	  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">Customer Type <span class="red_small">*</span></label>
                      <div class="col-md-6">
                      <?php echo $sql['customer_type']; ?>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">Ticket Type</label>
                      <div class="col-md-6"><?php echo $sql['ticket_type']; ?>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">Customer Name <span class="red_small">*</span></label>
                      <div class="col-md-6">
                      	<input name="customer_name" id="customer_name" type="text" value="<?=$sql['customer_name']?>" class="form-control required"/>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">Address <span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <textarea name="address" id="address" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical" readonly ><?=$sql['address'] ?></textarea>
                      </div>
                    </div>
                  </div>
                   <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">Contact No. <span class="small">(For SMS Update)</span> <span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <input name="phone1" type="text" class="digits required form-control" required id="phone1" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" value="<?=$sql['contact_no']?>" readonly>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">Alternate Contact No.</label>
                      <div class="col-md-6">
                      <input name="phone2" type="text" class="digits form-control" id="phone2" maxlength="10" value="<?=$sql['alternate_no']?>" readonly>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">State <span class="red_small">*</span></label>
                      <div class="col-md-6">
                         <select name="locationstate" id="locationstate" class="form-control required"   required>      
						   <option value=<?=$sql['state_id'];?>><?=getAnyDetails($sql['state_id'],"state","stateid","state_master",$link1);?></option>      
                        </select>               
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">Email</label>
                      <div class="col-md-6">
                          <input name="email" type="email" class="email form-control" id="email" value="<?=$sql['email'] ?>" readonly>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">City <span class="red_small">*</span></label>
                        <div class="col-md-6" id="citydiv">
                       <select name="locationcity" id="locationcity" class="form-control required" required>
					 <option value=<?=$sql['city_id'];?>><?=getAnyDetails($sql['city_id'],"city","cityid","city_master",$link1);?></option>                 
                       </select>
                      </div>
                    </div>
                   <div class="col-md-6"><label class="col-md-6 custom_label">Pincode</label>
                      <div class="col-md-6">
                        <input name="pincode" type="text" class="digits form-control" id="pincode" value="<?=$sql['pincode']?>" readonly>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
        
            <div class="panel panel-info">
              <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Product Details</div>
              <div class="panel-body">
              	<div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">Product <span class="red_small">*</span></label>
                      <div class="col-md-6">
                         <select name="product_name" id="product_name" class="form-control required" required>
                          <option value=<?=$sql['product_id'];?>><?=getAnyDetails($sql['product_id'],"product_name","product_id","product_master",$link1);?></option> 
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">Brand</label>
                      <div class="col-md-6">
                       <select name="brand" id="brand" class="form-control required"   required>
                         <option value=<?=$sql['brand_id'];?>><?=getAnyDetails($sql['brand_id'],"brand","brand_id","brand_master",$link1)?></option>   
                        </select>
                      </div>
                    </div>
                  </div>
              	<div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">Model <span class="red_small">*</span></label>
                      <div class="col-md-6" id="modeldiv">
                        <select name="model" id="model" class="form-control required"  required >						
						  <option value=<?=$sql['model_id'];?>><?=$sql['model']?></option>                  
                        </select>
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
              <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Observation</div>
              <div class="panel-body">      
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">VOC <span class="red_small">*</span></label>
                      <div class="col-md-6" id="vocdiv">
                      <select name="voc1" id="voc1" class="form-control required"  required >
					<option value=<?=$sql['cust_problem'];?>><?=$sql['cust_problem']?></option>                       
                        </select>       
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">Status <span class="red_small">*</span></label>
                    	<div class="col-md-6">
                            	<select name="status" id="status" class="required form-control">
                          <option value='open' selected>Open</option>
                          <option value='Closed'>Closed</option>
                         
                        </select>
                        </div>
                      	<div class="col-md-6">
                        
                      	</div>
                    </div>
                  </div>
				  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">Remark <span class="red_small">*</span></label>
                      <div class="col-md-6" >
                      <textarea name="remark" id="remark" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:none"></textarea> 
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">Priority<span class="red_small">*</span></label>
                    	<div class="col-md-6">
                           <select name="proiority" id="proiority" class="form-control required"  required >
                          <option value=''>--Select Proiority--</option>
						   <option value='1'>Low</option>
						    <option value='2'>Normal</option>
							 <option value='3'>High</option>
                        </select>        
                        </div>
                      	<div class="col-md-6">
                        
                      	</div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-12" align="center">
                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_ticket_create.php?<?=$pagenav?>'">&nbsp;
					  <input type="hidden" id="ticketno" name="ticketno" value ="<?=$sql['ticket_no']?>" >
                      <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               		  <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
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