<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);

///// after hitting receive button ///
if($_POST){
 if ($_POST['upd']=='Receive'){
	$ref_no=base64_decode($_POST['refno']);
	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg="";
	
	//////////////////////////Break IMEI Details
	$str=$_REQUEST['imei'];
//echo $str;
$s = array_filter(explode(",",$str));
//echo $s ;
//echo $_SESSION['locusertype'];
//print_r($s);
$sql3="select max(dc_res) as no from canbil_transaction where location_code='".$_SESSION['asc_code']."'";
$rs3=mysqli_query($link1,$sql3)or die("error1".mysql_error());
$row3=mysqli_fetch_assoc($rs3);
$dc_temp=$row3[no]+1;
$dcno="CAN".$dc_temp;

foreach($s as $tmp=>$value){ 


	/////////////////////// IMEI stock update//////////////////////////////////////
		  $imei_update="update imei_details_asp set status='3',dis_date='".$today."',challan_no='".$dcno."' where    imei1 ='".$value."'  and location_code='".$_SESSION['asc_code']."' ";
$result6 =	mysqli_query($link1,$imei_update);

}
////////////////// Stock Transaction For Cannibilzed Partcode/////////////////////////////
//echo "update client_inventory set faulty=faulty-'".$_POST['move_qty']."',updatedate='".$datetime."' where partcode='".$_POST['partcode']."' and location_code='".$_SESSION['asc_code']."'";

$result=mysqli_query($link1,"update client_inventory set faulty=faulty-'".$_POST['move_qty']."',updatedate='".$datetime."' where partcode='".$_POST['partcode_can']."' and location_code='".$_SESSION['asc_code']."'");
   $flag=stockLedger($dcno,$today,$_POST['partcode_can'],$_SESSION['asc_code'],$_SESSION['asc_code'],"OUT","faulty","Stock OUT","Canniblized",$_POST['move_qty'],"",$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
	////// fetching data from data table//////////////////////////////////////////////////////////////////////////////////////////
	$sql_po_data="select * from canabil_master where partcode='".$_POST['partcode_can']."' and status='1'";
    $res_poData=mysqli_query($link1,$sql_po_data)or die("error1".mysqli_error());
    while($row_poData=mysqli_fetch_assoc($res_poData)){
	
		  ///// initialize posted variables
		  $reqqty="req_qty".$row_poData['sno'];
		  $okqty="ok_qty".$row_poData['sno'];
		  $damageqty="broken".$row_poData['sno'];
		  $missqty="miss_qty".$row_poData['sno'];
		  $map_part="mapped_partcode".$row_poData['sno'];
		  ///// update stock in  client inventory //
		  if(mysqli_num_rows(mysqli_query($link1,"select partcode from client_inventory where partcode='".$_POST[$map_part]."' and location_code='".$_SESSION['asc_code']."'"))>0){
			 ///if product is exist in inventory then update its qty 
			$result=mysqli_query($link1,"update client_inventory set okqty=okqty+'".$_POST[$okqty]."',broken=broken+'".$_POST[$damageqty]."',missing=missing+'".$_POST[$missqty]."',updatedate='".$datetime."' where partcode='".$_POST[$map_part]."' and location_code='".$_SESSION['asc_code']."'");
		  }		
		  else{			
			 //// if product is not exist then add in inventory
			 $result=mysqli_query($link1,"insert into client_inventory set location_code='".$_SESSION['asc_code']."',partcode='".$_POST[$map_part]."',okqty='".$_POST[$okqty]."',broken='".$_POST[$damageqty]."',missing='".$_POST[$missqty]."',updatedate='".$datetime."'");
		  }
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $error_msg = "Error details1: " . mysqli_error($link1) . ".";
           }
		   ////// insert in stock ledger////
		   ### CASE 1 if user enter somthing in ok qty
		   if($_POST[$okqty]!="" && $_POST[$okqty]!=0 && $_POST[$okqty]!=0.00){
		$flag=stockLedger($ref_no,$today,$_POST[$map_part],$po_row['to_location'],$po_row['from_location'],"IN","OK","Stock In","Receive",$_POST[$okqty],$row_poData['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		   }
		   ### CASE 2 if user enter somthing in damage qty
		   if($_POST[$damageqty]!="" && $_POST[$damageqty]!=0 && $_POST[$damageqty]!=0.00){
		  $flag=stockLedger($ref_no,$today,$_POST[$map_part],$po_row['to_location'],$po_row['from_location'],"IN","DAMAGE","Stock In","Receive",$_POST[$damageqty],$row_poData['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		   }
		   ### CASE 3 if user enter somthing in missing qty
		   if($_POST[$missqty]!="" && $_POST[$missqty]!=0 && $_POST[$missqty]!=0.00){
		    $flag=stockLedger($ref_no,$today,$_POST[$map_part],$po_row['to_location'],$po_row['from_location'],"IN","MISSING","Stock In","Receive",$_POST[$missqty],$row_poData['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		   }
		   ///// update data table	
		   //echo "insert into canbil_transaction set dc_no='".$dcno."',canbil_partcode='".$_POST['partcode']."',can_qty='".$move_qty."',can_date='".$today."',updatedate='".$today."',logged_by='".$_SESSION['userid']."' ,location_code='".$_SESSION['asc_code']."',okqty='".$_POST[$okqty]."',missing='".$_POST[$missqty]."',damage='".$_POST[$damageqty]."',partcode='".$_POST[$map_part]."',dc_res='".$dc_temp."',imei='".$str."',status='Pending'";	   
           $result=mysqli_query($link1,"insert into canbil_transaction set dc_no='".$dcno."',canbil_partcode='".$_POST['partcode_can']."',can_qty='".$_POST['move_qty']."',can_date='".$today."',updatedate='".$today."',logged_by='".$_SESSION['userid']."' ,location_code='".$_SESSION['asc_code']."',okqty='".$_POST[$okqty]."',missing='".$_POST[$missqty]."',damage='".$_POST[$damageqty]."',partcode='".$_POST[$map_part]."',dc_res='".$dc_temp."',imei='".$str."',status='Pending'");
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $error_msg = "Error details2: " . mysqli_error($link1) . ".";
		   }
		   //// update substatus of jobsheet data 
		
	}//// close while loop
	
	////// insert in activity table////
	//$flag=dailyActivity($_SESSION['userid'],$ref_no,"Stock In","Cannibilze",$ip,$link1,$flag);
		
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		$msg="Stock is successfully received against ".$docid;
		$cflag="success";
		$cmsg="Success";
    } else {
		mysqli_rollback($link1);
		$cflag="danger";
		$cmsg="Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
    mysqli_close($link1);
	///// move to parent page
  header("location:assgin_can_repair.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
 <script type="text/javascript">
$(document).ready(function(){
    $("#frm2").validate();
});
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript">
function checkRecQty(a){
	var reqqty=0;
	var okqty=0;
	var damageqty=0;
	//// check requested qty
    if(document.getElementById("req_qty"+a).value==""){
       reqqty=0;
	}else{
	   reqqty=parseInt(document.getElementById("req_qty"+a).value);
	}
	//// check enter ok qty
    if(document.getElementById("ok_qty"+a).value==""){
       okqty=0;
    }else{
       okqty=parseInt(document.getElementById("ok_qty"+a).value);
    }
	//// check enter damage qty
    if(document.getElementById("broken"+a).value==""){
       damageqty=0;
    }else{
       damageqty=parseInt(document.getElementById("broken"+a).value);
    }
	//// check enter qty should not be greater than requested qty
    if(reqqty < (okqty + damageqty)){
       alert("Ok Qty & Damage Qty can not more than requested Qty!");
		document.getElementById("miss_qty"+a).value=0;
		document.getElementById("broken"+a).value=0;
		//document.getElementById("ok_qty"+a).focus();
		document.getElementById("upd").disabled=true;
    }else{
		document.getElementById("miss_qty"+a).value=(reqqty - (okqty + damageqty));
		document.getElementById("miss_qty"+a).focus();
		document.getElementById("upd").disabled=false;
	}
}
</script>
<script type="text/javascript" src="../js/common_js.js"></script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
   <div class="<?=$screenwidth?> tab-pane fade in active">
      <h2 align="center"><i class="fa fa-inbox"></i> Cannibalize</h2><br/>
   <div class="panel-group">
   <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading">Cannibalize Part Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Product Name </label></td>
                <td width="30%"><?php $model_detail=getAnyDetails($_REQUEST['partcode'],"product_id,brand_id,part_name","partcode","partcode_master",$link1);
	$model= explode("~",$model_detail); echo $model[0]; 
	
	
	?> <input name="partcode_can" id="partcode_can" type="text" value="<?=$_REQUEST['partcode']?>" /></td>
                <td width="20%"><label class="control-label">Brand Name</label></td>
                <td width="30%"><?php echo $model[1];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Model Name</label></td>
                <td><?php echo $po_row['from_addrs'];?></td>
                <td><label class="control-label">Part Name</label></td>
                <td><?php  echo $model[2];
				
				
				$selected_sno="";
$cnt=0;
foreach($_POST['list'] as $tmp=>$value){ 
$rs=mysqli_fetch_array(mysqli_query($link1,"select imei1 as a from imei_details_asp where status ='1' and location_code='".$_SESSION['asc_code']."' and id='".$value."'"));
//$c_nos=$rs[a];
$selected_sno.=$rs['a'].",";
$cnt+=count($value);
}
	?>
            <input name="imei" id="imei" type="hidden" value="<?=$selected_sno?>" /> </td>
              </tr>
           
			  <tr>

                <td><label class="control-label">Cannibalize IMEI No. Detail: </label></td>
                            <td colspan="4"><table border="0" cellpadding="1" cellspacing="1">
         <?php
		    $exp_imei=explode(",",$selected_sno);
		   for($i=0;$i<count($exp_imei); $i++){ if($i%8==1){ ?><tr><?php } ?>
                             <td><span class="style2"><?php
							                                                if($exp_imei[$i]!=''){
                                                                                if($c==""){
                                                                                   echo $c=$exp_imei[$i];
                                                                                }else{
                                                                                   echo $c=",".$exp_imei[$i];
                                                                                }
																			}
																		   ?></span></td>
          <?php if($i/3==0){ ?></tr><?php } }?>
          </table>     </td>
                
              </tr> 
                <tr>
                <td><label class="control-label">Cannibalize Qty</label></td>
                <td><?php echo $cnt;?> <input name="move_qty" id="move_qty" type="hidden" value="<?=$cnt?>" /> </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>      
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Items Information</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr>
                <th width="3%" rowspan="2" style="text-align:center">#</th>
                <th width="35%" rowspan="2" style="text-align:center">Part Description</th>
			
                <th width="9%" rowspan="2" style="text-align:center">Cannibalize Qty</th>
                <th colspan="3" style="text-align:center">Receive Qty</th>
                </tr>
              <tr>
                <th style="text-align:center" width="15%">Ok</th>
                <th style="text-align:center" width="15%">Damage</th>
                <th style="text-align:center" width="15%">Missing</th>
                </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			echo $podata_sql="select * from canabil_master where partcode='".$_REQUEST['partcode']."'  and status='1' ";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=getAnyDetails($podata_row["mapped_partcode"],"part_name","partcode","partcode_master",$link1)."-".$podata_row['mapped_partcode'];?>
                <input type="hidden" name="mapped_partcode<?=$podata_row['sno']?>" id="mapped_partcode<?=$podata_row['sno']?>" value="<?=$podata_row["mapped_partcode"]?>"></td>
				
				  <td style="text-align:right"><?php echo $cnt;?><input type="hidden" name="req_qty<?=$podata_row['sno']?>" id="req_qty<?=$podata_row['sno']?>" value="<?=$cnt;?>"></td>        
                <td align="right"><input type="text" class="digits form-control alert-success" style="width:100px;text-align:right" name="ok_qty<?=$podata_row['sno']?>" id="ok_qty<?=$podata_row['sno']?>"  autocomplete="off" value="<?php echo $cnt?>"  required onBlur="checkRecQty('<?=$podata_row['sno']?>');myFunction(this.value,'<?=$podata_row['sno']?>','ok_qty');"></td>
                <td align="right"><input type="text" class="digits form-control alert-success" style="width:100px;text-align:right" name="broken<?=$podata_row['sno']?>" id="broken<?=$podata_row['sno']?>"  autocomplete="off" value="<?php echo round($podata_row['broken'])?>"  required onBlur="checkRecQty('<?=$podata_row['sno']?>');myFunction(this.value,'<?=$podata_row['sno']?>','broken');"></td>
                <td align="right"><input type="text" class="digits form-control" style="width:100px;text-align:right; background-color:#CCC" name="miss_qty<?=$podata_row['sno']?>" id="miss_qty<?=$podata_row['sno']?>" value="<?php echo round($podata_row['missing'])?>"   autocomplete="off" readonly></td>
                </tr>
              
            <?php
			$i++;
			}
			?>
            <tr>
                <td colspan="4" align="right"><strong>Receive Remark <span style="color:#F00">*</span></strong></td>
                <td colspan="3"><textarea name="rcv_rmk" id="rcv_rmk" class="form-control required" style="resize:none;width:300px;" required></textarea></td>
                </tr>
            <tr>
              <td colspan="7" align="center"><input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Receive" title="Receive">&nbsp;
                    <input name="refno" id="refno" type="hidden" value="<?=base64_encode($po_row['challan_no'])?>"/>
                   <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='inventory_stock_in.php?<?=$pagenav?>'"></td>
              </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    </form>
  </div><!--close panel group-->
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>