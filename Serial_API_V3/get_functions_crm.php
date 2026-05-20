<?php
include('constant.php');
class GET_Functions{       
	private $db;
	private $link;
	private $dt_format;
	function __construct() { 
		include_once './config/dbconnect.php';
		$this->db = new DatabaseService();
		$this->link = $this->db->getConnection();
		///////////////////
		$this->dt_format = new DateTime("now", new DateTimeZone('Asia/Calcutta')); //first argument "must" be a string
		$this->dt_format->setTimestamp(time()); //adjust the object to correct timestamp
	}       
	function __destruct() {
	}
	////// date format
	function date_format($dt_sel){
		return substr($dt_sel,8,2)."-".substr($dt_sel,5,2)."-".substr($dt_sel,0,4);
	}
	////// get any details of any table written by shekhar on 29 MAR 2023
	public function getAnyDetails($keyid,$fields,$lookupname,$tbname){
		///// check no. of column
		$chk_keyword = substr_count($fields, ',');
			
		if($chk_keyword > 0){
			$explodee = explode(",",$fields);
			$tb_details = mysqli_fetch_array(mysqli_query($this->link,"SELECT ".$fields." FROM ".$tbname." WHERE ".$lookupname." = '".$keyid."'"));
			$rtn_str = "";
			for($k=0;$k < count($explodee);$k++){
				if($rtn_str==""){
					$rtn_str.= $tb_details[$k];
				}
				else{
					$rtn_str.= "~".$tb_details[$k];
				}
			}
		}
		else{
			$tb_details = mysqli_fetch_array(mysqli_query($this->link,"SELECT ".$fields." FROM ".$tbname." WHERE ".$lookupname." = '".$keyid."'"));
			$rtn_str = $tb_details[$fields];
		}
		return $rtn_str;
	}
	##### Days Difference
	public function daysDifference($endDate, $beginDate){

	$date_parts1=explode("-", $beginDate); $date_parts2=explode("-", $endDate);

	$start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);

	$end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);

	return $end_date - $start_date;

	}
	############
	//// get serial no. info
	public function getValidSerial($serialno,$productid){
		$a = array();
		$today1=date("Y-m-d");
		
		#### Check Serial No. is available in Replacement data
		$sql_repl = "SELECT * FROM replacement_data WHERE replace_serial_no='".$serialno."' and status != '12' ORDER BY id DESC";
		$res_repl = mysqli_query($this->link,$sql_repl);
		$num_repl = mysqli_num_rows($res_repl);
		
		//check serial no in db
		$sql = "SELECT id,imei1,psale_date,model_id,invoice_no,dealer_name FROM imei_data_import WHERE imei1='".$serialno."' ORDER BY id DESC";
		$res = mysqli_query($this->link,$sql);
		$num = mysqli_num_rows($res);
		################
		
		if($num_repl>0){
		$row_repl = mysqli_fetch_array($res_repl);
		
		$partdet_repl = explode("~",$this->getAnyDetails($row_repl['replace_model_id'],"model,modelcode,product_id,brand_id,wp,gp","model_id","model_master"));
		$brandname = $this->getAnyDetails($partdet_repl[3],"brand","brand_id","brand_master");
		$pname = explode("~",$this->getAnyDetails($partdet_repl[2],"product_name,productcode","product_id","product_master"));
		
				$warranty = "";
				$balance_wsdays=0;
				if($today1 < $row_repl["warranty_end_date"]){ 
					$warranty = "IN";
					$balance_wsdays= $this->daysDifference($row_repl["warranty_end_date"],$today1);
				}
				else{
					$warranty = "OUT";
					$balance_wsdays='0';
				}
		
			$a["responseStatus"] = "2";
			$a["responseMessage"] = "Serial Number by Replacement Data";
			$a["responseData"] = array("serial_no"=>$serialno,"warranty_status"=>$warranty,"lastbilltoparty"=>"","model_id"=>$row_repl['replace_model_id'],"modelcode"=>$partdet_repl[1],"model_name"=>$partdet_repl[0],"brand_id"=>$partdet_repl[3],"brand"=>$brandname,"product_id"=>$partdet_repl[2],"product"=>$pname[0],"lastrefno"=>$row["invoice_no"],"lastrefdate"=>$row_repl["dop"],"warranty_days"=>$row_repl["warranty_days"],"gp"=>$row_repl["warranty_days"],"warant_end_date"=>$row_repl["warranty_end_date"],"mfd"=>$row_repl["replace_serial_mfg"],"mfd_ex"=>$row_repl["replace_serial_mfg_ex"],"final_date"=>$row_repl["dop"],"final_date_with_grace"=>$row_repl["warranty_end_date"],"invoice_no"=>"","dealer_name"=>"","additionalinfo"=>"","balance_ws_days"=>$balance_wsdays,"repl_job_no"=>$row_repl['job_no']);	
		}
		#### END Serial No. Replacement Data
		else if($num>0){
		///// if exist
		
			$row = mysqli_fetch_array($res);
			$partdet = explode("~",$this->getAnyDetails($row['model_id'],"model,modelcode,product_id,brand_id,wp,gp","model_id","model_master"));
			//$resp = $this->getValidateSerialPartcode($serialno,$row['prod_code']);
			$brandname = $this->getAnyDetails($partdet[3],"brand","brand_id","brand_master");
			$pname = explode("~",$this->getAnyDetails($partdet[2],"product_name,productcode","product_id","product_master"));
			
			$final_date = "";
			$manufacturing_date = "";
			$mfd_exbsn = "";
			##### Function for BSN Logic
			$serialinfo = $this->checkSerialNoLogic(strtoupper($serialno),$pname[1],$productid);
			//$a["responseData"] = array("serial_no"=>$serialno,"warranty_status"=>"","lastbilltoparty"=>"","model_id"=>$serialinfo['model_id'],"modelcode"=>$serialinfo['model_code'],"model_name"=>$serialinfo['model_name'],"brand_id"=>$serialinfo['brand_id'],"brand"=>$serialinfo['brand'],"product_id"=>$serialinfo['product_id'],"product"=>$serialinfo['product'],"lastrefno"=>"","lastrefdate"=>"0000-00-00","warranty_days"=>$serialinfo['warranty_days'],"gp"=>$serialinfo['gp'],"warant_end_date"=>"0000-00-00","mfd"=>$serialinfo['mfd'],"mfd_ex"=>$serialinfo['mfd_ex'],"final_date"=>"0000-00-00","final_date_with_grace"=>"0000-00-00","invoice_no"=>"","dealer_name"=>"","additionalinfo"=>"");
			
			$manufacturing_date=$serialinfo['mfd'];
			$mfd_exbsn=$serialinfo['mfd_ex'];
			$ws_daysbsn=$serialinfo['warranty_days'];
			##### END BSN Logic
			
			
			$final_date = date("Y-m-d", strtotime($row['psale_date'])); 
			
			
			$warant_end_date = "";
			$warant_end_date = date('Y-m-d', strtotime($final_date. ' + '.$ws_daysbsn.' day'));
	
	
			$final_groce_date = "";
			if($warant_end_date != ""){
				$newDate = strtotime($warant_end_date . '+ '.$partdet['5'].' days');
				$final_groce_date = date('Y-m-d', $newDate);
			}else{
				$final_groce_date = "";
			}
				
				$warranty = "";
				if($today1 < $warant_end_date){ 
					$warranty = "IN";
				}
				else if($today1 < $final_groce_date){ 
					$warranty = "IN";
				}
				else{
					$warranty = "OUT";
				}

			$a["responseStatus"] = "1";
			$a["responseMessage"] = "Success";
			$a["responseData"] = array("serial_no"=>$serialno,"warranty_status"=>$warranty,"lastbilltoparty"=>$row["dealer_name"],"model_id"=>$row["model_id"],"modelcode"=>$partdet[1],"model_name"=>$partdet[0],"brand_id"=>$partdet[3],"brand"=>$brandname,"product_id"=>$partdet[2],"product"=>$pname[0],"lastrefno"=>$row["invoice_no"],"lastrefdate"=>$row["psale_date"],"warranty_days"=>$ws_daysbsn,"gp"=>$partdet[5],"warant_end_date"=>$warant_end_date,"mfd"=>$manufacturing_date,"mfd_ex"=>$mfd_exbsn,"final_date"=>$row["psale_date"],"final_date_with_grace"=>$final_groce_date,"invoice_no"=>$row['invoice_no'],"dealer_name"=>$row['dealer_name'],"additionalinfo"=>$resp,"modelstr"=>$serialinfo['model_dd'],"model_dd_app"=>json_decode($serialinfo['model_dd_app']));
		}else{
			$a["responseStatus"] = "0";
			$a["responseMessage"] = "Serial Number by BSN Logic";
			////get product code
				$prod_cat =$this->getAnyDetails($productid,"productcode","product_id","product_master");
				$a["test"] =$prod_cat;
				//////// check if serial product code and partcode product should be matched (except  product category (2) E-Rickshaw Charger )
				if($prod_cat!=''){
					
					$serialinfo = $this-> checkSerialNoLogic(strtoupper($serialno),$prod_cat,$productid);
					
					$a["responseData"] = array("serial_no"=>$serialno,"warranty_status"=>"","lastbilltoparty"=>"","model_id"=>$serialinfo['model_id'],"modelcode"=>$serialinfo['model_code'],"model_name"=>$serialinfo['model_name'],"brand_id"=>$serialinfo['brand_id'],"brand"=>$serialinfo['brand'],"product_id"=>$serialinfo['product_id'],"product"=>$serialinfo['product'],"lastrefno"=>"","lastrefdate"=>"0000-00-00","warranty_days"=>$serialinfo['warranty_days'],"gp"=>$serialinfo['gp'],"warant_end_date"=>"0000-00-00","mfd"=>$serialinfo['mfd'],"mfd_ex"=>$serialinfo['mfd_ex'],"final_date"=>"0000-00-00","final_date_with_grace"=>"0000-00-00","invoice_no"=>"","dealer_name"=>"","additionalinfo"=>"","modelstr"=>$serialinfo['model_dd'],"model_dd_app"=>json_decode($serialinfo['model_dd_app']));
					////// if serial no. search for all charged battery
					
				}else{
					$a["responseStatus"] = "2";
					$a["responseMessage"] = "Invalid Serial Number";
				}
		}
		return $a;
	}
	///// function to check serial no. logic written by shekhar on 08 feb 2022
	public function checkSerialNoLogic($serialno,$checklogicfor,$product_id){
		//echo $serialno.", ".$checklogicfor.", ".$product_id."<br><br>";
		$a = array();
		############ if we are checking logic for all charged battery serial no.
		if($checklogicfor=="BTR" || $checklogicfor=="EB"){
			$prod_code = substr($serialno,0,3);/////// get product code
			$manuf_date = substr($serialno,3,1);/////// get manufacturing date
			$manuf_month = substr($serialno,4,1);/////// get manufacturing month
			$manuf_year = substr($serialno,5,1);/////// get manufacturing year
			$charging_site = substr($serialno,6,1);/////// get charging site
			$segment = substr($serialno,7,1);/////// get segment
			$brand = substr($serialno,8,1);/////// get brand
			$capacity = substr($serialno,9,1);/////// get AH capacity
			$ws_slab = substr($serialno,10,1);/////// get warranty slab
			$battery_layout = substr($serialno,11,1);/////// get battery layout
			$battery_serial = substr($serialno,12,5);/////// get battery serial no.
			$model_code = $brand.$capacity.$ws_slab.$battery_layout; ///// 4 character model code

			$prod_code_str = substr($serialno,0,3);/////// get first three char
			$model_code_str = substr($serialno,8,4);/////// get model code
			
			///// battery layout
			$bt_layout = $this->getBatteryLayout($battery_layout);
			
			
			///// get model info
			$bt_model = explode("~",$this->getModelNameV2BTR($model_code,$product_id,$prod_code_str));
			
			////// get warranty
			$ws_days = $this->getWarrantySlab($ws_slab);
			///// get manufacturing date
			$bt_mfd = $this->getMfDate($manuf_date)." ".$this->getMfMonth($manuf_month)." ".$this->getMfYear($manuf_year);
			$mfg_dt = date("Y-m-d", strtotime($bt_mfd));
			
			/////////////
			//$ws_days_minus_one = $ws_days-1;
			
			///// Calculate MFD Exp
			$mfd_ex=date('Y-m-d', strtotime($mfg_dt. ' + '.$ws_days.' days'));
			////// get charging site
		//	$bt_chgsite = $this->getChargingSite($charging_site);
			
			
			//$row_model["model_name"]."~".$brandname."~".$pscname."~".$row_model["model_id"]."~".$row_model["brand_id"]."~".$row_model["product_id"]."~".$row_model["wp"]."~".$row_model["gp"];
			
			$a = array("mfd" => $mfg_dt, "warranty_days" => $ws_days, "battery_layout" => $bt_layout, "serial_no" => $battery_serial, "model_id" => $bt_model[4], "brand" => $bt_model[2], "product_id" => $bt_model[6],"model_name"=>$bt_model[0],"brand_id"=>$bt_model[5],"product"=>$bt_model[3],"gp"=>$bt_model[8],"mfd_ex"=>$mfd_ex,"model_code"=>$bt_model[9],"model_dd"=>$bt_model[10],"model_dd_app"=>$bt_model[11]);
		}
		else if($checklogicfor=="LTHIBTR"){ ############ if we are checking logic for Lithium ion battery serial no.
			$prod_code = substr($serialno,0,3);/////// get product code
			$manuf_date = substr($serialno,3,1);/////// get manufacturing date
			$manuf_month = substr($serialno,4,1);/////// get manufacturing month
			$manuf_year = substr($serialno,5,1);/////// get manufacturing year
			$can_softw = substr($serialno,6,1);/////// get CAN software
			$segment = substr($serialno,7,1);/////// get segment
			$brand = substr($serialno,8,1);/////// get brand
			$capacity = substr($serialno,9,1);/////// get AH capacity
			$ws_slab = substr($serialno,10,1);/////// get warranty slab
			$battery_gps = substr($serialno,11,1);/////// get GPS/without GPS
			$battery_serial = substr($serialno,12,5);/////// get battery serial no.
			$model_code = $brand.$capacity.$ws_slab.$battery_gps; ///// 4 character model code

			$prod_code_str = substr($serialno,0,3);/////// get first three char
			$model_code_str = substr($serialno,8,4);/////// get model code
			
			///// battery GPS
			$bt_gps = $this->getGPSInfo($battery_gps);
			///// get model info
			$bt_model = explode("~",$this->getModelNameV2($model_code,$product_id));
			///// get manufacturing date
			$bt_mfd = $this->getMfDate($manuf_date)." ".$this->getMfMonth($manuf_month)." ".$this->getMfYear($manuf_year);
			$mfg_dt = date("Y-m-d", strtotime($bt_mfd));
			
			///// Calculate MFD Exp
			$ws_days=$bt_model[7];
			
			$mfd_ex=date('Y-m-d', strtotime($mfg_dt. ' + '.$ws_days.' days'));
			////// get software info
		//	$bt_sw = $this->getSoftwareName($can_softw);
			////// get warranty
		//	$bt_ws = $this->getWarrantySlabForLithIon($ws_slab);
			/*$a = array("product_code" => $prod_code, "mf_date" => $bt_mfd, "can_software" => $bt_sw, "segment" => $bt_segment, "capacity" => $bt_model[4], "warranty_slab" => $bt_ws, "battery_gps" => $battery_gps, "battery_serial" => $battery_serial, "model_code" => $bt_model[0], "brand" => $bt_model[1], "prod_cat" => $bt_model[2], "prod_subcat" => $bt_model[3]);*/
			$a = array("mfd" => $mfg_dt, "warranty_days" => $bt_model[7], "battery_layout" => "", "serial_no" => $battery_serial, "model_id" => $bt_model[4], "brand" => $bt_model[2], "product_id" => $bt_model[6],"model_name"=>$bt_model[0],"brand_id"=>$bt_model[5],"product"=>$bt_model[3],"gp"=>$bt_model[8],"mfd_ex"=>$mfd_ex,"model_code"=>$bt_model[9],"model_dd"=>$bt_model[10],"model_dd_app"=>$bt_model[11]);
		}
		else if($checklogicfor=="ERBTRCHR"){ ############ if we are checking logic for E-Rickshaw battery charger serial no.
			$production_linecode = substr($serialno,0,1);/////// get production line code
			$engg_chgcode = substr($serialno,1,2);/////// get engineering change code
			$prod_code = substr($serialno,3,2);/////// get product code
			$vendor_code = substr($serialno,5,1);/////// get vendor code
			$ws_slab = substr($serialno,6,1);/////// get warranty slab
			$segment = substr($serialno,7,1);/////// get segment
			$last_2digitpo = substr($serialno,8,2);/////// get last 2 digits of PO
			$manuf_month = substr($serialno,10,1);/////// get manufacturing month
			$manuf_year = substr($serialno,11,2);/////// get manufacturing year
			$charger_serial = substr($serialno,13,4);/////// get charger serial no.
			$model_code = $prod_code.$vendor_code.$ws_slab;/////// get charger model code
			$prod_code_str = substr($serialno,0,3);/////// get first three char
			$model_code_str = substr($serialno,8,4);/////// get model code
			
			///// line code
			$chg_linecode = $this->getProductionLine($production_linecode);
			///// get model info
			//echo $model_code_str.", ".$prod_code_str."<br><br>";
			$bt_model = explode("~",$this->getModelNameV2($model_code,$product_id));
			
			////// get warranty
			$ws_days = $this->getWarrantySlabForERickshawChg($ws_slab);
			///// get manufacturing month year
			$chg_mfd = $this->getMfMonth($manuf_month)." ".$this->getMfYear2($manuf_year);
			$mfg_dt = date("Y-m-01", strtotime($chg_mfd));
			
			///// Calculate MFD Exp
			$mfd_ex=date('Y-m-d', strtotime($mfg_dt. ' + '.$ws_days.' days'));
			////// get vendor info
		//	$chg_vendor = $this->getVendorName($vendor_code);
			
			////// get engineer change note code
		//	$chg_engchgnote = $this->getEngChangeNote($engg_chgcode);
			
		//	$a = array("product_code" => $prod_code, "product_linecode" => $chg_linecode, "mf_date" => $chg_mfd, "vendor_code" => $chg_vendor, "segment" => $chg_segment, "last_2digitpo" => $last_2digitpo, "engg_chgcode" => $engg_chgcode, "warranty_slab" => $chg_ws, "charger_serial" => $charger_serial, "model_code" => $chg_model[0], "brand" => $chg_model[1], "prod_cat" => $chg_model[2], "prod_subcat" => $chg_model[3]);
			
			$a = array("mfd" => $mfg_dt, "warranty_days" => $ws_days, "battery_layout" => "", "serial_no" => $battery_serial, "model_id" => $bt_model[4], "brand" => $bt_model[2], "product_id" => $bt_model[6],"model_name"=>$bt_model[0],"brand_id"=>$bt_model[5],"product"=>$bt_model[3],"gp"=>$bt_model[8],"mfd_ex"=>$mfd_ex,"model_code"=>$bt_model[9],"model_dd"=>$bt_model[10],"model_dd_app"=>$bt_model[11]);
		}
		else if($checklogicfor=="SPL"){ ############ check serial logic for all solar products
			$range_code = substr($serialno,0,3);/////// get product code
			$manuf_date = "";/////// get manufacturing date
			//$manuf_month = substr($serialno,3,1);/////// get manufacturing month
			//$manuf_year = substr($serialno,4,1);/////// get manufacturing year
			$modelc = substr($serialno,3,2);/////// get model code
			$vendor_code = substr($serialno,5,2);/////// get vendor code
			$segment = substr($serialno,7,1);/////// get segment
			$prod_code = substr($serialno,8,2);/////// get last 2 digits of PO
			$ws_slab = substr($serialno,10,1);/////// get warranty slab
			$manuf_month = substr($serialno,10,1);/////// get warranty slab
			$capacity = substr($serialno,11,1);/////// get voltage capacity
			$manuf_year = substr($serialno,11,2);/////// get voltage capacity
			$solar_serial = substr($serialno,13,4);/////// get solar serial no.
			$model_code = $modelc;/////// get solar model code

			$prod_code_str = substr($serialno,0,3);/////// get first three char
			$model_code_str = substr($serialno,8,4);/////// get model code

			///// get model info
			$bt_model = explode("~",$this->getModelNameV2($model_code,$product_id));
		
			//// get vendor name
			$sol_vend = $this->getVendorName($vendor_code);
			////// get warranty
			$sol_ws = $this->getWarrantySlab($ws_slab);
			///// get manufacturing month year
			$spl_mfd = $this->getMfMonth($manuf_month)." ".$this->getMfYear2($manuf_year);
			$mfg_dt = date("Y-m-01", strtotime($spl_mfd));
			
			///// Calculate MFD Exp
			$ws_days=$bt_model[7];
			
			$mfd_ex=date('Y-m-d', strtotime($mfg_dt. ' + '.$ws_days.' days'));
		//	$a = array("range_code" => $range_code, "mf_date" => $sol_mfm." ".$sol_mfy, "vendor_code" => $sol_vend, "segment" => $sol_segment, "product_code" => $sol_model, "voltagerating" => $sol_model[4], "warranty_slab" => $sol_ws, "solar_serial" => $solar_serial, "model_code" => $sol_model[0], "brand" => $sol_model[1], "prod_cat" => $sol_model[2], "prod_subcat" => $sol_model[3]);
		
		$a = array("mfd" => $mfg_dt, "warranty_days" => $bt_model[7], "battery_layout" => "", "serial_no" => $battery_serial, "model_id" => $bt_model[4], "brand" => $bt_model[2], "product_id" => $bt_model[6],"model_name"=>$bt_model[0],"brand_id"=>$bt_model[5],"product"=>$bt_model[3],"gp"=>$bt_model[8],"mfd_ex"=>$mfd_ex,"model_code"=>$bt_model[9],"model_dd"=>$bt_model[10],"model_dd_app"=>$bt_model[11]);
		}
		else if($checklogicfor=="SPU" || $checklogicfor=="SCC"){ ############ check serial logic for solar product electronics
			$range_code = substr($serialno,0,3);/////// get range code/ sub category
			$manuf_date = "";/////// get manufacturing date
			$prod_code = substr($serialno,3,2);/////// get product model code
			$vendor_code = substr($serialno,5,2);/////// get vendor code
			$segment = substr($serialno,7,1);/////// get segment
			$last_2digitpo = substr($serialno,8,2);/////// get last 2 digits of PO
			$manuf_month = substr($serialno,10,1);/////// get manufacturing month
			$manuf_year = substr($serialno,11,2);/////// get manufacturing year
			$solar_serial = substr($serialno,13,4);/////// get solar serial no.

			$prod_code_str = substr($serialno,0,3);/////// get first three char
			$model_code_str = substr($serialno,8,4);/////// get model code
			
			///// get model info
			//$bt_model = explode("~",$this->getModelNameV2($model_code,$product_id));
			$bt_model = explode("~",$this->getModelNameV2($prod_code,$product_id));
			
			//// get vendor name
			$sol_vend = $this->getVendorName($vendor_code);
			
			///// get manufacturing month year
			$mfd = $this->getMfMonth($manuf_month)." ".$this->getMfYear2($manuf_year);
			$mfg_dt = date("Y-m-01", strtotime($mfd));
			
			///// Calculate MFD Exp
			$ws_days=$bt_model[7];
			
			$mfd_ex=date('Y-m-d', strtotime($mfg_dt. ' + '.$ws_days.' days'));
			////// get warranty
			//$sol_ws = getWarrantySlab($serialinfo["warranty_slab"]);
			
		//	$a = array("range_code" => $range_code, "mf_date" => $sol_mfm." ".$sol_mfy, "vendor_code" => $sol_vend, "segment" => $sol_segment, "product_code" => $prod_code, "last_2digitpo" => $last_2digitpo, "solar_serial" => $solar_serial,"model_code" => $sol_model[0], "brand" => $sol_model[1], "prod_cat" => $sol_model[2], "prod_subcat" => $sol_model[3]);
		
		$a = array("mfd" => $mfg_dt, "warranty_days" => $bt_model[7], "battery_layout" => "", "serial_no" => $battery_serial, "model_id" => $bt_model[4], "brand" => $bt_model[2], "product_id" => $bt_model[6],"model_name"=>$bt_model[0],"brand_id"=>$bt_model[5],"product"=>$bt_model[3],"gp"=>$bt_model[8],"mfd_ex"=>$mfd_ex,"model_code"=>$bt_model[9],"model_dd"=>$bt_model[10],"model_dd_app"=>$bt_model[11]);
		}
		else if($checklogicfor=="HUPS"){ ############ check serial logic for HUPS
			$range_code = substr($serialno,0,1);/////// get production line code
			$engg_chgcode = substr($serialno,1,2);/////// get engineering change code
			$manuf_date = substr($serialno,3,1);;/////// get manufacturing date
			$manuf_month = substr($serialno,4,1);/////// get manufacturing month
			$manuf_year = substr($serialno,5,1);/////// get manufacturing year
			$vendor_code = substr($serialno,6,1);/////// get vendor code   1 01 K K 2 V C 2 B 6 H 01263
			$segment = substr($serialno,7,1);/////// get segment
			$brand = substr($serialno,8,1);/////// get brand
			$model_code = substr($serialno,8,4);/////// get model
			$ws_slab = substr($serialno,10,1);/////// get warranty slab
			$type = substr($serialno,11,1);/////// get inverter type
			$solar_serial = substr($serialno,12,5);/////// get solar serial no.

			$prod_code_str = substr($serialno,0,3);/////// get first three char
			$model_code_str = substr($serialno,8,4);/////// get model code

			///// get warranty
			$ws_days= $this->getWarrantySlabForHUPS($ws_slab);
			#### Manufacture Date
			$hups_mfd = $this->getMfDate($manuf_date)." ".$this->getMfMonth($manuf_month)." ".$this->getHupsMfYear($manuf_year);
			$hups_mfdmfg_dt = date("Y-m-d", strtotime($hups_mfd));
			
			$mfd_ex=date('Y-m-d', strtotime($hups_mfdmfg_dt. ' + '.$ws_days.' days'));
			///// get model info
			$bt_model = explode("~",$this->getModelNameV2($model_code,$product_id));
			
			//$a = array("product_linecode" => $range_code, "engg_chgcode" => $engg_chgcode, "mf_date" => $manuf_date, "mf_month" => $manuf_month, "mf_year" => $manuf_year, "vendor_code" => $vendor_code, "segment" => $segment, "brand_code" => $brand, "type" => $type, "warranty_slab" => $ws_slab, "solar_serial" => $solar_serial, "model_code" => $model_code);
			
			$a = array("mfd" => $hups_mfdmfg_dt, "warranty_days" => $ws_days, "battery_layout" => "", "serial_no" => $battery_serial, "model_id" => $bt_model[4], "brand" => $bt_model[2], "product_id" => $bt_model[6],"model_name"=>$bt_model[0],"brand_id"=>$bt_model[5],"product"=>$bt_model[3],"gp"=>$bt_model[8],"mfd_ex"=>$mfd_ex,"model_code"=>$bt_model[9],"model_dd"=>$bt_model[10],"model_dd_app"=>$bt_model[11]);
		}
		else if($checklogicfor=="ERCHR"){ ############ check serial logic for ER Charger NEW (It's like HUPS)
			$range_code = substr($serialno,0,1);/////// get production line code
			$engg_chgcode = substr($serialno,1,2);/////// get engineering change code
			$manuf_date = substr($serialno,3,1);;/////// get manufacturing date
			$manuf_month = substr($serialno,4,1);/////// get manufacturing month
			$manuf_year = substr($serialno,5,1);/////// get manufacturing year
			$vendor_code = substr($serialno,6,1);/////// get vendor code   1 01 K K 2 V C 2 B 6 H 01263
			$segment = substr($serialno,7,1);/////// get segment
			$brand = substr($serialno,8,1);/////// get brand
			$model_code = substr($serialno,9,1);/////// get model
			$ws_slab = substr($serialno,10,1);/////// get warranty slab
			$type = substr($serialno,11,1);/////// get inverter type
			$solar_serial = substr($serialno,12,5);/////// get solar serial no.

			$prod_code_str = substr($serialno,0,3);/////// get first three char
			$model_code_str = substr($serialno,8,4);/////// get model code

			///// get warranty
			$ws_days= $this->getWarrantySlabForHUPS($ws_slab);
			#### Manufacture Date
			$hups_mfd = $this->getMfDate($manuf_date)." ".$this->getMfMonth($manuf_month)." ".$this->getHupsMfYear($manuf_year);
			$hups_mfdmfg_dt = date("Y-m-d", strtotime($hups_mfd));
			
			$mfd_ex=date('Y-m-d', strtotime($hups_mfdmfg_dt. ' + '.$ws_days.' days'));
			///// get model info
			$bt_model = explode("~",$this->getModelNameV2($model_code,$product_id));
			
			//$a = array("product_linecode" => $range_code, "engg_chgcode" => $engg_chgcode, "mf_date" => $manuf_date, "mf_month" => $manuf_month, "mf_year" => $manuf_year, "vendor_code" => $vendor_code, "segment" => $segment, "brand_code" => $brand, "type" => $type, "warranty_slab" => $ws_slab, "solar_serial" => $solar_serial, "model_code" => $model_code);
			
			$a = array("mfd" => $hups_mfdmfg_dt, "warranty_days" => $ws_days, "battery_layout" => "", "serial_no" => $battery_serial, "model_id" => $bt_model[4], "brand" => $bt_model[2], "product_id" => $bt_model[6],"model_name"=>$bt_model[0],"brand_id"=>$bt_model[5],"product"=>$bt_model[3],"gp"=>$bt_model[8],"mfd_ex"=>$mfd_ex,"model_code"=>$bt_model[9],"model_dd"=>$bt_model[10],"model_dd_app"=>$bt_model[11]);
		}
		else if($checklogicfor=="LBC"){ ############ check serial logic for Charger House (LITHIUM BATTERY CHARGER)
			$ws_slab = substr($serialno,0,1);/////// get warranty slab
			$manuf_year = substr($serialno,8,2);/////// get manufacturing year
			$manuf_week = substr($serialno,10,2);/////// get manufacturing week
			$brand = substr($serialno,1,2);/////// get brand

			///// get warranty
			$ws_days= $this->getWarrantySlabForCH($ws_slab);
			#### Manufacture Date
			$get_mfd= $this->getManufacturingDateCH($manuf_week, $manuf_year);
			$mfg_dt_ch = date("Y-m-d", strtotime($get_mfd[0]));
			
			$mfd_ex=date('Y-m-d', strtotime($mfg_dt_ch. ' + '.$ws_days.' days'));
			///// get model info
			$bt_model = explode("~",$this->getModelNameV2_CH($product_id));
			///// get bsn serial
			$battery_serial="";
						
			$a = array("mfd" => $mfg_dt_ch, "warranty_days" => $ws_days, "battery_layout" => "", "serial_no" => $battery_serial, "model_id" => $bt_model[4], "brand" => $bt_model[1], "product_id" => $bt_model[6],"model_name"=>$bt_model[0],"brand_id"=>$bt_model[5],"product"=>$bt_model[3],"gp"=>$bt_model[8],"mfd_ex"=>$mfd_ex,"model_code"=>$bt_model[9],"model_dd"=>$bt_model[10],"model_dd_app"=>$bt_model[11]);	
		}
		else{
		
		}
		return $a;
	}
	////// get exact date from date code written by shekhar on 09 feb 2022
	public function getMfDate($datecode){
		$d = array("1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "6" => "6", "7" => "7", "8" => "8", "9" => "9", "A" => "10", "B" => "11", "C" => "12", "D" => "13", "E" => "14", "F" => "15", "G" => "16", "H" => "17", "I" => "18", "J" => "19", "K" => "20", "L" => "21", "M" => "22", "N" => "23", "O" => "24", "P" => "25", "Q" => "26", "R" => "27", "S" => "28", "T" => "29", "U" => "30", "V" => "31");
		return $d[$datecode];
	}
	////// get exact month from month code written by shekhar on 09 feb 2022
	public function getMfMonth($monthcode){
		$m = array("A" => "Jan", "B" => "Feb", "C" => "Mar", "D" => "Apr", "E" => "May", "F" => "Jun", "G" => "Jul", "H" => "Aug", "I" => "Sep", "J" => "Oct", "K" => "Nov", "L" => "Dec");
		return $m[$monthcode];
	}
	////// get exact year from year code written by shekhar on 09 feb 2022
	public function getMfYear($yearcode){
		$y = array("7" => "2017", "8" => "2018", "9" => "2019", "0" => "2020", "1" => "2021", "2" => "2022", "3" => "2023", "4" => "2024", "5" => "2025", "6" => "2026");
		return $y[$yearcode];
	}
	////// get exact year from year code written by shekhar on 25 AUG 2023
	public function getHupsMfYear($yearcode){
		$y = array("2" => "2022", "3" => "2023", "4" => "2024", "5" => "2025", "6" => "2026", "7" => "2027", "8" => "2028", "9" => "2029", "0" => "2030", "1" => "2031");
		return $y[$yearcode];
	}
	////// get exact battery layout from layout code written by shekhar on 10 feb 2022
	public function getBatteryLayout($layoutcode){
		$ly = array("L" => "Left Hand", "R" => "Right Hand", "S" => "Standard");
		return $ly[$layoutcode];
	}
	////// get exact inverter type written by shekhar on 25 AUG 2023
	public function getInverterType($type){
		$ty = array("H" => "HUPS", "P" => "PCU (Solar)");
		return $ty[$type];
	}
	////// get exact warranty slab from slab code written by shekhar on 10 feb 2022
	public function getWarrantySlab($wslabcode){
		$ws = array("0" => "183", "1" => "365", "2" => "457", "3" => "547", "4" => "730", "5" => "914", "6" => "1095", "7" => "1278", "8" => "1460", "9" => "1825", "N" => "0", "W" => "0", "A" => "638", "B" => "225", "C" => "1521", "D" => "241", "E" => "200", "F" => "250", "G" => "2190", "H" => "3285", "I" => "2737", "J" => "1187", "P" => "1825", "X" => "3650", "Y" => "272", "Z" => "300");
		return $ws[$wslabcode];
	}
	/////// get charging site name from charging site code written by shekhar on 09 feb 2022
	/*public function getChargingSite($chargingsitecode){
		$res_chgsite = mysqli_query($this->link,"SELECT site_name FROM charging_site_master WHERE site_code='".$chargingsitecode."'");
		$row_chgsite = mysqli_fetch_assoc($res_chgsite);
		return $row_chgsite["site_name"];
	}*/
	////// get brand name from brand id written by shekhar on 10 feb 2022
	public function getBrandName($brandid){
		 $str .= " brand_id = '".$brandid."'";
		$res_brand = mysqli_query($this->link,"SELECT brand FROM brand_master WHERE ".$str);
		$row_brand = mysqli_fetch_assoc($res_brand);
		/////// brand
		return $row_brand["brand"];
	}
	////// get brand name from brand id written by shekhar on 25 AUG 2023
	public function getHupsBrandName($brandcode){
		$brand = array("1" => "Eastman", "2" => "Addo");
		return $brand[$brandcode];
	}
	////// get product cat name from psc id written by shekhar on 10 feb 2022
	public function getPSCName($pscid){
		$res_psc = mysqli_query($this->link,"SELECT product_id,product_name FROM product_master WHERE product_id = '".$pscid."'");
		$row_psc = mysqli_fetch_assoc($res_psc);
		/////// product sub cat
			return $row_psc["product_id"]."~".$row_psc["product_name"];
		
	}
	/////// get model name from model code written by shekhar on 09 feb 2022
	public function getModelName($modelcode,$checklogicfor){
		$res_model = mysqli_query($this->link,"SELECT model,product_id,brand_id,model_id,wp,gp,modelcode FROM model_master WHERE (model_code='".$modelcode."' OR model_code2 LIKE '".$modelcode.",%' OR model_code2 LIKE '%".$modelcode."' OR model_code2 LIKE '%,".$modelcode.",%') and product_id='".$checklogicfor."'");
		$row_model = mysqli_fetch_assoc($res_model);
			/////// get brand name
			$brandname = $this->getBrandName($row_model["brand_id"]);
			////// get product sub cat
			$pscname = $this->getPSCName($row_model["product_id"]);
			return $row_model["model"]."~".$brandname."~".$pscname."~".$row_model["model_id"]."~".$row_model["brand_id"]."~".$row_model["product_id"]."~".$row_model["wp"]."~".$row_model["gp"]."~".$row_model["modelcode"];
	}
	/////// get model name from model code written by shekhar on 09 feb 2022
	public function getModelNameV2($modelcode,$checklogicfor){
		$res_model = mysqli_query($this->link,"SELECT model,product_id,brand_id,model_id,wp,gp,modelcode FROM model_master WHERE (model_code='".$modelcode."' OR model_code2 LIKE '".$modelcode.",%' OR model_code2 LIKE '%".$modelcode."' OR model_code2 LIKE '%,".$modelcode.",%') and product_id='".$checklogicfor."' "); ///and product_id='".$checklogicfor."'
		$mod_str1 = "";
		$mod_str = "";
		
		$aaa = [];
		while($mod_row=mysqli_fetch_array($res_model)){
			$aaa[$mod_row["model_id"]] = $mod_row["model"];
			$mod_str1 = $mod_str1.$mod_row['model_id']."','";
		}
		$mod_str = rtrim($mod_str1,"','");

		$res_model1 = mysqli_query($this->link,"SELECT model,product_id,brand_id,model_id,wp,gp,modelcode FROM model_master WHERE (model_code='".$modelcode."' OR model_code2 LIKE '".$modelcode.",%' OR model_code2 LIKE '%".$modelcode."' OR model_code2 LIKE '%,".$modelcode.",%') and product_id='".$checklogicfor."' ");
		$row_model = "";
		$row_model = mysqli_fetch_assoc($res_model1);
		
		/////// get brand name
		$brandname = $this->getBrandName($row_model["brand_id"]);
		////// get product sub cat
		$pscname = $this->getPSCName($row_model["product_id"]);
		return $row_model["model"]."~".$brandname."~".$pscname."~".$row_model["model_id"]."~".$row_model["brand_id"]."~".$row_model["product_id"]."~".$row_model["wp"]."~".$row_model["gp"]."~".$row_model["modelcode"]."~".$mod_str."~".json_encode($aaa);
	}
	/////// get model name from model code written by shekhar on 09 feb 2022
	public function getModelNameV2BTR($modelcode,$checklogicfor,$modstr){
		$addon_str = "";
		if($checklogicfor=="1" || $checklogicfor=="4"){
			$addon_str = " and (productcode='".$modstr."' OR product_code2 LIKE '".$modstr.",%' OR product_code2 LIKE '%".$modstr."' OR product_code2 LIKE '%,".$modstr.",%') ";
		}else{
			$addon_str = " ";
		}
		$res_model = mysqli_query($this->link,"SELECT model,product_id,brand_id,model_id,wp,gp,modelcode FROM model_master WHERE (model_code='".$modelcode."' OR model_code2 LIKE '".$modelcode.",%' OR model_code2 LIKE '%".$modelcode."' OR model_code2 LIKE '%,".$modelcode.",%') and product_id='".$checklogicfor."' ".$addon_str." "); ///and product_id='".$checklogicfor."'
		$mod_str1 = "";
		$mod_str = "";
		
		$aaa = [];
		while($mod_row=mysqli_fetch_array($res_model)){
			$aaa[$mod_row["model_id"]] = $mod_row["model"];
			$mod_str1 = $mod_str1.$mod_row['model_id']."','";
		}
		$mod_str = rtrim($mod_str1,"','");

		$res_model1 = mysqli_query($this->link,"SELECT model,product_id,brand_id,model_id,wp,gp,modelcode FROM model_master WHERE (model_code='".$modelcode."' OR model_code2 LIKE '".$modelcode.",%' OR model_code2 LIKE '%".$modelcode."' OR model_code2 LIKE '%,".$modelcode.",%') and product_id='".$checklogicfor."' ".$addon_str." ");
		$row_model = "";
		$row_model = mysqli_fetch_assoc($res_model1);

		/////// get brand name
		$brandname = $this->getBrandName($row_model["brand_id"]);
		////// get product sub cat
		$pscname = $this->getPSCName($row_model["product_id"]);
		return $row_model["model"]."~".$brandname."~".$pscname."~".$row_model["model_id"]."~".$row_model["brand_id"]."~".$row_model["product_id"]."~".$row_model["wp"]."~".$row_model["gp"]."~".$row_model["modelcode"]."~".$mod_str."~".json_encode($aaa);
	}
	////// get vendor name from vendor code written by shekhar on 10 feb 2022
	public function getVendorName($vendorcode){
		$vend = array("A1" => "Fujiyama Power", "B1" => "Advance Electronics", "C1" => "Kstar", "D1" => "Premier", "E1" => "Intelizon", "F1" => "Eastman", "G1" => "Insolation", "V" => "VOLTSMAN POWER TECHNOTOGIES PRIVATE TIMITED");
		return $vend[$vendorcode];
	}
	////// get software name from s/w code written by shekhar on 07 mar 2022
	public function getSoftwareName($swcode){
		$sw = array("C" => "CAN");
		return $sw[$swcode];
	}
	////// get GPS details written by shekhar on 07 mar 2022
	public function getGPSInfo($gpscode){
		$gps = array("W" => "Without GPS", "G" => "GPS");
		return $gps[$gpscode];
	}
	////// get exact warranty slab from slab code for lithium ion battery written by shekhar on 07 mar 2022
	public function getWarrantySlabForLithIon($wslabcode){
		$ws = array("0" => "365", "1" => "730", "2" => "1095", "3" => "1278", "4" => "1460", "5" => "1825");
		return $ws[$wslabcode];
	}
	////// get production line from line code written by shekhar on 07 mar 2022
	public function getProductionLine($linecode){
		$prodline = array("A" => "Production Line - 1", "B" => "Production Line - 2", "C" => "Production Line - 3", "D" => "Production Line - 4", "E" => "Production Line - 5", "F" => "Production Line - 6", "G" => "Production Line - 7", "H" => "Production Line - 8", "I" => "Production Line - 9", "J" => "Production Line - 10", "K" => "Production Line - 11", "L" => "Production Line - 12", "M" => "Production Line - 13", "N" => "Production Line - 14", "O" => "Production Line - 15", "P" => "Production Line - 16", "Q" => "Production Line - 17", "R" => "Production Line - 18", "S" => "Production Line - 19", "T" => "Production Line - 20", "U" => "Production Line - 21", "V" => "Production Line - 22", "W" => "Production Line - 23", "X" => "Production Line - 24", "Y" => "Production Line - 25", "Z" => "Production Line - 26", "1" => "Production Line - 1", "2" => "Production Line - 2", "3" => "Production Line - 3", "4" => "Production Line - 4");//// edited on 25 aug 23 by shekhar
		return $prodline[$linecode];
	}
	////// get engineer change note from engineer change note code written by shekhar on 07 mar 2022
	public function getEngChangeNote($engchgcode){
		$engchgnote = array("AA" => "BOM Change Code Note - 1", "AB" => "BOM Change Code Note - 2", "AC" => "BOM Change Code Note - 3", "AD" => "BOM Change Code Note - 4", "AE" => "BOM Change Code Note - 5", "AF" => "BOM Change Code Note - 6", "AG" => "BOM Change Code Note - 7", "AH" => "BOM Change Code Note - 8", "AI" => "BOM Change Code Note - 9", "AJ" => "BOM Change Code Note - 10", "AK" => "BOM Change Code Note - 11", "AL" => "BOM Change Code Note - 12", "AM" => "BOM Change Code Note - 13", "AN" => "BOM Change Code Note - 14", "AO" => "BOM Change Code Note - 15", "AP" => "BOM Change Code Note - 16", "AQ" => "BOM Change Code Note - 17", "AR" => "BOM Change Code Note - 18", "AS" => "BOM Change Code Note - 19", "AT" => "BOM Change Code Note - 20", "AU" => "BOM Change Code Note - 21", "AV" => "BOM Change Code Note - 22", "AW" => "BOM Change Code Note - 23", "AX" => "BOM Change Code Note - 24", "AY" => "BOM Change Code Note - 25", "AZ" => "BOM Change Code Note - 26", "01" => "BOM Change Code Note - 1", "02" => "BOM Change Code Note - 2", "03" => "BOM Change Code Note - 3", "04" => "BOM Change Code Note - 4", "05" => "BOM Change Code Note - 5", "06" => "BOM Change Code Note - 6", "07" => "BOM Change Code Note - 7");//// edited on 25 aug 23 by shekhar
		return $engchgnote[$engchgcode];
	}
	////// get exact warranty slab from slab code for E-Rickshaw battery charger written by shekhar on 07 mar 2022
	public function getWarrantySlabForERickshawChg($wslabcode){
		$ws = array("0" => "183", "1" => "365", "2" => "547", "3" => "730", "4" => "914", "5" => "1095");
		return $ws[$wslabcode];
	}
	////// get exact year from year code for E-Rickshaw battery charger and solar products electronics written by shekhar on 07 mar 2022
	public function getMfYear2($yearcode){
		$y = array("17" => "2017", "18" => "2018", "19" => "2019", "20" => "2020", "21" => "2021", "22" => "2022", "23" => "2023", "24" => "2024", "25" => "2025", "26" => "2026", "27" => "2027", "28" => "2028", "29" => "2029", "30" => "2030");
		return $y[$yearcode];
	}
	////// get exact warranty slab from slab code for HUPS
	public function getWarrantySlabForHUPS($wslabcode){
		$ws = array("0" => "183", "1" => "365", "2" => "457", "3" => "547", "4" => "730", "5" => "914", "6" => "1095", "7" => "1278", "8" => "1460", "9" => "1825");
		return $ws[$wslabcode];
	}
	////// get exact Manufacturing date for Charge house
	public function getManufacturingDateCH($week, $year){
		$time = strtotime("1 January $year", time()); // Getting the timestamp for January 1st of the given year.
		$day = date('w', $time); // Getting the numeric representation of the day of the week for January 1st.
		$time += ((7*$week)+1-$day)*24*3600; // Calculating the timestamp for the starting day of the given week.
		$dates[0] = date('Y-n-j', $time); // Formatting the starting date of the week.
		$time += 6*24*3600; // Adding six days to get the timestamp for the end of the week.
		$dates[1] = date('Y-n-j', $time); // Formatting the end date of the week.
		return $dates; // Returning the array containing the starting and end dates of the week.
	}

	////// get brand name from brand id written by shekhar on 10 feb 2022
	public function getBrandName_CH($brandid){
		$res_brand = mysqli_query($this->link,"SELECT brand FROM brand_master WHERE brand_id = '".$brandid."' ");
		$row_brand = mysqli_fetch_array($res_brand);
		/////// brand
		return $row_brand['brand'];
	}

	////// get exact warranty slab from slab code for HUPS
	public function getWarrantySlabForCH($wslabcode){
		$ws = array("0" => "0", "1" => "365", "2" => "730", "3" => "1095", "4" => "1460", "5" => "1825");
		return $ws[$wslabcode];
	}

	/////// get model name from model code written by shekhar on 09 feb 2022
	public function getModelNameV2_CH($product_id){
		$res_model = mysqli_query($this->link,"SELECT model,product_id,brand_id,model_id,wp,gp,modelcode FROM model_master WHERE model_id = 'M0977' and product_id='".$product_id."' "); ///and product_id='".$checklogicfor."'
		$mod_str1 = "";
		$mod_str = "";
		
		$aaa = [];
		while($mod_row=mysqli_fetch_array($res_model)){
			$aaa[$mod_row["model_id"]] = $mod_row["model"];
			$mod_str1 = $mod_str1.$mod_row['model_id']."','";
		}
		$mod_str = rtrim($mod_str1,"','");

		$res_model1 = mysqli_query($this->link,"SELECT model,product_id,brand_id,model_id,wp,gp,modelcode FROM model_master WHERE model_id = 'M0977' and product_id='".$product_id."' ");
		//$row_model = "";
		$row_model = mysqli_fetch_assoc($res_model1);

		/////// get brand name
		$brandname = $this->getBrandName_CH($row_model["brand_id"]);
		////// get product sub cat
		$pscname = $this->getPSCName($row_model["product_id"]);
		return $row_model["model"]."~".$brandname."~".$pscname."~".$row_model["model_id"]."~".$row_model["brand_id"]."~".$row_model["product_id"]."~".$row_model["wp"]."~".$row_model["gp"]."~".$row_model["modelcode"]."~".$mod_str."~".json_encode($aaa);
	}

}