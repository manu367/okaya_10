<?php
include("includes/config.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title><?=siteTitle?></title>
  <meta charset="utf-8">
  <link rel="shortcut icon" href="../img/titleimg.png" type="image/png">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="js/jquery.js"></script>
 <link href="css/font-awesome.min.css" rel="stylesheet">
 <link href="css/abc.css" rel="stylesheet">
 <link rel="stylesheet" href="css/bootstrap.min.css">
 <script src="js/bootstrap.min.js"></script>
 <link href="css/abc2.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("includes/leftnavemp2.php");
    ?>
    <div class="col-sm-9">
    	<div class="row">
        	<div class="btn-group-lg col-sm-12">
              	<button type="button" class="btn btn-danger" style="width:100px;">CL(0)</button>
                <button type="button" class="btn btn-primary" style="width:100px;">DL(0)</button>
                <button type="button" class="btn btn-info" style="width:100px;">SDL(0)</button>
                <button type="button" class="btn<?=$btncolor?>" style="width:100px;">QT<span style="font-size:12px">1</span>(0)</button>
                <button type="button" class="btn<?=$btncolor?>" style="width:100px;">QT<span style="font-size:12px">n</span>(0)</button>
                <button type="button" class="btn btn-warning" style="width:100px;">AT(0)</button>
            </div>
        </div>
        <div class="row">
        	<div class="col-sm-12">
              	&nbsp;
            </div>
        </div>
        <div class="row">
        	<div class="panel-group col-sm-6">
              <div class="panel panel-info class">
                  <div class="panel-heading">Task Status</div>
                  <div class="panel-body">Ongoing Task</div>
                  <div class="panel-body">Pending Task</div>
                  <div class="panel-body">Completed Task</div>
              </div>
            </div>
            <div class="col-sm-6">
              <table class="table">
                <thead>
                    <tr>
                        <th colspan="3" style="text-align:center">Performance</th>
                    </tr>
                    <tr>
                        <th width="29%">&nbsp;</th>
                        <th width="33%">Task</th>
                        <th width="38%">Efficiency</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Today</th>
                        <td>0</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <th>MTD</th>
                        <td>0</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <th>YTD</th>
                        <td>0</td>
                        <td>0</td>
                    </tr>
                </tbody>
            </table>
            </div>            
        </div>
        <div class="row">
            <div class="col-sm-12">
                &nbsp;
            </div>
        </div>
        <div class="row">
            <div class="panel-group col-sm-12">
              <div class="panel panel-default">
                  <div class="panel-heading">Daily Efficiency Meter</div>
                  <div class="panel-body">
                     <div class="progress">
                        <div class="progress-bar progress-bar-success" role="progressbar" style="width:40%">
                          Working Time
                        </div>
                        <div class="progress-bar progress-bar-warning" role="progressbar" style="width:10%">
                          Free Time
                        </div>
                        <div class="progress-bar progress-bar-danger" role="progressbar" style="width:20%">
                          Allowed/Extra Time
                        </div>
                      </div>
                  </div>
              </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                &nbsp;
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table class="table">
                <thead>
                    <tr>
                        <th colspan="3" style="text-align:center">My Today Schedule</th>
                    </tr>
                    <tr>
                        <th width="29%">From</th>
                        <th width="33%">To</th>
                        <th width="38%">Task</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>9:00 AM</td>
                        <td>10:00 AM</td>
                        <td>Follow up for payment</td>
                    </tr>
                    <tr>
                        <td>10:00 AM</td>
                        <td>04:00 PM</td>
                        <td>Cyclic Task- call to customer</td>
                    </tr>
                    <tr>
                        <td>04:00 PM</td>
                        <td>05:00 PM</td>
                        <td>Management Meeting</td>
                    </tr>
                    <tr>
                        <td>05:00 PM</td>
                        <td>06:00 PM</td>
                        <td>Report to manager</td>
                    </tr>
                </tbody>
            </table>
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
