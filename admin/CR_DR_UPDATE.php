<?php
require_once("../includes/config.php");
/////get status//
$today=date("Y-m-d",$time_zone);

 $sql_count="select * from location_master  where 1";
$rs_count=mysqli_query($link1,$sql_count)or die("error1".mysqli_error($link1));
while ($selcounter=mysqli_fetch_array($rs_count)){
$rs_cr  = mysqli_query($link1,"select sum(amount) as cr_amt from location_account_ledger where location_code='".$selcounter['location_code']."' and crdr='CR' and transaction_type!='Amount Transfer to Security A/C' group by crdr")or die("Cr error1".mysqli_error($link1));
 $cr = mysqli_fetch_assoc($rs_cr) ;
 
 $rs_dr  = mysqli_query($link1,"select sum(amount) as dr_amt from location_account_ledger where location_code='".$selcounter['location_code']."' and crdr='DR' and transaction_type!='Amount Transfer to Security A/C'  group by crdr")or die("DR error1".mysqli_error($link1));
 $dr= mysqli_fetch_assoc($rs_dr);
 $crdr_amt = $cr['cr_amt']-$dr['dr_amt'];
$res_upd = mysqli_query($link1,"UPDATE current_cr_status set  	credit_bal ='". $crdr_amt."' , 	total_credit_limit  ='". $crdr_amt."'  where location_code='".$selcounter['location_code']."'");

}?>


