<?php
require_once("../includes/config.php");
/////get status//
$arrstatus = getFullStatus("master",$link1);
$getid = base64_decode($_REQUEST['refid']);
@extract($_POST);
////// case 1. if we want to update details

////// case 2. if we want to Add new user
if($_POST){
	//// initialize transaction parameters

    if ($_POST['add']=='ADD'){
    ///////// insert model data	   
   $usr_add="INSERT INTO repaircode_master set product_id ='".$product_name."', brand_id ='".$brand_name."', rep_desc='".$repair."' ,status='".$status."',part_replace='".$part_rep."',check_rep='".$rep_type."',	rep_for='".$rep_for."',rep_level='".$rep_level."'";
    $res_add=mysqli_query($link1,$usr_add);

	$insid = mysqli_insert_id($link1);
    /// make 5 digit padding
    $pad=str_pad($insid,4,"0",STR_PAD_LEFT);
    //// make logic of employee code
    $newmodelcode="R".$pad; 
	//////// update system genrated code in model
    $req_res = mysqli_query($link1,"UPDATE repaircode_master set rep_code='".$newmodelcode."' where id='".$insid."'");
	//// check if query is not executed


	
	////// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$newmodelcode,"Repair Action","ADD",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	////// return message
	$msg="You have successfully created a Repair Action like ".$newmodelcode;
	$cflag="success";
	$cmsg="Success";
   }
  
 
	mysqli_close($link1);
   ///// move to parent page
   header("location:repair_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
	$('#release_date').datepicker({
		format: "yyyy-mm-dd",
		//startDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
});
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-cog"></i> <?=$_REQUEST['op']?> Repair Action Price</h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Repair Level <span class="red_small">*</span></label>
                <div class="col-md-6">
               	<select name="rep_lvel" id="rep_lvel" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <?php
					$dept_query="SELECT * FROM repaircode_master where status = '1' group by rep_level ";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['rep_level']?>"<?php if($_REQUEST['rep_level'] == $br_dept['rep_level']){ echo "selected";}?>><?php echo $br_dept['rep_level']?></option>
                <?php }?>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Model Category <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name="model_category" id="model_category" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <?php
					$dept_query="SELECT * FROM model_master where status = '1' group by feature_type ";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['feature_type ']?>"<?php if($_REQUEST['model_category'] == $br_dept['feature_type ']){ echo "selected";}?>><?php echo $br_dept['feature_type ']?></option>
                <?php }?>
                </select>
              </div>
            </div>
          </div>
      
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Repair Action </label>
                <div class="col-md-6">
               	 <input type="text" name="repair" class=" form-control" id="repair" value="<?=$sel_result['symp_desc']?>" required/>
              </div>
            </div>
              <div class="col-md-6"><label class="col-md-6 control-label">Status </label>
              <div class="col-md-6">
                 <select name="status" id="status" class="form-control">
                    <?php foreach($arrstatus as $key => $value){?>
                    	<option value="<?=$key?>" <?php if($sel_result['status'] == $key) { echo 'selected'; }?>><?=$value?></option>
                    <?php } ?>
                 </select>
              </div>
            </div>
          </div>
       
		  
		    <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Part Replace <span class="red_small">*</span></label>
              <div class="col-md-6">
               	 <input type="checkbox" name="part_rep" id="part_rep" value="Y" class="col-md-offset-4">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Repair Level <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input name="rep_level" type="text"  id="rep_level" class="number form-control">
              </div>
            </div>
          </div>
       
           
           
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($_REQUEST['op']=='Add'){ ?>
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New Fault">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update fault Details">
              <?php }?>
              <input type="hidden" name="refid"  id="refid" value="<?=base64_encode($sel_result['symp_code'])?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='repair_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
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