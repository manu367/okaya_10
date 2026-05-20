<?php
require_once("../includes/config.php");
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
$(document).ready(function(){
    $('#myTable').dataTable();
});
</script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-bullseye"></i> Target </h2>
     <?php if($_REQUEST['msg']!=''){?>
      	<h4 align="center">
        	<span 
			<?php if($_REQUEST['sts']=="success"){ echo "class='info-success' style='color: #090;'"; } if($_REQUEST['sts']=="fail"){ echo "class='info-fail' style='color:#FF0033'";} else echo "class='info-fail' style='color:#FF0033'";?>>
			<?php echo $_REQUEST['msg'];?>
			</span>
        </h4>
	  <?php }?>
      <form class="form-horizontal" role="form">
        <div style="display:inline-block;float:right">
			<?php /*?><button title="Upload Target" type="button" class="btn <?=$btncolor?>" style="float:right;"margin-left:10px; onClick="window.location.href='target_upload.php?<?=$pagenav?>'"><i class="fa fa-upload fa-lg" ></i> &nbsp; <span>Upload Target </span></button><?php */?>
			
			 <button title="Add Target" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='target_add.php?<?=$pagenav?>'"><i class="fa fa-plus-circle fa-lg" ></i> &nbsp; <span>Add Target </span></button>
		</div>
		
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th style="text-align:center;" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
			  <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Target No.</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Emp Name</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Month</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Year</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Type</th>
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Status</th>
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>View</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			
			$sql=mysqli_query($link1,"Select * from sf_target_master where 1 order by id desc");
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
			?>
            <tr class="even pointer">
              <td style="text-align:center;" ><?php echo $sno;?></td>
			  <td style="text-align:center;" ><?php echo $row['target_no']; ?></td>
              <td><?php echo getAnyDetails($row['emp_id'],'name','username','admin_users',$link1)." | ".$row['emp_id']; ?></td>
              <td style="text-align:center;" >
			  	<?php 
					if($row['month'] == '01'){ echo "JAN"; }
					else if($row['month'] == '02'){ echo "FEB"; }
					else if($row['month'] == '03'){ echo "MAR"; }
					else if($row['month'] == '04'){ echo "APR"; }
					else if($row['month'] == '05'){ echo "MAY"; }
					else if($row['month'] == '06'){ echo "JUN"; }
					else if($row['month'] == '07'){ echo "JUL"; }
					else if($row['month'] == '08'){ echo "AUG"; }
					else if($row['month'] == '09'){ echo "SEP"; }
					else if($row['month'] == '10'){ echo "OCT"; }
					else if($row['month'] == '11'){ echo "NOV"; }
					else if($row['month'] == '12'){ echo "DEC"; }
					else{}
				?>
              </td>
              <td style="text-align:center;" ><?php echo $row['year']; ?></td>
              <td><?php echo $row['target_type']; ?></td>
              <td style="text-align:center;" ><?php echo $row['status']; ?></td>
              <td align="center"><a href='target_view.php?id=<?php echo base64_encode($row['id']);?><?=$pagenav?>'  title='View'><i class="fa fa-eye fa-lg" title="View"></i></a></td>
            </tr>
            <?php }?>
          </tbody>
          </table>
      </div>
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