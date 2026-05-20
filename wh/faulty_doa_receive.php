<?php 
require_once("../includes/config.php");
//////////////// after hitting upload button
@extract($_POST);
if($_POST['Submit']=="Upload"){
if ($_FILES["file"]["error"] > 0)
{
$code=$_FILES["file"]["error"];
}
else
{
$location_code = $_POST['location_code'];
move_uploaded_file($_FILES["file"]["tmp_name"],
"../ExcelExportAPI/upload/".$today.$_FILES["file"]["name"]);
$file="../ExcelExportAPI/upload/".$today.$_FILES["file"]["name"];
chmod ($file, 0755);
}
	/////////// delete from temp table///////////////////////////////////////////////////////////////
	  		 mysqli_query($link1,"delete from temp_doa_job where 1");
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
               /*echo '<script>alert("le fichier a �t� charg� avec succes !");</script>';*/
                $sheet = $objPHPExcel->getSheet(0); //we specify the sheet to use
                $highestRow = $sheet->getHighestRow();//we select all the rows used in the sheet 
                $highestCol = $sheet->getHighestColumn();// we select all the columns used in the sheet
                $indexCol = PHPExcel_Cell::columnIndexFromString($highestCol); //////// count no. of column 
				$highest = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow(); //////////////// count no of rows in excel
	  		
		
                //importing files to the database
                for($row =2 ;$row <= $highest;$row++)
                {
                    $jobno = $sheet->getCellByColumnAndRow(0,$row)->getValue();
                    $imei = $sheet->getCellByColumnAndRow(1,$row)->getValue();
					
					
										
                    //inserting query into data base
              $sql = "INSERT INTO temp_doa_job (distributer_code , job_no, imei,update_by,update_date ,status)VALUES('".$location_code."','".$jobno."','".$imei."','".$_SESSION['userid']."','".$today."','')";
			$result =	mysqli_query($link1,$sql);							
					     
     			 }////// end of for loop			  
			
		///// move to parent page	   
	  		header("location:doa_jobview.php?dist_code=".$location_code."".$pagenav."");
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
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
<script src="../js/jquery-1.10.1.min.js"></script>
<script src="../js/fileupload.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
   include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-upload"></i> DOA</h2><div style="display:inline-block;float:right">
      <a href="../templates/doa_job_upload.xlsx" title="Download Excel Template"><i class="fa fa-download"> &nbsp;Download Templete</i> </a></div>	<br></br> 

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
            <div class="col-md-12"><label class="col-md-4 control-label">Distributer<span class="red_small">*</span></label>
              <div class="col-md-4">
              <select name="location_code" id="location_code" required class="form-control required" >
                <option value=''>--Please Select--</option>
				<?php
               $res_query="select location_code,locationname from location_master where locationtype ='DIST'";
			        $check1=mysqli_query($link1,$res_query);
                while($br = mysqli_fetch_array($check1)){?>
                <option value="<?=$br['location_code']?>" <?php if($_REQUEST['location_code'] == $br['location_code']) { echo 'selected'; }?>><?=$br['locationname']?></option>
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