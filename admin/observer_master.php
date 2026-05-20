<?php
require_once("../includes/config.php");
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" />
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/jquery.js"></script>
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">

 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>

    <style>
        .dataTables_paginate{
            display:block !important;
        }

        .badge-A{background:#27ae60;}
        .badge-D{background:#c0392b;}
    </style>
<title><?=siteTitle?></title>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <!-- Datatable plugin JS file -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-user-circle-o"></i> Primary Observer</h2>
      <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">

	    <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label"> Status</label>
			<div class="col-md-5" align="left">
			   <select name="status" id="status" class="form-control"  onChange="document.form1.submit();">
                   <option value="ALL">All</option>
                   <option value="A" <?=($status=="A"?"selected":"")?>>Active</option>
                   <option value="D" <?=($status=="D"?"selected":"")?>>Deactive</option>
                </select>
            </div>
          </div>
		  <div class="col-md-6">
			<div class="col-md-5" align="left">
            </div>
          </div>
	    </div><!--close form group-->
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>
			<div class="col-md-5" align="left">
               <?php
			    //// get excel process id ////
				//$processid=getExlCnclProcessid("Admin Users",$link1);
			    ////// check this user have right to export the excel report
			    //if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
			   ?>
               <a href="excelexport.php?rname=<?=base64_encode("primary_observer_file")?>&rheader=<?=base64_encode("primary_observer_file")?>&status=<?=$_GET['status']?>" title="Export model details in excel">
                   <i class="fa fa-file-excel-o fa-2x faicon" title="Export model details in excel"></i>
               </a>
               <?php
				//}
				?>
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
      <form class="form-horizontal" role="form">

        <button title="Add Observer" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='edit_observation.php?op=Add<?=$pagenav?>'"><span>Add Primary Observer</span></button> <br><br>

		<button title="Add d New VOC" type="button" class="btn<?=$btncolor?>" style="float:right;display: none;" onClick="window.location.href='voc_uploader.php?op=Add<?=$pagenav?>'"><span>Add New VOC By Uploader</span></button>&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;

        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="example" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
                <th>S.ID</th>
                <th>Observation</th>
                <th>Status</th>
                <th>Edit</th>
            </tr>
          </thead>
       </table>
        </div>
      <!--</div>-->
      </form>
    </div>

  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<script>
    $(document).ready(function () {
        $('#example').DataTable({
            processing: true,
            serverSide: true,
            order: [[0, "desc"]],
            ajax: {
                url: "../pagination/observation-grid-data.php",
                type: "POST",
                data: {
                    status: "<?=$_GET['status']?>"
                }
            }
        });
    });
</script>
<script>
    function twopointerAlgorithm(arr,target){
        let left=0;
        let right =arr.length-1;
        while(left<right){
            let check =arr[left]+arr[right];
            if(check===target)return [arr[left],arr[right]];
            else if (check < target) left++;
            else right--;
        }
        return [-1,-1];
    }
    const a=[1,2,3,4,5,6,7,8,8];
    const [b,c]=twopointerAlgorithm(a,3);
    const [d,e]=twopointerAlgorithm(a,5)
    console.log(b);
    console.log(c);
    console.log(d,e);
    function maxSubarraySum(arr, k) {
        let windowSum = 0;
        let maxSum = -Infinity;
        for (let i = 0; i < k; i++) {
            windowSum += arr[i];
        }
        maxSum = windowSum;
        for (let i = k; i < arr.length; i++) {
            windowSum += arr[i];        // add next element
            windowSum -= arr[i - k];    // remove left element
            maxSum = Math.max(maxSum, windowSum);
        }
        return maxSum;
    }
    console.log(maxSubarraySum([2,1,5,1,3,2], 3));
</script>
</body>
</html>