<?php
////// Function ID ///////
$fun_id = array("a"=>array(33));
//////////////////////////
require_once("../includes/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
//////////////////////////
$arrstatus = getFullStatus("",$link1);
$get_opr_rgts = getOprRights($_SESSION['userid'],$_REQUEST['pid'],$link1);
$docid=base64_decode($_REQUEST['id']);
///// planning details
$sql_locdet="SELECT * FROM jobcard_master where system_ref_no='".$docid."'";
$res_locdet=mysqli_query($link1,$sql_locdet);
$row_locdet=mysqli_fetch_assoc($res_locdet);
$prodet = explode("~",getAnyDetails($row_locdet['bom_modelcode'],"brandid, itemtypeid, part_name, productid, psubcatid, purchase_unit","partcode","partcode_master",$link1));
$prod_cat = getAnyDetails($prodet[3],"product_name","productid","product_master",$link1);
$prod_subcat = getAnyDetails($prodet[4],"prod_sub_cat","psubcatid","product_sub_category",$link1);
$prod_brand = getAnyDetails($prodet[0],"brand","brandid","brand_master",$link1);
$prod_it = getAnyDetails($prodet[1],"parttype","typeid","part_type_master",$link1);
$jobpart = "<strong>".$prodet[2]."</strong>&nbsp;&nbsp;/&nbsp;&nbsp;".$prod_subcat."&nbsp;&nbsp;/&nbsp;&nbsp;".$prod_cat."&nbsp;&nbsp;/&nbsp;&nbsp;".$prod_brand."&nbsp;&nbsp;/&nbsp;&nbsp;".$prod_it;
?>
<!DOCTYPE html>
<html>
  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?=siteTitle?></title>
  <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
  <script src="../js/jquery.js"></script>
  <?php 
	 include("../includes/fontawesome.html");
	 include("../includes/main_css.html"); 
	 include("../includes/bootstrap.html");
	 include("../includes/datatables.html");
  ?>
  <script src="../js/jquery.validate.js"></script>
  <script src="../js/bootstrap-select.js"></script>
  <link href="../css/bootstrap-select.css" rel="stylesheet"/>
  <script>
$(document).ready(function(){
	var spinner = $('#loader');
    $("#frm2").validate({
		submitHandler: function (form) {
			if(!this.wasSent){
				this.wasSent = true;
				$(':submit', form).val('Please wait...')
							  .attr('disabled', 'disabled')
							  .addClass('disabled');
				spinner.show();		  
				form.submit();
			} else {
				return false;
			}
		}
	});	
});
$(document).ready(function () {
	if( !$(':checkbox:checked').length){
		$('#saveButton').attr("disabled","disabled");
	}else{
		$('#saveButton').removeAttr("disabled");
	}
});	  
function checkSelRow(){
	
}	  
	/*function checkStockk() {
	var qty_ap = 0;
	var stockqty = 0;
	qty_ap = parseInt(document.getElementById("outcome_qty").value);
				 
	stockqty = parseInt(document.getElementById("min_issue").value);
				  //alert(stockqty);
	if (qty_ap > stockqty) {
		alert("Outcome  Qty can not be more then Issued Qty!");
		document.getElementById("outcome_qty").value = stockqty;
		document.getElementById("outcome_qty").focus();
	} else {
	}
}*/
	  
	function checkStockk() {
    
}
	    		  
////// function to extract part deatails  which has to be selected in consumable and scrap itms
function getPartDet(val,indx,pref){
	var split_val = val.split("~");
	document.getElementById(pref+"_bom_unit["+indx+"]").value = split_val[1];
	document.getElementById(pref+"_purchase_unit["+indx+"]").value = split_val[2];
	///// get part inventory
	getInvtry(split_val[0],indx,pref);
	checkStock(indx,pref)
	//checkDuplicatePart(indx,split_val[0]);
}
function makedropdown(){
	$('.selectpicker').selectpicker({
      liveSearch: true
	});
	$('.number').on('input', function() {
		match = (/(\d{0,6})[^.]*((?:\.\d{0,6})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
		this.value = match[1] + match[2];
  	});
}
////// function for open model to view planning details
function openModel(docid){
	$.get('planning_request_viewonly.php?doc_id=' + docid, function(html){
		 $('#courierModel .modal-body').html(html);
		 $('#courierModel').modal({
			show: true,
			backdrop:"static"
		});
	 });
	 $("#docno").html(docid);
}
/////// add new row for consumable items 
$(document).ready(function() {
	$("#add_consum").click(function() {		
		var numi = document.getElementById('theValue1');
		var preno=document.getElementById('theValue1').value;
		var num = (document.getElementById("theValue1").value -1)+2;
		var cnsm = "'consum'";
		numi.value = num;
		if($("#addr1_" + numi.value + ":visible").length == 0) {
			var r = '<tr id="addr1_'+num+'"><td width="30%"><div style="display:inline-block;float:right"><select name="consum_partcode['+num+']" id="consum_partcode['+num+']" class="form-control required selectpicker show-tick" required data-width="240px" onChange="getPartDet(this.value,'+num+','+cnsm+');"><option value="">--Select Part--</option><?php $res_pro = mysqli_query($link1,"select partcode,part_name,bom_unit,purchase_unit from partcode_master where itemtypeid in ('8') and status='1' order by part_name")or die(mysqli_error($link1));while($row_pro = mysqli_fetch_assoc($res_pro)){?><option value="<?=$row_pro['partcode']."~".$row_pro['bom_unit']."~".$row_pro['purchase_unit']?>"><?=$row_pro['part_name']." (".$row_pro['partcode'].")"?></option><?php } ?></select></div><div style="display:inline-block;float:left;"><i class="fa fa-close fa-lg" onClick="fun_removeC('+num+');"></i></div></td><td width="20%" align="right" colspan="2"><input type="text" name="consum_cf['+num+']" class="form-control number required" required id="consum_cf['+num+']" placeholder="Conversion Factor"/></td><td width="10%" align="right"><input type="text" name="consum_bom_qty['+num+']" class="form-control number required" required id="consum_bom_qty['+num+']" onkeyup="checkStock('+num+','+cnsm+');" placeholder="BOM Qty"/><input name="consum_avl_qty['+num+']" id="consum_avl_qty['+num+']" type="hidden"/></td><td width="10%"><input type="text" name="consum_bom_unit['+num+']" class="form-control" id="consum_bom_unit['+num+']" readonly="readonly"/></td><td width="10%" align="right"><input type="text" name="consum_pur_qty['+num+']" class="form-control number required" required id="consum_pur_qty['+num+']"  placeholder="Purchase Qty"/></td><td width="10%"><input type="text" name="consum_purchase_unit['+num+']" class="form-control" id="consum_purchase_unit['+num+']" readonly="readonly"/></td><td width="10%" align="center">&nbsp;</td></tr>';
			$('#itemsTable1').append(r);
			makedropdown();
		}
	});
});
function fun_removeC(con){
	var c = document.getElementById('addr1_' + con);
	c.parentNode.removeChild(c);
	con--;
	document.getElementById('theValue1').value = con;
}
/////// add new row for Scrap items 
$(document).ready(function() {
	$("#add_scrap").click(function() {		
		var numi = document.getElementById('theValue2');
		var preno=document.getElementById('theValue2').value;
		var num = (document.getElementById("theValue2").value -1)+2;
		var scrp = "'scrap'";
		numi.value = num;
		if($("#addr2_" + numi.value + ":visible").length == 0) {
			var r = '<tr id="addr2_'+num+'"><td width="30%"><div style="display:inline-block;float:right"><select name="scrap_partcode['+num+']" id="scrap_partcode['+num+']" class="form-control required selectpicker show-tick" required data-width="240px" onChange="getPartDet(this.value,'+num+','+scrp+')"><option value="">--Select Part--</option><?php $res_pro = mysqli_query($link1,"select partcode,part_name,bom_unit,purchase_unit from partcode_master where itemtypeid in ('9') and status='1' order by part_name")or die(mysqli_error($link1));while($row_pro = mysqli_fetch_assoc($res_pro)){?><option value="<?=$row_pro['partcode']."~".$row_pro['bom_unit']."~".$row_pro['purchase_unit']?>"><?=$row_pro['part_name']." (".$row_pro['partcode'].")"?></option><?php } ?></select></div><div style="display:inline-block;float:left;"><i class="fa fa-close fa-lg" onClick="fun_removeS('+num+');"></i></div></td><td width="20%" align="right" colspan="2"><input type="text" name="scrap_cf['+num+']" class="form-control number required" required id="scrap_cf['+num+']" placeholder="Conversion Factor"/></td><td width="10%" align="right"><input type="text" name="scrap_bom_qty['+num+']" class="form-control number required" required id="scrap_bom_qty['+num+']" placeholder="BOM Qty"/><input name="scrap_avl_qty['+num+']" id="scrap_avl_qty['+num+']" type="hidden"/></td><td width="10%"><input type="text" name="scrap_bom_unit['+num+']" class="form-control" id="scrap_bom_unit['+num+']" readonly="readonly"/></td><td width="10%" align="right"><input type="text" name="scrap_pur_qty['+num+']" class="form-control number required" required id="scrap_pur_qty['+num+']" placeholder="Purchase Qty"/></td><td width="10%"><input type="text" name="scrap_purchase_unit['+num+']" class="form-control" id="scrap_purchase_unit['+num+']" readonly="readonly"/></td><td width="10%" align="center">&nbsp;</td></tr>';
			$('#itemsTable2').append(r);
			makedropdown();
		}
	});
});
function fun_removeS(con){
	var c = document.getElementById('addr2_' + con);
	c.parentNode.removeChild(c);
	con--;
	document.getElementById('theValue2').value = con;
}
 ///////////// select all or not
 function checkAll(field){
   for (i = 0; i < field.length; i++){
   	  if(field[i].disabled == true){
	  }else{
		field[i].checked = true ;
	  }
   }
 }
 function uncheckAll(field){
   for (i = 0; i < field.length; i++)
        field[i].checked = false ;
 }
 ////check TAB click so we can check and uncheck the bom part
/*function checkTabClick(val,field){
    var chk=val.checked;
    if(chk==true){ 
		checkAll(field); 
	}
	else{
		uncheckAll(field);
	}
}*/
	  
function checkTabClick(val, field) {
    var chk = val.checked;
    if (chk == true) {
        checkAll(field);
    } else {
        uncheckAll(field);
    }
	
   //checkOutComeQty();
    // Check if all checkboxes are checked
    var allChecked = true;
    $('input[type="checkbox"][name^="chkbox"]').each(function() {
        if (!this.checked) {
            allChecked = false;
            return false;
        }
    });
	
    // Enable or disable the submit button
    if (allChecked) {
        $("#saveButton").removeAttr("disabled");
		checkOutComeQty();
    } else {
        $("#saveButton").attr("disabled", "disabled");
    }
}	 
	//UPDATED ON 18SEPT25
	function checkTabClick(val) {
    var chk = val.checked;

    // Only select/deselect checkboxes that are NOT SCRAP
    $('.partchk2.nonScrap').each(function () {
        if (!this.disabled) {
            this.checked = chk;
        }
    });

    checkOutComeQty(); // to revalidate form
}  
	  
	  
	  
////// get inventory on selection of part
///// Get part inventory ////////////
function getInvtry(prod_id,indx,pref){
    $.ajax({
		type:'post',
		url:'../includes/getAzaxFields.php',
		data:{avl_stock:prod_id,loc:'<?=$row_locdet['location_code'];?>',indx:indx},
		success:function(data){
			var sub_parts = data.split ("~");
			document.getElementById(pref+'_avl_qty['+sub_parts[0]+']').value = sub_parts[1];
		}
    });
}
	
////////  check available stock
function checkStock(indx,pref){
	if(document.getElementById(pref+"_avl_qty["+indx+"]").value){
		var avlstock=document.getElementById(pref+"_avl_qty["+indx+"]").value;
	}else{ 
		var avlstock=0.00
	}
	var enterstock=document.getElementById(pref+"_bom_qty["+indx+"]").value;
	if( parseFloat(avlstock) < parseFloat(enterstock) ){
		alert("Stock is not available");
		document.getElementById(pref+"_bom_qty["+indx+"]").value="";
		//document.getElementById("saveButton").disabled = true;
	}else{
		//document.getElementById("saveButton").disabled = false;
	}
}
	  


///// check job qty with outcome qty
/*function checkOutComeQty(){
	var jobqty2 = $("#job_qty").val();
	var jobqty = atob(jobqty2);
	var outqty = $("#outcome_qty").val();
	if(parseFloat(outqty)>parseFloat(jobqty)){
		$("#err_msg").html("<br/>Outcome Qty should not be more than Job Qty");
		$("#saveButton").attr("disabled","disabled");
	}else{
		$("#err_msg").html("");
		$("#saveButton").removeAttr("disabled");
	}
}*/
	  
	  
	  
	/*function checkOutComeQty() {
    var jobqty2 = $("#bal_qty").val();
    var jobqty = atob(jobqty2);
    var outqty = $("#outcome_qty").val();
    if (parseFloat(outqty) > parseFloat(jobqty)) {
        $("#err_msg").html("<br/>Outcome Qty should not be more than Balance Qty");
        $("#saveButton").attr("disabled", "disabled");
    } else {
        $("#err_msg").html("");
        
    }
}*/
function checkOutComeQty() {
	
	<!--UPDATED ON 3 JUNE 2025-->
	 // Prevent minus sign
    var qtyField = document.getElementById("outcome_qty");
    var qtyValue = qtyField.value;

    if (qtyValue.includes('-')) {
        $("#err_msg").html("<br/>Negative values are not allowed in Outcome Qty.");
        qtyField.value = qtyValue.replace(/-/g, '');
        qtyField.focus();
        $("#saveButton").attr("disabled", "disabled");
        return; // Stop further validation
    }
	<!-- UPDATED ON 3 JUNE 2025-->
	
	if( !$(':checkbox:checked').length){
		$('#saveButton').attr("disabled","disabled");
	}else{
		var mxcnt = $('#mxc').val();
		//console.log((':checkbox:checked').length+"=="+mxcnt);
		if($(':checkbox:checked').length==mxcnt){
			var qty_ap = 0;
			var stockqty = 0;
			qty_ap = parseInt(document.getElementById("outcome_qty").value);
			stockqty = parseInt(document.getElementById("min_issue").value);
			//alert(stockqty);
			if (qty_ap > stockqty) {
				$("#err_msg").html("<br/>Outcome Qty can not be more then Issued Qty!");
				document.getElementById("outcome_qty").value = stockqty;
				document.getElementById("outcome_qty").focus();
				// Enable the save button
				$("#saveButton").attr("disabled", "disabled");
			} else {
				var jobqty2 = $("#bal_qty").val();
				var jobqty = atob(jobqty2);
				var outqty = $("#outcome_qty").val();
				if (parseFloat(outqty) > parseFloat(jobqty)) {
					$("#err_msg").html("<br/>Outcome Qty should not be more than Balance Qty.");
					$("#saveButton").attr("disabled", "disabled");
				} else {
					$("#err_msg").html("");
					// Enable the save button
					$("#saveButton").removeAttr("disabled");
				}
			}
		}else{
			$('#saveButton').attr("disabled","disabled");
		}
	}
	
	updateScrapOutcomeQty(); 	//UPDATED ON 18 SEPTEMBER 2025
}
	  
	  
	  
	  ///// check bal qty with outcome qty
	 /* function checkOutComeQty1(){
	var jobqty22 = $("#bal_qty").val();
	var jobqty1 = atob(jobqty22);
	var outqty1 = $("#outcome_qty").val();
	if(parseFloat(outqty1)>parseFloat(jobqty1)){
		$("#err_msg").html("<br/>Balance Qty should not be more than Job Qty");
		$("#saveButton").attr("disabled","disabled");
	}else{
		$("#err_msg").html("");
		$("#saveButton").removeAttr("disabled");
	}
}*/
	
	//UPDATED ON 18 SEPTEMBER 2025
    function updateScrapOutcomeQty() {
    // Get the Outcome Qty
    let outcomeQty = parseFloat(document.getElementById('outcome_qty').value) || 0;

    // Loop through all scrap_outcome_qty fields
    document.querySelectorAll('.scrap_outcome_qty').forEach(function(scrapInput) {
        let idParts = scrapInput.id.split('_'); // e.g., scrap_outcome_qty_12
        let bomId = idParts[idParts.length - 1]; // Get the 12

        let bomperInput = document.getElementById('bomperunit_' + bomId);
        let bomperunit = bomperInput ? parseFloat(bomperInput.value) || 0 : 0;

        // Calculate scrap outcome qty
        let scrapQty = bomperunit * outcomeQty;
        scrapInput.value = scrapQty.toFixed(6);
    });
}	
//UPDATED ON 18 SEPTEMBER 2025  
	  
  </script>
  <link href="../css/loader.css" rel="stylesheet"/>
  </head>
  <body>
	<div class="container-fluid">
    	<div class="row content">
    	<?php 
    	include("../includes/leftnav_admin.php");
    	?>
      		<div class="<?=$screenwidth?>">
        		<h2 align="center"><i class="fa fa-sitemap"></i> BOM use against Job</h2>
                <h4 align="center"><?=$docid?></h4>
        		<div class="form-group"  id="page-wrap" style="margin-left:10px;">
                	<div class="panel-group">
    					<div class="panel panel-success table-responsive">
        					<div class="panel-heading"><i class="fa fa-newspaper-o fa-lg"></i>&nbsp;&nbsp;Job Details</div>
         					<div class="panel-body">
                            	<table class="table table-bordered" width="100%">
                                    <tbody>
                                      <tr>
                                        <td width="20%"><label class="control-label">Job Location</label></td>
                                        <td width="80%" colspan="3"><?php echo getAnyDetails($row_locdet['location_code'],"locationname","location_code","location_master",$link1)." (".$row_locdet['location_code'].")"; ?></td>
                                      </tr>
                                      <tr>
                                        <td width="20%"><label class="control-label">Job Product</label></td>
                                        <td width="80%" colspan="3"><?=$jobpart?></td>
                                      </tr>
                                      <tr>
                                        <td width="20%"><label class="control-label">Job Qty</label></td>
                                        <td width="30%"><?=$row_locdet['job_qty']." ".$prodet[5];?></td>
                                        <td width="20%"><label class="control-label">Balance Qty</label></td>
                                        <td width="30%"><?=$row_locdet['bal_qty']." ".$prodet[5];?></td>
                                      </tr>
                                      <tr>
                                        <td width="20%"><label class="control-label">Job Start Date</label></td>
                                        <td width="30%"><?=dt_format($row_locdet['start_date']);?></td>
                                        <td width="20%"><label class="control-label">Job End Date</label></td>
                                        <td width="30%"><?=dt_format($row_locdet['end_date']);?></td>
                                      </tr>
                                      <tr>
                                        <td width="20%"><label class="control-label">Planning No.</label></td>
                                        <td width="30%"><button title="Click to view planning details" type="button" class="btn<?=$btncolor?>" onClick="openModel('<?php echo $row_locdet['planning_no'];?>');"><?php echo $row_locdet['planning_no']; ?></button></td>
                                        <td width="20%"><label class="control-label">Planning Date</label></td>
                                        <td width="30%"><?=dt_format($row_locdet['planning_date'])?></td>
                                      </tr>
                                     </tbody>
                                  </table>    
                    		</div><!--close panel body-->
                    	</div><!--close panel-->
                        
						<div class="panel panel-success table-responsive">
							<div class="panel-heading"><i class="fa fa-sitemap fa-lg"></i>&nbsp;&nbsp;<em>You are working on <?=$row_locdet['bal_qty']?> qty of</em>   <strong>MODEL :</strong>  <span class="red_small"><?=$prodet[2]." , ".$prodet[5]?></span></div>
							 <div class="panel-body">
							 <form id="frm2" name="frm2" class="form-horizontal" action="bom_use_save.php" method="post">
							  <table class="table table-bordered" width="100%">
								<thead>
									<tr class="<?=$tableheadcolor?>">
									  	<th width="5%">S.No.</th>
										<th width="25%">BOM Part</th>
										<th width="10%">Available Stock<br/> <span class="red_small">(As Per BOM)</span></th>
										<th width="10%">BOM/Unit Qty</th>
										<th width="10%">Conversion Factor</th>
										<th width="10%">BOM Qty</th>
										<th width="10%">BOM Unit</th>
										<th width="10%">Updated BOM Qty</th>
										<th width="10%">Purchase Qty</th>
										<th width="10%">PR/Unit Qty</th>
										<th width="10%">PR Qty</th>
										<th width="10%">Purcahse Unit</th>
                                       <!-- <th width="10%" style="text-align:center"><input name="chkbox" id="chkbox" type="checkbox" onClick="checkTabClick(this,document.frm2.partchk2);"/></th>-->
										 <th width="10%" style="text-align:center"><input name="chkbox" id="chkbox" type="checkbox" onClick="checkTabClick(this);" />
									</tr>
								</thead>
								<tbody>
								<?php
								$l = 1;
								$check_inventory = 1;
								$balbomqty = 0.00000;
								$min_issue = 900000;
											
								$res_job = mysqli_query($link1,"SELECT * FROM jobcard_data where system_ref_no='".$docid."' and bomperunit >0 and parttype!='SCRAP' ");
								while($row_bom = mysqli_fetch_assoc($res_job)){
									$partdet = explode("~",getAnyDetails($row_bom['partcode'],"part_name,bom_unit,purchase_unit","partcode","partcode_master",$link1));
									
								
									$bomqty=mysqli_fetch_array(mysqli_query($link1,"select bom_qty,conversion_factor,bom_unit from bom_master 
                                          where bomid='".$row_locdet['bomid']."' and bom_partcode='".$row_bom['partcode']."' and status='1' and bom_qty >0"));
									
									$bomperqty = $row_bom['qty'] / $row_locdet['job_qty'];

									$balbomqty = getQtyFormate($row_locdet['bal_qty']*$bomqty['bom_qty'],6);
									$balprqty = getQtyFormate($row_locdet['bal_qty']*$bomqty['pr_qty'],6);
									//$purqty = $row_bom['qty'] * $row_bom['conversion_factor'];
									$purqty = $balbomqty * $row_bom['conversion_factor'];
									$prqty = $balprqty * $row_bom['conversion_factor'];
									//// get inventory of bom items
									$avl_stk = getInventory($row_locdet['location_code'],$row_bom['partcode'],"ok_qty",$link1);
								
									//$avl_invt = mysqli_fetch_assoc(mysqli_query($link1, "select ok_qty from inventory_status where partcode='" . $row_bom['partcode'] . "' and location_code='" . $row_locdet['location_code'] . "'"));
								
									if ($row_bom['qty'] <= $avl_stk) {
                                    	$check_inventory *= 1;
                                    } else {
                                     	$check_inventory *= 0;
                                     }
									
								?>   
								   <tr <?=$highlight?>
									   
									   <?php if($avl_stk <= 0){ echo "class='bg-danger'";} else if($balbomqty > $avl_stk){ echo "class='bg-danger'";}else{ }?>>
									
								    <td><?=$l?></td>
									<td><?php echo $partdet[0]." (".$row_bom['partcode'].")";?></td>
									  
								<td align="right">			 
							       <?php	
									//$getstock=explode("~",getInventory($row_locdet['location_code'],$row_bom['partcode'],"ok_qty",$link1));
								///// so now available stock for use is current stock minus hold stock //////
								$stock_qty1=number_format(($avl_stk),'6','.','');
								//// converting current stock into bom stock
								$stock_qty=$stock_qty1;
                                echo $stock_qty;?>
							    </td>
									  
									<!--<td align="right"><?php //echo getQtyFormate($bomperqty,6);?></td>-->
									  
									  <td align="right"><?php echo getQtyFormate($bomqty['bom_qty'],6);?></td>
									  
									<td align="right"><?=$row_bom['conversion_factor']?></td>
									   
									<td align="right"><?php //echo getQtyFormate($row_bom['qty'],6);
										echo $balbomqty;
										?>
										
									 <input type="hidden" name="avl_qty<?=$row_bom['id']?>" value="<?=$avl_stk?>"> 
									  </td>
									<td><?=$partdet[1]?></td>
									   <td align="right"><?=$row_bom['bomperunit']?></td>
									<td align="right"><?php echo getQtyFormate($purqty,6);?></td>
									   
									   <!--PR/Unit Qty-->
									     <td align="right"><?php echo getQtyFormate($bomqty['pr_qty'],6);?></td>
									   
									   <!--PR Qty-->
									   <td align="right"><?php //echo getQtyFormate($row_bom['qty'],6);
										echo $balprqty;
										?>
										   
									<td><?=$partdet[2]?></td>
									   
									   
                                   <!-- <td align="center"><input name="chkbox<?/*=$row_bom['id']*/?>" id="partchk2" type="checkbox" <?/*php if($avl_stk <= 0){ echo "disabled='disabled'"; echo "title='Inventory is not available for this part'";}*/?> value="<?/*=$row_bom['id']*/?>"/></td>-->
									   
									    <td align="center"><input name="chkbox<?=$row_bom['id']?>" id="partchk2" 
  type="checkbox" class="partchk2 <?= (strtoupper($row_bom['parttype']) == 'SCRAP') ? 'scrapOnly' : 'nonScrap' ?>" 
  <?php if($avl_stk <= 0){ echo "disabled='disabled' title='Inventory is not available for this part'";}?> 
  value="<?=$row_bom['id']?>"/></td>
								  </tr>
								<?php 	
								$minissue = round(($stock_qty) / $bomqty['bom_qty']);
								//$minissue = round(($stock_qty) / $balbomqty);
								//echo $minissue;  
								if (is_nan($minissue)) {
									$minissue = 0; 
								}
								$min_issue = min($minissue, $min_issue);
							    //echo $min_issue;
				                $l++; 
								}?>
                                </tbody>
							  </table>
								
					
                             <div class="form-group">
                                <div class="col-md-6"><label class="col-md-5 control-label">Outcome Qty <span class="red_small">*</span></label>
                                	<div class="col-md-6">
										
                                    	<!--<input name="outcome_qty" id="outcome_qty" type="text" value="<?=$min_issue?>" class="form-control number required" onKeyUp="checkOutComeQty();" />-->
										
										<!--<input name="outcome_qty" id="outcome_qty" type="text" value="<?php echo min($row_locdet['bal_qty'], $min_issue) ?>" class="form-control number required" onKeyUp="checkOutComeQty();" />-->
										
									<!--UPDATED ON 3 JUNE 2025-->
	<input name="outcome_qty" id="outcome_qty" type="text" value="<?php echo min($row_locdet['bal_qty'], $min_issue) ?>"
   class="form-control number required" onKeyUp="checkOutComeQty();"onkeypress="return event.key !== '-' && event.key !== '+';" />
										
                                    </div>
                               </div>
                               <div class="col-md-6"><label class="col-md-4 control-label">Remark <span class="red_small">*</span></label>
                                	<div class="col-md-6">
                                    	<textarea name="remark" id="remark" class="form-control addressfield required" style="resize:none" required></textarea>
                                    </div>
                               </div>
                             </div>
								 
<!-- START ISIQC -->
                                <div class="form-group">
                                <div class="col-md-6"><label class="col-md-5 control-label">FQA<span class="red_small">*</span></label>
                                	<div class="col-md-6">
									<select name="is_fqa" id="is_fqa" class="form-control required custom-select" required>
                                   <option value="Y" selected>YES</option>
                                  <option value="N">NO</option>
                                  </select>
								  <!-- <input type="hidden" name="freight" id="freight" class="form-control number" value="0.00" style="width:150px;text-align:right"/> -->
                                    </div>
                               </div>
                               <div class="col-md-6"><label class="col-md-4 control-label"></label>
                                	<div class="col-md-6">
                                    	
                                    </div>
                               </div>
                             </div>
<!-- END ISIQC -->		 
								 
								 
								 
                             <div class="form-group">  
                               <div class="col-md-12" style="display:inline-block;" align="center">
                               		  <input name="jobno" id="jobno" type="hidden" value="<?=$_REQUEST['id']?>"/>
									  <input name="job_qty" id="job_qty" type="hidden" value="<?=base64_encode($row_locdet['job_qty'])?>"/>
								   <input name="bal_qty" id="bal_qty" type="hidden" value="<?=base64_encode($row_locdet['bal_qty'])?>"/>
                                      <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
								      <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
								      <input name="icn" id="icn" type="hidden" value="<?=$_REQUEST['icn']?>"/>
								   
								   <?php /* if ($check_inventory == 1) { */?> 
                                    <!--  <button class='btn<?=$btncolor?>' id="saveButton" type="submit" name="saveButton" value="Save"><i class="fa fa-save fa-lg"></i>&nbsp;&nbsp;Save</button>-->
								   <?php /*}else{ echo "<span class='text-danger'>Stock is not available</span>";}*/?>
								   
								   	<input name="min_issue" type="hidden" id="min_issue" value="<?= $min_issue ?>" />
								   <?php if ($min_issue > 0 && $min_issue != 900000) { ?>
								    <button class='btn<?=$btncolor?>' id="saveButton" type="submit" name="saveButton" value="Save" <?=($avl_stk <= 0)? 'disabled="disabled" title="Inventory is not available for this part"' : '';?> onClick="checkOutComeQty();"><i class="fa fa-save fa-lg"></i>&nbsp;&nbsp;Save</button>
								   <input type="hidden" value="<?=$l?>" id="mxc" name="mxc"/>
									<?php } ?>
								   
								  
								   
								   
									 <button title="Back" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='<?=$_REQUEST['pageref']?>.php?status=<?php if(isset($_REQUEST['status'])){ echo $_REQUEST['status'];}?>&request_from=<?php if(isset($_REQUEST['request_from'])){ echo $_REQUEST['request_from'];}?>&request_to=<?php if(isset($_REQUEST['request_to'])){ echo $_REQUEST['request_to'];}?>&daterange=<?php if(isset($_REQUEST['daterange'])){ echo $_REQUEST['daterange'];}?>&id=<?php if(isset($_REQUEST['id'])){ echo $_REQUEST['id'];}?><?=$pagenav?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button>
								   <span class="red_small" id="err_msg"></span>
                                </div>
                              </div>
							  </form>
							</div><!--close panel body-->
						</div><!--close panel-->    
                        
                    </div><!--close panel group-->
        		</div><!--End form group--> 
      		</div><!--End col-sm-9--> 
    	</div><!--End row content--> 
  	</div><!--End container fluid-->
  	<div id="loader"></div>
  <?php
  include("../includes/footer.php");
  include("../includes/connection_close.php");
  ?>
  <!-- Start Model Mapped Modal -->
<div class="modal modalTH fade" id="courierModel" role="dialog">
<form class="form-horizontal" role="form" id="frm3" name="frm3">
<div class="modal-dialog modal-dialogTH modal-lg">

  <!-- Modal content-->
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h2 class="modal-title" align="center"><i class='fa fa-bar-chart faicon'></i> Planning Request</h2>
      <h4 id="docno" align="center"></h4>
    </div>
    <div class="modal-body modal-bodyTH">
     <!-- here dynamic task details will show -->
    </div>
    <div class="modal-footer">
      <button type="button" id="btnCancel" class="btn<?=$btncolor?>" data-dismiss="modal"><i class="fa fa-window-close fa-lg"></i> Close</button>
    </div>
    
  </div>
</div>
</form>
</div><!--close Model Mapped modal-->
  </body>
  </html>