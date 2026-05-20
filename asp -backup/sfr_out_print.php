<?php
require_once("../includes/config.php");
/////////////////////// fetching data from master table ///////////////////////////////////////////////
$rs=mysqli_query($link1,"select * from sfr_challan where challan_no='".$_REQUEST['challan_no']."' ");
$row=mysqli_fetch_array($rs);
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
$(document).ready(function(){
    $('#myTable').dataTable();
});
</script>
<title><?=siteTitle?></title>
</head>
<body onLoad="window.print()">
<div class="container-fluid">
  <div class="row content">
	<?php 
       include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-user-circle-o"></i> SFR Document</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
      <form class="form-horizontal" role="form">
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
		  <tr><td width="50%" colspan="8"><b><?=$row['from_location']?></b>
                              <br>
                              <?=$row['from_address']?>
                           
                            GSTIN No.-  
                            <b><?=$row['from_gst_no']?></b></td>
                            <td width="50%" colspan="8">Challan No - <strong>
                            <?=$row['challan_no']?>
                            </strong><br>
                               Sale Date -
                          <?=dt_format($row['challan_date'])?></td>
               </tr>
			   <tr>
                        <td colspan="8"><strong>Bill To :</strong><br>
                              <b>
                              <?=$row['to_location']?>
                              </b><br>
                               <?=$row['to_address']?>
                              <br>
                              <?=$row['to_state']?>
                              <br>
                          GSTIN No.-<b>
                          <?=$row['to_gst_no']?>
                          </b></td>
                          <td colspan="8"><strong>Ship To :</strong><br>
                              <b>
                              <?=$row['to_location']?>
                              </b><br><?=$row['deliv_addrs']?><br><?=$row['to_state']?><br>GSTIN No.-<b><?=$row['to_gst_no']?></b></td>
                        </tr>
            <tr>
              <td>S.No</td>
              <td>Item Description</td>
              <td>HSN Code</td>
              <td>Qty</td>
              <td>Unit</td>
              <td>Rate</td>
              <td>Total</td>
              <td>Discount</td>
              <td>Taxable Value</td>
              <td colspan="2">CGST</td>
			  <td colspan="2">SGST</td>
			  <td colspan="2">IGST</td>
            </tr>
			 <tr>
                 <td colspan="10"  align="right">Rate</td>
                            <td>Amt.</td>
                            <td>Rate</td>
                            <td>Amt.</td>
                            <td>Rate</td>
                            <td>Amt.</td>
                        </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			/////////////////////// fetching data item wise  from data  table ///////////////////////////////////////////////
             	$res=mysqli_query($link1,"select * from sfr_transaction where challan_no='".$_REQUEST['challan_no']."' ");
			while($row_loc=mysqli_fetch_array($res)){ 
			$sno=$sno+1;          
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td><?=getAnyDetails($row_loc["part_id"],"part_name","partcode","partcode_master",$link1)."-".getAnyDetails($row_loc["model_id"],"model","model_id","model_master",$link1)?></td>
              <td><?=$row_loc['hsn_code'];?></td>
              <td><?=$row_loc['qty'];?></td>
              <td><?=$row_loc['uom'];?></td>
              <td><?=$row_loc['price'];?></td>
              <td><?=$row_loc['value'];?></td>
              <td><?=$row_loc['discount_amt'];?></td>
              <td><?=$row_loc['basic_amt'];?></td>
              <td><?=$row_loc['cgst_per'];?></td>
              <td><?=$row_loc['cgst_amt'];?></td>
			  <td><?=$row_loc['sgst_per'];?></td>
              <td><?=$row_loc['sgst_amt'];?></td>
              <td><?=$row_loc['igst_per'];?></td>
              <td><?=$row_loc['igst_amt'];?></td>
            </tr>
            <?php }?>
			<tr>
                            <td colspan="6" class="alignright">Total</td>
                            <td><?=$row['basic_cost']?></td>
                            <td><?=$row['discount_amt']?></td>
                            <td><?=$row['basic_cost']?></td>
                            <td>&nbsp;</td>
                            <td><?=$row['cgst_amt']?></td>
                            <td>&nbsp;</td>
                            <td><?=$row['sgst_amt']?></td>
                            <td>&nbsp;</td>
                            <td><?=$row['igst_amt']?></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="alignleft">Total Invoice Value(In Figure)</td>
                            <td colspan="10" class="alignleft">Rs.
                            <?=$row['total_cost']?></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="alignleft">Total Invoice Value(In Words)</td>
                            <td colspan="10" class="alignleft"><?=$row['total_cost']?></td>
                        </tr>
                        <tr>
                            <td colspan="9" class="alignleft">Amount Of Tax Subject to Reverse Charges</td>
                            <td colspan="2">-</td>
                            <td colspan="2">-</td>
                            <td colspan="2">-</td>
                        </tr>
						 <tr>
                             <td colspan="6"><h5>Terms and Conditions-E.&amp;.O.E</h5><p>&nbsp;</p></td>
                            <td colspan="5"><h5>Customer Note</h4><p>&nbsp;</p></td>
                            <td colspan="5"><h5>For <b>
                              <?=$row[bill_from]?>
                           </b></h5><p>Signature</p></td>
                        </tr>
                         <tr>
                            <td>Declaration</td>
                           
                        </tr>
          </tbody>
          </table>
      </div>
      </form>
    </div>    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>