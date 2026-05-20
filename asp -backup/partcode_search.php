<?php
require_once("../includes/config.php");

////get access product details
$access_product = getAccessProduct($_SESSION['asc_code'],$link1);
////get access brand details
$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);

//print_r($access_brand)."sdsdf";
//echo $_REQUEST['brand'];
/////get status//
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/jquery.js"></script>
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">

 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript" language="javascript" >
/*$(document).ready(function(){
    $('#myTable').dataTable();
});*/
$(document).ready(function() {
	var dataTable = $('#partcode-grid').DataTable( {
		"sectioning": true,
		"serverSide": true,
		"order": [[ 2, "asc" ]],
		"ajax":{
			url :"../pagination/partcode-grid-asc.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "brand": "<?=$_REQUEST['brand']?>" , "product": "<?=$_REQUEST['product_name']?>", "model": "<?=$_REQUEST['model']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".partcode-grid-error").html("");
				$("#partcode-grid").append('<tbody class="partcode-grid-error"><tr><th colspan="12">No data found in the server</th></tr></tbody>');
				$("#partcode-grid_sectioning").css("display","none");
				
			}
		}
	} );
} );
 function getmodel(indx){

	  var brandid=document.getElementById("brand").value;

	  var productCode=document.getElementById("product_name").value;

	  $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{brandinfo:brandid,productinfo:productCode},

		success:function(data){

		var getValue = data.split("~");

		document.getElementById("modeldiv").innerHTML=getValue[0];

	    }

	  });

  }
////// function for Stock Details to see the task history
function checkstockAvl(partid){
	$.get('stock_able_asc.php?partcode=' + partid, function(html){
		 $('#mappedModel .modal-body').html(html);
		 $('#mappedModel').modal({
			show: true,
			backdrop:"static"
		});
	 });
}

</script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
   include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-gears"></i> Part Search</h2>
      <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	    <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label"> Product Name <span class="red_small">*</span></label>
                <div class="col-md-5" align="left"> <!----- document.form1.submit(); ------>
               		<select name="product_name" id="product_name" class="form-control required" required onChange="document.form1.submit();" >
                  <option value="" <?php if($_REQUEST['product_name'] == ""){ echo "selected";}?> >--Select Product--</option>
                  <?php
					$dept_query="SELECT * FROM product_master where status = '1'  and product_id in (".$access_product.") order by product_name";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['product_id']?>"<?php if($_REQUEST['product_name'] == $br_dept['product_id']){ echo "selected";}?>><?php echo $br_dept['product_name']?></option>
                <?php }?>
                </select>
              </div>
            </div>
           <div class="col-md-6"><label class="col-md-5 control-label">Brand <span class="red_small">*</span></label>
              <div class="col-md-5" align="left" >
			  <?php
				$prd_maped_brd = mysqli_fetch_array(mysqli_query($link1, "select mapped_brand from product_master where product_id = '".$_REQUEST['product_name']."' "));
				$a = explode(",", $prd_maped_brd['mapped_brand']);
				$b = implode("','", $a);
			  ?>
               <select name="brand" id="brand" class="form-control required" onChange="document.form1.submit();" required>
                      <option value="" <?php if($_REQUEST['brand'] == ""){ echo "selected";}?>>--Select Brand--</option>
                      <?php 
                        $dept_query="SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.") and brand_id in ('".$b."') order by brand";
                        $check_dept=mysqli_query($link1,$dept_query);
                        while($br_dept = mysqli_fetch_array($check_dept)){
                      ?>
                      <option value="<?=$br_dept['brand_id']?>"<?php if($_REQUEST['brand'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>
                <?php }  ?>
                </select>
              </div>
            </div>
          </div>
	    <div class="form-group">
         
		  <div class="col-md-6"><label class="col-md-5 control-label">Model</label>	  
			<div class="col-md-5" >
            	<!--- onChange="document.form1.submit();" -->
				
			 <select name="model" id="model" class="form-control required" required  onChange="document.form1.submit();" >
             <option value="" <?php if($_REQUEST['model'] == ""){ echo "selected";}?> >--Select Model--</option>
            	<?php 
					if($_REQUEST['brand']!="" && $_REQUEST['product_name']==""){
					$mod_query="SELECT * FROM model_master where status = '1' and brand_id='".$_REQUEST['brand']."' order by model";
					}
					else if($_REQUEST['brand']!="" && $_REQUEST['product_name']!=""){
					$mod_query="SELECT * FROM model_master where status = '1' and brand_id='".$_REQUEST['brand']."' and  product_id='".$_REQUEST['product_name']."' order by model";
					}else if($_REQUEST['brand']=="" && $_REQUEST['product_name']!=""){
					$mod_query="SELECT * FROM model_master where status = '1'  and  product_id='".$_REQUEST['product_name']."' order by model";
					}else if($_REQUEST['brand']=="" && $_REQUEST['product_name']=""){
					$mod_query="SELECT * FROM model_master where status = '1'  order by model";
					}else{
						$mod_query="SELECT * FROM model_master where status = '1' and (brand_id='".$_REQUEST['brand']."' or product_id='".$_REQUEST['product_name']."' )  order by model";
					}					
					$check_mod=mysqli_query($link1,$mod_query);
					while($br_mod = mysqli_fetch_array($check_mod)){?>
                     <option value="<?=$br_mod['model_id']?>"<?php if($_REQUEST['model'] == $br_mod['model_id']){ echo "selected";}?>><?php echo $br_mod['model']?></option>
                <?php }?>			
                   </select>
			 
            </div>
          </div>
		 
	    </div><!--close form group-->
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
              
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
      <form class="form-horizontal" role="form">
       
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="partcode-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
			  <th>Brand</th>
			  <th>Product Category</th>
			  <th>Model Name</th>
			  <th>Part Name</th>
              <th>Partcode</th>
              <th>HSN Code</th>
			  <th>Vendor Partcode</th>
	          <th>Customer Price</th>
			   <th>Customer Warranty Days</th>
			    <th>Dealar Warranty Days</th>
			 
              <th>Availability</th>
            </tr>
          </thead>
          </table>
          </div>
      <!--</div>-->
      </form>
	    <!-- Start Model Mapped Modal -->
          <div class="modal modalTH fade" id="mappedModel" role="dialog">
            <div class="modal-dialog modal-dialogTH">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" align="center">Stock Details</h4>
                </div>
                <div class="modal-body modal-bodyTH">
                 <!-- here dynamic task details will show -->
                </div>
                <div class="modal-footer">
                  <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div><!--close Model Mapped modal-->
    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>