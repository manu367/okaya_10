<?php
require_once("../includes/config.php");

$brandarray=$_REQUEST['brand'];
////get access brand details
$access_brand = getAccessBrand($_SESSION['userid'],$link1);

if($access_brand!=''){
	$brandfiltter="and brand_id in (".$access_brand.")";
}else {
	$brandfiltter="";
}

/////get status//
$arrstatus = getFullStatus("master",$link1);
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='Edit'){
	$sel_usr="select * from product_master where product_id='".$_REQUEST['id']."' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){

	//// array initialization to send by query string of  brand
	$brandstr = "";
	$arr_brand = $_REQUEST['brand'];
	for($i=0; $i<count($arr_brand); $i++){
		if($brandstr){
			$brandstr.=",".$arr_brand[$i];
		}else{
			$brandstr.= $arr_brand[$i];
		}
	}

   if ($_POST['add']=='ADD'){
    $usr_add="INSERT INTO product_master set product_name ='".$product_name."',status='".$status."', mapped_brand = '".$brandstr."', createdate='".date("Y-m-d H:i:s")."',createby='".$_SESSION['userid']."'";
    $res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));
	$dptid = mysqli_insert_id($link1); 
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$dptid,"PRODUCT","ADD",$_SERVER['REMOTE_ADDR'],$link1,"");
	////// return message
	$msg="You have successfully created a product like ".$product_name;
	$cflag="success";
   }
   else if ($_POST['upd']=='Update'){
    $usr_upd="update product_master set product_name ='".$product_name."',status='".$status."', mapped_brand = '".$brandstr."', updatedate='".date("Y-m-d H:i:s")."',updateby='".$_SESSION['userid']."' where product_id = '".$refid."'";
    $res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysqli_error($link1));
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$com_name,"PRODUCT","UPDATE",$ip,$link1,"");
	////// return message
	$msg="You have successfully updated product details for ".$product_name;
	$cflag="success";
   }
   ///// move to parent page
    header("location:product_master.php?msg=".$msg."&chkflag=".$cflag."".$pagenav);
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
</script>
<script src="../js/frmvalidate.js"></script>
<script src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-suitcase"></i> <?=$_REQUEST['op']?> Product</h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Product Name<span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input type="text" name="product_name" class="required form-control" id="product_name" value="<?=$sel_result['product_name']?>" required/>
              </div>
            </div>
          </div>
		  
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Status</label>
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
            <div class="col-md-10"><label class="col-md-4 control-label">Map Brand</label>
              <div class="col-md-6">
			  
			  <table width="100%" id="brandmap" class="table table-bordered table-hover">
                  <tbody>
                    <?php
					///// check if any mapping entry with Y status is there
					$res_map = mysqli_query($link1,"select mapped_brand from product_master where product_id='".$sel_result['product_id']."'")or die(mysqli_error());
					$num_map = mysqli_fetch_assoc($res_map);
					$res_entity = explode(",",$num_map['mapped_brand']);
					
					/////make mapped entity array
					$array_entity = array();
					for($i=0; $i<count($res_entity); $i++){
						$array_entity[$res_entity[$i]] = $res_entity[$i];
					}
						
					$rs=mysqli_query($link1,"select brand_id,brand from brand_master where status='1' order by brand");
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
                      <td><input style="width:20px"  type="checkbox" id="brand" name="brand[]" value="<?=$row['brand_id']?>" <?php if($array_entity[$row['brand_id']] == $row['brand_id']){ echo "checked";}?>/>
                        &nbsp;  
                        <?=$row['brand']?></td>
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
            <div class="col-md-10" align="center">
              <?php if($_REQUEST['op']=='Add'){ ?>
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New Product">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update Product Details">
              <?php }?>
              <input type="hidden" name="refid"  id="refid" value="<?=$sel_result['product_id']?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='product_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
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