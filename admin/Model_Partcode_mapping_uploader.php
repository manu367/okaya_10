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
              $brand_id=  $sheet->getCellByColumnAndRow(0,$row)->getValue();
              $product_id= $sheet->getCellByColumnAndRow(1,$row)->getValue();
              $model_id = $sheet->getCellByColumnAndRow(2,$row)->getValue();
              $partcode = $sheet->getCellByColumnAndRow(3,$row)->getValue();
              if($model_id!='' and $partcode!=''){  
                echo "Select brand_id,brand from brand_master where brand='$brand_id' and status='1'";

                $check_brand=mysqli_fetch_assoc(mysqli_query($link1,"Select brand_id,brand from brand_master where brand='$brand_id' and status='1'"));
                //echo $check_brand['brand_id'].'<- brand id';
                if(count($check_brand)==0)
                {
                  echo "Flag->1".$flag;
                  $flag=false;
                  $err_msg = "Incorrect Brand Name";  
                  mysqli_rollback($link1);
                  $cflag = "danger";
                  $cmsg = "Failed";
                  header("location:model_master.php?msg=".$err_msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
                  exit; 

                }
                //echo "Select product_id,product_name from product_master where product_name='$product_id' and status='1'";
//  exit;
                $check_product=mysqli_fetch_assoc(mysqli_query($link1,"Select product_id,product_name from product_master where product_name='$product_id' and status='1'"));
                if(count($check_product)==0)
                {
                  echo "Flag->2".$flag;
                  $flag=false;
                  $err_msg = "Incorrect Product Name"; 
                  mysqli_rollback($link1);
                  $cflag = "danger";
                  $cmsg = "Failed";
                  header("location:model_master.php?msg=".$err_msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
                  exit;                 
                }
                
				
                $check_partcode=mysqli_fetch_assoc(mysqli_query($link1,"Select partcode from partcode_master where partcode='$partcode' and status='1' and product_id='".$check_product['product_id']."' and brand_id='".$check_brand['brand_id']."'"));
                if(count($check_partcode)==0)
                {
                  echo "Flag->3".$flag;
                  $flag=false;
                  $err_msg = "Product ID OR Brand ID Not Mapped With Partcode";                 
                }      
				
				$check_model=mysqli_fetch_assoc(mysqli_query($link1,"Select model_id from model_master where model_id='$model_id' and status='1' and product_id='".$check_product['product_id']."' and brand_id='".$check_brand['brand_id']."'"));
                if(count($check_model)==0)
                {
                  echo "Flag->3".$flag;
                  $flag=false;
                  $err_msg = "Product ID OR Brand ID Not Mapped With Model";                 
                }    
				         
                if((count($check_partcode)>0)&& (count($check_brand)>0) && (count($check_product)>0) && (count($check_model)>0) )
                {
                  $check_part=mysqli_fetch_assoc(mysqli_query($link1,"Select partcode from partcode_master where partcode='$partcode' and status='1'"));     
                  if(count($check_part)==0)
                  {
                  echo "Flag->4".$flag;
                  $flag=false;
                  $err_msg = "Wrong Partcode.";                 
                  }             
                  if(count($check_part)>0)
                  {
                    $check_model_partcode=mysqli_query($link1,"Select partcode ,model_id from partcode_master where partcode='$partcode' and status='1' and FIND_IN_SET('$model_id',model_id)");                
                    $check_model_partcode_val=mysqli_fetch_assoc($check_model_partcode);
                    if(mysqli_num_rows($check_model_partcode)==0)
                    {
                      $array_mappedmodel ='';
                      $previous_partcode=mysqli_fetch_assoc(mysqli_query($link1,"select model_id from partcode_master where partcode ='".$partcode."'"));
                      //echo $previous_partcode['model_id'];
                      //echo "old";
                      if($previous_partcode['model_id']=='#N/A')
                      {
                        $previous_partcode['model_id']='';
                        $array_mappedmodel =$previous_partcode['model_id'].$model_id;
                      }
                      if($previous_partcode['model_id']=='')
                      {
                        $previous_partcode['model_id']='';
                        $array_mappedmodel =$model_id;
                      }
                      if($previous_partcode['model_id']!='')
                      {
                        $array_mappedmodel = $previous_partcode['model_id'].",".$model_id;
                      }
                       echo $array_mappedmodel;
                      $usr_add="UPDATE partcode_master set model_id ='".$array_mappedmodel."' , updatedate='".date("Y-m-d H:i:s")."',updateby='".$_SESSION['userid']."'  where partcode = '".$partcode."'";
                      $res_add=mysqli_query($link1,$usr_add);                    
                      //// check if query is not executed
                      if (!$res_add) {                         
                          $error_msg = "Error details1: " . mysqli_error($link1) . ".";
                      }
                      //// insert in activity table////
                      $flag = dailyActivity($_SESSION['userid'],$partcode,"MODEL","UPDATE",$_SERVER['REMOTE_ADDR'],$link1,$flag);
                    }//check model is present in partmaster
                    else
                    {
                       echo $flag."Flag->5";
                      $flag = false;
                      $err_msg = "Model Already Mapped";
                    }
                  }// chck part                    
                }                
              }// Empty Modal
              else
              {
                echo $flag."Flag->6";
                $flag = false;
                $err_msg = "Empty Model OR Partcode.";
              }   
            }//For Loop                     
            if ($flag)
            {
                echo $flag."Flag->7";
                mysqli_commit($link1);
                $cflag = "success";
                $cmsg = "Success";
                $msg = "Successfully Uploaded ";
            }
            else
            {
              echo $flag."Flag->8";
                mysqli_rollback($link1);
                $cflag = "danger";
                $cmsg = "Failed";
                $msg = $err_msg." ! Request could not be processed. Please try again." ;
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
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <title><?=siteTitle?></title>
 <!-- <script src="../js/jquery.min.js"></script> -->
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <!-- <script src="../js/bootstrap.min.js"></script> -->
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <!-- <link rel="stylesheet" href="../css/bootstrap-select.min.css"> -->
 <!-- <script src="../js/bootstrap-select.min.js"></script> -->
 
 <!-- <link rel="stylesheet" href="../css/jquery.dataTables.min.css"> -->
 <!-- <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script> -->
<script src="../js/frmvalidate.js"></script>
<!-- <script type="text/javascript" src="../js/jquery.validate.js"></script> -->
 <script type="text/javascript" src="../js/common_js.js"></script>
 <!-- <link rel="stylesheet" href="../css/datepicker.css"> -->
<!-- <script src="../js/jquery-1.10.1.min.js"></script> -->
<!-- <script src="../js/bootstrap-datepicker.js"></script> -->
<script src="../js/fileupload.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
   include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-upload"></i>Model Part Mapping Uploader </h2>
      <div style="display:inline-block;float:right"><a href="../templates/PARTCODE_MODEL_MAPPING.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/></a></div>	<br></br> 

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
                        <input type="file"  name="file"  required class="form-control"  accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/ > 
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