<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
//// po details
$job_sql="SELECT po_no,po_date,status,to_state FROM po_master where po_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
$locinfo= mysqli_fetch_array(mysqli_query($link1,"select locationname,locationaddress from location_master where location_code='".$job_row['to_code']."' "));
////// final submit form ////
@extract($_POST);
if($_POST){
  if($_POST['Submit']=='update'){
	  mysqli_autocommit($link1, false);
	  $flag = true;	  
	  
	  $po_srch="select id from po_items where  po_no='".$docid."'";
$result_po=mysqli_query($link1,$po_srch);
while($arr_po=mysqli_fetch_array($result_po)){	
	  ///// cancel po in po_master ///////////
$part="partcode".$arr_po['id'];

$up_qty="qty".$arr_po['id'];

	  echo  $query2="UPDATE po_items set partcode='".$_POST[$part]."',qty='".$_POST[$up_qty]."',cancel_by='".$_SESSION[userid]."',cancel_date='".$today."',cancel_rmk='Partcode edit' where id='".$arr_po['id']."'";
	
	  $result1 = mysqli_query($link1,$query2)or die ("ER1".mysqli_error());
	  
	  //// check if query is not executed
	  if (!$result1) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
      }
	  }
  }/// close if 

 
	 ///// check  master  query are successfully executed
	 if ($flag) {
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "Record has been updated PO no." .$docid ;
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	
  
  ///// move to parent page
 header("Location:pending_po_cancel.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  exit;
}

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
 <script type="text/javascript" >
 </script>
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
 <div class="row content">
	<?php 
   include("../includes/leftnav2.php");
    ?>
   <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-shopping-basket"></i> Cancel PO</h2>
      <h4 align="center">PO No.- <?=$docid?></h4>
	  <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">  
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;PO Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Location Name</label></td>
                <td width="30%"><?php echo $locinfo['locationname'];?></td>
                <td width="20%"><label class="control-label">Address</label></td>
                <td width="30%"><?php echo $locinfo['locationaddress'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">PO No.</label></td>
                <td><?php echo $job_row['po_no'];?></td>
                <td><label class="control-label">PO  Date.</label></td>
                <td><?php echo $job_row['po_date'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Status</label></td>
                <td><?php echo getdispatchstatus($job_row['status']);?></td>
                <td><label class="control-label">State</label></td>
                <td><?php echo getAnyDetails($job_row["to_state"],"state","stateid","state_master",$link1);?></td>
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
                <th width="5%" style="text-align:center">#</th>
                <th width="20%" style="text-align:center">Product</th>
                <th width="20%" style="text-align:center">Brand</th>
                <th width="15%" style="text-align:center">Model</th>
                <th width="15%" style="text-align:center">Partcode</th>
                <th width="7%" style="text-align:center">Qty</th>
				<th width="12%" style="text-align:center">Status</th>
				<th width="20%" style="text-align:center">Processed Qty</th>
			
                </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			 $podata_sql="SELECT id,product_id ,brand_id ,model_id,partcode ,status ,processed_qty,qty FROM po_items where po_no='".$job_row['po_no']."'  ";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$proddet=explode("~",getAnyDetails($podata_row[product_id],"product_name","product_id","product_master",$link1));
				$brand=explode("~",getAnyDetails($podata_row[brand_id],"brand","brand_id","brand_master",$link1));
				$model=explode("~",getAnyDetails($podata_row[model_id],"model","model_id","model_master",$link1));
				$part=explode("~",getAnyDetails($podata_row[partcode],"part_name","partcode","partcode_master",$link1));
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet[0]?></td>
                <td><?=$brand[0]?></td>
                <td><?=$model[0]?></td>
                <td>  <select name="partcode<?=$podata_row['id']?>" id="partcode<?=$podata_row['id']?>" class="form-control required" required>
                         
                          <?php



				$cus_query="SELECT * FROM alt_part_map where status = '1' and partcode='".$podata_row['partcode']."' order by partcode";



				$check_cust=mysqli_query($link1,$cus_query);

?>
                   <option value="<?=$podata_row['partcode']?>"><?=$part[0]?></option>
			<?php	while($br_cust = mysqli_fetch_array($check_cust)){

$part_alter=explode("~",getAnyDetails($br_cust['alter_partcode'],"part_name","partcode","partcode_master",$link1));

				?>
                          <option value="<?=$br_cust['alter_partcode']?>"><?php echo $part_alter['0']?></option>
                          <?php  }?>
                        </select></td>
                <td><input name="qty<?=$podata_row['id']?>" id="qty<?=$podata_row['id']?>" type="text" class="form-control " value="<?=$podata_row['qty']?> " /> </td>    
				<td><?=getdispatchstatus($podata_row['status']);?></td> 
				<td><?=$podata_row['processed_qty']?></td> 
              
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
       <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Cancel Reason</div>
      <div class="panel-body">
       
        <table class="table table-bordered" width="100%">
            <tbody>
              
    
              <tr>
                <td colspan="2" align="center">
                  <input type="submit" class="btn btn-primary" name="Submit" id="save" value="update" title="" <?php if($_POST['Submit']=='update'){?>disabled<?php }?>>&nbsp;
                  <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='pending_po_cancel.php?<?=$pagenav?>'">
                  </td>
                </tr>
            </tbody>
          </table>
         
      </div><!--close panel body-->
    </div><!--close panel-->
  </div><!--close panel group-->
	</form>
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>