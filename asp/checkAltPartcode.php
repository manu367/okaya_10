<?php
require_once("../includes/config.php");
$docid=$_REQUEST['partcode'];
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <title><?=siteTitle?></title>
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">

</head>
<body>
<div class="container-fluid">
   <div class="col-sm-12"> 
    <div class="panel panel-success table-responsive">
      <div class="panel-heading">Alternate Partcode <?=$docid?></div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
            <tr>
                <th width="25%">Alternate Partcode </th>
                <th width="75%">Partcode Name</th>
                 <th width="75%">Part Qty</th>
              </tr>
            </thead>
            <tbody>
            <?php
			$sql_model = "SELECT partcode,alter_partcode FROM `alt_part_map` where partcode='".$docid."' and status = '1' "; 
			$res_model = mysqli_query($link1,$sql_model)or die(mysqli_error($link1));
			$row_num=mysqli_num_rows($res_model);
			$i=0;
			if($row_num>0){
				while($row_model = mysqli_fetch_assoc($res_model)){
					$qty=mysqli_fetch_array(mysqli_query($link1,"select okqty from client_inventory where partcode='".$row_model['alter_partcode']."' and location_code='".$_SESSION['asc_code']."' "));
					
					?>
				<tr>
              <td><?=$row_model['alter_partcode'];?></td>
              <td><?=getAnyDetails($row_model['alter_partcode'],"part_name","partcode","partcode_master",$link1);?></td>
              <td><?=$qty['okqty'];?></td>
            </tr>	
				<?php 	}
                    $i++;
			}else{
			  ?>
            <tr>
              <td colspan="2">No Alternate Partcode map.</td>
            </tr>  
            <?php 
			}
			?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
 </div><!--close col-sm-9-->
</div><!--close container-fluid-->
<?php
include("../includes/connection_close.php");
?>
</body>
</html>