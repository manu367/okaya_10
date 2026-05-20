<?php include "../includes/dbconnect.php";?>
<div id="pty"><select name="party_name" id="party_name" style="width:350px;" class="required form-control" onChange="return getPartyaddress(this.value);">
             <option value="">Please Select </option>
                          <?php 
$sql_ch2="select distinct(id),name,state from vendor_master where status='Active'";
$res_ch2=mysqli_query($link1,$sql_ch2);

while($result_ch2=mysqli_fetch_array($res_ch2)){?>

<option value="<?=$result_ch2['id']?>" <?php if($_REQUEST['party_name']==$result_ch2['id'])echo "selected";?> >
                <?=$result_ch2['name']." | ".$result_ch2['id']." | ".$result_ch2['state']?>
                </option>
<?php
}
$sql_ch3="select distinct(id),name,state from temp_vendor_master  where  status='Active' and asc_code='$_SESSION[asc_code]'";
$res_ch3=mysqli_query($link1,$sql_ch3);
while($result_ch3=mysqli_fetch_array($res_ch3)){?>

<option value="<?=$result_ch3['id']?>" <?php if($_REQUEST['party_name']==$result_ch3['id'])echo "selected";?> >
                <?=$result_ch3['name']." | ".$result_ch3['id']." | ".$result_ch3['state']."(Temporary)"?>
                </option>
                    <?php

}
?>
              </select>&nbsp;
             <a href="#" onClick="window.open('../master/addVendor.php?str=S', 'addSupplier', 'toolbar=No, status=No, resizable=yes, scrollbars=yes, width=800, height=310, top=100, left=340');return false"><img src="../images/file_add.png" width="20" height="20" border="0" title="Add New" /></a>&nbsp;
               <a id="refresh" href="javascript:void(0);"><img src="../images/refresh.gif" border="0" title="Refresh..."></a><script>$(function() {
      $("#refresh").click(function(evt) {
         $("#pty").load("div_supplier.php")
         evt.preventDefault();
		 document.getElementById('ship_address').value="";
      })
    })
</script>

</div>
