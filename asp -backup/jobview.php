<?php
/* Database connection start */
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
if($docid !='')

{

	//$job_sel="select * from jobsheet_data where job_no='".$_REQUEST['job_no']."'";
	//$query=mysqli_query($link1, $sql) or die("job-grid-data.php: get job details");
	//$job_res=mysql_query($job_sel);

	//$job_result=mysql_fetch_assoc($job_res);


	include "job_print_customer.php?refid=".base64_encode($docid)."'";


	include "job_print_location.php?refid=".base64_encode($docid)."'";


}



?>