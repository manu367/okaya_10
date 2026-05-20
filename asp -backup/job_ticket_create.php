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
	 $("#contact_no").keyup(function() {
		 if($("#contact_no").val()!=""){ 
			$("#Submit").attr("disabled",false);
		 }else{
			 $("#Submit").attr("disabled",true);
		 }
    });
 });

////// function for open model to see the job details
function viewTicketDetails(ticketno){
	$.get('ticket_view_only.php?refid=' + ticketno, function(html){
		 $('#viewTicket .modal-body').html(html);
		 $('#viewTicket').modal({
			show: true,
			backdrop:"static"
		});
	 });
}
 </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
       <h2 align="center"><i class="fa fa-ticket"></i> New Ticket</h2><br/><br/>
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
                <div class="col-md-10"><label class="col-md-4 control-label">Contact No.</label>
                  <div class="col-md-6">
                     <input type="text" name="contact_no" class="digits form-control" id="contact_no" maxlength="10"  value="<?=$_REQUEST['contact_no']?>" placeholder="Enter only Contact No."/>
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
			<?php if($_POST['Submit']=="Search" && ($_POST['contact_no']!='')){
			$sql = mysqli_query($link1,"select * from ticket_master where contact_no = '".$_POST['contact_no']."' ");
			$sql1 = mysqli_query($link1,"select * from jobsheet_data where contact_no = '".$_POST['contact_no']."' ");
			///// (1) if contact no. found in both jobsheet data and  ticket master table/////////////////////////////////////////////////////////// 
			if(mysqli_num_rows($sql) >=1 && (mysqli_num_rows($sql1) >=1))
			{
			?>
			<div class="panel panel-info">
              <div class="panel-heading" align="center">Ticket History</div>
               <div class="panel-body">
			  	<?php 					
					if($_POST['contact_no']){ echo "Your searched criteria <strong>Contact No. :- </strong>".$_POST['contact_no'];}
				?>
                	<table class="table table-bordered" width="100%">
                    	<thead>
                        	<tr>
                            	<td><strong>S.No.</strong></td>
                                <td><strong>Ticket No.</strong></td>
                                <td><strong>Customer Name</strong></td>
								<td><strong>Customer Email</strong></td>
                                <td><strong>Location Name</strong></td>
                                <td><strong>Open Date</strong></td>
                                <td><strong>Model</strong></td>
                                <td><strong>View</strong></td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
						$j = 1;
						while ($row = mysqli_fetch_array($sql)){
						?>
                        	<tr>
                            	<td><?=$j;?></td>
                                <td><?=$row['ticket_no'];?></td>
                                <td><?=$row['customer_name'];?></td>
                                <td><?=$row['email'];?></td>
                                <td><?= getAnyDetails($row['location_code'],"locationname","location_code","location_master",$link1);?></td>
                                <td><?=dt_format($row['open_date']);?></td>
                                <td><?=$row['model'];?></td>
                                <td><div align="center"><a href='#' title='view ticket details' onClick='viewTicketDetails("<?=base64_encode($row['ticket_no'])?>");'><i class='fa fa-eye fa-lg faicon' title='view ticket details'></i></a></div></td>
                            </tr>
						<?php
						}
                        ?>
                        </tbody>
                    </table>
                    <div align="center">
                    <form id="frm4" name="frm4" class="form-horizontal" action="job_ticket_history.php" method="post">
                    <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                    <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
					<input type="hidden" id="contact" name = "contact" value="<?=$_POST['contact_no']?>"/>
                    <input title="Ticket View" type="submit" id="viewticket" name="viewticket" class="btn<?=$btncolor?>" value="Ticket View">
                    </form>
                    </div>
					<br></br>
				<?php					
					?>
					<div class="panel panel-info">
              		<div class="panel-heading" align="center">Job History</div>
				  	<div class="panel-body">                        
						<?php 
						if($_POST['contact_no']){ echo "Your searched criteria <strong>Contact No. :- </strong>".$_POST['contact_no'];}
							?>	
                      	<table class="table table-bordered" width="100%">
                    	<thead>
                        	<tr>
                            	<td><strong>S.No.</strong></td>
                                <td><strong>Job No.</strong></td>
                                <td><strong>Customer Name</strong></td>
								<td><strong>Customer Email</strong></td>
                                <td><strong>Location Name</strong></td>
                                <td><strong>Open Date</strong></td>
                                <td><strong>Model</strong></td>
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
                                <td><?=$row1['email'];?></td>
                                <td><?= getAnyDetails($row1['location_code'],"locationname","location_code","location_master",$link1);?></td>
                                <td><?=dt_format($row1['open_date']);?></td>
                                <td><?=$row1['model'];?></td>
                                <td><div align="center"><a href='job_view_newonly.php?refid=<?=$row1['job_no'];?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg faicon" title="view details"></i></a></div></td>
                            </tr>
						<?php
						}
                        ?>
                        </tbody>
                    </table>
			<?php
			}
			//////// (2)  if contact no exist in ticket master table/////////////////////////////////////////////////////////////////////
			else if (mysqli_num_rows($sql) >=1)
				{						
			?>
            <div class="panel panel-info">
              <div class="panel-heading" align="center">Ticket History</div>
              <div class="panel-body">
			  	<?php 					
					if($_POST['contact_no']){ echo "Your searched criteria <strong>Contact No. :- </strong>".$_POST['contact_no'];}
				?>
                	<table class="table table-bordered" width="100%">
                    	<thead>
                        	<tr>
                            	<td><strong>S.No.</strong></td>
                                <td><strong>Ticket No.</strong></td>
                                <td><strong>Customer Name</strong></td>
								<td><strong>Customer Email</strong></td>
                                <td><strong>Location Name</strong></td>
                                <td><strong>Open Date</strong></td>
                                <td><strong>Model</strong></td>
                                <td><strong>View</strong></td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
						$j = 1;
						while ($row = mysqli_fetch_array($sql)){
						?>
                        	<tr>
                            	<td><?=$j;?></td>
                                <td><?=$row['ticket_no'];?></td>
                                <td><?=$row['customer_name'];?></td>
                                <td><?=$row['email'];?></td>
                                <td><?= getAnyDetails($row['location_code'],"locationname","location_code","location_master",$link1);?></td>
                                <td><?=dt_format($row['open_date']);?></td>
                                <td><?=$row['model'];?></td>
                                <td><div align="center"><a href='#' title='view ticket details' onClick='viewTicketDetails("<?=base64_encode($row['ticket_no'])?>");'><i class='fa fa-eye fa-lg faicon' title='view ticket details'></i></a></div></td>
                            </tr>
						<?php
						}
                        ?>
                        </tbody>
                    </table>
                    <div align="center">
                    <form id="frm4" name="frm4" class="form-horizontal" action="job_ticket_history.php" method="post">
                    <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                    <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
					<input type="hidden" id="contact" name = "contact" value="<?=$_POST['contact_no']?>"/>
                    <input title="Ticket View" type="submit" id="viewticket" name="viewticket" class="btn<?=$btncolor?>" value="Ticket View">
                    </form>
                    </div>
					</div>
				<?php
						}
			////////////// (3) if contact no exist in jobsheet data table ///////////////////////////////////////////////////////////////////
				else if(mysqli_num_rows($sql1) >=1) {						
					?>
					<div class="panel panel-info">
              		<div class="panel-heading" align="center">Job History</div>
				  	<div class="panel-body">                        
						<?php 
						if($_POST['contact_no']){ echo "Your searched criteria <strong>Contact No. :- </strong>".$_POST['contact_no'];}
							?>	
                      	<table class="table table-bordered" width="100%">
                    	<thead>
                        	<tr>
                            	<td><strong>S.No.</strong></td>
                                <td><strong>Job No.</strong></td>
                                <td><strong>Customer Name</strong></td>
								<td><strong>Customer Email</strong></td>
                                <td><strong>Location Name</strong></td>
                                <td><strong>Open Date</strong></td>
                                <td><strong>Model</strong></td>
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
                                <td><?=$row1['email'];?></td>
                                <td><?= getAnyDetails($row1['location_code'],"locationname","location_code","location_master",$link1);?></td>
                                <td><?=dt_format($row1['open_date']);?></td>
                                <td><?=$row1['model'];?></td>
                                <td><div align="center"><a href='job_view_newonly.php?refid=<?=$row1['job_no'];?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg faicon" title="view details"></i></a></div></td>
                            </tr>
						<?php
						}
                        ?>
                        </tbody>
                    </table>
					<?php  }  
					////////// (4) if does not exist in any table ///////////////////////////////////////////////////////////// 
					else {					
					?>
					<div class="panel panel-info">
              		<div class="panel-heading" align="center">Search History</div>
				  	<div class="panel-body">  
					<?php 
					echo "<strong>Not found in database. Please create Ticket</strong>"
							?>
					<div align="center">
                    <input title="Make New Ticket" type="button" id="newticket" name="newticket" class="btn<?=$btncolor?>" value="Make New Ticket" onclick="location.href='job_ticket_make.php?contact_no=<?=base64_encode($_POST['contact_no'])?><?=$pagenav?>';">
                    </div>
					<?php  }  				
					?>					
                  <!-- Start Model Mapped Modal -->
                  <div class="modal modalTH fade" id="viewTicket" role="dialog">
                    <div class="modal-dialog modal-lg">
                    
                      <!-- Modal content-->
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                          <!--<h4 class="modal-title" align="center">Ticket Details</h4>-->
                        </div>
                        <div class="modal-body modal-bodyTH">
                         <!-- here dynamic task details will show -->
                        </div>
                        <div class="modal-footer">
                          <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!--close Model Mapped modal-->
              </div>
            </div>
       <?php } ?>
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