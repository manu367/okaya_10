<?php
require_once("../includes/config.php");
@extract($_POST);
////// if we hit process button
if ($_POST['save'] == 'Save') {
	mysqli_autocommit($link1, false);
    $flag = true;
    $err_msg = "";

				///// Insert into location_Acount ledger table
                $query12 = "INSERT INTO location_account_ledger set location_code='".$_POST['location']."', transaction_type='A/C Adjustment',crdr='".$_POST['adjust_type']."',amount='".$_POST['amount']."',entry_date='".$today."',remark='".$_POST['remark']."' ,transaction_no= 'Adjustment'";			
              $result2 = mysqli_query($link1, $query12);
                    //// check if query is not executed
                    if (!$result2) {
                        $flag = false;
                        $err_msg = "Error Code1:";
                    }
					
			////////////// update current_cr_status table /////////////////////////////////////
			if($_POST['adjust_type'] == 'CR') {
			 $adjustment  = " total_credit_limit = total_credit_limit + '".$_POST['amount']."' ";
			 $adjustcredit =  "credit_bal = credit_bal +  '".$_POST['amount']."' ";			
			}
			else if($_POST['adjust_type'] == 'DR') {
			 $adjustment  = " total_credit_limit = total_credit_limit -  '".$_POST['amount']."' ";
			 $adjustcredit =  "credit_bal = credit_bal -  '".$_POST['amount']."' ";		
			} else {$adjustment = "";}
			
		   	$query2 = "update current_cr_status set $adjustment , $adjustcredit,last_updated = '".$today."' where location_code = '".$_POST['location']."'  ";			
                $result1 = mysqli_query($link1, $query2);
                    //// check if query is not executed
                    if (!$result1) {
                        $flag = false;
                        $err_msg = "Error Code2:";
                    }
		
		    ////// insert in activity table////
                $flag = dailyActivity($_SESSION['userid'], $_POST['location'], "A/C Adjust",$_POST['adjust_type'], $ip, $link1, $flag);
		  
		
		  ///// check  query are successfully executed
                    if($flag) {
                        mysqli_commit($link1);
                        $msg = "Successfully done !";
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
    		header("location:ac_adjustment.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
    		exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= siteTitle ?></title>
<script src="../js/jquery.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<script type="text/javascript">
    $(document).ready(function() {
        $("#form1").validate();
    });
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>

 
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
   include("../includes/leftnavemp2.php");
    ?>
   <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-users"></i> Account Adjustment</h2>
     <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php }?>
	  <br></br>
	  <form class="form-horizontal" id="form1" name="form1" action="" method="post">  
	    <div class="form-group">
         <div id= "dt_range" class="col-md-6"><label class="col-md-5 control-label">Location<span style="color:#F00">*</span></label>	  
			<div class="col-md-6 input-append date" align="left">
			 <select   name="location" id="location"  class="form-control required"   onChange="document.form1.submit();"  required>
			 <option value="" <?php if($_REQUEST['location']=="") { echo 'selected'; } ?>>Please Select</option>
				<?php 
                $loc = mysqli_query($link1,"select location_code, locationname from location_master  where statusid= '1' order by locationname " ); 
                while($locinfo = mysqli_fetch_assoc($loc)){ 
				?>		
             <option value="<?=$locinfo['location_code']?>" <?php if($_REQUEST['location']==$locinfo['location_code']) { echo 'selected'; } ?>><?=$locinfo['locationname']?></option>
                <?php }?>
	</select>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Adjustment Type<span style="color:#F00">*</span></label>	  
			<div class="col-md-5" align="left">
			<select   name="adjust_type" id="adjust_type"  class="form-control required"   required>
			 <option value="" <?php if($_REQUEST['location']=="") { echo 'selected'; } ?>>Please Select</option>
			  <option value="CR" <?php if($_REQUEST['location']=="CR") { echo 'selected'; } ?>>CR</option>
			   <option value="DR" <?php if($_REQUEST['location']=="DR") { echo 'selected'; } ?>>DR</option>
	</select>
            </div>
          </div>
	    </div><!--close form group-->
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Adjustment Amt<span style="color:#F00">*</span></label>	  
			<div class="col-md-6" >
	<input type="text" id="amount" name="amount" class="form-control required" required >
				
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Remark <span style="color:#F00">*</span></label>	  
			<div class="col-md-5">
                  <textarea id="remark" name="remark"  cols="5" rows="4" class="form-control required" required ></textarea>
              </div>
          </div>
	    </div>
        
        		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Current Amount<span style="color:#F00">*</span></label>	  
			<div class="col-md-6" >
            <?php $cr_limit = getAnyDetails($_REQUEST['location'],"total_credit_limit","location_code","current_cr_status",$link1);?>
	<input type="text" id="cr_amount" name="cr_amount" class="form-control required" required value="<?=$cr_limit?>"  readonly>
				
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5">
                 
              </div>
          </div>
	    </div>
	
        <div class="form-group">
         <div class="col-md-12" align="center">
               <input type="submit" class="btn<?=$btncolor?>" name="save" id="save" value="Save" title="Save">
                            
            </div>
         
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5">
               
            </div>
          </div>
		  
	    </div><!--close form group-->
        
	  </form>
	</div> 
     </div> 
    </div>    

<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>