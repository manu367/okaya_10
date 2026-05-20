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
"../ExcelExportAPI/upload/".$now.$_FILES["file"]["name"]);
$file="../ExcelExportAPI/upload/".$now.$_FILES["file"]["name"];
chmod ($file, 0755);
}
	$tY=date("Y");
	$tM=date("m");
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
				//Checking data
				$part_count=0;
				$error_msg = "";
			
				
	


	
              for($row =2 ;$row <= $highest;$row++){
					
                    $cust_b_id = trim($sheet->getCellByColumnAndRow(0,$row)->getValue()); ///customer in use 
                    $reg_date = trim($sheet->getCellByColumnAndRow(1,$row)->getValue()); ///// QTY
					 $reg_time = trim($sheet->getCellByColumnAndRow(2,$row)->getValue()); ///// QTY
					 $call_id = trim($sheet->getCellByColumnAndRow(3,$row)->getValue()); ///customer in use 
                    $call_date = trim($sheet->getCellByColumnAndRow(4,$row)->getValue()); ///// QTY
					 $call_time = trim($sheet->getCellByColumnAndRow(5,$row)->getValue()); ///// QTY
					  $cust_name_add = trim($sheet->getCellByColumnAndRow(6,$row)->getValue());
					    $cust_add = trim($sheet->getCellByColumnAndRow(7,$row)->getValue());
						  $cust_state = trim($sheet->getCellByColumnAndRow(8,$row)->getValue());
						   $cust_reg = trim($sheet->getCellByColumnAndRow(9,$row)->getValue());
						     $cust_pin = trim($sheet->getCellByColumnAndRow(10,$row)->getValue());
							   $cust_loc = trim($sheet->getCellByColumnAndRow(11,$row)->getValue());
							     $cust_mobile= trim($sheet->getCellByColumnAndRow(12,$row)->getValue());
								  $cust_model= trim($sheet->getCellByColumnAndRow(13,$row)->getValue());
								    $job_dop= trim($sheet->getCellByColumnAndRow(14,$row)->getValue());
									   $job_serial= trim($sheet->getCellByColumnAndRow(15,$row)->getValue());
									    $job_type= trim($sheet->getCellByColumnAndRow(16,$row)->getValue());
										 $job_Source= trim($sheet->getCellByColumnAndRow(17,$row)->getValue());
										  $cust_type= trim($sheet->getCellByColumnAndRow(18,$row)->getValue());
										  $entity_type= trim($sheet->getCellByColumnAndRow(19,$row)->getValue());
				
				
		$address = cleanData($cust_add);
		$cust_name = cleanData($cust_name_add);
		
		
	
				if( $call_id!='')	
				{
					/////product check is active
					//$mysqk_model=mysqli_query($link1,"select product_id,brand_id,model,model_id,wp from model_master where ven_code='".$cust_model."' and status='1'");
					$mysqk_model=mysqli_query($link1,"select product_id,brand_id,model,model_id,wp from model_master where model_id='".$cust_model."' and status='1'");
					$model_check=mysqli_num_rows($mysqk_model);
					$row_model=mysqli_fetch_array($mysqk_model);
					if($model_check>0)
					{
					    /////Brand check is active

					$usr_srch_st="select stateid from state_master  where state='".$cust_state."'";
$result_usr_st=mysqli_query($link1,$usr_srch_st);
$arr_usr_st=mysqli_fetch_array($result_usr_st);		
if(mysqli_num_rows($result_usr_st)==0){
	 $flag = false;

		 $error_msg = "State not Found  $cust_state: " . mysqli_error($link1) . ".";
}					
										    ///fetch  data form partcode master 		
											//////////////////////////////customer details//////////////////////////////////////////
$usr_srch="select mobile,customer_id from customer_master where mobile='".$cust_mobile."'";
$result_usr=mysqli_query($link1,$usr_srch);
$arr_usr=mysqli_fetch_array($result_usr);	
if ((mysqli_num_rows($result_usr)==0) ){	
// also save customer details \\ 	
$sel_uid="select max(max_id) from customer_master";
$res_uid=mysqli_query($link1,$sel_uid);
$arr_result2=mysqli_fetch_array($res_uid);
$code_id=$arr_result2[0]+1;
$pad=str_pad($code_id,5,"0",STR_PAD_LEFT);
$customer_id="C".$stCode.$pad;

	echo $usr_add="insert into customer_master set  customer_id='".$customer_id."', customer_name='".$cust_name."', address1='".$address ."', pincode='". $cust_pin."', cityid='".$locationcity."',  stateid='".$arr_usr_st['stateid']."', mobile='".$cust_mobile."' ,update_date='".$today."', update_by='".$_SESSION['asc_code']."',max_id='".$code_id."',type='".$cust_type."',b_cust_id='".$cust_b_id."',b_reg_date='".$reg_date."',reg_time='".$reg_time."',area='".$cust_reg."'";
$res_add=mysqli_query($link1,$usr_add); 

	if (!$res_add) {

		 $flag = false;

		 $error_msg = "Error detailsuser: " . mysqli_error($link1) . ".";

	}

$cust_id=$customer_id;
}else{
	$cust_id=$arr_usr['customer_id'];
	}
	
	//// Product Register \\\\\
		//echo "select * from product_registered where serial_no='$serial_no'<br />";
		$usr_product="select serial_no from product_registered where serial_no='".$job_serial."'";
		$result_product=mysqli_query($link1,$usr_product);
		///// if found \\\\\
		if (mysqli_num_rows($result_product)==0){
		
	 $usr_add3="INSERT INTO product_registered set serial_no='".$job_serial."', customer_id='".$cust_id."', product_id='".$row_model['product_id']."', model_id='".$row_model['model_id']."', purchase_date='".$job_dop."', status='1',mobile_no='".$cust_mobile."',brand_id='".$row_model['brand_id']."'";
		$res_add3=mysqli_query($link1,$usr_add3);
		
		}
		
		
		
		
		$warraty_day = daysDifference($call_date ,$job_dop);
		if( $warraty_day <=  $row_model['wp'] ){
		$ws_status='IN';
		} else{
		$ws_status='OUT';
		}
		
$brand_row=getAnyDetails($row_model['brand_id'],"brand","brand_id","brand_master",$link1);
$val_brand=substr($brand_row,0,2);
$val_y=substr($tY,2,2);
	$res_jobcount = mysqli_query($link1,"SELECT job_count, job_series from job_counter where location_code='".$_SESSION['asc_code']."'");

	$row_jobcount = mysqli_fetch_assoc($res_jobcount);

	///// make job sequence

	$nextjobno = $row_jobcount['job_count'] + 1;

	$jobno = $row_jobcount['job_series']."".$val_brand."".$val_y."".$tM."".str_pad($nextjobno,4,0,STR_PAD_LEFT);

	//// first update the job count

	$res_upd = mysqli_query($link1,"UPDATE job_counter set job_count='".$nextjobno."' where location_code='".$_SESSION['asc_code']."'");

	//// check if query is not executed

	if (!$res_upd) {

		 $flag = false;

		 $error_msg = "Error details1: " . mysqli_error($link1) . ".";

	}
	
	//$aging = daysDifference($today,$job_dop);
	
	$mappin="select location_code,area_type  from location_pincode_access where pincode='".$cust_pin."' and statusid='1'";
$result_pin=mysqli_query($link1,$mappin);
$arr_pin=mysqli_fetch_array($result_pin);
if(mysqli_num_rows($result_pin)==0){

 $flag = false;

		 $error_msg= "Pincode not mapped Please check:" .$cust_pin;

}
	
		$st="status='1', sub_status='1'";

	  $sql_inst = "INSERT INTO jobsheet_data set job_no='".$jobno."', system_date='".$today."', location_code='".$_SESSION['asc_code']."', city_id='".$locationcity."', state_id='".$arr_usr_st['stateid']."', pincode='".$cust_pin."', product_id='".$row_model['product_id']."', brand_id='".$row_model['brand_id']."', customer_type='".$cust_type."', model_id='".$row_model['model_id']."', model='".$row_model['model']."', imei='".$job_serial."', open_date='".$today."', open_time='".$currtime."', warranty_status='".$ws_status."',warranty_days='".$warraty_day."', dop='".$job_dop."',call_type='".$job_Source."', call_for='".$job_type."', customer_name='".$cust_name."',  contact_no='".$cust_mobile."',address='".$address."', created_by='".$_SESSION['userid']."', ".$st." ,ip='".$ip."',customer_id='".$cust_id."',entity_type='".$entity_type."',area_type='".$arr_pin['area_type']."',pen_status='2',ticket_no ='".$call_id."',b_cust_id='".$cust_b_id."',b_reg_date='".$reg_date."',reg_time='".$reg_time."',current_location='".$arr_pin['location_code']."',area='".$cust_reg."',m_job_date='".$call_date."',m_time='".$call_time."'";

	$res_inst = mysqli_query($link1,$sql_inst);
	
	//// check if query is not executed

	if (!$res_inst) {

		 $flag = false;

		 $error_msg = "Error jobsheet : " . mysqli_error($link1) . ".";

	}
	
			
	$flag = callHistory($jobno,$_SESSION['asc_code'],"1","Complaint Login","Complaint Login",$_SESSION['userid'],$warranty_status,$remark,"","",$ip,$link1,$flag);

	////// insert in activity table////

	$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB","CREATE",$_SERVER['REMOTE_ADDR'],$link1,$flag);				 
										
					}///// product check 
					 else {
					 $flag = false;
					  $error_msg= "Model No is not Present In Data base :" .$cust_model;
					 
					 }	
				}////qty check
				else {
						$flag = false;
					  $error_msg= "Call Id is Blank";
				     }	 				 
			
					
       		}////// end of for loop 
				
					  
					
		///////////Close Document type
	   if ($flag) {
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "Successfully Uploaded ";
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed.Please try again.".$error_msg ;
	} 
    mysqli_close($link1);
	   ///// move to parent page
header("location:job_uploader.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
<script src="../js/bootstrap-select.min.js"></script>
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
<script src="../js/jquery-1.10.1.min.js"></script>
<script src="../js/fileupload.js"></script>

<link rel="stylesheet" href="../css/datepicker.css">
  <script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
   include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-angle-double-right"></i>Job Create</h2>
      <div style="display:inline-block;float:right"><a href="../templates/job_uploader.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/> Download Excel Template</a></div><br></br> 

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
            <div class="col-md-12"><label class="col-md-4 control-label"></label>
              <div class="col-md-4">
             
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label"></label>
              <div class="col-md-4">
           
              </div>
            </div>
          </div>
         
		  <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label"></label>
              <div class="col-md-4">
               
			  </div>
            </div>
          </div>
        
          <div class="form-group">
            <div class="col-md-12">
              <label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
              <div class="col-md-4">
                  <div>
                    <label >
                       <span>
                        <input type="file"  name="file" class="form-control required"    accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"  / > 
                    </span>
                    </label>             
                </div>
              </div>
              <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
            </div>
          </div>
		  
		            <div class="form-group">
            <div class="col-md-12">
             <table class="table table-bordered" width="100%">
  <tr>
    <td colspan="2"><strong>Instractions:</strong></td>
   
  </tr>
  <tr>
   <td>1.&nbsp;</td>
    <td>Date Format should be "YYYY-MM-DD" Like :"2019-01-23".</td>
   
   
  </tr>
  <tr>
   <td>2.&nbsp;</td>
  <td>Time Format Should be Hs:Mn:S PM Like  12:49:01 PM.</td>
  </tr>
  <tr>
    <td>3.&nbsp;</td>
    <td>Call source should be (Presidential,Social Media,Web,Call Center,SMS Feedback,Walkin).</td>
    
  </tr>
  <tr>
    <td>4.</td>
    <td>Call Type Should be (Repair,Installation,Demo,AMC Booking,Stock Repair,Maintenance service,Accessories Purchase,Replacement Handling,Refurbishing,Workshop).</td>
 
  </tr>
    <tr>
    <td>4.</td>
    <td>Please enter Entity Type Id Form Entity Master Otherwise Enter "Others".  </td>
 
  </tr>
</table>

            </div>
          </div>
         <div class="form-group">
            <div class="col-md-12" align="center">&nbsp;&nbsp;&nbsp;
              
              <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
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