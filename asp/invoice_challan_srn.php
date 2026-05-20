<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
$po_sql="SELECT * FROM billing_master where challan_no='".$docid."' and from_location='".$_SESSION['asc_code']."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);

?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
});
</script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
   <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-reply-all"></i> Sale Return View</h2><br/>
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading">Party Information</div>
         <div class="panel-body">
         
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Billing To</label></td>
                <td width="30%">
				  <?php 
				  /// bill to party
				  echo $po_row['to_location'];?></td>
                <td width="20%"><label class="control-label">Billing From</label></td>
                <td width="30%"><?php echo str_replace("~",",",getAnyDetails($po_row['from_location'],"locationname,cityid,stateid","location_code","location_master",$link1));?></td>
              </tr>
              <tr>
                <td><label class="control-label">Invoice No.</label></td>
                <td><?php echo $po_row['challan_no'];?></td>
                <td><label class="control-label">Billing Date</label></td>
                <td><?php echo dt_format($po_row['sale_date']);?></td>
              </tr>
              <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><?php echo $po_row['logged_by'];?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo getdispatchstatus($po_row['status']);?></td>
              </tr>
              <tr>
                <td><label class="control-label">State</label></td>
                <td><?php echo getAnyDetails($po_row["to_stateid"],"state","stateid","state_master",$link1);?></td>
                <td><label class="control-label">Address</label></td>
                <td><?php echo $po_row['deliv_addrs'];?></td>
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
                <th style="text-align:center" width="5%">#</th>
                <th style="text-align:center" width="20%">Product</th>
                <th style="text-align:center" width="8%">HSN Code</th>
                <th style="text-align:center" width="8%">Bill Qty</th>
                <th style="text-align:center" width="8%">Price</th>                
                <th style="text-align:center" width="11%">Discount/<br>Unit</th>
                <th style="text-align:center" width="8%">Value After Discount</th>
                <?php if($po_row['to_stateid']==$po_row['from_stateid']){ ?>
                <th style="text-align:center" width="12%">SGST(%)</th>
                <th style="text-align:center" width="12%">SGST Amount</th>
                <th style="text-align:center" width="12%">CGST(%)</th>
                <th style="text-align:center" width="12%">CGST Amount</th>
                <?php }else{ ?>
                <th style="text-align:center" width="12%">IGST(%)</th>
                <th style="text-align:center" width="12%">IGST Amount</th>
                <?php }?>
                <th style="text-align:center" width="15%">Total</th>
				  <th style="text-align:center" width="15%">Old Invoice No.</th>
               <!--  <th style="text-align:center" width="15%">IMEI Upload</th>-->
               
              </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="SELECT * FROM billing_product_items where challan_no='".$docid."' and from_location='".$po_row['from_location']."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$proddet=explode("~",getAnyDetails($podata_row['partcode'],"part_name,product_id,brand_id,hsn_code,part_category","partcode","partcode_master",$link1));
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet[0]." (".$podata_row['partcode'].")"?></td>
                <td style="text-align:right"><?=$proddet[3]?></td>
                <td style="text-align:right"><?=$podata_row['qty']?></td>
                <td style="text-align:right"><?=$podata_row['price']?></td>
                <td style="text-align:right"><?=$podata_row['discount_amt']?></td>
                <td style="text-align:right"><?=$podata_row['price'] - $podata_row['discount_amt']*$podata_row['qty']?></td>
                <?php if($po_row['to_stateid']==$po_row['from_stateid']){ ?>
                <td style="text-align:right"><?=$podata_row['sgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['sgst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_amt']?></td>
                <?php }else{ ?>
                <td style="text-align:right"><?=$podata_row['igst_per']?></td>
                <td style="text-align:right"><?=$podata_row['igst_amt']?></td>
                <?php }?>
                <td style="text-align:right"><?=$podata_row['item_total']?></td>
				<td style="text-align:right"><?=$podata_row['old_challan']?></td>
             <!--   <td>			
				<?php 
					 if($proddet[4]=="UNIT" || $proddet[4]=="BOX"){
						if($podata_row['imei_attach']==""){
						 if($podata_row['type']=="Stock Transfer" || $podata_row['type']=="Sale Return" ){ $grntype="STN";}else{ $grntype="";}
						 ?>
						 <?php /*?><a href="#" onClick="uploadIMEI('<?=$podata_row['id']?>');" title="Upload IMEI"><i class="fa fa-upload fa-lg faicon"></i></a>&nbsp;&nbsp;<?php */?>
						 <a href="invoice_imei_loc.php?refid=<?=base64_encode($podata_row['challan_no'])?>&partcode=<?=base64_encode($podata_row['partcode'])?>&pqty=<?=base64_encode($podata_row['qty'])?>&partname=<?=base64_encode($make_partname)?>&model_id=<?=base64_encode($podata_row['model_id'])?>&grn_type=<?=$grntype?>&status=<?=$po_row['status']?>&id=<?=$po_row['id']?>&doc=<?=$po_row['doc_type']?><?=$pagenav?>" title="Enter IMEI"><i class="fa fa-shower fa-lg faicon"></i></a>
						 <?php 
						}else{
							echo "Attached";
						}
					 }else{
						 echo "Not Applicable";
					 }
					?>	
				</td>-->
                 
              </tr>
            <?php
			$i++;
			}
			?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-default table-responsive">
      <div class="panel-heading">Amount Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Sub Total</label></td>
                <td width="30%"><?php echo currencyFormat($po_row['basic_cost']);?></td>
                <td width="20%"><label class="control-label">Total Discount</label></td>
                <td width="30%"><?php echo currencyFormat($po_row['discount_amt']);?></td>
              </tr>
              <tr>                
                <td><label class="control-label">Round Off</label></td>
                <td><?php echo currencyFormat($po_row['round_off']);?></td>
                <td><label class="control-label">Grand Total</label></td>
                <td><?php echo currencyFormat($po_row['total_cost']);?></td>
              </tr>
               <tr>
                <td><label class="control-label">Delivery Address</label></td>
                <td><?php echo $po_row['deliv_addrs'];?></td>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo $po_row['billing_rmk'];?></td>
              </tr>
              <?php if($po_row['status']=="Cancelled"){ ?> 
              <tr>
				 <td><label class="control-label">Cancel By</label></td>
                 <td><?php echo getAdminDetails ($po_row['cancel_by'],"name",$link1);?></td>
				 <td><label class="control-label">Cancel Date</label></td>
                 <td ><?php echo dt_format ($po_row['cancel_date']);?></td>
				 </tr>
				<tr>                 
				 <td><label class="control-label">Cancel Remark</label></td>
                 <td colspan="3"><?php echo $po_row['cancel_rmk'];?></td>
                </tr>
			  <?php }?>
              
            <!--  <tr>  <form id="frm1" name="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
              <td>Imei Upload</td>
              <td><label >  
                       <span>
                        <input type="file"  name="file"  required class="form-control"   accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/ > 
                      
                         <input name="challan" type="hidden" class="form-control" id="challan" value="<?=$docid?>"/>	
                          <input name="location" type="hidden" class="form-control" id="challan" value="<?=$po_row['to_location']?>"/>					          
                    </span>
                    </label></td>
                    <td> <div style="display:inline-block;float:right"><a href="../templates/PO_DISPATCH_IMEI.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/></a></div></td>
                 <td colspan="4" align="center"><input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>></td>
                 </form>
                </tr>
              -->
              
              <tr>
              
              
                 <td colspan="4" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='asp_sale_return.php?daterange=<?=$_REQUEST['daterange']?>&status=<?=$_REQUEST['status']?>&doc_type=<?=$_REQUEST['doc_type']?><?=$pagenav?>'"></td>
                </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <?php /*?><div class="panel panel-default table-responsive">
      <div class="panel-heading">Logistic Information</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Logistic Name</label></td>
                <td width="30%"><?php echo getLogistic($po_row['diesel_code'],$link1);?></td>
                <td width="20%"><label class="control-label">Docket No.</label></td>
                <td width="30%"><?php echo $po_row['docket_no'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Logistic Person</label></td>
                <td><?php echo $po_row['logistic_person'];?></td>
                <td><label class="control-label">Contact No.</label></td>
                <td><?php echo $po_row['logistic_contact'];?></td>
              </tr>
               <tr>
                 <td><label class="control-label">Carrier No.</label></td>
                 <td><?php echo $po_row['vehical_no'];?></td>
                 <td><label class="control-label">Dispatch Date</label></td>
                 <td><?php echo $po_row['dc_date'];?></td>
               </tr>
               <tr>
                <td><label class="control-label">Dispatch Remark</label></td>
                <td colspan="3"><?php echo $po_row['disp_rmk'];?></td>
                </tr>
               <tr>
                 <td colspan="4" align="center"><input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='retailbillinglist.php?<?=$pagenav?>'"></td>
                </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel--><?php */?>
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