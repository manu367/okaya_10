<?php
require_once("../includes/config.php");
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
 <script type="text/javascript" src="../js/moment.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css"> 
 <script type="text/javascript" language="javascript" >
 $(document).ready(function(){
	$('input[name="daterange"]').daterangepicker({
		locale: {
			format: 'YYYY-MM-DD'
		}
	});
});
 
/////////// function to get city on the basis of state
 function get_citydiv(){
	  var name=$('#state').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{state:name},
		success:function(data){
	    $('#citydiv').html(data);
	    }
	  }); 
 }
 /////////// function to get city on the basis of state
 function get_location(){
	  var name=$('#locationcity').val();
	   var name1=$('#state').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{citynew:name, statenew:name1},
		success:function(data){
	    $('#location').html(data);
	    }
	  });
   
 }
</script>
<!-- Include Date Range Picker -->
 <script type="text/javascript" src="../js/daterangepicker.js"></script>
 <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
 <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-check"></i>Party Ledger</h2>
	  <br></br>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	  <div class="form-group">
         <div id= "dt_range" class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" />
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
            </div>
          </div>
	    </div><!--close form group-->
   
				
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label">Location</label>
            <div class="col-md-5" id="location">
             <select name="location_code" id="location_code" class="form-control" >
               <option value=''>--Please Select-</option>
                <?php
                $res_maploc = mysqli_query($link1,"select location_code from map_wh_location where wh_location='".$_SESSION['asc_code']."'"); 
                while($row_maploc = mysqli_fetch_assoc($res_maploc)){
					$locname = getAnyDetails($row_maploc['location_code'],"locationname","location_code","location_master",$link1);
					?>
                <option value="<?=$row_maploc['location_code']?>" <?php if($_REQUEST['location_code'] == $row_maploc['location_code']) { echo 'selected'; }?>><?=$locname." (".$row_maploc['location_code'].")"?></option>
                <?php } ?>
              
                </select>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5">
               <input name="Submit" type="submit" class="btn btn-success" value="GO"  title="Go!">  
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
        <?php if ($_REQUEST['Submit']){
		   ?>
           <div class="form-group">
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
            
               <a href="../excelReports/partyledger_excel.php?location=<?=$_REQUEST['location_code']?>&daterange=<?=$_REQUEST['daterange']?>" title="Export Party Ledger details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Party Ledger details in excel"></i></a>
             
            </div>
          </div>
	    </div><!--close form group-->
			<br><br/>	 
	<form class="form-horizontal" role="form" method="post">
	<div class="panel panel-info">
	<div class="panel-body">
      	<table class="table table-bordered" width="100%">
           <thead>
            <tr>
            <td><strong>S.No.</strong></td>
            <td><strong>Trasaction Details</strong></td>
             <td><strong>Trsacation Type</strong></td>
			<td><strong>Trasaction Date</strong></td>
             <td><strong>Amount Cr</strong></td>
             <td><strong>Amount Dr</strong></td>
             </tr>
             </thead>
			 <tbody>
                  <?php
				  $date_range = explode(" - ",$_REQUEST['daterange']);
				  if($_REQUEST['daterange'] != ""){
					$daterange = "entry_date  >= '".$date_range[0]."' and entry_date  <= '".$date_range[1]."'";
						}else{
						$daterange = "1";
					}
					if($_REQUEST['location_code'] != ""){
					$locationcode = "location_code = '".$_REQUEST['location_code']."' ";
						}else{
						$locationcode = "1";
					}
					
					///////////////////////////fetching data from ledger table//////////////////////////////////////////
				  $sql = mysqli_query($link1,"SELECT * FROM location_account_ledger where ".$locationcode." and   ".$daterange." ");
					$j = 1;
					while ($row = mysqli_fetch_array($sql)){
					//////////////////// get amount on basis of cr/dr ////////////////////////////`
						if ($row[crdr] == "CR" ) { 
						$cr_amt = $row["amount"];  $dr_amt = "0" ;}
						else { $dr_amt = $row["amount"];  $cr_amt = "0";  }
						?>
                        <tr>
                        <td><?=$j;?></td>
                        <td><?=$row['remark'];?></td>
                        <td><?=$row['transaction_type'];?></td>
                         <td><?=dt_format($row['entry_date']);?></td>
                         <td><?= $cr_amt;?></td>
                          <td><?=$dr_amt;?></td>                    
                            </tr>
						<?php
						$j++;
						$totalcr+=$cr_amt;
						$totaldr+=$dr_amt;
						$balance = $totalcr - $totaldr;//// calculate balance (credit - debit)
						if($balance<0){ $value = $balance."DR";} else {$value = $balance."CR"; } //// condition if balance is negative then its dr amount else cr amount
						}
                        ?>
						<tr>
						<td colspan="4" align="right">Cr/Dr Total Amt</td><td><?=$totalcr;?></td><td><?=$totaldr;?></td>
						</tr>
						<tr><td colspan="4" align="right">Balance</td><td colspan="2"><?=$value;?></td></tr>
                        </tbody>
			</table>
		</div>
	</div>
      <!--</div>-->
      </form>
	 <?php }?>  
    </div>    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>