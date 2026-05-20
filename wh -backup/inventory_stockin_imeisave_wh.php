<?php
require_once("../includes/config.php");
/////////////// decode challan no /////////////////////////////////
$getid = base64_decode($_REQUEST['refid']);
@extract($_POST);
if($_POST){
//// initialize transaction parameters
if ($_POST['upd']=='Receive'){
$flag=true;
mysqli_autocommit($link1, false);
$error_msg="";
	//////////  get no. of rows //////////////////////////////////////
	
	for ($i=1 ; $i<$noofrows ; $i++){
		$partcode= 'partcode'.$i;
  		$imei1 = 'imei1'.$i;
		$imei2 = 'imei2'.$i;
	  	$status = 'status'.$i;
		$model = 'model'.$i;
		
		///// condition to enter only those rows where status is selected //////////////////////////////////////////////
		if ($_POST[$status] != '' ){
		
	///////////////////  update into imei _detail table //////////////////////////////////////////////////////////////
	
	$upd_imeidetail = mysqli_query($link1,"update imei_details_asp set status_type = '".$_POST[$status]."' , receive_date = '".$today."' , status ='4'   where challan_no = '".$getid."' and partcode = '".$_POST[$partcode]."' ") or die ("err1".mysqli_error($link1));
	//// check if query is not executed
				if (!$upd_imeidetail) {
					$flag = false;
					$error_msg = "Error details1: " . mysqli_error($link1) . ".";
				}
		
		//////////////   update imei detail asp only when if selected ok and damage status //////////////////////////////////////
		if($_POST[$status] == 'okqty' || $_POST[$status] == 'damage' ){
		
			$upd_imeidetailasp = mysqli_query($link1,"insert into  imei_details set imei1 ='".$_POST[$imei1]."' ,imei2 = '".$_POST[$imei2]."' ,grn_no = '".$getid."' , partcode= '".$_POST[$partcode]."' , model_id ='".$_POST[$model]."' ,status ='1' , stock_type='".$_POST[$status]."', location_code='".$_SESSION['asc_code']."' , entry_date = '".$today."'"	) or die ("err2".mysqli_error($link1));
	//// check if query is not executed
				if (!$upd_imeidetailasp) {
					$flag = false;
					$error_msg = "Error details2: " . mysqli_error($link1) . ".";
				}
			
			}
			
			//////// inset in imei history table//////////////////////////////////////////
					
			$result__2 = mysqli_query($link1,"INSERT INTO imei_history set imei1='".$_POST[$imei1]."',imei2='".$_POST[$imei2]."',partcode='".$_POST[$partcode]."',transaction_no='".$getid."',remark='IMEI Receive' ,location_code='".$_SESSION['asc_code']."' ") or die ("err3".mysqli_error($link1)); 
				//// check if query is not executed
				if (!$result__2) {
					$flag = false;
					$error_msg = "Error details3: " . mysqli_error($link1) . ".";
				}
			
			/////////////////////////////////////////////////////
			/////////////  update imei_attach flag y to R  in billing product item/////////////////////////////	
			
			
			$upd_item = mysqli_query($link1,"update billing_product_items set imei_attach='R' where challan_no='".$getid."' and partcode='".$_POST[$partcode]."' ") or die ("err4".mysqli_error($link1));
			

	//// check if query is not executed
				if (!$upd_item) {
					$flag = false;
					$error_msg = "Error details4: " . mysqli_error($link1) . ".";
				}
			
		}
	
}

	if($flag){
		$cflag="success";
		$cmsg="Success";
		$msg="You have successfully Receive Imei With challan no ".$getid;
		mysqli_commit($link1);
	}
	else{
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	}
	mysqli_close($link1);
   header("location:stock_in_salereturn.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."&refid='".base64_encode($getid)."'&doc_type=&status=".$pagenav);
   exit;
   }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>
<?=siteTitle?>
</title>
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
      <h2 align="center"><i class="fa fa-inbox"></i> IMEI Receive</h2>
      <br/>
      <br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
        <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <table width="100%"  class="table table-bordered"  align="center" cellpadding="4" cellspacing="0" border="1">
              <thead>
                <tr>
                  <th width="15%" style="text-align:center"><label class="control-label">Sno</label></th>
                  <th width="15%" style="text-align:center"><label class="control-label">Item Description</label></th>
                  <th width="15%" style="text-align:center"><label class="control-label">Model</label></th>
                  <th width="15%" style="text-align:center"><label class="control-label">IMEI 1</label></th>
                  <th width="15%" style="text-align:center"><label class="control-label">IMEI 2</label></th>
                  <th width="15%" style="text-align:center" ><label class="control-label">Status</label></th>
              </thead>
              <?php 
 $sel_tras="select * from imei_details_asp where challan_no = '".$getid."'  and  partcode = '".$_REQUEST['partcode']."'  ";
$sel_res12=mysqli_query($link1,$sel_tras)or die("error1".mysqli_error($link1));
$j=1;
while($imei_det = mysqli_fetch_array($sel_res12)){ ?>
              <tr>
                <td width="15%" style="text-align:center"><label class="control-label"><?=$j?></label></td>
                <td width="15%" style="text-align:center"><label class="control-label"><?=getAnyDetails($imei_det['partcode'],"part_name","partcode","partcode_master",$link1)?><input type="hidden" name="partcode<?=$j?>"  id="partcode<?=$j?>" value="<?=$imei_det['partcode']?>"/></label></td>
                <td width="15%" style="text-align:center"><label class="control-label"><?=getAnyDetails($imei_det['model_id'],"model","model_id","model_master",$link1)?><input type="hidden" name="model<?=$j?>"  id="model<?=$j?>" value="<?=$imei_det['model_id']?>"/></label></td>
                <td width="15%" style="text-align:center"><label class="control-label"><?=$imei_det['imei1']?><input type="hidden" name="imei1<?=$j?>"  id="imei1<?=$j?>" value="<?=$imei_det['imei1']?>"/></label></td>
                <td width="15%" style="text-align:center"><label class="control-label"><?=$imei_det["imei2"]?><input type="hidden" name="imei2<?=$j?>"  id="imei2<?=$j?>" value="<?=$imei_det['imei2']?>"/></label></td>
              <td width="15%" style="text-align:center"><label class="control-label"><select id="status<?=$j?>" name="status<?=$j?>" class="form-control required"><option value="">Please Select</option><option value="okqty">OK</option><option value="missing">Missing</option><option value="damage">Damage</option></select></label> </td>
                
              </tr>
              <?php 	$j++; }?>             
                </tbody>             
            </table>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Receive" title="Receive">
               <input name="noofrows" id="noofrows" type="hidden" value="<?=$j?>"/>
                  <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               	   <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='stock_in_salereturn.php?refid=<?=base64_encode($getid)?>=&doc_type=&status=<?=$status?><?=$pagenav?>'">
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