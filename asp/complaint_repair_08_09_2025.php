<?php
require_once("../includes/config.php");
$_SESSION['messageIdent_sfr'] ="";
$_SESSION['messageIdent_pna'] = "";
$_SESSION['messageIdent_con'] = "";
$_SESSION['messageIdent_repl'] = "";
//$arrstatus = getFullStatus("process",$link1);
$docid=base64_decode($_REQUEST['refid']);
//// job details
$job_sql="SELECT * FROM jobsheet_data where job_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
if($job_row['call_for']!='Workshop'){$page_link='job_list';}
else { $page_link='job_list_asp';}
/////some security check should get from model master
$model_flags = explode("~",getAnyDetails($job_row['model_id'],"make_doa,repairable,replacement,replace_days,out_warranty,wp,ser_charge","model_id","model_master",$link1));
//print_r($model_flags);
$replc_daycount = daysDifference($job_row['open_date'],$job_row['dop']);
///// get repair level
$replvl = getAnyDetails($_SESSION['id_type'],"rep_level","usedname","location_type_master",$link1);
############################################ Symptom Code #######################################################
/*$rs_symp=mysqli_query($link1,"select * from symptom_master where status='1'  order by symp_desc")or die("Error-> in symptom code".mysqli_error($link1));
if(mysqli_num_rows($rs_symp)>0){
	$symp_arr[][]=array();
	$j=0;
	while($row_symp=mysqli_fetch_array($rs_symp)){
		$symp_arr[$j][0]=$row_symp['symp_code'];
		$symp_arr[$j][1]=$row_symp['symp_desc'];
		$j++;
	}
}else{}*/


############################################ Condition Code #######################################################
//echo "select * from condition_master where status='1' and product_id='".$job_row['product_id']."' order by  	condition_desc ";
/*$rs_cond=mysqli_query($link1,"select * from condition_master where status='1'  order by  	condition_desc ")or die("Error-> in condition code".mysqli_error($link1));
if(mysqli_num_rows($rs_cond)>0){
	$cond_arr[][]=array();
	$jk=0;
	while($row_cond=mysqli_fetch_array($rs_cond)){
		$cond_arr[$jk][0]=$row_cond['condition_code'];
		$cond_arr[$jk][1]=$row_cond['condition_desc'];
		$jk++;
	}
}else{}*/

############################################ Section Code #######################################################

/*$rs_sec=mysqli_query($link1,"select * from section_master where status='1'  order by  	section_desc ")or die("Error-> in section code".mysqli_error($link1));
if(mysqli_num_rows($rs_sec)>0){
	$sec_arr[][]=array();
	$jk=0;
	while($row_sec=mysqli_fetch_array($rs_sec)){
		$sec_arr[$jk][0]=$row_sec['section_code'];
		$sec_arr[$jk][1]=$row_sec['section_desc'];
		$jk++;
	}
}else{}*/
//////////////////////////////////////////////////////
$rs_def=mysqli_query($link1,"select distinct(defect_desc) ,defect_code from defect_master where status='1' and  mapped_product  like '%".$job_row['product_id']."%'  group by defect_desc  order by  	defect_desc  ")or die("Error-> in defect code".mysqli_error($link1));
if(mysqli_num_rows($rs_def)>0){
	$def_arr[][]=array();
	$jk=0;
	while($row_def=mysqli_fetch_array($rs_def)){
		$def_arr[$jk][0]=$row_def['defect_code'];
		$def_arr[$jk][1]=$row_def['defect_desc'];
		$jk++;
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
  //// date difference

function date_difference(enddate,startdate){

	var end_date = (enddate).split("-");

	var start_date = (startdate).split("-");	

	var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds

	var firstDate = new Date(start_date[0], start_date[1], start_date[2]);

	var secondDate = new Date(end_date[0], end_date[1], end_date[2]);

	/////calculate days

	var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime()) / (oneDay)));

	return diffDays;

}
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


////////////////////Close type Selection//////////

function change_closetype (val){
if(val=='Nothing'){

var span = document.getElementById("customer_span");
span.textContent = "*";
span.classList.add("red_small");

document.getElementById("customer_satis").style.display="";
document.getElementById("customer_satis1").style.display="";
document.getElementById("warranty_status").value="VOID";
document.getElementById("custumer_pay").className="required form-control";
document.getElementById("handset_img").className="";
document.getElementById("invoice_no").className="";
document.getElementById("serial_no").className="";
document.getElementById("warrantycard").className="";
var span = document.getElementById("document_type");
span.textContent = "";
span.classList.remove("red_small");
document.getElementById("serialno").style.display="none";
document.getElementById("warranty_card").style.display="none";
document.getElementById("invoiceno").style.display="none";
//document.getElementById("manufacterdate").style.display="none";
document.getElementById("iddoc").style.display="none";
}
else if(val=='Serial No'){

document.getElementById("serial_no").className=" form-control";
document.getElementById("handset_img").className="form-control";
document.getElementById("customer_satis").style.display="none";
document.getElementById("customer_satis1").style.display="none";
var span = document.getElementById("document_type");
span.textContent = "*";
span.classList.add("red_small");
document.getElementById("iddoc").style.display="";
document.getElementById("serialno").style.display="";
document.getElementById("warranty_card").style.display="none";
document.getElementById("invoiceno").style.display="none";
//document.getElementById("manufacterdate").style.display="none";
document.getElementById("iddoc").style.display="";
}
else if(val=='Invoice'){

document.getElementById("invoice_no").className=" form-control";
document.getElementById("handset_img").className=" form-control";
document.getElementById("customer_satis").style.display="none";
document.getElementById("customer_satis1").style.display="none";
var span = document.getElementById("document_type");
span.textContent = "*";
span.classList.add("red_small");
document.getElementById("iddoc").style.display="";
document.getElementById("serialno").style.display="none";
document.getElementById("warranty_card").style.display="none";
document.getElementById("invoiceno").style.display="";
//document.getElementById("manufacterdate").style.display="none";
document.getElementById("iddoc").style.display="";
}
else if(val=='Warranty Card'){
document.getElementById("warrantycard").className="required form-control";
document.getElementById("handset_img").className=" form-control";
document.getElementById("customer_satis").style.display="none";
document.getElementById("customer_satis1").style.display="none";
var span = document.getElementById("document_type");
span.textContent = "*";
span.classList.add("red_small");
document.getElementById("iddoc").style.display="";
document.getElementById("serialno").style.display="none";
document.getElementById("warranty_card").style.display="";
document.getElementById("invoiceno").style.display="none";
//document.getElementById("manufacterdate").style.display="none";
document.getElementById("iddoc").style.display="";
}
else if(val=='Manufacturing date'){
document.getElementById("manufacter_date").className="required form-control";
document.getElementById("handset_img").className=" form-control";
document.getElementById("customer_satis").style.display="none";
document.getElementById("customer_satis1").style.display="none";
var span = document.getElementById("document_type");
span.textContent = "*";
span.classList.add("red_small");
document.getElementById("iddoc").style.display="";
document.getElementById("serialno").style.display="none";
document.getElementById("warranty_card").style.display="none";
document.getElementById("invoiceno").style.display="none";
//document.getElementById("manufacterdate").style.display="";
document.getElementById("iddoc").style.display="";
}
else{

document.getElementById("customer_satis").style.display="none";
document.getElementById("customer_satis1").style.display="none";

}

}
///// function for switching the repair status and their corresponding fields
function change_stat(val){
	//var ws=document.getElementById("warranty_status").value;
	
	if(val=='5'){
		document.getElementById("ep").style.display="";
		document.getElementById("sfr").style.display="none";
		document.getElementById("pna").style.display="none";
		document.getElementById("pending_reason").style.display="none";
		document.getElementById("close_reason").style.display="none";
		document.getElementById("part_dmd").style.display="none";
		document.getElementById("part_consumption").style.display="none";
		document.getElementById("req_app").style.display="none";
		document.getElementById("replacement").style.display="none";
		document.getElementById("demo_install").style.display="none";
		document.getElementById("errmsg").innerHTML = "";
		document.getElementById("savejob").style.display="";
			var span1 = document.getElementById("handset_span");
span1.textContent = "";
span1.classList.remove("red_small");
document.getElementById("handset_img").className=" form-control";
document.getElementById("closetype").className="  form-control";

		
		//document.getElementById("SFR").style.display="none";
	}
	else if(val=='6'){
		////check repairable flag is Y or N

			document.getElementById("ep").style.display="none";
			document.getElementById("pna").style.display="none";
			document.getElementById("sfr").style.display="none";
			document.getElementById("pending_reason").style.display="none";
			//document.getElementById("close_reason").style.display="";
		    document.getElementById("close_reason").className="required";
			document.getElementById("rep_remark").className="required";
			document.getElementById("part_dmd").style.display="none";
			document.getElementById("req_app").style.display="none";
			document.getElementById("part_consumption").style.display="";
			document.getElementById("replacement").style.display="none";
			document.getElementById("demo_install").style.display="none";
			document.getElementById("errmsg").innerHTML = "";
			document.getElementById("savejob").style.display="";
			var span1 = document.getElementById("handset_span");
			var span_rep_rmk = document.getElementById("rep_remark_span");
span1.textContent = "*";
span_rep_rmk.textContent = "*";
span1.classList.add("red_small");
span_rep_rmk.classList.add("red_small");
document.getElementById("handset_img").className=" form-control";
document.getElementById("closetype").className=" required form-control";
			//document.getElementById("SFR").style.display="none";
	
	}
	else if(val=='58'){
		////check repairable flag is Y or N

			document.getElementById("ep").style.display="none";
			document.getElementById("pna").style.display="none";
			document.getElementById("sfr").style.display="none";
			document.getElementById("pending_reason").style.display="none";
			document.getElementById("close_reason").style.display="";
		    document.getElementById("close_reason").className="required";
			document.getElementById("part_dmd").style.display="none";
			document.getElementById("req_app").style.display="none";
			document.getElementById("part_consumption").style.display="";
			document.getElementById("replacement").style.display="none";
			document.getElementById("demo_install").style.display="none";
			document.getElementById("errmsg").innerHTML = "";
			document.getElementById("savejob").style.display="";
			var span1 = document.getElementById("handset_span");
			span1.textContent = "*";
			span1.classList.add("red_small");
			document.getElementById("handset_img").className=" form-control";
			document.getElementById("closetype").className=" required form-control";
			//document.getElementById("SFR").style.display="none";
	
	}
	else if(val=='3'){
		document.getElementById("pna").style.display="";
		document.getElementById("ep").style.display="none";
		document.getElementById("pending_reason").style.display="none";
		document.getElementById("close_reason").style.display="none";
		document.getElementById("part_consumption").style.display="none";
		document.getElementById("req_app").style.display="none";
		document.getElementById("demo_install").style.display="none";
		document.getElementById("sfr").style.display="none";
		document.getElementById("part_dmd").style.display="none";
		document.getElementById("replacement").style.display="none";
		document.getElementById("errmsg").innerHTML = "";
		document.getElementById("savejob").style.display="";
			var span1 = document.getElementById("handset_span");
span1.textContent = "";
span1.classList.remove("red_small");
document.getElementById("handset_img").className=" form-control";
document.getElementById("closetype").className="  form-control";
	
		//document.getElementById("SFR").style.display="none";
	}
	else if(val=='4'){
		document.getElementById("sfr").style.display="";
		document.getElementById("ep").style.display="none";
		document.getElementById("pna").style.display="none";
		document.getElementById("close_reason").style.display="none";
		document.getElementById("pending_reason").style.display="none";
		document.getElementById("part_consumption").style.display="none";
		document.getElementById("part_dmd").style.display="none";
			document.getElementById("demo_install").style.display="none";
			document.getElementById("req_app").style.display="none";
		document.getElementById("replacement").style.display="none";
		document.getElementById("errmsg").innerHTML = "";
		document.getElementById("savejob").style.display="";
		//document.getElementById("SFR").style.display="";
			var span1 = document.getElementById("handset_span");
span1.textContent = "";
span1.classList.remove("red_small");
document.getElementById("handset_img").className=" form-control";
document.getElementById("closetype").className="  form-control";
	}
		else if(val=='48' || val=='49' ){
		document.getElementById("sfr").style.display="none";
		document.getElementById("ep").style.display="none";
		document.getElementById("pna").style.display="none";
		document.getElementById("close_reason").style.display="";
		document.getElementById("pending_reason").style.display="none";
		document.getElementById("part_consumption").style.display="none";
			document.getElementById("demo_install").style.display="";
			document.getElementById("part_dmd").style.display="none";
			document.getElementById("req_app").style.display="none";
		document.getElementById("replacement").style.display="none";
		document.getElementById("errmsg").innerHTML = "";
		document.getElementById("savejob").style.display="";
		
			var span = document.getElementById("handset_span");
span.textContent = "*";
span.classList.add("red_small");
document.getElementById("handset_img").className=" form-control";
document.getElementById("closetype").className=" required form-control";
		//document.getElementById("SFR").style.display="";
	}
	else if(val=='50' ){
		document.getElementById("sfr").style.display="none";
		document.getElementById("ep").style.display="none";
		document.getElementById("pna").style.display="none";
		document.getElementById("close_reason").style.display="none";
		document.getElementById("pending_reason").style.display="none";
		document.getElementById("part_consumption").style.display="none";
		document.getElementById("part_dmd").style.display="none";
			document.getElementById("demo_install").style.display="none";
			document.getElementById("req_app").style.display="";
		document.getElementById("replacement").style.display="none";
		document.getElementById("errmsg").innerHTML = "";
		document.getElementById("savejob").style.display="";
			var span1 = document.getElementById("handset_span");
span1.textContent = "";
span1.classList.remove("red_small");
document.getElementById("handset_img").className=" form-control";
document.getElementById("closetype").className="  form-control";
		//document.getElementById("SFR").style.display="";
	}
		else if(val=='7' ){
		document.getElementById("sfr").style.display="none";
		document.getElementById("ep").style.display="none";
		document.getElementById("pna").style.display="none";
		document.getElementById("close_reason").style.display="none";
		document.getElementById("pending_reason").style.display="";
		document.getElementById("part_consumption").style.display="none";
			document.getElementById("demo_install").style.display="none";
			document.getElementById("part_dmd").style.display="none";
			document.getElementById("req_app").style.display="none";
		document.getElementById("replacement").style.display="none";
		document.getElementById("errmsg").innerHTML = "";
		document.getElementById("savejob").style.display="";
		//document.getElementById("SFR").style.display="";
			var span1 = document.getElementById("handset_span");
span1.textContent = "";
span1.classList.remove("red_small");
document.getElementById("handset_img").className=" form-control";
document.getElementById("closetype").className="  form-control";
	}
	else if(val=='54' ){
		document.getElementById("sfr").style.display="none";
		document.getElementById("ep").style.display="none";
		document.getElementById("pna").style.display="none";
		document.getElementById("close_reason").style.display="none";
		document.getElementById("pending_reason").style.display="";
		document.getElementById("part_consumption").style.display="none";
			document.getElementById("demo_install").style.display="none";
			document.getElementById("part_dmd").style.display="";
			document.getElementById("req_app").style.display="none";
		document.getElementById("replacement").style.display="none";
		document.getElementById("errmsg").innerHTML = "";
		document.getElementById("savejob").style.display="";
			var span1 = document.getElementById("handset_span");
span1.textContent = "";
span1.classList.remove("red_small");
document.getElementById("handset_img").className=" form-control";
document.getElementById("closetype").className="  form-control";
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
	else if(val=='8'){
		///// check replacement of this model is Y or N
		if("<?=$model_flags[2]?>" == "Y"){
			///// check replacement days from model master
			var replcedays = "<?=$replc_daycount?>";
			var modelreplcedays = "<?=$model_flags[3]?>";
			if(parseInt(replcedays) <= parseInt(modelreplcedays)){
				document.getElementById("sfr").style.display="none";
				document.getElementById("ep").style.display="none";
				document.getElementById("pna").style.display="none";
				document.getElementById("close_reason").style.display="none";
				document.getElementById("pending_reason").style.display="none";
				document.getElementById("part_consumption").style.display="none";
					document.getElementById("demo_install").style.display="none";
					document.getElementById("req_app").style.display="none";
					document.getElementById("part_dmd").style.display="none";
				document.getElementById("replacement").style.display="";
				document.getElementById("errmsg").innerHTML = "";
				document.getElementById("savejob").style.display="";
					var span1 = document.getElementById("handset_span");
span1.textContent = "*";
span1.classList.add("red_small");
document.getElementById("handset_img").className=" form-control";
document.getElementById("closetype").className=" required form-control";
			}else{
				document.getElementById("sfr").style.display="none";
				document.getElementById("ep").style.display="none";
				document.getElementById("pna").style.display="none";
				document.getElementById("close_reason").style.display="none";
				document.getElementById("part_consumption").style.display="none";
				document.getElementById("pending_reason").style.display="none";
					document.getElementById("demo_install").style.display="none";
					document.getElementById("req_app").style.display="none";
					document.getElementById("part_dmd").style.display="none";
				document.getElementById("replacement").style.display="none";
				document.getElementById("errmsg").innerHTML = "Replacement days are exceeding for this model.";
				document.getElementById("savejob").style.display="none";
					var span1 = document.getElementById("handset_span");
span1.textContent = "";
span1.classList.remove("red_small");
document.getElementById("handset_img").className=" form-control";
document.getElementById("closetype").className="  form-control";
			}
		}else{
			document.getElementById("sfr").style.display="none";
			document.getElementById("ep").style.display="none";
			document.getElementById("pna").style.display="none";
			document.getElementById("close_reason").style.display="none";
			document.getElementById("req_app").style.display="none";
			document.getElementById("pending_reason").style.display="none";
			document.getElementById("part_consumption").style.display="none";
			document.getElementById("part_dmd").style.display="none";
				document.getElementById("demo_install").style.display="none";
			document.getElementById("replacement").style.display="none";
			document.getElementById("errmsg").innerHTML = "This model is not eligible for replacement.";
			document.getElementById("savejob").style.display="none";
		var span1 = document.getElementById("handset_span");
span1.textContent = "";
span1.classList.remove("red_small");
document.getElementById("handset_img").className=" form-control";
document.getElementById("closetype").className="  form-control";
		}
		
		//document.getElementById("SFR").style.display="none";
		
	}
	else{
		document.getElementById("sfr").style.display="none";
		document.getElementById("ep").style.display="none";
		document.getElementById("pna").style.display="none";
		document.getElementById("close_reason").style.display="none";
		document.getElementById("pending_reason").style.display="none";
			document.getElementById("demo_install").style.display="none";
		document.getElementById("part_consumption").style.display="none";
		document.getElementById("part_dmd").style.display="none";
		document.getElementById("replacement").style.display="none";
		document.getElementById("errmsg").innerHTML = "";
		document.getElementById("savejob").style.display="";
		//document.getElementById("SFR").style.display="none";
			var span1 = document.getElementById("handset_span");
span1.textContent = "";
span1.classList.remove("red_small");
document.getElementById("handset_img").className=" form-control";
document.getElementById("closetype").className="  form-control";
	}
}
//// check warrantty on the basis of els status


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
	   newdiv.innerHTML = '<i class="fa fa-close fa-lg" onclick="return getRowDisable('+num+')" title="Remove this row"></i><table width="auto" class="table table-bordered"><tbody><tr class="<?=$tableheadcolor?>"><td width="20%"><strong>Fault Code</strong></td><td width="20%"><strong>Repair Code</strong></td><td width="20%"><strong>Part</strong></td><td width="10%"><strong>Price</strong></td><td width="10%"><strong>Available QTY</strong></td></tr><tr><td width="25%"><select name="fault_code['+num+']" id="fault_code['+num+']" style="width:230px;" class="form-control" onChange="getRepairDropDown(this.value,'+num+')"><option value="" selected="selected">Select Defect</option><?php  $f=0; while($f<count($def_arr)){?><option value="<?=$def_arr[$f][0]?>"><?=$def_arr[$f][1]?> (<?=$def_arr[$f][0]?>)</option><?php $f++;}?></select></td><td width="25%"><span id="repDiv'+num+'"><select name="repair_code['+num+']" id="repair_code['+num+']" style="width:230px;" class="form-control" disabled="disabled" onChange="return getPartDropDown(this.value,'+num+');"><option value="" selected="selected"> Select Repair code</option></select></span><input name="repair_level['+num+']" id="repair_level['+num+']" class="form-control" type="hidden" readonly/></td><td width="30%"><span id="partDiv'+num+'"><select name="part['+num+']" id="part['+num+']" disabled="disabled" style="width:300px" class="form-control" onChange="getstockable(this.value,'+num+');"><option value="" selected="selected"> Select Part</option></select></span> <div id="dispswapimeifield['+num+']" style="display:none;"><input name="swap_imei1['+num+']" id="swap_imei1['+num+']" type="text"  class="form-control alphanumeric"  maxlength="15" style="width:200px;" placeholder="Enter IMEI1/Serial No.1"/><input name="swap_imei2['+num+']" id="swap_imei2['+num+']"  type="text"  style="width:200px;" class="form-control alphanumeric" placeholder="Enter IMEI2/Serial No.2"/></div></td><td width="10%"><input name="part_price['+num+']" id="part_price['+num+']" class="form-control" type="text" style="width:100px;text-align:right" readonly/></td><td width="10%"><input name="avlqty['+num+']" id="avlqty['+num+']" class="form-control" type="text" style="width:100px;text-align:right" readonly/></td></tr></tbody></table>';
       ni.appendChild(newdiv);
	   ni.appendChild(newdiv);
	    if(num==4){ 
	    document.getElementById('add_row').style.display="none";
	   }
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
/////////////////  updated by priya on 13 july 2018 //////////////////////////////////////////////////
function duplicate_entry(ind,val){
	var max_chk=document.getElementById("theValuePna").value;
	for(var i=0;i<max_chk;i++){
	    var part_id=document.getElementById("pending_part["+i+"]").value;
		var splt = 0;
		splt = part_id.split('~');
	    if(splt[0] ==val && i!=ind){
	       //alert("Please select unique part");
		   document.getElementById("errmsg["+ind+"]").innerHTML = "Please select unique part.";
		   document.getElementById("pending_part["+ind+"]").value='';
	    }
		else {
		document.getElementById("errmsg["+ind+"]").innerHTML = "";
		}
	}
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///// Script For Add new Row for PNA////////////////
function addNewRowPna(findex){
   var ni = document.getElementById('myDivPna');
    var numi = document.getElementById('theValuePna');
    //// increment of 1 value for new added row///
    var num = (document.getElementById("theValuePna").value -1)+ 2;

    //// check if previous row is filled with these three values (fault code,repair code, part)/////
       numi.value = num;
       var divIdName = "my"+num+"DivPna";
	   //alert(divIdName);
       var newdiv = document.createElement('div');
       newdiv.setAttribute("id",divIdName);
	   newdiv.innerHTML = "<i class='fa fa-close fa-lg' onclick='return getRowDisablePna("+num+")' title='Remove this row'></i><table width='100%' class='table table-bordered'><tbody><tr><td><select name='pending_part["+num+"]' class='form-control' id='pending_part["+num+"]' style='width:250px;'  onChange='duplicate_entry("+num+",this.value)';><option value='' selected='selected'> Select Pending Part</option><?php $rs_pna = mysqli_query($link1,"select partcode, part_name, part_category,part_desc,vendor_partcode from partcode_master  where  model_id like '%".$job_row['model_id']."%' and status='1'  order by part_name")or die("error in pna dropdown".mysqli_error($link1));while($row_pna = mysqli_fetch_array($rs_pna)){?><option value='<?=$row_pna[0]?>'><?=$row_pna[3]."-".$row_pna[2]?> (<?=$row_pna[4]?> ) </option><?php }?></select><span id='errmsg["+num+"]' name='errmsg["+num+"]' class='red_small'></span></td></tr></tbody></table>";
       ni.appendChild(newdiv);
	    if(num==4){ 
	    document.getElementById('add_rowpna').style.display="none";
	   }
	
}
///// Close Script For Add new Row for PNA////////////////
//// Disable/Hide Part Row //////
function getRowDisablePna(ind) {
	 // hide My Div \\
    document.getElementById("my"+ind+"DivPna").style.display="none";
	// Reset Value\\
	document.getElementById("pending_part"+"["+ind+"]").value="";
}

///// Script For Add new Row for Engineer Part Demand////////////////
function addNewrowPdmd(findex){
   var ni = document.getElementById('myDivPartDMD');
    var numi = document.getElementById('theValuePdmd');
    //// increment of 1 value for new added row///
    var num = (document.getElementById("theValuePdmd").value -1)+ 2;

    //// check if previous row is filled with these three values (fault code,repair code, part)/////
       numi.value = num;
       var divIdName = "my"+num+"DivPartDMD";
	   //alert(divIdName);
       var newdiv = document.createElement('div');
       newdiv.setAttribute("id",divIdName);
	   newdiv.innerHTML = "<i class='fa fa-close fa-lg' onclick='return getRowDisablePdmd("+num+")' title='Remove this row'></i><table width='100%' class='table table-bordered'><tbody><tr><td><select name='dmd_part["+num+"]' class='form-control' id='dmd_part["+num+"]' style='width:250px;'  onChange='duplicate_entry("+num+",this.value)';><option value='' selected='selected'> Select  Part</option><?php $rs_pna = mysqli_query($link1,"select partcode, part_name, part_category,part_desc,vendor_partcode from partcode_master  where  model_id like '%".$job_row['model_id']."%' and status='1'      and partcode not in  ( select partcode from user_inventory  where location_code='".$_SESSION['asc_code']."'  and  	locationuser_code='".$job_row['eng_id']."' and okqty > 0 )  order by part_name")or die("error in pna dropdown".mysqli_error($link1));while($row_pna = mysqli_fetch_array($rs_pna)){?><option value='<?=$row_pna[0]?>'><?=$row_pna[3]."-".$row_pna[2]?> (<?=$row_pna[4]?> ) </option><?php }?></select><span id='errmsg["+num+"]' name='errmsg["+num+"]' class='red_small'></span></td></tr></tbody></table>";
       ni.appendChild(newdiv);
	    if(num==4){ 
	    document.getElementById('add_rowDmd').style.display="none";
	   }
	
}

//// Disable/Hide Part Row //////
function getRowDisablePdmd(ind) {
	 // hide My Div \\
    document.getElementById("my"+ind+"DivPartDMD").style.display="none";
	// Reset Value\\
	document.getElementById("dmd_part"+"["+ind+"]").value="";
}

///// Script For Add new Row for Demo/install////////////////
function addNewRowdemo(findex){
   var ni = document.getElementById('myDivDemo_install');
    var numi = document.getElementById('theValuedemo');
    //// increment of 1 value for new added row///
    var num = (document.getElementById("theValuedemo").value -1)+ 2;

    //// check if previous row is filled with these three values (fault code,repair code, part)/////
       numi.value = num;
       var divIdName = "my"+num+"DivDemo_install";
	   //alert(divIdName);
       var newdiv = document.createElement('div');
       newdiv.setAttribute("id",divIdName);
	   newdiv.innerHTML = "<i class='fa fa-close fa-lg' onclick='return getRowDisabledemo("+num+")' title='Remove this row'></i><table width='100%' class='table table-bordered'><tbody><tr><td><select name='req_part["+num+"]' class='form-control' id='req_part["+num+"]' style='width:250px;'  onChange='duplicate_entry("+num+",this.value)';><option value='' selected='selected'> Select  Part</option><?php $rs_as = mysqli_query($link1,"select partcode, part_name, part_category,part_desc,vendor_partcode from partcode_master  where  model_id like '%".$job_row['model_id']."%' and status='1'      and partcode  in  ( select partcode from user_inventory  where location_code='".$_SESSION['asc_code']."' and locationuser_code='".$job_row['eng_id']."' and  okqty > 0  ) and part_category='ACCESSORY' order by part_name")or die("error in pna dropdown".mysqli_error($link1));while($row_as = mysqli_fetch_array($rs_as)){?><option value='<?=$row_as[0]?>'><?=$row_as[3]."-".$row_as[2]?> (<?=$row_as[4]?> ) </option><?php }?></select><span id='errmsg["+num+"]' name='errmsg["+num+"]' class='red_small'></span></td></tr></tbody></table>";
       ni.appendChild(newdiv);
	    if(num==4){ 
	    document.getElementById('add_demo').style.display="none";
	   }
	
}
///// Close Script For Add new Row for PNA////////////////
//// Disable/Hide Part Row //////
function getRowDisabledemo(ind) {
	 // hide My Div \\
    document.getElementById("my"+ind+"DivDemo_install").style.display="none";
	// Reset Value\\
	document.getElementById("req_part"+"["+ind+"]").value="";
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
		data:{symptomcode:val, replevel:'<?=$replvl?>', indxno:indx,product_id:'<?=$job_row['product_id']?>'},
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
  /////////////////  updated by priya on 13 july 2018 //////////////////////////////////////////////////
   var part_det="";
    if(indx>0){
    var totrows=document.getElementById('theValue').value;
		 for(var i=0; i<totrows; i++){		
 			var part_desc_val=document.getElementById("part["+i+"]").value;
			var part_desc=part_desc_val.split("^");
			var part_no=part_desc[0];
			  if(part_det==''){
				  part_det=part_no;
			  }else{
				  part_det = part_det.concat(",",part_no);
			  }
		  
		  }
	  }
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{repaircode:val, modelcode111:'<?=$job_row['model_id']?>',engid:'<?=$job_row['eng_id']?>', locationcode:'<?=$_SESSION['asc_code']?>',job_no:'<?=$docid?>', indxno:indx, part_inf:part_det},
		success:function(data){
		var splitPart=data.split("~");
	
	  	if(splitPart[0]!="" && splitPart[0]!=0 && splitPart[2]=="Y"){

			document.getElementById("partDiv"+splitPart[1]).innerHTML=splitPart[0];
			document.getElementById("repair_level["+splitPart[1]+"]").value=splitPart[3];
			if(val=='R0086'){
			 
			 document.getElementById("dispswapimeifield["+splitPart[1]+"]").style.display='';
			 document.getElementById("swap_imei1["+splitPart[1]+"]").className="required form-control alphanumeric";
			
			
			}
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
			document.getElementById("repair_level["+splitPart[1]+"]").value=splitPart[3]; 
		}
		}
	  });
  }
  ////// fill stock available input field on selection of part
  function getstockable(val,indx){
	  var result = val.split("^");
	  
	  	document.getElementById("part_price["+indx+"]").value=result[1];
	 
	  document.getElementById("avlqty["+indx+"]").value=result[2];
  }
  /////// get replace type dropdown like BOX or UNIt on the basis of new replace model selected
  function replace_type(val){
	  var modelid = '<?=$job_row['model_id']?>';
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{replacemodel_eng:val,modelcode1:modelid, eng_id:'<?=$job_row['eng_id']?>',locationcode:'<?=$_SESSION['asc_code']?>',brand_id:<?=$job_row['brand_id']?>},
		success:function(data){
	//	alert(data);
			document.getElementById('replacedDiv').innerHTML=data;
		}
	  });
  }
  ///////////////get second imei value////////////
   function get_sec_imei(val){
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{sec_imei:val},
		success:function(data){
			var splitdata = data.split("~");
			//alert(splitdata);
			document.getElementById('new_imei2').value=splitdata[0];
			///document.getElementById('new_imei1').value=splitdata[1];
		}
	  });
  }
  ///// get stock of selected part
  function getstock(val){
	  ///// get model specification like sim type, serial/imei length
	  ////// get ok qty of selected replace model
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{replacTagNo:val, eng_id:'<?=$job_row['eng_id']?>', locationcode:'<?=$_SESSION['asc_code']?>'},
		success:function(data){
			alert(data);
			
			document.getElementById('dispimeifield').innerHTML=data;
				//document.getElementById("dispimeifield").style.display="none";
				document.getElementById("rep_tagno").className="required form-control";
			
		
			
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
function bigImg(x) {
  x.style.height = "300px";
  x.style.width = "300px";
}

function normalImg(x) {
  x.style.height = "100px";
  x.style.width = "100px";
}

 function validateImage(nam,ind) {
	var err_msg="";
	var img1=document.getElementById("handset_img").value;
	
    var file = document.getElementById(nam).files[0];
    var t = file.type.split('/').pop().toLowerCase();
	
    if(t != "jpeg" && t != "jpg" && t != "png" && t != "bmp" && t != "gif") {
		err_msg = "<strong>Please select a valid file. <br/></strong>";
		document.getElementById("errmsg"+ind).innerHTML = err_msg;
		document.getElementById(nam).value = '';
        return false;
    }else if(file.size > 2048000){  /**** 204800 ***/
		err_msg = "<strong>Max file size can be 2 MB.<br/></strong>";
		document.getElementById("errmsg"+ind).innerHTML = err_msg;
		document.getElementById(nam).value = '';
        return false;
    }else{
		document.getElementById("errmsg"+ind).innerHTML ="";
	}
    
	return true;
}
$(document).ready(function () {
	$('#manufacter_date').datepicker({
		format: "yyyy-mm-dd",
		//startDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
	


});
  </script>
  
    <!-- Include Date Picker -->
 <script type="text/javascript" src="../js/jquery.validate.js"></script>

 <script type="text/javascript" src="../js/common_js.js"></script>

  <!-- Include Date Picker -->

 <link rel="stylesheet" href="../css/datepicker.css">

 <script src="../js/bootstrap-datepicker.js"></script>

 <!-- Include multiselect -->

 <script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>

 <link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
 <?php if($job_row['doc_type']!=""){?>
<body onLoad="change_closetype('<?=$job_row['doc_type']?>')" >
<?php } else {?>

<body>

<?php }?>
  <div class="container-fluid">
    <div class="row content">
      <?php 
    include("../includes/leftnavemp2.php");
	$cust_det = explode("~",getAnyDetails($job_row['customer_id'],"customer_id,landmark,email,phone,dob_date,mrg_date,alt_mobile","customer_id","customer_master",$link1));
	$product_det = mysqli_fetch_assoc(mysqli_query($link1,"SELECT * FROM product_registered  where job_no='".$job_row['job_no']."'"));
    ?>
      <div class="<?=$screenwidth?>">
        <h2 align="center"><i class="fa fa-wrench"></i> Job Repair</h2>
        <h4 align="center">Job No.-
          <?=$docid?>
        </h4>
		
		           <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        
       
        <?php }?>
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
                    <td><?php echo $cust_det[6];?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">State</label></td>
                    <td><?php echo getAnyDetails($job_row["state_id"],"state","stateid","state_master",$link1);?></td>
                    <td><label class="control-label">Email</label></td>
                   <td><?php echo $cust_det[2];?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">City</label></td>
                    <td><?php echo getAnyDetails($job_row["city_id"],"city","cityid","city_master",$link1);?></td>
                    <td><label class="control-label">Pincode</label></td>
                    <td><?php echo $job_row['pincode'];?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Customer Category</label></td>
                    <td><?php echo $job_row['customer_type'];?></td>
                      <td><label class="control-label">Residence No</label></td>
                <td><?php echo $cust_det[3];?></td>
                  </tr>
				 <tr>
                <td><label class="control-label">Landmarks</label></td>
                <td><?php echo $cust_det[1];?></td>
                <td><label class="control-label"></label></td>
                <td><?php ?></td>
              </tr>
                  <tr>
                    <td colspan="4" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='<?=$page_link?>.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'">
                      &nbsp;
                      <button title="Next" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#menu1'">Next >></button>
                    
                     </td>
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
                    <td><?=getAnyDetails($job_row['model_id'],"model","model_id","model_master",$link1)." (".$job_row['model_id'].")";?></td>
					    <td><label class="control-label">Warranty Status</label></td>
              <td><?=$job_row['warranty_status']?></td>
                    
                  </tr>
                  <tr>
                    <td><label class="control-label"><?php echo SERIALNO ?></label></td>
                    <td><?=$job_row['imei']?></td>
                 <td><label class="control-label">Call source</label></td>
              <td><?=$job_row['call_type']?></td>
                  </tr>
      
            <tr>
              <td><label class="control-label">Purchase Date</label></td>
              <td><?=dt_format($job_row['dop'])?></td>
              <td><label class="control-label">Warranty End Date</label></td>
              <td><?=dt_format($product_det['warranty_end_date'])?></td>
            </tr>
		
			 <tr>
			  <td><label class="control-label">Date Of Installation</label></td>
              <td><?=dt_format($product_det['installation_date'])?></td>
              <td><label class="control-label">Purchase From</label></td>
              <td ><?php if( $job_row['entity_type']=='Others') { echo "Others" ;} else {echo getAnyDetails($job_row['entity_type'],"name","id","entity_type",$link1); }?></td>
              
            </tr>
			<tr>
              <td><label class="control-label">Dealer Name</label></td>
              <td><?=$job_row['dname']?></td>
              <td><label class="control-label">Invoice No</label></td>
              <td><?=$job_row['inv_no']?></td>
            </tr>
                  <tr>
                    <td colspan="4" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='<?=$page_link?>.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'">
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
              <td><label class="control-label">VOC</label></td>
              <td><?php echo getAnyDetails($job_row["cust_problem"],"voc_desc","voc_code","voc_master",$link1);?></td>
              <td><?php 	$voc= explode(",",$job_row['cust_problem2']); 
			           $vocpresent   = count($voc);
					   if($vocpresent == '1'){
					   $name = getAnyDetails($voc[0],"voc_desc","voc_code","voc_master",$link1 );
					   }
					   else if($vocpresent >1){
					     $name ='';
					   for($i=0 ; $i<$vocpresent; $i++){					 
			 			$name.=  getAnyDetails($voc[$i],"voc_desc","voc_code","voc_master",$link1 ).",";
			 			}} echo $name;?></td>
              <td><?=$job_row['cust_problem3']?></td>
            </tr>
            <tr>
              <td><label class="control-label">Remark </label></td>
              <td colspan="3"><?=$job_row['remark']?></td>
            </tr>
			<?php 
			$image_det = mysqli_fetch_assoc(mysqli_query($link1,"SELECT img_url FROM image_upload_details  where job_no='".$job_row['job_no']."'"));
			 if($image_det['img_url']!=""){?>  <tr>
              <td><label class="control-label">Product Image </label></td>
              <td colspan="3"  > <img src="<?=$image_det['img_url']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"> </td>
            </tr><?php }?>
                  <tr>
                    <td colspan="4" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='<?=$page_link?>.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'">
                      &nbsp;
                      <button title="Previous" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#menu1'"><< Previous</button>
                      &nbsp;
                      <button title="Next" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#menu3'">Next >></button></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div id="menu3" class="tab-pane fade">
            <form  name="frm1" id="frm1" class="form-horizontal"  enctype="multipart/form-data"  action="job_repair_save.php" method="post"><br>
            <div class="form-group">
                <div class="col-md-6"><label class="col-md-5">Job For </label>
                  <div class="col-md-6">
                    <input type="hidden" name="call_for" id="call_for" value="<?=$job_row['call_for']?>">
                 <?=$job_row['call_for']?>
                  </div>
                </div>
                <div class="col-md-6"><label class="col-md-6">Current Status</label>
                  <div class="col-md-6">
              <?= getAnyDetails($job_row['status'],"display_status","status_id","jobstatus_master",$link1);?>
                  </div>
                </div>
              </div><!--close form group-->
              
               <div class="form-group">
                <div class="col-md-6"><label class="col-md-5">Assigned Engineer </label>
                  <div class="col-md-6">
                    
                <?=$job_row['eng_id']?>
                  </div>
                </div>
                <div class="col-md-6"><label class="col-md-6"></label>
                  <div class="col-md-6">
               
                  </div>
                </div>
              </div><!--close form group-->

			  	  
			  
			  
            <div class="form-group">
                <div class="col-md-6"><label class="col-md-5">Repair Status <span class="red_small">*</span></label>
                  <div class="col-md-6">
                    <select name="jobstatus" id="jobstatus" onChange="return change_stat(this.value);" class="required form-control" style="width:250px;" required>
                      <option value="" selected>--Select Repair Result--</option>
                      <?php if( ($job_row['call_for']=='Installation' || $job_row['call_for']=='Demo' || $job_row['call_for']=='Reinstallation') ){
					   $res_jobstatus = mysqli_query($link1,"select status_id, display_status from jobstatus_master where system_status in ('Demo Done','Installation Done','Request For Approval') order by display_status");
						}
						else if($job_row['els_status']=='Fail' && $job_row['estimate_approval']!="Y"){
							if($job_row['status']==5){
							$res_jobstatus = mysqli_query($link1,"select status_id, display_status from jobstatus_master where status_id=main_status_id and system_status in ('RWR') order by display_status");}
							else {
						$res_jobstatus = mysqli_query($link1,"select status_id, display_status from jobstatus_master where status_id=main_status_id and system_status in ('RWR','EP') order by display_status");
							}
						}								  
						else{
						if($job_row['warranty_status']=='OUT'){
						// EP will come for In warranty call only - ravi
						 $res_jobstatus = mysqli_query($link1,"select status_id, display_status from jobstatus_master where status_id=main_status_id and system_status in ('EP','PNA','Repair Done','RWR','Request For Approval','Repair at Field' ) order by display_status");
						}else{
						$res_jobstatus = mysqli_query($link1,"select status_id, display_status from jobstatus_master where status_id=main_status_id and system_status in ('PNA','Repair Done','RWR','Request For Approval','Repair at Field') order by display_status");
						}
						   
						}	
							 while($row_jobstatus = mysqli_fetch_assoc($res_jobstatus)){
							?>
                      <option value="<?=$row_jobstatus['status_id']?>" <?php if($job_result['status'] == $row_jobstatus['status_id']){echo 'selected';}?>>
                      <?=$row_jobstatus['display_status']?>
                      </option>
                      <?php } ?>
										
						<option value="12">Cancel</option>
					
						<?php if($job_row['app_reason']=='Replacement' &&  $job_row['doa_approval']=='Y'){ ?>
					    <option value="8">Replacement</option>							
						<?php } ?>
						
						<?php //if($job_row['product_id']=='' ||  $job_row['product_id']==''){ ?>
					   <!-- <option value="58">GAS Charging</option>-->
						<?php //} ?>
						
                    </select>
                  </div>
                </div>
                <div class="col-md-6"><label class="col-md-6">Repair Remark <span id="rep_remark_span" ></span> </label>
                  <div class="col-md-6">
                    <textarea name="rep_remark" id="rep_remark" class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:none"></textarea>
                  </div>
                </div>
              </div><!--close form group-->
			  
		            <div class="form-group">
                <div class="col-md-6"><label class="col-md-5">Warranty Document <span id="closedoctype" class="red_small" >*</span></label>
                  <div class="col-md-6">
                    <select name="closetype" id="closetype" onChange="return change_closetype(this.value);" class="form-control" style="width:250px;">
                      <option value="" selected>--Select Document--</option>
                      <?php 
					   $res_closetype = mysqli_query($link1,"select * from close_type");
				
							 while($row_closetype = mysqli_fetch_assoc($res_closetype)){
							?>
                      <option value="<?=$row_closetype['type']?>" <?php if($job_row['doc_type'] == $row_closetype['type']){echo 'selected';}?>>
                      <?=$row_closetype['type']?>
                      </option>
                      <?php
							 }?>
						
				
				
                    </select>
                  </div>
                </div>
                <div class="col-md-6"><label class="col-md-6" id="customer_satis" style="display:none">Customer Ready to Pay<span id="customer_span"></span></label>
                  <div class="col-md-6">
                   <span  id="customer_satis1" style="display:none"> <select name="custumer_pay" id="custumer_pay"  style="width:250px;" >
				   <option value="" >--Please select--</option>
                 <option value="Yes" <?php if($job_row['customer_satif'] == "Yes"){echo 'selected';}?>>Yes</option>
				  <option value="No"<?php if($job_row['customer_satif'] == "No"){echo 'selected';}?>>No</option>
				   
                        </select></span>
                  </div>
                </div>
              </div><!--close form group-->
          
               <div class="form-group">
           <?php if($job_row['area_type']=='Upcountry'){?>     <div class="col-md-6"><label class="col-md-5">Travel By Engineer<span class="red_small">(IN KM)</span></label>
                  <div class="col-md-6">
                    <input name="travel" type="text" class="number form-control"  id="travel">
                  </div>
                </div><?php } else{?>
				   <div class="col-md-6"><label class="col-md-5"></label>
                  <div class="col-md-6">
                  
                  </div>
                </div>
				<?php }?>
				<?php if($job_row['eng_id']==''){?>
                <div class="col-md-6"><label class="col-md-6">Engineer Name</label>
                  <div class="col-md-6">
                    <select   name="eng_name" id="eng_name" class="form-control required" >
                   <option value="">--Please Select--</option>
                   <?php
               $vendor_query="select userloginid ,locusername  from locationuser_master where statusid ='1' and location_code  = '".$_SESSION['asc_code']."' ";
			        $check1=mysqli_query($link1,$vendor_query);
                while($br = mysqli_fetch_array($check1)){?>
                   <option value="<?=$br['userloginid']?>" <?php if($_REQUEST['vendor'] == $br['userloginid']) { echo 'selected'; }?>>
                  <?=$br['locusername']." | ".$br['userloginid']?>
                  </option>
                   <?php } ?>
                 </select>
                  </div>
                </div><?php } else {?>
				 <div class="col-md-6"><label class="col-md-5"></label>
                  <div class="col-md-6">
                
                  </div>
                </div><?php }?>
				
              </div><!--close form group-->
			  			      <div class="form-group" id="pending_reason" style="display:none">
                <div class="col-md-6"><label class="col-md-5">Pending Reason </label>
                  <div class="col-md-6">
				   <select name="pending_reason" id="pending_reason" class="form-control" style="width:250px;" >
				   <option value="" selected>--Select Pending Reason--</option>
                   <?php

  

				$re_query="SELECT * FROM reason_master   where status = '1' order by reason";



				$check_rea=mysqli_query($link1,$re_query);



				while($br_reson = mysqli_fetch_array($check_rea)){



				?>
                          <option value="<?=$br_reson['reason']?>"<?php if($_REQUEST['pending_reason']==$br_reson['reason']){ echo "selected";}?>><?php echo $br_reson['reason']?></option>
                          <?php }?>
                        </select>

                  </div>
                </div>
                <div class="col-md-6"><label class="col-md-6"></label>
                  <div class="col-md-6">
                
                  </div>
                </div>
              </div><!--close form group-->
			  
			<div class="form-group" id="close_reason" style="display:none">
                <div class="col-md-6"><label class="col-md-5">Close Reason </label>
                  <div class="col-md-6">
				   <select name="close_reason" id="close_reason"  class="form-control"  style="width:250px;">
				   <option value="" selected>--Select Close Reason--</option>
                   <?php

  

				$re_query="SELECT * FROM close_reason_master   where status = '1' order by reason";



				$check_rea=mysqli_query($link1,$re_query);



				while($br_reson = mysqli_fetch_array($check_rea)){



				?>
                          <option value="<?=$br_reson['reason']?>"<?php if($_REQUEST['close_reason']==$br_reson['reason']){ echo "selected";}?>><?php echo $br_reson['reason']?></option>
                          <?php }?>
                        </select>

                  </div>
                </div>
                <div class="col-md-6"><label class="col-md-5"></label>
                  <div class="col-md-6">
                
                  </div>
                </div>
              </div><!--close form group-->
			  	<div class="form-group" >
                <div class="col-md-6"><label class="col-md-5">Upload Image<span  id="handset_span"></span></label>
                  <div class="col-md-6">
				    <input type="file" class="form-control" name="handset_img" id="handset_img" onChange="return validateImage('handset_img','0');"  accept=".png,.jpg,.jpeg,.gif" />

                  </div>
                </div>
                <div class="col-md-6"><label class="col-md-6"></label>
                  <div class="col-md-6">
                
                  </div>
                </div>
              </div><!--close form group-->
			  
			  			  	<div class="form-group" >
                <div class="col-md-6"><label class="col-md-5">Image Upload 2</label>
                  <div class="col-md-6">
				    <input type="file"  name="handset_img2" id="handset_img2" onChange="return validateImage('handset_img2','0');" class="form-control "  accept=".png,.jpg,.jpeg,.gif" />

                  </div>
                </div>
                <div class="col-md-6"><label class="col-md-6">Image Upload 3</label>
                  <div class="col-md-6">
                  <input type="file"  name="handset_img3" id="handset_img3" onChange="return validateImage('handset_img3','0');" class="form-control "  accept=".png,.jpg,.jpeg,.gif" />
                  </div>
                </div>
              </div><!--close form group-->
			  			  			  	<div class="form-group" >
                <div class="col-md-6"><label class="col-md-5">Image Upload 4</label>
                  <div class="col-md-6">
				    <input type="file"  name="handset_img4" id="handset_img4" onChange="return validateImage('handset_img4','0');" class="form-control "  accept=".png,.jpg,.jpeg,.gif" />

                  </div>
                </div>
                <div class="col-md-6"><label class="col-md-6">Btr/Test Report Upload</label>
                  <div class="col-md-6">
                  <input type="file"  name="handset_img5" id="handset_img5" onChange="return validateImage('handset_img5','0');" class="form-control "  accept=".png,.jpg,.jpeg,.gif" />
                  </div>
                </div>
              </div><!--close form group-->
                			  	<div class="form-group" >
                <div class="col-md-6"><label class="col-md-5" id="iddoc" style="display:none">Document No.(Invoice/Warranty Card/Serial) <span  id="document_type"></span></label>
                  <div class="col-md-6" id="serialno" style="display:none">
				    <input type="text"  name="serial_no" id="serial_no" value="<?=$job_row['imei']?>"   class="form-control"  />

                  </div>
				   <div class="col-md-6" id="warranty_card" style="display:none">
				    <input type="text"  name="warrantycard" id="warrantycard" value="<?=$job_row['warranty_card']?>"   class="form-control"  required />

                  </div>
				   <div class="col-md-6" id="invoiceno" style="display:none">
				    <input type="text"  name="invoice_no" id="invoice_no" value="<?=$job_row['invoice_no']?>"   class="form-control"  />

                  </div>
				  
				<!--   <div class="col-md-6" id="manufacterdate" style="display:none">
				   
    <div style="display:inline-block;float:left;"><input type="text" class="form-control " name="manufacter_date"  id="manufacter_date" style="width:150px;" value="<?php if($row_customer['manufacter_date']!='0000-00-00'){ echo $row_customer['manufacter_date'];?>  <?php }else{ echo "";}?>"   ></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>
                  </div> -->
				  
                </div>
                 <div class="col-md-6"><label class="col-md-5">Enter SCM Code</label>
                  <div class="col-md-6">
                    <input name="happy_code" id="happy_code" class="form-control" type="text" style="width:100px;text-align:right" /> 
				   <!-- <a href="complaint_edit_asp.php?refid=<?=base64_encode($job_row['job_no'])?>&pageid=repair" target="_blank" onClick="window.location.href='$page_link'"><i class="fa fa-pencil" style="font-size:24px;"></i></a>
-->
                  </div>
                </div>
                </div>
                
                 <?php $pna_info=mysqli_query($link1,"Select * from auto_part_request where job_no ='".$docid."' ");
	 if(mysqli_num_rows($pna_info)>0){?>	
	 <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;&nbsp;PNA Request Details</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
                  <tr>
                    <td width="15%"><strong>Part Detail</strong></td>
					 <td width="10%"><strong>PNA Request Date</strong></td>
                    <td width="10%"><strong>Request No.</strong></td>
					<td width="10%"><strong>Request Generate Date</strong></td>
                    <td width="10%"><strong>PO Dispatch Date</strong></td>               
                    <td width="10%"><strong>Challan No.</strong></td>
					 <td width="10%"><strong>Docket No.</strong></td>
					 <td width="10%"><strong>Courier</strong></td>                 
                  </tr>
                </thead>
                <tbody>
                <?php			
				while($pna_detail = mysqli_fetch_assoc($pna_info)){
				 $po_items =  mysqli_fetch_array(mysqli_query($link1,"select * from po_items where job_no = '".$pna_detail['job_no']."' and partcode= '".$pna_detail['partcode']."' "));
				?>
                  <tr>
                    <td><?=getAnyDetails($pna_detail['partcode'],"part_desc","partcode","partcode_master",$link1);?></td>
					<td><?=dt_format($pna_detail['request_date']);?></td>
					<td><?=$po_items['po_no'];?></td>
					<td><?=dt_format($po_items['update_date']);?></td>	
                    <?php
					$bill_details= explode("~",getAnyDetails($po_items['process_challan'],"sale_date,challan_no,docket_no,courier","challan_no","billing_master",$link1));
					?>				
                    <td><?=$bill_details[0]?></td>
                    <td><?=$bill_details[1]?></td>                  
                    <td><?=$bill_details[2]?></td>
					<td><?=$bill_details[3]?></td>
                  </tr>
                  <?php
				}
				  ?>	
             </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
 
  <?php } ?> 
  
                
                
			  	<div class="form-group" id="req_app" style="display:none">
                <div class="col-md-6"><label class="col-md-5">Request For </label>
                  <div class="col-md-6">
				   <select name="app_req" id="app_req"  class="form-control" style="width:250px;" >
				   <option value="" >--Select Request--</option>
                 <option value="Deviation" >Deviation</option>
				   <option value="Part Discount" >Part Discount</option>
				      <option value="Replacement" >Replacement</option>
					    <option value="Call cancellation" >Call cancellation</option>
						 <option value="Warranty Period Relaxation">Warranty Period Relaxation</option>
                        </select>

                  </div>
                </div>
                <div class="col-md-6"><label class="col-md-6"></label>
                  <div class="col-md-6">
                
                  </div>
                </div>
              </div><!--close form group-->
              <div class="form-group">
                <div class="col-md-12">
                  <div id="part_consumption" style="display:none"><br/>
                    <table width="100%" class="table table-bordered">
                      <thead>
                       
                      </thead>
                      <tbody>
                      
						
						<tr class="<?=$tableheadcolor?>">
						 <td width="20%"><strong>Fault Code</strong></td>     
                          <td width="20%"><strong>Repair Code</strong></td>
                          <td width="20%"><strong>Part</strong></td>
                          <td width="10%"><strong>Price</strong></td>
                          <td width="10%"><strong>Available QTY</strong></td></tr>
						  <tr>   
						  <td>
                              <select name="fault_code[0]" id="fault_code[0]" style="width:230px;" class="required form-control" onChange="getRepairDropDown(this.value,'0')">
                                  <option value="" selected="selected">Select Defect Code</option>
                                  <?php  $f=0; while($f<count($def_arr)){?>
                                    <option value="<?=$def_arr[$f][0]?>"><?=$def_arr[$f][1]?> (<?=$def_arr[$f][0]?>)</option>
                                  <?php $f++;}?>
                              </select>
                          </td>
						                             
                          <td>
                          	<span id="repDiv0">
                            <select name="repair_code[0]" id="repair_code[0]" style="width:230px;" class="required form-control" disabled="disabled" onChange="return getPartDropDown(this.value,'0');">
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
							                           <div align="left" id="dispswapimeifield[0]" style="display:none;"> 

                        <!-- //  onBlur="get_sec_imei_pcb(this.value,0);"-->

                            	 <input name="swap_imei1[0]" id="swap_imei1[0]" type="text" class=" form-control alphanumeric"  maxlength="15" style="width:200px;" placeholder="Enter IMEI1/Serial No.1"/>

                    			 <input name="swap_imei2[0]" id="swap_imei2[0]"  type="text" class="form-control alphanumeric" style="width:200px;" placeholder="Enter IMEI2/Serial No.2"  />
                          </td>
                          <td><input name="part_price[0]" id="part_price[0]" class="form-control" type="text" style="width:100px;text-align:right" readonly/></td>
                          <td><input name="avlqty[0]" id="avlqty[0]" class="form-control" type="text" style="width:100px;text-align:right" readonly/></td></tr>
                      </tbody>
                    </table>
                    <div id="myDiv"></div>
                    <div align="left" id="add_row"><i class="fa fa-plus fa-lg" onClick="addNewRow(0);" title="Add new part for use"></i> <strong>&nbsp;ADD NEW ROW</strong><input name="theValue" type="hidden" class="form-control" id="theValue" value="0" /></div>
					  
				<!-- battry process -->
					  
					  
					  
					  
					  
					  
					  
              
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
							
    							$rs_sendfor = mysqli_query($link1,"select location_code, locationname, cityid, stateid from location_master  where statusid='1' and location_code in (select location_code from access_brand where status='Y' and brand_id='".$job_row['brand_id']."' and  location_code in(select repair_location from map_repair_location where location_code='".$_SESSION['asc_code']."' and status='Y'))")or die("error2".mysqli_error($link1));
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
                              <option value="" selected="selected">Select Defect Code</option>
                              <?php  $z=0; while($z<count($def_arr)){?>
                              <option value="<?=$def_arr[$z][0]?>">
                              <?=$def_arr[$z][1]?> (<?=$def_arr[$z][0]?>)</option>
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
                    			<select name="pending_part[0]" class="required form-control"  id="pending_part[0]" style="width:250px;">
        							<option value="" selected="selected"> Select Pending Part-1</option>
        							<?php
									
									
									
									//$rs_pna = mysqli_query($link1,"select pm.partcode, pm.part_name, pm.part_category from partcode_master pm, client_inventory ci where pm.model_id like '%".$job_row['model_id']."%' and pm.status='1'  and ci.okqty = 0 and ci.location_code='".$_SESSION['asc_code']."' order by pm.part_name")or die("error in pna dropdown".mysqli_error($link1));
									
									$rs_pna = mysqli_query($link1,"select partcode, part_name, part_category,part_desc,vendor_partcode from partcode_master  where  model_id like '%".$job_row['model_id']."%'  and status='1' and partcode not in ( select partcode from auto_part_request where job_no='".$docid."')     order by part_name")or die("error in pna dropdown".mysqli_error($link1));
									
									while($row_pna = mysqli_fetch_array($rs_pna)){
									?>
                        			<option value="<?=$row_pna[0]."~".$row_pna[2]?>"><?=$row_pna[3]."-".$row_pna[2]?> (<?=$row_pna[4]?> ) </option>
                        			<?php
                        			}	
                        			?>
      							</select>
      						</td>
                  		</tr>
                      </tbody>  
                	</table>
              		<div id="myDivPna"></div>
                    <!-- comment done for only one part in one call  for PNA-->
                 <!--   <div align="left" id="add_rowpna"><i class="fa fa-plus fa-lg" onClick="addNewRowPna(0);" title="Add new part for PNA"></i> <strong>&nbsp;ADD NEW PART</strong><input name="theValuePna" type="hidden" class="form-control" id="theValuePna" value="0" /></div>-->
                  </div>
				  
				      <!-- Part Damand By Engineer-->
                  <div id="part_dmd" style="display:none">
                    <table width="100%" class="table">
                  	  <thead>	
                        <tr>
                    		<td><strong>Engineer  Part Demand</strong></td>
                  		</tr>
                      </thead>
                      <tbody>  
                  		<tr>
                    		<td>
                    			<select name="dmd_part[0]" class="required form-control"  id="dmd_part[0]" style="width:250px;">
        							<option value="" selected="selected"> Select  Part-1</option>
        							<?php

									
									
									
									//$rs_pna = mysqli_query($link1,"select pm.partcode, pm.part_name, pm.part_category from partcode_master pm, client_inventory ci where pm.model_id like '%".$job_row['model_id']."%' and pm.status='1'  and ci.okqty = 0 and ci.location_code='".$_SESSION['asc_code']."' order by pm.part_name")or die("error in pna dropdown".mysqli_error($link1));
									
									$rs_pna = mysqli_query($link1,"select partcode, part_name, part_category from partcode_master  where  model_id like '%".$job_row['model_id']."%'  and status='1'     order by part_name")or die("error in pna dropdown".mysqli_error($link1));
									
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
              		<div id="myDivPartDMD"></div>
                    <div align="left" id="add_rowDmd"><i class="fa fa-plus fa-lg" onClick="addNewrowPdmd(0);" title="Add new part for PNA"></i> <strong>&nbsp;ADD NEW PART</strong><input name="theValuePdmd" type="hidden" class="form-control" id="theValuePdmd" value="0" /></div>
                  </div>
				  
				  
				     <!-- Start Div Demo/Installation . It will display only when repair status select as Demo Done/Installation Done -->
                  <div id="demo_install" style="display:none">
                    <table width="100%" class="table">
                  	  <thead>	
                        <tr>
                    		<td><strong>Please Select Required Parts</strong></td>
                  		</tr>
                      </thead>
                      <tbody>  
                  		<tr>
                    		<td>
                    			<select name="req_part[0]" class=" form-control"  id="req_part[0]" style="width:250px;">
        							<option value="" selected="selected"> Select  Part-1</option>
        							<?php
									
									
									
								
									
									$rs_as = mysqli_query($link1,"select partcode, part_name, part_category,part_desc,vendor_partcode from partcode_master  where  model_id like '%".$job_row['model_id']."%'  and status='1'  and part_category='ACCESSORY'     and partcode  in  ( select partcode from user_inventory  where location_code='".$_SESSION['asc_code']."' and locationuser_code='".$job_row['eng_id']."' and okqty > 0)   order by part_name")or die("error in pna dropdown".mysqli_error($link1));
									
									while($row_as = mysqli_fetch_array($rs_as)){
									?>
                        			<option value="<?=$row_as[0]?>"><?=$row_as[3]."-".$row_as[2]?> (<?=$row_as[4]?> ) </option>
                        			<?php
                        			}	
                        			?>
      							</select>
      						</td>
                  		</tr>
                      </tbody>  
                	</table>
              		<div id="myDivDemo_install"></div>
                    <div align="left" id="add_demo"><i class="fa fa-plus fa-lg" onClick="addNewRowdemo(0);" title="Add new part"></i> <strong>&nbsp;ADD NEW PART</strong><input name="theValuedemo" type="hidden" class="form-control" id="theValuedemo" value="0" /></div>
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
                        <select name="esti_part[0]" class="required form-control"  id="esti_part[0]" style="width:250px;" onChange="getData_esti(this.value,'0');">
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
    				<td width="15%"><input name="ser_charge" type="text" class=" form-control" value="<?=$model_flags[6]?>" id="ser_charge" onBlur="getCost_service()" style="width:100px;" readonly /><input name="ser_tax_hsn" type="hidden" class="form-control" id="ser_tax_hsn" value="998716" readonly style="width:100px;"/></td>
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
                  <!--<div id="SFR" style="display:none"> </div>-->
                  <div id="replacement" style="display:none"><br/>
                  	<table width="100%" class="table table-bordered">
                	  <thead>	
                        <tr>
                  			<td width="20%"><strong>Fault Code</strong></td>
                  			<td width="20%"><strong>Repair Code</strong></td>
                  			
                   			<td width="10%"><strong>Partcode</strong></td>
                         
                  			<td width="20%"><strong>Replace TAG /<?php echo SERIALNO?>.</strong></td>
                		</tr>
                      </thead>
                      <tbody>  
                		<tr>
                  			<td>
                            	<select name="fault_code_replace" id="fault_code_replace" style="width:200px;"  class="required form-control">
                      				<option value="" selected="selected">Select Fault Code</option>
                      				<?php  $z=0; while($z<count($def_arr)){?>
                      				<option value="<?=$def_arr[$z][0]?>"><?=$def_arr[$z][1]?> (<?=$def_arr[$z][0]?>)</option>
                      				<?php $z++;}?>
                    			</select></td>
                  			<td> 
                    			<select name="repair_code_replace" id="repair_code_replace" style="width:200px;" class="required form-control " onChange="replace_type(this.value);">
                                 	<option value="" selected="selected"> Select Repair code</option>
                                   	<?php 
									$sql = "SELECT rep_code,rep_desc,rep_level FROM repaircode_master where rep_desc = 'Replacement' and status='1'";
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
                  			
                     		<td><div id="replacedDiv">
                                <select  name="rep_part" id="rep_part"  class="required form-control " style="width:150px;">
                              	<option value="" >Select Partcode</option>    		
      							</select>
                              </div></td>
                   
                  			<td id="dispimeifield" >
                           
								  <!-- <select  name="rep_tagno"  id="rep_tagno"  class="form-control" style="width:150px;">
                              	<option value="" >Select TAG NO</option>    		
      							</select>-->
                    			<input name="new_imei2" id="new_imei2"  type="text" class="form-control alphanumeric" style="width:200px;" placeholder="Enter IMEI2/Serial No.2"/>
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
				
				 <input name="warranty_status" id="warranty_status" type="hidden" value="<?=$job_row['warranty_status']?>" >
                  <input name="savejob" id="savejob" type="submit" class="btn btn-success" value="Save" title="Save this job">
                  <input name="postjobno" type="hidden" class="form-control" id="postjobno" value="<?=base64_encode($job_row['job_no'])?>"/>
                  <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               	  <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                  <br/><br/>
                  <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='<?=$page_link?>.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'">
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
                   <!-- <td width="10%"><strong>Warranty</strong></td>-->
                    <!--<td width="10%"><strong>Job Status</strong></td>-->
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
              <!--      <td><?=$row_jobhistory['warranty_status']?></td>-->
                    <!--<td><?=$row_jobhistory['status']?></td>-->
                    <td><?=$row_jobhistory['updated_by']?></td>
                    <td><?=$row_jobhistory['remark']?></td>
                    <td><?=$row_jobhistory['update_date']?></td>
                  </tr>
                <?php
				}
				?>
                  <tr>
                    <td colspan="8" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='<?=$page_link?>.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'">
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
  <!--End row content--><!--End container fluid-->
  <?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
  </body>
</html>