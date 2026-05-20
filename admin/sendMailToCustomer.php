<?php
require_once("../includes/config.php");
///$accessState=getAccessState($_SESSION['userid'],$link1);
////$accessLocation=getAccessLocation($_SESSION['userid'],$link1);
include("customer_mail.php");
////// final submit form ////
@extract($_POST);
if($_POST['save']=='Send Mail'){
     
	if(count($parentloc)>0){  
		$msgsent="Mail Sent";
		$count=count($parentloc);
		$i=0;
		while($i < $count){
			$cust_details=explode("~",$parentloc[$i]);
            //if($cust_details[1]==''){
           // }else{
				$content1="Hi $cust_details[2],<br><br>$mail_text<br><br>Regards,<br>CANSALE";
 				///send_mail_function($content1,$cust_details[1],"support@candoursoft.com",$mail_subject);
				//dailyActivity($_SESSION['userid'],$cust_details[0],"Mail",$msgsent,$ip,$link1,$flag);
		   	//}
			$i++;
		}
			$cflag = "success";
			$cmsg = "Success";
	  $msg="You have successfully sent Mail to selected customers";
	  ///// move to parent page   
	}
        else {
			$cflag = "danger";
			$cmsg = "Failed";
	    $msg="Please checked atleast one customer to send Mail";
	  }

         header("Location:customer_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
	  exit;
}

############################ Filters value apply to refine loctaion ids to mapped
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
      <h2 align="center"><i class="fa fa-envelope-o"></i> e-Mail To Customer</h2><br><br>
         
      <form  name="form1" id="form1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-12" align="right"><input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.form1.list)" value="Check All" />&nbsp;
                                  <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.form1.list)" value="Uncheck All" />
            </div>
          </div>
          <div class="panel-group">
           <div class="panel panel-info table-responsive">
             <div class="panel-heading">Locations Name</div>
               <div class="panel-body">
          <?php
				   $docid=base64_decode($_REQUEST['refid']);
				   $srch_criteria = "where customer_id = '".$docid."'";
         $sql_locations="select  *  from customer_master ".$srch_criteria."   order by id desc";
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
              <td><input type="checkbox" name="parentloc[]" id="list" value="<?=$row_locations['customer_id']."~".$row_locations['email']."~".$row_locations['customer_name']?>"/>&nbsp;<?=$row_locations['customer_name']." | ".$row_locations['email']." | ".$row_locations['mobile']."(".$row_locations['customer_id'].")";?></td>
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
            <div class="col-md-10"><label class="col-md-4 control-label">Mail Subject</label>
              <div class="col-md-6">
              	<input type="text" name="mail_subject" id="mail_subject" class="form-control required mastername" required>             
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Mail Content</label>
              <div class="col-md-6">
				<textarea name="mail_text" id="mail_text" class="form-control required" required style="height:150px;resize:vertical"></textarea>  
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($hide=='NO'){ ?>
                 <input type="submit" class="btn btn-primary" name="save" id="save" value="Send Mail">&nbsp;
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
