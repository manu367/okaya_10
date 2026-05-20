<?php
require_once("../includes/config.php");
/////get status//
$today=date("Y-m-d",$time_zone);
$arrstatus = getFullStatus("master",$link1);
$getid = base64_decode($_REQUEST['refid']);
@extract($_POST);
if($_POST){
	if ($_POST['upd']=='Dispatch'){
		//// initialize transaction parameters
		$flag=true;
		mysqli_autocommit($link1, false);
		$error_msg="";
		if($_POST['list']){
		}else{
			$flag = false;
			$cflag = "info";
			$cmsg = "Warning";
			$msg = "Please select atleast one handset...";

		}
		foreach($_POST['list'] as $tmp=>$value){
    		///////// update counter 	   
 			$sql_count="select * from invoice_counter where location_code='".$_SESSION['asc_code']."'";
			$rs_count=mysqli_query($link1,$sql_count)or die("error1".mysqli_error($link1));
			$selcounter=mysqli_fetch_array($rs_count);
			$max=$selcounter['sfr_count']+1;
			$challan_no= $selcounter['series']."".$selcounter['fy']."".str_pad($max,4,"0",STR_PAD_LEFT);
			$result=mysqli_query($link1,"update invoice_counter set sfr_count=$max  where location_code='".$_SESSION['asc_code']."'");
    		//// Get Address////////////////////////////	
			$to_address=getLocationAddress($to_location,$link1);
			$from_address=getLocationAddress($_SESSION['asc_code'],$link1);
			////////////////////////Get Any detail of From locatio////////////////////////
			$to_detail=getAnyDetails($to_location,"stateid,gstno","location_code","location_master",$link1);
			$to = explode("~",$to_detail);
			$from_detail=getAnyDetails($_SESSION['asc_code'],"stateid,gstno","location_code","location_master",$link1);
			$from= explode("~",$from_detail);
			///////////////////////////////Check partcode//////////////////////////////////
//echo "select partcode,hsn_code,location_price,part_name from partcode_master where partcode='".$partcode."' and  status='Active' ";
$rs_part  = mysqli_query($link1,"select partcode,hsn_code,location_price,part_name from partcode_master where partcode='".$partcode."' and  status='1' ")or die("part error1".mysqli_error($link1));
	//echo "select partcode,hsn_code,distributer_price,name from partcode_master where partcode='".$part."' and  status='Active' ";
	
	//echo "select partcode,hsn_code,distributer_price,name from partcode_master where partcode='".$part."' and  status='Active' ";
        $part = mysqli_fetch_assoc($rs_part) ;
		if($part['partcode']==""){
			$flag=false;
			$error_msg="part not found in partcode master";
		}
        //  get tax on HSN Code
		
		$rs_hsn_tax  = mysqli_query($link1,"select id,cgst,igst,sgst from tax_hsn_master where hsn_code='".$part['hsn_code']."'")or die("part error2".mysqli_error($link1));
		
		//echo "select cgst,igst,sgst from tax_hsn_master where hsn_code='$part[hsn_code]' and  status='Active' ";
        $part_tax = mysqli_fetch_assoc($rs_hsn_tax) ;
		if($part_tax['id']==""){
		
			 $flag = false;
	   	$cflag = "info";
		$cmsg = "Warning";
   		$msg = "Tax not found in HSN TAX MASTER...";
		}
		
 $val=$part['location_price']*1;
		
	$cgst_per=0;
	$cgst_val=0;
	
	$sgst_per=0;
	$sgst_val=0;
	
	$igst_per=0;
	$igst_val=0;
	$tot_val=0;
		if($to[0]== $from[0]){
			//echo "in CGST";
	//----------------------------- CGST & SGST Applicable----------------------//
	if($_SESSION['gstno']!=''){
		$cgst_per=$part_tax['cgst'];
		$sgst_per=$part_tax['sgst'];
	}else{
		$cgst_per="0";
		$sgst_per="0";
	}
	
		
	$cgst_val=($cgst_per*$val)/100;
	$cgst_final_val=$cgst_final_val+$cgst_val;
	
	
	$sgst_val=($sgst_per*$val)/100;
	
	$sgst_final_val=$sgst_final_val+$sgst_val;
$basic_cost=$basic_cost+$val;	
$tot_val=$val+$cgst_val+$sgst_val;
	
}else{
		//echo "in IGST";
	
	//----------------------------- IGST Applicable----------------------//
	if($_SESSION['gstno']!=''){
		$igst_per=$part_tax['igst'];
	}else{
		$igst_per="0";
	}
	
	$igst_val=($igst_per*$val)/100;
	
	$basic_cost=$basic_cost+$val;
	$igst_final_val=$igst_final_val+$igst_val;
	$tot_val=$val+$igst_val;
}
  //----------------------------- Tax Total Amount update in SFR  Challan------------------------//
$inv_tot_cost=$basic_cost+$cgst_final_val+$sgst_final_val+$igst_final_val;
  //--------------------------------- inserting in  SFR Transation------------------------------//
 $sfr_items="insert into sfr_transaction set from_location='".$_SESSION['asc_code']."',to_location='".$to_location."',job_no='".$job_no."',imei='".$imei."',model_id='".$model_id."',part_id='".$partcode."',challan_no='".$challan_no."', hsn_code='".$part['hsn_code']."',qty='1',uom='PCS',cost='".$val."',price='".$part['location_price']."',basic_amt='".$val."',cgst_per='".$cgst_per."',cgst_amt='".$cgst_val."',sgst_per='".$sgst_per."',sgst_amt='".$sgst_val."',igst_per='".$igst_per."',igst_amt='".$igst_val."',total_cost='".$tot_val."',status='1'";
 $sfr_items_qry=mysqli_query($link1,$sfr_items);
 
   if (!$sfr_items_qry) {
    $flag = false;
   $cmsg = "Error details2.1: " . mysqli_error($link1) . ".";
  }
 
 
 ///////////////////////////////Update call history////////////////////////////////////
  $flag = callHistory($job_no,$_SESSION['asc_code'],"414","SFR Dispatched Back To ASP","SFR Dispatched Back To ASP",$_SESSION['userid'],"","","","",$ip,$link1,$flag);
 ///////////////////////////////////////Update SFR Bin//////////////////////////////////////////////
 $up_sfr_bin=mysqli_query($link1,"update sfr_repaired_bin set status='414',challan_no='".$challan_no."'  where sid='".$value."'" );
 
 ///////////////////////update jobsheet data////////////////////////////
 ///echo "update jobsheet_data set status='41'  where job_no='".$job_no."'";
  $up_job=mysqli_query($link1,"update jobsheet_data set sub_status='414'  where job_no='".$job_no."'" );
 
}
$sfr_challan="insert into  sfr_challan set from_location='".$_SESSION['asc_code']."',to_location='".$to_location."',from_gst_no='".$from[1]."',to_gst_no='".$to[1]."',from_address='".$from_address."',to_address='".$to_address."',basic_cost='".$basic_cost."',  total_cost='".$inv_tot_cost."',cgst_amt='".$cgst_final_val."',sgst_amt='".$sgst_final_val."',igst_amt='".$igst_final_val."', challan_no='".$challan_no."',from_state='".$_SESSION['stateid']."',to_state='$to[0]',courier='".$Courier_name."',docket_no='".$doc_code."',status='1',challan_date='".$today."'";
 $sfr_challan_query=mysqli_query($link1,$sfr_challan);
 
 
 if($flag){
 
		$cflag="success";
		$cmsg="Success";
		$msg="You have successfully Dispatch SFR Handset With challan no ".$challan_no;
		mysqli_commit($link1);
}else{

 mysqli_rollback($link2);
 	
  header("location:sfr_bucket.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
}
   }
mysqli_close($link1);
   ///// move to parent page
 header("location:sfr_bucket.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
    exit;
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
      <h2 align="center"><i class="fa fa-truck"></i> Dispatch SFR </h2>
      <br/>
      <br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
        <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-6">
              <label class="col-md-6 control-label">Location Name:-</label>
              <div class="col-md-6">
                <?=$_REQUEST['to_location']?>
              </div>
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
                  <th width="15%" style="text-align:center"><label class="control-label">Job No</label></th>
                  <th width="15%" style="text-align:center"><label class="control-label">IMEI</label></th>
                  <th width="15%" style="text-align:center"><label class="control-label">Model</label></th>
                  <th width="15%" style="text-align:center" ><label class="control-label">Part</label></th>
                  <th width="15%" style="text-align:center"><label class="control-label">Confrim</label></th>
              </thead>
              <?PHP 	$sel_tras="select * from sfr_repaired_bin where to_location='".$_REQUEST['to_location']."'";
	$sel_res12=mysqli_query($link1,$sel_tras)or die("error1".mysqli_error($link1));
	$j=1;
                 while($sfr = mysqli_fetch_array($sel_res12)){ ?>
              <tr>
                <td width="15%" style="text-align:center"><label class="control-label">
                    <?=$j?>
                  </label></td>
                <td width="15%" style="text-align:center"><label class="control-label">
                    <?=$sfr['job_no']?>
                    <input type="hidden" name="job_no" class="number form-control" id="job_no" value="<?=$sfr['job_no']?>"/>
                  </label></td>
                <td width="15%" style="text-align:center"><label class="control-label">
                    <?=$sfr['imei']?>
                    <input type="hidden" name="imei" class="number form-control" id="imei" value="<?=$sfr['imei']?>"/>
                  </label></td>
                <td width="15%" style="text-align:center"><label class="control-label">
                    <?=getAnyDetails($sfr["model_id"],"model","model_id","model_master",$link1)?>
                    <input type="hidden" name="model_id" class="number form-control" id="model_id" value="<?=$sfr['model_id']?>"/>
                  </label></td>
                <td width="15%" style="text-align:center"><?=getAnyDetails($sfr["part_id"],"part_name","partcode","partcode_master",$link1)?>
                  <input type="hidden" name="partcode" class="number form-control" id="partcode" value="<?=$sfr['part_id']?>"/></td>
                <td width="15%" style="text-align:center"><input type="checkbox" checked="checked"  name="list[]"  id="list" value="<?=$sfr['sid']?>" /></td>
              </tr>
              <?php 	$j++; }?>
              <tr>
                <td colspan="6">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;Select Courier Name</td>
                <td colspan="2">&nbsp;
                  <select name="Courier_name" id="Courier_name" class="form-control">
                    <?php
                $res_pro = mysqli_query($link1,"select name,courier_id from courier_master where 1"); 
                while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                    <option value="<?=$row_pro['courier_id']?>" <?php if($_REQUEST['Courier_name'] == $row_pro['courier_id']) { echo 'selected'; }?>>
                    <?=$row_pro['name']." (".$row_pro['courier_id'].")"?>
                    </option>
                    <?php } ?>
                  </select></td>
                <td>Docket No</td>
                <td><input type="text" name="doc_code" class="required form-control" id="doc_code"  required/></td>
              </tr>
                </tbody>
              
            </table>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Dispatch" title="Dispatch sfr">
              <input type="hidden" name="to_location"  id="to_location" value="<?=$_REQUEST['to_location']?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='sfr_bucket.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
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