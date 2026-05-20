
<?php 
require_once("../includes/config.php");

$docid = base64_decode($_REQUEST['refid']);

##################
/// BY HEMANT, 2024
 $po_sql = "select * from billing_master where challan_no='".$docid."' and po_type in ('PNA','PO','Opening Stock','P2C','STN','PICKUP NOTE') AND status='3'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
//print_r($po_row);exit;
if(!$po_row || $po_row['status']!=3)
{
	$cflag="danger";
	$cmsg="Failed";
	$error_msg = "You can't perform this action!";
	header("location:inventory_stock_in.php?msg=".$error_msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
	exit;
}
##################

$msg="";
///// after hitting receive button ///
if($_POST)
{
	barCheck($link1);
	if($_POST['upd']=='Receive')
	{
		mysqli_autocommit($link1, false);
		$flag = true;
		$error_msg="";
		
		$ref_no = base64_decode($_POST['refno']);
		
		/*
		$current_status = getAnyDetails($docid,"status","challan_no","billing_master",$link1);
		if($current_status != '3')
		{
			$flag = false;
			$error_msg = "You can't perform this action!";
		}
		*/

		if($flag)
		{
			$sql_po_data = "SELECT * FROM billing_product_items WHERE challan_no='".$ref_no."'"; // AND status='3'";
			$res_poData = mysqli_query($link1, $sql_po_data);
			if($res_poData)
			{
				if(mysqli_num_rows($res_poData) > 0)
				{
					while($row_poData=mysqli_fetch_assoc($res_poData))
					{
						/////
						$reqqty="req_qty".$row_poData['id'];
						/////
						$okqty="ok_qty".$row_poData['id'];
						$ok_qty = (int)$_POST[$okqty];
						$damageqty="broken".$row_poData['id'];
						$dam_qty = (int)$_POST[$damageqty];
						/////
						$tot_qty = $ok_qty+$dam_qty;

						$expected_qty = (int)$row_poData['qty'];
						$processed_qty = $tot_qty;
						if($expected_qty != $processed_qty)
						{
							$flag = false;
							$error_msg = "Error : Processed quantity isn't matched with expected quantity for part ".$row_poData['partcode']."!";
							break;
						}

						/// QTY Credited to (TO LOCATION)
						$sql_sci = "SELECT id,location_code,partcode,in_transit from client_inventory WHERE partcode='".$row_poData['partcode']."' AND location_code='".$po_row['to_location']."'";
						$res_sci = mysqli_query($link1, $sql_sci);
						if($res_sci)
						{
							if(mysqli_num_rows($res_sci) > 0)
							{
								$row_sci = mysqli_fetch_assoc($res_sci);
								$in_trans = (int)$row_sci['in_transit'];
								if($in_trans <= 0)
								{
									$flag = false;
									$error_msg = "Error : SCI (insufficient IN-Transit Qty)!";
									break;
								}
								
								$sql_uci = "UPDATE client_inventory SET okqty=(okqty+".$ok_qty."), broken=(broken+".$dam_qty."), in_transit=(in_transit-".$tot_qty."), updatedate='".$datetime."' WHERE partcode='".$row_poData['partcode']."' AND location_code='".$po_row['to_location']."'";
								$res_uci = mysqli_query($link1, $sql_uci);
								if($res_uci)
								{
									if(mysqli_affected_rows($link1)==0)
									{
										$flag = false;
										$error_msg = "Error : UCI (Unable to update)!";
										break;
									}
								}
								else
								{
									$flag = false;
									$error_msg = "Error : UCI";
									break;
								}
							}
							else
							{
								$sql_ici = "INSERT into client_inventory set location_code='".$po_row['to_location']."', partcode='".$row_poData['partcode']."', okqty='".$ok_qty."', broken='".$dam_qty."', updatedate='".$datetime."'";
								$res_ici = mysqli_query($link1, $sql_ici);
								if($res_ici)
								{
									if(mysqli_affected_rows($link1)==0)
									{
										$flag = false;
										$error_msg = "Error : ICI (Unable to insert)!";
										break;
									}
								}
								else
								{
									$flag = false;
									$error_msg = "Error : ICI";
									break;
								}
							}						
						}
						else
						{
							$flag = false;
							$error_msg = "Error : SCI";
							break;
						}

						if($flag)
						{
							if($ok_qty > 0)
							{
								$flag_h1 = stockLedgerO($ref_no, $today, $row_poData['partcode'], $po_row['from_location'],$po_row['to_location'], "IN", "OK", "Stock In", "Receive", $ok_qty, $row_poData['price'], $_SESSION['userid'], $today, $currtime, $ip, $link1, $flag,$po_row['to_location']);
								if(!$flag_h1)
								{
									$flag = false;
									$error_msg = "Error : H1 (Unable to record history)!";
									break;
								}
							}		
						}

						if($flag)
						{
							if($dam_qty > 0)
							{
								$flag_h2 = stockLedgerO($ref_no, $today, $row_poData['partcode'],  $po_row['from_location'],$po_row['to_location'], "IN", "DAMAGE", "Stock In", "Receive", $dam_qty, $row_poData['price'], $_SESSION['userid'], $today, $currtime, $ip, $link1, $flag,$po_row['to_location']);
								if(!$flag_h2)
								{
									$flag = false;
									$error_msg = "Error : H2 (Unable to record history)!";
									break;
								}
							}
						}

						if($flag)
						{
							$sql_ubpi = "UPDATE billing_product_items SET okqty='".$ok_qty."', broken='".$dam_qty."' WHERE id='".$row_poData['id']."'";
							$res_ubpi = mysqli_query($link1, $sql_ubpi);
							if($res_ubpi)
							{
								if(mysqli_affected_rows($link1) == 0)
								{
									/* currently not in use
									$flag = false;
									$error_msg = "Error : UBPI (Unable to update item data)!";
									break;
									*/
								}
							}
							else
							{
								$flag = false;
								$error_msg = "Error : UBPI";
								break;
							}								   
						}

						if($flag)
						{
							//// update substatus of jobsheet data 
							if($row_poData['job_no']!="" && $ok_qty > 0)
							{
								$sql_jd = "SELECT status FROM jobsheet_data WHERE job_no='".$row_poData['job_no']."'";
								$res_jd=mysqli_query($link1,$sql_jd);
								if($res_jd)
								{
									if(mysqli_fetch_assoc($res_jd) > 0)
									{
										$row_job=mysqli_fetch_assoc($rs_jd);
										if($row_job['status']=='3')
										{
											$sql_ujd = "UPDATE jobsheet_data SET sub_status='34' WHERE job_no='".$row_poData['job_no']."'";
											$res_ujd = mysqli_query($link1, $sql_ujd);
											if($res_ujd)
											{
												if(mysqli_affected_rows($link1)==0)
												{
													$flag = false;
													$error_msg = "Error : UJD (Unable to update job status)!";
													break;
												}
												else
												{
													$flag_h3 = callHistory($row_poData['job_no'], $_SESSION['asc_code'], "34", "PNA Part Received", "Part Received", $_SESSION['userid'], "", "", "", "", $ip, $link1, $flag);
													if(!$flag_h3)
													{
														$flag = false;
														$error_msg = "Error : H3 (Unable to record history)!";
														break;
													}
												}
											}
											else
											{
												$flag = false;
												$error_msg = "Error : UJD";
												break;
											}
										}
									}
									else
									{
										$flag = false;
										$error_msg = "Error : SJD (Couldn't found job info.)!";
										break;
									}
								}
								else
								{
									$flag = false;
									$error_msg = "Error : SJD";
									break;
								}
							}
						}
					}
				}
				else
				{
					$flag = false;
					$error_msg = "Error : SBPI (Nothing to receive)!";
				}
			}
			else
			{
				$flag = false;
				$error_msg = "Error : SBPI";
			}
		}
		
		if($flag)
		{
			$sql_ubm = "UPDATE billing_master SET status='4', receive_date='".$today."', rcv_rmk='".$_POST['rcv_rmk']."' WHERE challan_no ='".$ref_no."'";
			$res_ubm = mysqli_query($link1, $sql_ubm);
			if($res_ubm)
			{
				if(mysqli_affected_rows($link1)==0)
				{
					$flag = false;
					$error_msg = "Error : UBM (Unable to update billing data)!";
				}
			}
			else
			{
				$flag = false;
				$error_msg = "Error : UBM";
			}
		}
		
		if($flag){
			//$flag_h4 = dailyActivity($_SESSION['userid'],$ref_no,"Stock In","RECEIVE",$ip,$link1,$flag);
			$flag_h4 = dailyActivity($_SESSION['userid'],$ref_no,"Stock In","RECEIVE",$_SERVER['REMOTE_ADDR'],$link1,$flag);
			/*if(!$flag_h4)
			{
				$flag = false;
				$error_msg = "Error : H4 (Unable to record activity)!";
				break;
			}*/			
		}

		if($flag)
		{
			mysqli_commit($link1);
			$msg="Stock is successfully received against ".$docid;
			$cflag="success";
			$cmsg="Success";
		}
		else
		{
			mysqli_rollback($link1);
			$cflag="danger";
			$cmsg="Failed";
			$msg = $error_msg;
		} 
		mysqli_close($link1);

		header("location:inventory_stock_in.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit;
	}
	else
	{
		exit('invalid request!');
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
      <h2 align="center"><i class="fa fa-inbox"></i> Stock In Receive</h2><br/>
   <div class="panel-group">
   <form id="frm2" name="frm2" class="form-horizontal" onsubmit="return really('receive')" action="" method="post">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading">Party Information</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">From </label></td>
                <td width="30%"><?php echo $po_row["bill_from"]."(".$po_row['from_location'].")";?></td>
                <td width="20%"><label class="control-label">To</label></td>
                <td width="30%"><?php echo $po_row["bill_to"]."(".$po_row['to_location'].")";?></td>
              </tr>
              <tr>
                <td><label class="control-label">From Address</label></td>
                <td><?php echo $po_row['from_addrs'];?></td>
                <td><label class="control-label">To Address</label></td>
                <td><?php echo $po_row['to_addrs'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Document No.</label></td>
                <td><?php echo $po_row['challan_no'];?></td>
                <td><label class="control-label">Document Date</label></td>
                <td><?php echo dt_format($po_row['sale_date']);?></td>
              </tr>  
				<tr>
					<td><label class="control-label">Status</label></td>
					<td><?php echo getdispatchstatus($po_row['status']);?></td>
					<td><label class="control-label"></label></td>
					<td></td>
				</tr>
				
				<tr>
					<td><label class="control-label">Image Attachement</label></td>
					<td>
						<?php
						if($po_row['attach_a'] != "")
						{
						?>
						<div style="max-width:100%;margin:0px auto;">
							<img src="<?=$po_row['attach_a'];?>" alt="Attachement" style="display:block;max-width:inherit;margin:0px auto">
							<button type="button" class="btn btn-primary" name="" id="" onclick= "window.open('<?=$po_row['attach_a'];?>', '_blank');" title="View" style="width:100%;margin-top:5px;background:#33b767;border-color:#149b49;"><i class="fa fa-external-link" style="color:#fff;" aria-hidden="true"></i> View</button>
						</div>
						<?php
						}
						else
						{
							echo "-";
						}
						?>					
					</td>
					<td><label class="control-label">Video Attachement</label></td>
					<td>
						<?php
						if($po_row['attach_b'] != "")
						{
						?>
						<video width="720" height="240" controls style="max-width:100%;margin:0px auto;background:#000;">
							<source src="<?=$po_row['attach_b'];?>" type="video/mp4">
							Your browser does not support the video tag.
						</video>
						<button type="button" class="btn btn-primary" name="" id="" onclick= "window.open('<?=$po_row['attach_b'];?>', '_blank');" title="View" style="width:100%;background:#33b767;border-color:#149b49;"><i class="fa fa-external-link" style="color:#fff;" aria-hidden="true"></i> View</button>
						<?php
						}
						else
						{
							echo "-";
						}
						?>
					</td>
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
				<th width="8%" rowspan="2" style="text-align:center">HSN Code</th>
                <th width="9%" rowspan="2" style="text-align:center">Dispatched Qty</th>
                <th colspan="3" style="text-align:center">Receive Qty</th>
                </tr>
              <tr>
				  <?php if($po_row['po_type']=='PICKUP NOTE'){?>
                  <th style="text-align:center" width="15%">Faulty</th>
				  <?php }else{ ?>
				  <th style="text-align:center" width="15%">Ok</th>
				  <?php } ?>
                <th style="text-align:center" width="15%">Damage</th>
                <!-- <th style="text-align:center" width="15%">Missing</th> -->
                </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="select * from billing_product_items where challan_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$podata_row['part_name']."(".$podata_row['partcode'].")";?></td>
				 <td style="text-align:right"><?=$podata_row['hsn_code'];?></td>
				  <td style="text-align:right"><?=$podata_row['qty']?><input type="hidden" name="req_qty<?=$podata_row['id']?>" id="req_qty<?=$i?>" value="<?=$podata_row['qty'];?>"></td>   

				   <td align="right"><input type="text" class="digits form-control" readonly style="width:100px;text-align:right" name="ok_qty<?=$podata_row['id']?>" id="ok_qty<?=$i?>"  autocomplete="off" value="<?php echo round($podata_row['okqty'])?>"  required onBlur="checkRecQty('<?=$i?>');myFunction(this.value,'<?=$i?>','ok_qty');"></td>
				  
                <td align="right"><input type="text" class="digits form-control" readonly style="width:100px;text-align:right" name="broken<?=$podata_row['id']?>" id="broken<?=$i?>"  autocomplete="off" value="<?php echo round($podata_row['broken'])?>"  required onBlur="checkRecQty('<?=$i?>');myFunction(this.value,'<?=$i?>','broken');"></td>
                <!-- <td align="right"><input type="text" class="digits form-control" readonly style="width:100px;text-align:right;" name="miss_qty<?=$podata_row['id']?>" id="miss_qty<?=$i?>" value="<?php echo round($podata_row['missing'])?>"   autocomplete="off" readonly></td> -->
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