<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
////////////////////////////////////////// fetching datta from master table///////////////////////////////////////////////
$po_sql="select * from billing_master where challan_no='".$docid."' ";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);

$msg="";
///// after hitting receive button ///
if($_POST){
 if ($_POST['upd']=='Receive'){}
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
      <h2 align="center"><i class="fa fa-inbox"></i> Stock In Receive with TAG /<?php echo SERIALNO ?></h2><br/>
   <div class="panel-group">
   <form id="frm2" name="frm2" class="form-horizontal"  method="post">
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
                <th style="text-align:center" width="15%" rowspan="2">TAG /<?php echo SERIALNO ?></th>
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
			$podata_sql="select * from billing_product_items where challan_no='".$docid."' ";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$podata_row['part_name'];?></td>
				 <td style="text-align:right"><?=$podata_row['hsn_code'];?></td>
				  <td style="text-align:right"><?=$podata_row['qty']?></td>        
                <td align="right"><input type="text" class="digits form-control" style="width:100px;text-align:right" name="ok_qty" id="ok_qty"  autocomplete="off" value="<?php echo round($podata_row['okqty'])?>"  readonly></td>
                <td align="right"><input type="text" class="digits form-control " style="width:100px;text-align:right" name="broken" id="broken"  autocomplete="off" value="<?php echo round($podata_row['broken'])?>"  readonly></td>
                <td align="right"><input type="text" class="digits form-control" style="width:100px;text-align:right; background-color:#CCC" name="miss_qty" id="miss_qty" value="<?php echo round($podata_row['missing'])?>"   autocomplete="off" readonly></td>               
                <td><?php if($podata_row['attach_file'] != '' &&  $podata_row['imei_attach'] == 'Y' ){?><a href='inventory_stockin_imeisave.php?refid=<?=base64_encode($podata_row['challan_no'])?>&partcode=<?=$podata_row['partcode'];?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg faicon" title="view details"></i></a><?php } else if( $podata_row['imei_attach'] == 'R' ) {echo "Received" ;}else { echo " Not Attached" ;}?></td>
                </tr>
              
            <?php
			$i++;
			}
			?>
            
            <tr>
            <td colspan="8" align="center"s>
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