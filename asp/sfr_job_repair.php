<?php
require_once("../includes/config.php");
//$arrstatus = getFullStatus("process",$link1);
$docid=base64_decode($_REQUEST['refid']);
//// job details
$job_sql="SELECT * FROM jobsheet_data where job_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
/////some security check should get from model master
$model_flags = explode("~",getAnyDetails($job_row['model_id'],"make_doa,repairable,replacement,replace_days","model_id","model_master",$link1));
$replc_daycount = daysDifference($job_row['open_date'],$job_row['dop']);
///// get repair level
$replvl = getAnyDetails($_SESSION['id_type'],"rep_level","usedname","location_type_master",$link1);

############################################ Symptom Code #######################################################
$rs_symp=mysqli_query($link1,"select * from symptom_master where status='1' order by symp_desc")or die("Error-> in symptom code".mysqli_error($link1));
if(mysqli_num_rows($rs_symp)>0){
	$symp_arr[][]=array();
	$j=0;
	while($row_symp=mysqli_fetch_array($rs_symp)){
		$symp_arr[$j][0]=$row_symp['symp_code'];
		$symp_arr[$j][1]=$row_symp['symp_desc'];
		$j++;
	}
}else{}
############################################ Getting PArt Code For EP #######################################################
$rs_part=mysqli_query($link1,"select partcode,part_name from partcode_master where model_id like '%".$job_row['model_id']."%' and part_for='ALL' and status='1' order by part_name")or die("Error-> in part code dropdown in ep".mysqli_error($link1));
$row_partcode=mysqli_num_rows($rs_part);
if($row_partcode>0){
	$parcode_arr[][]=array();
	$j=0;
	while($row_part=mysqli_fetch_array($rs_part)){
		$parcode_arr[$j][0]=$row_part['partcode'];
		$parcode_arr[$j][1]=$row_part['part_name'];
		$j++;
	}
}
else{
}
?>
<!DOCTYPE html>
<html>
  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>
  <?=siteTitle?>
  </title>
  <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
  <script src="../js/jquery.js"></script>
  <link href="../css/font-awesome.min.css" rel="stylesheet">
  <link href="../css/abc.css" rel="stylesheet">
  <script src="../js/bootstrap.min.js"></script>
  <link href="../css/abc2.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <script>
	$(document).ready(function(){
        $("#frm1").validate();
    });
 </script>
  <script>
 $(document).ready(function() {
    if (location.hash) {
        $("a[href='" + location.hash + "']").tab("show");
    }
    $(document.body).on("click", "a[data-toggle]", function(event) {
        location.hash = this.getAttribute("href");
    });
});
$(window).on("popstate", function() {
    var anchor = location.hash || $("a[data-toggle='tab']").first().attr("href");
    $("a[href='" + anchor + "']").tab("show");
	if(location.hash=="#menu1"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="none";
	}
	else if(location.hash=="#menu2"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="";
		document.getElementById("menu3").style.display="none";
	}
	else if(location.hash=="#menu3"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="";
	}
	else{
		document.getElementById("home").style.display="";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="none";
	}
});
///// function for switching the repair status and their corresponding fields
function change_stat(val){
	//var ws=document.getElementById("warranty_status").value;
	if(val=='45'){
		document.getElementById("ep").style.display="";
		document.getElementById("sfr").style.display="none";
		document.getElementById("pna").style.display="none";
		document.getElementById("part_consumption").style.display="none";
		document.getElementById("replacement").style.display="none";
		document.getElementById("errmsg").innerHTML = "";
		document.getElementById("savejob").style.display="";
		//document.getElementById("SFR").style.display="none";
	}
	else if(val=='416'){
		////check repairable flag is Y or N
		if("<?=$model_flags[1]?>" == "Y"){
			document.getElementById("ep").style.display="none";
			document.getElementById("pna").style.display="none";
			document.getElementById("sfr").style.display="none";
			document.getElementById("part_consumption").style.display="";
			document.getElementById("replacement").style.display="none";
			document.getElementById("errmsg").innerHTML = "";
			document.getElementById("savejob").style.display="";
			//document.getElementById("SFR").style.display="none";
		}else{
			document.getElementById("ep").style.display="none";
			document.getElementById("pna").style.display="none";
			document.getElementById("sfr").style.display="none";
			document.getElementById("part_consumption").style.display="none";
			document.getElementById("replacement").style.display="none";
			document.getElementById("errmsg").innerHTML = "This model is not eligible for repair.";
			document.getElementById("savejob").style.display="none";
		}
	}
	else if(val=='43'){
		document.getElementById("pna").style.display="";
		document.getElementById("ep").style.display="none";
		document.getElementById("part_consumption").style.display="none";
		document.getElementById("sfr").style.display="none";
		document.getElementById("replacement").style.display="none";
		document.getElementById("errmsg").innerHTML = "";
		document.getElementById("savejob").style.display="";
		//document.getElementById("SFR").style.display="none";
	}
	else if(val=='4'){
		document.getElementById("sfr").style.display="";
		document.getElementById("ep").style.display="none";
		document.getElementById("pna").style.display="none";
		document.getElementById("part_consumption").style.display="none";
		document.getElementById("replacement").style.display="none";
		document.getElementById("errmsg").innerHTML = "";
		document.getElementById("savejob").style.display="";
		//document.getElementById("SFR").style.display="";
	}
	/*else if(val=='8'){
		document.getElementById("sfr").style.display="none";
		document.getElementById("ep").style.display="none";
		document.getElementById("pna").style.display="none";
		document.getElementById("part_consumption").style.display="none";
		document.getElementById("replacement").style.display="none";
		//document.getElementById("SFR").style.display="none";
	}*/
	else if(val=='48'){
		///// check replacement of this model is Y or N
		if("<?=$model_flags[2]?>" == "Y"){
			///// check replacement days from model master
			var replcedays = "<?=$replc_daycount?>";
			var modelreplcedays = "<?=$model_flags[3]?>";
			if(parseInt(replcedays) <= parseInt(modelreplcedays)){
				document.getElementById("sfr").style.display="none";
				document.getElementById("ep").style.display="none";
				document.getElementById("pna").style.display="none";
				document.getElementById("part_consumption").style.display="none";
				document.getElementById("replacement").style.display="";
				document.getElementById("errmsg").innerHTML = "";
				document.getElementById("savejob").style.display="";
			}else{
				document.getElementById("sfr").style.display="none";
				document.getElementById("ep").style.display="none";
				document.getElementById("pna").style.display="none";
				document.getElementById("part_consumption").style.display="none";
				document.getElementById("replacement").style.display="none";
				document.getElementById("errmsg").innerHTML = "Replacement days are exceeding for this model.";
				document.getElementById("savejob").style.display="none";
			}
		}else{
			document.getElementById("sfr").style.display="none";
			document.getElementById("ep").style.display="none";
			document.getElementById("pna").style.display="none";
			document.getElementById("part_consumption").style.display="none";
			document.getElementById("replacement").style.display="none";
			document.getElementById("errmsg").innerHTML = "This model is not eligible for replacement.";
			document.getElementById("savejob").style.display="none";
		}
		
		//document.getElementById("SFR").style.display="none";
		
	}
	else{
		document.getElementById("sfr").style.display="none";
		document.getElementById("ep").style.display="none";
		document.getElementById("pna").style.display="none";
		document.getElementById("part_consumption").style.display="none";
		document.getElementById("replacement").style.display="none";
		document.getElementById("errmsg").innerHTML = "";
		document.getElementById("savejob").style.display="";
		//document.getElementById("SFR").style.display="none";
	}
}
///// Script For Add new Row for part consumption////////////////
function addNewRow(findex){
    var ni = document.getElementById('myDiv');
    var numi = document.getElementById('theValue');
    /////// initialize row items////
    var faultcode="fault_code["+numi.value+"]";
    var repaircode="repair_code["+numi.value+"]";
    var partcode="part["+numi.value+"]";
    //// increment of 1 value for new added row///
    var num = (document.getElementById("theValue").value -1)+ 2;
    //// check if previous row is filled with these three values (fault code,repair code, part)/////
    //if(document.getElementById(faultcode).value!="" && document.getElementById(repaircode).value!="" && document.getElementById(partcode).value!=""){
       numi.value = num;
       var divIdName = "my"+num+"Div";
	   //alert(divIdName);
       var newdiv = document.createElement('div');
       newdiv.setAttribute("id",divIdName);
	   newdiv.innerHTML = '<i class="fa fa-close fa-lg" onclick="return getRowDisable('+num+')" title="Remove this row"></i><table width="100%" class="table table-bordered"><tbody><tr><td width="25%"><select name="fault_code['+num+']" id="fault_code['+num+']" style="width:230px;" class="form-control" onChange="getRepairDropDown(this.value,'+num+')"><option value="" selected="selected">Select Fault Code</option><?php  $z=0; while($z<count($symp_arr)){?><option value="<?=$symp_arr[$z][0]?>"><?=$symp_arr[$z][1]?> (<?=$symp_arr[$z][0]?>)</option><?php $z++;}?></select></td><td width="25%"><span id="repDiv'+num+'"><select name="repair_code['+num+']" id="repair_code['+num+']" style="width:230px;" class="form-control" disabled="disabled" onChange="return getPartDropDown(this.value,'+num+');"><option value="" selected="selected"> Select Repair code</option></select></span><input name="repair_level['+num+']" id="repair_level['+num+']" class="form-control" type="hidden" readonly/></td><td width="30%"><span id="partDiv'+num+'"><select name="part['+num+']" id="part['+num+']" disabled="disabled" style="width:300px" class="form-control" onChange="getstockable(this.value,'+num+');"><option value="" selected="selected"> Select Part</option></select></span></td><td width="10%"><input name="part_price['+num+']" id="part_price['+num+']" class="form-control" type="text" style="width:100px;text-align:right" readonly/></td><td width="10%"><input name="avlqty['+num+']" id="avlqty['+num+']" class="form-control" type="text" style="width:100px;text-align:right" readonly/></td></tr></tbody></table>';
       ni.appendChild(newdiv);
	//}
}
///// Close Script For Add new Row for part consumption////////////////
//// Disable/Hide Part Row //////
function getRowDisable(ind) {
	 // hide My Div \\
    document.getElementById("my"+ind+"Div").style.display="none";
	// Reset Value\\
	document.getElementById("fault_code"+"["+ind+"]").value="";
	document.getElementById("repair_code"+"["+ind+"]").value="";
	document.getElementById("part"+"["+ind+"]").value="";
	document.getElementById("avlqty"+"["+ind+"]").value="0";
}
//// Close  Disable/Hide Part Row //////
///// Script For Add new Row for PNA////////////////
function addNewRowPna(findex){
    var ni = document.getElementById('myDivPna');
    var numi = document.getElementById('theValuePna');
    /////// initialize row items////
    var pendPart="pending_part["+numi.value+"]";
    //// increment of 1 value for new added row///
    var num = (document.getElementById("theValuePna").value -1)+ 2;
    //// check if previous row is filled with these three values (fault code,repair code, part)/////
       numi.value = num;
       var divIdName = "my"+num+"DivPna";
	   //alert(divIdName);
       var newdiv = document.createElement('div');
       newdiv.setAttribute("id",divIdName);
	   newdiv.innerHTML = "<i class='fa fa-close fa-lg' onclick='return getRowDisablePna("+num+")' title='Remove this row'></i><table width='100%' class='table table-bordered'><tbody><tr><td><select name='pending_part["+num+"]' class='form-control' id='pending_part["+num+"]' style='width:250px;'><option value='' selected='selected'> Select Pending Part</option><?php $rs_pna = mysqli_query($link1,"select pm.partcode, pm.part_name, pm.part_category from partcode_master pm, client_inventory ci where pm.model_id like '%".$job_row['model_id']."%' and pm.status='1' and ci.partcode!=pm.partcode and ci.okqty > 0 and ci.location_code='".$_SESSION['asc_code']."' order by pm.part_name")or die("error in pna dropdown".mysqli_error($link1));while($row_pna = mysqli_fetch_array($rs_pna)){?><option value='<?=$row_pna[0]?>'><?=$row_pna[1]."-".$row_pna[2]?> (<?=$row_pna[0]?> ) </option><?php }?></select></td></tr></tbody></table>";
       ni.appendChild(newdiv);
	//}
}
///// Close Script For Add new Row for PNA////////////////
//// Disable/Hide Part Row //////
function getRowDisablePna(ind) {
	 // hide My Div \\
    document.getElementById("my"+ind+"DivPna").style.display="none";
	// Reset Value\\
	document.getElementById("pending_part"+"["+ind+"]").value="";
}
//// Close  Disable/Hide Part Row //////
///// Script For Add new Row for EP////////////////
function addNewRowEp(findex){
    var ni = document.getElementById('myDivEp');
    var numi = document.getElementById('theValueEp');
    /////// initialize row items////
    var estiPart="esti_part["+numi.value+"]";
    //// increment of 1 value for new added row///
    var num = (document.getElementById("theValueEp").value -1)+ 2;
    //// check if previous row is filled with these three values (fault code,repair code, part)/////
       numi.value = num;
       var divIdName = "my"+num+"DivEp";
	   //alert(divIdName);
       var newdiv = document.createElement('div');
       newdiv.setAttribute("id",divIdName);
	   newdiv.innerHTML = "<i class='fa fa-close fa-lg' onclick='getRowDisableEp("+num+");getCost_service();' title='Remove this row'></i><table width='100%' class='table table-bordered'><tbody><tr align='left'><td width='35%'><select name='esti_part["+num+"]' class='form-control' id='esti_part["+num+"]' style='width:250px;' onChange='getData_esti(this.value,"+num+");' ><option value='' selected='selected'> Select Estimate Part</option><?php $z=0;while($z<count($parcode_arr)){?><option value='<?=$parcode_arr[$z][0]?>'><?=$parcode_arr[$z][1]?> ( <?=$parcode_arr[$z][0]?> )</option><?php $z++;}?></select></td><td width='15%'><input name='ep_cost["+num+"]' id='ep_cost["+num+"]' type='text'  class='form-control' style='width:100px;text-align:right' value='0.00' readonly /><input name='ep_hsn_code["+num+"]' id='ep_hsn_code["+num+"]' type='hidden' class='form-control' style='width:100px;text-align:right' value='' readonly /></td><td width='15%'><input name='ep_taxper["+num+"]' id='ep_taxper["+num+"]' type='text' class='form-control' style='width:75px;text-align:right' value='0.00' readonly /></td><td width='15%'><input name='ep_taxamt["+num+"]' id='ep_taxamt["+num+"]' type='text' class='form-control' style='width:85px;text-align:right' value='0.00' readonly /></td><td width='20%'><input name='ep_totalamt["+num+"]' id='ep_totalamt["+num+"]' type='text' class='form-control' style='width:100px;text-align:right' value='0.00' readonly /></td></tr></tbody></table>";
       ni.appendChild(newdiv);
	//}
}
///// Close Script For Add new Row for EP////////////////
//// Disable/Hide Part Row //////
function getRowDisableEp(ind) {
	 // hide My Div \\
    document.getElementById("my"+ind+"DivEp").style.display="none";
	// Reset Value\\
	document.getElementById("esti_part"+"["+ind+"]").value="";
	document.getElementById("ep_cost"+"["+ind+"]").value="";
	document.getElementById("ep_hsn_code"+"["+ind+"]").value="";
	document.getElementById("ep_taxper"+"["+ind+"]").value="";
	document.getElementById("ep_taxamt"+"["+ind+"]").value="";
	document.getElementById("ep_totalamt"+"["+ind+"]").value="";
}
function row_count_ep(){
	var val=document.getElementById("theValueEp").value;	
	if(val==6){
		document.getElementById("add_rowep").style.display="none";
	}else{
		document.getElementById("add_rowep").style.display="";
	}
}
//// Close  Disable/Hide Part Row //////
///// function to get EP details with GST and part price
function getData_esti(val,indx){
	$.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{eppart_code:val, wrs:'<?=$job_row['warranty_status']?>', customerstate:'<?=$job_row['state_id']?>', locstate:'<?=$_SESSION['stateid']?>', indxno:indx},
		success:function(data){
		var resval = data.split("~");
		var fldIndx1 = resval[5];
		/////initialize gst amount
		var ep_taxtotal = 0;
		var ep_tot_cost = 0;
		var ep_grandtotal = 0;
		var ep_hsn_code = "ep_hsn_code["+fldIndx1+"]";
	    //// initialize tax percentage fields
		var ep_taxper = "ep_taxper["+fldIndx1+"]";
		var ep_taxamt = "ep_taxamt["+fldIndx1+"]";
		var ep_totalamt = "ep_totalamt["+fldIndx1+"]";
		//// check if return data is something
		if(data != ""){
			document.getElementById("ep_cost["+fldIndx1+"]").value = resval[0];
			document.getElementById(ep_hsn_code).value = resval[1];
			document.getElementById(ep_taxper).value = resval[2];
			document.getElementById(ep_taxamt).value = resval[3];
			document.getElementById(ep_totalamt).value = resval[4];		
		}
		getCost_service();
	    }
	});
}
function getCost_service(){
	if(document.getElementById('ser_charge').value){
		var ser_chargeV = parseFloat(document.getElementById('ser_charge').value);
	}else{
		var ser_chargeV = 0.00;
	}
	var ser_tax_perV = parseFloat(document.getElementById('ser_tax_per').value);
	var ser_tax_amtV = (ser_chargeV * ser_tax_perV)/100;
	var total_ser_tax_amtV = parseFloat(ser_chargeV) + parseFloat(ser_tax_amtV);
	document.getElementById('ser_tax_amt').value = parseFloat(ser_tax_amtV,2);
	document.getElementById('total_ser_tax_amt').value = parseFloat(total_ser_tax_amtV,2);
	var taxtotal=0;
	var tot_cost=0;
	var grandtotal=0;
	var numi = document.getElementById('theValueEp').value;
	for(var i=0;i<=numi;i++){
		var c1="ep_taxamt["+i+"]";
		var c4="ep_cost["+i+"]";
		var c5="ep_totalamt["+i+"]";
		if(document.getElementById(c1).value){ var eptaxamt=parseFloat(document.getElementById(c1).value);}else{ var eptaxamt=0.00;}
		if(document.getElementById(c4).value){ var epcostamt=parseFloat(document.getElementById(c4).value);}else{ var epcostamt=0.00;}
		if(document.getElementById(c5).value){ var eptotamt=parseFloat(document.getElementById(c5).value);}else{ var eptotamt=0.00;}
		var taxtotal = taxtotal + eptaxamt;	
		var tot_cost = tot_cost + epcostamt;
		var grandtotal = grandtotal+eptotamt;
	}
	if(document.getElementById('ser_tax_amt').value){
		var tax_srv = parseFloat(document.getElementById('ser_tax_amt').value);
	}else{ 
		var tax_srv = 0.00;
	}
	if(document.getElementById('ser_charge').value){
		var totcost_srv = parseFloat(document.getElementById('ser_charge').value);
	}else{ 
		var totcost_srv = 0.00;
	}
	if(document.getElementById('total_ser_tax_amt').value){
		var total_srv = parseFloat(document.getElementById('total_ser_tax_amt').value);
		var newes_srv = parseFloat(document.getElementById('total_ser_tax_amt').value);
	}else{ 
		var total_srv = 0.00;
		var newes_srv = 0.00;
	}
				
	document.getElementById("taxtotal").value = (taxtotal + tax_srv).toFixed(2);
	document.getElementById("tot_cost").value = (tot_cost + totcost_srv).toFixed(2);
	document.getElementById("grandtotal").value = (grandtotal + total_srv).toFixed(2);
	document.getElementById("ep_new_es").value = (grandtotal + newes_srv).toFixed(2);	
}
/////////// function to get repair code on the basis of symptom code
  function getRepairDropDown(val,indx){

	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{symptomcode:val, replevel:'<?=$replvl?>', indxno:indx},
		success:function(data){
		var getValue = data.split("~");
		if(getValue[0]!=""){
        	document.getElementById("repDiv"+getValue[1]).innerHTML=getValue[0];
		 	document.getElementById("repair_code["+getValue[1]+"]").className="required form-control";
	  	}else{
		  //// if no val found then back to previous stage
		 	document.getElementById("repair_code["+getValue[1]+"]").value="";
		 	document.getElementById("repair_code["+getValue[1]+"]").disabled=true; 
		 	document.getElementById("repair_code["+getValue[1]+"]").className="form-control";
		}
	    }
	  });
  }
/////////// function to get part on the basis of repair code
  function getPartDropDown(val,indx){
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{repaircode:val, modelcode:'<?=$job_row['model_id']?>', locationcode:'<?=$_SESSION['asc_code']?>', indxno:indx},
		success:function(data){
			//alert(data);
		var splitPart=data.split("~");
	  	if(splitPart[0]!="" && splitPart[0]!=0){
			document.getElementById("partDiv"+splitPart[1]).innerHTML=splitPart[0];
			document.getElementById("repair_level["+splitPart[1]+"]").value=splitPart[3];
		   	//document.getElementById("part_replace["+splitPart[1]+"]").value=splitPart[2];
		   	//document.getElementById("rep_level["+splitPart[1]+"]").value=splitPart[3];
		 	///// assign class for part mandatory if repair code selected /////////
		 	/*if((document.getElementById("part_replace["+splitPart[1]+"]").value)=="Y"  ){
			 	document.getElementById("part["+splitPart[1]+"]").className="required form-control";
			}*/		 
	  	}else{
		  	//// if no val found then back to previous stage
		 	document.getElementById("part["+splitPart[1]+"]").value="";
		 	document.getElementById("part["+splitPart[1]+"]").disabled=true;
			document.getElementById("repair_level["+splitPart[1]+"]").value=""; 
		}
		}
	  });
  }
  ////// fill stock available input field on selection of part
  function getstockable(val,indx){
	  var result = val.split("^");
	  if("<?php echo $job_row['warranty_status'] ?>" == "OUT"){
	  	document.getElementById("part_price["+indx+"]").value=result[1];
	  }else{
	  	document.getElementById("part_price["+indx+"]").value=0.00;
	  }
	  document.getElementById("avlqty["+indx+"]").value=result[2];
  }
  /////// get replace type dropdown like BOX or UNIt on the basis of new replace model selected
  function replace_type(val){
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{replacemodel:val},
		success:function(data){
			document.getElementById('replacedDiv').innerHTML=data;
		}
	  });
  }
  ///// get stock of selected part
  function getstock(val){
	  ///// get model specification like sim type, serial/imei length
	  var repl_model = document.getElementById('replace_model').value;
	  var sim_type = "";
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{modelcode:repl_model, fieldss:'sim_type,len_serialno'},
		success:function(data){
			var splitdata = data.split("~");
			sim_type = splitdata[0];
		}
	  });
	  ////// get ok qty of selected replace model
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{partcodestk:val, stk_type:'okqty', locationcode:'<?=$_SESSION['asc_code']?>'},
		success:function(data){
			document.getElementById('ok_qty').value=data;
			if(data==0 || data=='' || data < 0){
				document.getElementById("dispimeifield").style.display="none";
				/*document.getElementById("new_imei1").className="alphanumeric form-control";
				if(sim_type != "C" && sim_type != "G"){
					document.getElementById("new_imei2").style.display="none";
				}*/
			}else{
				document.getElementById("dispimeifield").style.display="";
				document.getElementById("new_imei1").className="required alphanumeric form-control";
				if(sim_type == "C" || sim_type == "G"){
					document.getElementById("new_imei2").style.display="none";
					document.getElementById("new_imei1").onblur = "";
				}else{
					document.getElementById("new_imei1").onblur = function() {get_secondimei(this.value)};

				}
			}
		}
	  });
  }
  ////// get second imei/serial no.
  function get_secondimei(val){
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{postimei1:val},
		success:function(data){
			if(data){
				document.getElementById("new_imei2").value=data;
				document.getElementById("new_imei2").readOnly = true;
			}else{
				alert("Second IMEI/Serial No. is not found in database. Please enter second IMEI/Serial No. manually.");
				document.getElementById("new_imei2").readOnly = false;
			}
		}
	  });
  }
  ///////apply loading message after hitting save button
 /*$('#savejob').on('click', function() {
    var $this = $(this);
  	$this.button('loading');
    setTimeout(function() {
       $this.button('reset');
   }, 8000);
 }); */
$('#savejob').on('click', function() {
  $('#frm1').submit(function(){
    $("input[type='submit']", this)
      .val("Please Wait...")
      .attr('disabled', 'disabled');
    return true;
  });
});
  </script>
  <script type="text/javascript" src="../js/jquery.validate.js"></script>
  <body>
  <div class="container-fluid">
    <div class="row content">
      <?php 
    include("../includes/leftnavemp2.php");
    ?>
      <div class="<?=$screenwidth?>">
        <h2 align="center"><i class="fa fa-wrench"></i> Job Repair</h2>
        <h4 align="center">Job No.-
          <?=$docid?>
        </h4>
        <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-id-card"></i> Customer Details</a></li>
            <li><a data-toggle="tab" href="#menu1"><i class="fa fa-desktop"></i> Product Details</a></li>
            <li><a data-toggle="tab" href="#menu2"><i class="fa fa-pencil-square-o"></i> Observation</a></li>
            <li><a data-toggle="tab" href="#menu3"><i class="fa fa-wrench"></i> Repair Action</a></li>
            <li><a data-toggle="tab" href="#menu4"><i class="fa fa-gear"></i> History</a></li>
          </ul>
          <div class="tab-content">
            <div id="home" class="tab-pane fade in active">
              <table class="table table-bordered" width="100%">
                <tbody>
                  <tr>
                    <td width="20%"><label class="control-label">Customer Name</label></td>
                    <td width="30%"><?php echo $job_row['customer_name'];?></td>
                    <td width="20%"><label class="control-label">Address</label></td>
                    <td width="30%"><?php echo $job_row['address'];?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Contact No.</label></td>
                    <td><?php echo $job_row['contact_no'];?></td>
                    <td><label class="control-label">Alternate Contact No.</label></td>
                    <td><?php echo $job_row['alternate_no'];?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">State</label></td>
                    <td><?php echo getAnyDetails($job_row["state_id"],"state","stateid","state_master",$link1);?></td>
                    <td><label class="control-label">Email</label></td>
                    <td><?php echo $job_row['email'];?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">City</label></td>
                    <td><?php echo getAnyDetails($job_row["city_id"],"city","cityid","city_master",$link1);?></td>
                    <td><label class="control-label">Pincode</label></td>
                    <td><?php echo $job_row['pincode'];?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Customer Type</label></td>
                    <td><?php echo $job_row['customer_type'];?></td>
                    <td><label class="control-label"></label></td>
                    <td><?php ?></td>
                  </tr>
                  <tr>
                    <td colspan="4" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'">
                      &nbsp;
                      <button title="Next" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#menu1'">Next >></button></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div id="menu1" class="tab-pane fade">
              <table class="table table-bordered" width="100%">
                <tbody>
                  <tr>
                    <td width="20%"><label class="control-label">Product</label></td>
                    <td width="30%"><?php echo getAnyDetails($job_row["product_id"],"product_name","product_id","product_master",$link1);?></td>
                    <td width="20%"><label class="control-label">Brand</label></td>
                    <td width="30%"><?php echo getAnyDetails($job_row["brand_id"],"brand","brand_id","brand_master",$link1);?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Model</label></td>
                    <td><?=$job_row['model']?></td>
                    <td><label class="control-label">Accessory Present</label></td>
                    <td><?php echo $job_row['acc_rec'];?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">IMEI 1/Serial No. 1</label></td>
                    <td><?=$job_row['imei']?></td>
                    <td><label class="control-label">IMEI 2/Serial No. 2</label></td>
                    <td><?=$job_row['sec_imei']?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Job Type</label></td>
                    <td><?=$job_row['call_type']?></td>
                    <td><label class="control-label">Job For</label></td>
                    <td><?=$$job_row['call_for']?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Purchase Date</label></td>
                    <td><?=$job_row['dop']?></td>
                    <td><label class="control-label">Activation Date</label></td>
                    <td><?=$$job_row['activation']?></td>
                  </tr>
                  <tr>
                    <td colspan="4" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'">
                      &nbsp;
                      <button title="Previous" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#home'"><< Previous</button>
                      &nbsp;
                      <button title="Next" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#menu2'">Next >></button></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div id="menu2" class="tab-pane fade">
              <table class="table table-bordered" width="100%">
                <tbody>
                  <tr>
                    <td width="20%"><label class="control-label">Initial Symptom</label></td>
                    <td width="30%"><?php echo $job_row['symp_code'];?></td>
                    <td width="20%"><label class="control-label">Physical Condition</label></td>
                    <td width="30%"><?php echo $job_row['phy_cond'];?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">ELS Status</label></td>
                    <td><?=$job_row['els_status']?></td>
                    <td><label class="control-label">Warranty Status</label></td>
                    <td><?php echo $job_row['warranty_status'];?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Dealer Name</label></td>
                    <td><?=$job_row['dname']?></td>
                    <td><label class="control-label">Invoice No</label></td>
                    <td><?=$job_row['inv_no']?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">VOC</label></td>
                    <td><?=$job_row['voc1']?></td>
                    <td><?=$job_row['voc2']?></td>
                    <td><?=$job_row['voc3']?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Remark </label></td>
                    <td colspan="3"><?=$job_row['remark']?></td>
                  </tr>
                  <tr>
                    <td colspan="4" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'">
                      &nbsp;
                      <button title="Previous" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#menu1'"><< Previous</button>
                      &nbsp;
                      <button title="Next" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#menu3'">Next >></button></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div id="menu3" class="tab-pane fade">
            <form  name="frm1" id="frm1" class="form-horizontal" action="sfr_job_repair_save.php" method="post"><br/>
              <div class="form-group">
                <div class="col-md-6"><label class="col-md-5">Repair Status <span class="red_small">*</span></label>
                  <div class="col-md-6">
                    <select name="jobstatus" id="jobstatus" onChange="return change_stat(this.value);" class="required form-control" style="width:250px;" required>
                      <option value="" selected>--Select Repair Result--</option>
                      <?php
							 $res_jobstatus = mysqli_query($link1,"select status_id, display_status from jobstatus_master where main_status_id='4' and system_status in ('BER','EP','PNA','QC Pending','RWR','Replacement') order by display_status");
							 while($row_jobstatus = mysqli_fetch_assoc($res_jobstatus)){
							?>
                      <option value="<?=$row_jobstatus['status_id']?>" <?php if($job_result['status'] == $row_jobstatus['status_id']){echo 'selected';}?>>
                      <?=$row_jobstatus['display_status']?>
                      </option>
                      <?php
							 }
							 ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-6"><label class="col-md-6">Repair Remark</label>
                  <div class="col-md-6">
                    <textarea name="rep_remark" id="rep_remark" class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:none"></textarea>
                  </div>
                </div>
              </div><!--close form group-->
              <div class="form-group">
                <div class="col-md-12">
                  <div id="part_consumption" style="display:none"><br/>
                    <table width="100%" class="table table-bordered">
                      <thead>
                        <tr>
                          <td width="25%"><strong>Fault Code</strong></td>
                          <td width="25%"><strong>Repair Code</strong></td>
                          <td width="30%"><strong>Part</strong></td>
                          <td width="10%"><strong>Price</strong></td>
                          <td width="10%"><strong>Available QTY</strong></td>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>
                              <select name="fault_code[0]" id="fault_code[0]" style="width:230px;" class="form-control" onChange="getRepairDropDown(this.value,'0')">
                                  <option value="" selected="selected">Select Fault Code</option>
                                  <?php  $z=0; while($z<count($symp_arr)){?>
                                    <option value="<?=$symp_arr[$z][0]?>"><?=$symp_arr[$z][1]?> (<?=$symp_arr[$z][0]?>)</option>
                                  <?php $z++;}?>
                              </select>
                          </td>
                          <td>
                          	<span id="repDiv0">
                            <select name="repair_code[0]" id="repair_code[0]" style="width:230px;" class="form-control" disabled="disabled" onChange="return getPartDropDown(this.value,'0');">
                            	<option value="" selected="selected"> Select Repair code</option>
                          	</select>
                            </span><input name="repair_level[0]" id="repair_level[0]" class="form-control" type="hidden" readonly/>
                          </td>
                          <td>
                          	<span id="partDiv0">
                            <select name="part[0]" id="part[0]" disabled="disabled" style="width:300px" class="form-control" onChange="getstockable(this.value,'0');">
                            	<option value="" selected="selected"> Select Part</option>
                          	</select>
                            </span>
                          </td>
                          <td><input name="part_price[0]" id="part_price[0]" class="form-control" type="text" style="width:100px;text-align:right" readonly/></td>
                          <td><input name="avlqty[0]" id="avlqty[0]" class="form-control" type="text" style="width:100px;text-align:right" readonly/></td>
                        </tr>
                        </tbody>
                    </table>
                    <div id="myDiv"></div>
                    <div align="left" id="add_row"><i class="fa fa-plus fa-lg" onClick="addNewRow(0);" title="Add new part for use"></i> <strong>&nbsp;ADD NEW ROW</strong><input name="theValue" type="hidden" class="form-control" id="theValue" value="0" /></div>
                     
                  </div>
                  <!-- Start Div For Send For Repair . It will display only when repair status select as SFR -->
                  <div id="sfr" style="display:none"><br/>
                    <table width="100%" class="table">
                      <tbody>
                        <tr>
                          <td width="20%"><strong>Repair Center <span class="red_small">*</span></strong></td>
                          <td width="30%"><select name="send_for" id="send_for" class="required form-control" style="width:250px;" required>
                              <option value="" selected="selected"> Select Repair Center</option>
                              <?php
    							$rs_sendfor = mysqli_query($link1,"select location_code, locationname, cityid, stateid from location_master where locationtype in ('L3','L4') and statusid='1'")or die("error2".mysqli_error($link1));
    							while($row_sendfor = mysqli_fetch_array($rs_sendfor)){
    						  ?>
                              <option value="<?=$row_sendfor["location_code"]?>"><?=$row_sendfor["locationname"].", ".getAnyDetails($row_sendfor["cityid"],"city","cityid","city_master",$link1)." (".$row_sendfor["location_code"].")"?></option>
                              <?php
    }
    ?>
                            </select></td>
                          <td width="20%">&nbsp;</td>
                          <td width="30%">&nbsp;</td>
                        </tr>
                        <tr>
                          <td width="20%"><strong>Fault Code <span class="red_small">*</span></strong></td>
                          <td width="30%"><select name="fault_code_sfr" id="fault_code_sfr" style="width:250px;"  class="required form-control" >
                              <option value="" selected="selected">Select Fault Code</option>
                              <?php  $z=0; while($z<count($symp_arr)){?>
                              <option value="<?=$symp_arr[$z][0]?>">
                              <?=$symp_arr[$z][1]?> (<?=$symp_arr[$z][0]?>)</option>
                              <?php $z++;}?>
                            </select></td>
                          <td width="20%"><strong>Repair Code <span class="red_small">*</span></strong></td>
                          <td width="30%"><select name="repair_code_sfr" id="repair_code_sfr" style="width:250px;" class="required form-control"  >
                                  <option value="" selected="selected"> Select Repair code</option>
                                   <option value="SFR" selected="selected"> Send For Repair</option>
                                </select></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <!-- Start Div For Part Not Available . It will display only when repair status select as PNA -->
                  <div id="pna" style="display:none">
                    <table width="100%" class="table">
                  	  <thead>	
                        <tr>
                    		<td><strong>Pending Part</strong></td>
                  		</tr>
                      </thead>
                      <tbody>  
                  		<tr>
                    		<td>
                    			<select name="pending_part[0]" class="form-control"  id="pending_part[0]" style="width:250px;">
        							<option value="" selected="selected"> Select Pending Part</option>
        							<?php
									$rs_pna = mysqli_query($link1,"select pm.partcode, pm.part_name, pm.part_category from partcode_master pm, client_inventory ci where pm.model_id like '%".$job_row['model_id']."%' and pm.status='1' and ci.partcode!=pm.partcode and ci.okqty > 0 and ci.location_code='".$_SESSION['asc_code']."' order by pm.part_name")or die("error in pna dropdown".mysqli_error($link1));
									while($row_pna = mysqli_fetch_array($rs_pna)){
									?>
                        			<option value="<?=$row_pna[0]."~".$row_pna[2]?>"><?=$row_pna[1]."-".$row_pna[2]?> (<?=$row_pna[0]?> ) </option>
                        			<?php
                        			}	
                        			?>
      							</select>
      						</td>
                  		</tr>
                      </tbody>  
                	</table>
              		<div id="myDivPna"></div>
                    <div align="left" id="add_rowpna"><i class="fa fa-plus fa-lg" onClick="addNewRowPna(0);" title="Add new part for PNA"></i> <strong>&nbsp;ADD NEW PART</strong><input name="theValuePna" type="hidden" class="form-control" id="theValuePna" value="0" /></div>
                  </div>
                  <!-- Start Div For Part Estimation. It will display only when repair status select as EP -->
                  <div id="ep" style="display:none"><br/>
                    <table width="100%" class="table table-bordered">
                     <thead> 
                      <tr>
                        <td width="35%"><strong>Estimated Part</strong></td>
                        <td width="15%"><strong>Part Cost</strong></td>
                        <td width="15%"><strong>Tax %</strong></td>
                        <td width="15%"><strong>Tax Amt</strong></td>
                        <td width="20%"><strong>Total Amt</strong></td>
                      </tr>
                     </thead>
                     <tbody> 
                      <tr>
                        <td>
                        <select name="esti_part[0]" class="form-control"  id="esti_part[0]" style="width:250px;" onChange="getData_esti(this.value,'0');">
                           <option value="" selected="selected"> Select Estimate Part</option>
                           <?php
                            $z=0;
                            while($z<count($parcode_arr)){?>
                           <option value="<?=$parcode_arr[$z][0]?>"><?=$parcode_arr[$z][1]?> ( <?=$parcode_arr[$z][0]?> )</option>
                           <?php
                            $z++;
                            }
                            ?>
                        </select></td>
                        <td><input name="ep_cost[0]" id="ep_cost[0]" type="text"  class="form-control" style="width:100px;text-align:right" value="0.00" readonly /><input name="ep_hsn_code[0]" id="ep_hsn_code[0]" type="hidden"  class="form-control" style="width:100px;text-align:right" value="" readonly /></td>
                        <td><input name="ep_taxper[0]" id="ep_taxper[0]" type="text"  class="form-control" style="width:75px;text-align:right" value="0.00" readonly /></td>
                        <td><input name="ep_taxamt[0]" id="ep_taxamt[0]" type="text"  class="form-control" style="width:80px;text-align:right" value="0.00" readonly /></td>
                        <td><input name="ep_totalamt[0]" id="ep_totalamt[0]" type="text"  class="form-control" style="width:100px;text-align:right" value="0.00" readonly /></td>
                      </tr>
                  	</tbody>
                  </table>
               	  <div id="myDivEp"></div>
                  <div align="left" id="add_rowep"><i class="fa fa-plus fa-lg" onClick="addNewRowEp(0);row_count_ep();" title="Add new row for EP"></i> <strong>&nbsp;ADD NEW ROW</strong><input name="theValueEp" type="hidden" class="form-control" id="theValueEp" value="0" /></div>
                  <br/>              
              	  <table width="100%" class="table">                 
                   <tbody>
                   <tr>
    				<td width="35%"><div style="width:250px;"><strong>Service Charge</strong></div></td>
    				<td width="15%"><input name="ser_charge" type="text" class="number form-control" id="ser_charge" onBlur="getCost_service()" style="width:100px;" /><input name="ser_tax_hsn" type="hidden" class="form-control" id="ser_tax_hsn" value="998716" readonly style="width:100px;"/></td>
                    <td width="15%"><input name="ser_tax_per" type="text" class="form-control" id="ser_tax_per" value="18.00"  readonly="readonly" style="width:75px;" /></td>
                    <td width="15%"><input name="ser_tax_amt" type="text" class="form-control" id="ser_tax_amt"  value="0.00" readonly style="width:80px;" /></td>
    				<td width="20%"><input name="total_ser_tax_amt" type="text" class="form-control" id="total_ser_tax_amt"  value="0.00" readonly style="width:100px;" /></td>
  				   </tr>
  			      </tbody>
  			  </table>
              <table width="100%" class="table">
                 <tbody>  
                  <tr>
                    <td width="10%"><strong>Part Cost</strong></td>
                    <td width="25%"><input name="tot_cost" type="text" class="form-control" id="tot_cost" value="0.00" readonly style="width:100px;text-align:right"/></td>
                    <td width="10%"><strong>Tax Amt</strong></td>
                    <td width="20%"><input name="taxtotal" type="text" class="form-control" id="taxtotal" value="0.00" readonly style="width:100px;text-align:right"  /></td>
                    <td width="10%"><strong>Total Amt</strong></td>
                    <td width="25%"><input name="grandtotal" type="text" class="form-control" id="grandtotal" value="0.00" readonly style="width:120px;text-align:right"/>
                    <input name="ep_new_es" type="hidden" class="form-control" id="ep_new_es" value="0.00" readonly/></td>
                  </tr>
                 </tbody> 
                </table>
                  </div>
                  <!-- Start Div For SFR . It will display only when repair status select as SFR -->
                  <div id="SFR" style="display:none"> </div>
                  <div id="replacement" style="display:none"><br/>
                  	<table width="100%" class="table table-bordered">
                	  <thead>	
                        <tr>
                  			<td width="20%"><strong>Fault Code</strong></td>
                  			<td width="20%"><strong>Repair Code</strong></td>
                  			<td width="20%"><strong>Replace Model</strong></td>
                   			<td width="10%"><strong>Replace By</strong></td>
                            <td width="10%"><strong>Available Stock</strong></td>
                  			<td width="20%"><strong>Replace IMEI/Serial No.</strong></td>
                		</tr>
                      </thead>
                      <tbody>  
                		<tr>
                  			<td>
                            	<select name="fault_code_replace" id="fault_code_replace" style="width:200px;"  class="required form-control">
                      				<option value="" selected="selected">Select Fault Code</option>
                      				<?php  $z=0; while($z<count($symp_arr)){?>
                      				<option value="<?=$symp_arr[$z][0]?>"><?=$symp_arr[$z][1]?> (<?=$symp_arr[$z][0]?>)</option>
                      				<?php $z++;}?>
                    			</select></td>
                  			<td>
                    			<select name="repair_code_replace" id="repair_code_replace" style="width:200px;" class="required form-control">
                                 	<option value="" selected="selected"> Select Repair code</option>
                                   	<?php 
									$sql = "SELECT rep_code,rep_desc,rep_level FROM repaircode_master where rep_desc = 'Replacement' and status='Active'";
									$res = mysqli_query($link1,$sql);
									if(mysqli_num_rows($res)>0){
										while($row = mysqli_fetch_array($res)){
									?>		
									<option value="<?=$row[0]."~".$row[2]?>"><?php echo $row[1]."(".$row[0].")";?></option>
									<?php
										}
									}
									?>                                    
                    			</select></td>
                  			<td>
								<?php
                                $sql_model = "SELECT model_id, model FROM model_master where status='1' order by model";
                                $res_model = mysqli_query($link1,$sql_model);
                                ?>
                              	<select  name="replace_model" class="form-control" id="replace_model" style="width:150px;" onChange="replace_type(this.value);">
                                	<option value="" >Please Select</option>
                                <?php
								while($row_model = mysqli_fetch_array($res_model)){
								?>
                                	<option value="<?=$row_model['model_id']?>"><?php echo $row_model['model']?></option>
								<?php
                                }
								?>
                              	</select></td>
                     		<td><div id="replacedDiv">
                                <select  name="rep_part" class="form-control" id="rep_part" style="width:150px;">
                                  	<option value="">Please Select</option>
                                </select>
                              </div></td>
                            <td>
                                <input name="ok_qty" id="ok_qty" type="text" class="form-control" readonly maxlength="15" value="0" style="width:80px;"/></td>
                  			<td id="dispimeifield" style="display:none;">
                            	<input name="new_imei1" id="new_imei1" type="text" class="alphanumeric form-control"  style="width:200px;" placeholder="Enter IMEI1/Serial No.1"/>
                    			<input name="new_imei2" id="new_imei2" type="text" class="alphanumeric form-control" style="width:200px;" placeholder="Enter IMEI2/Serial No.2"/>
                            </td>
               	 		</tr> 
              		</table>
                  </div>
                </div>
              </div><!--close form group-->
              <div class="form-group">
                <div class="col-md-12" align="center">
                <span id="errmsg" class="red_small"></span>
                <br/>
                  <input name="savejob" id="savejob" type="submit" class="btn btn-success" value="Save" title="Save this job">
                  <input name="postjobno" type="hidden" class="form-control" id="postjobno" value="<?=base64_encode($job_row['job_no'])?>"/>
                  <br/><br/>
                  <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'">
                  &nbsp;
                  <button title="Previous" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#menu2'"><< Previous</button>
                  &nbsp;
                  <button title="Next" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#menu4'">Next >></button>
                </div>
              </div>
              </form>
            </div>
            <div id="menu4" class="tab-pane fade">
              <table class="table table-bordered" width="100%">
                <thead>	
                  <tr>
                    <td width="15%"><strong>Location</strong></td>
                    <td width="10%"><strong>Activity</strong></td>
                    <td width="15%"><strong>Outcome</strong></td>
                    <td width="10%"><strong>Warranty</strong></td>
                    <td width="10%"><strong>Job Status</strong></td>
                    <td width="10%"><strong>Update By</strong></td>
                    <td width="20%"><strong>Remark</strong></td>
                    <td width="10%"><strong>Update on</strong></td>
                  </tr>
                </thead>
                <tbody>
                <?php
				$res_jobhistory = mysqli_query($link1,"SELECT * FROM call_history where job_no='".$docid."'");
				while($row_jobhistory = mysqli_fetch_assoc($res_jobhistory)){
				?>
                  <tr>
                    <td><?=$row_jobhistory['location_code']?></td>
                    <td><?=$row_jobhistory['activity']?></td>
                    <td><?=$row_jobhistory['outcome']?></td>
                    <td><?=$row_jobhistory['warranty_status']?></td>
                    <td><?=$row_jobhistory['status']?></td>
                    <td><?=$row_jobhistory['updated_by']?></td>
                    <td><?=$row_jobhistory['remark']?></td>
                    <td><?=$row_jobhistory['update_on']?></td>
                  </tr>
                  <?php
				}
				  ?>
                  <tr>
                    <td colspan="8" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'">
                      &nbsp;
                      <button title="Previous" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#menu3'"><< Previous</button>
                      &nbsp; </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!--End form group--> 
    </div>
    <!--End col-sm-9--> 
  </div>
  <!--End row content-->
  </div>
  <!--End container fluid-->
  <?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>