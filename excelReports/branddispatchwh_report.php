<?php
require_once("../includes/config.php");

function getdispatchstatusnew($var){
 if($var==1){
  $status="Pending";
 }else if($var==2){
  $status="Processed";
 }else if($var==3){
  $status="Dispatched";
 }else if($var==4){
  $status="Received";
 }else if($var==5){
  $status="Cancelled";
 }else if($var==6){
  $status="Partial Processed";
 }else if($var==7){
  $status="Pending For Admin Approval";
 }else if($var==8){
  $status="Pending For Finance Approval";
 }else if($var==9){
  $status="Pending For Gate Entry";
 }else if($var==10){
  $status="Partial Received";
 }else if($var==11){
  $status="Missing";
 }
	else if($var==12){

		$status="CN Generated";

	}
	else if($var==13){

		$status="Pending for Receive";

	}
	
	else{

		$status="-";

	}

	return $status;

}
////// filters value/////
//////// get date /////////////////////////
if ($_REQUEST['daterange'] != ""){
	$date_range = explode(" - ",$_REQUEST['daterange']);
	$daterange = "sale_date  >= '".$date_range[0]."' and sale_date  <= '".$date_range[1]."'";
}
else{
	$daterange = "1";
}
 //////End filters value/////

/** Include PHPExcel */
require_once("../ExcelExportAPI/Classes/PHPExcel.php");
//require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
$objPHPExcel->getProperties()->setCreator("Candour Software")
							 ->setLastModifiedBy("Candour Software")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Brand Sale");
// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'ASC Code')
			->setCellValue('C1', 'Name of Receipient')			
			->setCellValue('D1', 'To Location Code')
			->setCellValue('E1', 'To Location')			
			->setCellValue('F1', 'Invoice number')
			->setCellValue('G1', 'Invoice date')			
			->setCellValue('H1', 'Partcode')
			->setCellValue('I1', 'Description of goods sold')
			->setCellValue('J1', 'HSN')				
			->setCellValue('K1', 'UQC of goods sold')
			->setCellValue('L1', 'Quantity of goods sold')
			->setCellValue('M1', 'Rate')			
			->setCellValue('N1', 'CGST Rate')
			->setCellValue('O1', 'CGST Tax Amount')
			->setCellValue('P1', 'SGST Rate')
			->setCellValue('Q1', 'SGST Tax Amount')
			->setCellValue('R1', 'IGST Rate')
			->setCellValue('S1', 'IGST Tax Amount')
			->setCellValue('T1', 'Total Invoice Value')
			->setCellValue('U1', 'Status');
////////////////
///////////////////////
cellColor('B1', 'F28A8C');
cellColor('A1', 'F28A8C');
cellColor('C1', 'F28A8C');
cellColor('D1', 'F28A8C');
cellColor('E1', 'F28A8C');
cellColor('F1', 'F28A8C');
cellColor('G1', 'F28A8C');
cellColor('H1', 'F28A8C');
cellColor('I1', 'F28A8C');
cellColor('J1', 'F28A8C');
cellColor('K1', 'F28A8C');
cellColor('L1', 'F28A8C');
cellColor('M1', 'F28A8C');
cellColor('N1', 'F28A8C');
cellColor('O1', 'F28A8C');
cellColor('P1', 'F28A8C');
cellColor('Q1', 'F28A8C');
cellColor('R1', 'F28A8C');cellColor('S1', 'F28A8C');cellColor('T1', 'F28A8C');cellColor('U1', 'F28A8C');
////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;
/* old report 
// $sql_loc=mysqli_query($link1,"Select * from billing_master where ".$daterange." and  from_location = '".$_SESSION['asc_code']."' and ".$to_loc_id." ");
// while($row_loc = mysqli_fetch_array($sql_loc)){
// $sql = mysqli_query($link1,"Select * from billing_product_items where challan_no= '$row_loc[challan_no]'  and ".$product_id." and ".$brand_id." and ".$model_id."  and from_location = '".$_SESSION['asc_code']."' and ".$to_loc_id." ");
*/
//changed by manisha.
$sql = mysqli_query($link1,"Select  from_location,partcode,challan_no,sale_date,sale_date,price,igst_per,igst_amt,sgst_per,sgst_amt,cgst_per,cgst_amt,hsn_code,part_name,uom,qty,to_location,okqty,value,request_no,basic_amt,item_total,stock_type,product_id,brand_id,type  from billing_product_items where  request_no = 'Dispatch to Vendor' and  from_location = '".$_SESSION['asc_code']."' and ".$daterange." ");
while($row_loc = mysqli_fetch_array($sql )){
	
	$sql_bl_mstr = mysqli_fetch_assoc(mysqli_query($link1,"select total_cost,status from billing_master where challan_no='$row_loc[challan_no]' and po_type='GRNR'"));	
	$ven_name=mysqli_fetch_array(mysqli_query($link1,"select name,id from vendor_master where id='$row_loc[to_location]'"));
	
	if($ven_name[0]=='')
	{	
		$loc_name=mysqli_fetch_array(mysqli_query($link1,"select locationname,location_code from location_master where location_code='$row_loc[to_location]'"));
		$to_loc = $loc_name[0];		 
	}
   else {
                 $to_loc = $ven_name[0];

         }
//die;
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)			
            ->setCellValue('B'.$i, $row_loc['from_location']) 
			->setCellValue('C'.$i, getAnyDetails($row_loc['from_location'],"locationname","location_code","location_master",$link1))
			->setCellValue('D'.$i, $row_loc['to_location'])
			->setCellValue('E'.$i, $to_loc)
			->setCellValue('F'.$i, $row_loc['challan_no'])
			->setCellValue('G'.$i, dt_format2($row_loc['sale_date']))
			->setCellValue('H'.$i, $row_loc['partcode'])
			->setCellValue('I'.$i, $row_loc['part_name'])
			->setCellValue('J'.$i, $row_loc['hsn_code'])
			->setCellValue('K'.$i, $row_loc['uom'])
            ->setCellValue('L'.$i, $row_loc['qty'])
			->setCellValue('M'.$i, $row_loc['price'])
			->setCellValue('N'.$i, $row_loc['cgst_per'])
			->setCellValue('O'.$i, $row_loc['cgst_amt'])
			->setCellValue('P'.$i, $row_loc['sgst_per'])
			->setCellValue('Q'.$i, $row_loc['sgst_amt'])
			->setCellValue('R'.$i, $row_loc['igst_per'])
			->setCellValue('S'.$i, $row_loc['igst_amt'])                        
			->setCellValue('T'.$i, $row_loc['item_total'])
			->setCellValue('U'.$i, getdispatchstatusnew($sql_bl_mstr[status]));
			$i++;
			$count++;
			}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client?s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="brand_partwise_salewh_report.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
