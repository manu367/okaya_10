<?php
require_once('../includes/config.php');
$arrstatus = getJobStatus($link1);
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
    /////// if user enter imei no or job no. then search button  should be enabled
	 $("#search_val").keyup(function() {
		 if($("#search_val").val()!=""){ 
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
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
       <h2 align="center"><i class="fa fa-print"></i> DOA Duplicate Certificate</h2><br/><br/>
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
                <div class="col-md-10"><label class="col-md-4 control-label">Enter IMEI No./Job No.</label>
                  <div class="col-md-6">
                     <input type="text" name="search_val" class="form-control" id="search_val"   value="<?=$_REQUEST['search_val']?>" placeholder="Enter only IMEI No./Job No."/>
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
			<?php if($_POST['Submit']=="Search" && ($_POST['search_val']!='')){
			
			$sql1 = mysqli_query($link1,"select * from jobsheet_data where (imei = '".$_POST['search_val']."' or sec_imei = '".$_POST['search_val']."' or job_no  = '".$_POST['search_val']."')");

			///// (1) if imei_no no. found in  jobsheet data /////////////////////////////////////////////////////////// 
			if(mysqli_num_rows($sql1) >=1)
			{
			?>
			<div class="panel panel-info">
              <div class="panel-heading" align="center">Search Detail</div>
               <div class="panel-body">
			  	<?php 					
					if($_POST['search_val']){ echo "Your searched criteria <strong>IMEI No./Job No. :- </strong>".$_POST['search_val'];}
				?>
                	<table class="table table-bordered" width="100%">
                    	<thead>
                        	<tr>
                            	<td width="5%"><strong>S.No.</strong></td>
								<td width="11%"><strong>Job No.</strong></td>
                                <td width="11%"><strong>IMEI 1</strong></td>
                                <td width="11%"><strong>IMEI 2</strong></td>
                                <td width="10%"><strong>Model </strong></td>
                                <td width="10%"><strong>Call Type</strong></td>
                                <td width="10%"><strong>Open Date</strong></td>
                                <td width="12%"><strong>Close Date</strong></td>
                                <td width="9%"><strong>Status</strong></td>
                                <td width="11%"><strong>Print</strong></td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
						$j = 1;
						while ($row = mysqli_fetch_array($sql1)){
						?>
                        	<tr>
                            	<td><?=$j;?></td>
								<td><?=$row['job_no'];?></td>
                                <td><?=$row['imei'];?></td>
                                <td><?=$row['sec_imei'];?></td>
                                <td><?=$row['model'];?></td>
                                <td><?=$row['call_type'];?></td>
                                <td><?= dt_format($row['open_date']);?></td>
                                <td><?=dt_format($row['close_date']);?></td>
                                <td><?php if($arrstatus[$row["sub_status"]][$row["status"]]){
		$result_st = $arrstatus[$row["sub_status"]][$row["status"]];
	}else{
		$result_st = getAnyDetails($row["status"],"display_status","status_id","jobstatus_master",$link1);
	}
	echo $result_st;
	?></td>
                                <td><?php if($row['status']=='9' && $row['sub_status']=='9' && $row['call_type']=='DOA'){?><a href="doa_print_cert.php?refid=<?=base64_encode($row['job_no'])?><?=$pagenav?>"  target="_blank" title="Print Duplicate DOA"><i class='fa fa-print fa-lg faicon' title='Print Duplicate DOA'><?php }?></i></a></td>                      
                            </tr>
						<?php
						}
                        ?>
                        </tbody>
                    </table>
			<?php }
	
		//////////  if does not exist in any table ///////////////////////////////////////////////////////////// 
					else {					
					?>
					<div class="panel panel-info">
              		<div align="center"></div>
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



</body>
</html>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>