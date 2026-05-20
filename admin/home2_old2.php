<?php
require_once("../includes/config.php");

//echo $_SESSION['userid'];exit;
/*
$tab_array = getTabRights($_SESSION['userid'], $link1);

if($_SESSION['locusertype']=="LOCATION"){
	$tabstr = " and tabid  in ('".implode("','",array_keys($tab_array))."')";
	  $tabstr;
}else{//// if location is logging
	$tabstr = "";
}
/////// default tab rights
if($_SESSION['id_type']=="ASP"){ 
	$tab_for=" tabfor in ('ASP')";
} elseif($_SESSION['id_type']=="L3" || $_SESSION['id_type']=="L4"){ 
	$tab_for=" tabfor in ('ASP','L3')";
} elseif($_SESSION['id_type']=="WH"){ 
	$tab_for=" tabfor in ('WH')";
}
elseif($_SESSION['id_type']=="DL" || $_SESSION['id_type']=="DWH"){ 
	$tab_for=" tabfor in ('DL')";
}
	else{ 
	$tab_for=" tabfor in ('')";
}
*/
$tabstr = '';
$tab_for = '';
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
<link rel="stylesheet" href="../css/home_new.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript" language="javascript" >
/*$(document).ready(function(){
    $('#myTable').dataTable();
});*/
$(document).ready(function() {
	var dataTable = $('#admin-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"ajax":{
			url :"../pagination/adminusr-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "status": "<?=$_REQUEST['status']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".admin-grid-error").html("");
				$("#admin-grid").append('<tbody class="admin-grid-error"><tr><th colspan="8">No data found in the server</th></tr></tbody>');
				$("#admin-grid_processing").css("display","none");
				
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
		  
		  <h2 align="center" style="border-bottom:1px solid #aaa8a8;padding:25px 0px;margin:0px;"><i class="fa fa-users"></i> NAVIGATOR </h2>
		  <?php if($_REQUEST['msg']){?><br>
		  <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
		  <?php }?>
		  <div class="form-group" style="overflow:hidden;margin:0px;">
			  <div class="col-md-12" style="padding:10px 0px;">
			
				  
				  
				  <div class="ag-format-container" style="display:block;overflow:hidden;padding:10px 0px;">
					  <div class="ag-courses_box">

						  <?php
						  $arr_nav[] = "home";
						  ///////////////////// select main tab /////////
						  $sql_a = "SELECT maintabname, maintabicon FROM tab_master WHERE status='1'".$tab_for." ".$tabstr." GROUP BY maintabname ORDER BY maintabseq";
						  $res_maintab = mysqli_query($link1, $sql_a)or die("error1".mysqli_error($link1));
						  $num_maintab=mysqli_num_rows($res_maintab);
						  if($num_maintab > 0)
						  {
							  ///// start main tab if
							  $i=1;
							  while($row_maintab=mysqli_fetch_array($res_maintab))
							  {
								  ////// start main tab while 
								  $icon = $row_maintab['maintabicon'];
						  ?>
						  <div class="ag-courses_item">
							  <?php 
								  //////////////////// select sub tab of main tab ///////////////
								  $sql_b = "SELECT tabid, subtabname, subtabicon, filename FROM tab_master WHERE status='1' AND  maintabname='".$row_maintab['maintabname']."'".$tab_for." ORDER BY subtabseq";
								  $res_subtab=mysqli_query($link1, $sql_b);
								  $num_subtab=mysqli_num_rows($res_subtab);
								  if($num_subtab > 0)
								  {
							  ?>
							  <span class="ag-courses-item_link" style="text-align:center;font-size:14px;font-weight:bold;background:#2e353d;"><span><i class="fa <?=$icon?> fa-lg" style="font-size:14px;display:inline-block;line-height: 20px;"></i><b style="color:#fff;padding-left:4px;vertical-align:bottom;"><?=$row_maintab['maintabname']?></b></span></span>
							  <?php
									  /////start sub tab if
									  while($row_subtab=mysqli_fetch_array($res_subtab))
									  {
										  /////// start sub tab while
										  ///// check permission of this tab is given or not /////
										  if($_SESSION['tabuserid']==''){ 
											  $state_acc=mysqli_query($link1,"select tabid from access_tab where status='1' and tabid='".$row_subtab['tabid']."' and userid='".$_SESSION['userid']."'")or die(mysqli_error($link1));
										  }
										  else{
											  $state_acc=mysqli_query($link1,"select tabid from access_tab where status='1' and tabid='".$row_subtab['tabid']."' and userid='".$_SESSION['tabuserid']."'")or die(mysqli_error($link1));
										  }
										  $num=mysqli_num_rows($state_acc);

										  if($num > 0) {  

											  ///// check permission of this tab is given or not
											  if($tab_array[$row_subtab['tabid']] == 1 || $_SESSION['locusertype']=="LOCATION"){

												  $arr_nav[] = $row_subtab['tabid'];
											  }
							  ?>


							  <a href="<?=$row_subtab['filename'];?>.php?pid=<?=$row_subtab['tabid']?>&hid=<?=$row_maintab['maintabname']?>" style="color:#333" class="ag-courses-item_link">
								  <div class="ag-courses-item_bg">
								  </div>
								  <div class="ag-courses-item_title">
									  <?=$row_subtab['subtabname']?>
								  </div>
							  </a>
							  <?php 
											  }
									  }///// end sub tab while
													   }//// end sub tab if
							  ?>
						  </div>    

						  <?php
								  $i++;
							  }////// end main tab while
						  }//// end main tab if
						  ?>
					  </div>
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  
			  </div>
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