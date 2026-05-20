<?php

require_once('../includes/config.php');



?>
<?php /*?>
<iframe src="http://sms.rvsolutions.in/smsapi/api/notification/sms?ApiKey=fc2a71e0-04ae-4488-8c1d-90c85d336d8f&apisecret=PathOnSite&number=<?=$_REQUEST['mobileno']?>&message=<?=base64_decode($_REQUEST['smsmsg'])?>"  width="1" height="1" scrolling="No" style="background:#00FF33"></iframe>
<?php */?>

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



 <script type="text/javascript" src="../js/jquery.validate.js"></script>

</head>

<body>

<div class="container-fluid">

  <div class="row content">

	<?php 

    include("../includes/leftnavemp2.php");

    ?>

    <div class="<?=$screenwidth?>">

      <h2 align="center"><i class="fa fa-plus"></i> Complaint Logged </h2>
      <br/><br/>
           <div class="panel panel-info">

              <div class="panel-heading" align="center">Finished</div>

              <div class="panel-body">
                  	<table class="table table-bordered" width="50%">

                    	

                        <thead>

                        	<tr>

                            	<td colspan="2" align="center"><strong><?php 
								if($_REQUEST['chkflag']=='success'){
						 echo $msg1= base64_decode($_REQUEST['msg'])."<br>";
								echo "Do You Want To Make Another Complaint ";
								}else{
							echo  $msg1= base64_decode($_REQUEST['msg']);
								
								}
								//echo $_REQUEST['mobileno'];
									//echo $_REQUEST['smsmsg'];
								?> </strong> 
															
</td>

                                
                            </tr>

                        </thead>

                        <tbody>
	
                         
                      
                        	<tr>

                            	<td align="right"> <?php if($_REQUEST['chkflag']=='success'){?>  <input title="Yes" type="button" class="btn<?=$btncolor?>" value="Yes" onClick="window.location.href='complaint_make.php?<?=$pagenav?>&mobileno=<?=$_REQUEST['mobileno']?>&customer_id=<?=$_REQUEST['customer_id']?>&email_id=<?=$_REQUEST['email_id']?>'"><?php }?></td>

                                <td>
								<?php if($_REQUEST['chkflag']=='success'){
								if($_SESSION['id_type']=='CC'){?>
								<input title="No" type="button" class="btn<?=$btncolor?>" value="No" onClick="window.location.href='complaint_list.php?<?=$pagenav?>&chkflag=<?=$_REQUEST['chkflag']?>&chkmsg=<?=$_REQUEST['chkmsg']?>&imei_serial=<?=$_REQUEST['imei_serial']?>'">
								<?php } else{?>
								<input title="No" type="button" class="btn<?=$btncolor?>" value="No" onClick="window.location.href='job_list.php?<?=$pagenav?>&msg=<?=$msg1?>&chkflag=<?=$_REQUEST['chkflag']?>&chkmsg=<?=$_REQUEST['chkmsg']?>&imei_serial=<?=$_REQUEST['imei_serial']?>'">
								 <?php } }else {?>  <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='complaint_create.php?<?=$pagenav?>&mobileno=<?=$_REQUEST['mobileno']?>&customer_id=<?=$_REQUEST['customer_id']?>&email_id=<?=$_REQUEST['email_id']?>'"> <?php }?></td>

                              
								
								
								

                         
								 

                            </tr>

					
                        </tbody>

                    </table>
	
                 

			

                  <!-- Start Model Mapped Modal -->

                

                  <!--close Model Mapped modal-->

              </div>

            </div>


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