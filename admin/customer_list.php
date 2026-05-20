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

    /////// if user enter contact no. then imei or serial no. field should be disabled

	 $("#mobileno").keyup(function() {

		 if($("#mobileno").val()!=""){ 
            $("#Submit").attr("disabled",false);

		 }else{
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
   include("../includes/leftnav2.php");
    ?>

    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-plus"></i>Customer Search</h2>
      <br/><br/>
<?php if($_REQUEST['chkflag']!='' && $_REQUEST['msg']==''){?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;customer information No Change.
        </div>
        <?php }?>
        
      <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php }?>
        
         <!--<button title="Black List Customer" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='blacklistcust.php?op=Add<?=$pagenav?>'"><span>Black List Customer</span></button>&nbsp;&nbsp;
		 <span><button title="Send SMS " type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='sendSmsToCustomer.php?<?=$pagenav?>'"><i class="fa fa-mobile"><strong> Send SMS</strong></i> </button></span></div>
       <div style="display:table-cell;float:right">&nbsp;&nbsp;&nbsp;</div><div style="display:table-cell;float:right">
   <span><button title="Add Customer" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='sendMailToCustomer.php?<?=$pagenav?>'"><i class="fa fa-envelope-o"><strong> Send Mail</strong></i> </button></span></div><div style="display:table-cell;float:right">&nbsp;&nbsp;&nbsp;</div><div style="display:table-cell;float:right">
        <span><button title="Add Customer" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addNewCustomer.php?<?=$pagenav?>'"><i class="fa fa-user-plus"><strong> Add Customer</strong></i> </button></span>-->
        <!--<button title="Black List Customer" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='blacklistcust.php?op=Add<?=$pagenav?>'"><span>Black List Customer</span></button>&nbsp;&nbsp;-->

      	<div class="form-group"  id="page-wrap" style="margin-left:10px;">

			<form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
			  <div class="form-group">

                <div class="col-md-10"><label class="col-md-4 control-label">Mobile Number</label>

                  <div class="col-md-6">

                    <input type="text" name="mobileno" class="digits  form-control"  id="mobileno" minlength="10" maxlength="10" value="<?=$_REQUEST['mobileno']?>" placeholder="Enter Mobile Number"/>

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

            <?php 
			if($_REQUEST['mobileno']){
				$srch_criteria = "mobile = '".$_REQUEST['mobileno']."' ";
				}
				else{
				$srch_criteria="1";
				}							
?>

			  <div class="panel panel-info">
              <div class="panel-heading" align="center"> Customer Details</div>

              <div class="panel-body">
			 <table class="table table-bordered" width="100%">
                        <thead>

                        	<tr>

                            	<td><strong>S.No.</strong></td>
                                <td><strong> Customer ID</strong></td>
				                 <td><strong>Name</strong></td>
								 <td><strong>Address</strong></td>
                                <td><strong>State</strong></td>
                                 <td><strong>City</strong></td>
								 <td><strong>Email</strong></td>
								 <td><strong>Mobile No.</strong></td>
                                 <td><strong>Edit</strong></td>
								<td><strong>Email Send</strong></td>
                            </tr>

                        </thead>

                        <tbody>
		 			<?php 
					

					$sql_cust	= mysqli_query($link1, "select  *  from customer_master  where ".$srch_criteria."  order by id desc");
                         $post_customerid="";
					if($row_num=mysqli_num_rows($sql_cust) > 0){
                       $row_customer=mysqli_fetch_array($sql_cust);

						?> <tr> 
                            	<td><?=$k+1;?></td>

                                <td><?=$row_customer['customer_id']?></td>

                                <td><?=$row_customer['customer_name']?></td>

                                <td><?=$row_customer['address1']?></td>

                                 <td><?php echo getAnyDetails($row_customer["stateid"],"state","stateid","state_master",$link1);?></td>
								<td><?php echo getAnyDetails($row_customer["cityid"],"city","cityid","city_master",$link1);?></td>
                                <td><?=$row_customer['email']?></td>							
								<td><?=$row_customer['mobile']?></td>
                                <td>   <div align="center"><a href="customer_edit_admin.php?refid=<?=base64_encode($row_customer['customer_id'])?>."".$pagenav." title='view'><i class='fa fa-eye fa-lg faicon' title='view job details'></i></a></div></td>
							 <td>   <div align="center"><a href="sendMailToCustomer.php?refid=<?=base64_encode($row_customer['customer_id'])?>."".$pagenav." title='view'><i class='fa fa-eye fa-lg faicon' title='view job details'></i></a></div></td>
                            </tr>

						<?php
					}
                               $post_customerid=$row_customer['customer_id'];
						   

                        ?>

                        </tbody>
                    </table> 			  
			       </div>
			   </div>
			 

            <?php //} ?>

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