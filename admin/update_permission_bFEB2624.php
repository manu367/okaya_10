<?php 
// old
require_once("../includes/config.php");

@extract($_POST);

############# if form 2 is submitted #################

if($_POST['submitTab']){

	// Update Function Rights

	mysqli_query($link1,"update access_tab set status='0' where userid='".$_REQUEST['userid']."' ")or die(mysqli_error($link1));

		$rrr="report";

		$rep1=$_REQUEST[$rrr];

		$count=count($_REQUEST[$rrr]);

		$j=0;

		while($j < $count){

			 if($rep1[$j]==''){

				$newstatus=0;

			 }else{

				$newstatus=1;

			 }

			 // alrady exist

			 if(mysqli_num_rows(mysqli_query($link1,"select tabid from access_tab where userid='".$_REQUEST['userid']."' and tabid='".$rep1[$j]."'"))>0){

				mysqli_query($link1,"update access_tab set status='".$newstatus."' where userid='".$_REQUEST['userid']."' and tabid='".$rep1[$j]."'")or die(mysqli_error($link1));

			 }else{

				mysqli_query($link1,"insert into access_tab set userid='".$_REQUEST['userid']."' ,tabid='".$rep1[$j]."',status='".$newstatus."'")or die(mysqli_error($link1));

			 }

		   $j++;

		}

	// end Function Rights

}

else if($_POST['submitTab1']){
	/////// map city/state
	//$post_state = explode("~",$_POST['state_name']);
	$res_upd = mysqli_query($link1,"update access_region set status='' where userid='".$_REQUEST['userid']."'");
	$post_regiondata = $_POST['states1'];
	$count_region = count($post_regiondata);
	$i=0;
	while($i < $count_region){
		if($post_regiondata[$i]==''){
			$newstatus = "";
		}else{
			$newstatus = "Y";
		}
		// alrady exist
		if(mysqli_num_rows(mysqli_query($link1,"select id from access_region where userid='".$_REQUEST['userid']."' and stateid='".$post_regiondata[$i]."' "))>0){
			$res_mapupd = mysqli_query($link1,"update access_region set status='".$newstatus."' where userid='".$_REQUEST['userid']."' and stateid='".$post_regiondata[$i]."' ");
		}else{
			$res_mapupd = mysqli_query($link1,"insert into access_region set userid='".$_REQUEST['userid']."',stateid='".$post_regiondata[$i]."',status='".$newstatus."'");

		}
		$i++;
	}

}else if($_POST['submitTab2']){

	$res_upd = mysqli_query($link1,"update access_brand set status='' where location_code='".$_REQUEST['userid']."'");

	$postmapdata=$_POST['mapbrand'];

	$count=count($postmapdata);

	$j=0;

	while($j < $count){

		if($postmapdata[$j]==''){

			$newstatus = "";

		}else{

			$newstatus = "Y";

		}

		// alrady exist

		if(mysqli_num_rows(mysqli_query($link1,"select id from access_brand where location_code='".$_REQUEST['userid']."' and brand_id='".$postmapdata[$j]."'"))>0){

			$res_mapupd = mysqli_query($link1,"update access_brand set status='".$newstatus."' where location_code='".$_REQUEST['userid']."' and brand_id='".$postmapdata[$j]."'");

		}else{

			$res_mapupd = mysqli_query($link1,"insert into access_brand set location_code='".$_REQUEST['userid']."', brand_id='".$postmapdata[$j]."', status='".$newstatus."'");

		}

		$j++;

	}//// close while loop
///////////////////////////////////////////Access Product
	
}else if($_POST['submitTab3']){
	
	//print_r($_POST);
	//exit;
//echo "update  access_product set status='' where where location_code='".$_REQUEST['userid']."'";
	$res_upd = mysqli_query($link1,"update  access_product set status='' where  location_code='".$_REQUEST['userid']."'") or die(mysqli_error());

	 $postmapdata=$_POST['mapproduct'];

	$count=count($postmapdata);

	$jk=0;

	while($jk < $count){

		if($postmapdata[$jk]==''){

			$newstatus = "";

		}else{

			$newstatus = "Y";

		}

		// alrady exist

		if(mysqli_num_rows(mysqli_query($link1,"select id from access_product where location_code='".$_REQUEST['userid']."' and product_id='".$postmapdata[$jk]."'"))>0){

			$res_mapupd = mysqli_query($link1,"update access_product set status='".$newstatus."' where location_code='".$_REQUEST['userid']."' and product_id='".$postmapdata[$jk]."'");

		}else{
	//	echo "insert into access_product set location_code='".$_REQUEST['userid']."', product_id='".$postmapdata[$jk]."', status='".$newstatus."'";

			$res_mapupd = mysqli_query($link1,"insert into access_product set location_code='".$_REQUEST['userid']."', product_id='".$postmapdata[$jk]."', status='".$newstatus."'");

		}

		$jk++;

	}//// close while loop

	
}else{

}

?>



<!DOCTYPE html>

<html>

<head>

 <meta charset="utf-8">

 <meta name="viewport" content="width=device-width, initial-scale=1">

 <title><?=siteTitle?></title>

 <script src="../js/jquery.js"></script>

 <link href="../css/font-awesome.min.css" rel="stylesheet">

 <link href="../css/abc.css" rel="stylesheet">

 <script src="../js/bootstrap.min.js"></script>

 <link href="../css/abc2.css" rel="stylesheet">

 <link rel="stylesheet" href="../css/bootstrap.min.css">

 <script>

 function checkAll(field){

   for (i = 0; i < field.length; i++)

        field[i].checked = true ;

 }

 function uncheckAll(field){

   for (i = 0; i < field.length; i++)

        field[i].checked = false ;

 }

 ///// multiple check all function

 function checkFunc(field,ind,val){

	 //alert(field+"--"+ind+"--"+val);

	var chk=document.getElementById(val+""+ind).checked;

	if(chk==true){ checkAll(field); }

	else{ uncheckAll(field);}

 }

</script>

<script>

$(document).ready(function() {

    if (location.hash) {

        $("a[href='" + location.hash + "']").tab("show");

    }

    $(document.body).on("click", "a[data-toggle]", function(event) {

        location.hash = this.getAttribute("href");

    });

});

$(window).on("popstate", function() {

    var anchor = location.hash || $("a[data-toggle='tab']").first().attr("href");

    $("a[href='" + anchor + "']").tab("show");

	if(location.hash=="#menu1"){

		document.getElementById("home").style.display="none";

		document.getElementById("menu1").style.display="";

		document.getElementById("menu2").style.display="none";

		//document.getElementById("menu3").style.display="none";

		//document.getElementById("menu4").style.display="none";

		//document.getElementById("menu5").style.display="none";

	}

	else if(location.hash=="#menu2"){

		document.getElementById("home").style.display="none";

		document.getElementById("menu1").style.display="none";

		document.getElementById("menu2").style.display="";

		//document.getElementById("menu3").style.display="none";

		//document.getElementById("menu4").style.display="none";

		//document.getElementById("menu5").style.display="none";

	}

	/*else if(location.hash=="#menu3"){

		document.getElementById("home").style.display="none";

		document.getElementById("menu1").style.display="none";

		document.getElementById("menu2").style.display="none";

		document.getElementById("menu3").style.display="";

		document.getElementById("menu4").style.display="none";

		document.getElementById("menu5").style.display="none";

	}

	else if(location.hash=="#menu4"){

		document.getElementById("home").style.display="none";

		document.getElementById("menu1").style.display="none";

		document.getElementById("menu2").style.display="none";

		document.getElementById("menu3").style.display="none";

		document.getElementById("menu4").style.display="";

		document.getElementById("menu5").style.display="none";

	}

	else if(location.hash=="#menu5"){

		document.getElementById("home").style.display="none";

		document.getElementById("menu1").style.display="none";

		document.getElementById("menu2").style.display="none";

		document.getElementById("menu3").style.display="none";

		document.getElementById("menu4").style.display="none";

		document.getElementById("menu5").style.display="";

	}*/

	else{

		document.getElementById("home").style.display="";

		document.getElementById("menu1").style.display="none";

		document.getElementById("menu2").style.display="none";

		//document.getElementById("menu3").style.display="none";

		//document.getElementById("menu4").style.display="none";

		//document.getElementById("menu5").style.display="none";

	}

});

////// get city on the basis of state selection

function getCity(stateid){

	var state_id = stateid.split("~");

	  $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{permission_state:state_id[0],usrid:'<?=$_REQUEST['userid']?>'},

		success:function(data){

	    	//$('#disp_city').html(data);

		}

	  });

}

</script>

</head>

<body>

<div class="container-fluid">

  <div class="row content">

	<?php 

    include("../includes/leftnav2.php");

    ?>

    <div class="<?=$screenwidth?>">

      <h2 align="center"><i class="fa fa-users"></i> Update User Permission</h2>

      <h4 align="center"><?=$_REQUEST['u_name']."  (".$_REQUEST['userid'].")";?>

      <?php if($_POST['submitTab']=='Save' || $_POST['submitTab1']=='Save' || $_POST['submitTab2']=='Save'){ ?>

      <br/>

     <span style="color:#FF0000"><?php if($_POST['submitTab']=="Save"){ echo "Master/Reports Tab";}else if($_POST['submitTab1']=="Save"){echo "Region Tab";} else if($_POST['submitTab2']=="Save"){echo "Brand Tab";}?> permissions are updated.</span>

      <?php } ?>

      </h4>

      <div class="form-group"  id="page-wrap" style="margin-left:10px;">

         <ul class="nav nav-tabs">

          <li class="active"><a data-toggle="tab" href="#home">Masters / Reports</a></li>

          <li><a data-toggle="tab" href="#menu1">Region</a></li>
          <li><a data-toggle="tab" href="#menu2">Brand</a></li>

          <li><a data-toggle="tab" href="#menu3">Product</a></li>
		   <li><a data-toggle="tab" href="#menu4">Operation</a></li>

         </ul>

         <div class="tab-content">

         <!-- Tab 1 Master / Region Rights-->

           <div id="home" class="tab-pane fade in active">

          <form id="frm" name="frm" class="form-horizontal" action="" method="post">

          <div class="table-responsive">

                <table id="myTable1" class="table table-hover">

                <?php 

				$rs=mysqli_query($link1,"select maintabname from tab_master where status='1' and tabfor='admin' group by maintabname order by maintabname");

                $num=mysqli_num_rows($rs);

                if($num > 0){

                   $j=1;

                   while($row=mysqli_fetch_array($rs)){

                ?>

                <thead>

                  <tr>

                    <th style="border:none">&nbsp;<?=$row['maintabname']?>&nbsp;<input style="width:20px"  type="checkbox" id="funcTB<?=$j?>" name="funcTB[]" onClick="checkFunc(document.frm.report<?=$j?>,'<?=$j?>','funcTB');"/> </th>

                  </tr>

                </thead>

                <tbody>

                 <?php 

				   $i=1;

				   $report="select tabid, subtabname from tab_master where maintabname='".$row['maintabname']."' and status='1' and tabfor='admin' order by subtabname";

                   $rs_report=mysqli_query($link1,$report) or die(mysqli_error($link1));

                   while($row_report=mysqli_fetch_array($rs_report)){

                       if($i%4==1){?>

                  <tr>

                  <?php

                       }

                    $state_acc=mysqli_query($link1,"select tabid from access_tab where status='1' and tabid='".$row_report['tabid']."' and userid='".$_REQUEST['userid']."'")or die(mysqli_error());

                    $num1=mysqli_num_rows($state_acc);?>

                    <td><input style="width:20px"  type="checkbox" id="report<?=$j?>" name="report[]" value="<?=$row_report['tabid']?>" <?php if($num1 > 0) echo "checked";?> /><?=$row_report['subtabname']?></td>

                  <?php if($i/4==0){?>

                  </tr>

                  <?php 

                        }

						$i++;

                    }////// Close 2nd While Loop of TAB 2

                    $j++;

				   }  

				}?>

                </tbody>

                </table>

                </div>

            <div class="form-buttons" align="center">

              <input type="submit" class="btn<?=$btncolor?>" name="submitTab" id="submitTab" value="Save"> &nbsp;

              <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu1'">Next</button>

              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo $_REQUEST['userid'];?><?=$pagenav?>'">

            </div>

          </form>

      </div>

	<!-- Tab 2 Region Rights-->

          <div id="menu1" class="tab-pane fade" >

          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">

          <div class="table-responsive"> 

          	<?php /*?><table id="myTable2" class="table table-hover"> 

                <thead>

                  <tr>

                    <td style="border:none"> 

                    	<strong>State:</strong>
						<select name="state_name" id="state_name" class="form-control" style="width:250px;" onChange="getCity(this.value);">

                        	<option value="">--Select State--</option>

                             <?php 

							$rs2=mysqli_query($link1,"SELECT * FROM state_master ORDER BY state");

                			while($row=mysqli_fetch_array($rs2)){

                			?>

                            <option value="<?=$row['stateid']."~".$row['zoneid']?>"><?=$row['state']?></option>

                            <?php

							}

							?>

                    	</select>

                    </td>
					
                  </tr>
                </thead>

               </table><?php */?> 
			   
			   <table class='table table-hover'>
				  <tbody>
					  <tr>
						  <td>
							<input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm1.states1)" value="Check All" />&nbsp;&nbsp;
							<input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm1.states1)" value="Uncheck All" />
						  </td>
					  </tr>
					  <?php 
						$ad=1;
						$rs2=mysqli_query($link1,"SELECT * FROM state_master ORDER BY state");
						while($row=mysqli_fetch_array($rs2)){
						
						$rs2_1 = mysqli_fetch_array(mysqli_query($link1,"SELECT status FROM access_region where stateid='".$row['stateid']."' and userid='".$_REQUEST['userid']."' "));						
					  ?>
						  <tr>
							<td>
								<input style="width:20px"  type="checkbox" id="states1" name="states1[]" value="<?=$row['stateid'];?>" <?php if($rs2_1['status']=="Y"){ echo "checked"; } ?> >&nbsp;&nbsp;<?=$row['state'];?>
							</td>
						  </tr>
					  <?php
						}
					  ?>
				  </tbody>
			  </table>

               <span id="disp_city"></span>

                </div>

            <div class="form-buttons" align="center">

              <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#home'">Previous</button>

              <input type="submit" class="btn btn-primary" name="submitTab1" id="submitTab1" value="Save"> 

              <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu2'">Next</button>

              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo $_REQUEST['userid'];?><?=$pagenav?>'">

            </div>

          </form>

          </div>
          
          <!-- Tab 2 Region Rights-->

          <div id="menu2" class="tab-pane fade" >
                    
           <form  name="frm2" id="frm2" class="form-horizontal" action="" method="post">
           

				<table width="100%" id="brandmap" class="table table-bordered table-hover">

                	<tbody>

                    <?php

					$rs=mysqli_query($link1,"select brand_id,brand from brand_master where status='1' order by brand");

					$num=mysqli_num_rows($rs);

					if($num > 0){

                   		$j=1;

                   		while($row=mysqli_fetch_array($rs)){

							if($j%4==1){

					?>

                    	<tr>

                           <?php

                       		}

							///// check if any mapping entry with Y status is there 

							$res_map = mysqli_query($link1,"select id from access_brand where location_code='".$_REQUEST['userid']."' and brand_id='".$row['brand_id']."' and status='Y'")or die(mysqli_error());

                    		$num_map = mysqli_fetch_assoc($res_map);

							?>

                          <td><input style="width:20px"  type="checkbox" id="mapbrand" name="mapbrand[]" value="<?=$row['brand_id']?>" <?php if($num_map > 0){ echo "checked";}?>/>&nbsp;<?=$row['brand']?></td>

                           <?php 

						  	if($j/4==0){

							?>

                        </tr>

                    <?php

						  }

						$j++;

						}

					}

					?>    

                    </tbody>
				</table>

            <div class="form-buttons" align="center">

              <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#menu1'">Previous</button>

              <input type="submit" class="btn btn-primary" name="submitTab2" id="submitTab2" value="Save"> 

              <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu3'">Next</button>

              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo $_REQUEST['userid'];?><?=$pagenav?>'">

            </div>
             

          </form>

          </div>
		  
		            
          <!-- Tab 2 Product Rights-->

          <div id="menu3" class="tab-pane fade" >
          <div class="form-buttons" style="float:right">

                <input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm4.mapproduct)" value="Check All" />

                <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm4.mapproduct)" value="Uncheck All" /></div>


           <form  name="frm4" id="frm4" class="form-horizontal" action="" method="post">

				<table width="100%" id="brandmap" class="table table-bordered table-hover">

                	<tbody>

                    <?php

					$rs=mysqli_query($link1,"select product_id,product_name from product_master where status='1' order by product_name");

					$num=mysqli_num_rows($rs);

					if($num > 0){

                   		$j=1;

                   		while($row=mysqli_fetch_array($rs)){

							if($j%4==1){

					?>

                    	<tr>

                           <?php

                       		}

							///// check if any mapping entry with Y status is there 

							$res_map = mysqli_query($link1,"select id from access_product where location_code='".$_REQUEST['userid']."' and product_id='".$row['product_id']."' and status='Y'")or die(mysqli_error());

                    		$num_map = mysqli_fetch_assoc($res_map);

							?>

                          <td><input style="width:20px"  type="checkbox" id="mapproduct" name="mapproduct[]" value="<?=$row['product_id']?>" <?php if($num_map > 0){ echo "checked";}?>/>&nbsp;<?=$row['product_name']?></td>

                           <?php 

						  	if($j/4==0){

							?>

                        </tr>

                    <?php

						  }

						$j++;

						}

					}

					?>    

                    </tbody>
				</table>

            <div class="form-buttons" align="center">

              <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#menu2'">Previous</button>

              <input type="submit" class="btn btn-primary" name="submitTab3" id="submitTab3" value="Save"> 

              <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu4'">Next</button>

              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo $_REQUEST['userid'];?><?=$pagenav?>'">

            </div>
             

          </form>

          </div>

		<!-- Tab 4 Excel Export Rights-->

          <div id="menu4" class="tab-pane fade">

          <form id="frm3" name="frm3" class="form-horizontal" action="" method="post">

            <div class="table-responsive"> 

              <div class="form-buttons" style="float:right">

                <input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm3.report2)" value="Check All" />

                <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm3.report2)" value="Uncheck All" /></div>

                 <table id="myTable3" class="table table-hover">

                    <thead>

                      <tr>

                        <th style="border:none">&nbsp;</th>

                      </tr>

                    </thead>

                    <tbody>

                      <tr>

                        <td><input style="width:20px" type="checkbox" id="report2" name="report2[]" value="<?=$row_report['id']?>" <?php if($num > 0) echo "checked";?> />

                    <?=$row_report['subtabname']?></td>

                      </tr>

                    </tbody>

              	</table>

              </div>

            <div class="form-buttons" align="center">

             <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#menu1'">Previous</button>

             <input type="submit" class="btn btn-primary" name="submitTab2" id="submitTab2" value="Save"> 

             <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu3'">Next</button>

             <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo $_REQUEST['userid'];?><?=$pagenav?>'">

             </div>

          </form>

          </div>

          </div>

    </div>

    </div>

  </div>

</div>

<?php

include("../includes/footer.php");

include("../includes/connection_close.php");

?>

</body>

</html>