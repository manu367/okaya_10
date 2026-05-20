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
 <script>	 function getmappincode(){
	
	  var pincode=$('#Serch').val();
	//alert(pincode);
		 var partcode=$('#partcode_srch').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{pin_asc_serch:pincode,partcode:partcode},
		success:function(data){
		//alert(data);
	    $('#disp_pincode').html(data);
	    }
	  });
	
	}; </script>
</head>
<body>
<div class="container-fluid">
   <div class="col-sm-12"> 
    <div class="panel panel-success table-responsive">
      <div class="panel-heading"> Partcode <?=$docid?></div>
      <div class="panel-body">
	    <table class="table table-bordered" width="100%">
		<tr><td>Search By Pincode</td><td> <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
	   
	   <input name="Serch" type="text" class="form-control" id="Serch" onKeyup="getmappincode(this.value);" required> <input name="partcode_srch" type="hidden"  value="<?=$docid?>"  id="partcode_srch" title="partcode"></form></td></tr>
		</table>
		<span id="disp_pincode">
       <table class="table table-bordered" width="100%">
	   
	   
            <thead><?php $part_info = explode("~",getAnyDetails($docid,"part_name,model_id","partcode","partcode_master",$link1))?>
		<tr><td colspan="4">Part Name:-<?=$part_info[0]?></td></tr>
            <tr>
                <th width="29%">Location Name</th>
                <th width="27%">Location Code</th>
				 <th width="29%">City</th>
				  <th width="15%">Stock</th>
              </tr>
            </thead>
            <tbody>
            <?php  
		
		
			$sql_model = "SELECT * FROM client_inventory where partcode='".$docid."'";
		
			$res_model = mysqli_query($link1,$sql_model)or die(mysqli_error($link1));
			while($row_model = mysqli_fetch_assoc($res_model)){
			
				
			
			$location_info = explode("~",getAnyDetails($row_model['location_code'],"locationname,cityid","location_code","location_master",$link1));
			$city_loc = getAnyDetails($location_info[1],"city","cityid","city_master",$link1);
			?>
            <tr>
              <td><?=$location_info[0];?></td>
              <td><?=$row_model['location_code']?></td>
			   <td><?=$city_loc?></td>
			    <td><?=$row_model['okqty']?></td>
            </tr>
            <?php
			}
			?>
				
            </tbody>
          </table></span>
      </div><!--close panel body-->
    </div><!--close panel-->
 </div><!--close col-sm-9-->
</div><!--close container-fluid-->
<?php
include("../includes/connection_close.php");
?>
</body>
</html>