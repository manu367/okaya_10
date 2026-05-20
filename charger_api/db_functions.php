<?php
class DB_Functions {
	private $db;
	private $link;
	private $dt_format;
	function __construct() {
		include_once './db_connect.php';
		$this->db = new DB_Connect();         
		$this->link = $this->db->connect();
		$this->dt_format = new DateTime("now", new DateTimeZone('Asia/Calcutta')); //first argument "must" be a string
		$this->dt_format->setTimestamp(time()); //adjust the object to correct timestamp
	}
	function __destruct() {}

	function escape($x) {
		return "'".mysqli_real_escape_string($x)."'";
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////// validate serial number /////////////////////////////////////////////////
	public function ValidateSerialno() {
		$today = date("Y-m-d");
	
               $serial_no = mysqli_fetch_array(mysqli_query($this->link, " select serial_no from complaints_master where job_no = '".$_REQUEST['job_no']."' "));
		
		$result = mysqli_query($this->link,"SELECT * FROM warranty_data_battery  where serial_no='".$serial_no['serial_no']."' and start_date!='0000-00-00' and end_date!='0000-00-00' order by sno desc  ")or die(mysqli_error($this->link));
		if (mysqli_num_rows($result) > 0) {
			return $result;
		}
		
		
		
		
		}	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////// validate serial number /////////////////////////////////////////////////
           ###################################3 Discharger Wifi/Bluetooth api details ###############################################33
	public function discharger_info($jobno) {	
		$result = mysqli_query($this->link,"SELECT * FROM complaints_master where job_no = '".$jobno."' ")or die(mysqli_error($this->link));
		if(mysqli_num_rows($result)){
		
		return $result;
		}
	}
	
###### Function for CHeck User Details
	public function getUserExistDetails($eid, $pswd) {
		$result = mysqli_query($this->link,"SELECT username,mobile,userid,address1,sapcode,cityid,status FROM user_master where usercode='".$eid."' and password='".$pswd."' and status='A'")or die(mysqli_error($this->link));
		if(mysqli_num_rows($result)){
		
		return $result;
		}
	}

#### END FUnction	

###### Function for Post Discharger Result Data
public function save_btr($test_dt,$job_no,$test_loc,$test_by,$device_name,$capacity,$test_load,$start_voltage,$cut_off_voltage,$test_time,$test_result,$special_gr,$backup,$document_name,$end_voltage,$app_version,$test_decision,$phy_condition,$test_interrupt,$serial_no) {
	date_default_timezone_set('Asia/Kolkata');
	$today=date('Y-m-d');
	$curr_time=date("H:i:s");

	$b = array();
	
	if ($job_no!='')
	{	
			  	$query1 = "insert into  discharger_result_master set job_no ='".$job_no."',test_date='".$test_dt."', test_location='".$test_loc."',test_by='".$test_by."',device_name='".$device_name."',battery_capacity='".$capacity."',test_load='".$test_load."',start_voltage='".$start_voltage."',cut_off_voltage='".$cut_off_voltage."',test_time='".$test_time."',test_result='".$test_result."',result_doc_name='".$document_name."',entry_date='".$today."',entry_time='".$curr_time."',ip='".$_SERVER['REMOTE_ADDR']."',end_voltage='".$end_voltage."',app_version='".$app_version."',test_decision='".$test_decision."' , physical_condition = '".$phy_condition."' , test_interrupt = '".$test_interrupt."' ,serial_no  = '".$serial_no."'  ";	
				###### Complaint Master Update
			$result_query = mysqli_query($this->link,$query1)or die(mysqli_error($this->link));
######## If Specail Grid DatA post
				if(!empty($special_gr))
				{
					
					for($i=0; $i<count($special_gr) ; $i++)
					{
						$start_time =  $special_gr[$i]->start;
						$end_time =  $special_gr[$i]->end_time;	
					
						 $sql_query = "insert into discharger_result_data set job_no='".$job_no."',type='Special Grid', data1='".$start_time."',data2='".$end_time."',entry_date='".$today."'";
						$result = mysqli_query($this->link,$sql_query);
					}
				}
				
######## If Backup Grid DatA post
				if(!empty($backup))
				{
					
					for($i=0; $i<count($backup) ; $i++)
					{
						$backup_time =  $backup[$i]->backup_time;
						$voltage =  $backup[$i]->volts;	
					
						 $sql_query2 = "insert into discharger_result_data set job_no='".$job_no."',type='Backup', data1='".$backup_time."',data2='".$voltage."',entry_date='".$today."'";
						$result2 = mysqli_query($this->link,$sql_query2);
					}
				}				

			if ($result_query) {
			################
				$b["job_no"] = $job_no;
				$b["status"] = 'Details Updated';
				$b["status_code"]='1000';
			} else {
				if (mysqli_errno() == 1062) {
					// Duplicate key - Primary Key Violation

				$b["job_no"] = $job_no;
				$b["status"] = 'Something Went Wrong';
				$b["status_code"]='1001';
				} else {
					// For other errors
				$b["job_no"] = $job_no;
				$b["status"] = 'Something Went Wrong';
				$b["status_code"]='1002';
				}
			}
				
	} else {
		$b["job_no"] = $job_no;
		$b["status"] = 'Complaint No. Blank';
		$b["status_code"]='1003';

	}
return $b;
}///////////////////////////

###### Function for Physical COndition Details
	public function getPhyDetails() {
		$result = mysqli_query($this->link,"SELECT * FROM discharger_physical_condition where status='A' order by description asc")or die(mysqli_error($this->link));
		if(mysqli_num_rows($result)){
		
		return $result;
		}
	}
	
  ######################## discharger new api ########################################################################
	    public function ValidateNewSerialno() {
		$today = date("Y-m-d");
		
		$result = mysqli_query($this->link,"SELECT * FROM warranty_data  where serial_no='".$_REQUEST['serial_no']."' order by sno desc  ")or die(mysqli_error($this->link));
				if (mysqli_num_rows($result) > 0) {
					return $result;
				}
		}	
	

#### END FUnction		
}