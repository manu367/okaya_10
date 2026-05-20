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
               /*echo '<script>alert("le fichier a t charg avec succes !");</script>';*/
                $sheet = $objPHPExcel->getSheet(0); //we specify the sheet to use
                $highestRow = $sheet->getHighestRow();//we select all the rows used in the sheet 
                $highestCol = $sheet->getHighestColumn();// we select all the columns used in the sheet
                $indexCol = PHPExcel_Cell::columnIndexFromString($highestCol); //////// count no. of column 
				$highest = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow(); //////////////// count no of rows in excel
				
		///////////////////////// initialize parameter	
		
		///////////////////////////////////////// insert into grn master table
		//// pick max count of grn

		/////////////////////////////// Checking The qty///////////////////////////////////////////////
		
			  $flag1=1;
$today = date("Y-m-d-H-i-s");
$up_file = "../ExcelExportAPI/upload/un_upload_Imei_dispatch_report_ftp".$today.".xlsx";
$Content = "";
$handle = fopen($up_file, 'x+');
	
		$Content.= "IMEI1."."\t";
	
		$Content.= "Status"."\t";
		$Content.=("\n");
		
for($row =2 ;$row <= $highest;$row++) {
$counter+= count($row);
}
if($counter > $req_qty || $counter < $req_qty){
$alert1="a";
	mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
	echo 	$msg = "Please upload the record according to partcode qty.<br/>Please upload the correct detail.";
		echo "<input type='button' value='Close this window' onclick='self.close()'>";
}
else{

	        //importing files to the database
                for($row =2 ;$row <= $highest;$row++)
                {
					
					
					

					$modelid = $sheet->getCellByColumnAndRow(0,$row)->getValue();
                    $imei1 = $sheet->getCellByColumnAndRow(1,$row)->getValue();
                    $imei2 = $sheet->getCellByColumnAndRow(2,$row)->getValue();
					
					
					$partcode = getAnydetails("$modelid","model_id","partcode","partcode_master",$link1);
					$productid = getAnydetails("$modelid","product_id","model_id","model_master",$link1);
					$brandid = getAnydetails("$modelid","brand_id","model_id","model_master",$link1);
					
	             
					
			//////// first check whether enter model id in excel  is correct or not ////////////////////////
		
			//////////////////////// then check ime1  and imei2 already exist in table 
					
                   //inserting query into data base
				 //  echo "select imei1, imei2 from imei_details where imei1 in ('$imei1', '$imei2')  or  imei2 in ('$imei1', '$imei2') ";
				 //echo "select imei1, imei2 from imei_details where status='1' and  location_code='".$_SESSION['asc_code']."' and imei1 ='$imei1' ";
	$imeinfo	 = mysqli_query($link1, "select imei1, imei2,model_id from imei_details_asp where status='1' and  location_code='".$_SESSION['asc_code']."' and imei1 ='$imei1' ");
	
	$imeirow=mysqli_fetch_array($imeinfo);
	
	 if(mysqli_num_rows($imeinfo)==0){
		  
		  $flag1*=0;
          
		  $Content.=$imei1."\t";
		
		  $Content.="IMEI Not In your Stock"."\t";
		  $Content.= "\n";
	  }
	  
	  	else if($imeirow['partcode']!=$part_code){
		  
		  $flag1*=0;
          
		  $Content.=$imei1."\t";
		
		  $Content.="IMEI or model not matched"."\t";
		  $Content.= "\n";
	  }
		else {
			  $flag1*=1;
			
                  $sql = "INSERT INTO imei_details(imei1,imei2,partcode , model_id,location_code,status , entry_date)VALUES('".$imei1."','".$imei2."','".$part_code."','".$imeirow['model_id']."','".$_POST['location']."','1','".$today."')";
			$result =	mysqli_query($link1,$sql);
			 //// check if query is not executed
	
					  $imei_update="update imei_details_asp set status='3',dis_date='".$today."',challan_no='".$_REQUEST['challan']."' where    imei1 ='$imei1'  and location_code='".$_SESSION['asc_code']."' ";
$result6 =	mysqli_query($link1,$imei_update);

   $podata_sql="update billing_product_items set attach_file='Y'  where challan_no='".$_REQUEST['challan']."' and partcode='".$part_code."' ";
	$result7 =	mysqli_query($link1,$podata_sql);
}
				}
				
				
				    if($flag1==0){
							mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		 fwrite($handle, $Content);
      fclose($handle);
	  	$msg = "Please upload Again.";
//echo"<p class='style2 style6' align=center><br/>This Serial Nos. already in Uploaded.($serial)<br/></p>";	
        echo "<p align='center'><a href='$up_file'><img src='downloadsmall.gif'>Download Un Processed Data file</a></p>";
		
					}
					else {
						  mysqli_commit($link1);
		               $cflag = "success";
		               $cmsg = "Success";
                       $msg = "Successfully Uploaded ";
						echo "<BODY onLoad='window.close(); window.opener.location.reload(true);'></BODY>";
					
						
						}
	
		  mysqli_close($link1);
}
				
}
			
			    	   


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
	
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-upload"></i>Upload Imei</h2><div style="display:inline-block;float:right"><a href="../templates/PO_DISPATCH_IMEI.xlsx" title="Download Excel Template"><i class="fa fa-download"> &nbsp;Download Templete</i> </div>	
      <br></br> 

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
                        
                         <input name="challan" type="hidden" class="form-control" id="challan" value="<?=$_REQUEST['challan_no']?>"/>	
                          <input name="location" type="hidden" class="form-control" id="challan" value="<?=$_REQUEST['to_location']?>"/>	
                       <input name="part_code" type="hidden" class="form-control" id="part_code" value="<?=$_REQUEST['part_code']?>"/>
                             <input name="req_qty" type="hidden" class="form-control" id="req_qty" value="<?=$_REQUEST['req_qty']?>"/>			
                        
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