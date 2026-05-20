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
	var dataTable = $('#voc-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"order": [[ 2, "asc" ]],
		"ajax":{
			url :"../pagination/condition-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "status": "<?=$_REQUEST['status']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".voc-grid-error").html("");
				$("#voc-grid").append('<tbody class="voc-grid-error"><tr><th colspan="7">No data found in the server</th></tr></tbody>');
				$("#voc-grid_processing").css("display","none");
				
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
      <h2 align="center"><i class="fa fa-bug"></i> Condition Master</h2>
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
                    <option value=""<?php if($_REQUEST['status']==''){ echo "selected";}?>>All</option>
                    <option value="1"<?php if($_REQUEST['status']==1){ echo "selected";}?>>Active</option>
                    <option value="2"<?php if($_REQUEST['status']==2){ echo "selected";}?>>Deactive</option>
                </select>
            </div>
          </div>
		  <div class="col-md-6">  
			<div class="col-md-5" align="left">
			 
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
			    //// get excel process id ////
				//$processid=getExlCnclProcessid("Admin Users",$link1);
			    ////// check this user have right to export the excel report
			    //if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
			   ?>
               <a href="excelexport.php?rname=<?=base64_encode("conditionmaster")?>&rheader=<?=base64_encode("Condition Master")?>&status=<?=base64_encode($_GET['status'])?>" title="Export Condition details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Condition details in excel"></i></a>
               <?php
				//}
				?>
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
      <form class="form-horizontal" role="form">
        <button title="Add New Condition" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='add_condition.php?op=Add<?=$pagenav?>'"><span>Add New Condition</span></button>&nbsp;&nbsp;
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="voc-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>Condition Code</th>
			  <th>Description</th>
              <th>Brand </th>
			   <th>Product </th>
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