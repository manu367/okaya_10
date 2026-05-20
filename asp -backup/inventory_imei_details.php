<?php
require_once('../includes/config.php');
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
  <script>
	$(document).ready(function(){
        $("#frm1").validate();
    });
 </script>
 <script language="javascript" type="text/javascript">
 $(document).ready(function() {	
    /////// if user enter contact no. then search button  should be enabled
	 $("#imei_no").keyup(function() {
		 if($("#imei_no").val()!=""){ 
			$("#Submit").attr("disabled",false);
		 }else{
			 $("#Submit").attr("disabled",true);
		 }
    });
 });

 </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
   // include("../includes/leftnav2.php");
	  include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
       <h2 align="center"><i class="fa fa-history"></i> IMEI  Search</h2><br/><br/>
	   <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
          </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php }?>    
      	<div class="form-group"  id="page-wrap" style="margin-left:10px;">
		<br></br> 
			<form id="frm1" name="frm1" class="form-horizontal" action="" method="post">         
              <div class="form-group">
                <div class="col-md-10"><label class="col-md-4 control-label">Enter IMEI No.</label>
                  <div class="col-md-6">
                     <input type="text" name="imei_no" class=" form-control" id="imei_no" maxlength="30"  value="<?=$_REQUEST['imei_no']?>" placeholder="Enter only IMEI No."/>
                  </div>
                </div>
              </div>
               <div class="form-group">
                <div class="col-md-10"><label class="col-md-4 control-label"></label>
                  <div class="col-md-6">
                     <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="Submit" value="Search" title="Search" disabled>
                  </div>
                </div>
              </div>
          	</form>
			<?php if($_POST['Submit']=="Search" && ($_POST['imei_no']!='')){
			$sql = mysqli_query($link1,"select * from imei_data_import where imei1 = '".$_POST['imei_no']."'  or imei2 = '".$_POST['imei_no']."' ");
			$sql1 = mysqli_query($link1,"select * from jobsheet_data where (imei = '".$_POST['imei_no']."' or  sec_imei = '".$_POST['imei_no']."' )");
			$sql2 =  mysqli_query($link1,"select * from imei_details_asp where (imei1 = '".$_POST['imei_no']."' or  imei2 = '".$_POST['imei_no']."' )");
			/////// (1)  if imei no exist in imeidata import table/////////////////////////////////////////////////////////////////////
			if (mysqli_num_rows($sql) >=1)
				{						
			?>
            <div class="panel panel-info">
              <div class="panel-heading" align="center"> IMEI Import Detail</div>
              <div class="panel-body">
			  	<?php 					
					if($_POST['imei_no']){ echo "Your searched criteria <strong>IMEI No. :- </strong>".$_POST['imei_no'];}
				?>
                	<table class="table table-bordered" width="100%">
                    	<thead>
                        	<tr>
                            	<td><strong>S.No.</strong></td>
                                <td><strong>IMEI 1</strong></td>
                                <td><strong>IMEI 2</strong></td>
                                <td><strong>Model </strong></td>
                                <td><strong>Import Date</strong></td>
                                <td><strong>Activation Date</strong></td>
                                <td><strong>Update Date/Time</strong></td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
						$j = 1;
						while ($row = mysqli_fetch_array($sql)){
						?>
                        	<tr>
                            	<td><?=$j;?></td>
                                <td><?=$row['imei1'];?></td>
                                <td><?=$row['imei2'];?></td>
                                <td><?=getAnyDetails($row['model_id'],"model","model_id","model_master",$link1);?></td>
                                <td><?= dt_format($row['import_date']);?></td>
                                <td><?=dt_format($row['activation_date']);?></td>
                                <td><?=$row['update_date'];?></td> 
                            </tr>
						<?php
						}
                        ?>
                        </tbody>
                    </table>
          
				<?php
						}
			////////////// (3) if IMEI no exist in jobsheet data table ///////////////////////////////////////////////////////////////////
				 if(mysqli_num_rows($sql1) >=1) {						
					?>
					<div class="panel panel-info">
              		<div class="panel-heading" align="center">Job IMEI Detail</div>
				  	<div class="panel-body">                        
						<?php 
						if($_POST['imei_no']){ echo "Your searched criteria <strong>IMEI No. :- </strong>".$_POST['imei_no'];}
							?>	
                      	<table class="table table-bordered" width="100%">
                    	<thead>
                        	<tr>
                            	<td><strong>S.No.</strong></td>
                               <td><strong>Job No.</strong></td>
                                <td><strong>Customer Name</strong></td>
                                <td><strong>Location Name</strong></td>
                                <td><strong>Open Date</strong></td>
                                <td><strong>Model</strong></td>
                                <td><strong>IMEI 1</strong></td>
								<td><strong>IMEI 2</strong></td>
								<td><strong>View</strong></td>
                            </tr>
                        </thead>
                        <tbody>
				<?php	
			  	$j = 1;
						while ($row1 = mysqli_fetch_array($sql1)){
						?>
                        	<tr>
                            	<td><?=$j;?></td>
                                <td><?=$row1['job_no'];?></td>
                                <td><?=$row1['customer_name'];?></td>
                                <td><?= getAnyDetails($row1['location_code'],"locationname","location_code","location_master",$link1);?></td>
                                <td><?=dt_format($row1['open_date']);?></td>
								<td><?=$row1['model'];?></td>
                                <td><?=$row1['imei'];?></td>
                                <td><?=$row1['sec_imei'];?></td>
								<td><div align="center"><a href='job_view_imei.php?refid=<?=$row1['job_no'];?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg faicon" title="view details"></i></a></div></td>
                            </tr>
						<?php
						}
                        ?>
                        </tbody>
                    </table>
					<?php  }  
					
					//////////
					////////////// (4) if IMEI no exist in IMEI_DETAIL_ASP data table ///////////////////////////////////////////////////////////////////
				 if(mysqli_num_rows($sql2) >=1) {						
					?>
					<div class="panel panel-info">
              		<div class="panel-heading" align="center">IMEI Issue Detail</div>
				  	<div class="panel-body">                        
						<?php 
						if($_POST['imei_no']){ echo "Your searched criteria <strong>IMEI No. :- </strong>".$_POST['imei_no'];}
							?>	
                      	<table class="table table-bordered" width="100%">
                    	<thead>
                        	<tr>
                            	<td><strong>S.No.</strong></td>
                               <td><strong>IMEI 1</strong></td>
								<td><strong>IMEI 2</strong></td>
                                <td><strong>Partcode</strong></td>
                                <td><strong>Model </strong></td>
                                <td><strong>Location Name</strong></td>
                                <td><strong>Entry Date</strong></td>   
								 <td><strong>View</strong></td>     
                            </tr>
                        </thead>
                        <tbody>
				<?php	
			  	$j = 1;
						while ($row2 = mysqli_fetch_array($sql2)){
						?>
                        	<tr>
                            	<td><?=$j;?></td>
                                <td><?=$row2['imei1'];?></td>
                                <td><?=$row2['imei2'];?></td>
                                <td><?=$row2['partcode'];?></td>
								 <td><?=$row2['model_id'];?></td>
                                <td><?= getAnyDetails($row2['location_code'],"locationname","location_code","location_master",$link1);?></td>
                                <td><?=dt_format($row2['entry_date']);?></td>
								<td><div align="center"><a href='billingimei_view_imei.php?refid=<?=$row2['challan_no'];?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg faicon" title="view details"></i></a></div></td>
                            </tr>
						<?php
						}
                        ?>
                        </tbody>
                    </table>
					<?php  }  
					
					////////// (5) if does not exist in any table ///////////////////////////////////////////////////////////// 
					if(mysqli_num_rows($sql)==0 && mysqli_num_rows($sql1)==0 && mysqli_num_rows($sql2)==0) {					
					?>
					<div class="panel panel-info">
              		<div class="panel-heading" align="center">Search History</div>
				  	<div class="panel-body">  
					<?php 
					echo "<strong>Not found in database.</strong>"
							?>
					<?php  }  				
					?>					
  </div>
            </div>     
       <?php } ?>
      </div>
	  </div>
	  </div>
	  </div>  
</div>
</div>
</div>
</div>
</body>
</html>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>