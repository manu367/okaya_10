<?php
require_once("../includes/config.php");
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
$ustatus=($_REQUEST['status']);

## selected  Status
$where = "o.status='A'";
if($ustatus == 'D'){
    $where = "WHERE o.status='D'";
}

//////End filters value/////
$sql = "SELECT o.id,o.status, o.observation,
       GROUP_CONCAT(p.product_name ORDER BY p.product_name SEPARATOR ', ') AS 
           products FROM observation_master o JOIN product_master p 
               ON FIND_IN_SET(p.product_id, o.mapped_product) WHERE $where GROUP BY o.id";

$sql=mysqli_query($link1,$sql)or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
    <tr style="background:#396;color:#fff">
        <td>S.No.</td>
        <td>Observation</td>
        <td>Status</td>
        <td>Products</td>
    </tr>

    <?php $i=1;
    while($row = mysqli_fetch_assoc($sql)){ ?>
        <tr>
            <td><?=$i++?></td>
            <td><?=$row['observation']?></td>
            <td><?=$row['status']=='A'?'Active':'Deactive'?></td>
            <td><?=$row['products']?></td>
        </tr>
    <?php } ?>
</table>
