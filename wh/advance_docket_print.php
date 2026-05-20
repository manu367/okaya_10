<?php
require_once("../includes/config.php");
$id=base64_decode($_REQUEST['refid']);

//// Docket details from advance_docket_assign
$po_sql = "SELECT * FROM `advance_docket_assign` WHERE `id`=$id";

$po_res = mysqli_query($link1, $po_sql);

$po_row = mysqli_fetch_assoc($po_res);

////// get location details
//WH Details [assign_from]//////
//////  From 
$location_info_from = explode("~",getAnyDetails($po_row['assign_from'],"locationname,stateid,cityid,locationaddress","location_code","location_master",$link1));
////// From   city
$location_city_from = getAnyDetails($location_info_from[2],"city","cityid","city_master",$link1);
//// From  state
$location_state_from = getAnyDetails($location_info_from[1],"state","stateid","state_master",$link1);

//////  To 
$location_info_to = explode("~",getAnyDetails($po_row['assign_by'],"locationname,stateid,cityid,locationaddress","location_code","location_master",$link1));
////// To   city
$location_city_to = getAnyDetails($location_info_to[2],"city","cityid","city_master",$link1);
//// To  state
$location_state_to = getAnyDetails($location_info_to[1],"state","stateid","state_master",$link1);

?>
<!DOCTYPE>

<html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title><?=siteTitle?></title>

<link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/printcss.css" rel="stylesheet">
<script src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery-barcode.js"></script>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
	$("#barcodeprint").barcode(
		"<?=$po_row['doc_no'];?>", // Value barcode (dependent on the type of barcode)
		"code128" // type (string)
/* Types
codabar
code11 (code 11)
code39 (code 39)
code93 (code 93)
code128 (code 128)
ean8 (ean 8)
ean13 (ean 13)
std25 (standard 2 of 5 - industrial 2 of 5)
int25 (interleaved 2 of 5)
msi
datamatrix (ASCII + extended)
*/
/* Setting
barWidth: 1,
barHeight: 50,
moduleSize: 5,
showHRI: true,
addQuietZone: true,
marginHRI: 5,
bgColor: "#FFFFFF",
color: "#000000",
fontSize: 10,
output: "css",
posX: 0,
posY: 0
*/
	);
});
</script>
</head>

<body>
<!--	<page size="A4" layout="portrait"></page>-->
	<page size="A4">
		<table class="table" style="margin-bottom: 0px;">
            <tbody>
              <tr>
                <td width="25%"><img src="../images/blogo.png" /></td>

                <td width="25%" align="center"><div id="barcodeprint"></div></td>

                <td width="50%">
                	<div class="pull-left"><strong>Location Details :</strong></div>
  					<div class="pull-left"><?=$location_info_from[0]."<br/>".$location_info_from[3];?></div>                  
                </td>
              </tr>
            </tbody>
    	</table>
      <div align="center" class="lable"><u><strong>Advance Docket Print</strong></u></div>
      <table class="table" border="1" style="margin-bottom: 0px;">
            <tbody>
              <tr>
                <td width="15%" colspan="2"><strong>Document No.</strong></td>
                <td width="35%" colspan="2"><?=$po_row['doc_no'];?></td>
                <td width="15%" colspan="2"><strong>Document Date</strong></td>
                <td width="35%" colspan="2"><?=$po_row['doc_date'];?></td>
              </tr>
              <tr>
                <td colspan="8" align="left"><i class="fa fa-id-card fa-lg"></i><strong style="font-size:14px">&nbsp;<?=strtoupper($str)?>LOCATION DETAILS</strong></td>
              </tr>
              <!-- <tr>
                <td colspan="3"><strong>From </strong></td>
                
				        <td colspan="5"><strong>To </strong></td>
                                
              </tr> -->
              <tr>
			        <td colspan="2"><strong>Assign From</strong></td>
              <td colspan="2"><?=$location_info_from[0];?></td>
              <td colspan="2"><strong>Assign To</strong></td>
              <td colspan="2"><?=$location_info_to[0];?></td>	
              </tr>
              <tr>
			        <td colspan="2"><strong>City</strong></td>
                <td colspan="2"><?=$location_city_from;?></td>
                <td colspan="2"><strong>City</strong></td>
                <td colspan="2"><?=$location_city_to;?></td>				
              </tr>
              <tr>
                <td colspan="2"><strong>State</strong></td>
                <td colspan="2"><?=$location_state_from;?></td>
                <td colspan="2"><strong>State</strong></td>
                <td colspan="2"><?=$location_state_to;?></td>
              </tr>
              <tr>
                <td colspan="8" align="left"><i class="fa fa-tags fa-lg"></i><strong style="font-size:14px"> DOCKET DETAIL</strong></td>
              </tr>
	    </tbody>
        </table>
		<table class="table" border="1" style="margin-bottom: 0px;" width="100%">
          <thead width="100%">
          	<tr>
              <td width="10%"><strong>S.No</strong></<td>
              <td width="20%"><strong>Docket No</strong></<td>
              <td width="25%"><strong>Docket Company</strong></<td>
              <td width="20%"><strong>Mode Of Transport</strong></<td>
              <td width="25%"><strong>Response Msg</strong></<td>
            </tr>
				</thead>
          <tbody>
            <?php
			
			/////////////////////////// fetching data from data table /////////////////////////////////////////////////////////////////////////
			$podata_sql="SELECT * FROM `advance_docket_upload` WHERE doc_no='".$po_row['doc_no']."'";

			$podata_res=mysqli_query($link1,$podata_sql);
      $i=1;
			while($podata_row=mysqli_fetch_assoc($podata_res))
      {
       
				?>
          <tr>
                <td><?=$i?></td>

                <td><?=$podata_row['docket_no'];?></td>

				        <td><?=$podata_row['docket_company']?></td>

                <td><?=$podata_row['mode_of_transport']?></td>

                <td><?=$podata_row['response_msg']?></td>

      </tr>            
				<?php $i++;  } ?>
			 </tbody>
        </table>
		<table class="table" border="1">
           <tbody>         
              <tr>          
                <td colspan="8" align="right" style="vertical-align:bottom;border-bottom:none" height="50"><?php  echo "____________________________"?></td>
              </tr>
              <tr>        
                <td colspan="8" style="border-top:none" align="right">(Location signature)</td>
              </tr>
              <tr>
                <td style="border-right:none"><strong>Date & Time</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo "____________________________"?></td>
                <td colspan="7" style="vertical-align:bottom;border-left:none">&nbsp;</td>
              </tr>              
          </tbody>
   	  </table>
    </page>
</body>
</html>