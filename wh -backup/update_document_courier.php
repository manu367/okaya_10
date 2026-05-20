<?php
require_once("../includes/config.php");
$docid=$_REQUEST['doc_id'];
$po_sql="SELECT * FROM billing_master where challan_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
$asc_contact=getAnyDetails($po_row['to_location'],"contactno1","location_code","location_master",$link1);
?>
    <div class="panel panel-success table-responsive">
      <div class="panel-heading">Update Courier details of document <strong><?=$docid?></strong></div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
       	 <tbody>
            <tr>
              <td width="25%"><strong>Courier Name</strong><span class="red_small">*</span></td>
              <td width="75%"><input name="courier_name" id="courier_name" type="text" class="form-control" maxlength="50" value="<?=$po_row['courier']?>" required/></td>
            </tr>
            <tr>
              <td width="25%"><strong>Docket No.</strong><span class="red_small">*</span></td>
              <td width="75%"><input name="docket_no" id="docket_no" type="text" class="form-control" value="<?=$po_row['docket_no']?>" required/>
              <input name="asc_phone" id="asc_phone" type="hidden" class="form-control" value="<?=$asc_contact?>"/></td>
            </tr>
            <tr>
              <td width="25%"><strong>Dispatch Remark</strong></td>
              <td width="75%"><textarea name="disprmk" id="disprmk" class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"><?=$po_row['disp_rmk']?></textarea><input name="ref_no" id="ref_no" type="hidden" value="<?=base64_encode($docid);?>"/></td>
            </tr> 
			 <tr>
              <td width="25%"><strong>Courier Amount</strong></td>
              <td width="75%"><input name="courier_amt" id="courier_amt" type="text" class="number form-control "  value="<?=$po_row['doc_price']?>"/></td>
            </tr>      
         </tbody>
       </table>
      </div><!--close panel body-->
    </div><!--close panel-->
