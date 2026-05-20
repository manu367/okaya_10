<?php
// old_code file

require_once("../includes/config.php");
date_default_timezone_set('Asia/Calcutta');
$arrstatus = getJobStatus($link1);
/////// get Access state////////////////////////
$arrstate = getAccessState($_SESSION['userid'],$link1);
////get access brand details
$access_brand = getAccessBrand($_SESSION['userid'],$link1);

//// extract all encoded variables
$modelid = base64_decode($_REQUEST['modelid']);
$productid = base64_decode($_REQUEST['proid']);
$brandid = base64_decode($_REQUEST['brand']);
$state = base64_decode($_REQUEST['state']);
$status = base64_decode($_REQUEST['status']);
/*$substatus = base64_decode($_REQUEST['substatus']);*/
$loc_code = base64_decode($_REQUEST['location_code']);
$pending = base64_decode($_REQUEST['pending']);
////// filters value/////
//////// get date /////////////////////////
if ($_REQUEST['daterange'] != ""){
    $seldate = explode(" - ",$_REQUEST['daterange']);
    $fromdate = $seldate[0];
    $todate = $seldate[1];
}
else{
    $seldate = $today;
    $fromdate = $today;
    $todate = $today;
}

/////get location///////////////
if($loc_code!=""){
    $locationcode=" current_location in ('".$loc_code."')";
}
else {
    $locationcode="1";
}
/////get model///////////////
if($modelid!=""){
    $model_id=" and model_id in ('".$modelid."')";
}
else {
    $model_id="";
}
/////get product///////////////
if($productid !=""){
    $product_id=" and product_id in ('".$productid."')";
}
else {
    $product_id="";
}
/////get brand///////////////
if($brandid !=""){
    $brand_id=" and brand_id in ('".$brandid."')";
}
else {
    $brand_id=" and brand_id in (".$access_brand.")";
}
/////get status///////////////
if($status !=""){
    $st=" and status in ('".$status."')";
}
else {
    $st=" ";
}

if($state !=""){
    $stateid=" and state_id in ('".$state."')";
}
else {
    $stateid="";
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
    ->setCategory("All Jobs");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'S.No.')
    ->setCellValue('B1', 'State')
    ->setCellValue('C1', 'City')
    ->setCellValue('D1', 'Call Center Name')
    ->setCellValue('E1', 'Call Center Code')
    ->setCellValue('F1', 'ASC Name')
    ->setCellValue('G1', 'Job For')
    ->setCellValue('H1', 'Escalations From')
    ->setCellValue('I1', 'Warranty Status')
    ->setCellValue('J1', 'Job No.')
    ->setCellValue('K1', SERIALNO)
    ->setCellValue('L1', 'Product')
    ->setCellValue('M1', 'Brand')
    ->setCellValue('N1', 'Model')
    ->setCellValue('O1', 'Customer Category')
    ->setCellValue('P1', 'Customer Name')
    ->setCellValue('Q1', 'Contact No.')
    ->setCellValue('R1', 'Alternate Contact No.')
    ->setCellValue('S1', 'Customer State')
    ->setCellValue('T1', 'Region')
    ->setCellValue('U1', 'Address')
    ->setCellValue('V1', 'Email id')
    ->setCellValue('W1', 'Pincode')
    ->setCellValue('X1', 'Residence No')
    ->setCellValue('Y1', 'Landmarks')
    ->setCellValue('Z1', 'Open Date(DD-MM-YYYY)')
    ->setCellValue('AA1', 'Purchase Date (DD-MM-YYYY)')
    ->setCellValue('AB1', 'Installation Date(DD-MM-YYYY)')
    ->setCellValue('AC1', 'AMC End Date(DD-MM-YYYY)')
    ->setCellValue('AD1', 'AMC No.')
    ->setCellValue('AE1', 'Entity Name')
    ->setCellValue('AF1', 'Dealer Name')
    ->setCellValue('AG1', 'Invoice No.')
    ->setCellValue('AH1', 'Ageing')
    ->setCellValue('AI1', 'Defect Reported')
    ->setCellValue('AJ1', 'PNA/PO No.')
    ->setCellValue('AK1', 'PNA 1')
    ->setCellValue('AL1', 'PNA 1 Description')
    ->setCellValue('AM1', 'Close Date')
    ->setCellValue('AN1', 'CWH Challan No.')
    ->setCellValue('AO1', 'CWH Challan Date')
    ->setCellValue('AP1', 'CWH Courier Name')
    ->setCellValue('AQ1', 'CWH Docket No.')
    ->setCellValue('AR1', 'Eng Name')
    ->setCellValue('AS1', 'Job Status')
    ->setCellValue('AT1', 'Remark')
    ->setCellValue('AU1', 'Customer Feed Back')
    ->setCellValue('AV1', 'Customer Remarks')
    ->setCellValue('AW1', 'Area Type')
    ->setCellValue('AX1', 'Pending Reason')
    ->setCellValue('AY1', 'Closed Reason')
    ->setCellValue('AZ1', 'PO Date');
////////////////
///////////////////////
cellColor('A1:AZ1', 'F28A8C');
////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;
//all pending data nikalte hai yha se
if($pending == 'checked'){
    $sql_loc = mysqli_query($link1,"Select * from jobsheet_data where ".$locationcode." ".$model_id." ".$stateid." ".$cityid." ".$product_id." ".$brand_id." and status in ('1','2','3','7','81') ")or die("error 1 ".mysqli_error($link1));
}
else{
    $sql_loc = mysqli_query($link1,"Select * from jobsheet_data where ".$locationcode." and (open_date >= '".$fromdate."' and open_date <='".$todate."') ".$model_id." ".$st." ".$stateid." ".$product_id." ".$brand_id."")or die("error 2 ".mysqli_error($link1));
}

while($row_loc = mysqli_fetch_array($sql_loc)){

    $cust_det = explode("~",getAnyDetails($row_loc['customer_id'],"pincode,address1,stateid,landmark,email,phone,alt_mobile,cityid","customer_id","customer_master",$link1));

    $row_set=mysqli_fetch_array(mysqli_query($link1,"select  display_status from jobstatus_master where main_status_id='".$row_loc['status']."'"));
    $res_st=$row_set['display_status'];

    if( $row_loc['hc_feedback']==1){
        $feedback='Poor';
    }else if($row_loc['hc_feedback']==2){
        $feedback='Average';

    }else if($row_loc['hc_feedback']==3){
        $feedback='Good';

    }else if($row_loc['hc_feedback']==4){
        $feedback='Very Good';

    }else if($row_loc['hc_feedback']==5){
        $feedback='Excellent';

    }else{
        $feedback='';
    }

    $product_det = mysqli_fetch_assoc(mysqli_query($link1,"SELECT * FROM product_registered  where serial_no='".$row_loc['imei']."'"));
    $voc1 = getAnyDetails($row_loc['cust_problem'] ,"voc_desc","voc_code","voc_master" ,$link1);
    $voc2 = getAnyDetails($row_loc['cust_problem2'] ,"voc_desc","voc_code","voc_master" ,$link1);
    $voc3 = getAnyDetails($row_loc['cust_problem3'] ,"voc_desc","voc_code","voc_master" ,$link1);
    if($row_loc['close_date'] =='0000-00-00' ){ $aging = daysDifference($today,$row_loc['open_date']);} else {$aging = "--" ;}
    if($row_loc['close_date']  != '0000-00-00'){$tat = daysDifference($row_loc['close_date'],$row_loc['open_date']);}else{ $tat = "--" ;}
    $partdesc="";
    $statestr="";
    $pono_det =mysqli_query($link1,"Select * from auto_part_request where job_no= '".$row_loc['job_no']."' ")or die("error 5 ".mysqli_error($link1));
    if(mysqli_num_rows($pono_det)>0){
        while($pono = mysqli_fetch_array($pono_det)){
            if($partdesc==""){
                $partdesc=getAnyDetails($pono['partcode'],"part_desc","partcode","partcode_master",$link1);
            }else{
                $partdesc.=",".getAnyDetails($pono['partcode'],"part_desc","partcode","partcode_master",$link1);
            }
            if($statestr==""){

                $statestr=getAnyDetails($pono['partcode'],"vendor_partcode","partcode","partcode_master",$link1);
            }else{
                $statestr.=",".getAnyDetails($pono['partcode'],"vendor_partcode","partcode","partcode_master",$link1);
            }
        }
    }else {
        $partdesc="";
        $statestr="";
    }

    $pono = mysqli_fetch_array(mysqli_query($link1,"Select po_no,update_date from po_items where job_no= '".$row_loc['job_no']."' "));

    $challan_no=mysqli_fetch_array(mysqli_query($link1,"Select challan_no from billing_product_items where job_no= '".$row_loc['job_no']."' "));

    $info=mysqli_fetch_array(mysqli_query($link1,"Select dc_date,courier,docket_no from billing_master where challan_no = '".$challan_no['challan_no']."' "));


    if($row_loc['location_code'] == $asc_part['from_location']) {$status = "Pending at Asc to L3";} elseif ($row_loc['location_code'] == $asc_part['to_location'] ){$status = "Pending at L3 to Asc";}

    if($row_loc['entity_type']=='Others'){
        $entity="Others";

    }else{
        $entity= getAnyDetails($row_loc['entity_type'],"name","id","entity_type",$link1);
    }


    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A'.$i, $count )
        ->setCellValue('B'.$i, getAnyDetails($row_loc['state_id'],"state","stateid","state_master",$link1))
        ->setCellValue('C'.$i, getAnyDetails($row_loc['city_id'],"city","cityid","city_master",$link1))
        ->setCellValue('D'.$i, getAnyDetails($row_loc['location_code'],"locationname","location_code","location_master",$link1))
        ->setCellValue('E'.$i, $row_loc['location_code'])
        ->setCellValue('F'.$i, getAnyDetails($row_loc['current_location'],"locationname","location_code","location_master",$link1))
        ->setCellValue('G'.$i, $row_loc['call_for'])
        ->setCellValue('H'.$i, $row_loc['call_type'])
        ->setCellValue('I'.$i, $row_loc['warranty_status'])
        ->setCellValue('J'.$i, $row_loc['job_no'])
        ->setCellValue('K'.$i, $row_loc['imei'])
        ->setCellValue('L'.$i, getAnyDetails($row_loc['product_id'],"product_name","product_id","product_master",$link1))
        ->setCellValue('M'.$i, getAnyDetails($row_loc['brand_id'],"brand","brand_id","brand_master",$link1))
        //->setCellValue('N'.$i, $row_loc['model'])
        ->setCellValue('N'.$i, getAnyDetails($row_loc['model_id'],"model","model_id","model_master",$link1))
        ->setCellValue('O'.$i, $row_loc['customer_type'])
        ->setCellValue('P'.$i, $row_loc['customer_name'])
        ->setCellValue('Q'.$i, "".$row_loc['contact_no'])
        ->setCellValue('R'.$i, "".$cust_det[6])
        ->setCellValue('S'.$i, getAnyDetails($cust_det[3],"state","stateid","state_master",$link1))
        ->setCellValue('T'.$i,  $row_loc['area'])
        ->setCellValue('U'.$i, $cust_det[1])
        ->setCellValue('V'.$i, $cust_det[4])
        ->setCellValue('W'.$i, "".$cust_det[0])
        ->setCellValue('X'.$i, "".$cust_det[5])
        ->setCellValue('Y'.$i, $cust_det[3])
        ->setCellValue('Z'.$i, dt_format($row_loc['open_date']))
        ->setCellValue('AA'.$i, dt_format($row_loc['dop']))
        ->setCellValue('AB'.$i, dt_format($product_det['installation_date']))
        ->setCellValue('AC'.$i, dt_format($product_det['amc_end_date']))
        ->setCellValue('AD'.$i, $product_det['amc_no'])
        ->setCellValue('AE'.$i, $entity)
        ->setCellValue('AF'.$i, $row_loc['dname'])
        ->setCellValue('AG'.$i, $row_loc['inv_no'])
        ->setCellValue('AH'.$i, $aging )
        ->setCellValue('AI'.$i, $voc1."/".$voc2."/".$voc3)
        ->setCellValue('AJ'.$i, $pono['po_no'])
        ->setCellValue('AK'.$i, $statestr)
        ->setCellValue('AL'.$i, $partdesc)
        ->setCellValue('AM'.$i, dt_format($row_loc['close_date']))

        ->setCellValue('AN'.$i, $challan_no['challan_no'])
        ->setCellValue('AO'.$i, dt_format($info['dc_date']))
        ->setCellValue('AP'.$i, $info['courier'])
        ->setCellValue('AQ'.$i, $info['docket_no'])

        ->setCellValue('AR'.$i, getAnyDetails($row_loc['eng_id'],"locusername","userloginid","locationuser_master",$link1))
        ->setCellValue('AS'.$i, $res_st)
        ->setCellValue('AT'.$i, $row_loc['remark'])
        ->setCellValue('AU'.$i, $feedback)
        ->setCellValue('AV'.$i, $row_loc['hc_remark'])
        ->setCellValue('AW'.$i, $row_loc['area_type'])
        ->setCellValue('AX'.$i, $row_loc['reason'])
        ->setCellValue('AY'.$i, $row_loc['close_rmk'])
        ->setCellValue('AZ'.$i, dt_format($pono['update_date']));
    $i++;
    $count++;

}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client?s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="allcall_report.xlsx"');
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
