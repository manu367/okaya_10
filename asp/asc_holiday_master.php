<?php
require_once("../includes/config.php");
$timing_asc=mysqli_query($link1,"SELECT sno  FROM holidays where location_code='".$_SESSION['asc_code']."' and type='ASC Working Time' ");
$time_no=mysqli_num_rows($timing_asc);

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
	var dataTable = $('#asc-holiday-grid').DataTable( {
		"sectioning": true,
		"serverSide": true,
		"order": [[ 1, "asc" ]],
		"ajax":{
			url :"../pagination/asc-holiday-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "status": "<?=$_REQUEST['status']?>" },
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".asc-holiday-grid-error").html("");
				$("#asc-holiday-grid").append('<tbody class="asc-holiday-grid-error"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
				$("#asc-holiday-grid_sectioning").css("display","none");
				
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
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa <?=$fa_icon?>"></i>  Holidays  Master</h2>
      <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php }?>
         <form class="form-horizontal" role="form" name="form1" action="" method="post">
	   
	    <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label"> Status</label>	  
			<div class="col-md-5" align="left">
			  <select name="status" id="status" class="form-control"  >
                     <option value=""<?php if(isset($_REQUEST['status'])){if($_REQUEST['status']==''){ echo "selected";}}?>>All</option>
                    <option value="1"<?php if(isset($_REQUEST['status'])){if($_REQUEST['status']==1){ echo "selected";}}?>>Active</option>
                    <option value="2"<?php if(isset($_REQUEST['status'])){if($_REQUEST['status']==2){ echo "selected";}}?>>Deactive</option>
                </select>
            </div>
          </div>
		  <div class="col-md-6">  
			<div class="col-md-5" align="left">
			 
            </div>
          </div>
	    </div><!--close form group-->
        </form>
	 
      <form class="form-horizontal" role="form">
        <button title="Add Additional Holiday" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='asc_add_holiday.php?op=Add<?=$pagenav?>'"><span>Add Additional Holiday</span></button> <br/><br/> &nbsp;&nbsp;
		 &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; 
         <?php if($time_no == 0){?>
         
         <button title="Add Working Time" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='asc_add_working.php?op=Add<?=$pagenav?>'"><span>Add Working Time</span></button> <br/><br/> <?php } ?>
		 
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="asc-holiday-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>Weekly Close Day/Weekly Timing/Date</th>
              <th>Description</th>
              <th>Status</th>
              <!--<th>View/Edit</th>-->
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