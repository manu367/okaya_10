<?php 
require_once("../includes/config.php");
//////////////// after hitting upload button
@extract($_POST);
if($_POST['Submit']=="Upload"){
mysqli_autocommit($link1, false);
$flag = true;
if ($_FILES["file"]["error"] > 0)
{
$code=$_FILES["file"]["error"];
}
else
{
$model = $_POST['model'];
move_uploaded_file($_FILES["file"]["tmp_name"],
"../ExcelExportAPI/upload/".$now.$_FILES["file"]["name"]);
$file="../ExcelExportAPI/upload/".$now.$_FILES["file"]["name"];
chmod ($file, 0755);
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
               /*echo '<script>alert("le fichier a été chargé avec succes !");</script>';*/
                $sheet = $objPHPExcel->getSheet(0); //we specify the sheet to use
                $highestRow = $sheet->getHighestRow();//we select all the rows used in the sheet 
                $highestCol = $sheet->getHighestColumn();// we select all the columns used in the sheet
                $indexCol = PHPExcel_Cell::columnIndexFromString($highestCol); //////// count no. of column 
				$highest = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow(); //////////////// count no of rows in excel
				//Checking data
				$part_count=0;
				$error_msg = "";
				$doctype_flag = 1;
		
		
		////////////////////////if invoice /Document no generated
		if($doctype_flag==1){
			$res_po=mysqli_query($link1,"select max(ch_temp) as no from supplier_po_master where location_code='".$_SESSION['asc_code']."'");
	$row_po=mysqli_fetch_array($res_po);
	 $c_nos=$row_po[no]+1;
	$po_no=$_SESSION['asc_code']."V".$c_nos; 
		mysqli_autocommit($link1, false);
	$flag = true;
		$fromlocdet = explode("~",getAnyDetails($_POST['vendor'],"name,bill_address,ship_address ,address ,city,state,pincode,email,gst_no","id","vendor_master",$link1));
					////// PO receiver
					$tolocdet = explode("~",getAnyDetails($_SESSION['asc_code'],"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno,locationtype","location_code","location_master",$link1));
					
						$ship_add = explode("~",getAnyDetails($_POST['ship_to'],"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno,locationtype","location_code","location_master",$link1));
	
	    $po_add="INSERT INTO supplier_po_master set system_ref_no='".$po_no."', entry_date='".$today."' , location_code  ='".$_SESSION['asc_code']."' ,  ship_address2='".$ship_add[1]."', party_name ='".$_POST['vendor']."' ,bill_to ='".$_SESSION['asc_code']."', ch_temp='".$c_nos."' , bill_address ='".$tolocdet[1]."',status='7' ,po_type  ='PTV', comp_code='".$_POST['ship_to']."',user_code ='".$_POST['vendor']."'";
   $result=mysqli_query($link1,$po_add);
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
       $error_msg = "Error details1: " . mysqli_error($link1) . ".";
    }
	
                for($row =2 ;$row <= $highest;$row++){
					
                    $part = trim($sheet->getCellByColumnAndRow(0,$row)->getValue());
                    $post_dispqty = trim($sheet->getCellByColumnAndRow(1,$row)->getValue());
				if($part!='' && $post_dispqty>0 ){
					$partcheck=mysqli_query($link1,"select partcode,brand_id,product_id,l3_price from partcode_master where vendor_partcode='".$part."' and status='1'");
					$partcheck1=mysqli_num_rows($partcheck);
					$part_check=mysqli_fetch_array($partcheck);
					if($partcheck1 > 0){
						  $part_code=$part_check['partcode'];
					}else{
						$flag = false;
						 $part_code="";
						$error_msg= "partcode Not found:";
					
					}
					
				
 
					$tot_cost=$part_check['l3_price']*$post_dispqty;
					
					 $query2="insert into supplier_po_data set location_code  ='".$_SESSION['asc_code']."' , system_ref_no='".$po_no."',product_id ='".$part_check['product_id']."', brand_id ='".$part_check['brand_id']."', partcode ='".$part."', qty='".$post_dispqty."' ,req_qty='".$post_dispqty."'  ,price = '".$part_check['l3_price']."', cost='".$tot_cost."'  ,entry_date = '".$today."' ,status='7',flag='1'  ";
		  $result_po = mysqli_query($link1, $query2);
		   //// check if query is not executed
		   if (!$result_po) {
	           $flag = false;
              $error_msg = "Error details: " . mysqli_error($link1) . ".";
           }
					
       			}}////// end of for loop 
		
		
		
		
			
								
								
		
		}///////////Close Document type
	   if ($flag) {
		
		 mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "Successfully Uploaded ";
        $docid=$po_no;
        $locationname=$fromlocdet[0];
        $email=$fromlocdet[7];
        require "pdf_page/send_file_upload.php";
		}
   else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed.Please try again.".$error_msg;
	} 
    mysqli_close($link1);
	   ///// move to parent page
header("location:grn_vendor.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  exit;
            
}///// end of if condition



?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
<script src="../js/bootstrap-select.min.js"></script>
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
<script src="../js/jquery-1.10.1.min.js"></script>
<script src="../js/fileupload.js"></script>
<script language="javascript" type="text/javascript">
function makeDropdown(){
$('.selectpicker').selectpicker();
}
</script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
   include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-angle-double-right"></i>Direct Dispatch</h2><div style="display:inline-block;float:right"><a href="../templates/PO_Vendor_upload.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/> Download Excel Template</a></div><br></br> 

      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
      <?php if($_REQUEST['msg']){?><br>
      <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      <?php }?>
        <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
         
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Supplier Name<span class="red_small">*</span></label>
              <div class="col-md-4">
              <select   name="vendor" id="vendor" class="form-control required" >
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
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Bill To<span class="red_small">*</span></label>
              <div class="col-md-4">
              <select name="bill_to" id="bill_to" class="form-control required"  >
                <option value="">Please Select</option>
                <?php
                $map_wh = mysqli_query($link1,"select locationname, location_code from location_master where locationtype='WH' and location_code='".$_SESSION['asc_code']."'"); 
                while( $location = mysqli_fetch_assoc($map_wh)){
				
				?>
                <option value="<?=$location['location_code']?>" <?php if($_REQUEST['bill_from'] == $location['location_code']) { echo 'selected'; }?>><?=$location['locationname']." (".$location['location_code'].")"?></option>
                <?php } ?>
                 </select>
              </div>
            </div>
          </div>
         
		  <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Ship To<span class="red_small">*</span></label>
              <div class="col-md-4">
              <select name="ship_to" id="ship_to" class="form-control required"  >
                <option value="">Please Select</option>
                <?php
                $map_wh = mysqli_query($link1,"select locationname, location_code from location_master where locationtype!='CC' "); 
                while( $location = mysqli_fetch_assoc($map_wh)){
				
				?>
                <option value="<?=$location['location_code']?>" <?php if($_REQUEST['bill_to'] == $location['location_code']) { echo 'selected'; }?>><?=$location['locationname']." (".$location['location_code'].")"?></option>
                <?php } ?>
                 </select>
              </div>
            </div>
          </div>
           <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Product<span class="red_small">*</span></label>
              <div class="col-md-4">
              <select name="doc_type" id="doc_type" class="form-control">
                     <option value="INV">Invoice</option>
                    </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12">
              <label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
              <div class="col-md-4">
                  <div>
                    <label >
                       <span>
                        <input type="file"  name="file"  required class="form-control"   accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/ > 
                    </span>
                    </label>             
                </div>
              </div>
              <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
            </div>
          </div>
         <div class="form-group">
            <div class="col-md-12" align="center">&nbsp;&nbsp;&nbsp;
              
              <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
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