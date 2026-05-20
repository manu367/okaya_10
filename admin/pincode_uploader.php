<?php 
require_once("../includes/config.php");
$arrstatus = getFullStatus("master",$link1);
//////////////// after hitting upload button
@extract($_POST);
if($_POST['Submit']=="Upload"){
	mysqli_autocommit($link1, false);
	$flag = true;
	
	////// find state and city data ////
	$state = $_POST['locationstate'];
	$city = $_POST['locationcity'];
	
	if ($_FILES["file"]["error"] > 0){
		$code=$_FILES["file"]["error"];
	}else{
		$model = $_POST['model'];
		move_uploaded_file($_FILES["file"]["tmp_name"],
		"../ExcelExportAPI/create_pincode/".$today.$_FILES["file"]["name"]);
		$file="../ExcelExportAPI/create_pincode/".$today.$_FILES["file"]["name"];
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
		$picode = $sheet->getCellByColumnAndRow(0,$row)->getValue();
		$po_area = $sheet->getCellByColumnAndRow(1,$row)->getValue();
		
		/////// check all variable is not blank ///////////
		if($state != "" && $city != "" && $picode != "" && $po_area != ""){
		
			$sel_usr = "select * from pincode_master where stateid = '".$state."' and cityid = '".$city."' and  pincode = '".$picode."' and  area = '".$po_area."'  ";
			
			$sel_res12=mysqli_query($link1,$sel_usr)or die("error 1 ".mysqli_error($link1));
			$sel_result=mysqli_fetch_assoc($sel_res12);		
			
			if(mysqli_num_rows($sel_res12)>0){
				$pin_upld = "update pincode_master set stateid = '".$state."', cityid = '".$city."', pincode = '".$picode."', area = '".$po_area."', statusid = '1' where stateid = '".$state."' and cityid = '".$city."' and  pincode = '".$picode."' and  area = '".$po_area."'  ";
	
				$res_upld = mysqli_query($link1,$pin_upld);
				//// check if query is not executed
				if(!$res_upld){
					 $flag = false;
					 $error_msg = "Error details 1 : " . mysqli_error($link1) . ".";
				}
			}else{
				$pin_add = "INSERT INTO pincode_master set stateid = '".$state."', cityid = '".$city."', pincode = '".$picode."', area = '".$po_area."', statusid = '1' ";
				
				$res_add=mysqli_query($link1,$pin_add);
				//// check if query is not executed
				if(!$res_add){
					 $flag = false;
					 $error_msg = "Error details 2 : " . mysqli_error($link1) . ".";
				}
			}
		
		}
		
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$_SESSION['userid'],"PINCODE UPLOAD","ADD",$_SERVER['REMOTE_ADDR'],$link1,$flag);
				
	}////// end of for loop

	if($flag){
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "Pincodes Successfully Uploaded ";
    }else{
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	///// move to parent page
	header("location:pincode_uploader.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
 <script>  
  /////////// function to get city on the basis of state
 function get_citydiv(){
	  var name=$('#locationstate').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{state_new:name},
		success:function(data){
	    $('#citydiv').html(data);
	    }
	  });
 }

</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
<script src="../js/jquery-1.10.1.min.js"></script>
<!----------
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>------>
<script src="../js/fileupload.js"></script>

</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
   include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-map-pin"></i> PIN Uploader </h2>
      <div style="display:inline-block;float:right"><span class="red_small">Download Template : </span><a href="../templates/PINCODE_UPLOADER.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/></a></div>	<br></br> 
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
            <div class="col-md-12"><label class="col-md-4 control-label">State<span class="red_small">*</span></label>
              <div class="col-md-4">
                <select name="locationstate" id="locationstate" class="form-control required"  onchange="get_citydiv();" required >
					  <option value=''>--Please Select--</option>
					  <?php 
					 $state_query="select stateid, state from state_master where countryid='1' order by state";
					 $state_res=mysqli_query($link1,$state_query);
					 while($row_res = mysqli_fetch_array($state_res)){?>
					   <option value="<?=$row_res['stateid']?>"<?php if($sel_result['stateid']==$row_res['stateid']){ echo "selected";}?>><?=$row_res['state']?></option>
					 <?php }?> 	
                 </select>      
              </div>
            </div>
          </div>
		  
		  <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">City<span class="red_small">*</span></label>
              <div class="col-md-4" id="citydiv">
                  <select name="locationcity" id="locationcity" class="form-control required" required >
				  	<option value=''>--Please Select--</option>
    				<?php 
						 $city_query="SELECT cityid, city FROM city_master where stateid='".$sel_result['stateid']."' and cityid='".$sel_result['cityid']."'";
						 $city_res=mysqli_query($link1,$city_query);
						 while($row_city = mysqli_fetch_array($city_res)){
					?>
						<option value="<?=$row_city['cityid']?>"<?php if($sel_result['cityid']==$row_city['cityid']){ echo "selected";}?>><?=$row_city['city']?></option>
						<?php }	?>
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
              <!---<div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>--->
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