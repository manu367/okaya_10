<?php

require_once("../includes/config.php");



#########--------- For Checking Abl Qty----------###########

function chk_abl($partcode,$w_code,$field,$link1){

$row=mysqli_fetch_array(mysqli_query($link1,"select $field from client_inventory where partcode='".$partcode."' and location_code='".$w_code."'"));

return $row[0];

}

############################################################

///// After Hitting Process Buton

if($_POST['save']=='Process!') {

if($_POST['location_code'] != ''){

	

  ///// Check select partcode is not blank

  if(is_array($_POST['partcode'])){

	////// INITIALIZE PARAMETER/////////////////////////

   	mysqli_autocommit($link1, false);

	$flag = true;

	$error_msg = "";

	  

	//// Make System document//////

    $row_so=mysqli_fetch_array(mysqli_query($link1,"select max(temp_no) as no from stock_adjust_master where location_code='".$_POST['location_code']."' "));

    $c_nos=$row_so['no']+1;

    $so_no=$_POST['location_code']."SA".$c_nos;



    //// insert Stock Adjust entry in master table

  $sql_so_master= "INSERT INTO stock_adjust_master set location_code='".$_POST['location_code']."',system_ref_no='".$so_no."',temp_no='".$c_nos."',adjust_date='".$today."',entry_date='".$today."',entry_time='".$c_time."',status='PROCESSED',entry_by='".$_SESSION['userid']."',entry_ip='".$_SERVER['REMOTE_ADDR']."',entry_rmk='".$_POST['remark']."' , type = 'admin adjust' ";

  $sql=  mysqli_query($link1,$sql_so_master)or die("Error1".mysqli_error());

  //// check if query is not executed

	if (!$sql) {

		$flag = false;

       	$error_msg = "Error details1: " . mysqli_error($link1) . ".";

    }

    ////// insert in Stock Adjust data

	$partcode=$_POST['partcode'];

	$ok_type=$_POST['ok_type'];

	$ok_qty=$_POST['ok_qty'];

	$damage_type=$_POST['damage_type'];

	$damage_qty=$_POST['damage_qty'];

	$missing_type=$_POST['missing_type'];

	$missing_qty=$_POST['missing_qty'];

	$len=count($_POST['partcode']);

	////// Check at least one entry is selected

    if($len>0){

       ///////// read all array data

       for($i=0;$i<$len;$i++){

		  $item=$partcode[$i];

		  $okType=$ok_type[$i];

		  $okQty=$ok_qty[$i];

		  $damType=$damage_type[$i];

		  $damQty=$damage_qty[$i];

		  $missType=$missing_type[$i];

		  $missQty=$missing_qty[$i];

		  

		  ///////////////   get price of partcode /////////////////////////////////

		  $price=getAnyDetails($item,"location_price","partcode","partcode_master",$link1);

		  

		  

          ///// check part code is not blank

          if($item!=""){

			 //// For OK QTY 

             if($okType == "P"){

			    $ok_str="okqty=okqty+'$okQty'";

				$okstktype="IN";
				
				

			 }elseif($okType == "M"){ 

				//// check available stock if we are doing stock adjust minus because stock can go to negative (in case Minus(-) only)

			    $abl_ok_qty=chk_abl($item,$_POST['location_code'],"okqty",$link1);

				if($abl_ok_qty < $okQty){ $ok_str="";}else{ $ok_str="okqty=okqty-'$okQty'";}

				 $okstktype="OUT";

			 }

			

			 else{ 

			    $ok_str="";

				$okstktype="";

			 }

		

	

			 

			 //// For DAMAGE QTY 

			 if($damType == "P"){ 

			    $damg_str="broken=broken+'$damQty'";

				$damgstktype="IN";

			 }elseif($damType == "M"){ 

				//// check available stock if we are doing stock adjust minus because stock can go to negative (in case Minus(-) only)

			    $abl_damg_qty=chk_abl($item,$_POST['location_code'],"broken",$link1);

				if($abl_damg_qty < $damQty){ $damg_str="";}else{ $damg_str="broken=broken-'$damQty'";}

				$damgstktype="OUT";

			 }else{ 

			    $damg_str="";

				$damgstktype="";

			 }

			 

			

			 

			 //// For MISSING QTY 

			 if($missType == "P"){ 

			    $miss_str="missing=missing+'$missQty'";

				$missstktype="IN";

			 }elseif($missType == "M"){ 

				//// check available stock if we are doing stock adjust minus because stock can go to negative (in case Minus(-) only)

			    $abl_miss_qty=chk_abl($item,$_POST['location_code'],"missing",$link1);

				if($abl_miss_qty < $missQty){ $miss_str="";}else{ $miss_str="missing=missing-'$missQty'";}

				$missstktype="OUT";

			 }else{ 

			    $miss_str="";

				$missstktype="";

			 }

             

			 		 //// assgin flag for stock ledger

			 $okflag=0; $damageflag=0; $missflag=0;

			 

			 if($ok_str == "" && $damg_str == "" && $miss_str == "" ){                                              //// 0 0 0

				 

			 }else{

				 if($ok_str == "" && $damg_str == "" && $miss_str != ""){ $qry_str =  $miss_str;$missflag+=1;}                  //// 0 0 1

				 elseif($ok_str == "" && $damg_str != "" && $miss_str == ""){ $qry_str =  $damg_str;$damageflag+=1;}              //// 0 1 0

				 elseif($ok_str == "" && $damg_str != "" && $miss_str != ""){ $qry_str =  $damg_str.",".$miss_str;$missflag+=1;$damageflag+=1;}//// 1 0 0

				 elseif($ok_str != "" && $damg_str == "" && $miss_str == ""){ $qry_str =  $ok_str;$okflag+=1;}                //// 1 0 0

				 elseif($ok_str != "" && $damg_str == "" && $miss_str != ""){ $qry_str =  $ok_str.",".$miss_str;$okflag+=1;$missflag+=1;}  //// 1 0 1

				 elseif($ok_str != "" && $damg_str != "" && $miss_str == ""){ $qry_str =  $ok_str.",".$damg_str;$okflag+=1;$damageflag+=1;}  //// 1 1 0

				 else{ $qry_str =  $ok_str.",".$damg_str.",".$miss_str; $okflag+=1; $damageflag+=1; $missflag+=1;}                                           //// 1 1 1

				

                //// insert in data table

		$stock_data	= mysqli_query($link1,"insert into stock_adjust_data set system_ref_no='".$so_no."',partcode='".$item."' ,adj_ok_type='".$okType."',adj_ok_qty='".$okQty."',adj_damg_type='".$damType."',adj_damg_qty='".$damQty."',adj_miss_type='".$missType."',adj_miss_qty='".$missQty."' ,entry_by='".$_SESSION['userid']."' , entry_date = '".$today."' , type = 'admin adjust'  ");

			 

			 /// check if query is not executed

			if (!$stock_data) {

			$flag = false;

       		$error_msg = "Error details2: " . mysqli_error($link1) . ".";

   				 }
				 
				 	$dptid = mysqli_insert_id($link1); 
 $stock_price=getAnyDetails($item,"location_price","partcode" ,"partcode_master",$link1);
            if($okType == "P" && $okQty>0 ){

			 		 $res_fifo=mysqli_query($link1,"insert into  fifo_list set location_code='".$_POST['location_code']."',partcode='".$item."',challan_no='".$so_no."',pty_receive_date='".$today."',okqty='".$okQty."',type='Adjust',ref_sno='".$dptid."',price='".$stock_price."',sale_date='".$today."',document_type='Adjust' ");
		
		   //// check if query is not executed
		   if (!$res_fifo) {
	           $flag = false;
               $error_msg = "Error details Fifo: " . mysqli_error($link1) . ".";
           }
				
				

			 }

				///// update  client inventory ///////////////////////////////

				if(mysqli_num_rows(mysqli_query($link1,"select id from client_inventory where location_code='".$_POST['location_code']."' and partcode='".$item."' "))>0){

				$upd =  mysqli_query($link1,"update client_inventory set $qry_str where location_code='".$_POST['location_code']."' and partcode='".$item."' ");

					 /// check if query is not executed

					if (!$upd) {

						$flag = false;

       					$error_msg = "Error details3: " . mysqli_error($link1) . ".";

   				 }

					

				}else{

			

			$upd =mysqli_query($link1,"insert into client_inventory set $qry_str , location_code='".$_POST['location_code']."', partcode='".$item."' ");

					/// check if query is not executed

					if (!$upd) {

						$flag = false;

       					$error_msg = "Error details4: " . mysqli_error($link1) . ".";

   				 }

				}

			 }//close if

		  }//close if

	

		  	if($okflag>0){

		    /////////// insert entry into stock ledger//////////////////////////////////////

	$flag=stockLedger($so_no,$today,$item,$_POST['location_code'],$_POST['location_code'],$okstktype,"OK","Admin Stock Adjustment","Admin Stock Adjustment",$okQty,$price,$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);

			}

			if($damageflag>0){

		    /////////// insert entry into stock ledger//////////////////////////////////////

	$flag=stockLedger($so_no,$today,$item,$_POST['location_code'],$_POST['location_code'],$damgstktype,"DAMAGE"," Admin Stock Adjustment","Admin Stock Adjustment",$damQty,$price,$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);

			}

			if($missflag>0){

		    /////////// insert entry into stock ledger//////////////////////////////////////

	$flag=stockLedger($so_no,$today,$item,$_POST['location_code'],$_POST['location_code'],$missstktype,"MISSING","Admin Stock Adjustment","Admin Stock Adjustment",$missQty,$price,$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);

			}

 

	   }//close for		

	}	//if	

  }// partcode condition

  

     ////// insert in activity table////

    $flag = dailyActivity($_SESSION['userid'], $so_no, "Admin Stock Adjustment", "ADD", $ip, $link1, $flag);

	

///// check both master and data query are successfully executed

	if ($flag) {

        mysqli_commit($link1);

		$cflag = "success";

		$cmsg = "Success";

        $msg = "Stock Adjustment  is successfully placed with ref. no. ".$so_no;

    } else {

		mysqli_rollback($link1);

		$cflag = "danger";

		$cmsg = "Failed";

		$msg = "Request could not be processed. Please try again." .$error_msg ;

	} 

    mysqli_close($link1);

	///// move to parent pageadminstock_adjustment_admin.php

   header("location:adminstock_adjustment_admin.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);

 	exit;

	}

	else {

		$cflag = "danger";

		$cmsg = "Failed";

		$msg = "Request could not be processed.Location is not Selected." .$error_msg ;

		///// move to parent pageadminstock_adjustment_admin.php

   header("location:adminstock_adjustment_admin.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);

 	exit;

	

	}

	

}

?>