<?php 
include_once 'db_functions.php';     
$db = new DB_Functions();
$eng_id=$_REQUEST['eid'];
//Util arrays to create response JSON 
$a = array();
$b = array();
$c = array();
$arr_tab = array();
$final_array = array();
$flag = 1;
if($eng_id!=''){
		
		$res_tab = $db-> getTabRights($eng_id);
		while($row_set = mysqli_fetch_assoc($res_tab)){
			$arr_tab[$row_set['tabid']] = $row_set['status'];
		}
		////// pick main tab name
		$res_tabn = $db-> getMainTab();
		while($row_tabn = mysqli_fetch_array($res_tabn)){
			////// pick sub tab name
			$res_subtabn = $db-> getSubTab($row_tabn['maintabname']);
			while($row_subtabn = mysqli_fetch_array($res_subtabn)){
				///if($arr_tab[$row_subtabn['tabid']] == 1 && $row_subtabn['app_filename']!=""){
				if($arr_tab[$row_subtabn['tabid']] == 1){
					$c["mainTab"] = $row_tabn['maintabname'];
					$c["subTabId"] = $row_subtabn['tabid'];
					$c["subTabName"] = $row_subtabn['subtabname'];
					$c["subTabFile"] = $row_subtabn['app_filename'];
					array_push($b,$c);
					$final_array["option_list"] = $b;
				}
				
			}
			
		}
		$final_array["userId"] = $eng_id;
		$flag *= 1;
	
	array_push($a,$final_array);
}
echo json_encode($final_array);
?>