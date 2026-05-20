<?php
require_once('../includes/config.php');
//////////////// after hitting upload button
@extract($_POST);
if($_POST['Submit']=="Save"){
		mysqli_autocommit($link1, false);
		$flag = true;

 //  update  query into  imei import data table
           $sql = "update  imei_data_import  set imei1 = '".$_POST['imei1']."' ,imei2 = '".$_POST['imei2']."' ,import_date = '".$_POST['import_date']."',model_id = '".$_POST['model']."' ,  activation_date = '".$_POST['activation_date']."' where imei1 = '".$_POST['searchimei']."'  or imei2 = '".$_POST['searchimei']."' ";
	 
		$result =	mysqli_query($link1,$sql);
	//// check if query is not executed
		   if (!$result) {
	           $flag = false;
               echo "Error details: " . mysqli_error($link1) . ".";
           }		   	   
      
	   
	   if ($flag) {
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "Successfully Updated details  ";
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);

	   ///// move to parent page
     header("location:dop_date_change.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
     exit;


}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/jquery.js"></script>
 <script src="../js/bootstrap.min.js"></script>
 <script type="text/javascript" src="../js/moment.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
  <script>
	$(document).ready(function(){
        $("#frm1").validate();
    });
 </script>
 <script language="javascript" type="text/javascript">
 $(document).ready(function() {	
    /////// if user enter contact no. then search button  should be enabled
	 $("#imei_no").keyup(function() {
		 if($("#imei_no").val()!=""){ 
			$("#Submit").attr("disabled",false);
		 }else{
			 $("#Submit").attr("disabled",true);
		 }
    });
 });

$(document).ready(function () {
	$('#import_date').datepicker({
		format: "yyyy-mm-dd",
        todayHighlight: true,
		autoclose: true
	});
});

$(document).ready(function () {
	$('#activation_date').datepicker({
		format: "yyyy-mm-dd",
        todayHighlight: true,
		autoclose: true
	});
});


 </script>
  <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
       <h2 align="center"><i class="fa fa-calendar"></i> DOP Date Change</h2><br/><br/>
	   <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
          </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php }?>    
      	<div class="form-group"  id="page-wrap" style="margin-left:10px;"> 
			<form id="frm1" name="frm1" class="form-horizontal" action="" method="post">         
              <div class="form-group">
                <div class="col-md-10"><label class="col-md-4 control-label">Enter IMEI No.</label>
                  <div class="col-md-6">
                     <input type="text" name="imei_no" class="digits form-control" id="imei_no" maxlength="30"  value="<?=$_REQUEST['imei_no']?>" placeholder="Enter only IMEI No."/>
                  </div>
                </div>
              </div>
               <div class="form-group">
                <div class="col-md-10"><label class="col-md-4 control-label"></label>
                  <div class="col-md-6">
                     <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="Submit" value="Search" title="Search" disabled>
                  </div>
                </div>
              </div>
          	</form>
			<?php if($_POST['Submit']=="Search" && ($_POST['imei_no']!='')){
			$sql = mysqli_query($link1,"select * from imei_data_import where imei1 = '".$_POST['imei_no']."'  or imei2 = '".$_POST['imei_no']."' ");

			/////// (1)  if imei no exist in imeidata import table/////////////////////////////////////////////////////////////////////
			if (mysqli_num_rows($sql) >=1)
				{	
				$row =mysqli_fetch_array($sql);					
			?>
            <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Model</label>
              <div class="col-md-4">
                 <select name="model" id="model"   class="form-control" >
                <option value=''>--Please Select-</option>
				<?php $sql =mysqli_query($link1,"select distinct(model_id), model from model_master where status = '1'  ");
				 while($model = mysqli_fetch_array($sql)){ ?>
				 <option value="<?=$model['model_id']?>" <?php if($row['model_id'] == $model['model_id']) {echo "selected" ;}?>><?php echo $model['model'];?></option>				 
				 <?php
				 }
				?>
                 </select>
              </div>
            </div>
          </div>   		        
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">IMEI1 </label>
              <div class="col-md-4">
			  <input type="text" id="imei1" name="imei1" class="form-control"  value="<?=$row['imei1']?>" >
                              
                </div>
              </div>
          </div>		  
		  <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">IMEI2 </label>
              <div class="col-md-4">
			  <input type="text" id="imei2" name="imei2" class="form-control"  value="<?=$row['imei2']?>" >                            
                </div>
              </div>
          </div>
		   <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Import Date</label>
              <div class="col-md-4">
			  <div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="import_date"  id="import_date" style="width:150px;"  value="<?php if($_REQUEST['import_date']!='' && $_REQUEST['import_date']!='0000-00-00'){ echo $_REQUEST['start_date'];}else{ echo $row['import_date']; }?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
               
                </div>
              </div>
          </div>
		  <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">DOP/Activation Date</label>
              <div class="col-md-4">
			  <div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="activation_date"  id="activation_date" style="width:150px;"  value="<?php if($_REQUEST['activation_date']!='' && $_REQUEST['activation_date']!='0000-00-00'){ echo $_REQUEST['activation_date'];}else{ $row['activation_date'];}?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
                </div>
              </div>
          </div>
         <div class="form-group">
            <div class="col-md-12" align="center">
			<input type="hidden" value="<?=$_POST['imei_no']?>" id="searchimei" name="searchimei" >
              <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Save" title="" <?php if($_POST['Submit']=='Save'){?>disabled<?php }?>>
              &nbsp;&nbsp;&nbsp;
              
            </div>
          </div> 
   <?php }?>
   <?php 
			
					if(mysqli_num_rows($sql)==0) {					
					?>
					<div class="panel panel-info">
              		<div class="panel-heading" align="center">Search History</div>
				  	<div class="panel-body">  
					<?php 
					echo "<strong>Not found in database.</strong>"
							?>
					<?php  }  				
					?>					
  </div>
            </div>  
			 </form>   
       <?php } ?>
      </div>
	  </div>
	  </div>
	  </div>  
</div>
</div>
</div>
</div>
</body>
</html>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>