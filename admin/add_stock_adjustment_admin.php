<?php



require_once("../includes/config.php");



/////// get Access state////////////////////////



$arrstate = getAccessState($_SESSION['userid'],$link1);











?>



<!DOCTYPE html>



<html>



<head>



  <meta charset="utf-8">







 <meta name="viewport" content="width=device-width, initial-scale=1">







 <title><?=siteTitle?></title>



 



 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">



 <script src="../js/jquery.js"></script>



 <link href="../css/font-awesome.min.css" rel="stylesheet">



 <link href="../css/abc.css" rel="stylesheet">



 <script src="../js/bootstrap.min.js"></script>



 <link href="../css/abc2.css" rel="stylesheet">



 <link rel="stylesheet" href="../css/bootstrap.min.css">



 <link rel="stylesheet" href="../css/bootstrap-select.min.css">



 <script src="../js/bootstrap-select.min.js"></script>



 <script language="javascript" type="text/javascript">



  







 function makeDropdown(){



		$('.selectpicker').selectpicker();



   }







//////////////////////   get available stock and description //////////////////////////////////



	function getAvlStk(ind){



  var partcode=document.getElementById("partcode["+ind+"]").value;



   $.ajax({



	    type:'post',



		url:'../includes/getAzaxFields.php',



		data:{avlstock:partcode,indxx:ind,location:'<?=$_REQUEST['location']?>'},



		success:function(data){



	



		var getValue = data.split("~");	



		document.getElementById("descp["+getValue[4]+"]").value=getValue[3];	



		if(getValue[0] != ''){



		document.getElementById("abl_ok["+getValue[4]+"]").value=getValue[0];	



		}



		else { document.getElementById("abl_ok["+getValue[4]+"]").value=0;	}



		if(getValue[1] != ''){



		document.getElementById("abl_damage["+getValue[4]+"]").value=getValue[1];	}



		else{ document.getElementById("abl_damage["+getValue[4]+"]").value= 0;}



		if(getValue[2] != ''){



		document.getElementById("abl_missing["+getValue[4]+"]").value=getValue[2];	 }



		else {



		document.getElementById("abl_missing["+getValue[4]+"]").value=0;	



			}



			



			



		}







		});



		//document.getElementById("save").disabled = false;



	}



// Check Mandatory Form Fields////



function chk_data(){



var error=false;



var errorMsg="Sorry we can not complete your request.Following Information is missing: \n";



doc=document.form1;



var e="partcode[0]";



var ok_q="ok_qty[0]";



var damg_q="damage_qty[0]";



var miss_q="missing_qty[0]";



if(document.getElementById(e).value==""){



errorMsg+="Select atleast one Entry. \n";



document.getElementById("save").style.visibility="";



error=true;



}



if((document.getElementById(ok_q).value=="" || document.getElementById(ok_q).value=="0") && (document.getElementById(damg_q).value=="" || document.getElementById(damg_q).value=="0") && (document.getElementById(miss_q).value=="" || document.getElementById(miss_q).value=="0")){



errorMsg+="Enter Adjust Qty. \n";



document.getElementById("save").style.visibility="";



error=true;



}



var num = (document.getElementById("theValue").value);



//alert(num);



for(j=0;j<=num;j++){



  if(document.getElementById("partcode["+j+"]").value==""){



  errorMsg+="Select atleast one Partcode on row no."+(j+1)+". \n";



  document.getElementById("save").style.visibility="";



  error=true;



  }



  if(document.getElementById("ok_type["+j+"]").value=="" && document.getElementById("damage_type["+j+"]").value=="" && document.getElementById("missing_type["+j+"]").value==""){



  errorMsg+="Select atleast one stock Type on row no."+(j+1)+". \n";



  document.getElementById("save").style.visibility="";



  error=true;



  }



  if((document.getElementById("ok_qty["+j+"]").value=="" || document.getElementById("ok_qty["+j+"]").value=="0") && (document.getElementById("damage_qty["+j+"]").value=="" || document.getElementById("damage_qty["+j+"]").value=="0") && (document.getElementById("missing_qty["+j+"]").value=="" || document.getElementById("missing_qty["+j+"]").value=="0")){



  errorMsg+="Enter Adjust Qty on row no."+(j+1)+". for one type of stock \n";



  document.getElementById("save").style.visibility="";



  error=true;



  }



  



  if((document.getElementById("ok_qty["+j+"]").value!="" && document.getElementById("ok_qty["+j+"]").value!="0" && document.getElementById("ok_type["+j+"]").value=="") || (document.getElementById("damage_qty["+j+"]").value!="" && document.getElementById("damage_qty["+j+"]").value!="0" && document.getElementById("damage_type["+j+"]").value=="") || (document.getElementById("missing_qty["+j+"]").value!="" && document.getElementById("missing_qty["+j+"]").value!="0" && document.getElementById("missing_type["+j+"]").value=="")){



  errorMsg+="select stock type for Adjust stock on row no."+(j+1)+". \n";



  document.getElementById("save").style.visibility="";



  error=true;



  }



  



   if((document.getElementById("abl_ok["+j+"]").value =="") || (document.getElementById("abl_damage["+j+"]").value =="") || (document.getElementById("abl_missing["+j+"]").value=="")){



  errorMsg+="Avalibale Stock is not available on row no."+(j+1)+". \n";



  document.getElementById("save").style.visibility="";



  error=true;



  }



  



 



}



/*if(error==true){



alert(errorMsg);



document.getElementById("save").disabled = true; 



return false;







}else{ 



document.getElementById("save").disabled = false;



return true;



} 



}*/







if(error==true){



alert(errorMsg);



return false;



}else{ 



hideThis("save");



return true;



} 



}



function hideThis(val){



if(val!="" ){



document.getElementById(val).style.display= 'none';



}



}







///////////////////////



///// Add new Row////////////////



$(document).ready(function(){



    $("#add_row").click(function(){



		var numi = document.getElementById('theValue');



		var preno=document.getElementById('theValue').value;



		var num = (document.getElementById("theValue").value -1)+2;



		numi.value = num;



	var r= "<tr><td width='2%'><input name='sno["+num+"]' id='sno["+num+"]' type='text' class='form-control' size='3' value="+(num+1)+" readonly='readonly' style='width:35px'/></td><td><select name='partcode["+num+"]' id='partcode["+num+"]'  class='form-control selectpicker' data-live-search='true' onChange='getAvlStk("+num+")' ><option value='' selected='selected'> Select Partcode </option><?php $model_query="SELECT partcode, part_name, part_category,vendor_partcode FROM partcode_master where  status='1' order by part_name";$check1=mysqli_query($link1,$model_query);while($br = mysqli_fetch_array($check1)){?><option data-tokens='<?php echo $br['partcode']." | ".$br['vendor_partcode']." | ".$br['part_name'];?>' value='<?php echo $br['partcode'];?>'><?php echo  $br['part_name']."  |  ".$br['vendor_partcode'];?></option><?php }?></select></td><td width='18%' align='left'><input name='descp["+num+"]' id='descp["+num+"]' type='text' class='form-control' style='width:132px;' readonly='readonly'/></td><td align='center' width='5%'><select name='ok_type["+num+"]' id='ok_type["+num+"]' class='form-control' style='background:#D4FFFF;' ><option value=''>Select</option><option value='P'>+</option><option value='M'>-</option></select></td><td width='5%' align='center'><input name='ok_qty["+num+"]' id='ok_qty["+num+"]' type='text' class='form-control' size='10' onblur=myFunction(this.value,"+num+",'ok_qty');fillCost("+num+"); onKeyPress='return onlyFloatNum(this.value);' style='width:70px;text-align:right;background:#D4FFFF;' value='0'/></td><td width='5%' align='center'><input name='abl_ok["+num+"]' id='abl_ok["+num+"]' type='text' class='form-control' style='width:70px; text-align:right;' size='10' readonly='readonly'/></td><td align='center' width='5%'><select name='damage_type["+num+"]' id='damage_type["+num+"]' class='form-control' style='background:#D4FFFF;'><option value=''>Select</option><option value='P'>+</option><option value='M'>-</option></select></td><td width='5%' align='center'><input name='damage_qty["+num+"]' id='damage_qty["+num+"]' type='text' class='form-control' size='10' onblur=myFunction(this.value,"+num+",'damage_qty');fillCost("+num+"); onKeyPress='return onlyFloatNum(this.value);' style='width:70px;text-align:right;background:#D4FFFF;' value='0'/></td><td width='5%' align='center'><input name='abl_damage["+num+"]' id='abl_damage["+num+"]' type='text' class='form-control' style='width:70px; text-align:right;' size='10' readonly='readonly'/></td><td align='center' width='5%'><select name='missing_type["+num+"]' id='missing_type["+num+"]' class='form-control' style='background:#D4FFFF;'><option value=''>Select</option><option value='P'>+</option><option value='M'>-</option></select></td><td width='5%' align='center'><input name='missing_qty["+num+"]' id='missing_qty["+num+"]' type='text' class='form-control' size='10' onblur=myFunction(this.value,"+num+",'missing_qty');fillCost("+num+"); onKeyPress='return onlyFloatNum(this.value);' style='width:70px;text-align:right;background:#D4FFFF;' value='0'/></td><td width='5%' align='center'><input name='abl_missing["+num+"]' id='abl_missing["+num+"]' type='text' class='form-control' style='width:70px; text-align:right;' size='10' readonly='readonly'/></td></tr>";



$('#itemsTable1').append(r);



  makeDropdown();



 });



});



//// convert 01 string in 1



function myFunction(val,ind,idval)



{



	//alert(idval);



var test5 = new String(val);



var n = Number(test5);



    document.getElementById(idval+"["+ind+"]").value=n;



}











//////////////////



function  fillCost(ind){



  var sel_ok_type="ok_type["+ind+"]";



  var ent_ok_qty="ok_qty["+ind+"]";



  var ablokQty="abl_ok["+ind+"]";



  



  var sel_damg_type="damage_type["+ind+"]";



  var ent_damg_qty="damage_qty["+ind+"]";



  var abldamgQty="abl_damage["+ind+"]";



  



  var sel_miss_type="missing_type["+ind+"]";



  var ent_miss_qty="missing_qty["+ind+"]";



  var ablmissQty="abl_missing["+ind+"]";



    



  var okType=document.getElementById(sel_ok_type).value;



  var damgType=document.getElementById(sel_damg_type).value;



  var missType=document.getElementById(sel_miss_type).value;



  



  //// Check if OK type is minus(-)



  if(okType == "M"){



    if(parseFloat(document.getElementById(ent_ok_qty).value) > parseFloat(document.getElementById(ablokQty).value)){



     alert("Entered Adjust OK Qty is more than Available OK Qty.");



     document.getElementById(ent_ok_qty).value='0';



     document.getElementById(ent_ok_qty).focus();



     return false;



    }



  }



  //// Check if DAMAGE type is minus(-)



  if(damgType == "M"){



    if(parseFloat(document.getElementById(ent_damg_qty).value) > parseFloat(document.getElementById(abldamgQty).value)){



     alert("Entered Adjust DAMAGE Qty is more than Available DAMAGE Qty.");



     document.getElementById(ent_damg_qty).value='0';



     document.getElementById(ent_damg_qty).focus();



     return false;



    }



  }



  //// Check if OK type is minus(-)



  if(missType == "M"){



    if(parseFloat(document.getElementById(ent_miss_qty).value) > parseFloat(document.getElementById(ablmissQty).value)){



     alert("Entered Adjust MISSING Qty is more than Available MISSING Qty.");



     document.getElementById(ent_miss_qty).value='0';



     document.getElementById(ent_miss_qty).focus();



     return false;



    }



  }



  calculatetotal();



}



function calculatetotal(){



var rowno=(document.getElementById("theValue").value);



var sum_ok_qty=0;



var sum_dam_qty=0;



var sum_miss_qty=0;



for(var i=0;i<=rowno;i++){



var temp_ok_qty="ok_qty["+i+"]";



var temp_dam_qty="damage_qty["+i+"]";



var temp_miss_qty="missing_qty["+i+"]";



sum_ok_qty+=parseFloat(document.getElementById(temp_ok_qty).value);



sum_dam_qty+=parseFloat(document.getElementById(temp_dam_qty).value);



sum_miss_qty+=parseFloat(document.getElementById(temp_miss_qty).value);



}



document.getElementById("total_ok_qty").value=sum_ok_qty;



document.getElementById("total_damage_qty").value=sum_dam_qty;



document.getElementById("total_missing_qty").value=sum_miss_qty;



}



</script>



<script type="text/javascript" src="../js/jquery.validate.js"></script>



<script type="text/javascript" src="../js/common_js.js"></script>



<title><?=siteTitle?></title>



</head>



<body>



<div class="container-fluid">



  <div class="row content">



	<?php 



   include("../includes/leftnav2.php");



    ?>



    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">



      <h2 align="center"><i class="fa fa-adjust"></i> Stock Adjustment</h2>



      <?php if($_REQUEST['msg']){?>



        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">



            <button type="button" class="close" data-dismiss="alert" aria-label="Close">



                <span aria-hidden="true">&times;</span>



              </button>



            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.



        </div>



        <?php }?>



        <br><br/>



			<form name="form2" id="form2"   method="post"  onSubmit="return chk_data();"><tr><td><div id= "dt_range" class="col-md-6"><label class="col-md-5 control-label">Select Location <span style="color:#F00">*</span></label>	  



			<div class="col-md-6 input-append date" align="left">



			 <select   name="location" id="location"  class="form-control required" onChange="document.form2.submit();"   required>



			 <option value="" <?php if($_REQUEST['location']=="") { echo 'selected'; } ?>>Please Select</option>



				<?php 



                $loc = mysqli_query($link1,"select location_code, locationname from location_master  where statusid= '1'  and  locationtype in  ('ASP','L3','L4','WH') and stateid in($arrstate) order by locationname " ); 



                while($locinfo = mysqli_fetch_assoc($loc)){ 



				?>		



             <option value="<?=$locinfo['location_code']?>" <?php if($_REQUEST['location']==$locinfo['location_code']) { echo 'selected'; } ?>><?=$locinfo['locationname']."(".$locinfo['location_code'].")"?></option>



                <?php }?>



	</select>



            </div>



          </div></td></tr></form>



		<form name="form1" id="form1" action="save_stock_adjust_admin.php"   method="post"  onSubmit="return chk_data();">



         <table width="110%" cellpadding="1" cellspacing="1"  border="0" align="left">



		 



		  <tr>&nbsp;</tr>



          <tr>



            <td width="110%" colspan="3" align="left" >



             <table  width="110%"   align="center" cellpadding="4"  id="itemsTable1" cellspacing="0" border="1">



                <tr class="<?=$tableheadcolor?>">



                  <th width="2%" height="24" rowspan="2"><div align="center"><strong>S.No.</strong></div></th>



                  <th width="8%" rowspan="2"><div align="center"><strong>Partcode</strong></div></th>



                  <th width="12%" rowspan="2"><div align="center"><strong>Description</strong></div></th>



                  <th width="20%" colspan="3" height="25"><div align="center"><strong> OK QTY</strong></div></th>



                  <th width="20%" colspan="3"><div align="center"><strong>Damage QTY</strong></div></th>



                  <th width="20%" colspan="3"><div align="center"><strong>Missing QTY</strong></div></th>



                  



                </tr>



                <tr class="<?=$tableheadcolor?>">



                  <th width="8%"><div align="center"><strong>Type</strong></div></th>



                  <th width="5%" height="25"><div align="center"><strong>Adjust QTY</strong></div></th>



                  <th width="5%"><div align="center"><strong>Available QTY</strong></div></th>



                  <th width="8%"><div align="center"><strong>Type</strong></div></th>



                  <th width="5%"><div align="center"><strong>Adjust QTY</strong></div></th>



                  <th width="5%"><div align="center"><strong>Available QTY</strong></div></th>



                  <th width="8%"><div align="center"><strong>Type</strong></div></th>



                  <th width="5%"><div align="center"><strong>Adjust QTY</strong></div></th>



                  <th width="5%"><div align="center"><strong>Available QTY</strong></div></th>







                </tr>



                <tr>



                  <td><input name="sno[0]"  id="sno[0]" type="text" class="form-control" value="1" size="3" readonly="readonly" style="width:35px" /></td>



				  <input type="hidden" name="location_code" id="location_code" value="<?php echo $_REQUEST['location'] ?>">



                  <td><select name="partcode[0]" id="partcode[0]"  class="form-control selectpicker" data-live-search="true" onChange="getAvlStk(0)">



							<option value="" selected="selected"> Select Partcode</option>



							 <?php 



								$model_query="SELECT partcode, part_name, part_category,customer_partcode,vendor_partcode FROM partcode_master where  status='1' order by part_name";



			        			$check1=mysqli_query($link1,$model_query);



			        			while($br = mysqli_fetch_array($check1)){?>



                    				<option data-tokens="<?php echo $br['partcode']." | ".$br['customer_partcode']." | ".$br['part_name'];?>" value="<?php echo $br['partcode'];?>"><?php echo $br['part_name']."  |  ".$br['vendor_partcode'];?>



									</option>



                    		<?php }?>



						</select></td>



                  <td><input name="descp[0]" id="descp[0]" class="form-control" style="width:132px;" readonly="readonly"/></td>



                  <td align="center"><select name="ok_type[0]" id="ok_type[0]"  class="form-control" style="background:#D4FFFF;" ><option value="">Select</option><option value="P">+</option><option value="M">-</option></select></td>



                  <td align="center"><input name="ok_qty[0]" id="ok_qty[0]" type="text" class="digits  form-control" onBlur="myFunction(this.value,0,'ok_qty');fillCost(0);"  size="10" style="width:70px; text-align:right;background:#D4FFFF" value="0"/></td>



                  <td align="center"><input name="abl_ok[0]" id="abl_ok[0]" type="text" class="form-control" style="width:70px; text-align:right;" size="10" readonly="readonly"/></td>



                  



                  <td align="center"><select name="damage_type[0]" id="damage_type[0]"  class="form-control" style="background:#D4FFFF;" ><option value="">Select</option><option value="P">+</option><option value="M">-</option></select></td>



                  <td align="center"><input name="damage_qty[0]" id="damage_qty[0]" type="text" class="digits  form-control" onBlur="myFunction(this.value,0,'damage_qty');fillCost(0);"  size="10" style="width:70px; text-align:right;background:#D4FFFF" value="0"/></td>



                  <td align="center"><input name="abl_damage[0]" id="abl_damage[0]" type="text" class="form-control" style="width:70px; text-align:right;" size="10" readonly="readonly"/></td>



                  



                  <td align="center"><select name="missing_type[0]" id="missing_type[0]" class="form-control" style="background:#D4FFFF;" ><option value="">Select</option><option value="P">+</option><option value="M">-</option></select></td>



                  <td align="center"><input name="missing_qty[0]" id="missing_qty[0]" type="text" class="digits  form-control" onBlur="myFunction(this.value,0,'missing_qty');fillCost(0);" size="10" style="width:70px; text-align:right;background:#D4FFFF" value="0"/></td>



                  <td align="center"><input name="abl_missing[0]" id="abl_missing[0]" type="text" class="form-control" style="width:70px; text-align:right;" size="10" readonly="readonly"/></td>



             



                </tr>



              </table>



              <div align="left">



              				<tr class="0">



                				<td colspan="7" style="font-size:13px;">



                                	<a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a><input type="hidden" name="theValue" id="theValue" value="0"/>



                                </td>



              				</tr>



            			</div></td>



          </tr>



          <tr><td colspan="3" valign="top" class="lable" align="left"><table width="100%" border="0" cellpadding="1" cellspacing="1">



               <tr>



                  <td width="2%">&nbsp;</td>



                  <td width="8%">&nbsp;</td>



                  <td width="22%"><div align="right">Total Items :</div></td>



                  <td width="5%" align="center">&nbsp;</td>



                  <td width="5%" align="center"><input name="total_ok_qty" type="text" class="form-control" style="width:80px; text-align:right;" value="0" id="total_ok_qty" readonly="readonly"/></td>



                  <td width="5%" align="center">&nbsp;</td>



                  <td width="5%" align="center">&nbsp;</td>



                  <td width="5%" align="center">&nbsp;</td>



                  <td width="5%" align="center"><input name="total_damage_qty" type="text" class="form-control" style="width:80px; text-align:right;" value="0" id="total_damage_qty" readonly="readonly"/></td>



                  <td width="5%" align="center">&nbsp;</td>



                  <td width="5%" align="center">&nbsp;</td>



                  <td width="5%" align="center">&nbsp;</td>



                  <td width="5%" align="center"><input name="total_missing_qty" type="text" class="form-control" style="width:80px; text-align:right;" value="0" id="total_missing_qty" readonly="readonly"/></td>



                  <td width="5%" align="center">&nbsp;</td>



                  <td width="5%" align="center">&nbsp;</td>



                  <td>&nbsp;</td>



              



                </tr>  



       <tr><td>&nbsp;</td></tr>



                             



               <tr> 



                 <td>&nbsp;</td>



                 <td>&nbsp;</td>            



                 <td><div align="right">Remark :</div></td>



                 <td align="center">&nbsp;</td>



                 <td colspan="3" align="left"><textarea name="remark" id="remark" cols="25" rows="3" style="background:#D4FFFF;resize:none"></textarea></td>



                 <td align="center">&nbsp;</td>



                 <td align="center">&nbsp;</td>



                 <td align="center">&nbsp;</td>



                 <td align="center">&nbsp;</td>



                 <td align="center">&nbsp;</td>



                 <td align="center">&nbsp;</td>



                 <td align="center">&nbsp;</td>



                 <td align="center">&nbsp;</td>



                 <td>&nbsp;</td>



               </tr>



           </table>



           </td>



          </tr>



          



          <tr>



            <td align="center" colspan="3" height="38"><input name="save" type="Submit" id="save" class="btn<?=$btncolor?>" value="Process!"  />



            &nbsp;



            <input name="req" id="req" type="button" class="btn<?=$btncolor?>" onClick="window.location='adminstock_adjustment_admin.php'" value="Back"></td>



           



          </tr>



        </table>



        </form>



    	</div>



    </div>



  </div>



</div>



<?php



include("../includes/footer.php");



include("../includes/connection_close.php");



?>



</body>



</html>



