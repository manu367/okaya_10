<?php
require_once("../includes/config.php");
//$arrstatus = getFullStatus("process",$link1);
$docid=base64_decode($_REQUEST['refid']);
//// job details
$job_sql="SELECT * FROM jobsheet_data where job_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
$doa_sql="SELECT * FROM doa_data where job_no='".$docid."'";
$doa_res=mysqli_query($link1,$doa_sql);
$doa_row=mysqli_fetch_assoc($doa_res);

@extract($_POST);
////// if we hit process button
if ($_POST) {
    if ($_POST['update'] == 'Update') {
		mysqli_autocommit($link1, false);
            $flag = true;
            $err_msg = "";
			/////update jobsheet data  table
				if($status=='51' || $status=='54' || $status=='55'){
					//print_r();
					// Btr process
					$sql_btr="select id from initial_btr_data where job_no='".$docid."'";
					$result_btr=mysqli_query($link1,$sql_btr);
					$sql_btr_f="select id from final_btr_data where job_no='".$docid."'";
					$result_btr_f=mysqli_query($link1,$sql_btr_f);
					if((mysqli_num_rows($result_btr)>0)){
					 $btr_upd="UPDATE initial_btr_data set c1='".$_POST['c1_befor']."',c2='".$_POST['c2_befor']."',c3='".$_POST['c3_befor']."',c4='".$_POST['c4_befor']."',c5='".$_POST['c5_befor']."',c6='".$_POST['c6_befor']."',ocv='".$_POST['ocv_befor']."',eng_id='' where job_no='".$docid."'";	
				 $res_updt=mysqli_query($link1,$btr_upd);
					 if (!$res_updt) {
					 $flag = false;
					 $error_msg = "Error before Btr details: " . mysqli_error($link1) . ".";
				   }	
					}
					if((mysqli_num_rows($result_btr_f)>0)){
					$btr_upd_f="UPDATE final_btr_data set c1='".$_POST['c1_aftr']."',c2='".$_POST['c2_aftr']."',c3='".$_POST['c3_aftr']."',c4='".$_POST['c4_aftr']."',c5='".$_POST['c5_aftr']."',c6='".$_POST['c6_aftr']."',toc='".$_POST['toc']."',charging_hour='".$_POST['tot_chrg_hr']."',backup_load='".$bkp_load."',backup_time='".$_POST['backup_time']."',eng_id='',dischrg_current='".$_POST['dischrg_current']."',test_result='".$_POST['test_result']."',load_in_watt='".$_POST['load_in_watt']."',cut_off_volt='".$_POST['c_off_volt']."',pcv1='".$_POST['pcv1']."',pcv2='".$_POST['pcv2']."',pcv3='".$_POST['pcv3']."',pcv4='".$_POST['pcv4']."',pcv5='".$_POST['pcv5']."',pcv6='".$_POST['pcv6']."',pcv7='".$_POST['pcv7']."',invt_load_tst_reslt='".$_POST['invt_load_tst_reslt']."',ip='".$ip."',ocv='".$_POST['ocv_aftr']."',use_load='".$use_load."',eng_detection='".$eng_detection."' where job_no='".$docid."'";
					$res_updt_f=mysqli_query($link1,$btr_upd_f);
					if (!$res_updt_f) {
				    $flag = false;
				    $error_msg = "Error After Btr details: " . mysqli_error($link1) . ".";
			       }	
					}
					
					if($status=='51'){
					//$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set warranty_status='".$warranty_status."',sub_status  = '".$status."' ,doa_remark = '".$remark."',doa_approval= 'Y',doa_ar_by='".$_SESSION['userid']."',doa_ar_dt='".$today."'  where job_no='".$docid."' ");
						$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set warranty_status='".$warranty_status."',status='51' ,doa_remark = '".$remark."',doa_approval= 'Y',doa_ar_by='".$_SESSION['userid']."',doa_ar_dt='".$today."'  where job_no='".$docid."' ");
				$flag = callHistory($docid,$job_row['location_code'],$status,"Request Approved","Request Approved",$_SESSION['userid'],"",$remark,"","",$ip,$link1,$flag);
					}
					if($status=='54'){
					$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set warranty_status='".$warranty_status."',status='54' ,doa_remark = '".$remark."',doa_approval= 'T',doa_ar_by='".$_SESSION['userid']."',doa_ar_dt='".$today."'  where job_no='".$docid."' ");
				$flag = callHistory($docid,$job_row['location_code'],$status,"Request Approved(Tested Ok)","Request Approved",$_SESSION['userid'],"",$remark,"","",$ip,$link1,$flag);
					}
					if($status=='55'){
					$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set warranty_status='".$warranty_status."',status='55' ,doa_remark = '".$remark."',doa_approval= 'G',doa_ar_by='".$_SESSION['userid']."',doa_ar_dt='".$today."'  where job_no='".$docid."' ");
				$flag = callHistory($docid,$job_row['location_code'],$status,"Request Approved(Goodwill Approve)","Request Approved",$_SESSION['userid'],"",$remark,"","",$ip,$link1,$flag);
					}
					
				}
					if($status=='53'){
						
					// Btr process
					$sql_btr="select id from initial_btr_data where job_no='".$docid."'";
					$result_btr=mysqli_query($link1,$sql_btr);
					$sql_btr_f="select id from final_btr_data where job_no='".$docid."'";
					$result_btr_f=mysqli_query($link1,$sql_btr_f);
					if((mysqli_num_rows($result_btr)>0)){
					 $btr_upd="UPDATE initial_btr_data set c1='".$_POST['c1_befor']."',c2='".$_POST['c2_befor']."',c3='".$_POST['c3_befor']."',c4='".$_POST['c4_befor']."',c5='".$_POST['c5_befor']."',c6='".$_POST['c6_befor']."',ocv='".$_POST['ocv_befor']."',eng_id='' where job_no='".$docid."'";	
				 $res_updt=mysqli_query($link1,$btr_upd);
					 if (!$res_updt) {
					 $flag = false;
					 $error_msg = "Error before Btr details: " . mysqli_error($link1) . ".";
				   }	
					}
					if((mysqli_num_rows($result_btr_f)>0)){
					$btr_upd_f="UPDATE final_btr_data set c1='".$_POST['c1_aftr']."',c2='".$_POST['c2_aftr']."',c3='".$_POST['c3_aftr']."',c4='".$_POST['c4_aftr']."',c5='".$_POST['c5_aftr']."',c6='".$_POST['c6_aftr']."',toc='".$_POST['toc']."',charging_hour='".$_POST['tot_chrg_hr']."',backup_load='".$bkp_load."',backup_time='".$_POST['backup_time']."',eng_id='',dischrg_current='".$_POST['dischrg_current']."',test_result='".$_POST['test_result']."',load_in_watt='".$_POST['load_in_watt']."',cut_off_volt='".$_POST['c_off_volt']."',pcv1='".$_POST['pcv1']."',pcv2='".$_POST['pcv2']."',pcv3='".$_POST['pcv3']."',pcv4='".$_POST['pcv4']."',pcv5='".$_POST['pcv5']."',pcv6='".$_POST['pcv6']."',pcv7='".$_POST['pcv7']."',invt_load_tst_reslt='".$_POST['invt_load_tst_reslt']."',ip='".$ip."',ocv='".$_POST['ocv_aftr']."',use_load='".$use_load."',eng_detection='".$eng_detection."' where job_no='".$docid."'";
					$res_updt_f=mysqli_query($link1,$btr_upd_f);
					if (!$res_updt_f) {
				    $flag = false;
				    $error_msg = "Error After Btr details: " . mysqli_error($link1) . ".";
			       }	
					}	
						
						
					$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set sub_status  = '".$status."' ,doa_remark = '".$remark."',doa_approval= 'H',doa_ar_by='".$_SESSION['userid']."',doa_ar_dt='".$today."'  where job_no='".$docid."' ");
				$flag = callHistory($docid,$job_row['location_code'],$status,"Request For Brand Approval","Request For Brand Approval",$_SESSION['userid'],"",$remark,"","",$ip,$link1,$flag);
				}
				
				if($status=='52'){
					
					
					// Btr process
					$sql_btr="select id from initial_btr_data where job_no='".$docid."'";
					$result_btr=mysqli_query($link1,$sql_btr);
					$sql_btr_f="select id from final_btr_data where job_no='".$docid."'";
					$result_btr_f=mysqli_query($link1,$sql_btr_f);
					if((mysqli_num_rows($result_btr)>0)){
					 $btr_upd="UPDATE initial_btr_data set c1='".$_POST['c1_befor']."',c2='".$_POST['c2_befor']."',c3='".$_POST['c3_befor']."',c4='".$_POST['c4_befor']."',c5='".$_POST['c5_befor']."',c6='".$_POST['c6_befor']."',ocv='".$_POST['ocv_befor']."',eng_id='' where job_no='".$docid."'";	
				 $res_updt=mysqli_query($link1,$btr_upd);
					 if (!$res_updt) {
					 $flag = false;
					 $error_msg = "Error before Btr details: " . mysqli_error($link1) . ".";
				   }	
					}
					if((mysqli_num_rows($result_btr_f)>0)){
					$btr_upd_f="UPDATE final_btr_data set c1='".$_POST['c1_aftr']."',c2='".$_POST['c2_aftr']."',c3='".$_POST['c3_aftr']."',c4='".$_POST['c4_aftr']."',c5='".$_POST['c5_aftr']."',c6='".$_POST['c6_aftr']."',toc='".$_POST['toc']."',charging_hour='".$_POST['tot_chrg_hr']."',backup_load='".$bkp_load."',backup_time='".$_POST['backup_time']."',eng_id='',dischrg_current='".$_POST['dischrg_current']."',test_result='".$_POST['test_result']."',load_in_watt='".$_POST['load_in_watt']."',cut_off_volt='".$_POST['c_off_volt']."',pcv1='".$_POST['pcv1']."',pcv2='".$_POST['pcv2']."',pcv3='".$_POST['pcv3']."',pcv4='".$_POST['pcv4']."',pcv5='".$_POST['pcv5']."',pcv6='".$_POST['pcv6']."',pcv7='".$_POST['pcv7']."',invt_load_tst_reslt='".$_POST['invt_load_tst_reslt']."',ip='".$ip."',ocv='".$_POST['ocv_aftr']."',use_load='".$use_load."',eng_detection='".$eng_detection."' where job_no='".$docid."'";
					$res_updt_f=mysqli_query($link1,$btr_upd_f);
					if (!$res_updt_f) {
				    $flag = false;
				    $error_msg = "Error After Btr details: " . mysqli_error($link1) . ".";
			       }	
					}
					
					
					$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set status  = '1',sub_status  = '1' ,doa_remark = '".$remark."',doa_approval= 'N' ,doa_ar_by='".$_SESSION['userid']."',doa_ar_dt='".$today."' where job_no='".$docid."' ");
				$flag = callHistory($docid,$job_row['location_code'],$status,"Request Reject - call Re-open","Request Reject",$_SESSION['userid'],"",$remark,"","",$ip,$link1,$flag);
					}
			/// check if query is execute or not//
					if(!$jobsheet_upd){
						$flag = false;
						$err_msg = "Error1". mysqli_error($link1) . ".";
					}	
	
		///////////////////////// entry in call history table ///////////////////////////////////////	
		
				
					
		////////////////////////////////////////////////////////////////////////////////////////////////////////////
		////// insert in activity table////
       $flag = dailyActivity($_SESSION['userid'], $docid, $status,$remark,$_SERVER['REMOTE_ADDR'], $link1, $flag);
		
		  ///// check both master and data query are successfully executed
                    if ($flag) {
                        mysqli_commit($link1);
                        $msg = "Successfully done with ref. no. " . $docid;
						$cflag = "success";
						$cmsg = "Success";
                    } else {
                        mysqli_rollback($link1);
                        $msg = "Request could not be processed " . $err_msg . ". Please try again.";
						$cflag = "danger";
						$cmsg = "Failed";
                    }
                    mysqli_close($link1);
					 ///// move to parent page
        header("location:job_list_repl.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
       exit;
	}	               
}

?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<script>
function bigImg(x) {
  x.style.height = "300px";
  x.style.width = "300px";
}

function normalImg(x) {
  x.style.height = "100px";
  x.style.width = "100px";
}
	
	
	
	  
$(document).ready(function () {	 
	
document.getElementById('ocv_befor').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 2) {
        this.value = value.slice(0, 2) + '.' + value.slice(2, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('ocv_befor').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('c1_befor').focus();
    }
});
document.getElementById('c1_befor').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 1) {
        this.value = value.slice(0, 1) + '.' + value.slice(1, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('c1_befor').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('c2_befor').focus();
    }
});
document.getElementById('c2_befor').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 1) {
        this.value = value.slice(0, 1) + '.' + value.slice(1, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('c2_befor').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('c3_befor').focus();
    }
});
document.getElementById('c3_befor').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 1) {
        this.value = value.slice(0, 1) + '.' + value.slice(1, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('c3_befor').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('c4_befor').focus();
    }
});
document.getElementById('c4_befor').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 1) {
        this.value = value.slice(0, 1) + '.' + value.slice(1, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('c4_befor').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('c5_befor').focus();
    }
});
document.getElementById('c5_befor').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 1) {
        this.value = value.slice(0, 1) + '.' + value.slice(1, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('c5_befor').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('c6_befor').focus();
    }
});
document.getElementById('c6_befor').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 1) {
        this.value = value.slice(0, 1) + '.' + value.slice(1, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('c6_befor').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('tot_chrg_hr1').focus();
    }
});	
document.getElementById('tot_chrg_hr1').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 2) {
        this.value = value.slice(0, 2) + '.' + value.slice(2, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('tot_chrg_hr1').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('toc').focus();
    }
});
document.getElementById('toc').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 2) {
        this.value = value.slice(0, 2) + '.' + value.slice(2, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('toc').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('ocv_aftr').focus();
    }
});
document.getElementById('ocv_aftr').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 2) {
        this.value = value.slice(0, 2) + '.' + value.slice(2, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('ocv_aftr').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('c1_aftr').focus();
    }
});
document.getElementById('c1_aftr').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 1) {
        this.value = value.slice(0, 1) + '.' + value.slice(1, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('c1_aftr').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('c2_aftr').focus();
    }
});
document.getElementById('c2_aftr').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 1) {
        this.value = value.slice(0, 1) + '.' + value.slice(1, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('c2_aftr').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('c3_aftr').focus();
    }
});
document.getElementById('c3_aftr').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 1) {
        this.value = value.slice(0, 1) + '.' + value.slice(1, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('c3_aftr').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('c4_aftr').focus();
    }
});
document.getElementById('c4_aftr').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 1) {
        this.value = value.slice(0, 1) + '.' + value.slice(1, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('c4_aftr').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('c5_aftr').focus();
    }
});
document.getElementById('c5_aftr').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 1) {
        this.value = value.slice(0, 1) + '.' + value.slice(1, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('c5_aftr').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('c6_aftr').focus();
    }
});
document.getElementById('c6_aftr').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 1) {
        this.value = value.slice(0, 1) + '.' + value.slice(1, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('c6_aftr').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('dischrg_current').focus();
    }
});	
document.getElementById('dischrg_current').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 2) {
        this.value = value.slice(0, 2) + '.' + value.slice(2, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('dischrg_current').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('backup_time').focus();
    }
});
document.getElementById('backup_time').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 2) {
        this.value = value.slice(0, 2) + ':' + value.slice(2, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('backup_time').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('test_result').focus();
    }
});
document.getElementById('test_result').addEventListener('input', function () {
    if (this.value.length === 2) {
        document.getElementById('load_in_watt').focus();
    }
});
document.getElementById('load_in_watt').addEventListener('input', function () {
    if (this.value.length === 3) {
        document.getElementById('c_off_volt').focus();
    }
});
document.getElementById('c_off_volt').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 2) {
        this.value = value.slice(0, 2) + '.' + value.slice(2, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('c_off_volt').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('pcv1').focus();
    }
});
document.getElementById('pcv1').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 1) {
        this.value = value.slice(0, 1) + '.' + value.slice(1, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('pcv1').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('pcv2').focus();
    }
});
document.getElementById('pcv2').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 1) {
        this.value = value.slice(0, 1) + '.' + value.slice(1, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('pcv2').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('pcv3').focus();
    }
});
document.getElementById('pcv3').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 1) {
        this.value = value.slice(0, 1) + '.' + value.slice(1, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('pcv3').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('pcv4').focus();
    }
});	
document.getElementById('pcv4').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 1) {
        this.value = value.slice(0, 1) + '.' + value.slice(1, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('pcv4').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('pcv5').focus();
    }
});
document.getElementById('pcv5').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 1) {
        this.value = value.slice(0, 1) + '.' + value.slice(1, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('pcv5').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('pcv6').focus();
    }
});
document.getElementById('pcv6').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 1) {
        this.value = value.slice(0, 1) + '.' + value.slice(1, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('pcv6').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('pcv7').focus();
    }
});
document.getElementById('pcv7').addEventListener('input', function (e) {
    let value = this.value.replace(/[^0-9]/g, ''); // Only numbers
    if (value.length > 1) {
        this.value = value.slice(0, 1) + '.' + value.slice(1, 4); // e.g., 1234 → 12.34
    } else {
        this.value = value;
    }
});
document.getElementById('pcv7').addEventListener('input', function () {
    if (this.value.length === 5) {
        document.getElementById('invt_load_tst_reslt1').focus();
    }
});	
});	  	
</script>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
  	$cust_det = explode("~",getAnyDetails($job_row['customer_id'],"customer_id,landmark,email,phone,dob_date,mrg_date,alt_mobile ","customer_id","customer_master",$link1));
	$product_det = mysqli_fetch_assoc(mysqli_query($link1,"SELECT * FROM product_registered  where job_no='".$job_row['job_no']."'"));
    ?>
   <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-ticket"></i> Complaint Details</h2>
      <h4 align="center">Job No.- <?=$docid?></h4>
	  <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
      <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Customer Name</label></td>
                <td width="30%"><?php echo $job_row['customer_name'];?></td>
                <td width="20%"><label class="control-label">Address</label></td>
                <td width="30%"><?php echo $job_row['address'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Contact No.</label><br/><span class="red_small">(For SMS Update)</span></td>
                <td><?php echo $job_row['contact_no'];?></td>
                <td><label class="control-label">Alternate Contact No.</label></td>
                <td><?php echo $cust_det[6];?></td>
              </tr>
              <tr>
                <td><label class="control-label">State</label></td>
                <td><?php echo getAnyDetails($job_row["state_id"],"state","stateid","state_master",$link1);?></td>
                <td><label class="control-label">Email</label></td>
                <td><?php echo $cust_det[2];?></td>
              </tr>
              <tr>
                <td><label class="control-label">City</label></td>
                <td><?php echo getAnyDetails($job_row["city_id"],"city","cityid","city_master",$link1);?></td>
                <td><label class="control-label">Pincode</label></td>
                <td><?php echo $job_row['pincode'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Customer Category</label></td>
                <td><?php echo $job_row['customer_type'];?></td>
                <td><label class="control-label">Residence No</label></td>
                <td><?php echo $cust_det[3];?></td>
              </tr>
			   <tr>
                <td><label class="control-label">Landmarks</label></td>
                <td><?php echo $cust_det[1];?></td>
                <td><label class="control-label"></label></td>
                <td><?php ?></td>
              </tr>
			  	   <tr>
                <td><label class="control-label">Date Of Birth</label></td>
                <td><?php echo $cust_det[4];?></td>
                <td><label class="control-label">Marriage Date</label></td>
                <td><?php  echo $cust_det[5]; ?></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
   
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Observation</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
        
                <tr>
              <td width="26%"><label class="control-label">Assign Location</label></td>
              <td  colspan="3"><?php echo getAnyDetails($job_row["current_location"],"locationname","location_code","location_master",$link1);?></td>
           
            </tr>
            <tr>
              <td><label class="control-label">VOC</label></td>
              <td><?php echo getAnyDetails($job_row["cust_problem"],"voc_desc","voc_code","voc_master",$link1);?></td>
              <td><?php 	$voc= explode(",",$job_row['cust_problem2']); 
			           $vocpresent   = count($voc);
					   if($vocpresent == '1'){
					   $name = getAnyDetails($voc[0],"voc_desc","voc_code","voc_master",$link1 );
					   }
					   else if($vocpresent >1){
					     $name ='';
					   for($i=0 ; $i<$vocpresent; $i++){					 
			 			$name.=  getAnyDetails($voc[$i],"voc_desc","voc_code","voc_master",$link1 ).",";
			 			}} echo $name;?></td>
              <td><?=$job_row['cust_problem3']?></td>
            </tr>
            <tr>
              <td><label class="control-label">Remark </label></td>
              <td ><?=$job_row['remark']?></td>
			  <?php if($_SESSION['id_type']=='CC'){?>
			    <td><label class="control-label">Happy Code </label></td>
              <td ><?=$job_row['h_code']?></td><?php } else {?>
			    <td><label class="control-label">&nbsp; </label></td>
              <td >&nbsp;</td> <?php }?>
            </tr>
			 <tr>
              <td><label class="control-label">Confirm By</label></td>
              <td><?=$job_row['recipient_name']?></td>
              <td><label class="control-label">Contact No</label></td>
              <td><?=$job_row['recipient_contact']?></td>
            </tr>
			 <tr>
              <td><label class="control-label">Service Remark</label></td>
              <td><?=$job_row['service_rmak']?></td>
              <td><label class="control-label">Rating</label></td>
              <td><?=$job_row['rating']?></td>
            </tr> <tr>
              <td><label class="control-label">Service Charge</label></td>
              <td><?php echo "";?></td>
              <td><label class="control-label"></label></td>
              <td></td>
            </tr>
			
			<?php 
			$image_det = mysqli_query($link1,"SELECT * FROM image_upload_details  where job_no='".$job_row['job_no']."'");
			 while($row_image=mysqli_fetch_array($image_det)){?>  <tr>
              <td><label class="control-label"><?=$row_image['activity']?></label></td>
              <td colspan="3"  ><?php if ($row_image['img_url']!=""){?><span> <img src="<?=$row_image['img_url']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></span><?php } if($row_image['img_url1']!="") {?><span> <img src="<?=$row_image['img_url1']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></span> <?php } if($row_image['img_url2']!="") {?><span> <img src="<?=$row_image['img_url2']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></span><?php } if($row_image['img_url3']!="") {?><span> <img src="<?=$row_image['img_url3']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></span><?php } if($row_image['img_url4']!="") {?><span> <img src="<?=$row_image['img_url4']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></span><?php }?></td>
            </tr><?php }?>
            <tr>
              <td><label class="control-label">Eng Name</label></td>
              <td><?php $eng_det= explode("~",getAnyDetails($job_row['eng_id'],"locusername,contactmo","userloginid","locationuser_master",$link1));
			  echo $eng_det['0'];?></td>
              <td><label class="control-label">Eng Phone No</label></td>
              <td><?=$eng_det['1'];?></td>
            </tr>
            <tr>
            <td><label class="control-label">Pending  Reason</label></td>
            <td><?=$job_row[reason]?></td>
            <td><label class="control-label">Close  Reason</label></td> 
            <td><?=$job_row[close_rmk]?></td>
            </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->

    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;&nbsp;History</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
                  <tr>
                    <td width="15%"><strong>Location</strong></td>
                    <td width="10%"><strong>Activity</strong></td>
                    <td width="15%" colspan="2"><strong>Outcome</strong></td>
                    <td width="10%"><strong>Travel(KM)</strong></td>
                   
                    <td width="10%"><strong>Update By</strong></td>
                    <td width="10%"><strong>Remark</strong></td>
					<td width="10%"><strong>Priority</strong></td>
                    <td width="15%"><strong>Update on</strong></td>
                  </tr>
                </thead>
                <tbody>
                <?php
				$res_jobhistory = mysqli_query($link1,"SELECT * FROM call_history where job_no='".$docid."'");
				while($row_jobhistory = mysqli_fetch_assoc($res_jobhistory)){
				?>
                  <tr>
                    <td><?=$row_jobhistory['location_code']?></td>
                    <td><?=$row_jobhistory['activity']?></td>
                    <td colspan="2"><?=$row_jobhistory['outcome']?></td>
                    <td><?=$row_jobhistory['travel_km']?></td>
                   
                    <td><?=$row_jobhistory['updated_by']?></td>

                    <td><?=$row_jobhistory['remark']?></td>
					 <td><?php if($row_jobhistory['priority'] == '1') {echo "Low";} elseif($row_jobhistory['priority'] == '2') {echo "Normal";} else {echo "High" ;}?></td>
                    <td><?=$row_jobhistory['update_date']?></td>
                  </tr>
                  <?php
				}
				  ?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
     <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Product Details</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
            <tr>
                <td width="20%"><label class="control-label">Product</label></td>
                <td width="30%"><?php echo getAnyDetails($job_row["product_id"],"product_name","product_id","product_master",$link1);?></td>
                <td width="20%"><label class="control-label">Brand</label></td>
                <td width="30%"><?php echo getAnyDetails($job_row["brand_id"],"brand","brand_id","brand_master",$link1);?></td>
              </tr>
            <tr>
              <td><label class="control-label">Model</label></td>
              <td><?=$job_row['model']?></td>
              <td><label class="control-label">Date Of Installation</label></td>
              <td><?=dt_format($product_det['installation_date'])?></td>
            </tr>
            <tr>
              <td><label class="control-label"><?php echo SERIALNO ?></label></td>
              <td><?=$job_row['imei']?></td>
              <td><label class="control-label">Call Source</label></td>
              <td><?=$job_row['call_type']?></td>
            </tr>
            <tr>
              <td><label class="control-label">Warranty Status</label></td>
              <td><?=$job_row['warranty_status']?></td>
              <td><label class="control-label">Job For</label></td>
              <td><?=$job_row['call_for']?></td>
            </tr>
            <tr>
              <td><label class="control-label">Purchase Date</label></td>
              <td><?=dt_format($job_row['dop'])?></td>
              <td><label class="control-label">Warranty End Date</label></td>
              <td><?=dt_format($product_det['warranty_end_date'])?></td>
            </tr>
			
			 <tr>
			  <td><label class="control-label">Date Of Installation</label></td>
              <td><?=dt_format($product_det['installation_date'])?></td>
              <td><label class="control-label">Purchase From</label></td>
              <td ><?php if( $job_row['entity_type']=='Others') { echo "Others" ;} else {echo getAnyDetails($job_row['entity_type'],"name","id","entity_type",$link1); }?></td>
            </tr>
			 <tr>
              <td><label class="control-label">Dealer Name</label></td>
              <td><?=$job_row['dname']?></td>
              <td><label class="control-label">Invoice No</label></td>
              <td><?=$job_row['inv_no']?></td>
            </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
	<?php $repair_history = mysqli_query($link1,"SELECT * FROM repair_detail where job_no='".$docid."'"); if(mysqli_num_rows($repair_history)>0){?>	
	    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;&nbsp;Repair Detail</div>
      <div class="panel-body">
  <table class="table table-bordered" width="100%">
         <thead>	
                  <tr>
               	 
                    <td width="15%"><strong>Repair Location</strong></td>
					 <td width="15%"><strong>Defect Name</strong></td>
                    <td width="10%"><strong>Repair Code Name</strong></td>
                    <td width="10%"><strong>Partcode</strong></td>               
                    <td width="10%"><strong>Engineer Name</strong></td>
					 <td width="10%"><strong>Replace <?php echo SERIALNO ?> </strong></td>
                  
					 <td width="10%"><strong>Remark</strong></td>
					  <td width="10%"><strong>Update Date</strong></td>
                  </tr>
                </thead>
                <tbody>
               <?php
				
				while($repair_info = mysqli_fetch_assoc($repair_history)){
				?>
                  <tr>
      
                   
                    <td><?=getAnyDetails($repair_info['repair_location'],"locationname","location_code","location_master",$link1);?></td>
					 <td><?=getAnyDetails($repair_info['fault_code'],"defect_desc","defect_code","defect_master",$link1);?></td>
                    <td><?=getAnyDetails($repair_info['repair_code'],"rep_desc","rep_code","repaircode_master",$link1);?></td>
                    <td><?=getAnyDetails($repair_info['partcode'],"part_name","partcode","partcode_master",$link1);?></td>                  
                    <td><?=getAnyDetails($repair_info['eng_id'],"locusername","userloginid","locationuser_master",$link1);?></td>
					<td><?=$repair_info['replace_imei1']?></td>
				
                    <td><?=$repair_info['remark']?></td>
					 <td><?=$repair_info['update_date']?></td>
                  </tr>
                  <?php
				}
				  ?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->

<?php }?>
	
	<!--approval for EP-->
	
	<form   id="frm1" name="frm1" method="post" >
		
		
		
		
		
		
		
	<div id="btr_form" >	  
		<?php 
					 // $product_name_btr = getAnyDetails($job_row['product_id'],"product_name","product_id","product_master",$link1);
					  // $btr_product =  mysqli_num_rows(mysqli_query($link1,"select * from product_master where product_name in ('".$product_name_btr."') and status= '1' "));
					  //print_r($job_row);
					  if($job_row['product_id']==4 || $job_row['product_id']==5 || $job_row['product_id']==9 || $job_row['product_id']==10){
					  $sql_btr="select * from initial_btr_data where job_no='".$docid."' order by id desc";
	                  $result_btr_initial=mysqli_fetch_array(mysqli_query($link1,$sql_btr));
					  $sql_btr_f="select * from final_btr_data where job_no='".$docid."' order by id desc";
	                  $result_btr_final=mysqli_fetch_array(mysqli_query($link1,$sql_btr_f));
					  ?>			  
					  
		<div class="panel panel-info" style="margin-top: 25px;">

              <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Battery Inspection Details(Before)</div>

              <div class="panel-body">

              

              	<div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;"> OCV <span class="red_small">*</span></label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="ocv_befor" id="ocv_befor" type="text" value="<?=$result_btr_initial['ocv']?>" class="required form-control  " required maxlength="5">
   

                      </div>

                    </div>

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">C1 + Ve <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="c1_befor" id="c1_befor" type="text" value="<?=$result_btr_initial['c1']?>" class="required form-control  " required min="1.100" max="1.300" maxlength="5" style="width:62px;">
   
                      </div>

                    </div>
					 <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">C2 + Ve <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="c2_befor" id="c2_befor" type="text" value="<?=$result_btr_initial['c2']?>" class="required form-control  " required min="1.100" max="1.300" maxlength="5" style="width:62px;">
   
                      </div>

                    </div>

                  </div>

                  	<div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;"> C3 + Ve <span class="red_small">*</span></label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="c3_befor" id="c3_befor" type="text" value="<?=$result_btr_initial['c3']?>" class="required form-control  " required min="1.100" max="1.300" maxlength="5" style="width:62px;">
   

                      </div>

                    </div>

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">C4 + Ve <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="c4_befor" id="c4_befor" type="text" value="<?=$result_btr_initial['c4']?>" class="required form-control  " required min="1.100" max="1.300" maxlength="5" style="width:62px;">
   
                      </div>

                    </div>
					 <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">C5 + Ve <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="c5_befor" id="c5_befor" type="text" value="<?=$result_btr_initial['c5']?>" class="required form-control  " required min="1.100" max="1.300" maxlength="5" style="width:62px;">
   
                      </div>

                    </div>

                  </div>
				  
				  	<div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;"> C6 + Ve <span class="red_small">*</span></label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="c6_befor" id="c6_befor" type="text" value="<?=$result_btr_initial['c6']?>" class="required form-control  " required min="1.100" max="1.300" maxlength="5" style="width:62px;">
   

                      </div>

                    </div>

                   <!-- <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">C4 + Ve <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="imei_serial1" id="imei_serial1" type="text" value="" class="required form-control  " onkeyup="getdate4();">
   
                      </div>

                    </div>
					 <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">C5 + Ve <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="imei_serial1" id="imei_serial1" type="text" value="" class="required form-control  " onkeyup="getdate4();">
   
                      </div>

                    </div>-->

                  </div>


				  
              </div>

            </div>

<div class="panel panel-info" style="margin-top: 25px;">

              <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Battery Inspection Details(After)</div>

              <div class="panel-body">

              

              	<div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;"> Total Charging Hours </label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="tot_chrg_hr" id="tot_chrg_hr1" type="text" value="<?=$result_btr_final['charging_hour']?>" class=" form-control  "  placeholder="HH:MM" style="width:62px;">
   

                      </div>

                    </div>

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">TOC  </label>

                      <div class="col-md-4">
	<input name="toc" id="toc" type="text" value="<?=$result_btr_final['toc']?>" class=" form-control  "  style="width:62px;">
   
                      </div>

                    </div>
					 <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">OCV  </label>

                      <div class="col-md-4">
	<input name="ocv_aftr" id="ocv_aftr" type="text" value="<?=$result_btr_final['ocv']?>" class=" form-control  "  style="width:62px;">
   
                      </div>

                    </div>

                  </div>

                  	<div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;"> C1 + Ve </label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="c1_aftr" id="c1_aftr" type="text" value="<?=$result_btr_final['c1']?>" class=" form-control  "  min="1.100" max="1.300" maxlength="5" style="width:62px;">
   

                      </div>

                    </div>

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">C2 + Ve  </label>

                      <div class="col-md-4">
	<input name="c2_aftr" id="c2_aftr" type="text" value="<?=$result_btr_final['c2']?>" class=" form-control  "  min="1.100" max="1.300" maxlength="5" style="width:62px;">
   
                      </div>

                    </div>
					 <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">C3 + Ve  </label>

                      <div class="col-md-4">
	<input name="c3_aftr" id="c3_aftr" type="text" value="<?=$result_btr_final['c3']?>" class=" form-control  "  min="1.100" max="1.300" maxlength="5" style="width:62px;">
   
                      </div>

                    </div>

                  </div>
				  
				  	<div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;"> C4 + Ve </label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="c4_aftr" id="c4_aftr" type="text" value="<?=$result_btr_final['c4']?>" class=" form-control  "  min="1.100" max="1.300" maxlength="5" style="width:62px;">
   

                      </div>

                    </div>

                  <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">C5 + Ve  </label>

                      <div class="col-md-4">
	<input name="c5_aftr" id="c5_aftr" type="text" value="<?=$result_btr_final['c5']?>" class=" form-control  "  min="1.100" max="1.300" maxlength="5" style="width:62px;">
   
                      </div>

                    </div>
					 <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">C6 + Ve  </label>

                      <div class="col-md-4">
	<input name="c6_aftr" id="c6_aftr" type="text" value="<?=$result_btr_final['c6']?>" class=" form-control  "  min="1.100" max="1.300" maxlength="5" style="width:62px;">
   
                      </div>

                    </div>

                  </div>


				  
              </div>

            </div>
					  
			<div class="panel panel-info" style="margin-top: 25px;">

              <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Battery Test Result</div>

              <div class="panel-body">

              

              	<div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label" style="padding-left: 13px;"> Discharging Current <span class="red_small">*</span></label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="dischrg_current" id="dischrg_current" type="text" value="<?=$result_btr_final['dischrg_current']?>" class="required form-control  " required  style="width: 61px;">
   

                      </div>

                    </div>

                    
					 

                  </div>
				  <div class="form-group">

                   

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">Back Up Time <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="backup_time" id="backup_time" type="text" value="<?=$result_btr_final['backup_time']?>" class="required form-control  " required style="width: 61px;">
   
                      </div>

                    </div>
					 

                  </div>

                  	<div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">Test Result <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="test_result" id="test_result" type="text" maxlength="2" value="<?=$result_btr_final['test_result']?>" class="required form-control  " required>
   
                      </div>

                    </div>

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">Load In Watt <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="load_in_watt" id="load_in_watt" type="text" value="<?=$result_btr_final['load_in_watt']?>" class="required form-control  " required  maxlength="3" style="width:62px;">
   
                      </div>

                    </div>
					 <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">Cut off voltage <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="c_off_volt" id="c_off_volt" type="text" value="<?=$result_btr_final['cut_off_volt']?>" class="required form-control  " required  maxlength="5" style="width:62px;">
   
                      </div>

                    </div>

                  </div>
				  
				  	<div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;"> PCV 1 <span class="red_small">*</span></label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="pcv1" id="pcv1" type="text" value="<?=$result_btr_final['pcv1']?>" class="required form-control  " required  maxlength="5" min="0.000" max="1.999" style="width:62px;">
   

                      </div>

                    </div>

                  <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">PCV 2 <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="pcv2" id="pcv2" type="text" value="<?=$result_btr_final['pcv2']?>" class="required form-control  " required  maxlength="5" style="width:62px;" min="0.000" max="1.999">
   
                      </div>

                    </div>
					 <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">PCV 3 <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="pcv3" id="pcv3" type="text" value="<?=$result_btr_final['pcv3']?>" class="required form-control  " required  maxlength="5" style="width:62px;" min="0.000" max="1.999">
   
                      </div>

                    </div>

                  </div>
<div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;"> PCV 4 <span class="red_small">*</span></label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="pcv4" id="pcv4" type="text" value="<?=$result_btr_final['pcv4']?>" class="required form-control  " required  maxlength="5" style="width:62px;" min="0.000" max="1.999">
   

                      </div>

                    </div>

                  <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">PCV 5 <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="pcv5" id="pcv5" type="text" value="<?=$result_btr_final['pcv5']?>" class="required form-control  " required  maxlength="5" style="width:62px;" min="0.000" max="1.999">
   
                      </div>

                    </div>
					 <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;">PCV 6 <span class="red_small">*</span> </label>

                      <div class="col-md-4">
	<input name="pcv6" id="pcv6" type="text" value="<?=$result_btr_final['pcv6']?>" class="required form-control  " required maxlength="5" style="width:62px;" min="0.000" max="1.999">
   
                      </div>

                    </div>

                  </div>
				  <div class="form-group">

                    <div class="col-md-4"><label class="col-md-4 custom_label" style="padding-left: 13px;"> PCV 7 <span class="red_small">*</span></label>

                      <div class="col-md-4" id="modeldiv">

                       <input name="pcv7" id="pcv7" type="text" value="<?=$result_btr_final['pcv7']?>" class="required form-control  " required  maxlength="5" style="width:62px;" min="0.000" max="1.999">
   

                      </div>

                    </div>

					 

                  </div>
				  <div class="form-group">

                   

                  <div class="col-md-6"><label class="col-md-6 custom_label" style="padding-left: 12px;">Invertor Load Test Result <span class="red_small">*</span> </label>

                      <div class="col-md-6">
	<!--<input name="invt_load_tst_reslt" id="invt_load_tst_reslt" type="text" value="<?=$result_btr_final['invt_load_tst_reslt']?>" class="required form-control  " required  maxlength="5" style="width:62px;">-->
						  <select name="invt_load_tst_reslt" id="invt_load_tst_reslt1"  class="  form-control" style="width:250px;">
                      <option value="" >--Select --</option>
                      <option value="TEST FAIL" <?php if($result_btr_final['invt_load_tst_reslt']=='TEST FAIL'){echo "selected";}?>> TEST FAIL </option>
					  <option value="TEST PASS" <?php if($result_btr_final['invt_load_tst_reslt']=='TEST PASS'){echo "selected";}?>> TEST PASS </option>	  
                      <option value="RECHARGE" <?php if($result_btr_final['invt_load_tst_reslt']=='RECHARGE'){echo "selected";}?>> RECHARGE </option>
					  <option value="REJECT" <?php if($result_btr_final['invt_load_tst_reslt']=='REJECT'){echo "selected";}?>> REJECT </option>                    
                      						
				
				
                    </select>
   
                      </div>

                    </div>
					 

                  </div>


				  
              </div>

            </div>		  
					  
					  
					  
<?php } ?>
				  </div>	
		
		
		
		
		
		
		
		
		
		
		
<div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp; Approval Request Action</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%"><tbody><tr><td width="20%"><table class="table table-bordered" width="100%">
          <tbody>
            <?php  if(($job_row['status'] == '50' && $job_row['sub_status'] == '50') || ($job_row['status'] == '81' && $job_row['sub_status'] == '8')) { ?>
            <tr>
              <td class="btn-danger">Update Warranty of Product</td>
              <td class="btn-danger"><select   id="warranty_status" name="warranty_status" class="form-control" required >
                <option value="">Select Warranty Status</option>
                <option value="IN" <?php  if($job_row['warranty_status']=='IN'){echo "selected";}?>>IN</option>
                <option value="OUT" <?php  if($job_row['warranty_status']=='OUT'){echo "selected";}?>>OUT</option>
              </select></td>
              <td colspan="2" class="btn-warning">Note- its will effect incase of approved action only.</td>
              </tr>
            <tr>
              <td width="20%"><label class="control-label">Status</label></td>
              <td width="40%"><select   id="status" name="status" class="form-control" required >
                <option value="">Please Select</option>
                  <option value="51" >Approved</option>
                  <option value="52" >Rejected</option>
				
              </select></td>
              <td width="15%"><label class="control-label">Remark</label></td>
              <td width="25%"><textarea id="remark" name="remark" class="form-control" required></textarea></td>
            </tr>
            <tr>
              <td align="center" colspan="8"><input type="submit" id="update" name="update" value="Update" class="btn<?=$btncolor?>"></td>
            </tr>
            <?php  }?>
            <tr>
              <td align="center" colspan="8"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list_repl.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'">              </td>
            </tr>
          </tbody>
        </table>          <label class="control-label"></label></td>
                </tr>
      </tbody>
      </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	</form>

  </div><!--close panel group-->
 </div><!--close col-sm-9-->
</div><!--close row content-->

<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>