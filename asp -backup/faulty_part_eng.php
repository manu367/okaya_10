<?php
require_once("../includes/config.php");
///// if dispatch details is updated like courier or docket of any invoice

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
<!-- datatable plugin-->
<link rel="stylesheet" href="../css/jquery.dataTables.min.css">
<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<!--  -->
<script>
	$(document).ready(function(){
        $("#frm").validate();
    });
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" language="javascript" >
 ////////////////////// function to check or uncheck ///////////////////////////////////////////
 function checkAll(field){
   for (i = 0; i < field.length; i++)
        field[i].checked = true ;
 }
 function uncheckAll(field){
   for (i = 0; i < field.length; i++)
        field[i].checked = false ;
 }
 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$(document).ready(function() {
	var dataTable = $('#part-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		//"order": [[ 3, "asc" ]],
		"ajax":{
			url :"../pagination/faulty-part-return_asp.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>","status": "<?=$_REQUEST['status']?>","location_code": "<?=$_REQUEST['location_code']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".part-grid-error").html("");
				$("#part-grid").append('<tbody class="part-grid-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#part-grid_processing").css("display","none");
				
			}
		}
	} );
} );
////// function for open model to cancel row wise PNA part

</script>
<title>
<?=siteTitle?>
</title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
    <?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-shopping-basket"></i> Faulty Part Receive From Engineer</h2>
      <?php if($_REQUEST['msg']){?>
      <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        <strong>
        <?=$_REQUEST['chkmsg']?>
        !</strong>&nbsp;&nbsp;
        <?=$_REQUEST['msg']?>
        . </div>
      <?php }?>
      <form class="form-horizontal" role="form" id="frm" name="frm" >
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/>
          <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Engineer Name</label>	  
			<div class="col-md-6" align="left">
				<select name="location_code" id="location_code" class="form-control" onChange="document.form1.submit();">
                <option value="">ALL</option>
                <?php
                $res_pro = mysqli_query($link1,"select userloginid,locusername from locationuser_master where location_code='".$_SESSION['asc_code']."'"); 
                while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                <option value="<?=$row_pro['userloginid']?>" <?php if($_REQUEST['location_code'] == $row_pro['userloginid']) { echo 'selected'; }?>><?=$row_pro['locusername']." (".$row_pro['userloginid'].")"?></option>
                <?php } ?>
                 </select>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-4 control-label">Status</label>	  
			<div class="col-md-6" align="left">
			 <select id="status"  name="status" class="form-control"  onChange="document.form1.submit();">
			 
			   <option value="1" <?php if($_REQUEST['status'] == "1") { echo 'selected'; }?>>Pending</option>
				<option value="4" <?php if ($_REQUEST['status'] == "4"){ echo 'selected'; }?>>Received</option>				
			</select>
            </div>
          </div>
	    </div><!--close form group-->
           <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-6">
				
             
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6">
            	<div style="display:inline-block;float:left" id="modeldiv">
            
                </div>
                <div style="display:inline-block;float:right">
                	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               		<input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               		<input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                </div>
            </div>
          </div>
	    </div>
		   </form>
		   <form class="form-horizontal" role="form" id="frm2" name="frm2" method="post"  action="issue_part_faulty-return.php">
          <div class="form-buttons" style="float:right">
            <input type="button" class="btn btn<?=$btncolor?>"  onClick="checkAll(document.frm2.checkBox)"  value="Select All"  />
            <input type="button"  class="btn btn<?=$btncolor?>" onClick="uncheckAll(document.frm2.checkBox)"  value="Unselect All"/>
          </div>
          <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
          <table  width="100%" id="part-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
            <thead>
              <tr>
                <th>S.No</th>
                <th>Partcode</th>
                <th>Part Details</th>
                <th>Job No.</th>
                <th>Consume Date</th>
                <th>Engineer Name</th>
				 <th>Receive Type</th>
				
				 <th>Receive Date</th>
                <th>Confirm</th>
              </tr>
            </thead>
          </table>
          <div align="center">
         
          </div>
        </div>
		<div>     <input type="submit" name="save" id="save"  class="btn btn<?=$btncolor?>" value="Receive"/>
            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/></div>
        <!--</div>-->
      </form>
         <!-- Start Model Mapped Modal -->
          <div class="modal modalTH fade" id="cancel_pna" role="dialog">
       
            <div class="modal-dialog modal-dialogTH">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" align="center"><i class='fa fa-trash fa-lg faicon'></i> Delete PNA Part</h4>
                </div>
                <div class="modal-body modal-bodyTH">
                 <!-- here dynamic task details will show -->
                </div>
                <div class="modal-footer">
           
                  <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">No</button>
                </div>
              </div>
            </div>
            </form>
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