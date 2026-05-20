<?php
require_once("../includes/config.php");
$docid=$_REQUEST['doc_id'];
$po_sql="SELECT * FROM billing_master where challan_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);
$docid=$_REQUEST['doc_id'];
?>
    <div class="panel panel-success table-responsive">
      <div class="panel-heading">Update Courier details of document <strong><?=$docid?></strong></div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
       	 <tbody>
            <tr>
              <td width="25%"><strong>Courier Name</strong></td>
              <td width="75%"><select name="Courier_name" id="Courier_name" class="form-control " readonly>
                    <?php
$res_pro = mysqli_query($link1,"select name,courier_id from courier_master where 1"); 
while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                    <option value="<?=$row_pro['courier_id']?>" <?php if($po_row['courier'] == $row_pro['courier_id']) { echo 'selected'; }?>>
                    <?=$row_pro['name']." (".$row_pro['courier_id'].")"?>
                    </option>
                    <?php } ?>
                  </select></td>
            </tr>
            <tr>
              <td width="25%"><strong>Docket No.</strong></td>
              <td width="75%"><input name="docket_no" id="docket_no" type="text" class="form-control" value="<?=$po_row['docket_no']?>" readonly/></td>
            </tr>
            <tr>
              <td width="25%"><strong>Dispatch Remark</strong></td>
              <td width="75%"><textarea name="disprmk" id="disprmk" class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"><?=$po_row['disp_rmk']?></textarea><input name="ref_no" id="ref_no" type="hidden" value="<?=base64_encode($docid);?>"/></td>
            </tr>      
         </tbody>
       </table>
      </div><!--close panel body-->
    </div><!--close panel-->
