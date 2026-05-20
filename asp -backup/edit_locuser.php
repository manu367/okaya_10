<?php
require_once("../includes/config.php");
$getid=base64_decode($_REQUEST['id']);
////// get details of selected location////
$res_locdet=mysqli_query($link1,"SELECT * FROM locationuser_master where id='".$getid."'")or die(mysqli_error($link1));
$row_locdet=mysqli_fetch_array($res_locdet);
/////get status//
$arrstatus = getFullStatus("master",$link1);
////// final submit form ////
@extract($_POST);
if($_POST){	
/////////// initialize transcation parameter ////////
mysqli_autocommit($link1, false);
$flag = true;
if($_POST['Submit1']=='Update'){	
   // update all details of user location //
   $sql = "UPDATE locationuser_master set locusername ='".ucwords($user_name)."',  pwd ='".$password."' , emailid  ='".$emailid."' ,  	contactmo='".$contact_no."',date_of_birth='".$dob."',date_of_joining='".$doj."' , statusid='".$status."',updateby='".$_SESSION['userid']."',updatedate='".$datetime."',type='".$type."'  where id='".$getid."'";
    $result = mysqli_query($link1,$sql);
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         echo "Error details1: " . mysqli_error($link1) . ".";
    }
    //////////////////////////////////////////////////////////////
    ////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],ucwords($user_name),"LOCATION USER","UPDATE",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	////// return message
	$msg="You have successfully updated details of user ".$row_locdet['locusername'];
}
############# if form 2 is submitted #################
if($_POST['Submit2']=='Update'){
	// Update Function Rights
	$result = mysqli_query($link1,"update access_tab set status='' where userid='".$row_locdet['userloginid']."' ");
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         echo "Error details1: " . mysqli_error($link1) . ".";
    }
		$rrr="report";
		$rep1=$_REQUEST[$rrr];
		$count=count($_REQUEST[$rrr]);
		$j=0;
		while($j < $count){
			 if($rep1[$j]==''){
				$newstatus="0";
			 }else{
				$newstatus="1";
			 }
			 // alrady exist
			 if(mysqli_num_rows(mysqli_query($link1,"select tabid from access_tab where userid='".$row_locdet['userloginid']."' and tabid='".$rep1[$j]."'"))>0){
				$result = mysqli_query($link1,"update access_tab set status='".$newstatus."' where userid='".$row_locdet['userloginid']."' and tabid='".$rep1[$j]."'")or die(mysqli_error($link1));
			 }else{
				$result = mysqli_query($link1,"insert into access_tab set userid='".$row_locdet['userloginid']."' ,tabid='".$rep1[$j]."',status='".$newstatus."'")or die(mysqli_error($link1));
			 }
			 //// check if query is not executed
			if (!$result) {
				 $flag = false;
				 echo "Error details2: " . mysqli_error($link1) . ".";
			}
			 
		   $j++;
		}
		////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],ucwords($user_name),"LOCATION USER","UPDATE",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	////// return message
	$msg="You have successfully updated details of user ".$row_locdet['locusername'];
	// end Function Rights
}
	///// check  query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	   ///// move to parent page
 	header("location:myaccount_users.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
		$('#dob').datepicker({
			format: "yyyy-mm-dd",
			endDate: "<?=$today?>",
			todayHighlight: true,
			autoclose: true
		});
		$('#doj').datepicker({
			format: "yyyy-mm-dd",
			//endDate: "<?//=$today?>",
			todayHighlight: true,
			autoclose: true
		});
	});
	///// multiple check all function
 function checkFunc(field,ind,val){
	var chk=document.getElementById(val+""+ind).checked;
	if(chk==true){ checkAll(field); }
	else{ uncheckAll(field);}
 }
 function checkAll(field){
   for (i = 0; i < field.length; i++)
        field[i].checked = true ;
 }
 function uncheckAll(field){
   for (i = 0; i < field.length; i++)
        field[i].checked = false ;
 }
 </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <!-- Include Date Picker -->
 <link rel="stylesheet" href="../css/datepicker.css">
 <script src="../js/bootstrap-datepicker.js"></script>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
     include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-users"></i> View/Edit User</h2>
      <h4 align="center"><?=$row_locdet['locusername']."  (".$row_locdet['userloginid'].")";?>
      <?php if($_POST['Submit1']=='Save' || $_POST['Submit2']=='Save'){ ?>
      <br/>
      <span style="color:#FF0000"><?php echo $msg; ?></span>
      <?php } ?>
      </h4>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
      	 <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-id-card"></i> General Details</a></li>
            <li><a data-toggle="tab" href="#menu1"><i class="fa fa-sitemap"></i> Tab Permission</a></li>
			  <li><a data-toggle="tab" href="#menu2"><i class="fa fa-sitemap"></i> Pincode Mapping</a></li>
          </ul>
    	  <div class="tab-content">
            <div id="home" class="tab-pane fade in active"><br/>
              <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">           
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">User Name <span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <input name="user_name" type="text" class="required form-control" id="user_name"  value="<?=$row_locdet['locusername']?>">
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">User Password <span class="red_small">*</span></label>
                      <div class="col-md-6">
                      <input name="password" type="text" class="required form-control" id="password" value="<?=$row_locdet['pwd']?>">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">Email Id</label>
                      <div class="col-md-6">
                        <input name="emailid" type="email" class="email form-control"  id="emailid" value="<?=$row_locdet['emailid']?>">
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">Contact No.<span class="red_small">*</span></label>
                      <div class="col-md-6">
                      <input name="contact_no" type="text" class="digits form-control" id="contact_no"  minlength="10" value="<?=$row_locdet['contactmo']?>" maxlength="10" required>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">Date Of Birth</label>
                      <div class="col-md-6">
                        <div style="display:inline-block;float:left;"><input type="text" class="form-control " name="dob"  id="dob" style="width:150px;" value="<?php if($row_locdet['date_of_birth']!="0000-00-00"){ echo $row_locdet['date_of_birth'];}?>"></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">Date Of Joining</label>
                      <div class="col-md-6">
                      <div style="display:inline-block;float:left;"><input type="text" class="form-control " name="doj"  id="doj" style="width:150px;" value="<?php if($row_locdet['date_of_joining']!="0000-00-00"){ echo $row_locdet['date_of_joining'];}?>"></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">Status</label>
                      <div class="col-md-6">
                        <select name="status" id="status" class="form-control">
                          <option value="1"<?php if($row_locdet['statusid']=="1"){ echo "selected";}?>>Active</option>
                          <option value="2"<?php if($row_locdet['statusid']=="2"){ echo "selected";}?>>Deactive</option>
                        </select>
                      </div>
                    </div>
					  <div class="col-md-6"><label class="col-md-6 control-label">Status</label>
                      <div class="col-md-6">
                       <select name="type" id="type" class="form-control" required>
				  <option value="">--Select Type--</option>
					<option value="Engineer">Engineer</option>
					<option value="Freelancer">Freelancer</option>
				  </select>
                      </div>
                    </div>
                   
                  </div>
                  <div class="form-group">
                    <div class="col-md-12" align="center">
                      <input type="submit" class="btn<?=$btncolor?>" name="Submit1" id="save1" value="Update" title="" <?php if($_POST['Submit1']=='Update'){?>disabled<?php }?>>&nbsp;
                      <input name="id" id="id" type="hidden" value="<?=base64_encode($row_locdet['id'])?>"/>
                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='myaccount_users.php?<?=$pagenav?>'">
                    </div>
                  </div>
            </form>
            </div>
            <div id="menu1" class="tab-pane fade">
              <br/>
              <form  name="frm2" id="frm2" class="form-horizontal" action="" method="post">
      			<div class="table-responsive"> 
                <table id="myTable" class="table table-hover">
                <?php 
				$rs=mysqli_query($link1,"SELECT maintabname FROM tab_master where status='1' and ".$tab_for." group by maintabname ORDER BY maintabseq");
                $num=mysqli_num_rows($rs);
                if($num > 0){
                   $j=1;
                   while($row=mysqli_fetch_array($rs)){
                   $report="SELECT * FROM tab_master where maintabname='".$row['maintabname']."' and status='1' and ".$tab_for." ORDER BY subtabseq";
                   $rs_report=mysqli_query($link1,$report) or die(mysqli_error($link1));
                ?>
                <thead>
                  <tr>
                    <th style="border:none" class="bg-info">&nbsp;<?=$row['maintabname']?>&nbsp;<input style="width:20px"  type="checkbox" id="funcTB1<?=$j?>" name="funcTB1[]" onClick="checkFunc(document.frm2.report1<?=$j?>,'<?=$j?>','funcTB1');"/> </th>
                  </tr>
                </thead>
                <tbody>
                 <?php 
				   $i=1;
                    while($row_report=mysqli_fetch_array($rs_report)){
                       if($i%4==1){?>
                  <tr>
                  <?php
                       }
                    $state_acc=mysqli_query($link1,"select tabid from access_tab where userid='".$row_locdet['userloginid']."' and tabid='".$row_report['tabid']."' and status='1'")or die(mysqli_error());
                    $num1=mysqli_num_rows($state_acc);?>
                    <td><input style="width:20px"  type="checkbox" id="report1<?=$j?>" name="report[]" value="<?=$row_report['tabid']?>" <?php if($num1 > 0) echo "checked";?> /><?=$row_report['subtabname']?></td>
                  <?php if($i/4==0){?>
                  </tr>
                  <?php 
                        }
						$i++;
                    }////// Close 2nd While Loop of TAB 2
                    $j++;
				   }  
				}?>
                </tbody>
                </table>
                </div>
				
                  <div class="form-group">
                    <div class="col-md-12" align="center">
                      <input type="submit" class="btn<?=$btncolor?>" name="Submit2" id="save2" value="Update" title="" <?php if($_POST['Submit2']=='Save'){?>disabled<?php }?>>&nbsp;
                      <input name="id" id="id" type="hidden" value="<?=base64_encode($row_locdet['id'])?>"/>
                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='myaccount_users.php?<?=$pagenav?>'">
                    </div>
                  </div>      	
              </form>
            </div>
			            <div id="menu2" class="tab-pane fade"><br/>
              <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">           
                  <div class="form-group">
                   
                      <div class="col-md-12">
                      <?php
				//echo "select * from location_pincode_access where location_code='".$row_locdet['userloginid']."' and statusid='1' group by pincode";
				$res_pin = mysqli_query($link1,"select * from location_pincode_access where location_code='".$row_locdet['userloginid']."' and statusid='1' group by pincode");
				if(mysqli_num_rows($res_pin)>0){ 
				?>
                <table class='table table-hover'>
                	<thead>
                    <tr>
                    	<th align='left'>Mapped Pincode</th>
                    </tr>
                    </thead>
                    <tbody>
    				<?php
					$i = 1;
                	while($row_pin = mysqli_fetch_array($res_pin)){
     				if($i%4==1){
        				echo "<tr>";
					}
					?>
  					<td width="25%"><?=$row_pin["pincode"]." (".getAnyDetails($row_pin["cityid"],"city","cityid","city_master",$link1).")--".$row_pin["area_type"]."-".$row_pin["postoffice"]?></td>
					<?php 
					if($i/4==0){
						echo "</tr>";
					}
					$i++;
					}
					?>
					</tbody>
        		</table>
        		<?php }?>
                   
                    </div>
                 
                  </div>

                  <div class="form-group">
                    <div class="col-md-12" align="center">
                     
                      <input name="id" id="id" type="hidden" value="<?=base64_encode($row_locdet['id'])?>"/>
                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='myaccount_users.php?<?=$pagenav?>'">
                    </div>
                  </div>
            </form>
            </div>
			
      </div>
    </div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>