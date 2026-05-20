<?php
require_once("../includes/config.php");
/////get status//
//$today=date("Y-m-d",$time_zone);
$arrstatus = getFullStatus("master",$link1);
$getid = base64_decode($_REQUEST['refid']);
@extract($_POST);
if($_POST){
//// initialize transaction parameters
if ($_POST['upd']=='Dispatch'){
////////////////////  update by priya on 19 july to block multiple entry ///////////////////////////////////////////////////////////////////////////////////////
$messageIdent_disp = md5($_SESSION['asc_code'] . $_POST['upd']);
	//and check it against the stored value:
   	$sessionMessageIdent_disp = isset($_SESSION['messageIdent_disp'])?$_SESSION['messageIdent_disp']:'';
	if($messageIdent_disp!=$sessionMessageIdent_disp){//if its different:          
				//save the session var:
		$_SESSION['messageIdent_disp'] = $messageIdent_disp;
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
$flag=true;
mysqli_autocommit($link1, false);
$error_msg="";
//print_r( $_POST['list']);
if($_POST['list']){}else{
$flag = false;
$cflag = "info";
$cmsg = "Warning";
$msg = "Please select atleast one handset...";
}
$sql_count="select * from invoice_counter where location_code='".$_SESSION['asc_code']."'";
$rs_count=mysqli_query($link1,$sql_count)or die("error1".mysqli_error($link1));
$selcounter=mysqli_fetch_array($rs_count);
$max=$selcounter['p2c_count']+1;
//$challan_no= $_SESSION['asc_code']."P2C".str_pad($max,4,"0",STR_PAD_LEFT);
$challan_no= $_SESSION['asc_code']."P2C".$selcounter['fy'].str_pad($max,4,"0",STR_PAD_LEFT);
//echo "update invoice_counter set  p2c_count ='".$max."'  where location_code='".$_SESSION['asc_code']."'"."<br><br>";
$result=mysqli_query($link1,"update invoice_counter set  p2c_count ='".$max."'  where location_code='".$_SESSION['asc_code']."'");
foreach($_POST['list'] as $tmp=>$value){
///////// update counter 
//$part_code="partcode".$value;
$part_code="partcode".$value;
$model_code="model_id".$value;	
$product="product_id".$value;
$brand="brand_id".$value;	
$job_id="job_no".$value;
$imei_id="imei".$value;	   
$oldChalln="oldChallan".$value;	
//// Get Address////////////////////////////
$to_address=getLocationAddress($to_location,$link1);
$from_address=getLocationAddress($_SESSION['asc_code'],$link1);
////////////////////////Get Any detail of From locatio////////////////////////
$to_detail=getAnyDetails($to_location,"stateid,gstno","location_code","location_master",$link1);
$to = explode("~",$to_detail);
$from_detail=getAnyDetails($_SESSION['asc_code'],"stateid,gstno","location_code","location_master",$link1);
$from= explode("~",$from_detail);
///////////////////////////////Check partcode//////////////////////////////////
//echo "select partcode,hsn_code,location_price,part_name from partcode_master where partcode='".$part_code."' and  status='1'";
$rs_part  = mysqli_query($link1,"select partcode,hsn_code,location_price,part_name from partcode_master where partcode='".$_POST[$part_code]."'  ")or die("part error1".mysqli_error($link1));
//echo "select partcode,hsn_code,distributer_price,name from partcode_master where partcode='".$part."' and  status='Active' ";
//echo "select partcode,hsn_code,distributer_price,name from partcode_master where partcode='".$part."' and  status='Active' ";
$part = mysqli_fetch_assoc($rs_part) ;
if($part['partcode']==""){
$flag=false;
$error_msg="part not found in partcode master";
}
//  get tax on HSN Code
//echo "select id,cgst,igst,sgst from tax_hsn_master where hsn_code='".$part['hsn_code']."'";
$rs_hsn_tax  = mysqli_query($link1,"select id,cgst,igst,sgst from tax_hsn_master where hsn_code='".$part['hsn_code']."'")or die("part error2".mysqli_error($link1));
//echo "select cgst,igst,sgst from tax_hsn_master where hsn_code='$part[hsn_code]' and  status='Active' ";
$part_tax = mysqli_fetch_assoc($rs_hsn_tax) ;
if($part_tax['id']==""){
$flag = false;
$cflag = "warning";
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
if($_SESSION['gstno']!='' && $doc_type!='DC'){
$cgst_per=$part_tax['cgst'];
$sgst_per=$part_tax['sgst'];
$inv='INV';
}else{
$cgst_per="0";
$sgst_per="0";
$inv='DC';
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
if($_SESSION['gstno']!='' && $doc_type!='DC'){
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
//echo "insert into billing_product_items set from_location='".$_SESSION['asc_code']."',to_location='".$to_location."',job_no='".$_POST[$job_id]."',model_id='".$_POST[$model_code]."',partcode='".$part_code."',challan_no='".$challan_no."', hsn_code='".$part['hsn_code']."',qty='1',uom='PCS',value='".$val."',price='".$part['location_price']."',basic_amt='".$val."',cgst_per='".$cgst_per."',cgst_amt='".$cgst_val."',sgst_per='".$sgst_per."',sgst_amt='".$sgst_val."',igst_per='".$igst_per."',igst_amt='".$igst_val."',status='2',type='P2C',product_id='".$_POST[$product]."',brand_id='".$_POST[$brand]."'"."<br><br>";
$sfr_items="insert into billing_product_items set from_location='".$_SESSION['asc_code']."',to_location='".$to_location."',job_no='".$_POST[$job_id]."',model_id='".$_POST[$model_code]."',partcode='".$_POST[$part_code]."',challan_no='".$challan_no."', hsn_code='".$part['hsn_code']."',qty='1',uom='PCS',value='".$val."',price='".$part['location_price']."',basic_amt='".$val."',cgst_per='".$cgst_per."',cgst_amt='".$cgst_val."',sgst_per='".$sgst_per."',sgst_amt='".$sgst_val."',igst_per='".$igst_per."',igst_amt='".$igst_val."',status='2',type='P2C',product_id='".$_POST[$product]."',brand_id='".$_POST[$brand]."',item_total='".$tot_val."',old_challan='".$_POST[$oldChalln]."' ";
$sfr_items_qry=mysqli_query($link1,$sfr_items);
if (!$sfr_items_qry) {
$flag = false;
$cmsg = "Error details2.1: " . mysqli_error($link1) . ".";
}


	//////////////////////////////////////Update Inventory.///////////////////////////////
		
		$inv_faulty="Update client_inventory set faulty=faulty-1 where partcode='".$_POST[$part_code]."' and location_code='".$_SESSION['asc_code']."' and faulty>0";
		
		$p2c_inv=mysqli_query($link1,$inv_faulty);
		
		if (!$p2c_inv) {
		
		$flag = false;
		
		$cmsg = "Error details2.6_p2c_inv: " . mysqli_error($link1) . ".";
		
		}
		
		////////////////////////////Stock Ledger/////////////////////////////////////
		
		$flag = stockLedgerO($_POST[$job_id],$today,$_POST[$part_code],$_SESSION['asc_code'],$to_location,"OUT","Faulty","Faulty Dispatch","Faulty Dispatch","1",$part['location_price'],$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag,$_SESSION['asc_code']);
///////////////////////////////Update call history////////////////////////////////////
///////////////////////////////////////Update P2C Bin//////////////////////////////////////////////
//echo "update sfr_bin set status='41',challan_no='".$challan_no."'  where sid='".$value."'";
//echo "update part_to_credit set status='2',challan_no='".$challan_no."',challan_date='".$today."',dispatchstatus ='Dispatched'  where sno='".$value."'" ."<br><br>";
$up_sfr_bin=mysqli_query($link1,"update part_to_credit set status='2',challan_no='".$challan_no."',challan_date='".$today."',dispatchstatus ='Dispatched'  where sno='".$value."'" );
}
if($_SESSION['gstno']!='' && $doc_type!='DC'){
$inv='INV';
}else{
$inv='DC';
}
//////////////////////////////////Billing master/////////////////////////////////////
//echo "insert into  billing_master set from_location='".$_SESSION['asc_code']."',to_location='".$to_location."',from_gst_no='".$from[1]."',to_gst_no='".$to[1]."', 	from_addrs='".$from_address."',to_addrs ='".$to_address."',basic_cost='".$basic_cost."',  total_cost='".$inv_tot_cost."',cgst_amt='".$cgst_final_val."',sgst_amt='".$sgst_final_val."',igst_amt='".$igst_final_val."', challan_no='".$challan_no."',from_stateid ='".$_SESSION['stateid']."',to_stateid ='".$to[0]."',courier='".$Courier_name."',docket_no='".$doc_code."',status='2',sale_date='".$today."',po_type='P2C',document_type='".$inv."',deliv_addrs='".$to_address."'"."<br><br>";
$bill_master="insert into  billing_master set from_location='".$_SESSION['asc_code']."',to_location='".$to_location."',from_gst_no='".$from[1]."',to_gst_no='".$to[1]."', 	from_addrs='".$from_address."',to_addrs ='".$to_address."',basic_cost='".$basic_cost."',  total_cost='".$inv_tot_cost."',cgst_amt='".$cgst_final_val."',sgst_amt='".$sgst_final_val."',igst_amt='".$igst_final_val."', challan_no='".$challan_no."',from_stateid ='".$_SESSION['stateid']."',to_stateid ='".$to[0]."',courier='".$Courier_name."',docket_no='".$doc_code."',status='2',sale_date='".$today."',po_type='P2C',document_type='".$inv."',deliv_addrs='".$to_address."'";
$p2c_challan_query=mysqli_query($link1,$bill_master);

$up_docket_no=mysqli_query($link1,"update advance_docket_upload set status='1' where docket_no='".$_REQUEST[doc_code]."'" );


if (!$p2c_challan_query) {
$flag = false;
$cmsg = "Error details2.6: " . mysqli_error($link1) . ".";
}
if($flag){
$cflag="success";
$cmsg="Success";
$msg="You have successfully Dispatch Faulty Parts With challan no ".$challan_no;
mysqli_commit($link1);
///// move to parent page
header("location:invoice_list_p2c.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
exit;
}else{
mysqli_rollback($link1);
mysqli_close($link1);
header("location:p2c_bin.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
}

}else{
		$msg="You have successfully Dispatch Faulty Parts With challan no";
		$cflag="success";
		$cmsg="Success";
		header("location:invoice_list_p2c.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit; 
	}
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
<script type="text/javascript" src="../js/common_js.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
    <?php 
include("../includes/leftnavemp2.php");
?>
    <div class="col-sm-8">
      <h2 align="center"><i class="fa fa-reply-all  fa-lg"></i> Dispatch Faulty Parts </h2>
      <br/>
      <br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
        <form id="frm1" name="frm1" class="form-horizontal" onsubmit="return really('dispatch')" action="" method="post">
        
         <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">Brand <span style="color:#F00">*</span></label>
              <div class="col-md-9">
                 <select name="brand_id" id="brand_id" class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();" >
					 
                <option value="">-Please Select-</option>
          
					  <?php
$map_brand = mysqli_query($link1,"select brand_id  from access_brand where location_code ='".$_SESSION['asc_code']."'  and  status = 'Y'"); 
while($row_brand = mysqli_fetch_assoc($map_brand)){
$brand = mysqli_fetch_array(mysqli_query($link1, "select brand_id, brand from brand_master where brand_id = '".$row_brand['brand_id']."'  and status='1'"));				
?>
                  <option data-tokens="<?=$brand[1]." | ".$brand[0]?>" value="<?=$brand[0]?>" <?php if($_REQUEST['brand_id'] == $brand[0]) { echo 'selected'; }?>>
                  <?=$brand[1]." (".$brand[0].")"?>
                  </option>
                  <?php } ?>
                 </select>
              </div>
            </div>
          </div>
        
          <div class="form-group">
            <div class="col-md-6">
              <label class="col-md-6 control-label">Location Name:-</label>
              <div class="col-md-6">
                <select name="to_location" id="to_location" class="form-control required" >
                  <option value="">Please Select</option>
                  <?php
$map_wh = mysqli_query($link1,"select wh_location  from map_wh_location where location_code ='".$_SESSION['asc_code']."'  and  status = 'Y' and wh_location in (select location_code  from access_brand where brand_id ='".$_REQUEST['brand_id']."'  and  status = 'Y')"); 
while($row_wh = mysqli_fetch_assoc($map_wh)){
$location = mysqli_fetch_array(mysqli_query($link1, "select locationname, location_code from location_master where location_code = '".$row_wh['wh_location']."' "));				
?>
                  <option value="<?=$location['location_code']?>" <?php if($_REQUEST['location_code'] == $location['location_code']) { echo 'selected'; }?>>
                  <?=$location['locationname']." (".$location['location_code'].")"?>
                  </option>
                  <?php } ?>
                </select>
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
                <tr class="<?=$tableheadcolor?>" >
                  <th width="5%" style="text-align:center"><label class="control-label">Sno</label></th>
                  <th width="15%" style="text-align:center"><label class="control-label">Job No</label></th>
                  <th width="15%" style="text-align:center"><label class="control-label"><?php echo SERIALNO; ?></label></th>
                  <th width="15%" style="text-align:center"><label class="control-label">Model</label></th>
                  <th width="15%" style="text-align:center" ><label class="control-label">Part</label></th>
				  <th width="15%" style="text-align:center" ><label class="control-label" style="text-align:center">Miss. in Challan</label></th>
                  <th width="15%" style="text-align:center"><label class="control-label">Confrim</label></th>
              </thead>
              <?php //echo "select * from part_to_credit where status ='1' and from_location='".$_SESSION['asc_code']."'";
 $sel_tras="select * from part_to_credit where status ='1' and partcode != '' and partcode != '-1' and from_location='".$_SESSION['asc_code']."' and brand_id='".$_REQUEST['brand_id']."'";
$sel_res12=mysqli_query($link1,$sel_tras)or die("error1".mysqli_error($link1));
$j=1;
while($sfr = mysqli_fetch_array($sel_res12)){ ?>
              <tr>
                <td width="5%" style="text-align:center"><label class="control-label">
                    <?=$j?>
                  </label></td>
                <td width="15%" style="text-align:center"><label class="control-label">
                    <?=$sfr['job_no']?>
                    <input type="hidden" name="job_no<?=$sfr['sno']?>" class="number form-control" id="job_no<?=$sfr['sno']?>" value="<?=$sfr['job_no']?>"/>
                  </label></td>
                <td width="15%" style="text-align:center"><label class="control-label">
                	<?php $srr = getAnyDetails($sfr["job_no"],"imei","job_no","jobsheet_data",$link1); ?>
                    <?=$srr;?>
                    <input type="hidden" name="imei<?=$sfr['sno']?>" class="number form-control" id="imei<?=$sfr['sno']?>" value="<?=$srr;?>"/>
                  </label></td>
                <td width="15%" style="text-align:center"><label class="control-label">
                    <?=getAnyDetails($sfr["model_id"],"model","model_id","model_master",$link1)?>
                    <input type="hidden" name="model_id<?=$sfr['sno']?>" class="number form-control" id="model_id<?=$sfr['sno']?>" value="<?=$sfr['model_id']?>"/>
                    <input type="hidden" name="product_id<?=$sfr['sno']?>" class="number form-control" id="product_id<?=$sfr['sno']?>" value="<?=$sfr['product_id']?>"/>
                    <input type="hidden" name="brand_id<?=$sfr['sno']?>" class="number form-control" id="brand_id<?=$sfr['sno']?>" value="<?=$sfr['brand_id']?>"/>
					<input type="hidden" name="oldChallan<?=$sfr['sno']?>" class="form-control" id="oldChallan<?=$sfr['sno']?>" value="<?=$sfr['old_challan']?>"/>
                  </label></td>
                <td width="15%" style="text-align:center">
                  <?=getAnyDetails($sfr['partcode'],"part_name","partcode","partcode_master",$link1)?>
                  <input type="hidden" name="partcode<?=$sfr['sno']?>" class=" form-control" id="partcode<?=$sfr['sno']?>" value="<?=$sfr['partcode']?>"/></td>
				  <td width="15%" style="text-align:center; color:red;"><?php if($sfr['missing_in_challan']!=""){ echo "Miss. in - ".$sfr['missing_in_challan']; } ?></td>
                <td width="15%" style="text-align:center"><input type="checkbox" checked="checked"  name="list[]"  id="list" value="<?=$sfr['sno']?>" /></td>
              </tr>
              <?php 	$j++; }?>
              <tr>
                <td colspan="7">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;Select Courier Name</td>
                <td colspan="2">&nbsp;
                  <select name="Courier_name" id="Courier_name" class="form-control ">
                    <?php
$res_pro = mysqli_query($link1,"select docket_company,courier_code from advance_docket_upload where status='0' and asp_code='".$_SESSION['asc_code']."' group by docket_company"); 
while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                    <option value="<?=$row_pro['courier_code']?>" <?php if($_REQUEST['Courier_name'] == $row_pro['courier_code']) { echo 'selected'; }?>>
                    <?=$row_pro['docket_company']." (".$row_pro['courier_code'].")"?>
                    </option>
                    <?php } ?>
                    <option value="C0023">By Hand</option> 

                  </select></td>
                <td>Docket No</td>
                <td colspan="2">
                
                <select name="doc_code" id="doc_code" class="form-control " required>
                    <?php
$res_pro1 = mysqli_query($link1,"select * from advance_docket_upload where status='0' and asp_code='".$_SESSION['asc_code']."'"); 
while($row_pro1 = mysqli_fetch_assoc($res_pro1)){?>
                    <option value="<?=$row_pro1['docket_no']?>" <?php if($_REQUEST['doc_code'] == $row_pro1['docket_no']) { echo 'selected'; }?>>
                    <?=$row_pro1['docket_no']." (".$row_pro1['docket_company'].")"?>
                    </option>
                    <?php } ?>
                     <option value="By Hand">By Hand</option> 
                  </select>
                
              <!--  <input type="text" name="doc_code" class="required form-control" id="doc_code"  required/>--></td>
              </tr>
              <tr>
              <td>Document Type</td>
              <td><select name="doc_type" id="doc_type" class="form-control ">
             <option value="DC">Delivery Challan</option>
             <option value="INV">Invoice</option> 
              
              </select></td>
              </tr>
                </tbody>
              
            </table>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Dispatch" title="Dispatch sfr" onClick="this.style.visibility='hidden';">
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