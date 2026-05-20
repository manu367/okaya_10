<?php
require_once("../includes/config.php");

//////End filters value/////
if ($_REQUEST['daterange'] != ""){
    $seldate = explode(" - ",$_REQUEST['daterange']);
    $fromdate = $seldate[0];
    $todate = $seldate[1];
    $daterange=" (DATE(doc_date) >= '".$fromdate."' and DATE(doc_date) <='".$todate."') ";
 }else{
	$seldate = $today;
	$fromdate = $today;
	$todate = $today;
	$daterange=" (DATE(doc_date) >= '".$fromdate."' and DATE(doc_date) <='".$todate."') ";
}
## selected  status

if($_REQUEST['status'] != ""){
	$status = "status = '".$_REQUEST['status']."'";
}else{
	$status = "1";
}
## selected  asp name

if($_REQUEST['assign_to'] != ""){
    $aspname="assign_to='".$_REQUEST['assign_to'] ."'";
    //$aspname = "1";
}else{
    $aspname = "1";
}

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
							 ->setCategory("Advance Docket");


// Add some data ///EXCEL HEADING.
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'Assign From')
            ->setCellValue('C1', 'Assign To')
         	->setCellValue('D1', 'Doc. No.')
			->setCellValue('E1', 'Doc. Date')
			->setCellValue('F1', 'Entry By')
			->setCellValue('G1', 'Status')
			->setCellValue('H1', 'Docket No.')
			->setCellValue('I1', 'Docket Company')
			->setCellValue('J1', 'Mode of Transport')
			->setCellValue('K1', 'Response Msg')
			->setCellValue('L1', 'Use Status');
		
////////////////
///////////////////////
cellColor('A1:L1', 'F28A8C');


////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8



$i=2;
$cnt = 1;
$sql=mysqli_query($link1,"SELECT * FROM `advance_docket_assign` WHERE ".$aspname." AND ".$status." AND ".$daterange." ORDER BY doc_no");
while($row = mysqli_fetch_array($sql)){
	$sql2=mysqli_query($link1,"SELECT * FROM `advance_docket_upload` WHERE doc_no='".$row['doc_no']."' ORDER BY doc_no") or die(mysqli_error($link1));
	
	while($row1 = mysqli_fetch_array($sql2)){	
	if($row1['status']==1){
	$status_diaplay= "Used";
	}else{
	$status_diaplay="un-Used";
	}
		
		$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $cnt)
			->setCellValue('B'.$i, $row['assign_from'])
            ->setCellValue('C'.$i, $row['assign_to'])
            ->setCellValue('D'.$i,$row['doc_no'])
            ->setCellValue('E'.$i,$row['doc_date'])
            ->setCellValue('F'.$i,$row['assign_by'])
            ->setCellValue('G'.$i,$row['status'])            
            ->setCellValue('H'.$i,$row1['docket_no'])
            ->setCellValue('I'.$i,$row1['docket_company'])
            ->setCellValue('J'.$i,$row1['mode_of_transport'])
            ->setCellValue('K'.$i,$row1['response_msg'])
			 ->setCellValue('L'.$i,$status_diaplay);
			$i++;					
			$cnt++;
	}
	mysqli_free_result($sql2);
}
mysqli_free_result($sql);
///// apply border on export sheet
$styleArray = array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_MEDIUM
    )
  )
);
$cell = 'L'.($i-1);
$range = 'A1:'.$cell;
$objPHPExcel->getActiveSheet()->getStyle($range)->applyFromArray($styleArray);
unset($styleArray);
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client�s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Advance_Docket_Details_Report.xlsx"');
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
