<?php
require_once("config.php");
switch($_REQUEST["action"]){
case getModel:
echo "<select  name='model' class='dropdown'><option value='' >Please Select</option>";
$model_query="SELECT distinct model,make FROM model_master where make='$_REQUEST[value]' and status='Active' order by model";
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br[model]."'>";
echo $br['model']."</option>";
}
echo "</select>";
break;
case getPartsok:
$tar=$_REQUEST[target];
echo "<select  name='item_id[$tar]' id='item_id[$tar]' class='dropdown1' onchange='return getField($_REQUEST[target],this.value);'><option value='' >Please Select--</option>";

$model_query="SELECT  ci.partcode, pm.name, pm.model FROM warehouse_inventory ci, partcode_master pm where ci.partcode=pm.partcode  and ci.okqty > 0 and w_code='$_SESSION[asc_code]' and pm.model='$_REQUEST[value]' group by ci.partcode  order by pm.name";
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br[partcode]."'>";
echo $br[1].'-'.$br[0]."</option>";
}
echo "</select>~".$_REQUEST[target];
break;


case getaccount_edit:
echo "<select  name='ac_no' id='ac_no' class='dropdown'><option value='' >Please Select</option>";
$ch_val=mysqli_fetch_array(mysqli_query($link1,"select account_no from courierdetail where sn='$_REQUEST[id]'"));
if($ch_val[bankname]=='$_REQUEST[value]'){
$model_query="SELECT distinct account FROM bank_account_master where bank_name='$ch_val[bankname]' and status='Active' order by account";
}else{
$model_query="SELECT distinct account FROM bank_account_master where bank_name='$_REQUEST[value]' and status='Active' order by account";
}
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br[account]."'";
if($_REQUEST[ac_num]==$ch_val[account_no])echo "selected";
echo ">";
echo $br['account']."</option>";
}
echo "</select>";
break;

case getaccount:
echo "<select  name='ac_no' id='ac_no' class='dropdown'><option value='' >Please Select</option>";
//echo "SELECT distinct account FROM bank_account_master where bank_name='$_REQUEST[value]' and status='Active' order by account";
$model_query="SELECT distinct account FROM bank_account_master where bank_name='$_REQUEST[value]' and status='Active' order by account";
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br[account]."'>";
echo $br['account']."</option>";
}
echo "</select>";
break;

case getCity:
echo "<select  name='city' class='dropdown'><option value='' >Select City</option>";
$model_query="SELECT distinct city FROM citymaster where state='$_REQUEST[value]' order by city";
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br[city]."'>";
echo $br['city']."</option>";
}
echo "<option value='Others'>Others</option>";
echo "</select>";
break;
case getDname:
echo "&nbsp;&nbsp;<select  name='dname' id='dname' class='dropdown' style='width:auto;'><option value='' >Select Distributor Name</option>";
$model_query="SELECT * FROM distributer_master where city='$_REQUEST[value]' order by name";
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br['name'].", ".$br['city'].", (".$br['dist_code'].")'>";
echo $br['name'].", ".$br['city']."</option>";
}
echo "</select>";
break;

case getState:
echo "<select  name='state' id='state' class='dropdown' onChange='return getCity(this.value);'><option value='' >Select State</option>";
$state="SELECT distinct state FROM state_master where country='$_REQUEST[value]' order by state";
$check1=mysqli_query($link1,$state);
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br[state]."'>";
echo $br['state']."</option>";
}
echo "<option value='Others'>Others</option>";
echo "</select>";
break;

case getPartCode:
$index=$_REQUEST[indx];
//echo $_REQUEST[model];
$model=base64_decode($_REQUEST[model]);
$model_query="SELECT ci.partcode, pm.part_desc FROM warehouse_inventory ci, partcode_master pm where ci.partcode=pm.partcode and w_code='$_SESSION[asc_code]' and pm.cat!='' and pm.model='$model' and pm.status='Active' group by ci.partcode order by pm.name";
$check1=mysqli_query($link1,$model_query)or die("error1".mysqli_error($link1));
if(mysqli_num_rows($check1)>0){
echo "<select  name='item_id[$index]' id='item_id[$index]' class='dropdown1'>";
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br[0]."'>";
echo $br[partcode]." | ". $br[part_desc]."</option>";
}
echo "</select>"."~".$index;
}else{
echo "N"."~".$index;
}
break;

case getCity2:
echo "City:<select  name='city'  id='city' style='width:165px;'><option value='all' >ALL</option>";
$model_query="SELECT distinct city FROM citymaster where state='$_REQUEST[value]' order by city";
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br[city]."'>";
echo $br['city']."</option>";
}
echo "<option value='Others'>Others</option>";
echo "</select>";
break;

case getPNApart_l4:
if($_REQUEST[tar]==1){
$i=2;
$pna=" and partcode!='$_REQUEST[value]'";
}else if($_REQUEST[tar]==2){
$i=3;
$pna=" and (partcode!='$_REQUEST[value]' and partcode!='$_REQUEST[value2]')";
}else{
$i="";
$pna="";
}
echo "<select name='pending_part$i' class='dropdown1'  id='pending_part$i'";
if($_REQUEST[tar]<=2){
echo "onChange='return funDisplay_pna(this.value,$i);'";
}else{}
echo ">  <option value='' selected='selected'> Select Pending Part</option>";
$rs_part1=mysqli_query($link1,"select distinct(partcode),name,cat from partcode_master where model='$_REQUEST[model]' and partcode not in (select partcode from client_inventroy where asc_code='$_SESSION[asc_code]' $pna and okqty > 0 ) and status='Active' $pna order by name ")or die("error in PartCode".mysqli_error($link1));
while($row_part1=mysqli_fetch_array($rs_part1)){
echo "<option value=".$row_part1[0]."~".$row_part1[2].">";
echo $row_part1[1]."(".$row_part1[0].")"."</option>";
}
echo "</select>";
break;

case getPNApart:
if($_REQUEST[tar]==1){
$i=2;
$pna=" and partcode!='$_REQUEST[value]'";
}else if($_REQUEST[tar]==2){
$i=3;
$pna=" and (partcode!='$_REQUEST[value]' and partcode!='$_REQUEST[value2]')";
}else{
$i="";
$pna="";
}
echo "<select name='pending_part$i' class='dropdown1'  id='pending_part$i'";
if($_REQUEST[tar]<=2){
echo "onChange='return funDisplay_pna(this.value,$i);'";
}else{}
echo ">  <option value='' selected='selected'> Select Pending Part</option>";
$rs_part1=mysqli_query($link1,"select distinct(partcode),name,cat from partcode_master where model='$_REQUEST[model]' and part_for='Both' and partcode not in (select partcode from client_inventroy where asc_code='$_SESSION[asc_code]' $pna and okqty > 0 ) and status='Active' $pna order by name ")or die("error in PartCode".mysqli_error($link1));
while($row_part1=mysqli_fetch_array($rs_part1)){
echo "<option value=".$row_part1[0]."~".$row_part1[2].">";
echo $row_part1[1]."(".$row_part1[0].")"."</option>";
}
echo "</select>";
break;

case getINOUTParts:
$tar=$_REQUEST[target];
echo "<select  name='item_id[$tar]' id='item_id[$tar]' class='dropdown' style='width:500px;'><option value=''>Select Partcode</option>";
$model_query="SELECT distinct partcode, name FROM partcode_master where model='$_REQUEST[value]' and status='Active' order by name";
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br[partcode]."'>";
echo $br[1].'-'.$br[0]."</option>";
}
echo "</select>~".$_REQUEST[target];
break;
case getRepair:
//echo $_SESSION[id_type];
if($_SESSION[id_type]=='L4' ||  $_SESSION[id_type]=='L3'){
$used_repair="1";
}else{
$used_repair="type='Both'";
}
$i=$_REQUEST[sn];
$model_query="SELECT rep_code,rep_desc FROM repair_code where( fault_cd LIKE '%$_REQUEST[value]%' ) and status='Active' and $used_repair";
echo "<select  class='dropdown1' name='repair_code$i' id='repair_code$i' onchange='return getData3(this.value,$i);' ><option value='' >Please Select Repair</option>";

$check1=mysqli_query($link1,$model_query);
if(mysqli_num_rows($check1)>0){
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br[0]."'>";
echo $br[1]."(".$br[0].")"."</option>";
}
echo "</select>";
}else{
$model_query2="SELECT rep_code,rep_desc FROM repair_code where status='Active' and $used_repair";
$check2=mysqli_query($link1,$model_query2);
while($br2 = mysqli_fetch_array($check2)){
echo "<option value='".$br2[0]."'>";
echo $br2[1]."(".$br2[0].")"."</option>";
}
echo "</select>";
}
break;

case getReplacedPart:
//$i=$_REQUEST[sn];
echo "<select name='rep_part' class='dropdown1'  id='rep_part'  onchange='funDisplay_pric_qty(this.value);'><option value='' >Please Select Repair</option>";
$model_query="SELECT partcode,part_desc FROM partcode_master where model='$_REQUEST[value]' and status='Active' and (cat='DOA_SET' or cat='COM_SET') group by partcode order by name";
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){
//$val_br=$br[0];
echo "<option value='".$br[0]."'>";
echo $br[0]." | ".$br[1]."</option>";
}
echo "</select>";
break;

case getReplPart_pric_qty:
//$part=$_POST[value];
$partcode=$_REQUEST[model];
$mod_typ=mysqli_fetch_array(mysqli_query($link1,"SELECT type FROM model_master where model='$_REQUEST[model2]' and status='Active'"));
$old_part=mysqli_fetch_array(mysqli_query($link1,"SELECT price FROM partcode_master where model='$_REQUEST[model]' and cat='COM_SET' and status='Active'"));
$rep_part=mysqli_fetch_array(mysqli_query($link1,"SELECT price FROM partcode_master where partcode='$_POST[value]' and status='Active'"));
$old_pric=$old_part[price];
$rep_pric=$rep_part[price];
$model_query="SELECT okqty FROM client_inventroy where partcode='$_POST[value]' and asc_code='$_SESSION[asc_code]'";
$check1=mysqli_query($link1,$model_query);
$br = mysqli_fetch_array($check1);
if($br[okqty]!='0' && $br[okqty]!=''){
$ci=$br[okqty];
}else{
$ci=0;
}
echo $ci."~".$old_pric."~".$rep_pric."~".$mod_typ[type];
break;

//////////////////////REPL at Admin/////////////////////
case getReplacedPart_ad:
//$i=$_REQUEST[sn];
echo "<select name='rep_part' class='dropdown1'  id='rep_part'  onchange='funDisplay_pric_qty_ad(this.value);'><option value='' >Please Select Repair</option>";
$model_query="SELECT partcode,part_desc FROM partcode_master where model='$_REQUEST[value]' and status='Active' and (cat='DOA_SET' or cat='COM_SET') group by partcode order by name";
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){
//$val_br=$br[0];
echo "<option value='".$br[0]."'";
if($_REQUEST['rep_p']==$br[0]) echo " selected";
echo ">".$br[0]." | ".$br[1]."</option>";
}
echo "</select>";
break;

case getReplPart_pric_qty_ad:
//$part=$_POST[value];
$partcode=$_REQUEST[model]."HS";
$mod_typ=mysqli_fetch_array(mysqli_query($link1,"SELECT type FROM model_master where model='$_REQUEST[model2]' and status='Active'"));
$old_part=mysqli_fetch_array(mysqli_query($link1,"SELECT distributer_price,price FROM partcode_master where partcode='$partcode' and status='Active'"));
$rep_part=mysqli_fetch_array(mysqli_query($link1,"SELECT distributer_price,price,cat FROM partcode_master where partcode='$_POST[value]' and status='Active'"));
$old_pric=$old_part[price];
$rep_pric=$rep_part[price];
$c_i="SELECT okqty FROM client_inventroy where partcode='$_POST[value]' and asc_code='$_REQUEST[asc]'";
$check1=mysqli_query($link1,$c_i);
$br = mysqli_fetch_array($check1);
if($br[okqty]!='0' && $br[okqty]!=''){
$ci=$br[okqty];
}else{
$ci=0;
}
$w_i="SELECT okqty FROM warehouse_inventory where partcode='$_POST[value]' and w_code='$_REQUEST[w_code]'";
$check2=mysqli_query($link1,$w_i);
$br2 = mysqli_fetch_array($check2);
if($br2[okqty]!='0' && $br2[okqty]!=''){
$wi=$br2[okqty];
}else{
$wi=0;
}
echo $ci."~".$wi."~".$old_pric."~".$rep_pric."~".$mod_typ[type]."~",$rep_part[cat];
break;

/////////////////////////////////////////////////////////////
case getLevel:

$level_query="SELECT * FROM repair_code where rep_code='$_REQUEST[value]' and status='Active'";
$check2=mysqli_query($link1,$level_query);
$br = mysqli_fetch_array($check2);
if($_REQUEST[value2]=='IN'){
echo $br[rep_level]."~".$br[part_replace]."~".$br[rep_code]."~".$_REQUEST[sn]."~".$_REQUEST[model]."~".$br[check_rep];
}else{
echo $br[rep_level]."~".$br[part_replace]."~".$br[rep_code]."~".$_REQUEST[sn]."~".$_REQUEST[model]."~".$br[check_rep];
}
break;

case getConsumption:
$i=$_REQUEST[sn];
//echo $_REQUEST[value];
echo "<select  class='dropdown1' name='part$i' id='part$i' onchange='return getData(this.value,$i);' ><option value='' >Please Select Part</option>";
//$model_query="SELECT distinct ci.partcode, pm.name FROM client_inventroy ci, partcode_master pm where ci.partcode=pm.partcode and pm.model='$_REQUEST[model]' and ci.okqty > 0  and ci.asc_code='$_SESSION[asc_code]' and (pm.rep_code LIKE '%$_REQUEST[value]%') order by pm.name";
$model_query="SELECT distinct ci.partcode, pm.name FROM client_inventroy ci, partcode_master pm where ci.partcode=pm.partcode and pm.model='$_REQUEST[model]' and ci.okqty > 0  and ci.asc_code='$_SESSION[asc_code]' and (pm.rep_code LIKE '%$_REQUEST[value]%') order by pm.name";
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br[0]."'>";
echo $br[1]."(".$br[0].")"."</option>";
}
echo "</select>";
break;	

case getSaleModel:
echo "<select  name='model'><option value='' >Please Select</option>";
$model_query="SELECT distinct model,make FROM sales_stock where make='$_REQUEST[value]' and (type='cs' and (okqty > 0 and asc_code='$_SESSION[asc_code]')) order by model";
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br[model]."'>";
echo $br['model']."</option>";
}
echo "</select>";
break;

case getReplaceModel:
echo "<select  name='model_new'><option value='' >Please Select</option>";
$model_query="SELECT distinct model,make FROM sales_stock where make='$_REQUEST[value]' and (type='cs' and (okqty > 0 and asc_code='$_SESSION[asc_code]')) order by model";
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br[model]."'>";
echo $br['model']."</option>";
}
echo "</select>";
break;

case getSaleAcc:
echo "<select  name='acc_name' onchange='return getPrice(this.value);' ><option value='' >Please Select acc</option>";
$model_query="SELECT distinct ci.partcode, pm.name, pm.model FROM client_inventroy ci, partcode_master pm where (ci.partcode=pm.partcode and pm.make='$_REQUEST[value]') and ci.okqty > 0  and ci.asc_code='$_SESSION[asc_code]' and pm.status='Active' order by pm.name";
// $model_query="SELECT partcode,name FROM partcode_master order by name";
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br[0]."'>";
echo $br[0]."-".$br[2]."-".$br[1]."</option>";
}
echo "</select>";
break;

case getACCtype:
echo "<select  name='acc1' class='required form-control' id='acc1'><option value='' >Please Select </option>";
$modsym="SELECT acc_name FROM acc_master where model='$_REQUEST[value]'";
$check3=mysqli_query($link1,$modsym);
while($br = mysqli_fetch_array($check3)){
echo "<option value='".$br[acc_name]."'>";
echo $br['acc_name']."</option>";
}
break;

case getModelNew:
echo "<select  name='model_new'><option value='' >Please Select</option>";
$model_query="SELECT distinct model,make FROM partcode_master where make='$_REQUEST[value]' and status='Active' order by model";
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br[model]."'>";
echo $br['model']."</option>";
}
echo "</select>";
break;

case getAcc:
echo "<select  name='acc_name' ><option value='' >Please Select</option>";
$model_query="SELECT distinct model,make FROM partcode_master where make='$_REQUEST[value]' and status='Active' order by model";
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br[model]."'>";
echo $br['model']."</option>";
}
echo "</select>";
break;

case getCost:
$chk_qty=mysqli_query($link1,"select okqty from client_inventroy where partcode='$_REQUEST[value]' and asc_code='$_SESSION[asc_code]'");
$chk_abl_qty=mysqli_fetch_array($chk_qty);
$abl=$chk_abl_qty[0];

$chk_hsn=mysqli_query($link1,"select hsn_code from partcode_master where partcode='$_REQUEST[value]'");
$chk_abl_hsn=mysqli_fetch_array($chk_hsn);
$hsn_code=$chk_abl_hsn['hsn_code'];


$part_query="SELECT price,distributer_price,cat FROM partcode_master where partcode='$_REQUEST[value]' and status='Active'";
$check1=mysqli_query($link1,$part_query);
$br = mysqli_fetch_array($check1);
if($_REQUEST[wrs]=="OUT" || $_REQUEST[wrs]=="VOID"){
$chk_tax=mysqli_query($link1,"select sgst,igst,cgst from tax_hsn_master where hsn_code='$hsn_code'");
$chk_abl_tax=mysqli_fetch_array($chk_tax);
if($_REQUEST['customerstate']==$_REQUEST['state']){

$cgst=$chk_abl_tax['cgst'];
$sgst=$chk_abl_tax['sgst'];
$igst="0.00";
}
else{
	$cgst="0.00";
$sgst="0.00";
$igst=$chk_abl_tax['igst'];
	
	}

}else{
$cgst="0.00";
$sgst="0.00";
$igst="0.00";
}
if($_REQUEST['sf_fg']!=""){
$price=$br['distributer_price'];
}else{
$price=$br['price'];
}

$cgstamt=($price*$cgst)/100;
$sgstamt=($price*$sgst)/100;
$igstamt=($price*$igst)/100;
$totalamt=$price+$cgstamt+$sgstamt+$igstamt;


echo $price."~".$abl."~".$hsn_code."~".$cgst."~".$sgst."~".$igst."~".$cgstamt."~".$sgstamt."~".$igstamt."~".$totalamt;
break;

case chkDate:
$lastyear  = mktime(0, 0, 0, date("m"),   date("d"),   date("Y")-1);
if($lastyear > $_REQUEST[dd]){
echo "IN";
}else{
echo "OUT";
}
break;
case getPartsok:
echo("hkhk");
$pa= "SELECT partcode from partcode_master where 1";
$c=mysqli_query($link1,$pa) or die("error2".mysqli_error($link1));
$b=mysqli_fetch_array($c);
echo $b[0]."~".$b[1];
break;


case getValue:
$model_query="SELECT ci.okqty, pm.price, pm.hsn_code  FROM client_inventroy ci, partcode_master pm where ci.partcode=pm.partcode and  ci.partcode='$_REQUEST[value]' and ci.asc_code='$_SESSION[asc_code]' and 

pm.status='Active'";;
$check1=mysqli_query($link1,$model_query);
$br = mysqli_fetch_array($check1);
$okqty=$br['okqty'];
$price =$br['price'];
$hsn_code=$br['hsn_code'];
$tax=mysqli_fetch_array(mysqli_query($link1,"select sgst,igst,cgst from tax_hsn_master where hsn_code='$hsn_code'"));
if($_REQUEST[customerstate]==$_REQUEST[aspstate]){
$sgst=$tax['sgst'];
$igst=0.00;
$cgst=$tax['cgst'];
}
else{
	$sgst=0.00;
$igst=$tax['igst'];
$cgst=0.00;
	}
	if($_REQUEST['asp_gstno']==''){
$cgst="0.00";
$sgst="0.00";
$igst="0.00";
	}
echo $okqty."~".$price."~".$hsn_code."~".$cgst."~".$sgst."~".$igst;
break;

/////////////Repl_Sec_IMEI///////////////////////////////
case getAutoSecReplImei:
//echo $_REQUEST[value];
$get_imei=mysqli_query($link1,"select imei1,imei2 from imei_data_import where (imei1='$_REQUEST[value]' or imei2='$_REQUEST[value]') order by id desc") or die("error in sec_imei".mysqli_error($link1));
$check_val=mysqli_num_rows($get_imei);
$get_sec_imei=mysqli_fetch_array($get_imei);
if($get_sec_imei[imei1]==$_REQUEST[value]){
$imei_value=$get_sec_imei[imei2];	
}else{
$imei_value=$get_sec_imei[imei1];
}
echo $check_val."~".$imei_value;
break;

//////////////////////////////////////////////////////////
case getValue:
$model_query="SELECT ci.okqty, pm.price  FROM client_inventroy ci, partcode_master pm where ci.partcode=pm.partcode and  ci.partcode='$_REQUEST[value]' and ci.asc_code='$_SESSION[asc_code]' and pm.status='Active'";
$check1=mysqli_query($link1,$model_query);
$br = mysqli_fetch_array($check1);
echo $br[0]."~".$br[1];
break;

case getValue_asc:
if($_SESSION[id_type]=='WH'){
$model_query="SELECT okqty FROM warehouse_inventory where partcode='$_REQUEST[value]' and W_code='$_SESSION[asc_code]'";
}else{
$model_query="SELECT okqty FROM client_inventroy where partcode='$_REQUEST[value]' and asc_code='$_SESSION[asc_code]'";
}
$check1=mysqli_query($link1,$model_query);
$br = mysqli_fetch_array($check1);
if($br[okqty]!='0' && $br[okqty]!=''){
$ci=$br[okqty];
}else{
$ci=0;
}
$part_query="SELECT distributer_price,cat FROM partcode_master where partcode='$_REQUEST[value]' and status='Active'";
$check11=mysqli_query($link1,$part_query);
$br1 = mysqli_fetch_array($check11);
echo $ci."~".$br1[0]."~".$br1[1]."~".$_REQUEST[field];
break;

case getValue_wh:
$model_query="SELECT okqty FROM warehouse_inventory where partcode='$_REQUEST[value]' and w_code='$_SESSION[asc_code]'";
$check1=mysqli_query($link1,$model_query);
$br = mysqli_fetch_array($check1);
if($br[okqty]!='0' && $br[okqty]!=''){
$ci=$br[okqty];
}else{
$ci=0;
}
$part_query="SELECT distributer_price FROM partcode_master where partcode='$_REQUEST[value]' and status='Active'";
$check11=mysqli_query($link1,$part_query);
$br1 = mysqli_fetch_array($check11);
echo $ci."~".$br1[0]."~".$_REQUEST[field];
break;	
				
case getModelType:
$level_query="SELECT type,make FROM model_master where model='$_REQUEST[value]' and status='Active'";
$check2=mysqli_query($link1,$level_query);
$br = mysqli_fetch_array($check2);
echo $br[type]."~".$br[make];
break;
case getModelTypedoa:
$br1=mysqli_fetch_array(mysqli_query($link1,"SELECT model FROM partcode_master where partcode='$_REQUEST[value]' and status='Active'"));
$level_query="SELECT type FROM model_master where model='$br1[model]' and status='active'";
$check2=mysqli_query($link1,$level_query);
$br = mysqli_fetch_array($check2);
echo $br[type];
break;

case getParts:
$tar=$_REQUEST[target];
$trimLastChr=rtrim($_REQUEST[chkStr],"~");
$exp_str=explode("~",$trimLastChr);
for($i=0;$i<count($exp_str);$i++){
if($exp_str[0]!=''){
if($makeStr==""){
$makeStr.="'".$exp_str[$i]."'";
}else{
$makeStr.=",'".$exp_str[$i]."'";
}
}
}
if($makeStr){ $chkDupli=$makeStr;}else{$chkDupli="''";}
//echo $makeStr;
echo "<select  name='item_id[$tar]' id='item_id[$tar]' class='dropdown1' onchange='getField($_REQUEST[target],this.value);duplicate_entry($_REQUEST[target],this.value);'><option value='' >Please Select--</option>";
if($_SESSION[id_type]=='ASP'){
$model_query="SELECT distinct partcode, name FROM partcode_master where model='$_REQUEST[value]' and (cat!='DOA_SET' and cat!='COM_SET') and part_for='Both' and status='Active' and partcode not in ($chkDupli) order by name";
}
elseif($_SESSION[id_type]=='DOAWH'){
$model_query="SELECT distinct partcode, name FROM partcode_master where model='$_REQUEST[value]' and cat='DOA_SET' and status='Active' and partcode not in ($chkDupli) order by name";
}
else
{
$model_query="SELECT distinct partcode, name FROM partcode_master where model='$_REQUEST[value]'  and status='Active' and partcode not in ($chkDupli) order by name";
}
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br[partcode]."'>";
echo $br[1].'-'.$br[0]."</option>";
}
echo "</select>~".$_REQUEST[target];
break;

case getValue6:
$model_query="SELECT okqty FROM client_inventroy  where partcode='$_REQUEST[value]' and asc_code='$_SESSION[asc_code]'";
$check1=mysqli_query($link1,$model_query);
$br = mysqli_fetch_array($check1);
$model_query2="SELECT distributer_price,land_price FROM  partcode_master where partcode='$_REQUEST[value]' and status='Active'";
$check2=mysqli_query($link1,$model_query2);
$br2 = mysqli_fetch_array($check2);
if($_SESSION[id_type]=='L4'){
$part_price=$br2[land_price];
}else{
$part_price=$br2[distributer_price];
}
echo $br[0]."~".$part_price;
break;

case getValuePrice:
$model_query="SELECT okqty FROM warehouse_inventory  where partcode='$_REQUEST[value]' and w_code='$_SESSION[asc_code]'";
$check1=mysqli_query($link1,$model_query);
$br = mysqli_fetch_array($check1);
$model_query2="SELECT distributer_price,land_price FROM  partcode_master where partcode='$_REQUEST[value]' and status='Active'";
$check2=mysqli_query($link1,$model_query2);
$br2 = mysqli_fetch_array($check2);
$part_price=$br2[land_price];
echo $br[0]."~".$part_price;
break;

case getdop:
$chk_rfb=mysqli_query($link1,"select * from imei_data_refurb where (imei1='$_REQUEST[imei1]' or imei2='$_REQUEST[imei1]') and status=''") or die("error in refurb".mysqli_error($link1));
$sql_job_dop=mysqli_query($link1,"select dop from jobsheet_data where (imei='$_REQUEST[imei1]' or sec_imei='$_REQUEST[imei1]') and status!='cancel'") or die("error in jobsheet".mysqli_error($link1));
$get_job_dop=mysqli_num_rows($sql_job_dop);
$sql_secIMEI="SELECT sale_date FROM imei_data_auto where (imei1='$_REQUEST[imei1]' or imei2='$_REQUEST[imei1]') order by id desc";
$res_secIMEI=mysqli_query($link1,$sql_secIMEI);
$secIMEI = mysqli_fetch_array($res_secIMEI);
if(mysqli_num_rows($chk_rfb)==0){
if(mysqli_num_rows($res_secIMEI)>0 && $get_job_dop==0){
$get_dop=$secIMEI[sale_date];
}
else if($get_job_dop>0){
$dop_val=mysqli_fetch_array($sql_job_dop);
$get_dop=$dop_val[dop];	
}else{
$get_dop='';
}
}else{
$get_dop='';
}
break;

case getdop_doa:
$chk_rfb=mysqli_query($link1,"select * from imei_data_refurb where (imei1='$_REQUEST[imei1]' or imei2='$_REQUEST[imei1]') and status=''") or die("error in refurb".mysqli_error($link1));
//echo "select dop from jobsheet_data where (imei='$_REQUEST[imei1]' or sec_imei='$_REQUEST[imei1]') and (status!='cancel' or call_of!='Distributor')";
$sql_job_dop=mysqli_query($link1,"select dop from jobsheet_data where (imei='$_REQUEST[imei1]' or sec_imei='$_REQUEST[imei1]') and (status!='cancel' and call_of!='Distributor')") or die("error in jobsheet".mysqli_error($link1));
$get_job_dop=mysqli_num_rows($sql_job_dop);
$sql_secIMEI="SELECT sale_date FROM imei_data_auto where (imei1='$_REQUEST[imei1]' or imei2='$_REQUEST[imei1]') order by id desc";
$res_secIMEI=mysqli_query($link1,$sql_secIMEI);
$secIMEI = mysqli_fetch_array($res_secIMEI);
if(mysqli_num_rows($chk_rfb)==0){
if(mysqli_num_rows($res_secIMEI)>0 && $get_job_dop==0){
$get_dop=$secIMEI[sale_date];
}
else if($get_job_dop>0){
$dop_val=mysqli_fetch_array($sql_job_dop);
$get_dop=$dop_val[dop];	
}else{
$get_dop='';
}
}else{
$get_dop='';
}
//$sql_dop=mysqli_query($link1,"SELECT sale_date FROM imei_data_auto where model='$_REQUEST[model]' and (imei1='$_REQUEST[imei1]' or imei2='$_REQUEST[imei1]') order by id desc");
//$sale_dt=mysqli_fetch_array($sql_dop);
echo $get_dop;
break;

case getsecimei:
$sql_secimei="SELECT imei1,imei2 FROM imei_data_import where (imei1='$_REQUEST[imei1]' or imei2='$_REQUEST[imei1]') order by id desc";
$res_secimei=mysqli_query($link1,$sql_secimei);
$secimei = mysqli_fetch_array($res_secimei);
if(trim($secimei[imei1])==trim($_REQUEST[imei1])){
echo $secimei[imei2];
}else {
echo $secimei[imei1];
}
break;

case getSecIMEIdoa:
$sql_secIMEI="SELECT imei1,imei2 FROM imei_data_import where (imei1='$_REQUEST[imei1]' or imei2='$_REQUEST[imei1]') order by id desc";
$res_secIMEI=mysqli_query($link1,$sql_secIMEI);
$secIMEI = mysqli_fetch_array($res_secIMEI);
if($secIMEI[imei1]==$_REQUEST[imei1]){
echo $secIMEI[imei2];
}
else{
echo $secIMEI[imei1];
}
break;

case getpartcode:
$sqlpart="SELECT partcode,asc_code,sum(req_qty) as qty FROM  part_request  where partcode='$_REQUEST[part]' and asc_code='$_SESSION[asc_code]' and status='pending' group by partcode";
$respart=mysqli_query($link1,$sqlpart);
$secpart = mysqli_fetch_array($respart);
if($secpart[partcode]==$_REQUEST[part]){
echo $secpart[qty]."~".$secpart[partcode];
}
break;

case getSymtom:
echo "<select  name='symp_code' class='dropdown'><option value='' >Please Select </option>";
$modsym="SELECT symp_desc FROM symptom_code where type='$_REQUEST[value]'";
$check3=mysqli_query($link1,$modsym);
while($br = mysqli_fetch_array($check3)){
echo "<option value='".$br[symp_desc]."'>";
echo $br['symp_desc']."</option>";
}
break;
//////////////////////////////
case getCity_dist:
echo "City: <span class=red_small> *</span><select  name='city2' id='city2' onchange='return getDname(this.value);' class='dropdown'><option value='' >Select City</option>";
$model_query="SELECT distinct city FROM distributer_master where state='$_REQUEST[value]' order by city";
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){
echo "<option value='".$br[city]."'>";
echo $br['city']."</option>";
}
echo "</select>";
break;
/////////////////////////////

case getPartPriceCat:
/// get WH stock/
$wh_stock=mysqli_fetch_array(mysqli_query($link1,"select okqty from warehouse_inventory where w_code='$_SESSION[asc_code]' and partcode='$_REQUEST[value]'"));
$part_details=mysqli_fetch_array(mysqli_query($link1,"select distributer_price , cat from partcode_master where partcode='$_REQUEST[value]' and status='Active' group by partcode"));
/// get tax details
$tax=mysqli_fetch_array(mysqli_query($link1,"select per from tax_master where name='VAT' and status='Active' and state='$_SESSION[state]' and cat='$part_details[1]'"));
if($wh_stock[0]){ $return_stock=$wh_stock[0];} else{ $return_stock="0";}
if($part_details[0]){ $return_partPrice=$part_details[0];} else{ $return_partPrice="0.00";}
if($tax[0]){ $return_tax=$tax[0];} else{ $return_tax="0.00";}
echo $return_stock."~".$return_partPrice."~".$part_details[1]."~".$tax[0];
break;

//// get Vendor Address
case getPartyaddress:

$query1="SELECT address FROM vendor_master where id='$_REQUEST[value]'";
$check1=mysqli_query($link1,$query1);
if(mysqli_num_rows($check1)>0){
$br = mysqli_fetch_array($check1);
echo $br[0];
}
else{
$query2="SELECT addrs,state  FROM asc_master where uid='$_REQUEST[value]'";
$check2=mysqli_query($link1,$query2);
$br1 = mysqli_fetch_array($check2);
echo $br1[0]."~".$br1[1];
}
break;
#### End Vendor Address

#################################################Address for fetching billing ###########################


case getPartyaddressbill:

$query1="SELECT address FROM vendor_master where id='$_REQUEST[value]'";
$query_comp="SELECT addrs  FROM asc_master where asc_code='$_REQUEST[value]'";
$chk_comp=mysqli_query($link1,$query_comp);
$check1=mysqli_query($link1,$query1);

if(mysqli_num_rows($check1)>0){
$br = mysqli_fetch_array($check1);
echo $br[0];
}
else if(mysqli_num_rows($chk_comp)>0){
$br_comp=mysqli_fetch_array($chk_comp);
echo $br_comp[0];
}
else  {
$query2="SELECT bill_addrs  FROM wh_master where uid='$_REQUEST[value]'";
$check2=mysqli_query($link1,$query2);
$br1 = mysqli_fetch_array($check2);
echo $br1[0];
}
break;
#################################################
}
?>