<?php 
    require_once("../includes/config.php");
            //error_reporting(-1);
            //ini_set('display_errors', 'On');
              
            //////////////// after hitting upload button
//print_r('ddddddddddd');exit;

@extract($_POST);

if(isset($_POST['Submit']) && $_POST['Submit']=="Upload"){

mysqli_autocommit($link1, false);

$flag = true;

if ($_FILES["attchfile"]["error"] > 0)

{

$code=$_FILES["attchfile"]["error"];

}

else

{

		// check type 08-08-2024 

	 $temp = explode(".", $_FILES["attchfile"]["name"]);
	 $extension = end($temp);
	 $allowedTypes = ['xlsx'];
			//print_r($extension);exit;

	if(!in_array($extension, $allowedTypes))
	{
		//print_r($extension);exit;
		$flag = false;
		$err_msg = "Error Code1.12:Please upload a valid file, only xlsx files are allowed!". mysqli_error($link1);
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed.Please try again.".$err_msg;
		header("location:manual_serial_uploader.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
        exit;
		//exit;
	}
	
	// end check type 08-08-2024 
	
	require_once "../includes/simplexlsx.class.php";
	   
	$xlsx = new SimpleXLSX( $_FILES['attchfile']['tmp_name'] );	
	move_uploaded_file($_FILES["attchfile"]["tmp_name"],"../ExcelExportAPI/upload/".$now.$_FILES["attchfile"]["name"]);
	$f_name=$now.$_FILES["attchfile"]["name"];
	   

chmod ($f_name, 0755);

}



$filename=$f_name;

////////////////////////////////////////////////// code to import file/////////////////////////////////////////////////////////////

			//Checking data

			$part_count=0;

			$error_msg = "";

					

			$browserid=session_id();

			

			

			
            $sql='SELECT MAX(temp_no)  AS temp_no FROM `manual_serial_upload`';

                    //echo $sql."<br><br>";
//print_r('ddddddd');exit;
                    $result = mysqli_query($link1,$sql);
                    $fetch = mysqli_fetch_assoc($result);
                    //// Step 2 Add 1 in max temp no.
                    $nextno = $fetch['temp_no']+1;
                    ///// Step 3 Make document no. like AD/date('Ymd')/ 5 digit padding left of 0 on step 2 variable
                    $systemdocno = 'MN/'.date('Ymd').'/'.str_pad($nextno ,5,0,STR_PAD_LEFT);
                    //// step 4 insert in master table with update value step 2 in temp no. column

                    //echo "INSERT INTO `manual_serial_upload` SET `doc_no` = '".$systemdocno."', `temp_no`='". $nextno."',`upload_by`='".$_SESSION['userid']."', `upload_date`='".$datetime."', `upload_remark`='".$_POST['remark']."', `upload_ip`='".$ip."',`file_name`='".$f_name."'"."<br><br>";

                    $res1 = mysqli_query($link1,"INSERT INTO `manual_serial_upload` SET `doc_no` = '".$systemdocno."', `temp_no`='". $nextno."',`upload_by`='".$_SESSION['userid']."', `upload_date`='".$datetime."', `upload_remark`='".$_POST['remark']."', `upload_ip`='".$ip."',`file_name`='".$f_name."'");
                    if(!$result){
                        $flag = false;
                        $error_msg = "Error details1: " . mysqli_error($link1) . ".";
                    }

                    //Fetch data from location_master
                    $sql1="SELECT locationtype FROM `location_master` WHERE location_code='".$_SESSION['asc_code']."'";

                    //echo $sql1."<br><br>";

                    $locationTypeResult = mysqli_query($link1,$sql1);
                    $locationTypeResults = mysqli_fetch_assoc($locationTypeResult);
					$j=0;	  
                    $myXlsxData = $xlsx->rows();
                    unset($myXlsxData[0]);
                    $myXlsxData = array_unique(array_map('trim', array_column($myXlsxData,3)));
                    $myXlsxDataDiff =$locationTypeResults['locationtype'];
			  //// Check location is active /////
                     if($myXlsxDataDiff==''){
                        $msg = "Unwanted Location Code Found! ".implode(",",$myXlsxDataDiff);
                    }else {
					 if(!empty($xlsx->rows()) && count(array_unique(array_column($xlsx->rows(),0)))!=count($xlsx->rows())){
                        $sr_nos=array_count_values(array_column($xlsx->rows(),0));
                        foreach($sr_nos as $key => $nos){
                            if($nos>1){
                                $duplicate_SN_no[]=$key;
                            }
                        }
                    }else {
						 $d=[];	
						

           // for($row =2 ;$row <= $highest;$row++){	
	        list($cols) = $xlsx->dimension();	

				foreach( $xlsx->rows() as $k => $r) {
				 if ($k == 0 || $k == 1) continue; // skip first and second row 
				  for( $i = 0; $i < count($k); $i++)
				  {
					if($r[0]=='' && $r[1]==''){

					  }
					  else if($r[0]=="EOF"){
						   $eof="1";
					  }else{
            $d[] = $r[0];
		    $sr_no =$r[0];///// sr_no
            $part_id =$r[1];///// part_no
			$model_id =$r[1];///// model_no
						  //print_r($sr_no);exit;
						 
						 
						 
						 if($myXlsxDataDiff=="WH"){
                                $sql3="SELECT imei1 from imei_details WHERE imei1='".$sr_no."' ";

                                //echo $sql3."<br><br>";

                                $result3 = mysqli_query($link1,$sql3);
                                $fetch3 = mysqli_fetch_assoc($result3);
                                if(empty($fetch3)){
                                    $queryInsert= "INSERT INTO `imei_details` SET  `imei1`='". $sr_no."',`imei2`='',`challan_no`='',`partcode`='".$part_id."',`model_id`='".$model_id."',`location_code`='".$_SESSION['asc_code']."',`status`='1',`stock_type`='OK',entry_date='$today',dist_date='$today',receive_date='$today',grn_no='".$systemdocno."'";

                                    //echo $queryInsert."<br><br>";

                                    mysqli_query($link1,$queryInsert);
                                }else{
                                    $duplicate_SN_no[]=$sr_no;
                                }
                            }else if($myXlsxDataDiff=="ASP"){
								
                                $sql3="SELECT imei1 from imei_details_asp WHERE `imei1`='".$sr_no."' ";

                                //echo  $sql3."<br><br>";

                                $result3 = mysqli_query($link1,$sql3); 
                                $fetch3 = mysqli_fetch_assoc($result3);
                                if(empty($fetch3)){
                                    $queryInsert="INSERT INTO `imei_details_asp` SET  `imei1`='". $sr_no."',`partcode`='".$part_id."',`model_id`='".$model_id."',`location_code`='".$_SESSION['asc_code']."',`status`='1',`stock_type`='OK',entry_date='$today',grn_no='".$systemdocno."'";

                                    //echo $queryInsert."<br><br>";

                                    mysqli_query($link1,$queryInsert); 
                                }else{
                                    $duplicate_SN_no[]=$sr_no;
                                }
                            }else{
                                $locationTypeError[]=$fields[3];
                            }
					 
//print_r($duplicate_SN_no);exit;
					 	
					 
					 
						 
						 
						 
						 
						 
						 
					}  				
         }
		}	////// end of for loop  
						 
						 
				   if(empty($xlsx->rows())){
                        $err_msg = "No Data Found To Insert";
                        $chkflag = "danger";
                        $chkmsg = "Failed";
					   $flag = false;
                    }else if(count($xlsx->rows())==2){
                        $err_msg = "No Data Found To Insert";
                        $chkflag = "danger";
                        $chkmsg = "Failed";
					   $flag = false;
                    }else if($myXlsxDataDiff==''){
                        $err_msg = "Unwanted Location Code Found! ".implode(",",$myXlsxDataDiff);
                        $chkflag = "danger";
                        $chkmsg = "Failed";
					   $flag = false;
                    }else if(!empty($duplicate_SN_no)){
                       // mysqli_rollback($link1);
                        $err_msg = "Duplicate Serial Number Found! ".implode(",",$duplicate_SN_no);
                        $chkflag = "danger";
                        $chkmsg = "Failed";
					   $flag = false;
                    }else if(!empty($locationTypeError)){
                       // mysqli_rollback($link1);
                        $err_msg = "Undefined LocationType! ".implode(",",$locationTypeError);
                        $chkflag = "danger";
                        $chkmsg = "Failed";
					   $flag = false;
                    }else{
                       // mysqli_commit($link1);
                        $err_msg = "Manual Serial uploaded successfully";
                        $chkflag  = "success";
                        $chkmsg = "Success";

                    }		 
						 
					 
					 }
					 
					 
					 
					 }

			  

		//print_r($flag);	exit;

     

	if ($flag) {

        mysqli_commit($link1);

		$cflag = "success";

		$cmsg = "Success";

        $msg = "Successfully Uploaded ";

    } else {

		mysqli_rollback($link1);

		$cflag = "danger";

		$cmsg = "Failed";

		$msg = "Request could not be processed.Please try again.".$err_msg;

	} 

    mysqli_close($link1);

	///// move to parent page

	header("location:manual_serial_uploader.php?msg=".$msg."&chkflag=".$cflag."&doc_type=".$_POST['doc_type']."&chkmsg=".$cmsg."".$pagenav);

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
       <!-- <script src="../js/jquery-1.10.1.min.js"></script>-->
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
                    <h2 align="center"><i class="fa fa-upload"></i>Upload Serial</h2>
                    <?php if(!empty($_REQUEST['msg'])){?>
                        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
                        </div>
                    <?php }?>
                    <form action="manual_serial_uploader.php" method="post" accept-charset="utf-8" enctype="multipart/form-data">

                        <div class="col-md-12" style="text-align: center;">
                            <?php if(!empty($_REQUEST['data_arr'])){ ?>
                                Duplicate Data <a href="../excelReports/get_unloaded_data.php?data_arr=<?=base64_encode($_REQUEST['data_arr'])?>" title="Export Report in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Report details in excel"></i></a>
                            <?php } ?>
                        </div>

                        <div class="col-md-12">
                           	<div style="display:inline-block;float:right"><a href="../templates/Template_SerialNo_Uploader.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/></a></div>
                        </div><br></br> 

                        <div class="form-group"  id="page-wrap" style="margin-left:10px;">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
                                    <div class="col-md-4">
                                        <div>
                                           <input type="file" name="attchfile" value="" placeholder="Accept Only xlsx File"  accept=".xlsx" class="form-control">
                                            <br>         
                                        </div>       
                                    </div>
                                    <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx</strong> file</span></div>
                                    </div>
                                </div>
                                <div class="col-md-12">   
                                <label class="col-md-4 control-label">Remark</label> 
                                <textarea class="col-md-4 control-textarea form-control" name="remark" id="remark" width="100%" height="300px" placeholder="Give Remark Here !" style="width: 50%;"></textarea>
                            </div>
                            <div class="col-md-12 ">
                            <br>
                            <div class="col-md-10 ">
                                <center>
                                    <input type="submit" name="Submit" class="btn btn-primary mt-4" value="Upload"> <a href="manual_serial.php" class="btn btn-primary">Back</a>
                                </center>
                            </div>
                            </div>
                            </div>
                        </div>
                    </form>  
                </div>
            </div>
        </div>
        <?php
                     include("../includes/footer.php");
            include("../includes/connection_close.php");
        ?>
    </body>
</html>