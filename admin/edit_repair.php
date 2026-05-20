<?php
require_once("../includes/config.php");
$arrstatus = getFullStatus("master",$link1);
$getid = base64_decode($_REQUEST['refid']);
////// get details of selected partcode////
$sel_usr="select * from repaircode_master where rep_code='".$getid."'";
$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
$sel_result=mysqli_fetch_assoc($sel_res12);
////// final submit form ////
$msg="";
@extract($_POST);
if($_POST){
	//// initialize transaction parameters

	if($_POST['Submit1']=='Save'){
	
		//// array initialization to send by query string of  product
		$prdstr = "";
		$arr_prd = $_REQUEST['product'];
		for($i=0; $i<count($arr_prd); $i++){
			if($prdstr){
				$prdstr.=",".$arr_prd[$i];
			}else{
				$prdstr.= $arr_prd[$i];
			}
		}

		// update all details of partcode //
	 	 $usr_upd = "UPDATE repaircode_master set mapped_product ='".$prdstr."', brand_id ='".$brand_name."', rep_desc='".$repair."' ,status='".$status."',part_replace='".$part_rep."',check_rep='".$rep_type."',	rep_for='".$rep_for."',rep_level='".$rep_level."' where rep_code = '".$getid."'";
		$res_upd = mysqli_query($link1,$usr_upd);
		//// check if query is not executed
		if (!$res_upd) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}
   		//////////////////////////////////////////////////////////////
   		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$getid,"REPAIR ACTION","UPDATE",$ip,$link1,$flag);
		////// return message
		$msg="You have successfully updated Repair Action details for ".$getid;
		$cflag="success";
		$cmsg="Success";
	}////close 1st tab
	else if($_POST['Submit2']=='Save'){//// update Repair Fault mapping


		$postmapdata=$_POST['mappartmodel'];
		
		$sym=implode(",",$postmapdata);
 
		$res_mapupd = mysqli_query($link1,"update repaircode_master set fault_code='".$sym."' where rep_code='".$getid."'");
		
		//// check if query is not executed
		if (!$res_mapupd) {
			 $flag = false;
			 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
		}
		//// close while loop
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$getid,"PARTCODE","UPDATE",$ip,$link1,$flag);
		////// return message
		$msg="You have successfully updated partcode details for ".$getid;
		$cflag="success";
		$cmsg="Success";
	}////close 2nd tab 
	else{
		////// return message
		$flag = false;
		$cflag = "info";
		$cmsg = "Warning";
		$msg = "Request could not be processed. Please try again.";	
	}
	///// check both master and data query are successfully executed

	//mysqli_close($link1);
///// move to parent page
header("location:repair_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
 <script>
	$(document).ready(function(){
        $("#frm1").validate();
    });
 function checkAll(field){
   for (i = 0; i < field.length; i++)
        field[i].checked = true ;
 }
 function uncheckAll(field){
   for (i = 0; i < field.length; i++)
        field[i].checked = false ;
 }
 </script>
 <script src="../js/frmvalidate.js"></script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-cog"></i> <?=$_REQUEST['op']?> Repair Action </h2><br/><br/>
         <div class="form-group"  id="page-bug" style="margin-left:10px;" >
      	 <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-gear"></i> General Details</a></li>
            <li><a data-toggle="tab" href="#menu1"><i class="fa fa-bug"></i> Fault Mapping</a></li>
          </ul>
    	  <div class="tab-content">
            <div id="home" class="tab-pane fade in active"><br/>
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
		  <?php /* ?>
            <div class="col-md-6"><label class="col-md-6 control-label">Product Name <span class="red_small">*</span></label>
                <div class="col-md-6">
               	<select name="product_name" id="product_name" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <?php
					$dept_query="SELECT * FROM product_master where status = '1' order by product_name";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['product_id']?>"<?php if($sel_result['product_id'] == $br_dept['product_id']){ echo "selected";}?>><?php echo $br_dept['product_name']?></option>
                <?php }?>
                </select>
              </div>
            </div>
			<?php */ ?>
            <div class="col-md-6"><label class="col-md-6 control-label">Brand <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name="brand_name" id="brand_name" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <?php
					$dept_query="SELECT * FROM brand_master where status = '1' order by brand";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['brand_id']?>"<?php if($sel_result['brand_id'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>
                <?php }?>
                </select>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-6 control-label">Status</label>
                <div class="col-md-6">
              <select name="status" id="status" class="form-control">
                    <?php foreach($arrstatus as $key => $value){?>
                    	<option value="<?=$key?>" <?php if($sel_result['status'] == $key) { echo 'selected'; }?>><?=$value?></option>
                    <?php } ?>
                 </select>
              </div>
            </div>
          </div>
       <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Repair Code </label>
                <div class="col-md-6">
               	<input type="text" name="repair_code" class=" form-control alphanumeric" id="repair_code" readonly value="<?=$sel_result['rep_code']?>" required/>
              </div>
            </div>
              <div class="col-md-6"><label class="col-md-6 control-label">Repair Action  </label>
              <div class="col-md-6">
                  <input type="text" name="repair" class=" form-control" id="repair" value="<?=$sel_result['rep_desc']?>" required/>
              </div>
            </div>
          </div>
         
         <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Part Replace Check <span class="red_small">*</span></label>
              <div class="col-md-6">
               	<select name="rep_type" id="rep_type" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <option value="Y" <?php if($sel_result['check_rep'] == "Y"){ echo "selected";}?>>Yes</option>
                  <option value="N" <?php if($sel_result['check_rep'] == "N"){ echo "selected";}?>>No</option>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Repair For <span class="red_small">*</span></label>
              <div class="col-md-6">
                <select name="rep_for" id="rep_for" class="form-control required" required>
                  <option value="0"<?php if($sel_result['part_for'] == "ALL"){ echo "selected";}?>>ALL</option>
                  <?php
					$dept_query="SELECT * FROM location_type_master order by usedname";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['rep_level']?>"<?php if($sel_result['rep_for'] == $br_dept['rep_level']){ echo "selected";}?>><?php echo $br_dept['usedname']?></option>
                <?php }?>
                </select>
              </div>
            </div>
          </div>
		  
		    <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Part Replace <span class="red_small">*</span></label>
              <div class="col-md-6">
               	 <input type="checkbox" name="part_rep" id="part_rep" value="Y" class="col-md-offset-4" <?php if($sel_result['part_replace']=="Y"){ echo "checked";} ?>>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Repair Level <span class="red_small">*</span></label>
              <div class="col-md-6">
              
				   <select name="rep_level" id="rep_level" class="form-control required">
                  <option value="">Please Select</option>
                <?php
					$dept_query="SELECT id,name FROM repair_level where status = '1'";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['id']?>"<?php if($sel_result['rep_level']== $br_dept['id']){ echo "selected";}?>><?php echo $br_dept['name']?></option>
                <?php }?>
                </select>
              </div>
            </div>
          </div>
        		  
		  <div class="form-group">
            <div class="col-md-12"><label class="col-md-3 control-label">Map Product</label>
              <div class="col-md-9">
			  
			  <table width="100%" id="productmap" class="table table-bordered table-hover">
                  <tbody>
                    <?php
					///// check if any mapping entry with Y status is there
					$res_map = mysqli_query($link1,"select mapped_product from repaircode_master where rep_code='".$getid."'")or die(mysqli_error());
					$num_map = mysqli_fetch_assoc($res_map);
					$res_entity = explode(",",$num_map['mapped_product']);
					
					/////make mapped entity array
					$array_entity = array();
					for($i=0; $i<count($res_entity); $i++){
						$array_entity[$res_entity[$i]] = $res_entity[$i];
					}
						
					$rs=mysqli_query($link1,"select product_id,product_name from product_master where status='1' order by product_name");
					$num=mysqli_num_rows($rs);
					if($num > 0){
                   		$j=1;
                   		while($row=mysqli_fetch_array($rs)){
							if($j%2==1){
					?>
                    <tr>
                      <?php
                       		}
					  ?>
                      <td><input style="width:20px"  type="checkbox" id="product" name="product[]" value="<?=$row['product_id']?>" <?php if($array_entity[$row['product_id']] == $row['product_id']){ echo "checked";}?>/>
                        &nbsp;  
                        <?=$row['product_name']?></td>
                      <?php 
						  	if($j/2==0){
							?>
                    </tr>
                    <?php
						  }
						$j++;
						}
					}
					?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
           
           
          <div class="form-group">
            <div class="col-md-12" align="center">
             
                <input type="submit" class="btn<?=$btncolor?>" name="Submit1" id="save1" value="Save" title="" <?php if($_POST['Submit1']=='Save'){?>disabled<?php }?>>
          
              <input type="hidden" name="refid"  id="refid" value="<?=base64_encode($sel_result['rep_code'])?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='repair_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
            </div>
          </div>
            </form>
            </div>
            <div id="menu1" class="tab-pane fade">
              <form  name="frm2" id="frm2" class="form-horizontal" action="" method="post">
                <div class="form-buttons" style="float:right">
                <input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm2.mappartmodel)" value="Check All" />
                <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm2.mappartmodel)" value="Uncheck All" />
                </div>
				<table width="100%" id="modelmap" class="table table-bordered table-hover">
                	<tbody>
                    <?php
					$map_prd = explode(",",$sel_result['mapped_product']);
					$prd_flag = "";
					for($i=0; $i < count($map_prd); $i++){
						$prd_flag .= $map_prd[$i]."','";
					}
					$prd = substr($prd_flag, 0, -3);
					 /*product_id in ('$prd')*/
					$rs=mysqli_query($link1,"select defect_code,defect_desc from defect_master where status = '1' order by defect_desc");
					$num=mysqli_num_rows($rs);
					if($num > 0){
                   		$j=1;
                   		while($row=mysqli_fetch_array($rs)){
							if($j%4==1){
					?>
                    	<tr>
                           <?php
                       		}
							///// check if any mapping entry with Y status is there
							$res_map = mysqli_query($link1,"select id from repaircode_master where fault_code like '%".$row['defect_code']."%' and rep_code='".$getid."'" )or die(mysqli_error());
                    		$num_map = mysqli_num_rows($res_map);
							?>
                          <td><input style="width:20px"  type="checkbox" id="mappartmodel" name="mappartmodel[]" value="<?=$row['defect_code']?>" <?php if($num_map > 0){ echo "checked";}?>/>&nbsp;<?=$row['defect_desc']." (".$row['defect_code'].")";?></td>
                           <?php 
						  	if($j/4==0){
							?>
                        </tr>
                    <?php
						  }
						$j++;
						}
					}
					?>    
                    </tbody>
                    <tfoot>
                    	<tr>
                          <td colspan="4" align="center">
                          <input type="submit" class="btn<?=$btncolor?>" name="Submit2" id="save2" value="Save" title="" <?php if($_POST['Submit2']=='Save'){?>disabled<?php }?>>
                          <input name="refid" id="refid" type="hidden" value="<?=base64_encode($sel_result['rep_code'])?>"/>
                          <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='repair_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'"></td>
                        </tr>
                    </tfoot>
                </table>
              </form>
            </div>

          </div>
      </div><!--End form group-->
    </div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>