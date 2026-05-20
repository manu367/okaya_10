<?php
require_once("../includes/config.php");
/////get status//
$arrstatus = getFullStatus("master",$link1);
$getid = base64_decode($_REQUEST['refid']);
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='Edit'){
	$sel_usr="select * from defect_master where defect_code='".$getid."'";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){
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
	//// initialize transaction parameters
    if ($_POST['add']=='ADD'){
		
		$exit_co=mysqli_query($link1,"select defect_code from defect_master where defect_code='".$defect_code."'");
		
		if(mysqli_num_rows($exit_co)== 0 ){ 
    ///////// insert model data	   
    $usr_add="INSERT INTO defect_master set mapped_product ='".$prdstr."', brand_id ='".$brand_name."', defect_desc='".$defect."' ,status='".$status."',defect_code='".$defect_code."'";
    $res_add=mysqli_query($link1,$usr_add);

	
	$flag = dailyActivity($_SESSION['userid'],$defect_code,"Defect_master","ADD",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	////// return message
	$msg="You have successfully created a defect code like ".$defect_code;
	$cflag="success";
	$cmsg="Success";
		}
		else{
		$flag = false;
	   	$cflag = "info";
		$cmsg = "Warning";
   		$msg = "Request could not be processed. Defect Code ($defect_code) Already Created.";
			}
   }
   else if ($_POST['upd']=='Update'){ 
 $usr_upd = "Update defect_master set mapped_product ='".$prdstr."', brand_id ='".$brand_name."', defect_desc='".$defect."' ,status='".$status."' where defect_code='".$getid."'";
  
    $res_upd = mysqli_query($link1,$usr_upd);
///// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$getid,"Defect_master","UPDATE",$ip,$link1,$flag);
	////// return message
	$msg="You have successfully updated Defect details for ".$getid;
	$cflag="success";
	$cmsg="Success";
   }else{
	    $flag = false;
	   	$cflag = "info";
		$cmsg = "Warning";
   		$msg = "Request could not be processed. Please try again.";
   }
 
	mysqli_close($link1);
   ///// move to parent page
   header("location:defect_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
      <h2 align="center"><i class="fa fa-bug"></i> <?=$_REQUEST['op']?> Defect </h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
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
            <div class="col-md-6"><label class="col-md-6 control-label">Defect Code <span class="red_small">*</span></label>
                <div class="col-md-6">
                 <?php if($sel_result['defect_code']!=''){?>
					<input type="text" name="defect_code" class=" form-control alphanumeric" id="defect_code" value="<?=$sel_result['defect_code'];?>" required readonly/>
                    <?php }else{?>
                    <input type="text" name="defect_code" class=" form-control alphanumeric" id="defect_code" required/> <?php }?>
               	
              </div>
            </div>
              <div class="col-md-6"><label class="col-md-6 control-label">Defect Description<span class="red_small">*</span> </label>
              <div class="col-md-6">
                 <input type="text" name="defect" class=" form-control" id="defect" required value="<?=$sel_result['defect_desc'];?>"/>
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
					$res_map = mysqli_query($link1,"select mapped_product from defect_master where defect_code='".$getid."'")or die(mysqli_error());
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
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New Defect">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update Defect Details">
              <?php }?>
              <input type="hidden" name="refid"  id="refid" value="<?=base64_encode($sel_result['defect_code'])?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='defect_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
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