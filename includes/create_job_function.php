<?php

  require_once('dbconnect.php');
switch($_REQUEST["action"]){
	
case "jobimeiValidate":


echo imeiValidate($_REQUEST['imei1'],$link1);

}
#########################################REFURB IMEI DATA##############################################
function getValidateRefurb($imei1,$link1){
	

$w_day=365;
$calltype="Normal";
################################## Check in IMEI Data Refurb(Refurb Data)########################################################

 $str="select * from imei_data_refurb where (imei1='$imei1' or imei2='$imei1')  order by id desc ";
$sql_ref=mysqli_query($link1,$str) or die(mysqli_error($link1));	
$res_ref=mysqli_fetch_array($sql_ref);

///////////////////1. if IMEI is in imei_data(activation)//////////
if(mysqli_num_rows($sql_ref)!=0 ){

////////Check IN warranty status///////////////////////////      
return $res="1"."~"."REFUB"."~".$res_ref['imei1']."~".$res_ref['imei2']."~".$res_ref['model']."~".$res_ref['make']."~".$res_ref['sale_date']."~".$res_ref['warranty']."~".$res_ref['w_days'];
}
/////////////End 1. ///////////////////////////////////////////

else{
///////////////////2. if IMEI is not found in imei_data(activation)/////
return $res="0";
///////////////////End 2. //////////////////////////////////////////////
}
}

###########################################JOB Validation########################################
function getValidateJobSheet($imei1,$sale_date,$link1){

################################## Check in JobSheet Data##########################################################################   
$str="select imei,sec_imei,model,make,dop,con_type,call_type,status from jobsheet_data where imei='$imei1' or sec_imei='$imei1'  order by Id desc ";
$sql_job=mysqli_query($link1,$str) or die("error 2".mysqli_error($link1));
$res_job=mysqli_fetch_array($sql_job);
//////////////////1. IMEI Found in jobsheet data/////////////
if(mysqli_num_rows($sql_job)!=0){
//////////////////Check IMEI Nos OUT Warranty Status /////////////

return $res="1" ."~"."JOBS"."~".$res_job['imei']."~".$res_job['sec_imei']."~".$res_job['model']."~".$res_job['make']."~".$res_job['dop']."~".$res_job['status']."~".$res_job['call_type']."~".$res_job['con_type']."~".$res_job['swap_flag'];



}
else{
	
	return $res="0";
	}

}


###########################################JOB Validation REFUB ########################################
function getValidateJobSheet_refub($imei1,$sale_date,$link1){

################################## Check in JobSheet Data##########################################################################   
$str="select imei,sec_imei,model,make,dop,con_type,call_type,status from jobsheet_data where (imei='$imei1' or sec_imei='$imei1') and open_date>'$sale_date'  order by Id desc ";
$sql_job=mysqli_query($link1,$str) or die("error 3".mysqli_error($link1));
$res_job=mysqli_fetch_array($sql_job);
//////////////////1. IMEI Found in jobsheet data/////////////
if(mysqli_num_rows($sql_job)!=0){
//////////////////Check IMEI Nos OUT Warranty Status /////////////

return $res="1" ."~"."REFUB"."~".$res_job['imei']."~".$res_job['sec_imei']."~".$res_job['model']."~".$res_job['make']."~".$res_job['dop']."~".$res_job['status']."~".$res_job['call_type']."~".$res_job['con_type']."~".$res_job['swap_flag'];   


//echo "-->2";
}
else{
	
	return $res="0";
	}

}
################################## Check in IMEI Data Import(Import Data)########################################################
function getValidateImport($imei1,$link1){


  $str="select * from imei_data_import where (imei1='$imei1' or  imei2='$imei1') order by ID desc";
$sql_imp=mysqli_query($link1,$str) or die("error 4".mysqli_error($link1));
$res_imp=mysqli_fetch_array($sql_imp);

if(mysqli_num_rows($sql_imp)!=0){
  return $res="1"."~"."IMPORT"."~".$res_imp['imei1']."~".$res_imp['imei2']."~".$res_imp['model']."~".$res_imp['make']."~".$res_imp['act_date']."~".$res_imp['w_day'];
}
else{


return $res="0";
}

}



//////////////////////////////Start validity//////////////////////////////////

function imeiValidate($imei,$link1){
	
	
	



$found_refub=getValidateRefurb($imei,$link1);

if($found_refub!="0"){
	//////////////////////////////////////IMEI FOUND IN REFUB///////////////////////////////////////////
	
	$result_refub=explode("~", $found_refub);
	
	
	
	$sale_date=$result_refub[3];

	$found_jobs_ref=getValidateJobSheet_refub($imei,$sale_date,$link1);
	
	if($found_jobs_ref!="0"){
		//echo "5";
	 return $found_jobs_ref."~".$result_refub[1]."~".$result_refub[2];
	}
	else{//echo "4";
		//////////////////////////////////
		 return  $found_refub;
		}
	 
	// echo "3";
	
	}
	else{
	//////////////////Not Found In REfub///////////////////////////////	
$found_jobs=getValidateJobSheet($imei,$sale_date,$link1);
	if($found_jobs!="0"){
		//echo "2";
			 return $found_jobs;
		
		}
	else{
		/////////////////////////////////////// Validate Import///////////////////////////
		//echo "1";
		$found_import=getValidateImport($imei,$link1);
		if($found_import!="0"){
			return $found_import;
			
		}
			else{
				return "0"."~"."IMPORT";
				}
		
		}
		}
}

?>