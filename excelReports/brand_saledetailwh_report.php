<?php
require_once("../includes/config.php");
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
							 ->setCategory("Summarize Sale");
// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
			->setCellValue('B1', 'Location Code')
			->setCellValue('C1', 'Name of Receipient')
            ->setCellValue('D1', 'Invoice Type')
			->setCellValue('E1', 'Invoice number')
			->setCellValue('F1', 'Invoice date')
			->setCellValue('G1', 'Type')
            ->setCellValue('H1', 'Entry Date')
            ->setCellValue('I1', 'Entry Time')
			->setCellValue('J1', 'Nature of Supply')			
			->setCellValue('K1', 'GSTIN / UIN of recipient')
            ->setCellValue('L1', 'To GST')
			->setCellValue('M1', 'Party Name')
			->setCellValue('N1', 'State of receipient of Invoice')
			->setCellValue('O1', 'State of supply of services')
			->setCellValue('P1', 'Basic Cost')
			->setCellValue('Q1', 'CGST Tax Amount')
			->setCellValue('R1', 'SGST Tax Amount')
			->setCellValue('S1', 'IGST Tax Amount')
			->setCellValue('T1', 'Total Invoice Value')
			->setCellValue('U1', 'To Location Code')
			->setCellValue('V1', 'To Location')
			// ->setCellValue('R1', 'From Address')
			// ->setCellValue('S1', 'To Address')	
			->setCellValue('W1', 'To Disp. Add')
            ->setCellValue('X1', 'From Disp. Add')		
			->setCellValue('Y1', 'Status')
			->setCellValue('Z1', 'Logged By');
            
            
			
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
cellColor('R1', 'F28A8C');cellColor('S1', 'F28A8C');cellColor('T1', 'F28A8C');cellColor('U1', 'F28A8C');cellColor('V1', 'F28A8C');cellColor('W1', 'F28A8C');
cellColor('X1', 'F28A8C');cellColor('Y1', 'F28A8C');cellColor('Z1', 'F28A8C');
////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;
// echo "select from_location,to_location,from_gst_no,to_gst_no,party_name,challan_no,sale_date,entry_date,entry_time,logged_by,bill_from,from_stateid,to_stateid,bill_to,from_addrs,disp_addrs,to_addrs,deliv_addrs,status,document_type,po_type,basic_cost,total_cost,cgst_amt,sgst_amt,igst_amt from billing_master where ".$daterange." and  from_location ='".$_SESSION['asc_code']."' and po_type='GRNR'";
// die;
/* old 
$sql_loc=mysqli_query($link1,"Select * from billing_master where ".$daterange." and  from_location ='".$_SESSION['asc_code']."'");
*/
// By manisha
$sql_loc=mysqli_query($link1, "select from_location,to_location,from_gst_no,to_gst_no,party_name,challan_no,sale_date,entry_date,entry_time,logged_by,bill_from,from_stateid,to_stateid,bill_to,from_addrs,disp_addrs,to_addrs,deliv_addrs,status,document_type,po_type,basic_cost,total_cost,cgst_amt,sgst_amt,igst_amt from billing_master where ".$daterange." and  from_location ='".$_SESSION['asc_code']."' and po_type='GRNR'");
while($row_loc = mysqli_fetch_array($sql_loc)){
	$loc_name=mysqli_fetch_array(mysqli_query($link1,"select name,id from vendor_master where id='$row_loc[to_location]'"));
	//echo $loc_name[0]."vend";
	if($loc_name[0]=='')
	{	
		$loc_name=mysqli_fetch_array(mysqli_query($link1,"select locationname,location_code from location_master where id='$row_loc[to_location]'"));
		 //echo $loc_name[0]."loc";
		 
	}
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, $row_loc['from_location'])
			->setCellValue('C'.$i, getAnyDetails($row_loc['from_location'],"locationname","location_code","location_master",$link1))
			->setCellValue('D'.$i, $row_loc['document_type'])
			->setCellValue('E'.$i, $row_loc['challan_no'])
			->setCellValue('F'.$i, dt_format2($row_loc['sale_date']))
			->setCellValue('G'.$i, $row_loc['po_type'])
			->setCellValue('H'.$i, $row_loc['entry_date'])
            ->setCellValue('I'.$i, $row_loc['entry_time'])
          	->setCellValue('J'.$i, $row_loc['total_cost'])
			->setCellValue('K'.$i, $row_loc['from_gst_no'])
            ->setCellValue('L'.$i, $row_loc['to_gst_no'])
			->setCellValue('M'.$i, $row_loc['party_name'])
			->setCellValue('N'.$i, getAnyDetails($row_loc['from_stateid'],"state","stateid","state_master",$link1))
			->setCellValue('O'.$i, getAnyDetails($row_loc['to_stateid'],"state","stateid","state_master",$link1))
			->setCellValue('P'.$i, $row_loc['basic_cost'])	
			->setCellValue('Q'.$i, $row_loc['cgst_amt'])
			->setCellValue('R'.$i, $row_loc['sgst_amt'])	
			->setCellValue('S'.$i, $row_loc['igst_amt'])
			->setCellValue('T'.$i, $row_loc['total_cost'])	
			->setCellValue('U'.$i, $row_loc['to_location'])
			// ->setCellValue('V'.$i, getAnyDetails($row_loc['to_location'],"name","id","vendor_master",$link1))
			->setCellValue('V'.$i, $loc_name[0])
			->setCellValue('W'.$i, $row_loc['to_addrs'])
            ->setCellValue('X'.$i, $row_loc['disp_addrs'])
			->setCellValue('Y'.$i, getDispatchStatus($row_loc['status']))
			->setCellValue('Z'.$i,$row_loc['logged_by']);
			// ->setCellValue('R'.$i, $row_loc['from_addrs'])
			// ->setCellValue('S'.$i, $row_loc['to_addrs'])
			$count++;			
			$i++;					
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client?s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="brand_saledetailwh_report.xlsx"');
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
