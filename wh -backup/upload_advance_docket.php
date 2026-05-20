<?php 
require_once("../includes/config.php");
if(isset($_FILES['import'])){
	$path=$_SERVER['REQUEST_URI'];	
	////// transaction parameter initialization
	$flag =true;
	$error_msg = "";
	mysqli_autocommit($link1, false);
	$fileName = $_FILES["import"]["tmp_name"];
	$bulk_data=array();
    if ($_FILES["import"]["size"] > 0) {
		$file = fopen($fileName, "r");
		$response=array();
		$i=0;                    
		$filePath='ExcelExportAPI/upload_adv_docket/'.$_FILES["import"]["name"];
		move_uploaded_file($fileName, "../".$filePath);
		///// array initialize for duplicate docket
		$arr_docket = array();
        while(($data = fgetcsv($file, 10000, ",")) !== FALSE ) {
			if($i>0)
			{
				if($data[2]!=""){
					$arr_aspdet[$data[1]][] = $data[0]."~".$data[2]."~".$data[3]."~".$data[4]."~".$data[5]."~".$data[6];
				/*  0 ASP Name	
					1 ASP Code	
					2 Docket No.	
					3 Docket Company 
					4 Courier Code
					5 Mode Of Transport
					6 Response Message
				*/
					$arr_docket[] = $data[2];
				}
			}
            $i++;
      	}//// close while loop
		//// make unique docket array
		$uniq_docket = array_unique($arr_docket);
		////// check unique docket in excel
		if(count($uniq_docket) == count($arr_docket) ){
			//// start script saving
			foreach($arr_aspdet  as $aspcode => $asp_arrval){
				///// Step 1 max temp no. pick from master table
				$sql='SELECT MAX(temp_no)  AS temp_no FROM `advance_docket_assign`';
				$result = mysqli_query($link1,$sql);
				$fetch = mysqli_fetch_assoc($result);
				//// Step 2 Add 1 in max temp no.
				$nextno = $fetch['temp_no']+1;
				///// Step 3 Make document no. like AD/date('Ymd')/ 5 digit padding left of 0 on step 2 variable
				$systemdocno = 'AD/'.date('Ymd').'/'.str_pad($nextno ,5,0,STR_PAD_LEFT);
				//// step 4 insert in master table with update value step 2 in temp no. column
				$res1 = mysqli_query($link1,"INSERT INTO `advance_docket_assign` SET `doc_no` = '".$systemdocno."', `doc_date`='".$datetime."', `assign_from`='".$_POST['assign_from']."', `assign_to`='".$aspcode."', `assign_by`='".$_SESSION['userid']."', `assign_remark`='".$_POST['remark']."', `assign_ip`='".$ip."', `status`='Pending', `file_name`='".$filePath."', `temp_no`='".$nextno."'");
				if(!$res1){
					$flag = false;
					$error_msg = "ER1".mysqli_error($link1);
				}
				for($j=0; $j < count($asp_arrval); $j++){
					//echo $asp_arrval[$j]."<br/>";
					///// extract excel upload value
					$ext_val = explode("~",$asp_arrval[$j]);
					///// check docket no. is already exist or not
					if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM advance_docket_upload WHERE docket_no='".$ext_val[1]."'"))==0){
						///// insert in data table with document no. generated in step 3
						$res2 = mysqli_query($link1,"INSERT INTO `advance_docket_upload` SET `asp_name` = '".$ext_val[0]."', `asp_code`='".$aspcode."', `docket_no`='".$ext_val[1]."', `docket_company`='".$ext_val[2]."', `courier_code`='".$ext_val[3]."', `mode_of_transport`='".$ext_val[4]."', `response_msg`='".$ext_val[5]."', `doc_no`='".$systemdocno."', `doc_date`='".$datetime."'");
						if(!$res2){
							$flag = false;
							$error_msg = "ER2".mysqli_error($link1);
						}    
					}else{
						$flag = false;
						$error_msg = "Some docket no. are already uploaded. Please upload unique docket. ".$ext_val[1];
					}   
				}//// inner for loop of foreach close
			}//// close foreach loop
		}else{
			$flag = false;
			$error_msg = "There are duplicate docket no. in uploading excel file. Please upload unique docket.";
		}
		////// check if all query executed successfully
		if($flag){
			mysqli_commit($link1);
			$msg = "Advance docket is uploaded successfully with ref no. <b>".$systemdocno."</b>";
			$chkflag  = "success";
			$chkmsg = "Success";
		}else{
			mysqli_rollback($link1);
			$msg = "Request could not be processed ".$error_msg;
			$chkflag = "danger";
			$chkmsg = "Failed";
		}
		///// move header or redirect
		header("Location:upload_advance_docket.php?msg=".$msg."&chkflag=".$chkflag."&chkmsg=".$chkmsg."".$pagenav);
		exit;
	}    
}
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
        <script src="../js/bootstrap-select.min.js"></script>
        <script>
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
                    include("../includes/leftnavemp2.php");
                ?>
                <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
                    <h2 align="center"><i class="fa fa-tags"></i>Upload Advance Docket</h2><div style="display:inline-block;float:right"><a href="../templates/UPLOAD_ADVANCE_DOCKET.csv" title="Download Excel Template"><i class="fa fa-file-excel-o fa-3x"></i> Download Template</a></div><br/>
                    <?php if($_REQUEST['msg']){?><br>
                    <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                        </button>
                            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
                        </div>
                    <?php }?>
                    <div class="form-group"  id="page-wrap" style="margin-left:10px;">
                    <form action="" name="frm1"  id="frm1" class="form-horizontal" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    	<div class="form-group">
                            <div class="col-md-10"><label class="col-md-4 control-label">Assign From <span class="red_small">*</span></label>
                              <div class="col-md-6">
                                 <select name="assign_from" class="form-control required" required>
                                    <option value="">--Please select--</option>
                                    <?php
                                    $res_wh = mysqli_query($link1,"SELECT location_code,locationname FROM location_master WHERE locationtype='WH' ORDER BY locationname");
                                    while($row_wh = mysqli_fetch_assoc($res_wh)){ 
                                    ?>
                                    <option value="<?=$row_wh["location_code"]?>"><?=$row_wh["locationname"].",".$row_wh["location_code"]?></option>
                                    <?php
                                    }
                                    ?>
                            	</select>
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-md-10"><label class="col-md-4 control-label">Attach File <span class="red_small">*</span></label>
                              <div class="col-md-3">
                                 <input type="file" name="import" value="" class="form-control span2 required" placeholder="Accept Only CSV File"  accept=".csv">
                              </div>
                              <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.csv (CSV )</strong> file</span></div>
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-md-10"><label class="col-md-4 control-label">Remark</label>
                              <div class="col-md-6">
                                 <textarea class="form-control" name="remark" id="remark" style="resize:vertical" placeholder="Enter Remark"></textarea>
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-md-12" align="center">
                              <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Upload'){?>disabled<?php }?>>
                              &nbsp;&nbsp;&nbsp;
                              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='advance_docket_upload.php?<?=$pagenav?>'">
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