<?php
require_once("../includes/config.php");

function accessasc($brd,$area,$link1){

$rowsql=mysqli_query($link1,"SELECT *  FROM access_brand  where brand_id='".$brd."' and area='".$area."'");

if(mysqli_num_rows($rowsql)>0){

$rowcount=mysqli_num_rows($rowsql);


}else{
$rowcount=0;

}
return $rowcount;
}


?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/jquery.js"></script>
 <script src="../js/bootstrap.min.js"></script>
 <script type="text/javascript" src="../js/moment.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <!-- datatable plugin-->
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script src="../high/js/highcharts.js"></script>
<script src="../high/js/modules/data.js"></script>
<script src="../high/js/modules/drilldown.js"></script>
<script src="../high/js/highcharts-3d.js"></script>
<script src="../high/js/modules/exporting.js"></script>
<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
 <!-- datatable plugin-->
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <!--  -->



</head>
<body>
<!-- Include Date Range Picker -->
 <script type="text/javascript" src="../js/daterangepicker.js"></script>
 <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
<div class="container-fluid">
  <div class="row content">
  
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-ticket"></i> Brand ASP Mapping</h2>
     
  <table class="table table-bordered" width="50%">
  
<tr> <td ><label class="control-label">Brand</td><td ><label class="control-label">North</td><td ><label class="control-label">West</td><td><label class="control-label">South</td><td ><label class="control-label">East</td><td><label class="control-label">CENTRAL</td></tr>

<?php $res_brd = mysqli_query($link1,"SELECT * FROM brand_master where status='1'");
				while($row_brd = mysqli_fetch_assoc($res_brd)){?>
				
				<tr><td><?=$row_brd['brand']?></td><td><?php echo $accnoth= accessasc($row_brd['brand_id'],"NORTH",$link1)?></td><td><?php echo $accnoth= accessasc($row_brd['brand_id'],"WEST",$link1)?></td><td><?php echo $accnoth= accessasc($row_brd['brand_id'],"SOUTH",$link1)?></td><td><?php echo $accnoth= accessasc($row_brd['brand_id'],"EAST",$link1)?></td><td><?php echo $accnoth= accessasc($row_brd['brand_id'],"CENTRAL",$link1)?></td></tr> <?php }?></table>
        
    </div>
    
  </div>
   </div>


<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>