<?php
require_once("../includes/config.php");
/////get status//


$chk_asc=mysqli_query($link1,"select * from part_to_credit where  status='2' ");
$i=1;
while($asc1=mysqli_fetch_array($chk_asc)){
echo "update client_inventory set faulty=faulty-1 where partcode='".$asc1['partcode']."' and location_code='".$asc1['from_location']."' "."<br>";
mysqli_query($link1,"update client_inventory set faulty=faulty-1 where partcode='".$asc1['partcode']."' and location_code='".$asc1['from_location']."' ");
$i++;

}

?>