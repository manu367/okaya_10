<?php
require_once("../includes/config.php");
//$arrstatus = getFullStatus("process",$link1);
$docid=base64_decode($_REQUEST['refid']);
//// job details
$job_sql="SELECT * FROM po_master where po_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);

$locinfo= mysqli_fetch_array(mysqli_query($link1,"select * from location_master where location_code='".$job_row['from_code']."' "));
/////credit limit of location
$cr_limit = getAnyDetails($job_row["from_code"],"total_credit_limit","location_code","current_cr_status",$link1);
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
 <script src="../js/common_js.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script>
	$(document).ready(function(){
        $("#frm1").validate();
    });
	/////// function for calculate line wise item
	function calculateline(indx){
		////// get dispatching qty
		if(document.getElementById("disp_qty"+indx).value){
			var dispqty = document.getElementById("disp_qty"+indx).value;
		}else{
			var dispqty = 0.00;
		}
		////// get requested qty
		if(document.getElementById("req_qty"+indx).value){
			var reqqty = document.getElementById("req_qty"+indx).value;
		}else{
			var reqqty = 0.00;
		}
		////// get available qty
		if(document.getElementById("avl_qty"+indx).value){
			var avlqty = document.getElementById("avl_qty"+indx).value;
		}else{
			var avlqty = 0.00;
		}
		////// get part price
		if(document.getElementById("price"+indx).value){
			var partprice = document.getElementById("price"+indx).value;
		}else{
			var partprice = 0.00;
		}
		//// check dispatch qty should not more then requested qty
		if( parseFloat(reqqty) < parseFloat(dispqty) ){
			document.getElementById("errormsg").innerHTML = "Dispatch qty should not be more than requested qty.<br/>";
			document.getElementById("disp_qty"+indx).className="digits form-control alert-danger";
			document.getElementById("save").disabled = true;
		}else if( parseFloat(avlqty) < parseFloat(dispqty) ){
			document.getElementById("errormsg").innerHTML = "Qty is not available in inventory.<br/>";
			document.getElementById("disp_qty"+indx).className="digits form-control alert-danger";
			document.getElementById("save").disabled = true;
		}else{
			document.getElementById("errormsg").innerHTML = "";
			document.getElementById("disp_qty"+indx).className="digits form-control alert-success";
			document.getElementById("save").disabled = false;
			var linetotal = 0.00;
			linetotal = parseFloat(partprice) * parseFloat(dispqty);
			document.getElementById("amtid"+indx).innerHTML = currencyFormat(linetotal);
		}
		///// call function to calculate total
		calculatetotal();
	}
	/////// function for calculate total item
	function calculatetotal(){
		var maxrow = document.getElementById("noofrows").value;
		var totalamount = 0.00;
		var totaldispatchpqty = 0;
		////// begin loop from row 1 to max row
		for(var i=1; i<maxrow; i++){
			////// get dispatching qty
			if(document.getElementById("disp_qty"+i).value){
				var dispqty = document.getElementById("disp_qty"+i).value;
			}else{
				var dispqty = 0.00;
			}
			////// get requested qty
			if(document.getElementById("req_qty"+i).value){
				var reqqty = document.getElementById("req_qty"+i).value;
			}else{
				var reqqty = 0.00;
			}
			////// get part price
			if(document.getElementById("price"+i).value){
				var partprice = document.getElementById("price"+i).value;
			}else{
				var partprice = 0.00;
			}
			////calculate
			totalamount += parseFloat(partprice) * parseFloat(dispqty);
			totaldispatchpqty += parseInt(dispqty);
		}///end for loop
		document.getElementById("totalamtid").innerHTML = currencyFormat(totalamount);
		document.getElementById("totaldispqty").innerHTML = totaldispatchpqty;
		///// check if total dispatch qty is 0
		if(parseInt(totaldispatchpqty) == 0){
			document.getElementById("save").disabled = true;
		}
	}
 </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
   <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-shopping-bag"></i> Dispatch Location PO/PNA</h2>
      <h4 align="center">PO No.- <?=$docid?></h4>
   <div class="panel-group">
	<form class="form-horizontal" role="form" name="frm1" id="frm1" action="dispatch_pnapo_save.php" method="post">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;PO Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Location Name</label></td>
                <td width="30%"><?php echo $locinfo['locationname'];?></td>
                <td width="20%"><label class="control-label">PO No.</label></td>
                <td width="30%"><?php echo $job_row['po_no'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Address</label></td>
                <td><?php echo $locinfo['locationaddress'];?></td>
                <td><label class="control-label">PO  Date.</label></td>
                <td><?php echo dt_format($job_row['po_date']);?></td>
              </tr>
              <tr>
                <td><label class="control-label">State</label></td>
                <td><?php echo getAnyDetails($job_row["to_state"],"state","stateid","state_master",$link1);?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo getdispatchstatus($job_row['status']);?></td>
              </tr>
              <tr>
                <td><label class="control-label">Credit Balance</label></td>
                <td class="red_small"><?php echo currencyFormat($cr_limit);?></td>
                <td><label class="control-label">Request Type</label></td>
                <td><?php echo $job_row['potype'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Document Type</label></td>
                <td><select name="doc_type" id="doc_type" class="form-control">
                     <option value="INV" selected>Invoice</option>
                     <option value="DC">Delivery Challan</option>
                    </select></td>
                <td><label class="control-label">FCA Invoice No.</label></td>
                <td><input name="finvoice_no" id="finvoice_no" type="text" class="form-control" maxlength="30"/></td>
              </tr>
              <tr>
                <td><label class="control-label">Courier Name</label></td>
                <td><input name="courier_name" id="courier_name" type="text" class="form-control" maxlength="50" /></td>
                <td><label class="control-label">Docket No.</label></td>
                <td><input name="docket_no" id="docket_no" type="text" class="form-control"/></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->

  <div class="panel panel-info table-responsive">
     <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;PO Items Details</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr>
                <th width="3%">#</th>
                <th width="12%">Product</th>
                <th width="10%">Brand</th>
                <th width="12%">Model</th>
                <th width="15%">Partcode</th>
                <th width="10%">Price</th>
                <th width="8%">Req./Pend. Qty</th>
                <th width="10%">Dispatch Qty</th>
                <th width="10%">Amount</th>
                <th width="10%">Available Qty</th>
				</tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$tot_reqqty = 0;
			$tot_pendqty = 0;
			$podata_sql="SELECT * FROM po_items where po_no='".$job_row['po_no']."' and qty!=processed_qty";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$proddet=explode("~",getAnyDetails($podata_row['product_id'],"product_name","product_id","product_master",$link1));
				$brand=explode("~",getAnyDetails($podata_row['brand_id'],"brand","brand_id","brand_master",$link1));
				$model=explode("~",getAnyDetails($podata_row['model_id'],"model","model_id","model_master",$link1));
				$part=explode("~",getAnyDetails($podata_row['partcode'],"part_name,l1_price,l2_price,l3_price,l4_price,l5_price","partcode","partcode_master",$link1));
				///// get available inventory
				$avlqty = getInventory($_SESSION['asc_code'],$podata_row['partcode'],"okqty",$link1);
				$pend_qty = $podata_row['qty'] - $podata_row['processed_qty'];
				if ($locinfo['price_lvl']=='L1'){
					$price_asp=$part[1];
		         }
				 else if($locinfo['price_lvl']=='L2'){
					 $price_asp=$part[2];
					 }
					  else if($locinfo['price_lvl']=='L3'){
					 $price_asp=$part[3];
					 }
					  else if($locinfo['price_lvl']=='L4'){
					 $price_asp=$part[4];
					 }
					 else if($locinfo['price_lvl']=='L5'){
					 $price_asp=$part[5];
					 } else{
						 $price_asp='';
						 }
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet[0]?></td>
                <td><?=$brand[0]?></td>
                <td><?=$model[0]?></td>
                <td><?=$part[0]." (".$podata_row['partcode'].")"?></td>
                <td align="right"><?=currencyFormat($price_asp)?><input name="price<?=$podata_row['id']?>" id="price<?=$i?>" type="hidden" value="<?=$price_asp?>"/></td>
                <td align="right"><?=$podata_row['qty']." / ".$pend_qty?><input name="req_qty<?=$podata_row['id']?>" id="req_qty<?=$i?>" type="hidden" value="<?=$pend_qty?>"/></td>
                <td align="right"><input name="disp_qty<?=$podata_row['id']?>" id="disp_qty<?=$i?>" type="text" class="digits form-control alert-success" required style="width:80px;text-align:right" onKeyUp="calculateline('<?=$i?>');"></td>
                <td align="right"><span id="amtid<?=$i?>"><?=currencyFormat(0.00);?></span></td>
                <td align="right"><input name="avl_qty<?=$podata_row['id']?>" id="avl_qty<?=$i?>" type="text" class="form-control" readonly style="width:80px;text-align:right;background-color:#CCCCCC" value="<?=$avlqty?>" /></td>    
				</tr>
            <?php
			  $tot_reqqty += $podata_row['qty'];
			  $tot_pendqty += $pend_qty;
			$i++;
			}
			?>
            <tr>
                <td colspan="6" align="right"><strong>Total</strong></td>
                <td align="right"><span id="totalreqqty"><?=$tot_reqqty." / ".$tot_pendqty?></span></td>
                <td align="right"><span id="totaldispqty">0</span></td>
                <td align="right"><span id="totalamtid"><?=currencyFormat(0.00);?></span></td>
                <td align="right">&nbsp;</td>
              </tr>
			 <tr>
                <td align="center" colspan="10"><span id="errormsg" class="red_small"></span>
                   <input type="submit" class="btn<?=$btncolor?>" name="dispatchpo" id="save" value="Dispatch" title="" <?php if($_POST['Submit']=='Dispatch'){?>disabled<?php }?>>
                   <input name="noofrows" id="noofrows" type="hidden" value="<?=$i?>"/>&nbsp;&nbsp;
                   <input name="refid" id="refid" type="hidden" value="<?=base64_encode($docid)?>"/>
                   <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               	   <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                   <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='dispatch_pna_po.php?<?=$pagenav?>'">
                 </td>
                </tr>
            </tbody>
          </table>
          
      </div><!--close panel body-->
    </div><!--close panel-->
    </form>
    </div>
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>