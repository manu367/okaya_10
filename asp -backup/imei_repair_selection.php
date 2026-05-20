<?php
require_once("../includes/config.php");
/////get status//
$today=date("Y-m-d",$time_zone);
$arrstatus = getFullStatus("master",$link1);
$partid = base64_decode($_REQUEST['partcode']);
@extract($_POST);


	//// initialize transaction parameters

    if ($_POST['upd']=='Make Job'){
	
	//// initialize transaction parameters

	$flag = true;

	mysqli_autocommit($link1, false);

	$error_msg="";
	//print_r( $_POST['list']);
	if($_POST['list']){}else{
	 $flag = false;
	   	$cflag = "info";
		$cmsg = "Warning";
   		$msg = "Please select atleast one handset...";


}

	///// entry in job sheet data
foreach($_POST['list'] as $tmp=>$value){
    ///////// update counter 
	$part_code="partcode".$value;
	$model_code="model_id".$value;	
    $imei_id1="imei1".$value;
	$imei_id2="imei2".$value;	   
		$model_detail=getAnyDetails($_POST[$model_code],"product_id,brand_id,model","model_id","model_master",$link1);
	$model= explode("~",$model_detail);
	
		//// pick max count of job

	$res_jobcount = mysqli_query($link1,"SELECT job_count from job_counter where location_code='".$_SESSION['userid']."'");

	$row_jobcount = mysqli_fetch_assoc($res_jobcount);

	///// make job sequence

	$nextjobno = $row_jobcount['job_count'] + 1;

	$jobno = $_SESSION['userid']."".str_pad($nextjobno,4,0,STR_PAD_LEFT);

	//// first update the job count

	$res_upd = mysqli_query($link1,"UPDATE job_counter set job_count='".$nextjobno."' where location_code='".$_SESSION['userid']."'");

	//// check if query is not executed

	if (!$res_upd) {

		 $flag = false;

		 $error_msg = "Error details1: " . mysqli_error($link1) . ".";

	}

	
		$sql_inst = "INSERT INTO jobsheet_data set job_no='".$jobno."', system_date='".$today."', location_code='".$_SESSION['asc_code']."', city_id='".$_SESSION['cityid']."', state_id='".$_SESSION['stateid']."', pincode='".$_SESSION['zipcode']."', product_id='".$model[0]."', brand_id='".$model[1]."', customer_type='Company Handset', model_id='".$_POST[$model_code]."', partcode='".$_POST[$part_code]."', model='".$model[2]."', imei='".$_POST[$imei_id1]."', sec_imei='".$_POST[$imei_id2]."',open_date='".$today."', open_time='".$currtime."', warranty_status='IN', dop='".$today."', dname='".$_SESSION['uname']."',  call_type='Normal', call_for='Company Stock', customer_name='".$_SESSION['uname']."', phy_cond='Used', els_status ='OK', created_by='".$_SESSION['userid']."',  status='1', sub_status='1',ip='".$ip."'";

	$res_inst = mysqli_query($link1,$sql_inst);

	//// check if query is not executed

	if (!$res_inst) {

		 $flag = false;

		 $error_msg = "Error details2: " . mysqli_error($link1) . ".";

	}
	
	/////////////////////// IMEI stock update//////////////////////////////////////
		  $imei_update="update imei_details_asp set status='3',dis_date='".$today."',challan_no='".$jobno."' where    imei1 ='".$_POST[$imei_id1]."'  and location_code='".$_SESSION['asc_code']."' ";
$result6 =	mysqli_query($link1,$imei_update);

	$sql_invt = "UPDATE client_inventory set faulty = faulty-1,updatedate='" . $datetime . "' where partcode='".$_POST[$part_code]."' and location_code='".$_SESSION['asc_code']."'";
   				$res_invt = mysqli_query($link1,$sql_invt);
			  	//// check if query is not executed
			  	if (!$res_invt) {
					$flag = false;
					$error_msg = "Error details5: " . mysqli_error($link1) . ".";
				}
				
				$flag = stockLedger($jobno,$today,$_POST[$part_code],$_SESSION['asc_code'],$_SESSION['asc_code'],"OUT","faulty","Assgin To job","Job Create",'1',"",$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);


   	///// entry in call/job  history

	$flag = callHistory($jobno,$_SESSION['asc_code'],"1","Job Create","Job Create",$_SESSION['userid'],"IN",$remark,$ip,$link1,$flag);

	////// insert in activity table////

	$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB","CREATE",$_SERVER['REMOTE_ADDR'],$link1,$flag);
 
}

 
 
 if($flag){
 
		$cflag="success";
		$cmsg="Success";
		$msg="You have successfully Created job for selected IMEI's";
		mysqli_commit($link1);
}else{


 mysqli_rollback($link1);
 	mysqli_close($link1);
//header("location:sfr_bucket.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);

}
  



   ///// move to parent page
header("location:job_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
 exit;
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script>
$(document).ready(function(){
        $("#frm1").validate();
});
$(document).ready(function () {
	$('#release_date').datepicker({
		format: "yyyy-mm-dd",
		//startDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
});
 function checkAll(field){
   for (i = 0; i < field.length; i++)
        field[i].checked = true ;
 }
 function uncheckAll(field){

   for (i = 0; i < field.length; i++)
   
        field[i].checked = false ;
 }
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="col-sm-8">
      <h2 align="center"><i class="fa fa-reply-all  fa-lg"></i> IMEI Details </h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label"></label>
                <div class="col-md-6"></div>
            </div>
			
              <div class="col-md-6">
                  <div class="form-buttons" style="float:right">
                <input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm1.list)" value="Check All" />
                <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm1.list)" value="Uncheck All" />
                </div>
              </div>
            </div>
          
          <div class="form-group">
          
                
               	   <table width="100%"  class="table table-bordered"  align="center" cellpadding="4" cellspacing="0" border="1">
               <thead>
                  <tr>
                    <th width="15%" style="text-align:center"><label class="control-label">Sno</label></th>
                   <th width="15%" style="text-align:center"><label class="control-label">IMEI 1</label></th>
                    <th width="15%" style="text-align:center"><label class="control-label">IMEI 2</label></th>
                    <th width="15%" style="text-align:center"><label class="control-label">Model</label></th>
                    <th width="15%" style="text-align:center"><label class="control-label">Part Name</label></th>
					
					 <th width="15%" style="text-align:center"><label class="control-label">Confrim</label></th>
                 </thead>
       
				  
				 <?PHP 	 $sel_tras="select * from imei_details_asp where status ='1' and location_code='".$_SESSION['asc_code']."' and partcode='".$partid."'";
	$sel_res12=mysqli_query($link1,$sel_tras)or die("error1".mysqli_error($link1));
	$j=1;
                 while($imei = mysqli_fetch_array($sel_res12)){ ?>
				 <tr>
				 
				   <td width="15%" style="text-align:center"><label class="control-label"><?=$j?></label></td>
                    <td width="15%" style="text-align:center"><label class="control-label"><?=$imei['imei1']?>  <input type="hidden" name="imei1<?=$imei['id']?>" class="number form-control" id="imei1<?=$imei['id']?>" value="<?=$imei['imei1']?>"/></label></td>
                    <td width="15%" style="text-align:center"><label class="control-label"><?=$imei['imei2']?><input type="hidden" name="imei2<?=$imei['id']?>" class="number form-control" id="imei2<?=$imei['id']?>" value="<?=$imei['imei2']?>"/></label></td>
                    <td width="15%" style="text-align:center"><label class="control-label"><?=getAnyDetails($imei["model_id"],"model","model_id","model_master",$link1)?><input type="hidden" name="model_id<?=$imei['id']?>" class="number form-control" id="model_id<?=$imei['id']?>" value="<?=$imei['model_id']?>"/>
               </label>  </td>
					 <td width="15%" style="text-align:center"><?=getAnyDetails($imei["partcode"],"part_name","partcode","partcode_master",$link1)?><input type="hidden" name="partcode<?=$imei['id']?>" class=" form-control" id="partcode<?=$imei['id']?>" value="<?=$imei['partcode']?>"/></td>
					 <td width="15%" style="text-align:center"> <input type="checkbox" checked="checked"  name="list[]"  id="list" value="<?=$imei['id']?>" /></td>
				 </tr>
				 <?php 	$j++; }?>
				 
				 <tr><td colspan="6">&nbsp;</td></tr>
				
				  </tbody>
              </table> 
            
          
             
          </div>
        
       
           
           
          <div class="form-group">
            <div class="col-md-12" align="center">
          
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Make Job" title="Make Job">
             
              
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='assgin_can_repair.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
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