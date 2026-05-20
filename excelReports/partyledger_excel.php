 <?php 


require_once("../includes/config.php");



function tranction_Details($a,$fromdate,$todate,$b,$link1){
//echo "select sum(amount) as amt from  location_account_ledger where location_code='".$_REQUEST['location']."' and   entry_date <= '".$fromdate."' and crdr='".$b."'  order by entry_date,id";

$result_row = mysqli_query($link1,"select sum(amount) as amt from  location_account_ledger where location_code='".$_REQUEST['location']."' and   entry_date <= '".$fromdate."' and crdr='".$b."'  order by entry_date,id");

if(mysqli_num_rows($result_row)>0){
$row=mysqli_fetch_array($result_row);
$row_count= $row['amt'];
}
else{
$row_count=0;
}
return $row_count;

}


if ($_REQUEST['daterange'] != ""){
	$seldate = explode(" - ",$_REQUEST['daterange']);
	$fromdate = $seldate[0];
	$todate = $seldate[1];
}
else{
	$seldate = $today;
	$fromdate = $today;
	$todate = $today;
}




 $sql_part="select * from  location_account_ledger where location_code='".$_REQUEST['location']."' and   (entry_date >= '".$fromdate."' and entry_date <='".$todate."')  order by entry_date,id";
$result_part=mysqli_query($link1,$sql_part);



/////////////////ASP Array/////////////////////////////////////////////

	
  
?>


  <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#CC0000" >
   <tr><td colspan="2">Date Range</td><td  colspan="5"><?=dt_format($fromdate) ." TO ". dt_format($todate)?></td></tr>
  <tr><td colspan="2">Party Name</td><td  colspan="5"><?=getAnyDetails($_REQUEST['location'] ,"locationname","location_code","location_master",$link1);?></td></tr>
 
  
    
    <tr class="Header">
      <td width="40">Sno</td>
       <td width="40">Transation Details</td>
       <td width="40">Tansation Number</td>
      <td width="67">Tansation Date</td>
      <td width="67">Dr</td>
      <td width="97">Cr</td>
	     <td width="97">Total Amount</td>
      

    </tr>
    <?php  $i=1;
	
$cr_amt=tranction_Details($_REQUEST['location'],$fromdate,$todate,'CR',$link1);
$dr_amt=tranction_Details($_REQUEST['location'],$fromdate,$todate,'DR',$link1);
$tot_open= $cr_amt-$dr_amt;

if($tot_open<0 || $tot_open==0 ){
$amt_dr=$tot_open;
$amt_cr=0;

}else{
$amt_dr=0;
$amt_cr=$tot_open;
}
?>
<tr>
 <td>&nbsp;</td>
        
      <td>Opening Balance</td>
	  <td>&nbsp;</td>
     <td>&nbsp;</td>
    
      <td><?php 
		 echo $amt_dr;
		  ?></td>
            
      <td>  <?php 
		  echo $amt_cr;
		  ?></td>
		   <td>  <?php 
		  echo round($tot_open,2);
		  ?></td>
          
          </tr>
      <?php
$tot_dr_amt=0;
$tot_cr_amt=0;
  $tot_amount=0;
    while($row_part=mysqli_fetch_assoc($result_part))
{  

if($row_part['crdr']=='DR'){
$draamt=$row_part['amount'];
$tot_dr_amt+=$draamt; 
}
else{
$crcamt=$row_part['amount'];
$tot_cr_amt+=$crcamt;

}
?>

    <tr <?php if($i%2==1)echo "class='row1'";else echo "class='row2'";?> align="left" >
      <td><?=$i?></td>
        <td><?php echo $row_part['transaction_type'];?></td>
      <td><?php echo $row_part['remark']."". $row_part['transaction_no'];?></td>
     <td><?php echo dt_format($row_part['entry_date']);?></td>
    
      <td><?php if($row_part['crdr']=='DR'){
		   $dramt=$row_part['amount'];
		  $tot=$tot_open +$tot_cr_amt- $tot_dr_amt;
		  }else {
			   $dramt=0;
			  }
			  echo $dramt ;?></td>
            <td><?php if($row_part['crdr']=='CR'){
		  $cramt=$row_part['amount'];
		 $tot=$tot_open + $tot_cr_amt- $tot_dr_amt;
		  }
		  else{
			  $cramt=0;
			  }
			  echo  $cramt;
		  ?></td>
      <td><?php  
  
		echo round($tot,2);
			 ?></td>
      
      
    </tr>
    <?php

		  $tot_amount+=$tot;
	 $i++;
}?>
<tr class="Header">
      <td width="40">&nbsp;</td>
	   <td width="40">&nbsp;</td>
      
      <td width="67">&nbsp;</td>
      <td width="67">Total </td>
      <td width="97"><?php $dr_amount=$tot_dr_amt+$amt_dr ;  echo round($dr_amount,2)?></td>
      <td width="201"><?php $cr_amount=$tot_cr_amt+$amt_cr; echo round($cr_amount,2)?></td>
      <td width="112"><?php if($dr_amount<0) {$grand_tot=$dr_amount-$cr_amount; echo round($grand_tot,2) ;}else {$grand_tot=$cr_amount-$dr_amount; echo round($grand_tot,2);}?></td>

    </tr>
 
  </table>
</div>