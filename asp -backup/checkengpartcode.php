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
      <div class="panel-heading"> Partcode <?=$docid.":-".getAnyDetails($docid,"part_name","partcode","partcode_master",$link1)?></div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
            <tr>
                <th width="24%">Engineer Name </th>
                <th width="37%">OKQTY</th>
                <th width="37%">FAULTY</th>
               
              </tr>
            </thead>
            <tbody>
            <?php
			$sql_model = "SELECT * FROM `user_inventory` where partcode='".$docid."' and location_code = '".$_SESSION['asc_code']."' "; 
			$res_model = mysqli_query($link1,$sql_model)or die(mysqli_error($link1));
			$row_num=mysqli_num_rows($res_model);
			$i=0;
	
				while($row_model = mysqli_fetch_assoc($res_model)){
					
					
					?>
				<tr>
              <td><?=getAnyDetails($row_model['locationuser_code'],"locusername","userloginid","locationuser_master",$link1);?></td>
             
              <td><?=$row_model['okqty'];?></td>
              <td><?=$row_model['faulty'];?></td>
            </tr>	
				<?php 	
                  }?>
		
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