<?php 
require_once("../includes/config.php");
//////////////// after hitting upload button
@extract($_POST);
if($_POST['Submit']=="Upload"){
mysqli_autocommit($link1, false);
$flag = true;
if ($_FILES["file"]["error"] > 0)
{
$code=$_FILES["file"]["error"];
}
else
{
$model = $_POST['model'];
move_uploaded_file($_FILES["file"]["tmp_name"],
"../ExcelExportAPI/upload/".$today.$_FILES["file"]["name"]);
$file="../ExcelExportAPI/upload/".$today.$_FILES["file"]["name"];
chmod ($file, 0755);
}

$filename=$file;
////////////////////////////////////////////////// code to import file/////////////////////////////////////////////////////////////
error_reporting(E_ALL ^ E_NOTICE);
 $path = '../ExcelExportAPI/Classes/';
        set_include_path(get_include_path() . PATH_SEPARATOR . $path);//we specify the path" using linux"
        function __autoload($classe)
        {
            $var = str_replace
            (
                '_', 
                DIRECTORY_SEPARATOR,
                $classe
            ) . '.php' ;
            require_once($var);
        }   

 $indentityType = PHPExcel_IOFactory::identify($filename);
                $object = PHPExcel_IOFactory::createReader($indentityType);
                $object->setReadDataOnly(true);
                $objPHPExcel = $object->load($filename);
               /*echo '<script>alert("le fichier a été chargé avec succes !");</script>';*/
                $sheet = $objPHPExcel->getSheet(0); //we specify the sheet to use
                $highestRow = $sheet->getHighestRow();//we select all the rows used in the sheet 
                $highestCol = $sheet->getHighestColumn();// we select all the columns used in the sheet
                $indexCol = PHPExcel_Cell::columnIndexFromString($highestCol); //////// count no. of column 
				$highest = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow(); //////////////// count no of rows in excel
	  
                //importing files to the database
                for($row =2 ;$row <= $highest;$row++)
                {
                    $model = $sheet->getCellByColumnAndRow(0,$row)->getValue();
					$tech_model = $sheet->getCellByColumnAndRow(1,$row)->getValue();
					$make_job = $sheet->getCellByColumnAndRow(2,$row)->getValue();
					$make_doa = $sheet->getCellByColumnAndRow(3,$row)->getValue();
					$make_repairable = $sheet->getCellByColumnAndRow(4,$row)->getValue();
					$make_outwarr = $sheet->getCellByColumnAndRow(5,$row)->getValue();
					$make_replacement = $sheet->getCellByColumnAndRow(6,$row)->getValue();
					$chk_serial = $sheet->getCellByColumnAndRow(7,$row)->getValue();
					$service_cahrge = $sheet->getCellByColumnAndRow(8,$row)->getValue();
					$doa_days = $sheet->getCellByColumnAndRow(9,$row)->getValue();
					$replace_days = $sheet->getCellByColumnAndRow(10,$row)->getValue();
					$cust_warr_days = $sheet->getCellByColumnAndRow(11,$row)->getValue();
					$dealer_warr_days = $sheet->getCellByColumnAndRow(12,$row)->getValue();
				if($model!=''){	
                $usr_add="INSERT INTO model_master set product_id ='".$prod_code."', brand_id ='".$brand."', model='". $model."' , technical_model ='". $tech_model."', release_date='".$today."', make_doa='".$make_doa."', doa_days='".$doa_days."',out_warranty='".$make_outwarr."', repairable='".$make_repairable."', replacement='".$make_replacement."', replace_days='".$replace_days."',make_job='".$make_job."', chk_serimei ='".$chk_serial."', ser_charge = '".$service_cahrge."', status='1',createdate='".date("Y-m-d H:i:s")."',createby='".$_SESSION['userid']."',wp='".$cust_warr_days."',dwp='".$dealer_warr_days."' ";
					
				$res_add=mysqli_query($link1,$usr_add);
	//// check if query is not executed
	if (!$res_add) {
		 $flag = false;
		 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
	}
	$insid = mysqli_insert_id($link1);
    /// make 5 digit padding
    $pad=str_pad($insid,5,"0",STR_PAD_LEFT);
    //// make logic of employee code
    $newmodelcode="M".$pad; 
	//////// update system genrated code in model
	
    $req_res = mysqli_query($link1,"UPDATE model_master set model_id='".$newmodelcode."' where id='".$insid."'");
	//// check if query is not executed
	if (!$req_res) {
		 $flag = false;
		 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
	}	
	////// 2 entry in partcode master one for complete unit and other for single unit
	/// Box partcode in partcode master
	///////// insert model data	 
	//$bx_desc=$model."-BOX";     
   // $usr_add="INSERT INTO partcode_master set product_id ='".$prod_code."', brand_id ='".$brand."',model_id='".$newmodelcode."', part_name='".$bx_desc."' , hsn_code ='8517' ,part_desc='".$bx_desc."',location_price='', customer_price ='', servicekit_qty='',servicekit_flag='', part_category='BOX', part_for='ALL', repair_code='', status='1',createdate='".date("Y-m-d H:i:s")."',createby='".$_SESSION['userid']."'";
	
    /*$res_add=mysqli_query($link1,$usr_add);
	//// check if query is not executed
	if (!$res_add) {
		 $flag = false;
		 $error_msg = "Error details3: " . mysqli_error($link1) . ".";
	}
	$insid = mysqli_insert_id($link1);
    /// make 5 digit padding
    $pad=str_pad($insid,5,"0",STR_PAD_LEFT);
    //// make logic of partcode code
    $newpartcode="P".$pad; */
	//////// update system genrated code in model
	
    /*$req_res = mysqli_query($link1,"UPDATE partcode_master set partcode='".$newpartcode."' where id='".$insid."'");
	//// check if query is not executed
	if (!$req_res) {
		 $flag = false;
		 $error_msg = "Error details4: " . mysqli_error($link1) . ".";
	}*/
	/// Unit partcode in partcode master
	///////// insert model data	 
	/*$unt_desc=$model."-UNIT";     
    $usr_add="INSERT INTO partcode_master set product_id ='".$prod_code."', brand_id ='".$brand."',model_id='".$newmodelcode."', part_name='".$unt_desc."' , hsn_code ='8517' ,part_desc='".$unt_desc."',location_price='', customer_price ='', servicekit_qty='',servicekit_flag='', part_category='UNIT', part_for='ALL', repair_code='', status='1',createdate='".date("Y-m-d H:i:s")."',createby='".$_SESSION['userid']."'";
	
    $res_add=mysqli_query($link1,$usr_add);
	//// check if query is not executed
	if (!$res_add) {
		 $flag = false;
		 $error_msg = "Error details5: " . mysqli_error($link1) . ".";
	}
	$insid = mysqli_insert_id($link1);
    /// make 5 digit padding
    $pad=str_pad($insid,5,"0",STR_PAD_LEFT);
    //// make logic of partcode code
    $newpartcode="P".$pad; 
	//////// update system genrated code in model
	
    $req_res = mysqli_query($link1,"UPDATE partcode_master set partcode='".$newpartcode."' where id='".$insid."'");
	//// check if query is not executed
	if (!$req_res) {
		 $flag = false;
		 $error_msg = "Error details6: " . mysqli_error($link1) . ".";
	}*/
	////// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$newmodelcode,"MODEL","ADD",$_SERVER['REMOTE_ADDR'],$link1,$flag);
				} ///// END IF Model Condition
    }////// end of for loop
	   
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
	header("location:model_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
    exit;
            
}///// end of if condition



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
   include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-upload"></i>Upload New Model </h2>
      <div style="display:inline-block;float:right"><a href="../templates/UPLOAD_MODEL.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/></a></div>	<br></br> 

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
              <select   name="brand"  id="brand"  required class="form-control required">
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
            <div class="col-md-12"><label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
              <div class="col-md-4">
                  <div>
                    <label >
                       <span>
                        <input type="file"  name="file"  required class="form-control"   accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/ > 
                    </span>
                    </label>             
                </div>
              </div>
              <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
            </div>
          </div>
         <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
              &nbsp;&nbsp;&nbsp;
                   <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='model_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'"> 
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