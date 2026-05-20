<?php
require_once("../includes/config.php");

//$access_brand = getAccessBrand($_SESSION['userid'],$link1);
//
///////// get Access state////////////////////////
//$arrstate = getAccessState($_SESSION['userid'],$link1);
//
////// extract all encoded variables
//$tostate = base64_decode($_REQUEST['to_state']);
//$toloc = base64_decode($_REQUEST['to_loc']);
//$brandid = base64_decode($_REQUEST['brand']);
//$doctypid = base64_decode($_REQUEST['doc_typ']);
//
//////// filters value/////
////////// get date /////////////////////////
//if ($_REQUEST['daterange'] != ""){
//    $date_range = explode(" - ",$_REQUEST['daterange']);
//    $daterange = " and (sale_date  >= '".$date_range[0]."' and sale_date  <= '".$date_range[1]."')";
//}
//else{
//    $daterange = "and 1";
//}
//## selected  to_loc
//if($toloc != ""){
//    $to_location = "and from_location in ('".$toloc."')";
//}else if($tostate != ""){
//    $to_location = "and from_stateid in ('".$tostate."')";
//}else{
//    $to_location = "and from_stateid in (".$arrstate.") ";
//}
//## selected  brand
//if($brandid != ""){
//    $brand_id = "and brand_id  in ('".$brandid."' )";
//}else {
//    $brand_id="";
//}
//## selected  doc_typ
//if($doctypid == "P2C"){
//    $doc_type_id = " po_type='P2C' ";
//}else if($doctypid == "Sale Return"){
//    $doc_type_id = " po_type='Sale Return' ";
//}else{
//    $doc_type_id = " po_type in ('P2C','Sale Return') ";
//}

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
    ->setCategory("Part wise Sales Return Report");

// Add some data
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'S.No.')
    ->setCellValue('B1', 'Courier')
    ->setCellValue('C1', 'Docket No')
    ->setCellValue('D1', 'Docket Date')

    ->setCellValue('E1', 'From Location Code')
    ->setCellValue('F1', 'From Location Name')
    ->setCellValue('G1', 'City of Form Location')
    ->setCellValue('H1', 'State of From Location')
    ->setCellValue('I1', 'GSTIN / UIN of From Location')

    ->setCellValue('J1', 'To Location Code')
    ->setCellValue('K1', 'To Location Name')
    ->setCellValue('L1', 'City of To Location')
    ->setCellValue('M1', 'State of To Location')
    ->setCellValue('N1', 'GSTIN / UIN of To Location')

    ->setCellValue('O1', 'Invoice Type')
    ->setCellValue('P1', 'Type')

    ->setCellValue('Q1', 'Invoice number')
    ->setCellValue('R1', 'Invoice date')

    ->setCellValue('S1', 'Job No')
    ->setCellValue('T1', 'Product Name')
    ->setCellValue('U1', 'Model')

    ->setCellValue('V1', 'Partcode')
    ->setCellValue('W1', 'Part Description')
    ->setCellValue('X1', 'UQC of goods sold')
    ->setCellValue('Y1', 'Quantity of goods sold')
    ->setCellValue('Z1', 'HSN')

    ->setCellValue('AA1', 'Rate')
    ->setCellValue('AB1', 'IGST Rate')
    ->setCellValue('AC1', 'IGST Tax Amount')
    ->setCellValue('AD1', 'SGST Rate')
    ->setCellValue('AE1', 'SGST Tax Amount')
    ->setCellValue('AF1', 'CGST Rate')
    ->setCellValue('AG1', 'CGST Tax Amount')

    ->setCellValue('AH1', 'Total Value')
    ->setCellValue('AI1', 'Total with GST')

    ->setCellValue('AJ1', 'Status')
    ->setCellValue('AK1', 'Asc Receive Date')
    ->setCellValue('AL1', 'Brand Name')
    ->setCellValue('AM1', 'Received Qty')
    ->setCellValue('AN1', 'Broken Qty')
    ->setCellValue('AO1', 'Missing Qty')

    ->setCellValue('AP1', 'Ship From Code')
    ->setCellValue('AQ1', 'Ship From Name')
    ->setCellValue('AR1', 'Ship From State')
    ->setCellValue('AS1', 'Ship From Address')
    ->setCellValue('AT1', 'Ship From GST')

    ->setCellValue('AU1', 'Grand Total')
    ->setCellValue('AV1', 'Send Total Qty');

///////////////////////
cellColor('A1:AV1', 'F28A8C');
///////////////////////////////////////////////

// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;

$sql_loc=mysqli_query($link1,"Select * from billing_master where ".$doc_type_id." ".$daterange." ".$to_location." ");
while($row_loc = mysqli_fetch_array($sql_loc)){
    $sql = mysqli_query($link1,"Select * from billing_product_items where challan_no= '".$row_loc['challan_no']."' ".$brand_id." ");
    while($row = mysqli_fetch_array($sql)){

        $partdis="";
        if($row['partcode']!="SERVICE"){
            $partdis=getAnyDetails($row['partcode'],"part_desc","partcode","partcode_master",$link1);
        }else{
            $partdis="SERVICE";
        }

        $tolocation="";
        if($row['to_location']!=""){
            $toloc=getAnyDetails($row['to_location'],"locationname","location_code","location_master",$link1);
            if($toloc){
                $tolocation=$toloc;
            }else{
                $tolocation=$row['to_location'];
            }
        }else{
            $tolocation=$row['to_location'];
        }

        $ctyf=getAnyDetails($row_loc['from_location'],"cityid","location_code","location_master",$link1);
        $ctyt=getAnyDetails($row_loc['to_location'],"cityid","location_code","location_master",$link1);

        $tot = mysqli_fetch_array(mysqli_query($link1,"Select sum(qty) as tot from billing_product_items where challan_no= '".$row_loc['challan_no']."' group by challan_no "));

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
            ->setCellValue('B'.$i, $row_loc['courier'])
            ->setCellValue('C'.$i, $row_loc['docket_no'])
            ->setCellValue('D'.$i, dt_format($row_loc['dc_date']))

            ->setCellValue('E'.$i,  $row['from_location'])
            ->setCellValue('F'.$i,  getAnyDetails($row['from_location'],"locationname","location_code","location_master",$link1))
            ->setCellValue('G'.$i, getAnyDetails($ctyf,"city","cityid","city_master",$link1))
            ->setCellValue('H'.$i, getAnyDetails($row_loc['from_stateid'],"state","stateid","state_master",$link1))
            ->setCellValue('I'.$i, $row_loc['from_gst_no'])

            ->setCellValue('J'.$i, $row['to_location'])
            ->setCellValue('K'.$i, $tolocation)
            ->setCellValue('L'.$i, getAnyDetails($ctyt,"city","cityid","city_master",$link1))
            ->setCellValue('M'.$i, getAnyDetails($row_loc['to_stateid'],"state","stateid","state_master",$link1))
            ->setCellValue('N'.$i, $row_loc['to_gst_no'])

            ->setCellValue('O'.$i, $row_loc['document_type'])
            ->setCellValue('P'.$i, $row_loc['po_type'])

            ->setCellValue('Q'.$i, $row_loc['challan_no'])
            ->setCellValue('R'.$i, dt_format2($row_loc['sale_date']))

            ->setCellValue('S'.$i, $row['job_no'])
            ->setCellValue('T'.$i, getAnyDetails($row['product_id'],"product_name","product_id","product_master",$link1))
            ->setCellValue('U'.$i, getAnyDetails($row['model_id'],"model","model_id","model_master",$link1))

            ->setCellValue('V'.$i, $row['partcode'])
            ->setCellValue('W'.$i, $partdis)
            ->setCellValue('X'.$i, $row['uom'])
            ->setCellValue('Y'.$i, $row['qty'])
            ->setCellValue('Z'.$i, $row['hsn_code'])

            ->setCellValue('AA'.$i, $row['price'])
            ->setCellValue('AB'.$i, $row['igst_per'])
            ->setCellValue('AC'.$i, $row['igst_amt'])
            ->setCellValue('AD'.$i, $row['sgst_per'])
            ->setCellValue('AE'.$i, $row['sgst_amt'])
            ->setCellValue('AF'.$i, $row['cgst_per'])
            ->setCellValue('AG'.$i, $row['cgst_amt'])

            ->setCellValue('AH'.$i, $row['basic_amt'])
            ->setCellValue('AI'.$i, $row['item_total'])

            ->setCellValue('AJ'.$i, getDispatchStatus($row_loc['status']))
            ->setCellValue('AK'.$i, dt_format($row_loc['receive_date']))
            ->setCellValue('AL'.$i, getAnyDetails($row['brand_id'],"brand","brand_id","brand_master",$link1))
            ->setCellValue('AM'.$i, $row['okqty'])
            ->setCellValue('AN'.$i, $row['broken'])
            ->setCellValue('AO'.$i, $row['missing'])

            ->setCellValue('AP'.$i, $row_loc['ship_from_code'])
            ->setCellValue('AQ'.$i, getAnyDetails($row_loc['ship_from_code'],"locationname","location_code","location_master",$link1))
            ->setCellValue('AR'.$i, getAnyDetails($row_loc['ship_from_state'],"state","stateid","state_master",$link1))
            ->setCellValue('AS'.$i, $row_loc['ship_from_addr'])
            ->setCellValue('AT'.$i, $row_loc['ship_from_gst'])

            ->setCellValue('AU'.$i, $row_loc['total_cost'])
            ->setCellValue('AV'.$i, $tot['tot']);

        $i++;
        $count++;
    }

}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Part wise Sales Return Report');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client?s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="partwise_sales_return_report.xlsx"');
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
