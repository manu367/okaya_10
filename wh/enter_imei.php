<?php
require_once("../includes/config.php");
$currentsessionid = session_id();
//////////////// decode challan number////////////////////////////////////////////////////////
$po_no = base64_decode($_REQUEST['refid']);
$partcode = base64_decode($_REQUEST['partcode']);
$qty = base64_decode($_REQUEST['pqty']);
$model_id = base64_decode($_REQUEST['model_id']);
$productdet = base64_decode($_REQUEST['partname'])."&nbsp;&nbsp;|&nbsp;&nbsp;".$partcode;
$count_serial=mysqli_num_rows(mysqli_query($link1,"select id from imei_details where grn_no='".$po_no."' and partcode='".$partcode."'"));
////////////////////////////////////////// fetching datta from table///////////////////////////////////////////////
$po_sql="select * from grn_master where grn_no='".$po_no."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
////// if any action is taken
if($_POST['fresh']=='FLUSH ALL IMEI NOS.'){
   $res1 = mysqli_query($link1,"DELETE FROM temp_barcode_tnx WHERE browser_id ='".$currentsessionid."' and user_id='".$_SESSION['userid']."' and ref_no='".$po_no."' and partcode='".$partcode."' and trn_type='".$_REQUEST['grn_type']."'");
}
///// if we scan barcode
if ($_POST['button'] == 'SUBMIT') {
    $bar = trim($_POST['serialno']);
	///// check serial no. in stock
    $res = mysqli_query($link1,"select id,status from imei_details where imei1='".$_POST['serialno']."' and location_code='".$po_row['location_code']."'");
	$row_checkstock=mysqli_fetch_assoc($res);
	$disp_box_qty=1;
	///// check serial no. with in the temp table
    $res1 = mysqli_query($link1,"select serial_no from temp_barcode_tnx where serial_no='".$_POST['serialno']."' and trn_type='".$_REQUEST['grn_type']."'");
    if($row_checkstock['id']!='' && $row_checkstock['status']==1) {
		$msg = "IMEI no. is already exits into database.";
    }elseif (mysqli_num_rows($res1) > 0) {
        $msg = "You are entering duplicate serial no.";
    } elseif($bar!='') {
        $res2 = mysqli_query($link1,"insert into temp_barcode_tnx set serial_no='".$bar."',browser_id='".$currentsessionid."',user_id='".$_SESSION['userid']."',ref_no='".$po_no."',partcode='".$partcode."',upd_qty='1',trn_type='".$_REQUEST['grn_type']."'");
        $msg= "Insert successfully!"; 
	}
}
///// if we are going to edit scanned barcode
if ($_POST['submit'] == 'Save') {
    $change_bar = trim($_POST['change_bar']);
    $bar = trim($_POST['chang_bar_val']);
	///// check serial no. in stock
    $res = mysqli_query($link1,"select id,partcode,status from imei_details where imei1='".$_POST['change_bar']."' and location_code='".$po_row['location_code']."'");
	$row_checkstock=mysqli_fetch_assoc($res);
	$disp_box_qty=1;
	///// check serial no. with in the temp table
    $res1 = mysqli_query($link1,"select serial_no from temp_barcode_tnx where serial_no='".$_POST['change_bar']."'  and trn_type='".$_REQUEST['grn_type']."'");
    if($row_checkstock['id']!='' && $row_checkstock['status']==1) {
		
	} elseif (mysqli_num_rows($res1) > 0) {
		
    } elseif($change_bar!='') {
       $res2 = mysqli_query($link1,"update temp_barcode_tnx set serial_no='".$change_bar."' where serial_no='".$bar."' and browser_id='".$currentsessionid."' and user_id='".$_SESSION['userid']."' and ref_no='".$po_no."' and partcode='".$partcode."'  and trn_type='".$_REQUEST['grn_type']."'");
        $msg1= "IMEI no. is successfully updated.";     
    }
}
////// if we press  final process button then all scanned IMEI nos. shall be in stock
if($_POST['final_sub']=='PROCESS'){
	$get_serial = mysqli_query($link1,"select * from temp_barcode_tnx where browser_id='".$currentsessionid."' and user_id='".$_SESSION['userid']."' and ref_no='".$po_no."' and partcode='".$partcode."' and trn_type='".$_REQUEST['grn_type']."'");
	while($row_serial=mysqli_fetch_array($get_serial)){
		//// insert in import table//
		mysqli_query($link1,"INSERT INTO imei_details set imei1='".$row_serial['serial_no']."',grn_no='".$po_no."',grn_date='".$po_row['receive_date']."',partcode='".$row_serial['partcode']."',model_id='".$model_id."', location_code='".$po_row['location_code']."',status='1',entry_date='".$today."'");
		///// update serial no. inventroy
        mysqli_query($link1,"INSERT INTO imei_history set imei1='".$row_serial['serial_no']."',partcode='".$row_serial['partcode']."',transaction_no='".$po_no."',remark='GRN Receive' ,location_code='".$po_row['location_code']."' ");    
		mysqli_query($link1,"delete from temp_barcode_tnx where id='".$row_serial['id']."'");
	}
	/////// update attachment flag
	$checkboxqty=mysqli_fetch_assoc(mysqli_query($link1,"select COUNT(id) as dispqty from imei_details where grn_no='".$po_no."' and partcode='".$partcode."'"));
	if($checkboxqty['dispqty']==$qty){
        mysqli_query($link1,"update grn_data set attach_file='BY SCANNING',imei_attach='Y' where grn_no='".$po_no."' and partcode='".$partcode."' and sno='".$_REQUEST['sno']."'");
	}
	$cflag = "success";
	$cmsg = "Success";
	$msg = "IMEI is successfully uploaded against.".$partcode;
	header("Location:grn_status.php?refid=".base64_encode($po_no)."&msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
	exit;
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
</head>
<body onLoad="document.getElementById('serialno').focus();">
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
   <div class="<?=$screenwidth?> tab-pane fade in active">
      <h2 align="center"><i class="fa fa-shower"></i>  Scan IMEI For GRN</h2>
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading">Document Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Supplier Name:</label></td>
                <td width="30%"><?php echo getAnyDetails($po_row["party_code"],"name","id","vendor_master",$link1)."(".$po_row["party_code"].")";?></td>
                <td width="20%"><label class="control-label">Location</label></td>
                <td width="30%"><?php echo getAnyDetails($po_row["location_code"],"locationname","location_code","location_master",$link1)."(".$po_row['location_code'].")";?></td>
              </tr>
              <tr>
                <td><label class="control-label">GRN No</label></td>
                <td><?php echo $po_row['grn_no'];?></td>
                <td><label class="control-label">GRN Date</label></td>
                <td><?php echo dt_format($po_row['receive_date']);?></td>
              </tr>
              <tr>
                <td><label class="control-label">PO No.</label></td>
                <td><?php echo $po_row['po_no'];?></td>
                <td><label class="control-label">Gate Entry No.</label></td>
                <td><?php echo $po_row['gate_entry_no'];?></td>
              </tr>  
			  <tr>
                <td class="bg-warning"><label class="control-label">Product Details</label></td>
                <td class="bg-warning" colspan="3"><?=$productdet;?></td>
              </tr>
			  <tr class="bg-warning">
			    <td><label class="control-label">Remaining IMEI Upload</label></td>
			    <td><?=($qty-$count_serial)?></td>
			    <td><label class="control-label">IMEI Uploaded</label></td>
			    <td><?=$count_serial?></td>
			    </tr>       
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive" style="width:50%;display:inline-block;float:left;">
      <div class="panel-heading">Scan IMEI</div>
      <div class="panel-body">
      <form id="freshForm" name="freshForm" class="form-horizontal" method="post" action="">
            <h3><span style="color:red" class="lable"><?php echo $msg; ?></span></h3>
            <?php 
			$counupdser=mysqli_num_rows(mysqli_query($link1, "select id from temp_barcode_tnx where browser_id='".$currentsessionid."' and user_id='".$_SESSION['userid']."' and ref_no='".$po_no."' and partcode='".$partcode."' and trn_type='".$_REQUEST['grn_type']."'"));
			if((($counupdser+$count_serial) >= $qty)){
				echo "<style>
				::-webkit-input-placeholder { color: red; }
                 :-moz-placeholder { color: red; }/* Firefox 18- */
                ::-moz-placeholder { color: red; }/* Firefox 19+ */
                 :-ms-input-placeholder { color: red; }
				</style>";
				$props=" style='width:300px;color:#FF0000;' disabled='disabled' placeholder='You have reached maximum count'";
				$prp2="disabled='disabled'";
			 }
			 else{ 
			     $props=" placeholder='Scan IMEI here' style='width:300px;'"; 
				 $prp2="";
				 } ?>
            <div style="display:inline-block; float:left"><input type="text" name="serialno" id="serialno" class="form-control" <?php echo $props; ?> />
            <input name="partcode" id="partcode" type="hidden" value="<?=$_REQUEST['partcode']?>"/>
            <input name="challan_no" id="challan_no" type="hidden" value="<?=$po_no?>"/>
            </div><div style="display:inline-block; float:right">
            <input name="button" type="submit" id="button" class="btn<?=$btncolor?>"  value="SUBMIT" <?=$prp2?> />
            </div>
        </form>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive" style="width:50%; display:inline-block;float:right;">
      <div class="panel-heading">Scanned IMEI List</div>
      <div class="panel-body">
      <div style="overflow-y: auto;height:250px;">
      <span style="color:red; font-size:14px"><?php echo $msg1;?></span>
         <table class="table table-bordered" width="100%">
         <thead>
            <tr class="<?=$tableheadcolor?>">
              <th width="10%">SNO.</th>
              <th width="74%">IMEI NOS.</th>
              <th width="16%">DELETE</th>
            </tr>
          </thead>
          <tbody>
          <?php
  		  $i=1;
		  $str = "";
		  $sql = "select id,serial_no from temp_barcode_tnx where browser_id='".$currentsessionid."' and user_id='".$_SESSION['userid']."' and ref_no='".$po_no."' and partcode='".$partcode."' and trn_type='".$_REQUEST['grn_type']."'"; 
  		  $result = mysqli_query($link1,$sql);
  		  while ($row = mysqli_fetch_assoc($result)){
	  	  /// make serial no. string
	      if ($str == "") {  $str.= $row['serial_no']; } else {  $str.="," . $row['serial_no'];}
  		  ?>
            <tr>
              <td align="center"><?php echo $i;?></td>
              <td><a href="#" id="myBtn<?=$i?>" title="Edit barcode" onClick="getid('<?=$i?>','<?=$row['serial_no']?>')" style="text-decoration:none;font-weight:900;font-size:13px;color:#000"><?=$row['serial_no']?></a></td>
              <td align="center"><a href='' onClick="confirmDel('deleteTempGrnImei.php?refid=<?=base64_encode($po_no)?>&partcode=<?=base64_encode($partcode)?>&pqty=<?=base64_encode($qty)?>&partname=<?=$_REQUEST['partname']?>&grn_type=<?=$_REQUEST['grn_type']?>&rid=<?=$row['id']?><?=$pagenav?>', 'myWin3', 'toolbar=no, status=no, resizable=No, scrollbars=No, width=860, height=530, top=50, left=120');return false" title="Delete this imei no."><i class="fa fa-trash fa-lg faicon"></i></a></td>
            </tr>
            <?php 
  			$i++;
		  }
		?>
            <input type="hidden" name="hideserial" value="<?= $str ?>" id="hideserial">
          </tbody>  
         </table>
         </div>
      </div><!--close panel body-->
      <div style="display:inline-block; float:left">
     <form id="finalForm" name="finalForm" method="post" action="" class="form-horizontal">
     		<div style="display:inline-block; float:left">
            <input name="fresh" type="submit" id="fresh" class="btn<?=$btncolor?>" value="FLUSH ALL IMEI NOS." onClick="return confirm('Are you sure refresh?');" <?php if($str==""){ ?>disabled<?php } ?>/>
            </div><div style="display:inline-block; float:left">&nbsp;
            <input name="final_sub" type="submit" id="final_sub" class="btn<?=$btncolor?>" value="PROCESS" onClick="return confirm('Are you really want this action?');" <?php if($str==""){ ?>disabled<?php } ?>/>
            </div>
            <div style="display:inline-block; float:right">&nbsp;
            <input name="partcode" id="partcode" type="hidden" value="<?=base64_encode($partcode)?>"/>
            <input name="refid" id="refid" type="hidden" value="<?=base64_encode($po_no)?>"/>
            <input name="pqty" id="pqty" type="hidden" value="<?=base64_encode($qty)?>"/>
            <input name="partname" id="partname" type="hidden" value="<?=$_REQUEST['partname']?>"/>
            <input name="grn_type" id="grn_type" type="hidden" value="<?=$_REQUEST['grn_type']?>"/>
               <input name="sno" id="sno" type="hidden" value="<?=$_REQUEST['sno']?>"/>
            <?php if($i>1){echo "Scanned IMEI nos. -> ".($i-1);} ?>
            </div>
        </form>
     </div> 
    </div><!--close panel-->
    
 	<div style="display:inline-block; float:left">
         <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='grn_status.php?refid=<?=base64_encode($po_no)?><?=$pagenav?>'">
     </div>    
  </div><!--close panel group-->
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<!-- The Modal -->
<div class="modal modalTH fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-dialogTH modal-sm">
       <form id="form" name="form" method="post" action="">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" align="center">Update IMEI</h4>
        </div>
        <div class="modal-body modal-bodyTH">
         	<input type="text" name="change_bar" id="change_bar" class="form-control" autocomplete="off">
            <input type="hidden" name="chang_bar_val" id="chang_bar_val">
            <input name="partcode" id="partcode" type="hidden" value="<?=base64_encode($partcode)?>"/>
            <input name="refid" id="refid" type="hidden" value="<?=base64_encode($po_no)?>"/>
            <input name="pqty" id="pqty" type="hidden" value="<?=base64_encode($qty)?>"/>
            <input name="partname" id="partname" type="hidden" value="<?=$_REQUEST['partname']?>"/>
            <input name="grn_type" id="grn_type" type="hidden" value="<?=$_REQUEST['grn_type']?>"/>
        </div>
        <div class="modal-footer">
       	  <input name="submit" type="submit" id="submit" value="Save" class="btn<?=$btncolor?>"/>
          <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      </form>
    </div>
 </div>
<script>
// When the user clicks the button, open the modal 
function getid(ind,val) {
	//var val = $('#myBtn' + ind).text();
	$("#chang_bar_val").val(val);
	$("#change_bar").val(val);
	$('#myModal').modal({
			show: true,
			backdrop:"static"
	});
}
//////////////////////////////////
function confirmDel(store){
  var where_to= confirm("Do you really want to Delete this Record ??");
  if (where_to== true){
		//alert(window.location.href)
		var url="<?php echo $DelAction ?>";
		window.location=url+store;
  }
  else{
        return false;
  }
}
</script>
</body>
</html>

