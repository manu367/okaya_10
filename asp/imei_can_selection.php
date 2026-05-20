<?php
require_once("../includes/config.php");
/////get status//
$today=date("Y-m-d",$time_zone);
$arrstatus = getFullStatus("master",$link1);
$partid = base64_decode($_REQUEST['partcode']);

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
 <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="col-sm-8">
      <h2 align="center"><i class="fa fa-reply-all  fa-lg"></i> IMEI Details </h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="canbil_stock_in.php" method="post">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label"></label>
                <div class="col-md-6"></div>
            </div>
			
              <div class="col-md-6">
                  <div class="form-buttons" style="float:right">
                <input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm1.list)" value="Check All" />
                <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm1.list)" value="Uncheck All" />
                </div>
              </div>
            </div>
          
          <div class="form-group">
          
                
               	   <table width="100%"  class="table table-bordered"  align="center" cellpadding="4" cellspacing="0" border="1">
               <thead>
                  <tr>
                    <th width="15%" style="text-align:center"><label class="control-label">Sno</label></th>
                   <th width="15%" style="text-align:center"><label class="control-label">IMEI 1</label></th>
                    <th width="15%" style="text-align:center"><label class="control-label">IMEI 2</label></th>
                    <th width="15%" style="text-align:center"><label class="control-label">Model</label></th>
                    <th width="15%" style="text-align:center"><label class="control-label">Part Name</label></th>
					
					 <th width="15%" style="text-align:center"><label class="control-label">Confrim</label></th>
                 </thead>
       
				  
				 <?PHP 	 $sel_tras="select * from imei_details_asp where status ='1' and location_code='".$_SESSION['asc_code']."' and partcode='".$partid."'";
	$sel_res12=mysqli_query($link1,$sel_tras)or die("error1".mysqli_error($link1));
	$j=1;
                 while($imei = mysqli_fetch_array($sel_res12)){ ?>
				 <tr>
				 
				   <td width="15%" style="text-align:center"><label class="control-label"><?=$j?></label></td>
                    <td width="15%" style="text-align:center"><label class="control-label"><?=$imei['imei1']?>  <input type="hidden" name="imei1<?=$imei['id']?>" class="number form-control" id="imei1<?=$imei['id']?>" value="<?=$imei['imei1']?>"/></label></td>
                    <td width="15%" style="text-align:center"><label class="control-label"><?=$imei['imei2']?><input type="hidden" name="imei2<?=$imei['id']?>" class="number form-control" id="imei2<?=$imei['id']?>" value="<?=$imei['imei2']?>"/></label></td>
                    <td width="15%" style="text-align:center"><label class="control-label"><?=getAnyDetails($imei["model_id"],"model","model_id","model_master",$link1)?><input type="hidden" name="model_id<?=$imei['id']?>" class="number form-control" id="model_id<?=$imei['id']?>" value="<?=$imei['model_id']?>"/></label>
                 </td>
					 <td width="15%" style="text-align:center"><?=getAnyDetails($imei["partcode"],"part_name","partcode","partcode_master",$link1)?><input type="hidden" name="partcode<?=$imei['id']?>" class=" form-control" id="partcode<?=$imei['id']?>" value="<?=$imei['partcode']?>"/></td>
					 <td width="15%" style="text-align:center"> <input type="checkbox" checked="checked"  name="list[]"  id="list" value="<?=$imei['id']?>" /></td>
				 </tr>
				 <?php 	$j++; }?>
				 
				 <tr><td colspan="6">&nbsp;</td></tr>
				
				  </tbody>
              </table> 
            
          
             
          </div>
        
       
           
           
          <div class="form-group">
            <div class="col-md-12" align="center">
          
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Next" title="Next">
             <input name="partcode" type="hidden" class="form-control" id="partcode" value="<?=$partid?>"/>
              
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='assgin_can_repair.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
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