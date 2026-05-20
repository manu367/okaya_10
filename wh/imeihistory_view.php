<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
	$todayDate=date("Y-m-d");
	$todayTime=date("H:i:s");
//// job details
 $job_sql="SELECT * FROM imei_history where 	transaction_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);

////// final submit form ////

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
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
<div class="row content">
<?php 
    include("../includes/leftnavemp2.php");
    ?>
   <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-cubes"></i>TAG/<?php echo SERIALNO ?></h2>
	  <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;&nbsp;History</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
                  <tr>
				  <td width="5%"><strong>SNO</strong></td>				  
                    <td width="15%"><strong>TAG/<?php echo SERIALNO ?></strong></td>
                  
					 <td width="10%"><strong>Transcation No.</strong></td>
					 <td width="10%"><strong>Location Name</strong></td>
                    <td width="15%" ><strong>Partcode</strong></td>
					<td width="10%"><strong>Remark</strong></td> 
					<td width="10%"><strong>Update Date</strong></td>             
              
                  </tr>
                </thead>
                <tbody>
                <?php
				$i=1;
				while($row_jobhistory=mysqli_fetch_array($job_res)){
				?>
                  <tr>
				  <td><?=$i?></td>
                    <td><?=$row_jobhistory['imei1']?></td>
                 
					 <td><?=$row_jobhistory['transaction_no']?></td>
					 <td ><?=getAnyDetails($row_jobhistory['location_code'],"locationname","location_code","location_master",$link1);?></td> 
                    <td ><?=getAnyDetails($row_jobhistory['partcode'],"part_desc","partcode","partcode_master",$link1);?></td>                
					<td><?=$row_jobhistory['remark']?></td>
                    <td><?=$row_jobhistory['updatedate']?></td>
                                  
                  </tr>
                  <?php
				  $i++;
				}
				  ?>
            </tbody>
          </table>		  
      </div><!--close panel body-->
	  <div align="center">
	  <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='whimei_rpeort.php?<?=$pagenav?>'">
	  </div>
    </div><!--close panel-->

  </div><!--close panel group-->
  
	</form>
 </div><!--close col-sm-9-->
</div><!--close container-fluid-->
<?php
include("../includes/connection_close.php");
?>
</body>
</html>