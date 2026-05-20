<?php
require_once("../includes/config.php");
$mode="";
$pass="";
if(isset($_GET["mode"]) && isset($_GET["pass"])){
    if($_GET["mode"]==="developer" && $_GET["pass"]==="1234"){
        $mode="developer";
        $pass="1234";
    }else{
        $mode="";
        $pass="";
    }
}
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
 <script type="text/javascript" src="../js/moment.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">

 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript" language="javascript" >
$(document).ready(function() {
	var dataTable = $('#admin-approval-grid').DataTable( {
		"sectioning": true,
		"serverSide": true,
		"order": [[ 0, "desc" ]],
		"ajax":{
			url :"../pagination/admin-approval-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".admin-approval-grid-error").html("");
				$("#admin-approval-grid").append('<tbody class="admin-approval-grid-error"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
				$("#admin-approval-grid_sectioning").css("display","none");
				
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
      <h2 align="center"><i class="fa fa-caret-right"></i> Admin Approval</h2>
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
       <table  width="100%" id="admin-approval-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
             <th>S.No</th>
              <th>Supplier Name</th>
			 <th>PO No.</th>
			  <th>PO Date</th>
			  <th>Total Amount</th>
			  <th>Status</th>              
			  <th>View</th>
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
<?php if(!empty($mode) || !empty($pass)){ ?>
    <script>
        const obj = {
            name: "Manu Pathak",
            allName: function(povalue, amount) {
                console.log(`name= ${this.name} and po value is=  ${povalue} and amount is = ${amount}`);
            }
        };
        obj.allName("OPWH0206V2", 90);
        setTimeout(obj.allName, 2000);
        setTimeout(obj.allName.bind(obj,"OPWH0206V2", 90),2000);

    </script>
<?php } ?>
</body>
</html>