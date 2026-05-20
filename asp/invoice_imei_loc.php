<?php
require_once("../includes/config.php");
$currentsessionid = session_id();
//////////////// decode challan number////////////////////////////////////////////////////////
$po_no = base64_decode($_REQUEST['refid']);
$partcode = base64_decode($_REQUEST['partcode']);
$qty = base64_decode($_REQUEST['pqty']);
$model_id = base64_decode($_REQUEST['model_id']);
$productdet = getAnyDetails($partcode,"part_desc","partcode","partcode_master",$link1)."&nbsp;&nbsp;|&nbsp;&nbsp;".$partcode;
//echo "select id from imei_details_eng where challan_no='".$po_no."' and partcode='".$partcode."'";
$count_serial=mysqli_num_rows(mysqli_query($link1,"select id from imei_details_eng where challan_no='".$po_no."' and partcode='".$partcode."'"));

////////////////////////////////////////// fetching datta from table///////////////////////////////////////////////
$po_sql="select * from stn_master where challan_no='".$po_no."'";

$po_res=mysqli_query($link1,$po_sql)or die("error 1".mysqli_error($link1));
$po_row=mysqli_fetch_assoc($po_res);
////// if any action is taken
if($_POST['fresh']=='FLUSH ALL IMEI NOS.'){
   $res1 = mysqli_query($link1,"DELETE FROM temp_barcode_tnx WHERE browser_id ='".$currentsessionid."' and user_id='".$_SESSION['userid']."' and ref_no='".$po_no."' and partcode='".$partcode."' and trn_type='".$_REQUEST['grn_type']."'")or die("error 2".mysqli_error($link1));
}
///// if we scan barcode
if ($_POST['button'] == 'SUBMIT') {
    $bar = trim($_POST['serialno']);
	///// check serial no. in stock
		
    $res = mysqli_query($link1,"select id,status from imei_details_asp where imei1='".$_POST['serialno']."' and location_code= '".$_SESSION['asc_code']."' and status ='1'  order by id desc")or die("error 3".mysqli_error($link1));
	$row_checkstock=mysqli_fetch_assoc($res);
	///// check serial no. with in the temp table
	
    $res1 = mysqli_query($link1,"select serial_no from temp_barcode_tnx where serial_no='".$_POST['serialno']."' and trn_type='".$_REQUEST['grn_type']."'")or die("error 4".mysqli_error($link1));
   if (mysqli_num_rows($res1) > 0) {
        $msg = "You are entering duplicate serial no.";
    } elseif(mysqli_num_rows($res) > 0) {
	
        $res2 = mysqli_query($link1,"insert into temp_barcode_tnx set serial_no='".$bar."',browser_id='".$currentsessionid."',user_id='".$_SESSION['userid']."',ref_no='".$po_no."',partcode='".$partcode."',upd_qty='1',trn_type='ENGISSUE'")or die("error 5".mysqli_error($link1));
        $msg= "Insert successfully!"; 
	}
	else
	{
	$msg= "Stock is not availabel!"; 
	}
}
///// if we are going to edit scanned barcode
if ($_POST['submit'] == 'Save') {
    $change_bar = trim($_POST['change_bar']);
    $bar = trim($_POST['chang_bar_val']);
	///// check serial no. in stock
	
    $res = mysqli_query($link1,"select id,partcode,status from imei_details_asp where imei1='".$_POST['change_bar']."' and location_code= '".$_SESSION['asc_code']."' and status ='1'  order by id desc")or die("error 6".mysqli_error($link1));
	$row_checkstock=mysqli_fetch_assoc($res);
	$disp_box_qty=1;
	///// check serial no. with in the temp table
	
    $res1 = mysqli_query($link1,"select serial_no from temp_barcode_tnx where serial_no='".$_POST['change_bar']."'  and trn_type='ENGISSUE'")or die("error 7".mysqli_error($link1));
    if($row_checkstock['id']!='' ) {
		
	}  elseif(mysqli_num_rows($res) > 0) {
	
       $res2 = mysqli_query($link1,"update temp_barcode_tnx set serial_no='".$change_bar."' where serial_no='".$bar."' and browser_id='".$currentsessionid."' and user_id='".$_SESSION['userid']."' and ref_no='".$po_no."' and partcode='".$partcode."' and trn_type='ENGISSUE'")or die("error 8".mysqli_error($link1));
        $msg1= "IMEI no. is successfully updated.";     
    }
}
////// if we press  final process button then all scanned IMEI nos. shall be in stock
if($_POST['final_sub']=='PROCESS'){

  
echo "select * from temp_barcode_tnx where browser_id='".$currentsessionid."' and user_id='".$_SESSION['userid']."' and ref_no='".$po_no."' and partcode='".$partcode."' and trn_type='ENGISSUE'";

	$get_serial = mysqli_query($link1,"select * from temp_barcode_tnx where browser_id='".$currentsessionid."' and user_id='".$_SESSION['userid']."' and ref_no='".$po_no."' and partcode='".$partcode."' and trn_type='ENGISSUE'")or die("error 9".mysqli_error($link1));
	while($row_serial=mysqli_fetch_array($get_serial)){
		//// insert in import table//
		  $res1 = mysqli_query($link1,"select id,status,model_id,partcode from imei_details_asp where imei1='".$row_serial['serial_no']."' and location_code= '".$_SESSION['asc_code']."' and status ='1'  order by id desc")or die("error 3".mysqli_error($link1));
	$row_stock=mysqli_fetch_assoc($res1);
	
	//	echo "update imei_details_asp  set status='3',dis_date='".$today."',challan_no='".$po_no."' where  status='1' and (imei1='".$row_serial['serial_no']."'";
	
		mysqli_query($link1,"update imei_details_asp  set status='3',dis_date='".$today."',eng_issue_no='".$po_no."' where  status='1' and (imei1='".$row_serial['serial_no']."') ")or die("error 10".mysqli_error($link1));
		
			$upd_imeidetailasp = mysqli_query($link1,"insert into  imei_details_eng set imei1 ='".$row_serial['serial_no']."',challan_no = '".$po_no."' , partcode= '".$row_stock['partcode']."' , model_id ='".$row_stock['model_id']."' ,status ='1' , stock_type='ok' , entry_date = '".$today."' ,location_code ='".$_SESSION['asc_code']."',locationuser_code='".$_POST['engineerid']."' "	);
	//// check if query is not executed
				if (!$upd_imeidetailasp) {
					$flag = false;
					$error_msg = "Error details2: " . mysqli_error($link1) . ".";
				}
	
		///// update serial no. inventroy
						
       mysqli_query($link1,"INSERT INTO imei_history set imei1='".$row_serial['serial_no']."',partcode='".$row_stock['partcode']."',transaction_no='".$po_no."',remark='Issue To Engineer' ,location_code='".$_SESSION['asc_code']."' ")or die("error 11".mysqli_error($link1));   
	
		mysqli_query($link1,"delete from temp_barcode_tnx where id='".$row_serial['id']."'");
	}
	/////// update attachment flag
	
	$checkboxqty=mysqli_fetch_assoc(mysqli_query($link1,"select COUNT(id) as dispqty from imei_details_eng where challan_no='".$po_no."' and partcode='".$partcode."'"));
	
	if($checkboxqty['dispqty']==$qty){
	echo "update stn_items set attach_file='BY SCANNING',imei_attach='Y' where challan_no='".$po_no."' and id='".$_POST['sno']."'";
	
      mysqli_query($link1,"update stn_items set attach_file='BY SCANNING',imei_attach='Y' where challan_no='".$po_no."'  and id='".$_POST['sno']."'")or die("error 12".mysqli_error($link1));

	$cflag = "success";
	$cmsg = "Success";
	$msg = "IMEI is successfully uploaded against.".$partcode;
	}
	
	header("Location:challan_issue_part.php?refid=".base64_encode($po_no)."&msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."&daterange=".$today."&status=".$_REQUEST['grn_type']."&doc_type=".$_REQUEST['grn_type']."".$pagenav);
	//exit;
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
      <h2 align="center"><i class="fa fa-shower"></i> Scan TAG /<?php echo SERIALNO ?> </h2>
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading">Document Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">From Location Name</label></td>
                <td width="30%"><?php echo getAnyDetails($po_row["from_location"],"locationname","location_code","location_master",$link1)."(".$po_row["from_location"].")";?></td>
                <td width="20%"><label class="control-label">To Location Name</label></td>
                <td width="30%"><?php echo getAnyDetails($po_row["to_location"],"locusername","userloginid","locationuser_master",$link1)."(".$po_row['to_location'].")";?></td>
              </tr>
              
              <tr>
                <td><label class="control-label">From Address</label></td>
                <td><?php echo $po_row['from_addrs'];?></td>
                <td><label class="control-label">To Address</label></td>
                <td><?php echo $po_row['to_addrs'];?></td>
              </tr>  
			  <tr>
                <td><label class="control-label">PO No</label></td>
                <td><?php if($po_row['po_no']!=""){  echo $po_row['po_no'];} else { echo $po_row['challan_no']; }?></td>
                <td><label class="control-label">PO Date</label></td>
                <td><?php echo dt_format($po_row['sale_date']);?></td>
              </tr>
			  <tr>
                <td class="bg-warning"><label class="control-label">Product Details</label></td>
                <td class="bg-warning" colspan="3"><?=$productdet;?></td>
              </tr>
			  <tr class="bg-warning">
			    <td><label class="control-label">Remaining TAG /<?php echo SERIALNO ?> Upload</label></td>
			    <td><?=($qty-$count_serial)?></td>
			    <td><label class="control-label">TAG /<?php echo SERIALNO ?> Uploaded</label></td>
			    <td><?php echo $count_serial; ?></td>
			    </tr>       
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive" style="width:45%;display:inline-block;float:left;">
      <div class="panel-heading">Scan TAG /<?php echo SERIALNO ?> </div>
      <div class="panel-body">
      <form id="freshForm" name="freshForm" class="form-horizontal" method="post" action="">
            <h3><span style="color:red" class="lable"><?php echo $msg; ?></span></h3>
            <?php 
			
			//echo "select id from temp_barcode_tnx where browser_id='".$currentsessionid."' and user_id='".$_SESSION['userid']."' and ref_no='".$po_no."' and partcode='".$partcode."' and trn_type='ENGISSUE'";
			
			$counupdser=mysqli_num_rows(mysqli_query($link1, "select id from temp_barcode_tnx where browser_id='".$currentsessionid."' and user_id='".$_SESSION['userid']."' and ref_no='".$po_no."' and partcode='".$partcode."' and trn_type='ENGISSUE'"));
			if((($counupdser+ $count_serial) >= $qty)){
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
			     $props=" placeholder='Scan  here' style='width:300px;'"; 
				 $prp2="";
				 } ?>
            <div style="display:inline-block; float:left"><input type="text" name="serialno" id="serialno" class="form-control" <?php echo $props; ?> />
            <input name="partcode" id="partcode" type="hidden" value="<?=$_REQUEST['partcode']?>"/>
            <input name="challan_no" id="challan_no" type="hidden" value="<?=$po_no?>"/>
            </div><div style="display:inline-block; float:center; padding-top: 20px;">
            <input name="button" type="submit" id="button" class="btn<?=$btncolor?>"  value="SUBMIT" <?=$prp2?> />
            </div>
        </form>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive" style="width:50%; display:inline-block;float:right;">
      <div class="panel-heading">Scanned TAG /<?php echo SERIALNO ?> List</div>
      <div class="panel-body">
      <div style="overflow-y: auto;height:250px;">
      <span style="color:red; font-size:14px"><?php echo $msg1;?></span>
         <table class="table table-bordered" width="100%">
         <thead>
            <tr class="<?=$tableheadcolor?>">
              <th width="10%">SNO.</th>
              <th width="74%">TAG /<?php echo SERIALNO ?> .</th>
              <th width="16%">DELETE</th>
            </tr>
          </thead>
          <tbody>
          <?php
  		  $i=1;
		  $str = "";
		  $sql = "select id,serial_no from temp_barcode_tnx where browser_id='".$currentsessionid."' and user_id='".$_SESSION['userid']."' and ref_no='".$po_no."' and partcode='".$partcode."' and trn_type='ENGISSUE'"; 
  		  $result = mysqli_query($link1,$sql);
  		  while ($row = mysqli_fetch_assoc($result)){
	  	  /// make serial no. string
	      if ($str == "") {  $str.= $row['serial_no']; } else {  $str.="," . $row['serial_no'];}
  		  ?>
            <tr>
              <td align="center"><?php echo $i;?></td>
              <td><a href="#" id="myBtn<?=$i?>" title="Edit barcode" onClick="getid('<?=$i?>','<?=$row['serial_no']?>')" style="text-decoration:none;font-weight:900;font-size:13px;color:#000"><?=$row['serial_no']?></a></td>
              <td align="center"><a href='' onClick="confirmDel('deleteTempInvoiceImei.php?refid=<?=base64_encode($po_no)?>&partcode=<?=base64_encode($partcode)?>&pqty=<?=base64_encode($qty)?>&partname=<?=$_REQUEST['partname']?>&grn_type=<?=$_REQUEST['grn_type']?>&rid=<?=$row['id']?><?=$pagenav?>', 'myWin3', 'toolbar=no, status=no, resizable=No, scrollbars=No, width=860, height=530, top=50, left=120');return false" title="Delete this imei no."><i class="fa fa-trash fa-lg faicon"></i></a></td>
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
			 <input name="engineerid" id="engineerid" type="hidden" value="<?=$po_row["to_location"]?>"/>
			    <input name="sno" id="sno" type="hidden" value="<?=$_REQUEST['id']?>"/>
            <?php if($i>1){echo "Scanned TAG nos. -> ".($i-1);} ?>
            </div>
        </form>
     </div> 
    </div><!--close panel-->
    
 	<div style="width: 100%; padding-top: 30px; text-align:center; display:inline-block; float:left">
         <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='challan_issue_part.php?refid=<?=base64_encode($po_no)?>&daterange=<?=$_REQUEST['entry_date']?>&status=<?=$_REQUEST['status']?>&doc_type=<?=$_REQUEST['doc']?><?=$pagenav?>'">
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
          <h4 class="modal-title" align="center">Update TAG /<?php echo SERIALNO ?></h4>
        </div>
        <div class="modal-body modal-bodyTH">
         	<input type="text" name="change_bar" id="change_bar" class="form-control" autocomplete="off">
            <input type="hidden" name="chang_bar_val" id="chang_bar_val">
            <input name="partcode" id="partcode" type="hidden" value="<?=base64_encode($partcode)?>"/>
            <input name="refid" id="refid" type="hidden" value="<?=base64_encode($po_no)?>"/>
            <input name="pqty" id="pqty" type="hidden" value="<?=base64_encode($qty)?>"/>
			 <input name="sno" id="sno" type="hidden" value="<?=$_REQUEST['id']?>"/>
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


