<?php
require_once("../includes/config.php");
$getid=base64_decode($_REQUEST['id']);
$array_escl = array();

////// get details of selected location////
if ($_REQUEST['op']=='Edit'){
	$sel_usr="select * from tools_master where sno='".$getid."' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
/////get status//
$arrstatus = getFullStatus("master",$link1);
////// final submit form ////
@extract($_POST);
if($_POST){
	if($type=='SW'){
		$url="download/Software";
	}if($type=='SM'){
		$url="download/ServiceManual";
	}
		  
	$modelsplit = explode("~",$modelid);
    if ($_POST['add']=='ADD'){
	
		if(($_FILES['file_url']["name"]!='') && ($_FILES["file_url"]["size"] < 2048000)){ 
			$file_name =$_FILES['file_url']['name'];
			$file_tmp =$_FILES['file_url']['tmp_name'];
			if($file_name){
				$file_path="../".$url."/".$file_name;
				$img_upld1 = move_uploaded_file($file_tmp,$file_path);
			}else{
				$file_path = "";
			}
		}
		
		///////// insert folder condition
		$usr_add="insert into tools_master set brand='$brand_name',remark='$remark', model='$modelsplit[0]',url='$file_path',product_id='$product_name', type='$type',update_date='$today',version='$software_version',file_name='$file_name', status='1'";
		
		$res_add=mysqli_query($link1,$usr_add);
		
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$newpartcode,"Add Software","ADD",$_SERVER['REMOTE_ADDR'],$link1,$flag);
		////// return message
		$msg="Data Insert  successfully ";
		$cflag="success";
		$cmsg="Success";
   }
   else if ($_POST['upd']=='Update'){ 
   		if(($_FILES['file_url']["name"]!='') && ($_FILES["file_url"]["size"] < 2048000)){ 
			$file_name =$_FILES['file_url']['name'];
			$file_tmp =$_FILES['file_url']['tmp_name'];
			if($file_name){
				$file_path="../".$url."/".$file_name;
				$img_upld1 = move_uploaded_file($file_tmp,$file_path);
			}else{
				$file_path = "";
			}
		}
		
		$usr_upd = "Update tools_master  set brand='$brand_name',remark='$remark', model='$modelsplit[0]',url='$file_path',product_id='$product_name', type='$type',update_date='$today',version='$software_version',file_name='$file_name',status='$status' where sno='".$getid."'";
		
		$res_upd = mysqli_query($link1,$usr_upd);
		///// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$getid,"Software Details","UPDATE",$ip,$link1,$flag);
		////// return message
		$msg="You have successfully updated the details.";
		$cflag="success";
		$cmsg="Success";
   }else{
	    $flag = false;
	   	$cflag = "info";
		$cmsg = "Warning";
   		$msg = "Request could not be processed. Please try again.";
   }
 
	
   ///// move to parent page
   header("location:software_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
 <script language="javascript" type="text/javascript">
   /////////// function to get state on the basis of circle
  $(document).ready(function(){
	$('#country').change(function(){
	  var countryid=$('#country').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{cntryid:countryid},
		success:function(data){
	    $('#statediv').html(data);
	    }
	  });
    });
  });
 /////////// function to get model on the basis of brand

  $(document).ready(function(){

	$('#brand_name').change(function(){
		

	  var brandid=$('#brand_name').val();

	  $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{brand:brandid},

		success:function(data){

	    $('#modeldiv').html(data);

	    }

	  });

    });

  });
 /////////// function to get district on the basis of state
 function get_distdiv(){
	  var name=$('#locationstate').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{state2:name},
		success:function(data){
	    $('#distctdiv').html(data);
	    }
	  });
   
 } 
$(document).ready(function() {
	$('#example-multiple-selected1').multiselect({
			includeSelectAllOption: true,
			enableFiltering: true,
			buttonWidth:"320"
            //enableFiltering: true
	});
	$('#example-multiple-selected2').multiselect({
			includeSelectAllOption: true,
			enableFiltering: true,
			buttonWidth:"320"
            //enableFiltering: true
	});
}); 

function validateImage(nam,ind) {
	var err_msg="";
	var img1=document.getElementById("file_url").value;
	
    var file = document.getElementById(nam).files[0];
    var t = file.type.split('/').pop().toLowerCase();
	
    if(t != "jpeg" && t != "jpg" && t != "png" && t != "bmp" && t != "gif" && t != "pdf" && t != "xls" && t != "xlsx" && t != "zip" && t != "rar") {
		err_msg = "<strong>Please select a valid file. <br/></strong>";
		document.getElementById("errmsg"+ind).innerHTML = err_msg;
		document.getElementById(nam).value = '';
        return false;
    }else if(file.size > 2048000){  /**** 204800 ***/
		err_msg = "<strong>Max file size can be 2 MB.<br/></strong>";
		document.getElementById("errmsg"+ind).innerHTML = err_msg;
		document.getElementById(nam).value = '';
        return false;
    }else{
		document.getElementById("errmsg"+ind).innerHTML ="";
	}
    
	return true;
}

</script>
  <script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i ></i> <?=$_REQUEST['op']?> Software</h2><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
      
    	  <div class="tab-content">
            <div id="home" class="tab-pane fade in active"><br/>
              <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data" >
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">Product <span class="red_small">*</span></label>
                      <div class="col-md-6">
                     	<select name="product_name" id="product_name" class="form-control required" required>
                  <option value="">--Please Select--</option>
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
                    <div class="col-md-6"><label class="col-md-6 control-label">Brand <span class="red_small">*</span></label>
                      <div class="col-md-6">
                       <select name="brand_name" id="brand_name" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <?php
					$dept_query="SELECT * FROM brand_master where status = '1' order by brand";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['brand_id']?>"<?php if($sel_result['brand'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>
                <?php }?>
                </select>
                      </div>
                    </div>
                  </div>
               
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">Model <span class="red_small">*</span></label>
                     
                      <div class="col-md-6" id="modeldiv">

                        <select name="modelid" id="modelid" class="form-control required" required>

                         <?php
					$dept_query="SELECT * FROM model_master where model_id = '".$sel_result['model']."' order by model";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['model_id']?>"<?php if($sel_result['model'] == $br_dept['model_id']){ echo "selected";}?>><?php echo $br_dept['model']?></option>
                <?php }?>

                       

                        </select>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">Folder Type <span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <select name="type"  id="type" class="form-control required" required>
                    <option value="SW" <?php if($sel_result['type'] == "SW"){ echo "selected";}?>>Software</option>
                   <option value="SM"<?php if($sel_result['type'] == "SM"){ echo "selected";}?>>Service  Manaul</option>
                  <!--   <option value="SB"<?php //if($sel_result['type'] == "SB"){ echo "selected";}?>>Service Bulletin</option>
                     <option value="TD"<?php //if($sel_result['type'] == "TD"){ echo "selected";}?>>Tools & Driver</option>
                   <option value="IT"<?php //if($sel_result['type'] == "IT"){ echo "selected";}?>>IMEI Tools</option>
                  <option value="UD"<?php //if($sel_result['type'] == "UD"){ echo "selected";}?>>User Guide</option>-->
              </select>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">Remarks <span class="red_small">*</span></label>
                        <div class="col-md-6" >
                        <textarea name="remark" id="remark" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"><?php echo $sel_result['remark'];?></textarea>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">File Name <span class="red_small">*</span></label>
                      <div class="col-md-6">
                             <input name="file_url" id="file_url" type="file" class="required form-control" accept=".png,.jpg,.jpeg,.gif,.jpeg,.jpg,.zip,.rar,.gif" onChange="return validateImage('file_url','0');"  required value="<?=$sel_result['file_name']?>">
							 <div id="errmsg0" class="red_small"></div>
							 <span class="red_small">Accepted Extenstion like(.pdf, .ppt, .xls, .xlsx, .zip, .rar, .jpeg, .jpg, .zip, .rar, .gif)</span>
                      </div>
                    </div>
                  </div>		 
                <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">Software version <span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <input name="software_version" type="text" class="required form-control" id="software_version" required value="<?=$sel_result['version']?>">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <label class="col-md-6 control-label">Status <span class="red_small">*</span></label>
                    
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
                    <div class="col-md-12" align="center">
                          <?php if($_REQUEST['op']=='Add'){ ?>
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New Courier">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update Courier Details">
              <?php }?>
                      <input name="id" id="id" type="hidden" value="<?=base64_encode($sel_result['sno'])?>"/>
                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='software_master.php?<?=$pagenav?>'">
                    </div>
                </div>
            </form>
            </div>


        
                 
                 
                
              </form>
            </div>
           
      </div><!--End form group-->
    </div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>