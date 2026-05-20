<?php
require_once("../includes/config.php");
@extract($_POST);
if ($_POST['add']=='ADD'){
	//////  if we want to Add gate entry
   	if(is_array($_POST['total_shhipment_qty'])){
		$shipinv=$_POST['shhipment_inv'];
		$shipdate=$_POST['shhipment_date'];
		$item=$_POST['total_shhipment_qty'];
        $uom=$_POST['uom'];
       	$len2=count($_POST['total_shhipment_qty']);
       	if($len2>0){
	    	//////////// intialize parameter ///////////////////////////////////////////
			mysqli_autocommit($link1, false);
			$flag = true;
			$error_msg = "";
			$pono = base64_decode($_POST['po_no']);
			//// Make System generated request no for  gate entry.//////
			$max_sno="select max(sno) as no from gate_entry_detail where location_code='".$location_code."'";
			$rs3=mysqli_query($link1,$max_sno);
			$row3=mysqli_fetch_array($rs3);
			$req_no=$row3['no']+1;
			$pad=str_pad($req_no,3,"0",STR_PAD_LEFT);
			$request_no="GE".substr($location_code,3)."/".substr(date("Y"),2,2)."/".$pad;
			/////////////////////////// insert into master table//////////////////////////////////////////////////////////
			$master_query= mysqli_query($link1,"insert into gate_entry_detail set location_code='".$location_code."', inv_nos='".$_POST['inv_nos']."',inv_nos_his='".$_POST['inv_nos']."',vehicle_no='".$_POST['vehicle_no']."', date_of_loading='".$_POST['start_date']."', from_party_name='".$_POST['supplier']."',contact_person='".$_POST['contact_person']."',pilates_w='".$_POST['pilates_w']."',mas_cartoon_w='".$_POST['mas_cartoon_w']."',entry_date='".$today."',entry_time='".$time."',request_no='".$request_no."',po_no='".$pono."',box_no='".$_POST['box_no']."',comp_code='".$_SESSION['asc_code']."',logged_by='".$_SESSION['userid']."' , entry_status = '13' ");
			///////// get last insert id////////////////////////////////////////	
			$ins_id=mysqli_insert_id($link1);
			////// check query is executed or not /////////////////////
			if (!$master_query) {
	     		$flag = false;
            	$error_msg = "Error details1: " . mysqli_error($link1) . ".";
    		}
			//////////////// inset data in data table//////////////////////////////////////////////////////
          	for($i=0;$i<$len2;$i++){
				$gate_qty=$item[$i]; 
             	$gete_uom=$uom[$i];
				$gete_inv=$shipinv[$i];
				$gete_date=$shipdate[$i];
             	if($gate_qty!=""){
		        	$req_ins2="insert into gate_entry_oth set gen_id='".$ins_id."' , ship_qty='".$gate_qty."',uom='".$gete_uom."',ship_inv='".$gete_inv."',ship_date='".$gete_date."'";
		            $req_res2=mysqli_query($link1,$req_ins2);
					/////////////// check if query is executed or not/////////////////////
					if (!$req_res2) {
	    				$flag = false;
         				$error_msg =  "Error details2: " . mysqli_error($link1) . ".";
					}
				}
			}// close for loop
		  	///////// update flag  and status in  supplier master table
 	 		$upd_flag = mysqli_query($link1,"update supplier_po_master set status= '1' ,gate_entry_flag='Y' where system_ref_no='".$pono."' " );
			/////////////// check if query is executed or not/////////////////////
			if (!$upd_flag) {
	    		$flag = false;
         		$error_msg =  "Error details3: " . mysqli_error($link1) . ".";
			}
		 	///////// update status in  supplier data table	
    		$upd_st = mysqli_query($link1,"update supplier_po_data set status= '1',update_date='".$today."' where system_ref_no='".$pono."' " );
			/////////////// check if query is executed or not/////////////////////
			if (!$upd_st) {
	    		$flag = false;
         		$error_msg =  "Error details4: " . mysqli_error($link1) . ".";
			}			
		}/// if (length condition close)
	}/// if (isarray condition close)
	////// insert in activity table////
    $flag = dailyActivity($_SESSION['userid'], $request_no, "GATE ENTRY", "ADD", $ip, $link1, $flag);
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "Gate Entry  is successfully done with ref. no.".$pono;
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again.".$error_msg;
	} 
    mysqli_close($link1);
	///// move to parent page
 	header("location:gate_entry_details.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
	exit;
}
?>

<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/jquery.js"></script>
 <script src="../js/bootstrap.min.js"></script>
 <script type="text/javascript" src="../js/moment.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 
 <script language="javascript" type="text/javascript">
 $(document).ready(function(){
        $("#frm2").validate();
  });
   $(document).ready(function(){
        $("#frm1").validate();
  });
 $(document).ready(function () {
	$('#start_date').datepicker({
		format: "yyyy-mm-dd",
        todayHighlight: true,
		autoclose: true
	});
});
  
$(document).ready(function(){
     $("#add_row").click(function(){
		var numi = document.getElementById('rowno');
		var preno=document.getElementById('rowno').value;
		var num = (document.getElementById("rowno").value -1)+2;
		numi.value = num;
     var r='<tr id="addr'+num+'"><td><input type="text" class="form-control" name="shhipment_inv['+num+']" id="shhipment_inv['+num+']"  autocomplete="off" required ></td><td><input type="text" class="form-control" name="shhipment_date['+num+']" id="shhipment_date['+num+']"  autocomplete="off" value="<?=$today?>" required ></td><td><input type="text" class="form-control digits" name="total_shhipment_qty['+num+']" id="total_shhipment_qty['+num+']"  autocomplete="off" required  onKeyPress="return onlyNumbers(this.value);"></td><td><select name="uom['+num+']" id="uom['+num+']"  style="width:200px" class="form-control"><option value="">Select Measuring Unit</option><option value="KGS">KGS</option><option value="PCS">PCS</option></select></td></tr>';
      $('#itemsTable1').append(r);
	
  });
});

///////////////////////////
  </script>
  <script type="text/javascript" src="../js/jquery.validate.js"></script>
  <!-- Include Date Picker -->
  <link rel="stylesheet" href="../css/datepicker.css">
  <script src="../js/bootstrap-datepicker.js"></script>
 <style type="text/css">
 .custom_label {
	 text-align:left;
	 vertical-align:middle
 }
 </style>
 <title><?=siteTitle?></title>
 </head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
include("../includes/leftnavemp2.php");
    ?>
     <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-share-square-o"></i> Add Gate Entry </h2><br/>
      <div class="form-group" id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
         <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Select PO Number:</label>	  
			<div class="col-md-6" >
				<select   name="po" id="po" class="form-control required" onChange="document.frm1.submit();" required>
				<option value=''>--Please Select--</option>
				<?php
               $po_query="select system_ref_no from supplier_po_master where status in ('9','10') ";
			        $check1=mysqli_query($link1,$po_query);
                while($br = mysqli_fetch_array($check1)){?>
                <option value="<?=$br['system_ref_no']?>" <?php if($_REQUEST['po'] == $br['system_ref_no']) { echo 'selected'; }?>><?=$br['system_ref_no']?></option>
                <?php } ?>
	</select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5">
              <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn btn<?=$btncolor?>" value="GO"  title="Go!"> 
              </div>
          </div>
	    </div>		
         </form>
		  <?php if ($_REQUEST['Submit']){	
		  ////////////////////////// fetch info using po_no from master table/////////////////////////////////////////////////////////
		$res_po=mysqli_query($link1,"select * from supplier_po_master where system_ref_no='".$_REQUEST['po']."'");
		$row_po=mysqli_fetch_array($res_po);	  		  
	?>
         <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
		 <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Location Name</label>	  
			<div class="col-md-6" >
				<select   name="location_code" id="location_code" class="required form-control" >
				<option value=''>--Please Select--</option>
				<?php
                $map_wh = mysqli_query($link1,"select locationname, location_code from location_master where location_code='".$row_po['location_code']."'"); 
                while($location = mysqli_fetch_assoc($map_wh)){
				
				?>
                <option value="<?=$location['location_code']?>" <?php if($row_po['location_code'] == $location['location_code']) { echo 'selected'; }?>><?=$location['locationname']." (".$location['location_code'].")"?></option>
                <?php } ?>
	</select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Supplier Name</label>	  
			<div class="col-md-5">
                <select name="supplier" id="supplier" class="required form-control">
                    <option value=''>--Please Select--</option>
                    <?php
                    $vendor_query="select name,id from vendor_master where id='".$row_po['party_name']."'";
                    $check1=mysqli_query($link1,$vendor_query);
                    while($br = mysqli_fetch_array($check1)){?>
                    <option value="<?=$br['id']?>" <?php if($row_po['party_name'] == $br['id']) { echo 'selected'; }?>><?=$br['name']." | ".$br['id']?></option>
                    <?php } ?>
                </select>
              </div>
          </div>
	    </div>		
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">No. of Invoices<span class="red_small">*</span></label>	  
			<div class="col-md-6" >
				 <input type="text"  id="inv_nos" name="inv_nos"  value=""   class="form-control required"  required>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Master Carton Weight</label>	  
			<div class="col-md-5">
                  <input type="text"  id="mas_cartoon_w" name="mas_cartoon_w"  value=""   class="form-control" >
              </div>
          </div>
	    </div>
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Vehicle Number<span class="red_small">*</span></label>	  
			<div class="col-md-6" >
				 <input type="text"  id="vehicle_no" name="vehicle_no"  value=""   class="form-control required"  required>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Pallets Weight</label>	  
			<div class="col-md-5">
                  <input type="text"  id="pilates_w" name="pilates_w"  value=""   class="form-control" >
              </div>
          </div>
	    </div>
		 <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Date of Unloading</label>	  
			<div class="col-md-6 input-append date">
				<div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="start_date"  id="start_date" style="width:150px;" required value="<?=$today?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>
                  </div>
              </div>
          </div>
		 <div class="col-md-6"><label class="col-md-5 control-label">Contact Person</label>	  
			<div class="col-md-5">
              <input type="text"  id="contact_person" name="contact_person"   class="form-control" >   
              </div>
          </div>
	    </div>
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">No. of Boxes</label>	  
			<div class="col-md-6 input-append date">
			 <input type="text"  id="box_no" name="box_no"  value=""   class="form-control" >   
              </div>
          </div>
		 <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5">
              </div>
          </div>
	    </div>
		<div class="form-group">
         <div class="col-md-9"><label class="col-md-9 control-label">Shipment Details</label>	 		   
              </div>
	    </div>
          <div class="form-group">
           <table width="60%" id="itemsTable1" class="table table-bordered table-hover" style="width:600px;" align="center">
		    <thead>
              <tr class="<?=$tableheadcolor?>">
			    	<th width="25%" style="font-size:13px;">Invoice No.</th>
				<th width="25%" style="font-size:13px;">Invoice Date</th>
               	<th width="25%" style="font-size:13px;">Shipment Qty</th>
				<th width="25%" style="font-size:13px" align="center">Measuring Unit</th>         
              </tr>
			    </thead>
            <tbody>
              <tr id='addr0'>
			  <td><input type="text" class="form-control" name="shhipment_inv[0]" id="shhipment_inv[0]"  autocomplete="off" required></td>
			  <td><input type="text" class="form-control" name="shhipment_date[0]" id="shhipment_date[0]"  autocomplete="off"  value="<?=$today?>" required></td>
				<td><input type="text" class="form-control digits" name="total_shhipment_qty[0]" id="total_shhipment_qty[0]"  autocomplete="off" required></td>
                <td><select name="uom[0]" id="uom[0]" style="width:200px" class="form-control"><option value="">Select Measuring Unit</option><option value="KGS">KGS</option><option value="PCS">PCS</option></select>                                  
                </td>
              </tr>
            </tbody>
            <tfoot id='productfooter' style="z-index:-9999;">
              <tr class="0">
                <td colspan="7" style="font-size:13px;"><a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a><input type="hidden" name="rowno" id="rowno" value="0"/></td>
              </tr>
            </tfoot>
          </table>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">     
              <input type="submit" class="btn btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New Gate Entry">
			  <input type="hidden" name="po_no" id="po_no" value="<?=base64_encode($_REQUEST['po'])?>"/>
              <input title="Back" type="button" class="btn btn<?=$btncolor?>" value="Back" onClick="window.location.href='gate_entry_details.php?<?=$pagenav?>'">
            </div>
          </div>
         </form>  <?php 
		 }	  		  
	?>
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