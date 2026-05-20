<?php
require_once("../includes/config.php");
$today = date("Y-m-d");
$currtime = date("His");
//header("Content-Type: application/vnd.ms-word");
//header("Content-Disposition: attachment; filename="
//    .base64_decode($_REQUEST['rname']) . "_" .
//    $today . "_" .$currtime . ".doc");
//header("Pragma: no-cache");
//header("Expires: 0");

$query="SELECT * FROM `warranty_data` ORDER BY sno LIMIT 1,10";

$sql=mysqli_query($link1,$query)or die("er1".mysqli_error($link1));
if(!$sql || mysqli_num_rows($sql)==0){
    echo "No records found";
    exit;
}
?>

    <table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
        <tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
            <td height="25"><strong>S.No.</strong></td>
            <td><strong>Serial No</strong></td>
            <td><strong>Dealer Code</strong></td>
            <td><strong>PCB</strong></td>
            <td><strong>Transformer</strong></td>
            <td><strong>Model</strong></td>
            <td><strong>ModelCode</strong></td>
            <td><strong>Brand</strong></td>
            <td><strong>ProductName</strong></td>
            <td><strong>warrenty-date</strong></td>
            <td><strong>warrenty-end</strong></td>
            <td><strong>entry-date</strong></td>

        </tr>
        <?php
        $i=1;
        while($row_loc = mysqli_fetch_array($sql)){
            ?>
            <tr>
                <td align="left"><?=$i?></td>
                <td align="left"><?= isset($row_loc['serial_no']) && $row_loc['serial_no'] !== null ? $row_loc['serial_no'] : 'NaN' ?></td>
                <td align="left"><?= isset($row_loc['dealer_code']) && $row_loc['dealer_code'] !== null ? $row_loc['dealer_code'] : 'NaN' ?></td>
                <td align="left"><?= isset($row_loc['pcb']) && $row_loc['pcb'] !== '' ? $row_loc['pcb'] : 'NaN' ?></td>
                <td align="left"><?= isset($row_loc['transformer']) && $row_loc['transformer'] !== '' ? $row_loc['transformer'] : 'NaN' ?></td>
                <td align="left"><?= isset($row_loc['model_id']) && $row_loc['model_id'] !== null ? $row_loc['model_id'] : 'NaN' ?></td>
                <td align="left"><?= isset($row_loc['model_code']) && $row_loc['model_code'] !== null ? $row_loc['model_code'] : 'NaN' ?></td>
                <td align="left"><?= isset($row_loc['brand_id']) && $row_loc['brand_id'] !== null ? $row_loc['brand_id'] : 'NaN' ?></td>
                <td align="left"><?= isset($row_loc['product_id']) && $row_loc['product_id'] !== null ? $row_loc['product_id'] : 'NaN' ?></td>
                <td align="left"><?= isset($row_loc['start_date']) && $row_loc['start_date'] !== null ? $row_loc['start_date'] : 'NaN' ?></td>
                <td align="left"><?= isset($row_loc['end_date']) && $row_loc['end_date'] !== null ? $row_loc['end_date'] : 'NaN' ?></td>
                <td align="left"><?= isset($row_loc['entry_date']) && $row_loc['entry_date'] !== null ? $row_loc['entry_date'] : 'NaN' ?></td>
            </tr>
            <?php
            $i+=1;
        }
        ?>
    </table>
<?php
mysqli_close($link1);
?>