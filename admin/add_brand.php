<?php

require_once("../includes/config.php");

/////get status//

$arrstatus = getFullStatus("master",$link1);

@extract($_POST);

////// case 1. if we want to update details

if ($_REQUEST['op']=='Edit'){

	 $sel_usr="select * from brand_master where brand_id='".$_REQUEST['id']."' ";

	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));

	$sel_result=mysqli_fetch_assoc($sel_res12);

}

////// case 2. if we want to Add new user

if($_POST){
	
   if ($_POST['add']=='ADD'){
	   
   /*$folder1="images";

    $allowedExts = array("gif","jpeg","jpg","png","PNG","GIF","JPEG","JPG");

	

	if($_FILES["imei_img"]["name"]!=''){

	 $temp = explode(".", $_FILES["imei_img"]["name"]);

	 $extension = end($temp);

	 if(($_FILES["imei_img"]["size"] < 2000000) && (in_array($extension, $allowedExts))){

		 $file_name =$brand_name."_"."logo";

		 $file_tmp =$_FILES['imei_img']['tmp_name'];

		  $file_path="../".$folder1."/".$file_name;

		 $image_move1 = move_uploaded_file($file_tmp,$file_path);

			

	 }

}*/

  //  $usr_add="INSERT INTO brand_master set brand ='".$brand_name."',status='".$status."',brand_logo='".$file_path."',createdate='".date("Y-m-d H:i:s")."',createby='".$_SESSION['userid']."',len_serialno='".$len_serialno."',software_version='".$software_version."',make_job='".$make_job."',warr_days='".$warr_days."',make_doa='".$make_doa."',doa_days='".$doa_days."',repairable='".$repairable."',out_warranty='".$out_warranty."',replacement='".$replacement."',replace_days='".$replace_days."',editshipment='".$editshipment."',sendsms='".$sendsms."',dopapproval='".$dopapproval."',popapproval='".$popapproval."',re_esclate_appr='".$re_esclate_appr."',doaclasedwh='".$doaclasedwh."',brand_integ='".$brand_integ."',allowpart='".$allowpart."',cust_warranty_days='".$cust_warranty_days."',chk_serimei='".$chk_serimei."',minimum_minute='".$minimum_minute."',cust_amount='".$cust_amount."',dist_amount='".$dist_amount."',dealer_amount='".$dealer_amount."',trems_cond='".$trems_cond."',release_date='".$release_date."',ser_charge='".$ser_charge."' ";
	
	$usr_add="INSERT INTO brand_master set brand ='".$brand_name."',status='".$status."',createdate='".date("Y-m-d H:i:s")."',createby='".$_SESSION['userid']."',release_date='".$release_date."'";

    $res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));

	$dptid = mysqli_insert_id($link1); 

	////// insert in activity table////

	dailyActivity($_SESSION['userid'],$dptid,"BRAND","ADD",$_SERVER['REMOTE_ADDR'],$link1,"");

	////// return message

	$msg="You have successfully created a brand like ".$brand_name;

	$cflag="success";

   }

   else if ($_POST['upd']=='Update'){
	   

   mysqli_autocommit($link1, false);

	$flag = true;

  if(1==1){	

    //$usr_upd="update brand_master set brand ='".$brand_name."',status='".$status."',updatedate='".date("Y-m-d H:i:s")."',updateby='".$_SESSION['userid']."',brand_logo='".$file_path."',len_serialno='".$len_serialno."',software_version='".$software_version."',make_job='".$make_job."',warr_days='".$warr_days."',make_doa='".$make_doa."',doa_days='".$doa_days."',repairable='".$repairable."',out_warranty='".$out_warranty."',replacement='".$replacement."',replace_days='".$replace_days."',editshipment='".$editshipment."',sendsms='".$sendsms."',dopapproval='".$dopapproval."',popapproval='".$popapproval."',re_esclate_appr='".$re_esclate_appr."',doaclasedwh='".$doaclasedwh."',brand_integ='".$brand_integ."',allowpart='".$allowpart."',cust_warranty_days='".$cust_warranty_days."',chk_serimei='".$chk_serimei."',minimum_minute='".$minimum_minute."',cust_amount='".$cust_amount."',dist_amount='".$dist_amount."',dealer_amount='".$dealer_amount."',trems_cond='".$trems_cond."',release_date='".$release_date."',ser_charge='".$ser_charge."' where brand_id = '".$refid."'";
	
	$usr_upd="update brand_master set status='".$status."',updatedate='".date("Y-m-d H:i:s")."',updateby='".$_SESSION['userid']."' where brand_id = '".$refid."'";

    $res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysqli_error($link1));

	}else {

	  $flag = false;

	}

	////// insert in activity table////

	dailyActivity($_SESSION['userid'],$refid,"BRAND","UPDATE",$ip,$link1,"");

	////// return message

	if ($flag) {

        mysqli_commit($link1);

		$cflag = "success";

		$cmsg = "Success";

        $msg = "Successfully Uploaded ";

    } else {

		mysqli_rollback($link1);

		$cflag = "danger";

		$cmsg = "Failed";

	}

   }

   ///// move to parent page

   header("location:brand_master.php?msg=".$msg."&chkflag=".$cflag."".$pagenav);

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

</script>

<script src="../js/frmvalidate.js"></script>

<script src="../js/jquery.validate.js"></script>

</head>

<body>

<div class="container-fluid">

  <div class="row content">

	<?php 

    include("../includes/leftnav2.php");

    ?>

    <div class="<?=$screenwidth?>">

      <h2 align="center"><i class="fa fa-tag"></i> <?=$_REQUEST['op']?> Brand</h2><br/><br/>

      

      <div class="form-group"  id="page-wrap" style="margin-left:10px;">

          <form id="frm1" name="frm1" class="form-horizontal"  enctype="multipart/form-data" action="" method="post">

          <div class="form-group">

            <div class="col-md-6"><label class="col-md-6 control-label">Brand Name</label>

              <div class="col-md-6">
                 <input type="text" name="brand_name" class="required form-control" id="brand_name" value="<?=$sel_result['brand']?>" required/>
              </div>

            </div>

 

  

            <div class="col-md-6"><label class="col-md-6 control-label">Status</label>

              <div class="col-md-6">

                 <select name="status" id="status" class="form-control">

                    <?php foreach($arrstatus as $key => $value){?>

                    	<option value="<?=$key?>" <?php if($sel_result['status'] == $key) { echo 'selected'; }?>><?=$value?></option>

                    <?php } ?>

                 </select>

              </div>

            </div>

          </div>

<?php /*?><div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">IMEI/Serial No. length</label>
                <div class="col-md-6">
               	 <input type="text" name="len_serialno" class="digits form-control" id="len_serialno" value="<?=$sel_result['len_serialno']?>"/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Software Version</label>
              <div class="col-md-6">
                 <input type="text" name="software_version" class="form-control" id="software_version" value="<?=$sel_result['software_version']?>"/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Make JOB <span class="red_small">*</span></label>
              <div class="col-md-6">
               	<div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="make_job"  id="make_job1" value="Y" required <?php if($sel_result['make_job']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="make_job"  id="make_job2" value="N" required <?php if($sel_result['make_job']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
              </div>
            </div>
          <div class="col-md-6"><label class="col-md-6 control-label">Warranty Days </label>
              <div class="col-md-6">
                 <input type="text" name="warr_days" class="digits form-control" id="warr_days" value="<?=$sel_result['warr_days']?>"/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Make DOA <span class="red_small">*</span></label>
              <div class="col-md-6">
               	<div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="make_doa"  id="make_doa1" value="Y" required <?php if($sel_result['make_doa']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="make_doa"  id="make_doa2" value="N" required <?php if($sel_result['make_doa']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">DOA Days</label>
              <div class="col-md-6">
                 <input type="text" name="doa_days" class="digits form-control" id="doa_days" value="<?=$sel_result['doa_days']?>"/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Repairable <span class="red_small">*</span></label>
              <div class="col-md-6">
               	<div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="repairable"  id="repairable1" value="Y" required <?php if($sel_result['repairable']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="repairable"  id="repairable2" value="N" required <?php if($sel_result['repairable']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Out Warranty <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="out_warranty"  id="out_warranty1" value="Y" required <?php if($sel_result['out_warranty']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="out_warranty"  id="out_warranty2" value="N" required <?php if($sel_result['out_warranty']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Replacement <span class="red_small">*</span></label>
              <div class="col-md-6">
               	<div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="replacement"  id="replacement1" value="Y" required <?php if($sel_result['replacement']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="replacement"  id="replacement2" value="N" required <?php if($sel_result['replacement']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Replace Days </label>
              <div class="col-md-6">
                 <input type="text" name="replace_days" class="digits form-control" id="replace_days" value="<?=$sel_result['replace_days']?>"/>
              </div>
            </div>
          </div>
			  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Edit Ship Ment <span class="red_small">*</span></label>
              <div class="col-md-6">
               	<div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="editshipment"  id="editshipment1" value="Y" required <?php if($sel_result['editshipment']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="editshipment"  id="editshipment2" value="N" required <?php if($sel_result['editshipment']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Send SMS<span class="red_small">*</span></label>
              <div class="col-md-6">
                 <div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="sendsms"  id="sendsms1" value="Y" required <?php if($sel_result['sendsms']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="sendsms"  id="sendsms2" value="N" required <?php if($sel_result['sendsms']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
              </div>
            </div>
          </div>
			  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">DOA Approval <span class="red_small">*</span></label>
              <div class="col-md-6">
               	<div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="dopapproval"  id="dopapproval1" value="Y" required <?php if($sel_result['dopapproval']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="dopapproval"  id="dopapproval2" value="N" required <?php if($sel_result['dopapproval']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">POP Approval<span class="red_small">*</span></label>
              <div class="col-md-6">
                 <div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="popapproval"  id="popapproval1" value="Y" required <?php if($sel_result['popapproval']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="popapproval"  id="popapproval2" value="N" required <?php if($sel_result['popapproval']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
              </div>
            </div>
          </div>
			  
			  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Re Esclate Approval <span class="red_small">*</span></label>
              <div class="col-md-6">
               	<div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="re_esclate_appr"  id="re_esclate_appr1" value="Y" required <?php if($sel_result['re_esclate_appr']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="re_esclate_appr"  id="re_esclate_appr2" value="N" required <?php if($sel_result['re_esclate_appr']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">DOA Closed AT CWH<span class="red_small">*</span></label>
              <div class="col-md-6">
                 <div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="doaclasedwh"  id="doaclasedwh1" value="Y" required <?php if($sel_result['doaclasedwh']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="doaclasedwh"  id="doaclasedwh2" value="N" required <?php if($sel_result['doaclasedwh']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
              </div>
            </div>
          </div>
			  
			  
			  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Brand Integration <span class="red_small">*</span></label>
              <div class="col-md-6">
               	<div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="brand_integ"  id="brand_integ1" value="Y" required <?php if($sel_result['brand_integ']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="brand_integ"  id="brand_integ2" value="N" required <?php if($sel_result['brand_integ']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Allowed part<span class="red_small">*</span></label>
              <div class="col-md-6">
                 <div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="allowpart"  id="allowpart1" value="Y" required <?php if($sel_result['allowpart']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="allowpart"  id="allowpart2" value="N" required <?php if($sel_result['allowpart']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
              </div>
            </div>
          </div>
			  
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Check IMEI/Serial No. <span class="red_small">*</span></label>
              <div class="col-md-6">
               	<div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="chk_serimei"  id="chk_serimei1" value="Y" required <?php if($sel_result['chk_serimei']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="chk_serimei"  id="chk_serimei2" value="N" required <?php if($sel_result['chk_serimei']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Customer Warranty Days </label>
              <div class="col-md-6">
                 <input type="text" name="cust_warranty_days" class="digits form-control" id="cust_warranty_days" value="<?=$sel_result['cust_warranty_days']?>"/>
              </div>
            </div>
          </div>
         <!-- <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">AMC Days </label>
                <div class="col-md-6">
               	 <input type="text" name="amc_days" class=" form-control" id="amc_days" value="<?=$sel_result['amc_days']?>" />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">AMC Amount</label>
              <div class="col-md-6">
                 <input type="text" name="amc_amount" class="form-control number" id="amc_amount" value="<?=$sel_result['amc_amount']?>"/>
              </div>
            </div>
          </div>-->
			  
			   <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Minimum Closer In Minute</label>
                <div class="col-md-6">
               	 <input type="text" name="minimum_minute" class=" form-control" id="minimum_minute" value="<?=$sel_result['minimum_minute']?>" />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Service Charges<span class="red_small">*</span></label>
              <div class="col-md-6">
              <input type="text" name="ser_charge" class="required form-control number" id="ser_charge" value="<?=$sel_result['ser_charge']?>"/>   
              </div>
            </div>
          </div>
			  
			 
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Term and Conditions</label>
              <div class="col-md-6">
                 <textarea name="trems_cond" id="trems_cond"  class=" form-control addressfield"  onContextMenu="return false" style="resize:vertical" ><?php echo $sel_result['trems_cond'];?></textarea>
              </div>
            </div>
             <div class="col-md-6"><label class="col-md-6 control-label">Release Date<span class="red_small">*</span></label>
              <div class="col-md-6">
                 <div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="release_date"  id="release_date" style="width:150px;" required value="<?=$today?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>
                  </div>
              </div>
            </div>
          </div>
        
          

		    <?php if($_REQUEST['op']=='Add'){ ?>

		  <div class="form-group">

           <div class="col-md-6">  <label class="col-md-6 control-label">Brand Logo<span class="red_small">*</span></label>

              <div class="col-md-6">

                  <input type="file"  name="imei_img" value="<?=$sel_result['brand_logo']?>"  class="form-control"  accept=".png,.jpg,.jpeg,.gif"/ > 

              </div>

            </div>

          </div><?php } else {?>

		    <div class="form-group">

           <div class="col-md-6">  <label class="col-md-6 control-label">Brand Logo <span class="red_small">*</span></label>

              <div class="col-md-6">

			    <img src="<?=$sel_result['brand_logo']?>" height="80" width="150"/ >
                  <input type="file"  name="imei_img" value="<?=$sel_result['brand_logo']?>"  class="form-control "  accept=".png,.jpg,.jpeg,.gif"/ > 

                

              </div>

            </div>

          </div>

		  

		  

		  <?php }?><?php */?>

          <div class="form-group">

            <div class="col-md-12" align="center">

              <?php if($_REQUEST['op']=='Add'){ ?>

              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New Brand">

              <?php }else{?>

              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update Brand Details">

              <?php }?>

              <input type="hidden" name="refid"  id="refid" value="<?=$sel_result['brand_id']?>" />

              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='brand_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">

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
