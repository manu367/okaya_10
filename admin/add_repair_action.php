<?php
require_once("../includes/config.php");
/////get status//
$arrstatus = getFullStatus("master",$link1);
$getid = base64_decode($_REQUEST['refid']);
@extract($_POST);
////// case 1. if we want to update details

////// case 2. if we want to Add new user
if($_POST){
	//// initialize transaction parameters

    if ($_POST['add']=='ADD'){
	
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
	
    ///////// insert model data	   
	$exit_co=mysqli_query($link1,"select rep_code from repaircode_master where rep_code='".$repair_code."'");
		
	if(mysqli_num_rows($exit_co)== 0 ){   
	
   $usr_add="INSERT INTO repaircode_master set mapped_product ='".$prdstr."', brand_id ='".$brand_name."', rep_desc='".$repair."' ,status='".$status."',part_replace='".$part_rep."',check_rep='".$rep_type."',	rep_for='".$rep_for."',rep_level='".$rep_level."',rep_code='".$repair_code."'";
    $res_add=mysqli_query($link1,$usr_add);
	
	////// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$repair_code,"Repair Action","ADD",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	////// return message
	$msg="You have successfully created a Repair Action like ".$repair_code;
	$cflag="success";
	$cmsg="Success";
   }
	else{
		$flag = false;
	   	$cflag = "info";
		$cmsg = "Warning";
   		$msg = "Request could not be processed. Repair Code ($repair_code) Already Created.";
		}
	}
  
 
	mysqli_close($link1);
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
$(document).ready(function () {
	$('#release_date').datepicker({
		format: "yyyy-mm-dd",
		//startDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
});
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-cog"></i> <?=$_REQUEST['op']?> Repair Action </h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
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
            <div class="col-md-6"><label class="col-md-6 control-label">Repair Code <span class="red_small">*</span> </label>
                <div class="col-md-6">
               	<input type="text" name="repair_code" class=" form-control alphanumeric" id="repair_code" value="<?=$sel_result['rep_code']?>" required/>
              </div>
            </div>
              <div class="col-md-6"><label class="col-md-6 control-label">Repair Action  <span class="red_small">*</span> </label>
              <div class="col-md-6">
                  <input type="text" name="repair" class=" form-control" id="repair" value="<?=$sel_result['symp_desc']?>" required/>
              </div>
            </div>
          </div>
          
         <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Part Replace Check <span class="red_small">*</span></label>
              <div class="col-md-6">
               	<select name="rep_type" id="rep_type" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <option value="Y">Yes</option>
             <option value="N">No</option>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Repair For <span class="red_small">*</span></label>
              <div class="col-md-6">
                <select name="rep_for" id="rep_for" class="form-control required" required>
                  <option value="0"<?php if($sel_result['part_for'] == "0"){ echo "selected";}?>>ALL</option>
                  <?php
					$dept_query="SELECT * FROM location_type_master order by usedname";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['rep_level']?>"<?php if($sel_result['part_for'] == $br_dept['usedname']){ echo "selected";}?>><?php echo $br_dept['usedname']?></option>
                <?php }?>
                </select>
              </div>
            </div>
          </div>
          
		  
		    <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Part Replace <span class="red_small">*</span></label>
              <div class="col-md-6">
               	 <input type="checkbox" name="part_rep" id="part_rep" value="Y" class="col-md-offset-4">
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
                  <option value="<?=$br_dept['id']?>"<?php if($sel_result['rep_level'] == $br_dept['id']){ echo "selected";}?>><?php echo $br_dept['name']?></option>
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
              <?php if($_REQUEST['op']=='Add'){ ?>
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New Fault">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update fault Details">
              <?php }?>
              <input type="hidden" name="refid"  id="refid" value="<?=base64_encode($sel_result['symp_code'])?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='repair_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
            </div>
          </div>
    </form>
      </div>

    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>