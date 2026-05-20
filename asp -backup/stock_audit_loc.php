<?php
require_once("../includes/config.php");
$brandarray = getBrandArray($link1);
$productarray = getProductArray($link1);
//$yesterd = date('Y-m-d',strtotime("-1 days"));
////// posted data
@extract($_POST);
if($_POST["upd"]){
	///// make a string token
	$messageIdent = md5($selloc . $auditdate);
	//and check it against the stored value:
	$sessionMessageIdent = isset($_SESSION['auditsaveclick'])?$_SESSION['auditsaveclick']:'';
	if($messageIdent!=$sessionMessageIdent){//if its different:          
		//save the session var:
		$_SESSION['auditsaveclick'] = $messageIdent;
		//// whene receive button pressed /////////////////
		if($_POST['upd']=="Save"){
			////// INITIALIZE PARAMETER/////////////////////////
			mysqli_autocommit($link1, false);
			$flag = true;
			$err_msg = "";
			/////// decode posted master value
			$dcdloccode = base64_decode($selloc);
			$dcdauditdate = base64_decode($selauditdate);
			$dcdmonthyear = explode("~",base64_decode($selmonthyear));
           ///$tablename = "`client_inventory".str_replace("-","-",$dcdauditdate)."`";
            $tablename = "client_inventory";
			foreach($auditdone as $pk){
				///// decode primary key
				$dcdpk = base64_decode($pk);
				$res_invtry = mysqli_query($link1,"SELECT id, partcode, okqty, faulty, product_id, brand_id FROM ".$tablename." WHERE id ='".$dcdpk."'")or die (mysqli_error($link1));
				$row_invtry = mysqli_fetch_assoc($res_invtry);
				///// check if audit is done for selected part on audit date with respective location
				if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM stock_audit WHERE audit_date='".$dcdauditdate."' AND location_code='".$dcdloccode."' AND partcode='".$row_invtry["partcode"]."'"))==0){
					/////insert in audit table
					$res_inst1 = mysqli_query($link1,"INSERT INTO stock_audit SET ref_no = '', location_code = '".$dcdloccode."', audit_date = '".$dcdauditdate."', partcode = '".$row_invtry["partcode"]."', product_id = '".$row_invtry["product_id"]."', brand_id = '".$row_invtry["brand_id"]."', crm_okqty = '".$row_invtry["okqty"]."', crm_faultyqty = '".$row_invtry["faulty"]."', audit_okqty = '".$_POST["phyok".$pk]."', audit_faultyqty = '".$_POST["phyfaulty".$pk]."'");
					//// check if query is not executed
					if (!$res_inst1) {
						$flag = false;
						$err_msg = "Error Code1: ".mysqli_error($link1);
					}
				}
			}
			///// check if all stock audit is done or not
			$audit_count = mysqli_num_rows(mysqli_query($link1,"SELECT id FROM stock_audit WHERE location_code='".$dcdloccode."' AND audit_date='".$dcdauditdate."'"));
			$invty_count = mysqli_num_rows(mysqli_query($link1,"SELECT id FROM ".$tablename." WHERE location_code='".$dcdloccode."'"));
			if($audit_count == $invty_count){
				////// make a seq no. for audit document
				$res_sam = mysqli_query($link1,"SELECT MAX(temp_no) AS seqno FROM stock_audit_master WHERE location_code ='".$dcdloccode."'");
				$row_sam = mysqli_fetch_assoc($res_sam);
				$next_no = $row_sam["seqno"]+1;
				$makerefno = "AUD/".$dcdloccode."/".str_replace("-","",$dcdauditdate)."/".str_pad($next_no,3,0,STR_PAD_LEFT);
				//////// entry in master
				$res_inst2 = mysqli_query($link1,"INSERT INTO stock_audit_master SET location_code = '".$dcdloccode."', ref_no = '".$makerefno."', temp_no = '".$next_no."', audit_date = '".$dcdauditdate."', entry_date = '".$today."', entry_time = '".$currtime."', entry_by = '".$_SESSION["userid"]."', entry_ip = '".$_SERVER['REMOTE_ADDR']."'");
				//// check if query is not executed
				if (!$res_inst2) {
					$flag = false;
					$err_msg = "Error Code2: ".mysqli_error($link1);
				}
				///// update ref no. in audit stock data table
				$res_upd1 = mysqli_query($link1,"UPDATE stock_audit SET ref_no = '".$makerefno."' WHERE location_code = '".$dcdloccode."' AND audit_date = '".$dcdauditdate."'");
				//// check if query is not executed
				if (!$res_upd1) {
					$flag = false;
					$err_msg = "Error Code3: ".mysqli_error($link1);
				}
				$redirect_flag = "Y";
			}else{
				$redirect_flag = "N";
			}
			///// check both master and data query are successfully executed
			if ($flag) {
				mysqli_commit($link1);
				if($redirect_flag=="Y"){
					$msg = "Audit details is successfully saved with ref. no. ".$makerefno;
				}else{
					$msg = "Audit details is successfully saved.";
				}
				$cflag="success";
				$cmsg = "Success";
			} else {
				mysqli_rollback($link1);
				$msg = "Request could not be processed ".$err_msg.". Please try again.";
				$cflag="danger";
				$cmsg = "Failed";
			} 
			mysqli_close($link1);
		}
	}else {
		//// you've sent this already!
		$msg = "Re-submission is not allowed";
		$cflag="warning";
		$cmsg = "Warning";
	}
	if($redirect_flag=="Y"){
		///// move to parent page
		header("location:stock_audit_master_loc.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit;	
	}else{
		///// move to parent page
		header("location:stock_audit_loc.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."&selyear=".$dcdmonthyear[0]."&selmonth=".$dcdmonthyear[1]."&locationName=".$dcdloccode."&Submit=GO".$pagenav);
		exit;	
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="../js/jquery-1.10.2.js"></script>
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/abc.css" rel="stylesheet">
    <script src="../js/bootstrap.min.js"></script>
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-select.min.css">
    <script src="../js/bootstrap-select.min.js"></script>
    
    <script type="text/javascript">
	$(document).ready(function(){
		$('#myTable').dataTable();
    });
	$(document).ready(function() {
		$("#frm2").validate();
	});
    </script>
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    <title><?= siteTitle ?></title>
</head>
<body>
	<div class="container-fluid">
    	<div class="row content">
            <?php
            include("../includes/leftnavemp2.php");
            ?>
            <div class="col-sm-9 col-md-9 col-lg-9 tab-pane fade in active" id="home">
                <h2 align="center"><i class="fa fa-check-circle-o"></i> Stock Audit</h2>
                <?php if(isset($_REQUEST['msg'])){?>
                    <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
                    </div>
                  <?php $_SESSION['auditsaveclick'] = ""; }?>
                <form class="form-horizontal" role="form" name="form1" action="" method="post">
                	<div class="form-group">
                    	<div class="col-md-5"><label class="col-md-4 control-label">Self Audit Date</label>	  
                            <div class="col-md-4" align="left">
                                <select name="selyear" id="selyear" class="form-control" onChange="document.form1.submit();">
                                	<option value="" selected>--Select Year--</option>
									<?php 
                                    for($i=0; $i<3; $i++){ 
                                        $year = date('Y', strtotime(date("Y"). ' - '.$i.' year'));
                                    ?>
                                    <option value="<?=$year?>"<?php if($_REQUEST["selyear"]==$year){ echo "selected";}?>><?=$year?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-4" align="left">
                            	<?php if($_REQUEST["selyear"]){ ?>
                            	<select name="selmonth" id="selmonth" class="form-control">
									<?php 
                                    ///// check if current year is selected then month should be come till current month
                                    if($_REQUEST["selyear"]==date("Y")){ $nmonth = date("m", strtotime(date("F")."-".$_REQUEST["selyear"]));}else{ $nmonth = 12;}
                                    for($j=0; $j<$nmonth; $j++){ 
                                        if($_REQUEST["selyear"]==date("Y")){ if($j==0){continue;}else{$month = date ( 'F' , strtotime ( "-".$j." month"	 , strtotime ( date("F") ) ));}}else{$month = date('F', strtotime(date("F"). ' + '.$j.' month'));}
                                    ?>
                                    <option value="<?=$month?>"<?php if($_REQUEST["selmonth"]==$month){ echo "selected";}?>><?=$month?></option>
                                    <?php } ?>
                                </select>
                                <?php }?>
                            </div>
                        </div>
						<div class="col-md-5"><label class="col-md-4 control-label">Location Name</label>	  
                            <div class="col-md-8" align="left">
                                <select name="locationName" id="locationName"  class="form-control selectpicker required" data-live-search="true">
                                    <!--<option value="" selected="selected">Please Select </option>-->
                                    <?php 
                                    $sql_chl="SELECT location_code,locationname,locationtype,cityid,stateid FROM location_master WHERE statusid='1' AND location_code='".$_SESSION["asc_code"]."' ORDER BY locationname";
                                    $res_chl=mysqli_query($link1,$sql_chl);
                                    while($result_chl=mysqli_fetch_array($res_chl)){
										////// get state name
										$statename= mysqli_fetch_assoc(mysqli_query($link1,"SELECT state FROM state_master WHERE stateid='".$result_chl['state']."'"));
                                    ?>
                                    <option data-tokens="<?=$result_chl['location_code']." | ".$result_chl['locationname']?>" value="<?=$result_chl["location_code"]?>" <?php if($result_chl['location_code']==$_REQUEST['locationName'])echo "selected";?>><?=$result_chl['locationname']." | ".$result_chl['locationtype']." | ".$statename['state']." | ".$result_chl['location_code']?>
                                    </option>
                                    <?php
                                    }
                                    ?>
                                 </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                        	<input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!"> 
                            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                        </div>
                    </div>
                </form>
                <?php if($_REQUEST['Submit']!="" && $_REQUEST["selyear"]!="" && $_REQUEST["selmonth"]!=""){ ?>
                <form class="table-responsive" id="frm2" name="frm2" role="form" action="" method="post">
                	<table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
                    	<thead>
                        	<tr class="<?=$tableheadcolor?>">     
								<th width="10%" rowspan="2">Partcode</th>
                                <th width="20%" rowspan="2">Part Description</th>
                                <th width="10%" rowspan="2">Product</th>
                                <th width="10%" rowspan="2">Brand</th>         
                              	<th colspan="2"><div align="center">CRM</div></th>
							  	<th colspan="2"><div align="center">Physical</div></th>
                                <th width="10%" rowspan="2"><div align="center">Select</div></th>
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
							$k = 0;
							$lastdateofmonth = date('Y-m-d', strtotime("last day of ".$_REQUEST["selmonth"]." ".$_REQUEST["selyear"]));
							/// $tablename = "`client_inventory".str_replace("-","-",$lastdateofmonth)."`";
						$tablename = "client_inventory";
							///echo "SELECT id, partcode, okqty, faulty, product_id, brand_id, part_name FROM ".$tablename." WHERE location_code='".$_REQUEST["locationName"]."'";
                                $res_invt = mysqli_query($link1,"SELECT id, partcode, okqty, faulty, product_id, brand_id, part_name FROM ".$tablename." WHERE location_code='".$_REQUEST["locationName"]."'");
							while($row_invt = mysqli_fetch_assoc($res_invt)){
								///// check if audit is done already
                                ///echo "SELECT audit_okqty,audit_faultyqty FROM stock_audit WHERE audit_date='".$lastdateofmonth."' AND location_code='".$_REQUEST["locationName"]."' AND partcode='".$row_invt["partcode"]."'";
								$res_checkaud = mysqli_query($link1,"SELECT audit_okqty,audit_faultyqty FROM stock_audit WHERE audit_date='".$lastdateofmonth."' AND location_code='".$_REQUEST["locationName"]."' AND partcode='".$row_invt["partcode"]."'");
								$row_checkaud = mysqli_fetch_assoc($res_checkaud);
								$checkpartaudit = mysqli_num_rows($res_checkaud);
							?>
                            <tr>
                            	<td><?=$row_invt["partcode"];?></td>
                                <td><?=$row_invt["part_name"];?></td>
                                <td><?=$productarray[$row_invt["product_id"]];?></td>
                                <td><?=$brandarray[$row_invt["brand_id"]];?></td>
                                <td align="right"><?=$row_invt["okqty"];?></td>
                                <td align="right"><?=$row_invt["faulty"];?></td>
                                <td><input name="phyok<?=base64_encode($row_invt["id"]);?>" id="phyok<?=$i?>" type="text" class="form-control alert-success" style="text-align:right;" value="<?php if($checkpartaudit>0){ echo $row_checkaud["audit_okqty"];}else{ echo $row_invt["okqty"];}?>"></td>
                                <td><input name="phyfaulty<?=base64_encode($row_invt["id"]);?>" id="phyfaulty<?=$i?>" type="text" class="form-control alert-danger" style="text-align:right;" value="<?php if($checkpartaudit>0){ echo $row_checkaud["audit_faultyqty"];}else{ echo $row_invt["faulty"];}?>"></td>
                                <td align="center"> <?php if($checkpartaudit>0){ echo "Audited";}else{ $k++;?><input name="auditdone[]" id="auditdone<?=$i?>" type="checkbox" value="<?=base64_encode($row_invt["id"]);?>" <?php if($checkpartaudit>0){echo "disabled";}?>><?php }?></td>
                            </tr>
                            <?php
							$i++;
							}
							?>
                        </tbody>
                    </table>
<?php if($k>0){?>
                    <div class="form-group" align="center">
                        <div class="col-md-12">
                            <input type="submit" class="btn <?=$btncolor?>" name="upd" id="upd" value="Save" title="Save">
                            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               				<input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                            <input name="selloc" id="selloc" type="hidden" value="<?=base64_encode($_REQUEST['locationName'])?>"/> 
                            <input name="selauditdate" id="selauditdate" type="hidden" value="<?=base64_encode($lastdateofmonth)?>"/> 
                            <input name="selmonthyear" id="selmonthyear" type="hidden" value="<?=base64_encode($_REQUEST["selyear"]."~".$_REQUEST["selmonth"])?>"/> 
                        </div>
          			</div>
                    <?php }?>
                </form>
                <?php }?>
                <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='stock_audit_master_loc.php?<?=$pagenav?>'">
            </div>
        </div>
    </div>
    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
</body>
</html>