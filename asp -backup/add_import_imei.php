<?php 
require_once("../includes/config.php");
//////////////// after hitting upload button
@extract($_POST);
if($_POST['Submit']=="REQUEST"){
mysqli_autocommit($link1, false);
$flag = true;
	$folder1="imei_image";
    $allowedExts = array("gif","jpeg","jpg","png","PNG","GIF","JPEG","JPG");
	
	//echo $_FILES["imei_img"]["name"]."@@@@@@@";
	
if($_FILES["imei_img"]["name"]!=''){
	 $temp = explode(".", $_FILES["imei_img"]["name"]);
	 $extension = end($temp);
	 if(in_array($extension, $allowedExts)){
		 $file_name =$_FILES['imei_img']['name'];
		 $file_tmp =$_FILES['imei_img']['tmp_name'];
		 $file_path="../".$folder1."/".time().$file_name;
		 move_uploaded_file($file_tmp,$file_path);
			
	 }
}
if($_FILES["imei_img2"]["name"]!=''){
	 $temp = explode(".", $_FILES["imei_img2"]["name"]);
	 $extension = end($temp);
	 if(in_array($extension, $allowedExts)){
		 $file_name =$_FILES['imei_img2']['name'];
		 $file_tmp =$_FILES['imei_img2']['tmp_name'];
		 $file_path2="../".$folder1."/".time().$file_name;	
		 move_uploaded_file($file_tmp,$file_path2);
	 }
}
          $mod_info=getAnyDetails($imp_model,"model","model_id","model_master",$link1);

         $imei1 = $imei;
         $imei2 = $sec_imei;
		 $serialno="";
		 $imp_model = $model;  
		 if($imei1!="" && $imp_model!=""){                				
          	$sql = "INSERT INTO imei_data_temp (imei1,imei2,sn,import_date,model_id,model,imei_img1,imei_img2,status,req_by)VALUES('".$imei1."','".$imei2."','".$serialno."','".$today."','".$imp_model."','".$mod_info['model']."','".$file_path."','".$file_path2."','1','".$_SESSION['asc_code']."')";
			$result =	mysqli_query($link1,$sql);
					  //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               echo "Error details: " . mysqli_error($link1) . ".";
           }
		   
		}
	if ($flag) {
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "Successfully Uploaded ";
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	   ///// move to parent page
	header("location:add_import_imei.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  	exit;		   	   
}////// end of for loop
	   
            



?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
   //////////////////////// function to get model on basis of model dropdown selection///////////////////////////
 function getmodel(){
	  var brand=$('#brand').val();
	  var product=$('#prod_code').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{brandinfo:brand,productinfo:product},
		success:function(data){
		 $('#modeldiv').html(data);
	    }
	  });
  }

  </script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
 <link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/jquery-1.10.1.min.js"></script>
<script src="../js/bootstrap-datepicker.js"></script>
<script src="../js/fileupload.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
   include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h3 align="center"><i class="fa fa-external-link"></i>Update Import IMEI</h3><br></br> 

      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
      <?php if($_REQUEST['msg']){?><br>
      <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      <?php }?>
        <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Product<span class="red_small">*</span></label>
              <div class="col-md-4">
              <select name="prod_code" id="prod_code" required class="form-control required" >
                <option value=''>--Please Select--</option>
				<?php
               $model_query="select product_id,product_name from product_master where status='1'";
			        $check1=mysqli_query($link1,$model_query);
                while($br = mysqli_fetch_array($check1)){?>
                <option value="<?=$br['product_id']?>" <?php if($_REQUEST['prod_code'] == $br['product_id']) { echo 'selected'; }?>><?=$br['product_name']." | ".$br['product_id']?></option>
                <?php } ?>
	</select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Brand<span class="red_small">*</span></label>
              <div class="col-md-4">
              <select   name="brand"  id="brand"  required class="form-control required" onChange="getmodel();">
				<option value=''>--Please Select--</option>
				<?php
                $brand = mysqli_query($link1,"select brand_id, brand from brand_master where status='1'" );
                while($brandinfo = mysqli_fetch_assoc($brand)){?>
                <option value="<?=$brandinfo['brand_id']?>" <?php if($_REQUEST['brand'] == $brandinfo['brand_id']) { echo 'selected'; }?>><?=$brandinfo['brand']." | ".$brandinfo['brand_id']?></option>
                <?php } ?>
	</select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Model<span class="red_small">*</span></label>
              <div class="col-md-4" id="modeldiv">
                 <select name="model" id="model"  required class="form-control">
                <option value=''>--Please Select-</option>
                 </select>
              </div>
            </div>
          </div>   
		  <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">IMEI1<span class="red_small">*</span></label>
              <div class="col-md-4">
                 <input type="text" name="imei" class="required form-control" id="imei" maxlength="15" required/>
              </div>
            </div>
          </div>   
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">IMEI2</label>
              <div class="col-md-4">
               <input type="text" name="sec_imei" class="form-control" id="sec_imei" maxlength="15"/>
              </div>
            </div>
          </div>         
          <div class="form-group">
            <div class="col-md-12">
              <label class="col-md-4 control-label">Attach Image1<span class="red_small">*</span></label>
              <div class="col-md-4">
                  <div>
                    <label >
                       <span>
                        <input type="file"  name="imei_img"  required class="form-control" / > 
                    </span>
                    </label>             
                </div>
              </div>
              <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach IMEI Image</strong> file</span></div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12">
              <label class="col-md-4 control-label">Attach Image2</label>
              <div class="col-md-4">
                  <div>
                    <label >
                       <span>
                        <input type="file"  name="imei_img2" class="form-control" / > 
                    </span>
                    </label>             
                </div>
              </div>
              <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach IMEI2 Image</strong> file</span></div>
            </div>
          </div>
         <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="REQUEST" title="" <?php if($_POST['Submit']=='REQUEST'){?>disabled<?php }?>>
              &nbsp;&nbsp;&nbsp;
              
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