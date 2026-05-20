<?php
require_once("../includes/config.php");
///$accessState=getAccessState($_SESSION['userid'],$link1);
////$accessLocation=getAccessLocation($_SESSION['userid'],$link1);
////// final submit form ////
@extract($_POST);
   if($_POST['save']=='Send SMS'){
      $count=count($parentloc);
	  if($count >0) {
	  $i=0;
      while($i < $count){
		  $cust_details=explode("~",$parentloc[$i]);
           if($cust_details[0]==''){
             
           }else{
			  $msgsent="SMS Sent";
			  if($cust_details[1]!='' && $sms!=""){
				  $res = file_get_contents("http://sms.foxxglove.com/api/mt/SendSMS?user=cancrm&password=123456&senderid=CANCRM&channel=Trans&DCS=0&flashsms=0&number=".$cust_details[1]."&text=".urlencode($sms)); 
				  //print_r($res);
             dailyActivity($_SESSION['userid'],$parentloc[$i],"SMS",$msgsent,$ip,$link1,$flag);
		   	} 
		   }
           $i++;	
	  }///close while loop
	  $msg="You have successfully sent SMS to selected customers";
	 }
	 else {
	   $msg="Please checked atleast one customer to send sms";
	 
	  }
      ////// insert in activity table////
	  ////// return message
	  
	  ///// move to parent page
       header("Location:customer_list.php?msg=".$msg."".$pagenav);
	   exit;
   }

############################ Filters value apply to refine loctaion ids to mapped
if($_REQUEST[state]!=''){ $locstate="state='$_REQUEST[state]'" ; } else{ $locstate="state in ($accessState)";} 
if($_REQUEST[city]!=''){ $loccity="city='$_REQUEST[city]'" ; } else{ $loccity="1";}
//if($_REQUEST[loctype]!=''){ $loctype="id_type='$_REQUEST[loctype]'" ; } else{ $loctype="1";}
if($_REQUEST[location]!='' && $_REQUEST[location]!='all'){ $loccod="mapplocation='$_REQUEST[location]'" ; } else{ $loccod="mapplocation in(select asc_code from asc_master where ".$loccity." and ".$locstate." and asc_code in (".$accessLocation."))";}
?> 
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
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
$(document).ready(function(){
	$("#form1").validate();
});
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-mobile"></i> SMS To Customer</h2>
         
      <form  name="form1" id="form1" class="form-horizontal" action="" method="post">
			
          <div class="form-group">
            <div class="col-md-12" align="right"><input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.form1.list)" value="Check All" />&nbsp;
                                  <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.form1.list)" value="Uncheck All" />
            </div>
          </div>
          <div class="panel-group">
           <div class="panel panel-default table-responsive">
             <div class="panel-heading">Locations Name</div>
               <div class="panel-body">
          <?php
         $sql_locations="Select distinct(customer_id) as customerid,customer_name as customername,stateid,cityid,mobile from customer_master where  mobile!='' order by stateid, customer_name";
          $loccount=mysqli_query($link1,$sql_locations) or die(mysqli_error($link1));
		  ?>
          <table id="myTable" class="table table-hover" border="0">
           <tbody>
          <?php
          if(mysqli_num_rows($loccount) > 0){
             $hide='NO';
             $i=1;
             while($row_locations=mysqli_fetch_array($loccount)){
               if($i%3==1){
		  ?>
            <tr>
          <?php  } 
		  
		  ?>	
              <td><input type="checkbox" name="parentloc[]" id="list" value="<?=$row_locations['customerid']."~".$row_locations['mobile']."~".$row_locations['customername']?>"/>&nbsp;<?=$row_locations['customername']." | ".$row_locations['stateid']." | ".$row_locations['cityid']."(".$row_locations['customerid'].")";?></td>
              <?php 
			
			  if($i/3==0){
			  ?>
            </tr>
              <?php
              }
		     $i++;
			 }//close while loop
			 }//close row check if
          else{
            echo "<br/>";
			echo "<div align='center' class='red_small'>No Record Found !!</div>";
			echo "<br/>";
			$hide='YES';
		  }
		  ?>
          </tbody>
          </table>
          </div><!--close panel body-->
         </div><!--close panel-->
         </div><!--close panel group-->
         <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Message Content</label>
              <div class="col-md-6">
				<textarea name="sms" id="sms" class="form-control required" required style="height:150px;resize:vertical"></textarea>  
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($hide=='NO'){ ?>
                 <input type="submit" class="btn btn-primary" name="save" id="save" value="Send SMS">&nbsp;
              <?php } ?>   
                 <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='customer_list.php?<?=$pagenav?>'">
            </div>
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
