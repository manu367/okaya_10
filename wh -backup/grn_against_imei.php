<?php
require_once("../includes/config.php");
/////////////////////////////// get address of vendor / billfrom/bill to /////////////////////////////////////////////////////////////////////////////////
$vendor_addrs  = getAnyDetails($_REQUEST['vendor'],"address","id","vendor_master",$link1);
$from  = getAnyDetails($_REQUEST['bill_from'],"locationaddress","location_code","location_master",$link1);
$to  = getAnyDetails($_REQUEST['bill_to'],"locationaddress","location_code","location_master",$link1);
/////get status//
@extract($_POST);
if($_POST['Submit']=="Upload"){

if ($_FILES["file"]["error"] > 0)
{
$code=$_FILES["file"]["error"];
}
else
{
////// fetch post values  ////////////////////////////////////////////////////
$type = $_POST['type']; ///////////// complete box or complete unit 
$billto  = $_POST['bill_to']; /////////// location code//////////////////////
move_uploaded_file($_FILES["file"]["tmp_name"],
"../ExcelExportAPI/upload/".$today.$_FILES["file"]["name"]);
$file="../ExcelExportAPI/upload/".$today.$_FILES["file"]["name"];
//chmod ($file, 0755);
}

$filename=$file;

////////////////////////////////////////////////// code to import file/////////////////////////////////////////////////////////////
error_reporting(E_ALL ^ E_NOTICE);
 $path = '../ExcelExportAPI/Classes/';
        set_include_path(get_include_path() . PATH_SEPARATOR . $path);//we specify the path" using linux"
        function __autoload($classe)
        {
            $var = str_replace
            (
                '_', 
                DIRECTORY_SEPARATOR,
                $classe
            ) . '.php' ;
            require_once($var);
        }   

 $indentityType = PHPExcel_IOFactory::identify($filename);
                $object = PHPExcel_IOFactory::createReader($indentityType);
                $object->setReadDataOnly(true);
                $objPHPExcel = $object->load($filename);
               /*echo '<script>alert("le fichier a t charg avec succes !");</script>';*/
                $sheet = $objPHPExcel->getSheet(0); //we specify the sheet to use
                $highestRow = $sheet->getHighestRow();//we select all the rows used in the sheet 
                $highestCol = $sheet->getHighestColumn();// we select all the columns used in the sheet
                $indexCol = PHPExcel_Cell::columnIndexFromString($highestCol); //////// count no. of column 
				$highest = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow(); //////////////// count no of rows in excel
				
		///////////////////////// initialize parameter	
			mysqli_autocommit($link1, false);
			$flag = true;
		///////////////////////////////////////// insert into grn master table
		//// pick max count of grn
		echo  "SELECT grn_counter from invoice_counter where location_code='".$billto."'";
		$res_grncount = mysqli_query($link1,"SELECT grn_counter from invoice_counter where location_code='".$billto."'");
		$row_grncount = mysqli_fetch_assoc($res_grncount);
	///// make grn sequence
		$nextgrnno = $row_grncount['grn_counter'] + 1;
		$grnno = "GRN".$billto.str_pad($nextgrnno,4,0,STR_PAD_LEFT);
		//// first update the job count
		$upd = mysqli_query($link1,"UPDATE invoice_counter set grn_counter='".$nextgrnno."' where location_code='".$billto."'");
		//// check if query is not executed
		if (!$upd) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}
		/////////////////////////////// insert data into grn master  table///////////////////////////////////////////////
 		$grn_master="insert into grn_master set location_code ='".$billto."', receive_date='".$today."' ,receive_time='".$time."', entry_date_time='".$datetime."' , status='4' , grn_no='".$grnno."' , remark='Received by Uploader',comp_code='".$_SESSION['asc_code']."',update_by='".$_SESSION['userid']."',vendor='".$vendor."',ip_address='".$_SERVER['REMOTE_ADDR']."'";
		$result5=mysqli_query($link1,$grn_master);
		//// check if query is not executed
		if (!$result5) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}
	  
          $flag_imei=0;      //importing files to the database
                for($row =2 ;$row <= $highest;$row++)
                {
					$modelid = $sheet->getCellByColumnAndRow(0,$row)->getValue();
                    $imei1 = $sheet->getCellByColumnAndRow(1,$row)->getValue();
                    $imei2 = $sheet->getCellByColumnAndRow(2,$row)->getValue();
					 $price = $sheet->getCellByColumnAndRow(3,$row)->getValue();
					
					$partcode = getAnydetails("$modelid","partcode","model_id","partcode_master",$link1);
					$productid = getAnydetails("$modelid","product_id","model_id","model_master",$link1);
					$brandid = getAnydetails("$modelid","brand_id","model_id","model_master",$link1);
			
					
			//////// first check whether enter model id in excel  is correct or not ////////////////////////
				$getinfo	 = mysqli_query($link1, "select model_id,partcode from partcode_master where model_id = '".$modelid."'  and  part_category = '".$type."' ");
				
				$part_code=mysqli_fetch_assoc($getinfo);
			//////////////////////// then check ime1  and imei2 already exist in table 
					
                   //inserting query into data base
         $sql = "INSERT INTO imei_details(imei1,imei2,partcode , model_id,location_code,status , 	entry_date)VALUES('".$imei1."','".$imei2."','".$part_code['partcode']."','".$modelid."','".$billto."','1','".$today."')";
			$result =	mysqli_query($link1,$sql);
			 //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               echo "Error details: " . mysqli_error($link1) . ".";
          				 }		
						 
	/////////////////////// check whether partcode and location code exist in client inventory or not //////////////////////
		$check = mysqli_query($link1 , "select location_code , partcode from client_inventory where location_code = '".$billto."'  and partcode = '".$part_code['partcode']."' ");
		if(mysqli_num_rows($check) >=1)
			{ 
		////////////// update  okqty in client inventory table //////////////////////////////////////////////////////////	 
	   $result2   = mysqli_query($link1 , " update  client_inventory set okqty = okqty+1 where partcode = '".$part_code['partcode']."' and  location_code = '".$billto."' "	);	   
		}
		else {
		////////////// insert  okqty in client inventory table //////////////////////////////////////////////////////////	 
	  $result2   = mysqli_query($link1 , " insert into  client_inventory set okqty = '1' , partcode = '".$part_code['partcode']."' ,  location_code = '".$billto."',  	updatedate = '".$datetime."' ");	   
		
		}
			 //// check if query is not executed
		   if (!$result2) {
	           $flag = false;
               echo "Error details: " . mysqli_error($link1) . ".";
          				 }	
						 
	////////////////////// insert into grn data table /////////////////////////////////////////////				 
			$result3   = mysqli_query($link1 , " insert into  grn_data set grn_no = '".$grnno."' , product_id = '".$productid."' , model_id = '".$modelid."' , brand_id= '".$brandid."',partcode='".$part_code['partcode']."' ,okqty = okqty+1 , type = 'GRN  IMEI', imei1='".$imei1."',imei2='".$imei2."',price='".$price."' ");	  
			
			
			 $flag=stockLedger($grnno,$today,$part_code['partcode'],$billto,$vendor,"IN","OK","Stock In","Receive Against GRN",$okqty,$price,$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag); 
			 //// check if query is not executed
		   if (!$result3) {
	           $flag = false;
               echo "Error details: " . mysqli_error($link1) . ".";
          				 }				 
						 	 
							 $flag_imei=1;						    	   
      	////// end of if condition	 
						    
}///// end of for loop

 if ($flag) {
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "Successfully Uploaded ";
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Imei already exist or type is not correct";
	} 
    mysqli_close($link1);
	   ///// move to parent page
  header("location:grn_against_imei.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
   exit;
}
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
 
 <script language="javascript" type="text/javascript">

  </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script src="../js/frmvalidate.js"></script>
 <style type="text/css">
 .custom_label {
	 text-align:left;
	 vertical-align:middle
 }
 </style>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
include("../includes/leftnavemp2.php");
    ?>
     <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-arrows-alt"></i> GRN Against IMEI</h2>
	 <div style="display:inline-block;float:right"><a href="../templates/GRN_IMEI_IMPORT.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/></a></div>	<br></br>
      <div class="form-group" id="page-wrap" style="margin-left:10px;">
	  <?php if($_REQUEST['msg']){?><br>
      <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      <?php }?>
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
         <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Supplier Name</label>	  
			<div class="col-md-6" >
				<select   name="vendor" id="vendor" class="form-control" onChange="document.frm1.submit();">
				<option value=''>--Please Select--</option>
				<?php
               $vendor_query="select name,id from vendor_master where status='1'";
			        $check1=mysqli_query($link1,$vendor_query);
                while($br = mysqli_fetch_array($check1)){?>
                <option value="<?=$br['id']?>" <?php if($_REQUEST['vendor'] == $br['id']) { echo 'selected'; }?>><?=$br['name']." | ".$br['id']?></option>
                <?php } ?>
	</select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Supplier Address</label>	  
			<div class="col-md-5" id="venaddress">
                 <input id="ven_addrs" name="ven_addrs" value="<?=$vendor_addrs;?>" type="text">
              </div>
          </div>
	    </div>
          <div class="form-group">
         <div class="col-md-6">
			<label class="col-md-5 control-label">Bill To</label>	  
			<div class="col-md-6" >
				 <select name="bill_from" id="bill_from" class="form-control required"  onChange="document.frm1.submit();">
                <option value="">Please Select</option>
                <?php
                $map_wh = mysqli_query($link1,"select location_code , locationname  from location_master where locationtype ='WH' "); 
                while($location = mysqli_fetch_assoc($map_wh)){				
				?>
                <option value="<?=$location['location_code']?>" <?php if($_REQUEST['bill_from'] == $location['location_code']) { echo 'selected'; }?>><?=$location['locationname']." (".$location['location_code'].")"?></option>
                <?php } ?>
                 </select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Billing Address</label>	  
			<div class="col-md-5">
                 <input id="from_addrs" name="from_addrs" value="<?=$from;?>" type="text">
              </div>
          </div>
	    </div>
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Ship To</label>	  
			<div class="col-md-6" >
				 <select name="bill_to" id="bill_to" class="form-control required" onChange="document.frm1.submit();" >
                <option value="">Please Select</option>
                <?php
                $map_wh = mysqli_query($link1,"select location_code, locationname  from location_master where locationtype = 'WH' "); 
                while($location = mysqli_fetch_assoc($map_wh)){			
				?>
                <option value="<?=$location['location_code']?>" <?php if($_REQUEST['bill_to'] == $location['location_code']) { echo 'selected'; }?>><?=$location['locationname']." (".$location['location_code'].")"?></option>
                <?php } ?>
                 </select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Shipment Address</label>	  
			<div class="col-md-5" >
                  <input id="to_addrs" name="to_addrs" value="<?=$to;?>" type="text">
              </div>
          </div>
	    </div>
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Type</label>	  
			<div class="col-md-6" >
				 <select name="type" id="type" class="form-control required" onChange="document.frm1.submit();" >
                <option value="">Please Select</option>
			   <option value="BOX" <?php if($_REQUEST['type'] == "BOX") { echo 'selected'; }?>>Box</option>
			<option value="UNIT" <?php if($_REQUEST['type'] == "UNIT"){ echo 'selected'; }?>>Unit</option>
                 </select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Attach File</label>	  
			<div class="col-md-7" >
              <div>
                    <label >  
                       <span>
                        <input type="file"  name="file"  required class="form-control"   accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/ > 						          
                    </span>
                    </label>
                </div>
              </div>            
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
              &nbsp;&nbsp;&nbsp;
              
            </div>
          </div> 
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