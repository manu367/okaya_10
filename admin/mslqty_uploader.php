<?php 
require_once("../includes/config.php");
//////////////// after hitting upload button
@extract($_POST);
if($_POST['Submit']=="Upload"){
	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg = "";
	if ($_FILES["file"]["error"] > 0){
		$code=$_FILES["file"]["error"];
	}
	else{
		move_uploaded_file($_FILES["file"]["tmp_name"],"../ExcelExportAPI/upload_mslqty/".$today.$_FILES["file"]["name"]);
		$file="../ExcelExportAPI/upload_mslqty/".$today.$_FILES["file"]["name"];
		chmod ($file, 0755);
	}
	$filename=$file;
	////////////////////////////////////////////////// code to import file/////////////////////////////////////////////////////////////
	//error_reporting(E_ALL ^ E_NOTICE);
 	$path = '../ExcelExportAPI/Classes/';
    set_include_path(get_include_path() . PATH_SEPARATOR . $path);//we specify the path" using linux"
    function __autoload($classe){
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
	$partnotexist = array();
	$partdeactive = array();
    for($row =2 ;$row <= $highest;$row++){
		$partcode = $sheet->getCellByColumnAndRow(0,$row)->getValue();
		$mslqty = $sheet->getCellByColumnAndRow(1,$row)->getValue();
		//// partcode column should not be blank
		if($partcode!=""){
		//////// check in system partcode
		$res_part = mysqli_query($link1,"select id,status from partcode_master where partcode = '".$partcode."'");
		$row_part = mysqli_fetch_assoc($res_part);
		//////// check if partcode is exist or not
		if($row_part["id"]){
			//////// check if partcode is active or not
			if($row_part["status"] == 1){
				////check if partcode entry is already against with select location in inventory
				if(mysqli_num_rows(mysqli_query($link1,"select id from client_inventory where location_code='".$_POST["location_code"]."' and partcode='".$partcode."'"))>0){
					$res_invt = mysqli_query($link1,"update client_inventory set msl_qty = '".$mslqty."' where location_code='".$_POST["location_code"]."' and partcode='".$partcode."'");
				}else{
					$res_invt = mysqli_query($link1,"insert into client_inventory set msl_qty = '".$mslqty."', location_code='".$_POST["location_code"]."', partcode='".$partcode."', updatedate='".$datetime."'");
				}
				//// check if query is not executed
				if (!$res_invt) {
					 $flag = false;
					 $error_msg = "ER1 " . mysqli_error($link1) . ".";
				}
			}else{
				$partdeactive[] = $partcode;
			}
		}else{
			$partnotexist[] = $partcode;
		}
		}
	}////// end of for loop
	////// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$_POST["location_code"],"MSL QTY","UPLOAD",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	if ($flag) {
    	mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "File is successfully uploaded for location <strong>".$_POST["location_code"]."</strong> .Please check MSL Qty for selected location.";
		///// check if some partcode could not process
		if(!empty($partnotexist)){
			$notexist_msg = implode(" , ",$partnotexist);
			$msg .= "<br/> And some partcode <strong>".$notexist_msg."</strong> not exist in system";
		}
		if(!empty($partdeactive)){
			$deactive_msg = implode(" , ",$partdeactive);
			$msg .= "<br/> And some partcode <strong>".$deactive_msg."</strong> deactive in system";
		}
		
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	///// move to parent page
	header("location:msl_stock_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  	exit;        
}///// end of if condition
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script type="application/javascript">
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
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-upload"></i>Upload MSL Qty for selected location </h2>
      <div style="display:inline-block;float:right"><a href="../templates/MSL_QTY_TEMPLATE.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/></a></div><br/><br/>

      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
      <?php if($_REQUEST['msg']){?><br>
      <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
          </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      <?php unset($_REQUEST); }?>
        <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Location<span class="red_small">*</span></label>
              <div class="col-md-4">
              <select name="location_code" id="location_code" class="form-control required" required>
				<option value="">--Please Select--</option>
                <?php
                $res_pro = mysqli_query($link1,"select location_code,locationname from location_master where statusid='1' order by locationname"); 
                while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                <option value="<?=$row_pro['location_code']?>" <?php if($_REQUEST['location_code'] == $row_pro['location_code']) { echo 'selected'; }?>><?=$row_pro['locationname']." (".$row_pro['location_code'].")"?></option>
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
                        <input type="file"  name="file"  required class="form-control" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/ > 
                    </span>
                    </label>             
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Note</label>
              <div class="col-md-8">
              		<span class="red_small">Attach only <strong>.xlsx (Excel Workbook)</strong> file<br/> Do not delete any column of excel sheet . Fill only appropriate columns.</span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Recommendation</label>
              <div class="col-md-8">
              		<span class="red_small">We have given the data format in attached template. Please follow the same.<br/>PART CODE must be entered as system generated.<br/>There should not be any special character in excel columns.<br/>MSL Qty should be integer only.</span>
              </div>
            </div>
          </div> 
         <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
              &nbsp;&nbsp;
              <button title="Back" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='msl_stock_list.php?<?=$pagenav?>'">Back</button>
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