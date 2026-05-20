<?php
/**
 * Workflow of the Application
 *  Step 1= User upload the file
 *  Step 2= check post request and $_FILE exist or not
 *  Step 3= fileupload and loadExcelSheet
 *  Step 4= getAll the data in Array unit
 *  Step 5 = store the array unit in ReplcaeMentModal (future me data validation ke liye best rahega)
 *  Step 6 = one by one we are store data in db
 *           Step i = call vaidateDataFromDB : [status=true|false,msg]
 *           step ii = validatedatafrom_db[status]=true then call functon updateData
 *                     otherwise store error data in $error_data[]=[model,msg]
 *  Step 7 = if any error found in the application store all error in $error[];
 */

require_once("../includes/config.php");
require_once ("../ExcelExportAPI/Classes/PHPExcel.php");
require_once ("../ExcelExportAPI/Classes/PHPExcel/IOFactory.php");
global $link1;

/**
 * GlobalException Class
 *
 * Application-level custom exception handler.
 * Use this class to throw and manage exceptions
 * consistently across the entire application.
 *
 * This helps in centralizing error handling logic
 * and makes debugging and maintenance easier.
 */
class GlobalException extends Exception {
    public function __construct($msg){
        parent::__construct($msg);
    }
}

/**
 * Global Exception Handler
 *
 * This handler catches all uncaught exceptions in the application.
 * If the exception is an instance of GlobalException,
 * the user is redirected to a specific page with the error message.
 *
 * Note:
 * - The exception message is passed via query string.
 * - Consider encoding the message and avoiding direct exposure
 *   of sensitive error details in production environments.
 */
set_exception_handler(function ($e){
    if($e instanceof GlobalException){
        $http_res=$e->getMessage();
        header("location:replacement_uploader.php?".$http_res);
        exit();
    }
});

/**
 * Convert Excel serial date value to a standard date string (Y-m-d).
 *
 * Excel stores dates as serial numbers (e.g., 43126 = 2018-01-01).
 * This function converts that numeric value into a readable date format.
 *
 * @param int|float $v Excel serial date value
 * @return string Returns formatted date (Y-m-d) or '0000-00-00' if empty
 */
function excelDateToDate($v)
{
    if(empty($v)) return '0000-00-00';
    return gmdate("Y-m-d", ($v - 25569) * 86400);
}

/**
 * ReplacementModel
 * Data container for replacement-related operations.
 * Encapsulates job details, SAP references, and date fields
 * used during replacement processing workflows.
 *
 * Responsibilities:
 * - Store and provide access to replacement data
 * - Normalize invalid/default Excel dates (e.g., '1899-12-30' → '0000-00-00')
 * - Act as a structured data transfer object (DTO) between layers
 *
 * Note:
 * This class does not contain business logic; it only manages data.
 */
class ReplaceMentModal{
    private $job_no,
            $repl_settle_cat,
            $delivery_date,
            $old_battery_collection,
            $so_creation_date;
    private $sap_notification,
            $notification_created,
            $return_order_sap,
            $replacement_order_sap;
    private $repl_invoice_no_sap,
            $billing_date_sap;
    public $msg;

    /**
     * @return mixed
     */
    public function getJobNo()
    {
        return $this->job_no;
    }

    /**
     * @param mixed $job_no
     */
    public function setJobNo($job_no)
    {
        $this->job_no = $job_no;
    }

    /**
     * @return mixed
     */
    public function getReplSettleCat()
    {
        return $this->repl_settle_cat;
    }

    /**
     * @param mixed $repl_settle_cat
     */
    public function setReplSettleCat($repl_settle_cat)
    {
        $this->repl_settle_cat = $repl_settle_cat;
    }

    /**
     * @return mixed
     */
    public function getDeliveryDate()
    {
        return $this->delivery_date;
    }

    /**
     * @param mixed $delivery_date
     */
    public function setDeliveryDate($delivery_date)
    {
        if($delivery_date==='1899-12-30'){
            $this->delivery_date ='0000-00-00';
        }else{
            $this->delivery_date = $delivery_date;
        }
    }

    /**
     * @return mixed
     */
    public function getOldBatteryCollection()
    {
        return $this->old_battery_collection;
    }

    /**
     * @param mixed $old_battery_collection
     */
    public function setOldBatteryCollection($old_battery_collection)
    {
        $this->old_battery_collection = $old_battery_collection;
    }

    /**
     * @return mixed
     */
    public function getSoCreationDate()
    {
        return $this->so_creation_date;
    }

    /**
     * @param mixed $so_creation_date
     */
    public function setSoCreationDate($so_creation_date)
    {
        if($so_creation_date==='1899-12-30'){
            $this->so_creation_date = '0000-00-00';
        }else{
            $this->so_creation_date = $so_creation_date;
        }
    }

    /**
     * @return mixed
     */
    public function getSapNotification()
    {
        return $this->sap_notification;
    }

    /**
     * @param mixed $sap_notification
     */
    public function setSapNotification($sap_notification)
    {
        $this->sap_notification = $sap_notification;
    }

    /**
     * @return mixed
     */
    public function getNotificationCreated()
    {
        return $this->notification_created;
    }

    /**
     * @param mixed $notification_created
     */
    public function setNotificationCreated($notification_created)
    {
        $this->notification_created = $notification_created;
    }

    /**
     * @return mixed
     */
    public function getReturnOrderSap()
    {
        return $this->return_order_sap;
    }

    /**
     * @param mixed $return_order_sap
     */
    public function setReturnOrderSap($return_order_sap)
    {
        $this->return_order_sap = $return_order_sap;
    }

    /**
     * @return mixed
     */
    public function getReplacementOrderSap()
    {
        return $this->replacement_order_sap;
    }

    /**
     * @param mixed $replacement_order_sap
     */
    public function setReplacementOrderSap($replacement_order_sap)
    {
        $this->replacement_order_sap = $replacement_order_sap;
    }

    /**
     * @return mixed
     */
    public function getReplInvoiceNoSap()
    {
        return $this->repl_invoice_no_sap;
    }

    /**
     * @param mixed $repl_invoice_no_sap
     */
    public function setReplInvoiceNoSap($repl_invoice_no_sap)
    {
        $this->repl_invoice_no_sap = $repl_invoice_no_sap;
    }

    /**
     * @return mixed
     */
    public function getBillingDateSap()
    {
        return $this->billing_date_sap;
    }

    /**
     * @param mixed $billing_date_sap
     */
    public function setBillingDateSap($billing_date_sap)
    {
        if($billing_date_sap==='1899-12-30'){
            $this->billing_date_sap ='0000-00-00';
        }else{
            $this->billing_date_sap = $billing_date_sap;
        }
    }

}
class ReplacementUploaderPage{
    private $connection;
    public function __construct($connection){
        $this->connection = $connection;
    }

    /**
     * Extract column headers from the first row of an Excel sheet.
     *
     * Reads the first row (row 1) and returns all non-empty cell values
     * as an array of trimmed column names.
     *
     * @param PHPExcel_Worksheet $sheet Excel worksheet instance
     * @return array List of column headers
     * @throws PHPExcel_Exception
     */
    public function sheetcolumn($sheet){
        $columns = [];

        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

        for($col = 0; $col < $highestColumnIndex; $col++){
            $value = $sheet->getCellByColumnAndRow($col, 1)->getValue();
            if($value !== null){
                $columns[] = trim($value);
            }
        }
        return $columns;
    }

    /**
     * Read and transform Excel sheet data into structured array.
     *
     * Iterates from row 2 to the last row and maps each column
     * to a predefined key. ასევე converts Excel date values into
     * standard format using excelDateToDate().
     *
     * Column Mapping:
     * 0  => job_no
     * 1  => repl_settle_cat
     * 2  => delivery_date
     * 3  => old_battery_collection
     * 4  => so_creation_date
     * 5  => sap_notification
     * 6  => notification_created
     * 7  => return_order_sap
     * 8  => replacement_order_sap
     * 9  => repl_invoice_no_sap
     * 10 => billing_date_sap
     *
     * @param PHPExcel_Worksheet $sheet
     * @param int $highestRow
     * @return array Structured dataset from Excel
     */
    public function getAllExcelData($sheet, $highestRow){
        $rowdata=[];
        for($row = 2; $row <= $highestRow; $row++){
            $data=[];
            $job_no = trim($sheet->getCellByColumnAndRow(0,$row)->getValue());
            $repl_settle_cat  = trim($sheet->getCellByColumnAndRow(1,$row)->getValue());
            $delivery_date = excelDateToDate($sheet->getCellByColumnAndRow(2,$row)->getValue());
            $old_battery_collection   = ($sheet->getCellByColumnAndRow(3,$row)->getValue());
            $so_creation_date  = excelDateToDate(trim($sheet->getCellByColumnAndRow(4,$row)->getValue()));
            $sap_notification  = trim($sheet->getCellByColumnAndRow(5,$row)->getValue());
            $notification_created  = excelDateToDate(trim($sheet->getCellByColumnAndRow(6,$row)->getValue()));
            $return_order_sap  = trim($sheet->getCellByColumnAndRow(7,$row)->getValue());
            $replacement_order_sap  = trim($sheet->getCellByColumnAndRow(8,$row)->getValue());
            $repl_invoice_no_sap  = trim($sheet->getCellByColumnAndRow(9,$row)->getValue());
            $billing_date_sap=excelDateToDate(trim($sheet->getCellByColumnAndRow(10,$row)->getValue()));
            $data['job_no'] =$job_no;
            $data['repl_settle_cat']=$repl_settle_cat;
            $data['delivery_date']=$delivery_date;
            $data['old_battery_collection']=$old_battery_collection;
            $data['so_creation_date']=$so_creation_date;
            $data['sap_notification']=$sap_notification;
            $data['notification_created']=$notification_created;
            $data['return_order_sap']=$return_order_sap;
            $data['replacement_order_sap']=$replacement_order_sap;
            $data['repl_invoice_no_sap']=$repl_invoice_no_sap;
            $data['billing_date_sap']=$billing_date_sap;
            $rowdata[]=$data;
        }
        return $rowdata;
    }

    /**
     * Handle file upload with validation and safe storage.
     *
     * Validates uploaded file input, checks for upload errors,
     * restricts allowed file types, sanitizes the filename,
     * and stores the file in a designated directory with a unique name.
     *
     * @param array $file_up Uploaded file array ($_FILES)
     * @return string Full path of the uploaded file
     * @throws GlobalException If validation or upload fails
     */
    public function fileUploader($file_up)
    {
        if (!isset($file_up['file'])) {
            throw new GlobalException("No file provided");
        }

        $file = $file_up['file'];


        if ($file['error'] !== 0) {
            throw new GlobalException("File upload error");
        }

        $filename = $file['name'];
        $tmp = $file['tmp_name'];

        $allowedExt = ['xls', 'xlsx', 'csv', 'xlsm'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            throw new GlobalException("File type mismatch Error");
        }
        $cleanName = preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $filename);
        $today = date('YmdHis');
        $newName = $today . '_' . $cleanName;
        $uploadDir = "../ExcelExportAPI/upload/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $destination = $uploadDir . $newName;
        if (!move_uploaded_file($tmp, $destination)) {
            throw new GlobalException("Failed to move uploaded file");
        }
        chmod($destination, 0644);
        return $destination;
    }

    /**
     * this function load the sheet based on filename and retyrb sheet object
     * @param $filename
     * @return PHPExcel_Worksheet
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    public function loadSheets($filename){
        $identityType = PHPExcel_IOFactory::identify($filename);
        $object = PHPExcel_IOFactory::createReader($identityType);     //Ab jo file type detect hua hai uske according reader object create kiya ja raha hai.
        $object->setReadDataOnly(true); //data read karo
        $objPHPExcel = $object->load($filename); // Excel file ko memory me load
        $sheet = $objPHPExcel->getSheet(0); // first sheet
        return $sheet;
    }


    /**
     * @param $conn
     * @param ReplaceMentModal $modal
     * @return true
     * @throws Exception
     */
    public function updateData($conn, ReplaceMentModal $modal){

        $job_no = mysqli_real_escape_string($conn, $modal->getJobNo());
        $repl_settle_cat = mysqli_real_escape_string($conn, $modal->getReplSettleCat());
        $delivery_date = mysqli_real_escape_string($conn, $modal->getDeliveryDate());
        $old_battery_collection = mysqli_real_escape_string($conn, $modal->getOldBatteryCollection());
        $so_creation_date = mysqli_real_escape_string($conn, $modal->getSoCreationDate());
        $sap_notification = mysqli_real_escape_string($conn, $modal->getSapNotification());
        $notification_created = mysqli_real_escape_string($conn, $modal->getNotificationCreated());
        $return_order_sap = mysqli_real_escape_string($conn, $modal->getReturnOrderSap());
        $replacement_order_sap = mysqli_real_escape_string($conn, $modal->getReplacementOrderSap());
        $repl_invoice_no_sap = mysqli_real_escape_string($conn, $modal->getReplInvoiceNoSap());
        $billing_date_sap = mysqli_real_escape_string($conn, $modal->getBillingDateSap());



        $delivery_sql_part = "";

        if($this->checkDeliveryDateInDB($modal)){
            $delivery_sql_part = "delivery_date = '$delivery_date',";
        }

        $sql = "UPDATE replacement_data SET 
                $delivery_sql_part
                old_battery_collection = '$old_battery_collection',
                sap_notification = '$sap_notification',
                notification_created = '$notification_created',
                return_order_sap = '$return_order_sap',
                replacement_order_sap = '$replacement_order_sap',
                repl_invoice_no_sap = '$repl_invoice_no_sap',
                billing_date_sap = '$billing_date_sap'
            WHERE job_no = '$job_no'";

        $result = mysqli_query($conn, $sql);
        if(!$result){
            throw new Exception("Update Failed: " . mysqli_error($conn));
        }
        return true;
    }

    /**
     * Validate incoming modal data against database records
     *
     * Validation flow:
     * 1. Check if job number exists in DB
     * 2. Verify replacement settlement category matches DB
     * 3. Ensure delivery date in DB is not already set (i.e., not valid data)
     *
     * @param ReplaceMentModal $modal Object containing incoming data (e.g., from Excel)
     * @return array Returns associative array with status (bool) and message (string)
     * @throws Exception If any database query fails
     */
    public function vaidateDataFromDB(ReplaceMentModal $modal){
        $job_no = mysqli_real_escape_string($this->connection, $modal->getJobNo());
        $repl_settle_cat = mysqli_real_escape_string($this->connection, $modal->getReplSettleCat());
        $delivery_date = mysqli_real_escape_string($this->connection, $modal->getDeliveryDate());
        $old_battery_collection = mysqli_real_escape_string($this->connection, $modal->getOldBatteryCollection());
        $so_creation_date = mysqli_real_escape_string($this->connection, $modal->getSoCreationDate());
        $sap_notification = mysqli_real_escape_string($this->connection, $modal->getSapNotification());
        $notification_created = mysqli_real_escape_string($this->connection, $modal->getNotificationCreated());
        $return_order_sap = mysqli_real_escape_string($this->connection, $modal->getReturnOrderSap());
        $replacement_order_sap = mysqli_real_escape_string($this->connection, $modal->getReplacementOrderSap());
        $repl_invoice_no_sap = mysqli_real_escape_string($this->connection, $modal->getReplInvoiceNoSap());
        $billing_date_sap = mysqli_real_escape_string($this->connection, $modal->getBillingDateSap());

        //Check if complaint/job number exists in database
        if(!$this->checkComplaintExistOrNot($this->connection,$job_no)){
         return ["status"=>false,"msg"=>"Job Id Not Found"];
        }

        //Validate replacement settlement category consistency
        if(!$this->check_ReplacementCategory_And_Model_Category_Same($this->connection,$modal)){
            return ["status"=>false,"msg"=>"Replacement Settlement Category not found"];
        }
        // Check if delivery date is already present (i.e., record already processed)
        if(!$this->checkDeliveryDateInDB($modal)){
            return ["status"=>false,"msg"=>"Already exits data in database"];
        }
        // If all validations pass
        return ["status"=>true,"msg"=>"Data is validated"];
    }

    /**
     * Check whether the delivery date in DB is invalid (i.e., '0000-00-00')
     *
     * NOTE:
     * Despite the name, this function does NOT compare Excel vs DB date.
     * It only checks if the DB has a placeholder/invalid date.
     *
     * @param ReplaceMentModal $modal Object containing job data
     * @return bool Returns true if DB date is '0000-00-00', false otherwise
     * @throws Exception If query execution fails
     */
    private function checkDeliveryDateInDB(ReplaceMentModal $modal){
        $job_no = mysqli_real_escape_string($this->connection, $modal->getJobNo());
        $excelDate = $modal->getDeliveryDate();

        $sql = "SELECT delivery_date 
            FROM replacement_data 
            WHERE job_no = '$job_no' 
            LIMIT 1";

        $result = mysqli_query($this->connection, $sql);

        if(!$result){
            throw new Exception(mysqli_error($this->connection));
        }

        if(mysqli_num_rows($result) == 0){
            return false;
        }

        $row = mysqli_fetch_assoc($result);
        $dbDate = $row['delivery_date'];
        //  Case 1: DB me valid date hai ya nahi
        if($dbDate === '0000-00-00'){
            return true;
        }
        return false;
    }

    /**
     * Validate whether the replacement settlement category from DB
     * matches the category provided in the modal (e.g., Excel data)
     *
     * @param mysqli $conn Active database connection
     * @param ReplaceMentModal $modal Object containing incoming data
     * @return bool True if categories match, false otherwise
     * @throws Exception If query fails or no record is found
     */
    private function check_ReplacementCategory_And_Model_Category_Same($conn, ReplaceMentModal $modal){

        $job_no = mysqli_real_escape_string($conn, $modal->getJobNo());

        $sql = "SELECT repl_settle_cat 
            FROM replacement_data 
            WHERE job_no = '$job_no' 
            LIMIT 1";

        $result = mysqli_query($conn, $sql);

        if(!$result){
            throw new Exception(mysqli_error($conn));
        }

        // agar record hi nahi mila
        if(mysqli_num_rows($result) == 0){
            throw new Exception("Category not found for job_no");
        }

        $row = mysqli_fetch_assoc($result);

        $dbCategory = strtolower(trim($row['repl_settle_cat']));
        $excelCategory = strtolower(trim($modal->getReplSettleCat()));
        return $dbCategory === $excelCategory;
    }

    /**
     * Check whether a complaint (job number) exists in the database
     *
     * @param mysqli $conn Active MySQLi database connection
     * @param string $job_no Job/complaint number to check
     * @return bool Returns true if record exists, false otherwise
     * @throws Exception If query execution fails
     */
    private function checkComplaintExistOrNot($conn, $job_no){
        $job_no = mysqli_real_escape_string($conn, $job_no);
        $sql = "SELECT job_no FROM replacement_data WHERE job_no = '$job_no' LIMIT 1";
        $result = mysqli_query($conn, $sql);
        if(!$result){
            throw new Exception(mysqli_error($conn));
        }
        return mysqli_num_rows($result) > 0;
    }

    /**
     * Render error data in a formatted HTML table
     *
     * @param array $modal Array of objects containing error data
     * @return string|null Returns HTML table string or null if no data
     */
    public function errorDataRender($modal = []){
        if(empty($modal) || count($modal) == 0){
            return;
        }
        $str= '<table id="errorTable"  class="table table-warning" style="border-radius: 10px;">
            <thead style="background-color: darkred;color: white;border-radius: 5px;">
            <tr>
                <th>#</th>
                <th>Complaint No.</th>
                <th>Replacement Settlement Category</th>
                <th>TAT-3 Close Date</th>
                <th>Old Product Collection</th>
                <th>SO Creation Date</th>
                <th>Service Notification No</th>
                <th>Service Notification Date</th>
                <th>SAP Return Order No.</th>
                <th>SAP Replacement Order No.</th>
                <th>SAP Replacement Invoice No</th>
                <th>SAP Replacement Invoice Date</th>
                <th>Error Msg</th>
            </tr>
            </thead>
            <tbody>';

        $i = 1;
        foreach($modal as $obj){

            $str.= '<tr>
                <td>'.$i++.'</td>
                <td>'.$obj->getJobNo().'</td>
                <td>'.$obj->getReplSettleCat().'</td>
                <td>'.$obj->getDeliveryDate().'</td>
                <td>'.$obj->getOldBatteryCollection().'</td>
                <td>'.$obj->getSoCreationDate().'</td>
                <td>'.$obj->getSapNotification().'</td>
                <td>'.$obj->getNotificationCreated().'</td>
                <td>'.$obj->getReturnOrderSap().'</td>
                <td>'.$obj->getReplacementOrderSap().'</td>
                <td>'.$obj->getReplInvoiceNoSap().'</td>
                <td>'.$obj->getBillingDateSap().'</td>
                <td>'.$obj->msg.'</td>
              </tr>';
        }
        $str.= '</tbody></table>';
        return $str;
    }
}



// http data help to build redirect data = param units
$http_data=[];
$http_data['pid']=$_REQUEST['pid'];
$http_data['hid']=$_REQUEST['hid'];
// creating object
$replacement=new ReplacementUploaderPage($link1);
$pre_define_col=['Complaint No.',
        'Replacement Settlement Category',
        'TAT-3 Close Date (Delivery Date)',
        'Old Product Collection',
        'SO Creation Date',
        'Service Notification No.',
        'Service Notification Date',
        'SAP Return Order No.',
        'SAP Replacement Order No.',
        'SAP Replacement Invoice No.',
        'SAP Replacement Invoice Date'
];

// when form is submit
if(isset($_POST['Submit'])){
    try{
        $destinations=$replacement->fileUploader($_FILES);
        if(!$destinations){
            throw new Exception("Some things is wrong");
        }
        $sheet=$replacement->loadSheets($destinations);
        $sheet_column=$replacement->sheetcolumn($sheet);
        // Check count first
        if (count($pre_define_col) !== count($sheet_column)) {
            throw new Exception("Column count mismatch!");
        }
        // Check exact match (order + name)
        if ($pre_define_col !== $sheet_column) {
            $missing = array_diff($pre_define_col, $sheet_column);
            $extra   = array_diff($sheet_column, $pre_define_col);
            throw new Exception("Column mismatch detected");
        }
        $highestRow = $sheet->getHighestDataRow();
        $data=$replacement->getAllExcelData($sheet,$highestRow);
        if(count($data)<=0){
            throw new Exception("Empty data is not updated");
        }

        $modal = [];
        for($i = 0; $i < count($data); $i++){
            $row = $data[$i];
            $update_data = new ReplaceMentModal();
            $update_data->setJobNo($row['job_no']);
            $update_data->setReplSettleCat($row['repl_settle_cat']);
            $deliveryDate = $row['delivery_date'];
            $update_data->setDeliveryDate($deliveryDate);
            $update_data->setOldBatteryCollection($row['old_battery_collection']);
            $soDate = $row['so_creation_date'];
            $update_data->setSoCreationDate($soDate);
            $update_data->setSapNotification($row['sap_notification']);
            $notificationDate = $row['notification_created'];  // yha me future me date validation laga sakta hu
            $update_data->setNotificationCreated($notificationDate);
            $update_data->setReturnOrderSap($row['return_order_sap']);
            $update_data->setReplacementOrderSap($row['replacement_order_sap']);
            $update_data->setReplInvoiceNoSap($row['repl_invoice_no_sap']);
            $update_data->setBillingDateSap($row['billing_date_sap']);
            $modal[$i] = $update_data;
        }


        if(count($modal)<=0){
            throw new Exception('Empty data is not updated');
        }

        $error_data=[];
        for($i=0;$i<count($modal);$i++){
            $validate=$replacement->vaidateDataFromDB($modal[$i]);
            if($validate['status']){
                $replacement->updateData($link1,$modal[$i]);
            }else{
                $_modal=$modal[$i];
                $_modal->msg=$validate['msg'];
                $error_data[]=$_modal;
            }
        }

        if(count($error_data)>0){
        }
        else{
            $http_data['msg']='Successfully Updated the file';
            $http_data['type']='Successs';
            $param=http_build_query($http_data);
            header("location:replacement_uploader.php?".$param);
            exit();
        }

    }catch (Exception $e){
        $msg=$e->getMessage();
        $http_data['msg']=$msg;
        $http_data['type']='error';
        $param=http_build_query($http_data);
        throw new GlobalException($param);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=siteTitle?></title>
    <script src="../js/jquery.min.js"></script>
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/abc.css" rel="stylesheet">
    <script src="../js/bootstrap.min.js"></script>
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="../js/frmvalidate.js"></script>
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <script type="text/javascript" src="../js/common_js.js"></script>
    <script src="../js/jquery-1.10.1.min.js"></script>
    <script src="../js/fileupload.js"></script>
    <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    <style>
        .toast {
            position: fixed;
            top: 20px;
            right: -350px;
            display: flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(8px);
            color: #fff;
            padding: 14px 18px;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            font-size: 14px;
            font-weight: bold;
            min-width: 250px;
            max-width: 300px;
            z-index: 999;
            transition: all 0.4s ease;
            opacity: 0;
        }

        .toast.show {
            right: 20px;
            opacity: 1;
        }

        .toast .icon {
            font-size: 18px;
        }

        .toast .message {
            flex: 1;
        }
        .toast::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 100%;
            background: #fff;
            animation: progress 60s linear;
        }

        @keyframes progress {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>
    <script>
        window.addEventListener("load", function() {
            const toast = document.getElementById("errorPopup");
            if (toast) {
                setTimeout(() => {
                    toast.classList.add("show");
                }, 300); // small delay for smooth entry

                setTimeout(() => {
                    toast.classList.remove("show");
                }, 50000); // hide after 3s
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#errorTable').DataTable({
                "pageLength": 3,
                "lengthMenu": [3, 10, 25, 50],
                "ordering": false
            });
        });
    </script>
</head>
<body>
<?php
if(isset($_REQUEST['msg'])){?>
    <div id="errorPopup" class="toast" style="background-color: <?= isset($_REQUEST['type']) && $_REQUEST['type']==='error'?'darkred':'green' ?>">
        <span class="message"><?=htmlspecialchars($_GET['msg'], ENT_QUOTES, 'UTF-8');?></span>
    </div>
<?php } ?>
<div class="container-fluid">
    <div class="row content">
        <?php
        include("../includes/leftnav2.php");
        ?>
        <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
            <h2 align="center"><i class="fa fa-upload"></i>Replacement Uploader</h2><div style="display:inline-block;float:right">
                <a href="../templates/Uploader_Format.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/></a></div>	<br></br>

            <div class="form-group"  id="page-wrap" style="margin-left:10px;">

                <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">


                    <div class="form-group">
                        <div class="col-md-12"><label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
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
                        <div class="col-md-12" align="center">
                            <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
                            &nbsp;&nbsp;&nbsp;

                        </div>
                    </div>
                </form>
            </div><hr>
            <?php
            if(count($error_data)>0){
                echo '<h2 style="text-align: center">Wrong Data</h2>';
                echo $replacement->errorDataRender($error_data);
            }
            ?>
        </div>
    </div>


</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>