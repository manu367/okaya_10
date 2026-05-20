<?php
require_once("../includes/config.php");
$res_locdet=mysqli_query($link1,"SELECT * FROM location_master where location_code='".$_SESSION['asc_code']."'")or die(mysqli_error($link1));
$row_locdet=mysqli_fetch_array($res_locdet);
$citystate = explode("~",getAnyDetails($row_locdet['cityid'],"city,state","cityid","city_master",$link1));
?>
<!DOCTYPE html>
<html>
 <head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title>
 <?=siteTitle?>
 </title>
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <body>
 <div class="container-fluid">
   <div class="row content">
    <?php 
 		include("../includes/leftnavemp2.php");
    ?>
     <div class="<?=$screenwidth?>">
     	    <div class="col-sm-4 well">
              <div class="well">
                <p>My Profile</p>
                <i class="fa fa-id-card fa-4x" aria-hidden="true"></i>
              </div>
              <div class="well">
                <p>General Information</p>
                <p class="alert-success"><span class="pull-left"><strong>Location Name </strong></span> <span class="pull-right"><?=$row_locdet['locationname']?></span></p>
                <p class="alert-danger"><span class="pull-left"><strong>Location Type </strong></span> <span class="pull-right"><?=$row_locdet['locationtype']?></span></p>
                <p class="alert-warning"><span class="pull-left"><strong>Location City </strong></span> <span class="pull-right"><?=$citystate[0]?></span></p>
                <p class="alert-info"><span class="pull-left"><strong>Location State </strong></span> <span class="pull-right"><?=$citystate[1]?></span></p>
              </div>
            </div>
       <div class="col-sm-8">
    
         <div class="row">
                <div class="col-sm-12">
                  <div class="panel panel-default text-left">
                    <div class="panel-body">
                      <p align="center"><h2 align="center"><?=$row_locdet['locationname']?></h2></p>
                    </div>
                  </div>
                </div>
         </div>
         <table class="table table-bordered" width="100%" style="font-size:13px">
           <tbody>
             <tr>
               <td width="20%"><label class="control-label">Contact Person</label></td>
               <td width="30%"><?=$row_locdet['contact_person']?></td>
               <td width="20%"><label class="control-label">Email</label></td>
               <td width="30%"><?=$row_locdet['emailid']?></td>
             </tr>
             <tr>
               <td><label class="control-label">Phone Number1</label></td>
               <td><?php echo $row_locdet['contactno1'];?></td>
               <td><label class="control-label">Phone Number2</label></td>
               <td><?php echo $row_locdet['contactno2'];?></td>
             </tr>
             <tr>
               <td><label class="control-label">Firm Type</label></td>
               <td><?php echo $row_locdet['partner_type'];?></td>
               <td><label class="control-label">Helpline No</label></td>
               <td><?php echo $row_locdet['landlineno'];?></td>
             </tr>
             <tr>
               <td><label class="control-label">PAN No.</label></td>
               <td><?php echo $row_locdet['panno'];?></td>
               <td><label class="control-label">GST No.</label></td>
               <td><?php echo $row_locdet['gstno'];?></td>
             </tr>
             <tr>
               <td><label class="control-label">Other Tax Reg. No.</label></td>
               <td><?php echo $row_locdet['oth_taxr_no'];?></td>
               <td><label class="control-label">Other Tax Name</label></td>
               <td><?php echo $row_locdet['oth_tax_name'];?></td>
             </tr>
             <tr>
               <td><label class="control-label">ERP/SAP Code</label></td>
               <td><?php echo $row_locdet['erpid'];?></td>
               <td><label class="control-label">Other Code</label></td>
               <td><?php echo $row_locdet['othid'];?></td>
             </tr>
             <tr>
               <td><label class="control-label">Status</label></td>
               <td><?php if($row_locdet['statusid']==1){ echo "Active";}else{ echo "Deactive";}?></td>
               <td><label class="control-label">Country</label></td>
               <td><?php echo getAnyDetails($row_locdet['countryid'],"countryname","countryid","country_master",$link1);?></td>
             </tr>
             <tr>
               <td><label class="control-label">Address</label></td>
               <td colspan="3"><?php echo $row_locdet['locationaddress'];?></td>
             </tr>
           </tbody>
         </table>
              
         </div>     
     </div>
   	 <!--End col-sm-9--> 
   </div>
   <!--End row content-->
 </div>
 <!--End container fluid-->
 <?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>