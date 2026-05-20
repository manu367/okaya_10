<?php
require_once("../includes/config.php");
//////////////// decode challan number////////////////////////////////////////////////////////
$po_no = base64_decode($_REQUEST['refid']);
////////////////////////////////////////// fetching datta from table///////////////////////////////////////////////
$po_sql="select * from billing_product_items where challan_no='".$po_no."' group by challan_no ";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);


/////////////////////     fetchong data from master table ///////////////////////////////////////////////////////

$po_master="select * from billing_master where challan_no='".$po_no."'  ";
$po_res1=mysqli_query($link1,$po_master);
$po_row1=mysqli_fetch_assoc($po_res1);



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
 <script type="text/javascript">
$(document).ready(function(){
    $("#frm2").validate();
});
</script>
<script type="text/javascript">
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-reply-all"></i>  Reconciliation View</h2><br/>
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"> Reconciliation Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">From Location Name:</label></td>
                <td width="30%"><?php echo getAnyDetails($po_row["from_location"],"locationname","location_code","location_master",$link1)."(".$po_row['from_location'].")";?></td>
                <td width="20%"><label class="control-label">To Location Name:</label></td>
                <td width="30%"><?php echo getAnyDetails($po_row["to_location"],"locationname","location_code","location_master",$link1)."(".$po_row['to_location'].")";?></td>
              </tr>
              <tr>
                <td><label class="control-label">From State</label></td>
                <td><?php echo getAnyDetails($po_row1['from_stateid'],"state","stateid","state_master",$link1 );?></td>
                <td><label class="control-label">To State</label></td>
                <td><?php echo getAnyDetails($po_row1['to_stateid'],"state","stateid","state_master",$link1 );?></td>
              </tr>
              <tr>
                <td><label class="control-label">Address</label></td>
                <td><?php echo $po_row1['from_addrs'];?></td>
                <td><label class="control-label">Address</label></td>
                <td><?php echo $po_row1['to_addrs'];?></td>
              </tr>  
			  <tr>
                <td><label class="control-label">Challan No.</label></td>
                <td><?php echo $po_row['challan_no'];?></td>
                <td><label class="control-label">Entry Date</label></td>
                <td><?php echo dt_format($po_row1['sale_date']);?></td>
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
            <tr class="<?=$tableheadcolor?>">
               <td>S.No</td>
              <td>Challan No</td>
              <td>Partcode</td>
              <td>OK</td>
              <td>Damaged</td>
              <td>Missing</td>
			  <td>Total Amt</td>
            </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
		 	$data_sql="select * from billing_product_items where challan_no='".$po_no."' and missing>0 ";
			$data_res=mysqli_query($link1,$data_sql);
			while($data_row=mysqli_fetch_assoc($data_res)){
			?>
              <tr>
                <td><?=$i?></td>
               <td><?=$data_row['challan_no']?></td>
              <td><?=getAnyDetails($data_row['partcode'],"part_desc","partcode","partcode_master",$link1)." - (".$data_row['partcode'].")";?></td>
               <td style="text-align:right"><?=$data_row['okqty'];?></td>
                <td style="text-align:right"><?=$data_row['broken'];?></td>
                <td style="text-align:right"><?=$data_row['missing'];?> </td>
			  <td><?=$data_row['item_total'];?></td>           
                </tr>
            <?php
			$i++;
			}
			?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Receive</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>          
               <tr>
			   <td><label class="control-label">Total Amount</label></td>
                 <td><input type="text" name="tot_amt" id="tot_amt" class="number form-control required"   value="<?=$total;?>"  readonly/></td>
                   <td><label class="control-label">Receive Remark <span style="color:#F00">*</span></label></td>
                 <td><textarea  name="rcv_rmk" id="rcv_rmk" class="form-control required" readonly/><?=$po_row['opration_rmk'];?></textarea></td>
                   
                 </tr>
               <tr>
                 <td colspan="4" align="center">           
                   <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='reco_mss_dmd_list.php?<?=$pagenav?>'">
                 </td>
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
