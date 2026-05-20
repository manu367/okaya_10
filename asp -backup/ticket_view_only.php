<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
//// ticket  details
$ticket_sql="SELECT * FROM ticket_master where ticket_no='".$docid."'";
$ticket_res=mysqli_query($link1,$ticket_sql);
$ticket_row=mysqli_fetch_assoc($ticket_res);
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
   <div class="col-sm-12">
      <h2 align="center"><i class="fa fa-ticket"></i>Ticket Details</h2>
      <h4 align="center">Ticket No.- <?=$docid?></h4>
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Customer Name</label></td>
                <td width="30%"><?php echo $ticket_row['customer_name'];?></td>
                <td width="20%"><label class="control-label">Address</label></td>
                <td width="30%"><?php echo $ticket_row['address'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Contact No. <span class="small">(For SMS Update)</span></label></td>
                <td><?php echo $ticket_row['contact_no'];?></td>
                <td><label class="control-label">Alternate Contact No.</label></td>
                <td><?php echo $ticket_row['alternate_no'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">State</label></td>
                <td><?php echo getAnyDetails($ticket_row["state_id"],"state","stateid","state_master",$link1);?></td>
                <td><label class="control-label">Email</label></td>
                <td><?php echo $ticket_row['email'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">City</label></td>
                <td><?php echo getAnyDetails($ticket_row["city_id"],"city","cityid","city_master",$link1);?></td>
                <td><label class="control-label">Pincode</label></td>
                <td><?php echo $ticket_row['pincode'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Customer Type</label></td>
                <td><?php echo $ticket_row['customer_type'];?></td>
                <td><label class="control-label">Ticket Type</label></td>
                <td><?php echo $ticket_row['ticket_type']; ?></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Product Details</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
            <tr>
                <td width="20%"><label class="control-label">Product</label></td>
                <td width="30%"><?php echo getAnyDetails($ticket_row["product_id"],"product_name","product_id","product_master",$link1);?></td>
                <td width="20%"><label class="control-label">Brand</label></td>
                <td width="30%"><?php echo getAnyDetails($ticket_row["brand_id"],"brand","brand_id","brand_master",$link1);?></td>
              </tr>
            <tr>
              <td><label class="control-label">Model</label></td>
              <td><?=$ticket_row['model']?></td>
              <td><label class="control-label">Open Date</label></td>
              <td><?=$ticket_row['open_date']?></td>
            </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Observation</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
            <tr>
              <td><label class="control-label">VOC</label></td>
              <td><?=$ticket_row['cust_problem']?></td>
              <td><label class="control-label">Remark </label></td>
              <td colspan="3"><?=$ticket_row['remark']?></td>
            </tr>      
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;&nbsp;History</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
                  <tr>
                    <td width="10%"><strong>Priority</strong></td>
                    <td width="25%"  align="center"><strong>Remark</strong></td>
                    <td width="15%"><strong>Update on</strong></td>
                  </tr>
                </thead>
                <tbody>
                <?php
				$res_tickhistory = mysqli_query($link1,"SELECT * FROM ticket_history where ticket_no='".$docid."'");
				while($row_tickhistory = mysqli_fetch_assoc($res_tickhistory)){
				?>
                  <tr>             
                    <td><?php if ($row_tickhistory['priority'] == '1'){echo "Low" ;} elseif ($row_tickhistory['priority'] == '2') {echo "Normal";} else {echo "High";}?></td>
                    <td align="center"><?=$row_tickhistory['remark']?></td>
                    <td><?=$row_tickhistory['update_date']?></td>
                  </tr>
                  <?php
				}
				  ?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->

  </div><!--close panel group-->
 </div><!--close col-sm-9-->
</div><!--close container-fluid-->
<?php
include("../includes/connection_close.php");
?>
</body>
</html>