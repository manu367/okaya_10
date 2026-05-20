<div class="container-fluid">
	<div class="col-sm-12"> 
   	<div style="display:inline-block;float:right"><a href="../templates/GRN_IMEI_TEMPLATE.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/></a></div>
    <div class="form-group" id="test">
    	<div class="col-md-10"><label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
        	<div class="col-md-6">
            	<input type="file" name="fileupload" id="fileupload" required class="form-control" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/><input name="refsno" id="refsno" type="hidden" value="<?=base64_encode($_REQUEST['refid'])?>"/>
          	</div>
        </div>
    </div>
    <div class="form-group">
    	<div class="col-md-10"><label class="col-md-4 control-label"></label>
        	<div class="col-md-6">
        		<span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file and excel column should be in text format.</span>
            </div>
        </div>
    </div>
 </div><!--close col-sm-12-->
</div><!--close container-fluid-->
