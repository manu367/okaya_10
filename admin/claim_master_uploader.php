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
					$productname = $sheet->getCellByColumnAndRow(0,$row)->getValue();
					$product_id = getAnyDetails($productname,"product_id","product_name","product_master",$link1);
                    $brandname = $sheet->getCellByColumnAndRow(1,$row)->getValue();
					$brand = getAnyDetails($brandname,"brand_id","brand","brand_master",$link1);
					$levelname = $sheet->getCellByColumnAndRow(2,$row)->getValue();
					$level = getAnyDetails($levelname,"id","name","repair_level",$link1);
					$level_typename = $sheet->getCellByColumnAndRow(3,$row)->getValue();
					$level_type = getAnyDetails($level_typename,"locationtypeid","displayname","location_type_master",$link1);
					$level_value = $sheet->getCellByColumnAndRow(4,$row)->getValue();
					$party = $sheet->getCellByColumnAndRow(5,$row)->getValue();
					$status = "1";
					/////////brand check
					if($product_id!=''){
					if($brand!=''){
						if($level!=''){
							if($level_type!=''){
              $sel_claim="select * from claim_master where brand_id = '".$brand."' and product_id ='".$product_id."' and level_type='".$level_type."' and level='".$level."' and party='".$party."' ";
	$cliam_res12=mysqli_query($link1,$sel_claim)or die("error1".mysqli_error($link1));
	   if(mysqli_num_rows($cliam_res12)==0){
     $usr_add="INSERT INTO  claim_master set   brand_id = '".$brand."',product_id ='".$product_id."',level_type='".$level_type."',level='".$level."',level_value='".$level_value."' ,update_by ='".$_SESSION['userid']."',status='".$status."',party='".$party."'";
    $res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));  
	   if (!$res_add) {
		 $flag = false;
		 $error_msg = "Error details add: " . mysqli_error($link1) . ".";
	}
	////// insert in activity table////
		   if($flag){
	$flag=dailyActivity($_SESSION['userid'],"","Claim","ADD",$ip,$link1,$flag);
	   if (!$flag) {
		 $flag = false;
		 $error_msg = "Error details add daily: " . mysqli_error($link1) . ".";
	}
		   }
	////// return message
	   }///////check for data
							}///level tpye
							else {
							$flag = false;
		 				$error_msg = "Type is not available: " . $level_typename. ".";
							}
						}////level
						else {
						$flag = false;
		 				$error_msg = "Repair level is not available: " . $levelname . ".";
						}
					}//product check
						else {
							$flag = false;
		 				$error_msg = "Product is not available: " . $productname . ".";
						}
					}//////brand check
					else {
						$flag = false;
		 				$error_msg = "Brand is not available: " . $brandname . ".";
					}
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
		$msg = "Request could not be processed. Please try again.".$error_msg;
	} 
    mysqli_close($link1);
	///// move to parent page
	header("location:list_claim_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
      <h2 align="center"><i class="fa fa-upload"></i>Upload New Claim </h2>
      <div style="display:inline-block;float:right"><a href="../templates/UPLOAD_CLAIM_LEVEL.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/></a></div>	<br></br> 

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
                   <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='list_claim_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'"> 
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