 <?php 


require_once("../includes/config.php");



function stock_Details($location_code,$partcode,$fromdate,$todate,$b,$link1){
//echo "select sum(amount) as amt from  location_account_ledger where location_code='".$_REQUEST['location']."' and   entry_date <= '".$fromdate."' and crdr='".$b."'  order by entry_date,id";
$daterange = "create_date < '".$fromdate."' and stock_transfer='".$b."' and stock_type='OK' ";
//echo "select sum(qty) as tot_qty from stock_ledger where partcode='".$partcode."' and create_by='".$location_code."' and ".$daterange." group by partcode";
$result_row = mysqli_query($link1,"select sum(qty) as tot_qty from stock_ledger where partcode='".$partcode."' and create_by='".$location_code."' and ".$daterange." and action_taken!='Repair Done' group by partcode");

if(mysqli_num_rows($result_row)>0){
$row=mysqli_fetch_array($result_row);
$row_count= $row['tot_qty'];
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




 $sql_part="select * from  stock_ledger  where create_by='".$_REQUEST['location']."' and   (create_date >= '".$fromdate."' and create_date <='".$todate."') and partcode='".$_REQUEST['partcode']."' and action_taken!='Repair Done'  order by create_date";
$result_part=mysqli_query($link1,$sql_part);



/////////////////ASP Array/////////////////////////////////////////////

	
  
?>


  <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#CC0000" >
   <tr><td colspan="2">Date Range</td><td  colspan="5"><?=dt_format($fromdate) ." TO ". dt_format($todate)?></td></tr>
  <tr><td colspan="2">Part Name</td><td  colspan="5"><?=getAnyDetails($_REQUEST['partcode'] ,"part_name","partcode","partcode_master",$link1);?></td></tr>
 
  
    
    <tr class="Header">
      <td width="40">Sno</td>
       <td width="40">Transation Details</td>
       <td width="40">Tansation Number</td>
      <td width="67">Tansation Date</td>
      <td width="67">IN QTY</td>
      <td width="97">OUT QTY</td>
	     <td width="97">Total QTY</td>
      

    </tr>
    <?php  $i=1;
	
$open_in=stock_Details($_REQUEST['location'],$_REQUEST['partcode'],$fromdate,$todate,'IN',$link1);
$open_out=stock_Details($_REQUEST['location'],$_REQUEST['partcode'],$fromdate,$todate,'OUT',$link1);
$tot_open= $open_in-$open_out;

?>
<tr>
 <td>&nbsp;</td>
        
      <td>Opening Balance</td>
	  <td>&nbsp;</td>
     <td>&nbsp;</td>
    
    <td>&nbsp;</td>
     <td>&nbsp;</td>
		   <td>  <?php 
		  echo round($tot_open,2);
		  ?></td>
          
          </tr>
      <?php
$tot_in=0;
$tot_out=0;
  $tot_qty=0;
    while($row_part=mysqli_fetch_assoc($result_part))
{  

if($row_part['stock_transfer']=='IN'){

$tot_in+=$row_part['qty'];; 
}
else{

$tot_out+=$row_part['qty'];

}
?>

    <tr <?php if($i%2==1)echo "class='row1'";else echo "class='row2'";?> align="left" >
      <td><?=$i?></td>
        <td><?php echo $row_part['type_of_transfer'];?></td>
      <td><?php echo $row_part['reference_no'];?></td>
     <td><?php echo dt_format($row_part['create_date']);?></td>
    
      <td><?php if($row_part['stock_transfer']=='IN'){
		   $inqty=$row_part['qty'];
		
		  }else {
			   $inqty=0;
			  }
			  echo $inqty ;?></td>
            <td><?php if($row_part['stock_transfer']=='OUT'){
		  $ouqty=$row_part['qty'];
	
		  }
		  else{
			  $ouqty=0;
			  }
			  echo  $ouqty;
		  ?></td>
      <td><?php  
    $tot=$tot_open +$tot_in- $tot_out;
		echo round($tot,2);
			 ?></td>
      
      
    </tr>
    <?php

		  $tot_qty+=$tot;
	 $i++;
}?>
<tr class="Header">
      <td width="40">&nbsp;</td>
	   <td width="40">&nbsp;</td>
      
      <td width="67">&nbsp;</td>
      <td width="67">Total </td>
      <td width="97"><?php $in_qty_tot=$tot_in+$tot_open ;  echo round($in_qty_tot,2)?></td>
      <td width="201"><?php $out_qty_tot=$tot_out+$tot_open; echo round($out_qty_tot,2)?></td>
      <td width="112"><?php $grand_tot=$in_qty_tot-$out_qty_tot; echo round($grand_tot,2) ?></td>

    </tr>

  </table>
</div>