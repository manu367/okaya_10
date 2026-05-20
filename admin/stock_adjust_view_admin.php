<?php

require_once("../includes/config.php");

$docid=base64_decode($_REQUEST['refid']);

function getNotation($str){
	if($str == "P"){
	   return " (PLUS)";
	}elseif($str == "M"){
		return " (MINUS)";
	}else{
		return "";
	}
}
$gr_master=mysqli_fetch_array(mysqli_query($link1,"select * from stock_adjust_master where system_ref_no='".$docid."' "));
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
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
 <div class="row content">
	<?php
    include("../includes/leftnav2.php");
    ?>
  <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-adjust"></i> Stock Adjustment View</h2>
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Party Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
        <td width="15%"><strong>Location</strong></td>
        <td width="35%"><?php echo getAnyDetails($gr_master['location_code'],"locationname","location_code" ,"location_master",$link1);?></td>
        <td width="19%"><strong>System Ref. No.</strong></td>
        <td width="31%"><?=$gr_master['system_ref_no'];?></td>
      </tr>
      <tr>
        <td><strong>Entry By</strong></td>
        <td><?php echo $gr_master['entry_by'];?></td>
        <td><strong>Entry Date</strong></td>
        <td><?=dt_format($gr_master['entry_date']);?></td>
      </tr>
      <tr >
        <td><strong>Remark</strong></td>
        <td><?=$gr_master['entry_rmk'];?>&nbsp;</td>
        <td><strong>Status</strong></td>
        <td><?=$gr_master['status'];?></td>
      </tr>
     </tbody>
       </table>
      </div><!--close panel body-->
    </div><!--close panel-->
  <div class="panel panel-info table-responsive">
     <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Stock Adjust Details</div>
         <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr class="<?=$tableheadcolor?>">
    <td width="3%" height="25" rowspan="2"><div align="center"><strong>SNo.</strong></div></td>
    <td width="20%" rowspan="2"><div align="center"><strong>Item Description</strong></div></td>
    <td width="20%" colspan="2" height="25"><div align="center"><strong> OK QTY</strong></div></td>
    <td colspan="2"><div align="center"><strong>Damage QTY</strong></div></td>
    <td colspan="2"><div align="center"><strong>Missing QTY</strong></div></td>
    </tr>
  <tr class="<?=$tableheadcolor?>">
    <td width="15%" height="25"><div align="center"><strong>Adjust Type</strong></div></td>
    <td width="15%"><div align="center"><strong>Adjust QTY</strong></div></td>
    <td width="15%"><div align="center"><strong>Adjust Type</strong></div></td>
    <td width="15%"><div align="center"><strong>Adjust QTY</strong></div></td>
    <td width="15%"><div align="center"><strong>Adjust Type</strong></div></td>
    <td width="15%"><div align="center"><strong>Adjust QTY</strong></div></td>
    </tr>
  <?php
		$rs=mysqli_query($link1,"select * from stock_adjust_data where system_ref_no='".$docid."' ") ;
		$i=1;
		while($row=mysqli_fetch_array($rs)){
		$old_prod=explode("~",getAnyDetails($row['partcode'],"part_name,partcode,customer_partcode","partcode","partcode_master",$link1));
		?>
  <tr>
    <td height="25">&nbsp;<?=$i?></td>
    <td><?php echo $old_prod[0]."/".$old_prod[1];?></td>
    <td>&nbsp;&nbsp;<?php if ($row['adj_ok_qty']>=1){echo $row['adj_ok_type']."".getNotation($row['adj_ok_type']);}?></td>
    <td align="right"><?=$row['adj_ok_qty']?>&nbsp;&nbsp;&nbsp;</td>
    <td>&nbsp;&nbsp;<?php  if ($row['adj_damg_qty']>=1){ echo $row['adj_damg_type']."".getNotation($row['adj_damg_type']);}?></td>
    <td align="right"><?=$row['adj_damg_qty']?>&nbsp;&nbsp;&nbsp;</td>
    <td>&nbsp;&nbsp;<?php  if ($row['adj_miss_qty']>=1){ echo $row['adj_miss_type']."".getNotation($row['adj_miss_type']);}?></td>
    <td align="right"><?=$row['adj_miss_qty']?>&nbsp;&nbsp;&nbsp;</td>
    </tr>
		<?php
		$i++;
        $total_ok_qty+=$row['adj_ok_qty'];
		$total_damg_qty+=$row['adj_damg_qty'];
		$total_miss_qty+=$row['adj_miss_qty'];
		$total_ok_boxqty+=$row['box_okqty'];
		$total_damg_boxqty+=$row['box_damgqty'];
		$total_miss_boxqty+=$row['box_missqty'];
		}
		?>
      <tr >
        <td height="18">&nbsp;</td>
        <td align="right" ><strong>Grand Total</strong></td>
        <td align="right">&nbsp;</td>
        <td align="right"><strong><?=number_format($total_ok_qty);?></strong>&nbsp;&nbsp;&nbsp;</td>
        <td align="right"><strong></strong>&nbsp;&nbsp;&nbsp;</td>
        <td align="right"><strong><?=number_format($total_damg_qty);?></strong></td>
        <td align="right">&nbsp;&nbsp;&nbsp;</td>
        <td align="right"><strong><?=number_format($total_miss_qty);?></strong>&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <tr>
                <td width="100%" align="center" colspan="8">
                   <input title="Back" type="button" class="btn btn<?=$btncolor?>" value="Back"
                          onClick="window.location.href='adminstock_adjustment_admin.php?name=sss<?=$pagenav?>'">
                 </td>
                </tr>
</table>

 </div><!--close panel body-->

    </div><!--close panel-->

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