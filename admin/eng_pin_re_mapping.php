<?php 
require_once("../includes/config.php");
$arrstatus = getFullStatus("master",$link1);
//////////////// after hitting upload button
@extract($_POST);
if($_POST['Submit']=="Upload"){
	mysqli_autocommit($link1, false);
	$flag = true;
	
	if ($_FILES["file"]["error"] > 0){
		$code=$_FILES["file"]["error"];
	}else{
		$model = $_POST['model'];
		move_uploaded_file($_FILES["file"]["tmp_name"],
		"../ExcelExportAPI/upload_pincode/".$today.$_FILES["file"]["name"]);
		$file="../ExcelExportAPI/upload_pincode/".$today.$_FILES["file"]["name"];
		chmod ($file, 0755);
	}

	$filename=$file;
	////////////////////////////////////////////////// code to import file/////////////////////////////////////////////////////////////
	error_reporting(E_ALL ^ E_NOTICE);
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
	for($row =2 ;$row <= $highest;$row++){
		$loc = $sheet->getCellByColumnAndRow(0,$row)->getValue();
		$picode = $sheet->getCellByColumnAndRow(1,$row)->getValue();
		$tmp_a_typ = strtolower($sheet->getCellByColumnAndRow(2,$row)->getValue());
		$a_typ = ucfirst($tmp_a_typ);
		$po_area = $sheet->getCellByColumnAndRow(3,$row)->getValue();
		
		/******** Check pincode and post office is valid or not **********/
		$check_pin_master = mysqli_fetch_array(mysqli_query($link1, "SELECT cityid, stateid, area FROM pincode_master WHERE  pincode = '".$picode."'  AND statusid = '1' "));
		if($check_pin_master['area']!=""){
			
			////// check location is valid or not //////
			$check_loc = mysqli_fetch_array(mysqli_query($link1, "SELECT userloginid FROM locationuser_master WHERE  userloginid = '".$loc."' "));
			//print_r($check_loc);exit;
			if($check_loc['userloginid']!=""){
			
				$sel_usr="select * from location_pincode_access where location_code ='".$loc ."' and  pincode='". $picode."'  ";
				
				$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
				$sel_result=mysqli_fetch_assoc($sel_res12);		
				
				if(mysqli_num_rows($sel_res12)>0){
					$usr_add="update location_pincode_access set area_type = '".$a_typ."', statusid = '".$status."', cityid = '".$check_pin_master['cityid']."', stateid = '".$check_pin_master['stateid']."', postoffice = '".$po_area."' where  location_code ='".$loc ."' and  pincode='". $picode."' and postoffice = '".$po_area."'   ";
					
					$res_add=mysqli_query($link1,$usr_add);
					//// check if query is not executed
					if(!$res_add){
						 $flag = false;
						 $error_msg = "Error details 1 : " . mysqli_error($link1) . ".";
					}
				}else{
					$usr_add="INSERT INTO location_pincode_access set  area_type = '".$a_typ."', location_code = '".$loc ."', pincode = '". $picode."', statusid = '".$status."', cityid = '".$check_pin_master['cityid']."', stateid = '".$check_pin_master['stateid']."', postoffice = '".$po_area."' ";
					
					$res_add=mysqli_query($link1,$usr_add);
					//// check if query is not executed
					if(!$res_add){
						 $flag = false;
						 $error_msg = "Error details 2 : " . mysqli_error($link1) . ".";
					}
				}
				////// insert in activity table////
				$flag = dailyActivity($_SESSION['userid'],$loc,"ENG PINCODE MAPPING","ADD",$_SERVER['REMOTE_ADDR'],$link1,$flag);
				
			}else{
			    $flag = false;
				$cflag = "danger";
				$cmsg = "Failed";
				$msg = " This location is not valid.".$loc;
				///// move to parent page
				header("location:pin_re_mapping.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
				exit;
			}
			
		}else{
			$flag = false;
			$cflag = "danger";
			$cmsg = "Failed";
			$msg = " This Pincode(".$picode.") and area(".$po_area.") is not valid or blank.";
			///// move to parent page
			header("location:pin_re_mapping.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
			exit;
		}
	}////// end of for loop

	if($flag){
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = " Successfully Uploaded ";
    }else{
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = " Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	///// move to parent page
	header("location:pin_re_mapping.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
<!---------
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 
 ---------------->
 
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
<!---------------
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
---------->
<script src="../js/jquery-1.10.1.min.js"></script>
<script src="../js/fileupload.js"></script>

</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
   include("../includes/leftnav2.php");
	
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-link"></i> ENG PINCODE RE-MAPPING </h2>
      <div style="display:inline-block;float:right"><span class="red_small">Download Template : </span><a href="../templates/ENG_PINCODE_MAPPING.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/></a></div><div><a href="../excelReports/eng_pincode_mapping_report.php" title="Export pincode details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export pincode details in excel"></i></a></div><br></br> 
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
            <div class="col-md-12"><label class="col-md-4 control-label">Status<span class="red_small">*</span></label>
              <div class="col-md-4">
              	<select name="status" id="status" required class="form-control required" >
                <?php foreach($arrstatus as $key => $value){?>
                	<option value="<?=$key?>" <?php if($sel_result['status'] == $key) { echo 'selected'; }?>><?=$value?></option>
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
              <!---<div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>----->
            </div>
          </div>

         <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
            </div>
          </div> 
		  
		  <div class="form-group">
		  	<div class="col-md-2"></div>
            <div class="col-md-8" align="left">
				<br><br>
				<div><strong><u>Follow these steps for accurate result</u></strong></div>
				<br>
				<div><span class="red_small"> * Attach only given template.</span></div>
				<div><span class="red_small"> * Template should be <strong>.xlsx (Excel Workbook)</strong> format only.</span></div>
                <div><span class="red_small"> * Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
				<div><span class="red_small"> * Special character or space are not allowed into <strong>PINCODE</strong> field, Use only numeric characters.</span></div>
				<div><span class="red_small"> * Special character are not allowed into <strong>AREA NAME</strong> field, Use space between words.</span></div>
				<div><span class="red_small"> * Into the <strong>AREA TYPE</strong> field, Use only <strong>Local</strong> or <strong>Upcountry</strong>.</span></div>
				<div><span class="red_small"> * Into the <strong>AREA TYPE</strong> field, Avoid these words (local / LOCAL / upcountry / UPCOUNTRY).</span></div>
            </div>
			<div class="col-md-2"></div>
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