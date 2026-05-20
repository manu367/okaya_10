<?php
require_once("../includes/config.php");
$brandarray = getBrandArray($link1);
$productarray = getProductArray($link1);
$docid=base64_decode($_REQUEST['refno']);
//// stock audit details
$sa_sql = "SELECT * FROM stock_audit_master WHERE ref_no = '".$docid."'";
$sa_res = mysqli_query($link1,$sa_sql)or die (mysqli_error($link1));
$sa_row = mysqli_fetch_assoc($sa_res);
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
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
   <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-check-circle-o"></i>Stock Audit Details</h2>
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Audit Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Location Name</label></td>
                <td width="30%"><?php echo getAnyDetails($sa_row['location_code'],"locationname","location_code","location_master",$link1);?></td>
                <td width="20%"><label class="control-label">Ref. No.</label></td>
                <td width="30%"><?php echo $sa_row['ref_no'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Stock Taken Date</label></td>
                <td><?php echo $sa_row['audit_date'];?></td>
                <td><label class="control-label">Entry Date</label></td>
                <td><?php echo $sa_row["entry_date"]." ".$sa_row["entry_time"];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Entry By</label></td>
                <td><?php echo $entby = getAnyDetails($sa_row["entry_by"],"name","username","admin_users",$link1); if($entby==""){ echo $entby = getAnyDetails($sa_row["entry_by"],"locationname","location_code","location_master",$link1);};?></td>
                <td><label class="control-label">Entry IP</label></td>
                <td><?php echo $sa_row["entry_ip"];?></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Product Details</div>
      <div class="panel-body">
        <table  width="100%" id="myTable" class="table table-bordered" align="center">
            <thead>
                <tr class="<?=$tableheadcolor?>">     
                    <th width="5%" rowspan="2">S.No.</th>
                    <th width="10%" rowspan="2">Partcode</th>
                    <th width="25%" rowspan="2">Part Description</th>
                    <th width="10%" rowspan="2">Product</th>
                    <th width="10%" rowspan="2">Brand</th>         
                    <th colspan="2"><div align="center">CRM</div></th>
                    <th colspan="2"><div align="center">Physical</div></th>
                </tr>
                <tr class="<?=$tableheadcolor?>">
                  <th width="10%"><div align="center">OK</div></th>
                  <th width="10%"><div align="center">Faulty</div></th>
                  <th width="10%"><div align="center">OK</div></th>
                  <th width="10%"><div align="center">Faulty</div></th>
              </tr>
            </thead>
            <tbody>
                <?php 
                //////// get stock table
                $i = 1;
                $res_invt = mysqli_query($link1,"SELECT * FROM stock_audit WHERE ref_no='".$docid."'")or die (mysqli_error($link1));
                while($row_invt = mysqli_fetch_assoc($res_invt)){
                ?>
                <tr>
                    <td><?=$i;?></td>
                    <td><?=$row_invt["partcode"];?></td>
                    <td><?=getAnyDetails($row_invt["partcode"],"part_name","partcode","partcode_master",$link1);?></td>
                    <td><?=$productarray[$row_invt["product_id"]];?></td>
                    <td><?=$brandarray[$row_invt["brand_id"]];?></td>
                    <td align="right"><?=$row_invt["crm_okqty"];?></td>
                    <td align="right"><?=$row_invt["crm_faultyqty"];?></td>
                    <td align="right"><?=$row_invt["audit_okqty"];?></td>
                    <td align="right"><?=$row_invt["audit_faultyqty"];?></td>
                </tr>
                <?php
					$tot_crmok += $row_invt["crm_okqty"];
					$tot_crmflt += $row_invt["crm_faultyqty"];
					$tot_phyok += $row_invt["audit_okqty"];
					$tot_phyflt += $row_invt["audit_faultyqty"];
					$i++;
                }
                ?>
                <tr>
                    <td colspan="5" align="right"><strong>Total Qty</strong></td>
                    <td align="right"><strong><?php echo  $tot_crmok;?></strong></td>
                    <td align="right"><strong><?php echo  $tot_crmflt;?></strong></td>
                    <td align="right"><strong><?php echo  $tot_phyok;?></strong></td>
                    <td align="right"><strong><?php echo  $tot_phyflt;?></strong></td>
                </tr> 
            </tbody>
        </table>
        <div class="form-group">
            <div class="col-md-12" align="center">
                 <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='stock_audit_master_loc.php?<?=$pagenav?>&locationName=<?=base64_decode($_REQUEST['locationcode'])?>&auditdate=<?=base64_encode($_REQUEST['auditdate'])?>'">
            </div>
        </div> 
      </div>
      <!--close panel body-->
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