<?php
require_once("../includes/config.php");

if($_POST[Submit]=='Update'){
	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg="";
	$model=$_REQUEST['model'];
	$count=count($model);
	$i=0;
 	$sql =mysqli_query($link1,"update canabil_master  set status= '0' where partcode='".$_REQUEST['model_m']."' ");
	//// check if query is not executed
	if (!$sql) {
	     $flag = false;
        $error_msg =  "Error details1: " . mysqli_error($link1) . ".";
    }
	while($i < $count){	
	if($model[$i]==''){
		$status='0';
	}else{
		$status='1';
		}
	
	// alrady exist

		if(mysqli_num_rows(mysqli_query($link1,"select mapped_partcode from canabil_master where partcode='".$_REQUEST['model_m']."' and mapped_partcode='$model[$i]'"))>0){	
		
	$insert = mysqli_query($link1,"update canabil_master set mapped_partcode='$model[$i]' where partcode='".$_REQUEST['model_m']."' ");
	//// check if query is not executed
	if (!$insert) {
	     $flag = false;
        $error_msg =  "Error details1: " . mysqli_error($link1) . ".";
    }	
		}else{
	$insert =	mysqli_query($link1,"insert into canabil_master set mapped_partcode='$model[$i]',partcode='".$_REQUEST['model_m']."',model='".$_REQUEST['model_name']."' , status = '$status'  ");
	//// check if query is not executed
	if (!$insert) {
	     $flag = false;
        $error_msg =  "Error details1: " . mysqli_error($link1) . ".";
    }
	
    }
	 
	$i++;		
		}
///// check query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "Mapping is successfully done.";
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again.".$error_msg;;
	} 
    mysqli_close($link1);
	///// move to parent page
  header("location:canibalize_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  exit;
	}

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
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script>
</script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-wrench"></i> Map Model  For Canibilization</h2>
	  <br /></br>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm" name="frm" class="form-horizontal" action="" method="post">
          <div class="table-responsive">
           <table id="myTable" class="table table-hover">
          <tr>
    	<td width="21%" height="30" align="center"><strong>Model</strong></td>
   		 <td width="77%"  align="center"><strong>Mapped Components</td>
  	</tr>
  	<tr>
    <td ><div align="right"><strong>
      <input name="model_m" type="hidden" id="model_m" value="<?=$_REQUEST['partcode']?>">
	  <input name="model_name" type="hidden" id="model_name" value="<?=$_REQUEST['model_id']?>">
	     <?php  
		 $rs2=mysqli_query($link1,"select model_id from partcode_master where partcode='".$_REQUEST['partcode']."' ");
			$row2=mysqli_fetch_array($rs2);
			echo getAnyDetails($row2['model_id'],"model","model_id","model_master",$link1);
			echo " (".$_REQUEST['partcode'].")"; ?> </strong></div></td>&nbsp;&nbsp;
    <td ><?php 
	 $circlequery="Select distinct(partcode),model_id,part_desc,status from partcode_master where  model_id Like '%".$row2['model_id']."%' and (part_category='SPARE') and status='1' ";
				$circleresult=mysqli_query($link1,$circlequery) or die(mysqli_error($link1));
				echo "<table border=0 cellpadding=2 cellspacing=0 class=hoverable>";
				$i=1;
				while($circlearr=mysqli_fetch_array($circleresult)){
				if($i%3==1){
			echo "<tr>";
			}
			
				$state_acc=mysqli_query($link1,"Select mapped_partcode  from canabil_master where mapped_partcode='".$circlearr['partcode']."' and partcode='".$_REQUEST['partcode']."' ")or die(mysqli_error($link1));
				$num=mysqli_num_rows($state_acc);
				echo "<td ><input type='checkbox' name='model[]' value='".$circlearr['partcode']."'";
				if($num > 0)echo "checked";
				echo "/>".$circlearr['part_desc']." (".$circlearr['partcode'].") </td>";
				if($i/3==0){
			echo "</tr>";
			}$i++;
				}
				echo "</table>";
				?></td></tr> 
     </table>   
	  <div align="center">
        <input name="Submit" type="submit"  class="btn<?=$btncolor?>" value="Update">&nbsp;
       <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='canibalize_master.php?op=edit<?=$pagenav?>'">
      </div>             
	  </div> 
        
   </form>

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

