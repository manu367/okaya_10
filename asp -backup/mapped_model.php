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
      <div class="panel-heading">Mapped Model with partcode <?=$docid?></div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
            <tr>
                <th width="25%">Model Code </th>
                <th width="75%">Model Name</th>
              </tr>
            </thead>
            <tbody>
            <?php
			$sql_model = "SELECT model_id FROM partcode_master where partcode='".$docid."'"; 
			$res_model = mysqli_query($link1,$sql_model)or die(mysqli_error($link1));
			$row_model = mysqli_fetch_assoc($res_model);
			if($row_model['model_id']){
			$arr_model = explode(",",$row_model['model_id']);	
			for($i=0; $i<count($arr_model); $i++){
			?>
            <tr>
              <td><?=$arr_model[$i];?></td>
              <td><?=getAnyDetails($arr_model[$i],"model","model_id","model_master",$link1);?></td>
            </tr>
            <?php
			}
			}else{
			  ?>
            <tr>
              <td colspan="2">No model map.</td>
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