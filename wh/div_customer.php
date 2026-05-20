<?php include "../includes/dbconnect.php";?>

<div id="cust"><select name="cust_name" id="cust_name" style="width:350px;" class="required form-control" onChange="return getPartyaddress2(this.value);">

             <option value="">Please Select </option>

                    <?php 
$sql_chl="select name,city,state,asc_code from asc_master where id_type='WH' and status='Active'";
$res_chl=mysqli_query($link1,$sql_chl);
while($result_chl=mysqli_fetch_array($res_chl))

{

?>

                <option value="<?=$result_chl['asc_code']?>" <?php if($_REQUEST['cust_name']==$result_chl['asc_code'])echo "selected";?> >

                <?=$result_chl['name']." | ".$result_chl['asc_code']." | ".$result_chl['state']?>

                </option>

  

            

<?php

}

?>

              </select>&nbsp;

             <!--<a href="#"  onClick="window.open('../../master/addVendor.php?str=C', 'addCustomer', 'toolbar=No, status=No, resizable=yes, scrollbars=no, width=800, height=310, top=100, left=340');return false"><img src="../../images/file_add.png" width="20" height="20" border="0" title="Add New" /></a>&nbsp;

               <a id="refresh2" href="javascript:void(0);"><img src="../../images/refresh.gif" border="0" title="Refresh..."></a><script>$(function() {

      $("#refresh2").click(function(evt) {

         $("#cust").load("div_customer.php")

         evt.preventDefault();

		 document.getElementById('ship_address2').value="";

      })

    })

</script>-->&nbsp;Same as Bill To<input name="chk_addrs" id="chk_addrs" type="checkbox" value="Yes" disabled="disabled"  onclick="return displayShipAddrs_same();" /></div>

