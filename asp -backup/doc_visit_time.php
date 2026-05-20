<?php
require_once("../includes/config.php");
$docid=$_REQUEST['job_no'];
$job_sql="SELECT * FROM jobsheet_data where job_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
?>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
   <!-- Include Date Picker -->
 <link rel="stylesheet" href="../css/datepicker.css">
 <script src="../js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 
 <script>

$(document).ready(function(){

        $("#frm1").validate();

    });
	




	$(document).ready(function () {
		$('#vistor_date').datepicker({
			format: "yyyy-mm-dd",
				startDate: "<?=$today?>",
			todayHighlight: true,
			autoclose: true,
		}).on('changeDate', function(ev){
    		//checkJobType();
			//getWarranty();
		})
	});







</script>    
<div class="panel panel-success table-responsive">
      <div class="panel-heading">Visit <strong><?=$docid?></strong></div>
      <div class="panel-body">
	 
       <table class="table table-bordered" width="100%">
       	 <tbody>
		 <tr>
                <td width="20%"><label class="control-label">Customer Name</label></td>
                <td width="30%"><?php echo $job_row['customer_name'];?></td>
				</tr>
				<tr>
                <td width="20%"><label class="control-label">Address</label></td>
                <td width="30%"><?php echo $job_row['address'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Contact No.</label><br/><span class="red_small">(For SMS Update)</span></td>
                <td><?php echo $job_row['contact_no'];?></td>
				</tr>
				<tr>
				 <tr>
             
                <td><label class="control-label">Pincode</label></td>
                <td><?php echo $job_row['pincode'];?></td>
              </tr>
                <td><label class="control-label">Alternate Contact No.</label></td>
                <td><?php echo $job_row['alternate_no'];?></td>
              </tr>
		 
		  <tr>
              <td width="25%"><strong>Visit Date <span class="red_small">*</span> </strong></td>
              <td width="75%"><input type="text" class="form-control required" name="vistor_date"  id="vistor_date" style="width:150px" required >
                     <div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div> </td>
            </tr>
            <tr>
              <td width="25%"><strong>Visit Time <span class="red_small">*</span> </strong></td>
              <td width="75%">   <input type="time" id="vistor_time" name="vistor_time" required class="form-control required" /> </td>
            </tr>
            <tr>
              <td width="25%"><strong>Approved By <span class="red_small">*</span></strong></td>
              <td width="75%"> <input type="text" name="app_by" required id="app_by" class="form-control required" /></td>
            </tr>
            <tr>
              <td width="25%"><strong>Remark</strong></td>
              <td width="75%">  <input type="text" name="remark" class="form-control" id="remark"/>  <input name="ref_no" id="ref_no" type="hidden" value="<?=$docid?>"/></td>
            </tr>      
         </tbody>
       </table>
      </div><!--close panel body-->
    </div><!--close panel-->
</body>

</html>