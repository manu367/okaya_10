<?php
require_once("../includes/config.php");
/////get state//

//print_r($arrstate);
$arrstatus = getFullStatus("master",$link1);
////// case 1. if we want to update details
if ($_REQUEST['op']=='Edit'){
	$sel_usr="select * from pincode_master where id ='".$_REQUEST['id']."' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){
	
   if ($_POST['add']=='ADD'){ 
    $usr_add="INSERT INTO  pincode_master set  stateid='".$_POST['locationstate']."',cityid='".$_POST['locationcity']."',pincode='".$_POST['pin_code']."' ,area='".ucwords($_POST['area'])."',statusid='1'";
    $res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));
	$dptid = mysqli_insert_id($link1); 
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$dptid,"Picode","ADD",$_SERVER['REMOTE_ADDR'],$link1,"");
	////// return message
	$msg="You have successfully created a pincode with ref. no. ".$dptid;
   }
   else if ($_POST['upd']=='Update'){
    $usr_upd= "update pincode_master set  stateid='".$_POST['locationstate']."',cityid='".$_POST['locationcity']."',pincode='".$_POST['pin_code']."' ,area='".ucwords($_POST['area'])."',statusid='".$_POST['status']."'  where id ='".$_POST['refid']."' ";
    $res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysqli_error($link1));
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$_POST['pin_code'],"Pincode","UPDATE",$ip,$link1,"");
	////// return message
	$msg="You have successfully updated ";
   }
   ///// move to parent page
    header("location:pincode_generate_master.php?msg=".$msg."".$pagenav);
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
</script>
<script src="../js/frmvalidate.js"></script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script></head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-location-arrow"></i> <?=$_REQUEST['op']?> Pincode</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">State<span class="red_small">*</span></label>
              <div class="col-md-6">
                <select name="locationstate" id="locationstate" class="form-control required"  onchange="get_citydiv();" required >

                          <option value=''>--Please Select--</option>

                          <?php 

						 $state_query="select stateid, state from state_master where countryid='1' order by state";

						 $state_res=mysqli_query($link1,$state_query);

						 while($row_res = mysqli_fetch_array($state_res)){?>

						   <option value="<?=$row_res['stateid']?>"<?php if($sel_result['stateid']==$row_res['stateid']){ echo "selected";}?>><?=$row_res['state']?></option>

						 <?php }?> 	

                        </select>      
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">City<span class="red_small">*</span></label>
              <div class="col-md-6" id="citydiv">
                  <select name="locationcity" id="locationcity" class="form-control required" required >
    <?php 

					  

						 $city_query="SELECT cityid, city FROM city_master where stateid='".$sel_result['stateid']."' and cityid='".$sel_result['cityid']."'";

						 $city_res=mysqli_query($link1,$city_query);

						 while($row_city = mysqli_fetch_array($city_res)){

						?>

						<option value="<?=$row_city['cityid']?>"<?php if($sel_result['cityid']==$row_city['cityid']){ echo "selected";}?>><?=$row_city['city']?></option>

						<?php }

					

						?>

                       </select>
                    
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Area <span class="red_small">*</span></label>
              <div class="col-md-6">
                      <input name="area" type="text" class="form-control required" required  id="area" value="<?=$sel_result['area']?>"  >
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Pin Code<span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input type="text" name="pin_code" class="digits form-control required" required  minlength="6" maxlength="6"  id="pin_code" value="<?=$sel_result['pincode']?>"/>
              </div>
            </div>
          </div>
		   <?php if($_REQUEST['op']!='Add'){ ?>
		     <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Status<span class="red_small">*</span></label>
              <div class="col-md-6">
                <select name="status" id="status" class="form-control">
                    <?php foreach($arrstatus as $key => $value){?>
                    	<option value="<?=$key?>" <?php if($sel_result['statusid'] == $key) { echo 'selected'; }?>><?=$value?></option>
                    <?php } ?>
                 </select>
              </div>
            </div>
          </div> <?php }?>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($_REQUEST['op']=='Add'){ ?>
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New Pincode">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update pincode Details">
              <?php }?>
              <input type="hidden" name="refid"  id="refid" value="<?=$sel_result['id']?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='pincode_generate_master.php?status=<?=$pagenav?>'">
            </div>
          </div>
    </form>
      </div>

    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>