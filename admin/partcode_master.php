<?php
require_once("../includes/config.php");
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
			url :"../pagination/partcode-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "status": "<?=$_REQUEST['status']?>" , "model": "<?=$_REQUEST['model']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".partcode-grid-error").html("");
				$("#partcode-grid").append('<tbody class="partcode-grid-error"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
				$("#partcode-grid_sectioning").css("display","none");
				
			}
		}
	} );
} );
</script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-gears"></i> Partcode Master</h2>
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
         <div class="col-md-6"><label class="col-md-5 control-label"> Status</label>	  
			<div class="col-md-5" align="left">
			   <select name="status" id="status" class="form-control"  onChange="document.form1.submit();">
                    <option value="3"<?php if($_REQUEST['status']=='3'){ echo "selected";}?>>All</option>
                    <option value=""<?php if($_REQUEST['status']==''){ echo "selected";}?>>Active</option>
                    <option value="2"<?php if($_REQUEST['status']==2){ echo "selected";}?>>Deactive</option>
                </select>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Model</label>	  
			<div class="col-md-5" >
			<select name="model" id="model" class="form-control"  onChange="document.form1.submit();">
			<option value="">Please Select</option>
                    <?php 
				 $model_query=mysqli_query($link1,"SELECT distinct(model_id),model FROM model_master where status = '1'  "  );
				  while($model_res = mysqli_fetch_assoc($model_query)){?>
                <option value="<?=$model_res['model_id']?>" <?php if($_REQUEST[model] == $model_res['model_id']) { echo 'selected'; }?>><?=$model_res['model']." | ".$model_res['model_id']?></option>
                <?php } ?>
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
               <?php
			    //// get excel section id ////
				//$sectionid=getExlCnclProcessid("Admin Users",$link1);
			    ////// check this user have right to export the excel report
			    //if(getExcelRight($_SESSION['userid'],$sectionid,$link1)==1){
			   ?>
               <a href="excelexport.php?rname=<?=base64_encode("partcodemaster")?>&rheader=<?=base64_encode("Partcode Master")?>&status=<?=base64_encode($_GET['status'])?>" title="Export partcode details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export partcode details in excel"></i></a> 
               <?php
				//}
				?>
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
      <form class="form-horizontal" role="form">
		<div class="form-group">
          <div class="col-md-12">
			<button title="Add New Partcode" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='add_partcode.php?op=Add<?=$pagenav?>'"><span>Add New Partcode</span></button> <br/><br/>
			 <button title="Add New partcode" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='partcode_uploader.php?op=Add<?=$pagenav?>'"><span>Add New Partcode By Uploader</span></button>
		 </div>
		 </div>
		 
        <div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;">
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="partcode-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>Brand</th>
              <th>Product</th>
              <th>Model Name</th>
              <th>Partcode</th>
              <th>HSN Code</th>
			  <th>Part Name</th>
			  <th>Part Category</th>
              <th>Status</th>
              <th>View/Edit</th>
            </tr>
          </thead>
          </table>
          </div>
      <!--</div>-->
      </form>
    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>