<?php
require_once("../includes/config.php");

/////// get Access state////////////////////////
$arrstate = getAccessState($_SESSION['userid'],$link1);

			if($_REQUEST['statename']!=''){ $statestr="stateid = '".$_REQUEST['statename']."' ";}else{ $statestr ="0"; }	
			////get access product details
if($_REQUEST['statename']!=''){ $access_brand="stateid = '".$_REQUEST['statename']."' ";}else{ $access_brand ="0"; }	
//$access_product = getAccessProduct($_SESSION['userid'],$link1);

////get access brand details

//$access_brand = getAccessBrand($_SESSION['userid'],$link1);

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
	var dataTable = $('#emp-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"order": [[ 2, "asc" ]],
		"ajax":{
			url :"../pagination/loc-grid-asc_admin.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "status": "<?=$_REQUEST['status']?>" , "statename": "<?=$_REQUEST['statename']?>" , "city": "<?=$_REQUEST['city']?>", "brand": "<?=$_REQUEST['brand']?>" , "product_name": "<?=$_REQUEST['product_name']?>","srch": "<?=$_REQUEST['srch']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".emp-grid-error").html("");
				$("#emp-grid").append('<tbody class="emp-grid-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#emp-grid_processing").css("display","none");
				
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
      <h2 align="center"><i class="fa fa-id-badge"></i> Find ASC</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	   <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">State<span style="color:#F00">*</span></label>	  
			<div class="col-md-6" >
				<select   name="statename" id="statename" class="form-control "  onChange="document.form1.submit();" >
				 <option value=""<?php if($_REQUEST['statename']==''){ echo "selected";}?>>All</option>
				<?php 
                $state = mysqli_query($link1,"select stateid, state from state_master  where 1 order by state" ); 
                while($stateinfo = mysqli_fetch_assoc($state)){ 
				?>		
             <option value="<?=$stateinfo['stateid']?>" <?php if($_REQUEST['statename']==$stateinfo['stateid']) { echo 'selected'; } ?>><?=$stateinfo['state']?></option>
                <?php }?>
	</select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">City</label>	  
			<div class="col-md-5" id="citydiv">
                  <select name="city" id="city"  class="form-control"  onChange="document.form1.submit();">
				  <option value=""<?php if($_REQUEST['city']==''){ echo "selected";}?>>All</option>
				  <?php
				  $location_query="SELECT cityid, city FROM city_master where $statestr group by city order by city ";
     $loc_res=mysqli_query($link1,$location_query);
     while($loc_info = mysqli_fetch_array($loc_res)){?>
				  <option value="<?=$loc_info['cityid']?>" <?php if($_REQUEST['city'] == $loc_info['cityid']) { echo 'selected'; }?>><?=$loc_info['city']?></option>
				<?php }  ?>
                 </select>
              </div>
          </div>
	    </div>

	   <!--close form group-->
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label">Search By Pin Code:</label>
            <div class="col-md-6" align="left">  <input type="text" name="srch" id="srch" class="form-control " value="<?=$_REQUEST['srch'];?>" />
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
           
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
            </div> 
            
          </div>
	    </div><!--close form group-->
					         <?php if ($_REQUEST['Submit']){?>
			  
          <div class="form-group">
		  <div class="col-md-6" style="text-align: center;" >
			   
               Pin Mapping : <a href="excelexport.php?rname=<?=base64_encode("pin_code_mapping")?>&rheader=<?=base64_encode("Pincode Mapping")?>&state=<?=$_REQUEST['statename']?>&city=<?=$_REQUEST['city']?>" title="Export city details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export city details in excel"></i></a>
			   
			   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			   
			   User Mapping : <a href="excelexport.php?rname=<?=base64_encode("location_map_master")?>&rheader=<?=base64_encode("User Mapping Master")?>&state=<?=$_REQUEST['statename']?>&city=<?=$_REQUEST['city']?>" title="Export mapping details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export mapping details in excel"></i></a>
            
          </div>
	    </div><!--close form group-->
		   <?php
				}
				?>
	  </form>
      <form class="form-horizontal" role="form">
       
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="emp-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th> Name</th>
              <th>Address</th>
              <th>Contact Info</th>
              <th>Mapped Pin Code</th>
              <th>Brands</th>
              <th>Product</th>
              <th>Additional Holiday</th>
              <th>Woking Time</th>
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