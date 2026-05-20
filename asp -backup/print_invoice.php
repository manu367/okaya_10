<?php
require_once("../includes/config.php");
$docid = base64_decode($_REQUEST['id']);
$po_sql = "SELECT * FROM billing_master where challan_no='" . $docid . "' and from_location='".$_SESSION['asc_code']."'";
$po_res = mysqli_query($link1, $po_sql);
$po_row = mysqli_fetch_assoc($po_res);
//$loc = explode('~', getLocationDetails($po_row['from_location'], "phone,addrs,city,state,pan_no,gstin_no,st_no,name", $link1));
$location_info = getLocationDispAddress($po_row['from_location'],$link1);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
    <HEAD>
        <TITLE>Document Printing</TITLE>
        <META http-equiv=Content-Type content="text/html; charset=utf-8">
        <link href="../css/font-awesome.min.css" rel="stylesheet">
        <STYLE>
            P.page {
                PAGE-BREAK-AFTER: always
            }
            BODY {
                FONT-SIZE: 1px;
                FONT-FAMILY: 'ARIAL'
            }
            TABLE {
                BORDER-RIGHT: medium none;
                BORDER-LEFT-COLOR: black;
                BORDER-TOP-COLOR: black;
                BORDER-BOTTOM: medium none
            }
            TABLE.l {
                BORDER-TOP: medium none
            }
            TABLE.t {
                BORDER-LEFT: medium none
            }
            TABLE.none {
                BORDER-RIGHT: medium none;
                BORDER-TOP: medium none;
                BORDER-LEFT: medium none;
                BORDER-BOTTOM: medium none
            }
            TD.none {
                BORDER-RIGHT: medium none;
                BORDER-TOP: medium none;
                BORDER-LEFT: medium none;
                BORDER-BOTTOM: medium none
            }
            TD {
                BORDER-TOP: medium none;
                FONT-SIZE: 8pt;
                BORDER-BOTTOM-COLOR: black;
                BORDER-LEFT: medium none;
                BORDER-RIGHT-COLOR: black
            }
            TD.r {
                BORDER-BOTTOM: medium none
            }
            TD.b {
                BORDER-RIGHT: medium none

            }
            TD.l {
                BORDER-RIGHT: medium none

            }
            TD.bl {
                BORDER-RIGHT: medium none;
                BORDER-BOTTOM: thin outset
            }
            @media Print {
                .scrbtn {
                    DISPLAY: none
                }
            }
            .style6 {
                font-family: "Courier New", Courier, monospace
            }
            .style8 {
                font-family: "Courier New", Courier, monospace;
                font-weight: bold;
            }
            .style9 {
                font-size: 10pt;
                font-weight: bold;
            }
        </STYLE>
    </HEAD>
    <BODY bottomMargin=0 leftMargin=40 topMargin=0 onload=vbscript:window.print()>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <TABLE width=800 align="center" cellPadding=0 cellSpacing=0>
            <TBODY>
                <TR>
                    <TD vAlign=top>
                        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=1>
                            <TBODY>
                                <TR>
                                    <TD><div style="display:inline-block;float:left">
                                        
                                            <img src="../images/blogo.png"/>
                                           
                                            </div>
                                            <div style="display:inline-block;float:right">
                                            <span class="style9">
                                            <?php echo $location_info; ?> </span>
                                            </div><br/>
                                            <div style="margin-left:300px;"> <span class="style9">&nbsp;&nbsp;&nbsp;<?php echo "INVOICE"
;
?></span></div></TD>
                                </TR>
                            </TBODY>
                        </TABLE>
                        <TABLE cellSpacing=0 cellPadding=2 width="100%" border=1>
                            <TBODY>
                                <TR vAlign=top>
                                    <TD colspan="2" style="padding-left:5px;"><FONT size=2><B>Ship From(Consignor) :</B><br/><br/><B><span class="lable">
                                                <?= $po_row['bill_from'] ?>
                                                </span></B><br><span class="lable">
<?= $loc[1] ?>
                                            </span></FONT><BR>
                                        <FONT size=2><span class="lable">
<?= $loc[3] ?>
                                            </span><BR>

                                            <strong>GST No.:</strong><span class="lable">
                                                <?= $loc[5] ?>
                                            </span>,<strong>PAN No.:</strong><span class="lable">
<?= $loc[4] ?>
                                            </span></FONT>
                                    </TD>
                                    <TD colspan="2" vAlign=Top><FONT size=2><B>Invoice No.</B>
                                            &nbsp;&nbsp;<B><?= $po_row['challan_no'] ?></B></FONT><br>
                                        <FONT size=2><B>Invoice Date</B>
                                            &nbsp;<B><?= dt_format($po_row['entry_date']) ?></B></FONT></TD>
                                </TR>
                                <TR vAlign=top>
                                    <TD colspan="2" style="padding-left:5px;"><FONT size=2><B>Bill To:</B><BR><BR><span class="lable">
                                                <strong><?= $po_row['to_location'] ?></strong>
                                            </span></FONT>
                                        <FONT size=2><span class="lable">
<?= $to[1] ?>
                                            </span><BR>
                                            <span class="lable">
<?= $to[3] ?>
                                            </span><BR>
                                            <strong>GST No.:</strong><span class="lable">
                                                <?= $po_row['to_gst_no'] ?>
                                            </span> ,<strong>PAN No.:</strong><span class="lable">
<?= $to[3] ?>
                                            </span>
                                        </FONT></TD>
                                    <TD width="47%" colspan="2">
                                        <FONT size=2><B>Ship To:</B><BR><BR><span class="lable">
                                                <strong><?= $po_row['bill_to'] ?></strong>
                                            </span></FONT>
                                        <FONT size=2><span class="lable">
<?= $to[1] ?>
                                            </span><BR>
                                            <span class="lable">
<?= $to[3] ?>
                                            </span><BR>
                                            <strong>GST No.:</strong><span class="lable">
                                                <?= $to[4] ?>
                                            </span> ,<strong>PAN No.:</strong><span class="lable">
<?= $to[3] ?>
                                            </span>
                                        </FONT>
                                    </TD>
                                </TR>
                            </TBODY>
                        </TABLE>
                        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                            <TBODY>
                                <TR vAlign=top>
                                    <TD><table cellspacing=0 cellpadding=3 width="100%" border=1>
                                            <tr style="FONT-WEIGHT: 400" valign=top>
                                                <td width="7%"  align=center ><font size=2><strong>S.No.</strong></font></td>
                                                <td width="22%"  align=center><font size=2><strong>Description Of Goods</strong></font></td>
                                                <td width="22%"  align=center><font size=2><strong>HSN Code</strong></font></td>
                                                <td width="6%" align=center><font size=2><strong>Qty</strong></font></td>
                                                <td width="9%" align=center><font size=2><strong>Price</strong></font></td>
                                                <td width="9%"  align=center><font size=2><strong>Value</strong></font></td>
                                                <td width="14%"  align=center><font size=2><strong>Discount/<br>Unit</strong></font></td>
                                                <td width="22%"  align=center><font size=2><strong>Taxable</strong></font></td>
                                                <?php //if($po_row['to_stateid']==$po_row['from_stateid']){ ?>
                                                <td width="8%"  align=center><font size=2><strong>SGST %</strong></font></td>
                                                <td width="12%" align=center><font size=2><strong>SGST Amount</strong></font></td>
                                                <td width="8%"  align=center><font size=2><strong>CGST %</strong></font></td>
                                                <td width="12%" align=center><font size=2><strong>CGST Amount</strong></font></td>
                                                <?php //}else{
													?>
                                                <td width="8%"  align=center><font size=2><strong>IGST %</strong></font></td>
                                                <td width="12%" align=center><font size=2><strong>IGST Amount</strong></font></td>
                                                <?php //}?>
                                                <td width="13%"  align=center><font size=2><strong>Amount</strong></font></td>
                                            </tr>
                                            <?php
//-------------Getting invoice details from billing data---------------------//
                                            $rs = mysqli_query($link1, "select * from billing_product_items where challan_no='".$docid."' and from_location='".$_SESSION['asc_code']."'");
                                            $i = 1;
                                            $counter+=1;
                                            $hight = 350 - $counter * 14;
                                            $total = 0;
                                            $discount = 0;
                                            $value = 0;
                                            $tot_tax = 0;
                                            $grand_total = 0;
                                            while ($row = mysqli_fetch_array($rs)) {
                                                $product_name = explode("~", getAnyDetails($row['partcode'],"part_name,product_id,brand_id,hsn_code","partcode","partcode_master",$link1));                                            
                                                $val = $row['qty']*$row['price'];
                                                $taxable = $val-$row['discount_amt']*$row['qty'];
                                                ?>
                                                <tr>
                                                    <td align=center><span class="style6"><strong><?= $i ?></strong></span></td>
                                                    <td align=left><span class="style6"><strong><?= $product_name[0].' | '.$row['partcode'] ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= $row['hsn_code']; ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= $row['qty']; ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= $row['price'] ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= currencyFormat($val) ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= $row['discount_amt'] ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= currencyFormat($taxable) ?></strong></span></td>
                                                    <?php //if($po_row['to_stateid']==$po_row['from_stateid']){ ?>
                                                    <td align=right><span class="style6"><strong><?= $row['sgst_per'] ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= $row['sgst_amt'] ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= $row['cgst_per'] ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= $row['cgst_amt'] ?></strong></span></td>
                                                    <?php //}else{?>
                                                    <td align=right><span class="style6"><strong><?= $row['igst_per'] ?></strong></span></td>
                                                    <td align=right><span class="style6"><strong><?= $row['igst_amt'] ?></strong></span></td>
                                                    <?php //}?>
                                                    <td align=right><span class="style6"><strong><?= $row['item_total'] ?></strong></span></td>
                                                </tr>
                                                <?php
                                                $total+=$row['qty'];
                                                $price+=$row['price'];
                                                $value+=$row['totalvalue'];                                                
                                                $discount = $row['discount'];

                                                $i++;
                                            }
                                            $grand_total+=$after_discount + $tot_tax;
                                            ?>
                                            <tr height="<?= $hight ?>px">
                                                <td>&nbsp;</td>
                                                <td  align="right">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="center">&nbsp;</td>
                                                <td align="center">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="center">&nbsp;</td>
                                                <td align="center">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                                <td align="right">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td height="20" colspan="14" style="border-bottom:none"><div align="right"><B>Sub Total</B></div></td>
                                                <td style="border-bottom:none" align="right"><?php echo currencyFormat($value); ?></td>
                                            </tr>
                                            <tr>
                                                <td height="20" colspan="14"><div align="right"><strong><span class="style6">&nbsp;</span></strong><strong><span class="style6"></span>&nbsp;</strong><B>Round Off</B></div></td>

                                                <td  style="border-bottom:none" align="right"><?php echo currencyFormat($po_row['round_off']); ?></td>
                                            </tr>
                                            <tr>
                                                <td height="30" colspan="3" style="border-bottom:none" align="right"><B>Total Qty</B></td>
                                                <td align="right" style="border-bottom:none"><?php echo ($total); ?></td>
                                                <td colspan="10" align="right" style="border-bottom:none"><strong><span class="style6">&nbsp;</span></strong><strong><span class="style6"></span>&nbsp;</strong><B>Total Amount</B></td>
                                                <td align="right" style="border-bottom:none"><?php echo currencyFormat($po_row['total_cost']); ?></td>
                                            </tr>
                                        </table>
                                    </TD>
                                </TR>
                            </TBODY>
                        </TABLE>
                        <TABLE height=50 cellSpacing=0 cellPadding=2 width="100%" border=1>
                            <TBODY>
                                <TR>
                                    <TD width="409" rowspan="2" valign="top" style="padding-left:5px;">&nbsp;<FONT size=2><B>Amount in Words <i class="fa fa-inr" aria-hidden="true"></i> </B><?php echo number_to_words($po_row['total_cost']) . " Only"; ?></FONT><br/><br/>
                                        &nbsp;<FONT size=2>Remarks  : <?= $po_row['billing_rmk'] ?></FONT>
                                    </TD>
                                    <TD width="271" height="23" align="right" style="border-right:none"><strong>Total Amount </strong></TD>
                                    <TD width="98" height="23" align="right"><span class="style8">&nbsp;&nbsp;&nbsp; <?= currencyFormat($po_row['total_cost']); ?></span></TD>
                                </TR>
                                <TR>
                                    <TD height="50" colspan="3" vAlign=top align="right"><FONT size=2><span class="lable"><strong><?= $loc[7] ?></strong></span></FONT>&nbsp;&nbsp;<BR>
                                        <BR>
                                        <BR>
                                        <BR>
                                        <BR>Authorised Signatory&nbsp;&nbsp;</TD>
                                </TR>
                            </TBODY>
                        </TABLE>
                    </TD>
                </TR>
            </TBODY>
        </TABLE>
    </BODY>
</HTML>

