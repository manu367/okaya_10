<?php
require_once("dbconnect.php");
require_once("common_function.php");
require_once("globalvariables.php");
session_start();
//////////////////  Get state by selecting country dropdown

if($_POST['cntryid']){



     echo "<select name='locationstate' id='locationstate' class='form-control required' onchange='get_citydiv();get_distdiv();' required><option value=''>--Please Select--</option>";
     $state_query="select stateid, state from state_master where countryid='".$_POST['cntryid']."' order by state";



     $state_res=mysqli_query($link1,$state_query);



     while($row_res = mysqli_fetch_array($state_res)){



           echo "<option value='".$row_res['stateid']."'>";



           echo $row_res['state']."</option>";



	 }



     echo "</select>";



}



//////////////////  Get city by selecting state	dropdown



if($_POST['state']){



     echo "<select  name='locationcity' id='locationcity' class='form-control required' required><option value=''>--Please Select--</option>";



     $city_query="SELECT cityid, city FROM city_master where stateid='".$_POST['state']."' group by city order by city";



     $city_res=mysqli_query($link1,$city_query);



     while($row_city = mysqli_fetch_array($city_res)){



           echo "<option value='".$row_city['cityid']."'>";



           echo $row_city['city']."</option>";



	 }



           echo "<option value='Others'>Others</option>";



     echo "</select>";



}



if($_POST['oterstate']){

     echo "<select  name='otherlocationcity' id='otherlocationcity' class='form-control required' required><option value=''>--Please Select--</option>";



     $city_query="SELECT cityid, city FROM city_master where stateid='".$_POST['state']."' group by city order by city";



     $city_res=mysqli_query($link1,$city_query);



     while($row_city = mysqli_fetch_array($city_res)){



           echo "<option value='".$row_city['cityid']."'>";



           echo $row_city['city']."</option>";



	 }



           echo "<option value='Others'>Others</option>";



     echo "</select>";



}


//////////////////  Get model on basis of product and brand dropdown///// develope by vikas



if($_POST['modelinfostn']){



$indx  =$_POST['indxx'];

 $stocktype  = $_POST['stktype'];
  if($stocktype==''){$stocktype="okqty";}


     echo "<select  name='partcode[$indx]' id='partcode[$indx]' class='form-control selectpicker required selectpicker' data-live-search='true'  onChange='getAvlStk($indx); checkDuplicate($indx,this.value);'><option value='' >Please Select Partcode</option>";



 $acc_query="select partcode, part_name, part_category,vendor_partcode,brand_id from partcode_master where (model_id Like '%".$_POST['modelinfostn']."%' OR part_category='GLOBAL') and status='1' and partcode in ( select partcode from client_inventory where location_code='".$_SESSION['asc_code']."' and ".$stocktype." > 0) group by partcode order by part_name";



     $acc_res=mysqli_query($link1,$acc_query);



     while($row_acc = mysqli_fetch_array($acc_res)){



           echo "<option data-tokens='".$row_acc['part_name']."|".$row_acc['partcode']."' value='".$row_acc['partcode']."'>";



           echo $row_acc['part_name']."(". $row_acc['vendor_partcode'].") (". $row_acc['partcode'].") (".$row_acc['part_category'].")</option>";



	 }



    echo "</select>~".$_POST['indxx'];







}


/////////////////////////////////////////////////////////////user stock partcode////////////////////////////////

if($_POST['modelinfostneng']){

$indx  =$_POST['indxx'];
$stocktype  = $_POST['stocktype'];
  if($stocktype==''){$stocktype="okqty";}


     echo "<select  name='partcode[$indx]' id='partcode[$indx]' class='form-control required selectpicker' data-live-search='true' onChange='getAvlStk($indx);  checkDuplicate($indx,this.value);'><option value='' >Please Select Partcode</option>";



 $acc_query="select partcode, part_name, part_category,vendor_partcode,brand_id from partcode_master where (model_id Like '%".$_POST['modelinfostneng']."%' OR part_category='GLOBAL') and status='1' and partcode in( select partcode from user_inventory where locationuser_code ='".$_POST['usercode']."' and ".$stocktype." > 0) group by partcode order by part_name";



     $acc_res=mysqli_query($link1,$acc_query);



     while($row_acc = mysqli_fetch_array($acc_res)){



           echo "<option data-tokens='".$row_acc['part_name']."|".$row_acc['partcode']."' value='".$row_acc['partcode']."'>";



           echo $row_acc['part_name']."(". $row_acc['vendor_partcode'].") (". $row_acc['partcode'].") (".$row_acc['part_category'].")</option>";



	 }



    echo "</select>~".$_POST['indxx'];







}




//////////////////  Get district by selecting state	dropdown



if($_POST['state2']){



     echo "<select  name='locationdistrict' id='locationdistrict' class='form-control required' required><option value=''>--Please Select--</option>";



     $city_query="SELECT cityid, city FROM city_master where stateid='".$_POST['state2']."' and isdistrict='Y' group by city order by city";



     $city_res=mysqli_query($link1,$city_query);



     while($row_city = mysqli_fetch_array($city_res)){



           echo "<option value='".$row_city['cityid']."'>";



           echo $row_city['city']."</option>";



	 }



           echo "<option value='Others'>Others</option>";



     echo "</select>";



}

/////////////   get available stock and part description for stock adjustment////////////////////
 
 if($_POST['avlstock']){
 
   $stk_query="SELECT okqty, broken, missing FROM client_inventory where  partcode='".$_POST['avlstock']."' and location_code='".$_POST['location']."'";

     $stk_res=mysqli_query($link1,$stk_query);

	 $stk_row = mysqli_fetch_array($stk_res);
	 
	 $sql = mysqli_fetch_array(mysqli_query($link1,"select part_desc from partcode_master where partcode = '".$_POST['avlstock']."' "));

	 echo $stk_row[0]."~".$stk_row[1]."~".$stk_row[2]."~".$sql[0]."~".$_POST['indxx'];

}

//////////////////  Get Parent Location by selecting location type dropdown



if($_POST['loctype']){



     echo "<select  name='parentid' id='parentid' class='form-control required' required><option value=''>--Please Select--</option>";



     $parent_query="SELECT uid,name,city,state FROM asc_master where user_level<'".$_POST['loctype']."' order by id_type,name";



     $parent_res=mysqli_query($link1,$parent_query);



     while($row_parent = mysqli_fetch_array($parent_res)){



           echo "<option value='".$row_parent[uid]."'>";



           echo $row_parent['name'].",".$row_parent['city'].",".$row_parent['state']."</option>";



	 }



	 echo "<option value='NONE'>NONE</option>";



	       //if($_POST['loctype']==1){ echo "<option value='NONE'>NONE</option>";}



     echo "</select>";



}

////////////////////////////Stock List updated/////////////////////////////





 if($_POST['modelbilling']){



	$indx=$_POST['indxx'];

 	if($_REQUEST['dup_part']!=""){

		 	$dup_part="and partcode not in('".$_REQUEST['dup_part']."')";

		 }else{

			 $dup_part="";

		 }



     echo "<select  name='partcode[$indx]' id='partcode[$indx]' class='form-control selectpicker' data-live-search='true' onChange='return getAvlStk($indx)' style='width:140px;text-align:left;padding: 2px'><option value='' >Please Select Partcode</option>";



 echo $acc_query="select partcode, part_name, part_category,vendor_partcode,brand_id from partcode_master where model_id Like '%".$_POST['modelbilling']."%' and partcode in( select partcode from client_inventory where location_code='".$_SESSION['asc_code']."' and okqty > 0) ".$dup_part." group by partcode order by part_name ";



     $acc_res=mysqli_query($link1,$acc_query);



     while($row_acc = mysqli_fetch_array($acc_res)){



           echo "<option data-tokens='".$row_acc['partcode']."|".$row_acc['part_name']."' value='".$row_acc['partcode']."'>";



           echo $row_acc['partcode']." - ".$row_acc['part_name']."(".$row_acc['part_category'].")</option>";



	 }



      echo "</select>~".$indx;







}



if($_POST['stocklist']){



    $stk_query="update  client_inventory set list_qty='".$_POST['stocklist']."',list_price='".$_POST['lprice']."'  where  id='".$_POST['indxx']."' and location_code='".$_POST['location']."'";



     $stk_res=mysqli_query($link1,$stk_query);



	



	 echo "Updated"."~".$_POST['indxx'];



}

//////////////////  Get available stock of product for selected location ///// develope by priya



if($_POST['locstk']){



    $stk_query="SELECT ".$_POST['stktype']." FROM client_inventory where  partcode='".$_POST['locstk']."' and location_code='".$_POST['location']."'";



     $stk_res=mysqli_query($link1,$stk_query);



	 $stk_row = mysqli_fetch_array($stk_res);



	 echo $stk_row[0]."~".$_POST['indxx'];



}

//////////////////  Get available stock of product for selected location ///// develope by priya



if($_POST['userstk']){



    $stk_query="SELECT ".$_POST['stktype']." FROM user_inventory where  partcode='".$_POST['userstk']."' and locationuser_code='".$_POST['location']."'";



     $stk_res=mysqli_query($link1,$stk_query);



	 $stk_row = mysqli_fetch_array($stk_res);



	 echo $stk_row[0]."~".$_POST['indxx'];



}


//////////////////  Get brand on basis of product  dropdown///// develope by priya



if($_POST['productid']){



$indx  =$_POST['indxx'];



     echo "<select  name='brand[$indx]' id='brand[$indx]' class='form-control selectpicker' onChange='return getmodel($indx);'><option value='' >Please Select Brand</option>";



 $acc_query="SELECT distinct(brand_id) FROM model_master where product_id ='".$_POST['productid']."'  ";



     $acc_res=mysqli_query($link1,$acc_query);



     while($row_acc = mysqli_fetch_array($acc_res)){



	$brand =mysqli_query($link1 ,"select * from brand_master where  	brand_id= '$row_acc[brand_id]' ");



	 while($b = mysqli_fetch_array($brand)){



           echo "<option value='".$b['brand_id']."'>";



           echo $b['brand'].'('.$b['brand_id'].')'."</option>";



		   }



	 }



     echo "</select>~".$_POST['indxx'];







}



//////////////////  Get model on basis of product  and brand dropdown///// develope by priya



if($_REQUEST['brandinfo']){
    $indx  =$_REQUEST['indxx'];
    if($indx!=""){
        echo "<select  name='model[$indx]' id='model[$indx]' class='form-control required selectpicker' data-live-search='true' onChange='return getpartcode($indx);'>".
            "<option value='' >Please Select Model</option>";
        $acc_query="SELECT distinct(model_id),model FROM model_master where product_id ='".$_POST['productinfo']."'  and brand_id = '".$_REQUEST['brandinfo']."' and status='1' and division='".$_REQUEST['division']."' ";
        $acc_res=mysqli_query($link1,$acc_query);
        while($row_acc = mysqli_fetch_array($acc_res)){
            echo "<option data-tokens='".$row_acc['model_id']."|".$row_acc['model']."'  value='".$row_acc['model_id']."'>";
            echo $row_acc['model'].'('.$row_acc['model_id'].')'."</option>";
        }
        echo "</select>~".$_REQUEST['indxx'];
    }
    else {
        echo "<select  name='model' id='model' class='form-control selectpicker required' data-live-search='true' ><option value=''>Please Select Model</option>";
        $acc_query="SELECT distinct(model_id),model FROM model_master where product_id ='".$_REQUEST['productinfo']."'  and brand_id = '".$_REQUEST['brandinfo']."' ";
        $acc_res=mysqli_query($link1,$acc_query);
        while($row_acc = mysqli_fetch_array($acc_res)){
            echo "<option data-tokens='".$row_acc['model_id']."|".$row_acc['model']."' value='".$row_acc['model_id']."'>";
            echo $row_acc['model'].'('.$row_acc['model_id'].')'."</option>";
        }
        echo "</select>";
    }
}





////// get stock with Price status on the basis of partcode DEmicrotekP BY JITENDER 



if($_POST['partcodestkPrice']){



	$res_stock = mysqli_query($link1,"select ".$_POST['stk_type']." from client_inventory where location_code='".$_POST['locationcode']."' and partcode='".$_POST['partcodestkPrice']."'");



	$row_stock = mysqli_fetch_array($res_stock);



	

	$price=mysqli_query($link1,"select  location_price  from partcode_master where  partcode='".$_POST['partcodestkPrice']."'");

	$row_price = mysqli_fetch_array($price);



	if(mysqli_num_rows($res_stock)!=0 ){

		

		$stk=$row_stock[0];

		}

		else{ 

		$stk= 0;

		}

		echo  $stk."~".$_POST['indxx']."~".$row_price[0] ;



}





//////////////////  Get model on basis of product and brand dropdown///// develope by priya



if($_POST['modelinfo']){

$indx  =$_POST['indxx'];
$stocktype  = $_POST['stk_type'];
  if($stocktype==''){$stocktype="okqty";}
     echo "<select  name='partcode[$indx]' id='partcode[$indx]' class='form-control required selectpicker' data-live-search='true' onChange='return getAvlStk($indx);'><option value='' >Please Select Partcode</option>";

 //$acc_query="SELECT partcode, part_name, part_category,vendor_partcode FROM partcode_master where status='1' and model_id like '%".$_POST['modelinfo']."%'";
 ////// updated by shekhar on 19 mar 2021 make this query same as direct dispatch
 $acc_query="select partcode, part_name, part_category,vendor_partcode,brand_id from partcode_master where (model_id Like '%".$_POST['modelinfo']."%' OR part_category='GLOBAL') and status='1' and partcode in ( select partcode from client_inventory where location_code='".$_SESSION['asc_code']."' and ".$stocktype." > 0) group by partcode order by part_name";


     $acc_res=mysqli_query($link1,$acc_query);

     while($row_acc = mysqli_fetch_array($acc_res)){

           echo "<option data-tokens='".$row_acc['part_name']."|".$row_acc['partcode']."' value='".$row_acc['partcode']."'>";
           echo $row_acc['part_name']."(". $row_acc['partcode'].") (".$row_acc['part_category'].")</option>";



	 }
      echo "</select>~".$_POST['indxx'];

}

if($_POST['modelinfo_consume']){
$indx  =$_POST['indxx'];
$stocktype  = $_POST['stk_type'];
  if($stocktype==''){$stocktype="okqty";}
     echo "<select  name='partcode[$indx]' id='partcode[$indx]' class='form-control required selectpicker'  onChange='return getAvlStk($indx);'><option value='' >Please Select Partcode</option>";
 $acc_query="select partcode, part_name, part_category,vendor_partcode,brand_id from partcode_master where (model_id Like '%".$_POST['modelinfo_consume']."%' OR part_category='GLOBAL') and status='1'  and (part_group='CONSUMABLE' OR part_group='STATIONERY') and partcode in ( select partcode from client_inventory where location_code='".$_SESSION['asc_code']."' and ".$stocktype." > 0) group by partcode order by part_name";
     $acc_res=mysqli_query($link1,$acc_query);

     while($row_acc = mysqli_fetch_array($acc_res)){

           echo "<option value='".$row_acc['partcode']."'>";
           echo $row_acc['part_name']."(". $row_acc['partcode'].") (".$row_acc['part_category'].")</option>";



	 }
      echo "</select>~".$_POST['indxx'];

}


if($_POST['modelinfogrn']){

$indx  =$_POST['indxx'];

     echo "<select  name='partcode[$indx]' id='partcode[$indx]' class='form-control required selectpicker' data-live-search='true' onChange='return getAvlStk($indx);'><option value='' >Please Select Partcode</option>";

 //$acc_query="SELECT partcode, part_name, part_category,vendor_partcode FROM partcode_master where status='1' and model_id like '%".$_POST['modelinfo']."%'";
 ////// updated by shekhar on 19 mar 2021 make this query same as direct dispatch
 $acc_query="select partcode, part_name, part_category,vendor_partcode,brand_id from partcode_master where (model_id Like '%".$_POST['modelinfogrn']."%'  OR part_category='GLOBAL') and status='1' group by partcode order by part_name";


     $acc_res=mysqli_query($link1,$acc_query);

     while($row_acc = mysqli_fetch_array($acc_res)){

           echo "<option data-tokens='".$row_acc['part_name']."|".$row_acc['partcode']."' value='".$row_acc['partcode']."'>";
           echo $row_acc['part_name']."(". $row_acc['partcode'].") (".$row_acc['part_category'].")</option>";



	 }
      echo "</select>~".$_POST['indxx'];

}



//////////////////  Check duplicate document code for selected location



if($_POST['doccode']){



     $doccode_query="SELECT id from document_counter where financial_year='".$_POST['fcyear']."' and doc_code='".strtoupper($_POST['doccode'])."'";



     $doccode_res=mysqli_query($link1,$doccode_query);



	 $doccode_row = mysqli_num_rows($doccode_res);



	 echo $doccode_row;



}



//////////////////  Check duplicate Product code
if($_POST['pcode']){
     $prodcode_query="SELECT id from product_master where productcode='".$_POST['pcode']."'";
     $prodcode_res=mysqli_query($link1,$prodcode_query);
	 $prodcode_row = mysqli_num_rows($prodcode_res);
	 echo $prodcode_row;
}
//////////////////  Get price of product 



if($_POST['product']){



     $stk_query="SELECT price,mrp from price_master where product_code='".$_POST['product']."' and state='".$_POST['locstate']."' and location_type='".$_POST['lctype']."'";



     $stk_res=mysqli_query($link1,$stk_query);



	 $stk_row = mysqli_fetch_array($stk_res);



	 if($stk_row['price']){ echo $stk_row['price']."~".$stk_row['mrp'];}else{ echo "0.00~0.00";}



	



}


/////////////////////////////////////////////////////Model on the basis of Product/brand///////////////

if($_POST['brandModel']){



     echo "<select  name='modelid' id='modelid' class='form-control selectpicker required' data-live-search='true' required onchange='return getAccessory();'><option value=''>--Please Select--</option>";



     $model_query="SELECT model_id, model,wp,dwp FROM model_master where brand_id='".$_POST['brandModel']."' and product_id='".$_POST['product_id']."' order by model";



     $model_res=mysqli_query($link1,$model_query);



     while($row_model = mysqli_fetch_array($model_res)){



           echo "<option value='".$row_model['model_id']."~".$row_model['model']."~".$row_model['wp']."~".$row_model['dwp']."'>";



           echo $row_model['model']."</option>";



	 }



     echo "</select>";



}


if($_POST['brandModel_product']){



     echo "<select  name='modelid' id='modelid' class='form-control required' required onchange='return getAccessory();'><option value=''>--Please Select--</option>";



     $model_query="SELECT model_id, model,wp FROM model_master where brand_id='".$_POST['brandModel_product']."' and product_id='".$_POST['product_id']."' order by model";



     $model_res=mysqli_query($link1,$model_query);



     while($row_model = mysqli_fetch_array($model_res)){



           echo "<option value='".$row_model['model_id']."~".$row_model['model']."~".$row_model['wp']."'>";



           echo $row_model['model']."</option>";



	 }



     echo "</select>";



}
//////////////////  Get Model by selecting brand dropdown



if($_POST['brand']){



     echo "<select  name='modelid' id='modelid' class='form-control required' required onchange='return getAccessory();'><option value=''>--Please Select--</option>";



     $model_query="SELECT model_id, model,wp FROM model_master where brand_id='".$_POST['brand']."' order by model";



     $model_res=mysqli_query($link1,$model_query);



     while($row_model = mysqli_fetch_array($model_res)){



           echo "<option value='".$row_model['model_id']."~".$row_model['model']."~".$row_model['wp']."'>";



           echo $row_model['model']."</option>";



	 }



     echo "</select>";



}



if($_POST['jobcreatebrand']){



     echo "<select  name='modelid' id='modelid' class='form-control required' required onchange='return chk_serimei(this.value);'><option value=''>--Please Select--</option>";



     $model_query="SELECT model_id, model,chk_serimei FROM model_master where brand_id='".$_POST['jobcreatebrand']."' order by model";



     $model_res=mysqli_query($link1,$model_query);



     while($row_model = mysqli_fetch_array($model_res)){



           echo "<option value='".$row_model['model_id']."~".$row_model['model']."~".$row_model['chk_serimei']."'>";



           echo $row_model['model']."</option>";



	 }



     echo "</select>";



}



if($_POST['filterbrand']){



     echo "<select  name='modelid' id='modelid' class='form-control'><option value=''>All</option>";



     $model_query="SELECT model_id, model FROM model_master where brand_id='".$_POST['filterbrand']."' order by model";



     $model_res=mysqli_query($link1,$model_query);



     while($row_model = mysqli_fetch_array($model_res)){



           echo "<option value='".$row_model['model_id']."'>";



           echo $row_model['model']."</option>";



	 }



     echo "</select>";



}



//////////////////  Get Model by selecting brand dropdown



if($_POST['model']){



     echo "<select  name='acc_present[]' id='example-multiple-selected2' class='form-control'";

	 if($_POST['call_typ']=="DOA"){

	 echo "required";

	 }

	 echo " multiple='multiple'>";



     //$acc_query="SELECT a.partcode,b.part_name FROM map_partcode_model a, partcode_master b where a.model_id='".$_POST['model']."' and a.status='Y' and a.partcode=b.partcode and b.status='1' order by b.part_name";



	 echo $acc_query="SELECT partcode,part_name,vendor_partcode FROM partcode_master where model_id like '%".$_POST['model']."%' and status='1' and part_category='ACCESSORY' order by part_name";



     $acc_res=mysqli_query($link1,$acc_query);



     while($row_acc = mysqli_fetch_array($acc_res)){



           echo "<option value='".$row_acc['partcode']."'>";



           echo $row_acc['part_name']."(".$row_acc['partcode'].")"."</option>";



	 }



     echo "</select>";



}



//// get Solution Given drop down on the basis of symptom selection



if($_POST['symptomcode']){



	echo "<select style='width:230px' class='required form-control' name='repair_code[".$_POST['indxno']."]' id='repair_code[".$_POST['indxno']."]' onchange='return getPartDropDown(this.value,".$_POST['indxno'].");'><option value=''>Please Select Repair1</option>";
//symp_code LIKE '%".$_POST['symptomcode']."%'

$sql = "SELECT rep_code,rep_desc FROM repaircode_master where  status='1' and mapped_product like  '%".$_POST['product_id']."%' group by rep_code order by rep_desc ";



	$res = mysqli_query($link1,$sql);



	if(mysqli_num_rows($res)>0){



		while($row = mysqli_fetch_array($res)){



		echo "<option value='".$row[0]."'>";



		echo $row[1]."(".$row[0].")"."</option>";



		}



	}



	echo "</select>~".$_POST['indxno'];



}



//// get parts drop down on the basis of Solution Given selection


if($_POST['repaircodetrc']){

	$rep_details = mysqli_fetch_array(mysqli_query($link1,"SELECT check_rep,part_replace,rep_level FROM repaircode_master where rep_code='".$_POST['repaircodetrc']."'"));



	echo "<select  style='width:300px'";

	if($rep_details['check_rep']=='Y' || $rep_details['part_replace']=='Y'){ 

	echo" class='required form-control'";

	}else{

	echo"class='form-control'";

	 }

	 echo " name='part[".$_POST['indxno']."]' id='part[".$_POST['indxno']."]' onChange='getstockable(this.value,".$_POST['indxno']."); checkDuplicate(".$_POST['indxno'].",this.value);'><option value='' >Please Select Part</option>";

	//////check if part replace not require but location can view the part



	//if($rep_details['check_rep']=='Y' && $rep_details['part_replace']=='N'){



		//$sql = "SELECT partcode, part_name, customer_price from partcode_master where model_id like='%".$_POST['modelcode']."%' and repair_code LIKE '%".$_POST['repaircode']."%' and part_category not in ('BOX','UNIT') order by part_name";



	//}else{

	

	if($_POST['part_inf']!=''){

			 $prtstr = str_replace(",","','",$_POST['part_inf']);

			// $partinfo="and ci.partcode not in('".$prtstr."')";
		 $partinfo="";

		 }else{

			 $partinfo="";

		 }


     ////////////////////Gernal part show all model /////////////////
		 $sql = "SELECT ci.partcode, pm.part_name, pm.customer_price, ci.okqty, pm.vendor_partcode FROM client_inventory ci, partcode_master pm where ci.partcode=pm.partcode and (pm.model_id in ('".$_POST['modelcode']."') OR part_category='GLOBAL')  and  ci.okqty > 0  and ci.location_code='".$_POST['locationcode']."' ".$partinfo." order by pm.part_name";



	//}



	$res = mysqli_query($link1,$sql);



	while($row = mysqli_fetch_array($res)){



		echo "<option value='".$row[0]."^".$row[2]."^".$row[3]."'>";



		echo $row[1].'('.$row[4].')'.'-'.$row[0]."</option>";



	}



	echo "</select>~".$_POST['indxno']."~".$rep_details['part_replace']."~".$rep_details['rep_level'];



}

if($_POST['repaircode']){

	$rep_details = mysqli_fetch_array(mysqli_query($link1,"SELECT check_rep,part_replace,rep_level FROM repaircode_master where rep_code='".$_POST['repaircode']."'"));



	echo "<select  style='width:300px'";

	if($rep_details['check_rep']=='Y' || $rep_details['part_replace']=='Y'){ 

	echo" class='required form-control'";

	}else{

	echo"class='form-control'";

	 }

	 echo " name='part[".$_POST['indxno']."]' id='part[".$_POST['indxno']."]' onChange='getstockable(this.value,".$_POST['indxno'].");'><option value='' >Please Select Part</option>";

	//////check if part replace not require but location can view the part



	//if($rep_details['check_rep']=='Y' && $rep_details['part_replace']=='N'){



		//$sql = "SELECT partcode, part_name, customer_price from partcode_master where model_id like='%".$_POST['modelcode']."%' and repair_code LIKE '%".$_POST['repaircode']."%' and part_category not in ('BOX','UNIT') order by part_name";



	//}else{

	

	/*if($_POST['part_inf']!=''){

			 $prtstr = str_replace(",","','",$_POST['part_inf']);

			 $partinfo="and ci.partcode not in('".$prtstr."')";
		// $partinfo="";

		 }else{

			 $partinfo="";

		 }*/


     ////////////////////Gernal part show all model /////////////////
		 $sql = "SELECT ci.partcode, pm.part_name, pm.customer_price, ci.okqty, pm.vendor_partcode FROM user_inventory ci, partcode_master pm where ci.partcode=pm.partcode and pm.model_id like '%".$_POST['modelcode']."%'  and  ci.okqty > 0  and ci.location_code='".$_POST['locationcode']."' and ci.locationuser_code='".$_POST['engid']."' ".$partinfo." order by pm.part_name";
		 
		//  $sql="SELECT ci.partcode, ci.okqty FROM client_inventory ci where   ci.okqty > 0 and ci.location_code='".$_POST['locationcode']."' order by ci.partcode";
		// $sql = "SELECT ci.partcode, pm.part_name, pm.customer_price, ci.okqty, pm.vendor_partcode,pm.part_category FROM client_inventory ci, partcode_master pm where ci.partcode=pm.partcode  and pm.model_id like '%".$_POST['modelcode111']."%'   and  ci.okqty > 0  and ci.location_code='".$_POST['locationcode']."'  order by pm.part_name";



	//}



	$res = mysqli_query($link1,$sql);



	while($row = mysqli_fetch_array($res)){

		//$part_details=mysqli_fetch_array(mysqli_query($link1,"select part_name,vendor_partcode,customer_price from partcode_master where partcode='".$row['partcode']."'"));

		echo "<option value='".utf8_encode($row[0])."^".$row[2]."^".$row[3]."'>";



		echo utf8_encode($row[1]).'('.$row[4].')'.'-'.$row[0]."</option>";



	}



	echo "</select>~".$_POST['indxno']."~".$rep_details['part_replace']."~".$rep_details['rep_level'];



}



/////// get GST Details for EP parts



if($_POST['eppart_code']){



	$sql_part = "SELECT hsn_code, customer_price, part_category FROM partcode_master where partcode='".$_POST['eppart_code']."' and status='1'";



	$res_part = mysqli_query($link1,$sql_part);



	$row_part = mysqli_fetch_array($res_part);



	if($_POST['wrs'] == "OUT" || $_POST['wrs'] == "IN"){



 		$chk_tax = mysqli_query($link1,"select sgst,igst,cgst from tax_hsn_master where hsn_code='".$row_part['hsn_code']."'");



		$chk_abl_tax = mysqli_fetch_array($chk_tax);



        //////////



		$taxper = $chk_abl_tax['igst'];



	}



	else{



		$taxper = "0.00";



	}



	$price = $row_part['customer_price'];



    ///calculate all gst tax amount



	$taxamt = ($price * $taxper)/100;



	//// calculate total amount with tax



	$totalamt = $price + $taxamt;



    ////return data



	echo $price."~".$row_part['hsn_code']."~".$taxper."~".$taxamt."~".$totalamt."~".$_POST['indxno'];



}



////// get replacement type like BOX or UNIT of selected replace model



if($_POST['replacemodel']){



	echo "<select name='rep_part' class='required form-control' id='rep_part' style='width:150px;' onChange='return getstock(this.value);'><option value=''>Please Select Type</option>";



	//$sql_rplc = "SELECT partcode,part_name FROM partcode_master where model_id like '%".$_POST['replacemodel']."%' and status='1' and part_category in ('BOX','UNIT')";

	$sql_rplc = "SELECT ci.partcode, pm.part_name, pm.customer_price, ci.okqty, pm.vendor_partcode FROM client_inventory ci, partcode_master pm where ci.partcode=pm.partcode and pm.model_id like '%".$_POST['replacemodel']."%' and pm.part_category in ('BOX','UNIT') and  ci.okqty > 0  and ci.location_code='".$_POST['locationcode']."' order by pm.part_name";



	$res_rplc = mysqli_query($link1,$sql_rplc);



	while($row_rplc = mysqli_fetch_array($res_rplc)){



		echo "<option value='".$row_rplc[0]."'>";



		echo $row_rplc[1].'('.$row_rplc[4].')'." | ".$row_rplc[0]."</option>";



	}



	echo "</select>";



}

////// get second imei/////////////////////////

if($_POST['sec_imei']){



	$res_asc_stock = mysqli_query($link1,"SELECT imei1,imei2 from imei_details_asp where (imei1='".$_POST['sec_imei']."' or imei2='".$_POST['sec_imei']."') and location_code='".$_SESSION['asc_code']."' and status='1'");

	$count_asc_stk = mysqli_num_rows($res_asc_stock);

	if($count_asc_stk >0){

		$row_imei_stock = mysqli_fetch_assoc($res_asc_stock);

		if($row_imei_stock['imei1']==$_POST['sec_imei']){

			$sec_imei= $row_imei_stock['imei2']."~".$row_imei_stock['imei1'];

		}else if($row_imei_stock['imei2']==$_POST['sec_imei']){

			$sec_imei = $row_imei2['imei1']."~".$row_imei_stock['imei1'];

		}else{

		$sec_imei = ""."~".$_POST['sec_imei'];

		}

	}else{

		$sec_imei = ""."~"."";

	}



	echo $sec_imei;



}

//////////////////   stock conversion function 

   if($_POST['stockval']){
   $stocktype = "";
    if($_POST['stockval'] == 'okqty'){
	 $stock_st = mysqli_query($link1,"select okqty  from client_inventory  where location_code ='".$_POST['asccode']."' and partcode = '".$_POST['part']."' and okqty >0 ");
	 $stock_val = mysqli_fetch_array($stock_st);
	 $stocktype = $stock_val['okqty'];
	}
	   if($_POST['stockval'] == 'scrap'){

	 $stock_st = mysqli_query($link1,"select scrap  from client_inventory  where location_code ='".$_POST['asccode']."' and partcode = '".$_POST['part']."' and scrap >0 ");
	 $stock_val = mysqli_fetch_array($stock_st);
	 $stocktype = $stock_val['scrap'];
	}
	if($_POST['stockval'] == 'faulty'){
	 $stock_st = mysqli_query($link1,"select faulty from client_inventory  where location_code ='".$_POST['asccode']."' and partcode = '".$_POST['part']."' and faulty >0 ");
	$stock_val = mysqli_fetch_array($stock_st);
	$stocktype = $stock_val['faulty'];
	}
	   
	   if($_POST['stockval'] == 'broken'){
	 $stock_st = mysqli_query($link1,"select broken from client_inventory  where location_code ='".$_POST['asccode']."' and partcode = '".$_POST['part']."' and broken >0 ");
	$stock_val = mysqli_fetch_array($stock_st);
	$stocktype = $stock_val['broken'];
	}
	
	echo $stocktype."~".$_POST['indxx'];


}


////// get stock status on the basis of partcode



if($_POST['partcodestk']){



	$res_stock = mysqli_query($link1,"select ".$_POST['stk_type']." from client_inventory where location_code='".$_POST['locationcode']."' and partcode='".$_POST['partcodestk']."'");



	$row_stock = mysqli_fetch_array($res_stock);



	if($row_stock[0]){echo $row_stock[0];}else{ echo "0";}



}



////// get model features on the basis of model id



if($_POST['modelcode']){



	$explodee=explode(",",$_POST['fieldss']);



	$res_modeldet = mysqli_query($link1,"select ".$_POST['fieldss']." from model_master where model_id='".$_POST['modelcode']."'");



	$row_modeldet = mysqli_fetch_array($res_modeldet);



	$rtn_str="";



   	for($k = 0; $k < count($explodee); $k++){



    	if($rtn_str==""){



          	$rtn_str .= $row_modeldet[$k];



	   	}



       	else{



          	$rtn_str .= "~".$row_modeldet[$k];



	   	}



   	}



	echo $rtn_str;



}


/////////////////////////////////////Invoice Partcode//////////////////////////////////////////////

if($_POST['InvPartcode']){ ////// develop by priya

$indx=$_POST['indxx'];
$stk=explode("AND",$_POST['InvPartcode']);

	 echo "<select  name='inv[$indx]' id='inv[$indx]' class='form-control required selectpicker'  onChange='return getAvlStk($indx);'><option value='' >Select Invoice/sale date/Qty</option>";



 echo  $acc_query="select * from fifo_list  where location_code='".$_SESSION['asc_code']."' and okqty!=fifi_ty and partcode='".$stk[0]."'  and id='".$stk[1]."' group by challan_no  order by challan_no";



     $acc_res=mysqli_query($link1,$acc_query);



     while($row_acc = mysqli_fetch_array($acc_res)){

$sktqty=$row_acc['okqty']-$row_acc['fifi_ty'];

           echo "<option value='".$row_acc['challan_no']."'>";



           echo $row_acc['challan_no']."(". $row_acc['sale_date'].") (". $sktqty.")</option>";



	 }



    echo "</select>~".$_POST['indxx'];







}


if($_POST['locinvstk']){

$stk=explode("AND",$_POST['locinvstk']);

   $stk_query="SELECT fifi_ty,okqty  FROM fifo_list where  partcode='".$stk[0]."' and id='".$stk[1]."'";



     $stk_res=mysqli_query($link1,$stk_query);



	 $stk_row = mysqli_fetch_array($stk_res);

$sktqty=$stk_row['okqty']-$stk_row['fifi_ty'];

	 echo $sktqty."~".$_POST['indxx'];



}


if($_POST['partpriceinvtax']){

$stk=explode("AND",$_POST['partpriceinvtax']);
//echo "select a.hsn_code,b.price  from partcode_master a , fifo_list b  where partcode='".$stk[0]."' and id='".$stk[1]."'";

//echo "select a.hsn_code,b.price  from partcode_master a , fifo_list b  where b.partcode=='".$stk[0]."' and b.id='".$stk[1]."' and a.partcode=b.partcode";

	$res_partdet = mysqli_query($link1,"select a.hsn_code,b.price  from partcode_master a , fifo_list b  where b.partcode='".$stk[0]."' and b.id='".$stk[1]."' and a.partcode=b.partcode");



	$count_partdet = mysqli_num_rows($res_partdet);



	if($count_partdet >0){



		$row_partdet = mysqli_fetch_array($res_partdet);



		$hsncode = $row_partdet['hsn_code'];



		$custprice = 0.00;

		$locprice = $row_partdet['price'];
		
		$l3_price =0.00;



		///// get tax details



		$row_tax = mysqli_fetch_assoc(mysqli_query($link1,"select sgst, igst, cgst  from tax_hsn_master where hsn_code='".$hsncode."' and status='1'"));



		$sgstper = $row_tax['sgst'];



		$igstper = $row_tax['igst'];



		$cgstper = $row_tax['cgst'];



	}else{



		$hsncode = "";



		$custprice = 0.00;



		$sgstper = 0.00;



		$igstper = 0.00;



		$cgstper = 0.00;



	}



	echo $locprice."~".$hsncode."~".$igstper."~".$sgstper."~".$cgstper."~".$locprice."~".$l3_price;



}

/////////////////////////////////////////////////////////////imei stock check at asp by priya//////////////////////////////////////////////

if($_POST['imeino']){

	//echo "select imei1,imei2 from imei_details_asp where (imei1='".$_POST['imeino']."' or imei2 = '".$_POST['imeino']."') and location_code = '".$_POST['location']."'  and partcode = '".$_POST['partcode']."'  and status = '1'";

	 $res_imei21= mysqli_query($link1,"select imei1,imei2 from imei_details_asp where (imei1='".$_POST['imeino']."' or imei2 = '".$_POST['imeino']."') and location_code = '".$_POST['location']."'  and partcode = '".$_POST['partcode']."'  and status = '1' ");

	//$count_imei2 = mysqli_num_rows($res_imei2);

	if(mysqli_num_rows($res_imei21) >0){

		$flag1 ="Y";

	}else{

		$flag1 ="N";

	}

	echo $flag1."~".$_POST['indxx']."~".$_POST['imeino'];

}



////// get second imei or serial no. after enter first



if($_POST['postimei1']){



	$res_imei2 = mysqli_query($link1,"select imei1,imei2 from imei_data_import where imei1='".$_POST['postimei1']."'");



	$count_imei2 = mysqli_num_rows($res_imei2);



	if($count_imei2 >0){



		$row_imei2 = mysqli_fetch_array($res_imei2);



		$sec_imei = $row_imei2['imei2'];



	}else{



		$sec_imei = "";



	}



	echo $sec_imei;



}


////// get infomation of imei or serial no. after enter first



if($_POST['serialinfo']){



	$res_imei2 = mysqli_query($link1,"select import_date,out_flag from imei_data_import where imei1='".$_POST['serialinfo']."'");



	//$count_imei2 = mysqli_num_rows($res_imei2);

$row_imei2 = mysqli_fetch_array($res_imei2);
     $sec_imei = $row_imei2['out_flag'];


	echo $sec_imei;



}

/////////////////////////////////////////////////Brand Mapping///////////////////////////////////
if($_POST['brandmap']){



	$res_brand = mysqli_query($link1,"select * from access_brand where brand_id='".$_POST['brandmap']."' and location_code ='".$_POST['rep_location']."' and status='Y'");



	$count_acees = mysqli_num_rows($res_brand);



	if($count_acees >0){



		



		$row_count = 1;



	}else{



		$row_count = 0;


	}



	echo $row_count;



}

////// get part price and tax and hsn code etc. on the basis of partcode update by Priya








if($_POST['partpricetax']){
	//echo "select hsn_code, customer_price,location_price,l3_price  from partcode_master where partcode='".$_POST['partpricetax']."'";exit;
	$res_partdet = mysqli_query($link1,"select hsn_code, customer_price,location_price,l3_price  from partcode_master where partcode='".$_POST['partpricetax']."'");
	$count_partdet = mysqli_num_rows($res_partdet);
	if($count_partdet >0){
		$row_partdet = mysqli_fetch_array($res_partdet);
		$hsncode = $row_partdet['hsn_code'];
		$custprice = $row_partdet['customer_price'];
		$locprice = $row_partdet['location_price'];
		$l3_price = $row_partdet['l3_price'];
    ///// get tax details
		$row_tax = mysqli_fetch_assoc(mysqli_query($link1,"select sgst, igst, cgst  from tax_hsn_master where hsn_code='".$hsncode."' and status='1'"));
		$sgstper = $row_tax['sgst'];
		$igstper = $row_tax['igst'];
		$cgstper = $row_tax['cgst'];
	}else{
		$hsncode = "";
		$custprice = 0.00;
		$sgstper = 0.00;
		$igstper = 0.00;
		$cgstper = 0.00;
	}
	echo $custprice."~".$hsncode."~".$igstper."~".$sgstper."~".$cgstper."~".$locprice."~".$l3_price;
}
if($_POST['partpricetaxaddbill']){
	$res_partdet = mysqli_query($link1,"select hsn_code, customer_price,location_price,l3_price  from partcode_master where partcode='".$_POST['partpricetaxaddbill']."'");
	$count_partdet = mysqli_num_rows($res_partdet);
	if($count_partdet >0){
		$row_partdet = mysqli_fetch_array($res_partdet);
		$hsncode = $row_partdet['hsn_code'];
		
		///// get tax details
		$row_tax = mysqli_fetch_assoc(mysqli_query($link1,"select sgst, igst, cgst  from tax_hsn_master where hsn_code='".$hsncode."' and status='1'"));
		$sgstper = $row_tax['sgst'];
		$igstper = $row_tax['igst'];
		$cgstper = $row_tax['cgst'];
		$tax=$igstper+100;
		$custpriceb = ($row_partdet['customer_price']/$tax)*100;
		$locpriceb  = ($row_partdet['location_price']/$tax)*100;
		$l3_priceb  = ($row_partdet['l3_price']/$tax)*100;
		
		$custprice = round($custpriceb,2);
		$locprice = round($locpriceb,2);
		$l3_price = round($l3_priceb,2);
	}else{
		$hsncode = "";
		$custprice = 0.00;
		$sgstper = 0.00;
		$igstper = 0.00;
		$cgstper = 0.00;
	}
	echo $custprice."~".$hsncode."~".$igstper."~".$sgstper."~".$cgstper."~".$locprice."~".$l3_price."~".$row_partdet['customer_price'];
}

////////////////////////////////////////////////////local purchase//////////////////////////////

if($_POST['partpricetaxlocal']){



	$res_partdet = mysqli_query($link1,"select hsn_code, customer_price,l3_price  from partcode_master where partcode='".$_POST['partpricetaxlocal']."'");



	$count_partdet = mysqli_num_rows($res_partdet);



	if($count_partdet >0){



		$row_partdet = mysqli_fetch_array($res_partdet);



		$hsncode = $row_partdet['hsn_code'];



		$custprice = $row_partdet['customer_price'];

		$locprice = $row_partdet['l3_price'];



		///// get tax details



		$row_tax = mysqli_fetch_assoc(mysqli_query($link1,"select sgst, igst, cgst  from tax_hsn_master where hsn_code='".$hsncode."' and status='1'"));



		$sgstper = $row_tax['sgst'];



		$igstper = $row_tax['igst'];



		$cgstper = $row_tax['cgst'];



	}else{



		$hsncode = "";



		$custprice = 0.00;



		$sgstper = 0.00;



		$igstper = 0.00;



		$cgstper = 0.00;



	}



	echo $locprice."~".$hsncode."~".$igstper."~".$sgstper."~".$cgstper."~".$locprice;



}

if($_POST['getbrand']){ ////// develop by priya



	echo "<select name='voc1' class='form-control' id='voc1'><option value=''>Please VOC Type</option>";



	$sql_rplc = "select voc_code, voc_desc from voc_master where brand_id='".$_POST['getbrand']."' and product_id='".$_POST['getproduct']."' ";



	$res_rplc = mysqli_query($link1,$sql_rplc);



	while($row_rplc = mysqli_fetch_array($res_rplc)){



		echo "<option value='".$row_rplc[0]."'>";



		echo $row_rplc[1]." | ".$row_rplc[0]."</option>";



	}



	echo "</select>";



}

///////////////////////////////////////// Loction Master//////////////////////////////////////////////////////////

if($_POST['citynew']){

     echo "<select  name='location_code' id='location_code' class='form-control' ><option value=''>Please Select</option>";

  echo    $location_query="SELECT locationname, location_code FROM location_master where stateid='".$_POST['statenew']."'  and cityid='".$_POST['citynew']."' ";

     $loc_res=mysqli_query($link1,$location_query);

     while($loc_info = mysqli_fetch_array($loc_res)){

           echo "<option value='".$loc_info['location_code']."'>";

           echo $loc_info['locationname']."</option>";

	 }

     echo "</select>";

}



/////////////////////////////////////////Location Access Pin code///////////////////


if($_POST['Locpin']!=""){
	echo "<select  name='rep_location' id='rep_location' class='form-control required' ><option value=''>Please Select</option>";
  	$pin_query="SELECT b.location_code,b.locationname,b.nickname FROM  location_pincode_access a, location_master b WHERE a.statusid='1' AND a.pincode='".$_POST['Locpin']."' AND a.location_code=b.location_code  AND b.statusid='1' AND b.locationtype='ASP' GROUP BY a.location_code";
	$loc_pin=mysqli_query($link1,$pin_query);
	if(mysqli_num_rows($loc_pin)>0){
		while($loc_pin_acc = mysqli_fetch_array($loc_pin)){
			echo "<option value='".$loc_pin_acc['location_code']."'>";
			echo $loc_pin_acc['nickname']."</option>";
		}
		echo "</select>";
	}else{
		$loc_code = mysqli_query($link1,"SELECT location_code,locationname,nickname FROM  location_master where statusid='1' AND locationtype='ASP' ORDER BY locationname");
		while($loc_info = mysqli_fetch_array($loc_code)){	
			echo "<option value='".$loc_info['location_code']."'>";
			echo $loc_info['nickname']."</option>";
		}
		echo "</select>";
	}
}

//////// State Permission develop by shekhar 

if($_POST['permission_state']){

 $report="SELECT * FROM city_master where stateid='".$_POST['permission_state']."' ORDER BY city";

 $rs_report=mysqli_query($link1,$report) or die(mysqli_error());

 $i=1;

 if(mysqli_num_rows($rs_report)>0){

 echo "<table id='myTable4' class='table table-hover'><tbody><tr><td><input name='CheckAll' type='button' class='btn btn-primary' onClick='checkAll(document.frm1.report1)' value='Check All'/>&nbsp;<input name='UnCheckAll' type='button' class='btn btn-primary' onClick='uncheckAll(document.frm1.report1)' value='Uncheck All' /></td></tr>";

    while($row_report=mysqli_fetch_array($rs_report)){

     if($i%4==1){

         echo "<tr>";

  }                    

  $state_acc=mysqli_query($link1,"select cityid from access_region where userid='".$_POST['usrid']."' and cityid='".$row_report['cityid']."' and status='Y'")or die(mysqli_error());

        $num1=mysqli_num_rows($state_acc);

  echo "<td><input style='width:20px'  type='checkbox' id='report1' name='report1[]' value='".$row_report['cityid']."'";

  if($num1 > 0) echo "checked";

  echo "/>".$row_report['city']."</td>";

        if($i/4==0){

        echo "</tr>";

  }

  $i++;

 }

 echo "</tbody></table>";

 }

}
/////////////////////////////////////////develop by shekhar on 12-12-2018 //////////////////////////////////////////////////////////
if($_POST['getlocationdrop']){
	echo "<select  name='location_code' id='location_code' class='form-control'><option value=''>All</option>";
	$location_query = "SELECT locationname, location_code FROM location_master where statusid='1' and stateid='".$_POST['getlocationdrop']."' order by locationname";
    $loc_res=mysqli_query($link1,$location_query);
    while($loc_info = mysqli_fetch_array($loc_res)){
    	echo "<option value='".$loc_info['location_code']."'>";
        echo $loc_info['locationname']." ".$loc_info['location_code']."</option>";
	}
    echo "</select>";
}
/////////////////////////////////////////develop by shekhar on 12-12-2018 //////////////////////////////////////////////////////////
if($_POST['getproductdrop']!="" || $_POST["prdcat"]!=""){
	if($_POST['getproductdrop']){ $brand_look = "brand_id='".$_POST['getproductdrop']."'";}else{ $brand_look = "1";}
	if($_POST['prdcat']){ $product_look = "product_id='".$_POST['prdcat']."'";}else{ $product_look = "1";}
	echo "<select  name='product' id='product' class='form-control'><option value=''>All</option>";
	$prod_query = "select model_id,model from model_master where status='1' and ".$brand_look." and ".$product_look." order by model";
    $prod_res=mysqli_query($link1,$prod_query);
    while($prod_info = mysqli_fetch_array($prod_res)){
    	echo "<option value='".$prod_info['model_id']."'>";
        echo $prod_info['model']." ".$prod_info['model_id']."</option>";
	}
    echo "</select>";
}
/////////////////////////////////////////develop by shekhar on 12-12-2018 //////////////////////////////////////////////////////////
if($_POST['getproductdrop1']!="" || $_POST["prdcat1"]!=""){
	if($_POST['getproductdrop']){ $brand_look = "brand_id='".$_POST['getproductdrop']."'";}else{ $brand_look = "1";}
	if($_POST['prdcat']){ $product_look = "product_id='".$_POST['prdcat']."'";}else{ $product_look = "1";}
	echo "<select  name='product' id='product' class='form-control'><option value=''>All</option>";
	$prod_query = "select partcode,part_name from partcode_master where status='1' and ".$brand_look." and ".$product_look." and part_category='ACCESSORY' order by part_name";
    $prod_res=mysqli_query($link1,$prod_query);
    while($prod_info = mysqli_fetch_array($prod_res)){
    	echo "<option value='".$prod_info['partcode']."'>";
        echo $prod_info['part_name']." ".$prod_info['partcode']."</option>";
	}
    echo "</select>";
}
///// get city on selection of state in pincode permission tab
if($_POST['pinstate']){
     echo "<select  name='pincity' id='pincity' class='form-control required' required onchange='return getPostcode();' style='width:250px;'><option value=''>--Please Select--</option>";
     $city_query="SELECT cityid, city FROM city_master where stateid='".$_POST['pinstate']."' group by city order by city";
     $city_res=mysqli_query($link1,$city_query);
     while($row_city = mysqli_fetch_array($city_res)){
           echo "<option value='".$row_city['cityid']."'>";
           echo $row_city['city']."</option>";
	 }
           echo "<option value='Others'>Others</option>";
     echo "</select>";
}
if($_POST['pinstatepostoffice']){
     echo "<select  name='post_office' id='post_office' class='form-control required' required onchange='return getPincode();' style='width:250px;'><option value=''>--Please Select--</option>";
     $city_query="SELECT  postoffice  FROM pincode_master where cityid='".$_POST['pinstatepostoffice']."' group by postoffice order by postoffice";
     $city_res=mysqli_query($link1,$city_query);
     while($row_city = mysqli_fetch_array($city_res)){
           echo "<option value='".$row_city['postoffice']."'>";
           echo $row_city['postoffice']."</option>";
	 }
          
     echo "</select>";
}
//////// Pincode Permission develop by shekhar 
if(isset($_POST['state_pincode'])){
	$report="SELECT DISTINCT(pincode) as pinc FROM pincode_master where stateid='".$_POST['state_pincode']."' and cityid ='".$_POST['city_pincode']."' ORDER BY pincode";
 	$rs_report=mysqli_query($link1,$report) or die(mysqli_error());
 	$i=1;
	$j=0;
 	if(mysqli_num_rows($rs_report)>0){
 		echo "<table id='myTable4' class='table table-hover'><tbody><tr><td align='right' calspan=4><input name='CheckAll' type='button' class='btn btn-primary' onClick='checkAll(document.frm10.report10)' value='Check All'/>&nbsp;<input name='UnCheckAll' type='button' class='btn btn-primary' onClick='uncheckAll(document.frm10.report10)' value='Uncheck All' /></td></tr>";
    	while($row_report=mysqli_fetch_array($rs_report)){
     		if($i%2==1){
        		echo "<tr>";
			}          
			//echo "select id from location_pincode_access where location_code='".$_POST['pinloc']."' and pincode='".$row_report['pinc']."' and statusid='1'";
  			$state_acc=mysqli_query($link1,"select id,area_type from location_pincode_access where location_code='".$_POST['pinloc']."' and pincode='".$row_report['pinc']."' and statusid='1'")or die(mysqli_error($link1));
        	$num1=mysqli_num_rows($state_acc);
			$row_stk=mysqli_fetch_array($state_acc);
  			echo "<td><input style='width:20px'  type='checkbox' id='report10' name='pincod[]' value='".$row_report['pinc']."'";
  			if($num1 > 0) echo "checked";
			echo "/>".$row_report['pinc']." &nbsp;  &nbsp; &nbsp; &nbsp;</td><td>Travel Type<select  name='travel_type$j' id='travel_type$j' class='form-control required'  style='width:200px;'><option value=''";
			 if($row_stk[area_type]==''){ echo 'selected';}
			 echo ">Please select</option><option value='Upcountry'";
			  if($row_stk[area_type]=='Upcountry'){ echo 'selected';}
			  echo ">Upcountry</option><option value='local'";
			  if($row_stk[area_type]=='local'){ echo 'selected';}
			  echo ">local</option></select></td>";
			if($i/2==0){
				echo "</tr>";
			}
			$i++;
			$j++;
		}
		echo "</tbody></table>";
	}
	}
	//////////////////////////////////////////////////////////alernate partcode//////////////////////
	if(isset($_POST['alt_part_serch'])){
	echo "<table id='myTable4' class='table table-hover'><tbody><tr><td align='right' calspan=4><input name='CheckAll' type='button' class='btn btn-primary' onClick='checkAll(document.frm4.mappartatercode1)' value='Check All'/>&nbsp;<input name='UnCheckAll' type='button' class='btn btn-primary' onClick='uncheckAll(document.frm4.mappartatercode1)' value='Uncheck All' /></td></tr>";
echo	$report="select * from partcode_master where part_name='".$_POST['part_serch']."' or partcode='".$_POST['part_serch']."' ";
 	$rs_report=mysqli_query($link1,$report) or die(mysqli_error());
 	$i=1;
	$j=0;
 	if(mysqli_num_rows($rs_report)>0){
 		echo "<table id='myTable4' class='table table-hover'><tbody>";
    	while($row_report=mysqli_fetch_array($rs_report)){
			$part= explode(",",$row_report['model_id']); 
			           $partpresent   = count($part);
	
					   if($partpresent == '1'){
					   
					   				   $prod_query = "select model_id,model from model_master where status='1' and model_id='".$part[0]."'";
    $prod_res=mysqli_query($link1,$prod_query);
   $prod_info = mysqli_fetch_array($prod_res);
					   $name =  $prod_info['model'];
					   }
					   else if($partpresent >1){
					     $name ='';
					   for($i=0 ; $i<$partpresent; $i++){	
					   
					   				   $prod_query = "select model_id,model from model_master where status='1' and model_id='".$part[0]."'";
    $prod_res=mysqli_query($link1,$prod_query);
   $prod_info = mysqli_fetch_array($prod_res);
					  		 
			 			$name.= $prod_info['model'].",";
			 			}}
     		if($i%4==1){
        		echo "<tr>";
			}          
  			$state_acc=mysqli_query($link1,"select sno from alt_part_map where partcode='".$_POST['alt_part_serch']."' and alter_partcode ='".$row_report['partcode']."' and status='1'")or die(mysqli_error($link1));
        	$num1=mysqli_num_rows($state_acc);
  			echo "<td><input style='width:20px'  type='checkbox' id='mappartatercode1' name='mappartatercode[]' value='".$row_report['partcode']."'";
  			if($num1 > 0) echo "checked";
			echo "/>".$row_report['partcode']."-".$row_report['part_name']."(".$name.") &nbsp;  &nbsp; &nbsp; &nbsp;</td>";
			if($i/4==0){
				echo "</tr>";
			}
			$i++;
			$j++;
		}
		echo "</tbody></table>";
	}
	}
	//////////////////////////////////MAp PIN ASC PArtcode////////////////////////
if(isset($_POST['pin_asc_serch'])){
	
	
	
	
$report="SELECT location_code FROM `location_pincode_access` WHERE `pincode` like '".$_POST['pin_asc_serch']."' and statusid='1' GROUP by location_code";
 	$rs_report=mysqli_query($link1,$report) or die(mysqli_error());
 	$i=1;
	$j=0;
 	if(mysqli_num_rows($rs_report)>0){
 		echo "<table id='myTable4' class='table table-hover table table-bordered'><tbody>";
		echo " <tr>";
                echo "<th width='29%'>Location Name</th>";
               echo " <th width='27%'>Location Code</th>";
				echo" <th width='29%'>City</th>";
				 echo  "<th width='15%'>Stock</th>";
             echo "</tr>";
    	while($row_report=mysqli_fetch_array($rs_report)){
  		echo "<tr>";
			      // echo "select * from client_inventory where partcode='".$_POST['partcode']."' and location_code ='".$row_report['location_code']."'";
  			$part_sec=mysqli_query($link1,"select * from client_inventory where partcode='".$_POST['partcode']."' and location_code ='".$row_report['location_code']."'")or die(mysqli_error($link1));
        
			$row_model = mysqli_fetch_assoc($part_sec);
			
			$row_acc=mysqli_query($link1,"select locationname,cityid from location_master where location_code='".$row_report['location_code']."' ")or die(mysqli_error($link1));
			$rowasc=mysqli_fetch_array($row_acc);
			$cityrow=mysqli_query($link1,"select * from city_master where cityid='".$rowasc['cityid']."' ")or die(mysqli_error($link1));
        
			$row_city = mysqli_fetch_assoc($cityrow);
			echo "<td> $rowasc[locationname]</td>";
  			echo "<td> $row_report[location_code]</td>";
			echo "<td> $row_city[city]</td>";
			echo "<td> $row_model[okqty]</td>";
			echo "</tr>";
		
			$i++;
			$j++;
		}
		echo "</tbody></table>";
	}
	}
	
	
	// Product Master getting based on brand slection -ravi -17-12-21
if($_POST['brandproductmap']){



     echo "<select  name='product_name' id='product_name' class='form-control selectpicker required' data-live-search='true'  required onChange='resetProdModel();getVOC();'><option value=''>--Select Product--</option>";



     $model_query="SELECT product_id,product_name FROM product_master where status='1' and mapped_brand like '%".$_POST['brandproductmap']."%' order by product_name";

	//echo $model_query;

     $model_res=mysqli_query($link1,$model_query);



     while($row_model = mysqli_fetch_array($model_res)){



           echo "<option value='".$row_model['product_id']."'>";



           echo $row_model['product_name']."</option>";



	 }

     echo "</select>";

}	

if($_POST['filterbrand_nnn']){



     echo "<select  name='modelid' id='modelid' class='form-control'><option value=''>All</option>";



     $model_query="SELECT model_id, model FROM model_master where brand_id in ('".$_POST['filterbrand_nnn']."') order by model";

	//echo $model_query;

     $model_res=mysqli_query($link1,$model_query);



     while($row_model = mysqli_fetch_array($model_res)){



           echo "<option value='".$row_model['model_id']."'>";



           echo $row_model['model']."</option>";



	 }

     echo "</select>";

}	

/////////////////////////////////////////////////Product Mapping///////////////////////////////////
if($_POST['productmap']){
	$res_prod = mysqli_query($link1,"select * from access_product where product_id='".$_POST['productmap']."' and location_code ='".$_POST['rep_location']."' and status='Y'");
	$count_acees = mysqli_num_rows($res_prod);
	if($count_acees >0){
		$row_count = 1;
	}else{
		$row_count = 0;
	}
	echo $row_count;
}
/////////////////////////////////////////////////Product Mapping///////////////////////////////////
if($_POST['SerialdeatilsValidation']){
	$res_serial = mysqli_query($link1,"select product_id,model_id,brand_id,purchase_date,warranty_end_date,installation_date,amc_no,amc_end_date,invoice_no,d_name from product_registered where serial_no='".$_POST['SerialdeatilsValidation']."'");
	$count_serial = mysqli_num_rows($res_serial);
	if($count_serial >0){
		$serial_data=mysqli_fetch_array($res_serial);
		 $model_query="SELECT model FROM model_master where model_id='".$serial_data['model_id']."'";
		$model_res=mysqli_query($link1,$model_query);
		$model_name=mysqli_fetch_array($model_res);
        $warranty='IN';
		echo  $serial_data['product_id']."~".$serial_data['model_id']."~".$model_name['model']."~".$serial_data['brand_id']."~".$serial_data['purchase_date']."~".$serial_data['warranty_end_date']."~".$serial_data['installation_date']."~".$serial_data['amc_no']."~".$serial_data['amc_start_date']."~".$serial_data['amc_end_date']."~".$serial_data['invoice_no']."~".$serial_data['d_name']."~".$warranty;
	}else{
		echo  0 ;
	}

}
////////////////////////////////////////////Select Mapping ///////////////////////

if($_POST['vocproduct']){

$vocpro="SELECT * FROM voc_master where find_in_set('".$_POST['vocproduct']."',mapped_product) and status='1' and voc_desc!='' group by voc_desc order by voc_desc";

     echo "<select  name='voc1' id='voc1' class='form-control required'  required><option value=''>--Please Select--</option>";



  //   $vocpro="SELECT * FROM voc_master where mapped_product like '%".$_POST['vocproductmulti']."%' and status='1' and voc_desc!='' group by voc_desc order by voc_desc";
//   $vocpro="SELECT * FROM voc_master where mapped_product in ('".$_POST['vocproductmulti']."') and status='1' and voc_desc!='' group by voc_desc order by voc_desc";




     $row_res=mysqli_query($link1,$vocpro);



     while($vocrow = mysqli_fetch_array($row_res)){



           echo "<option value='".$vocrow['voc_code']."'>";



           echo $vocrow['voc_desc']."</option>";



	 }



        



     echo "</select>";



}
//////////////////////////Multi VOC select/////////////////////
if($_POST['vocproductmulti']){



     echo "<select name='voc2[]' id='example-multiple-selected1' multiple='multiple' class='form-control'>";



     $vocpro="SELECT * FROM voc_master where  find_in_set('".$_POST['vocproductmulti']."',mapped_product) and status='1'  and voc_desc!='' group by voc_desc order by voc_desc";



     $row_res=mysqli_query($link1,$vocpro);



     while($vocrow = mysqli_fetch_array($row_res)){



           echo "<option value='".$vocrow['voc_code']."'>";



           echo $vocrow['voc_desc']."</option>";



	 }



        



     echo "</select>";



}

//////////////////////////warranty change select/////////////////////
if($_POST['wrt_dd']){
     echo "<select name='updtd_wrnty' id='updtd_wrnty' class='form-control required' required onChange='return varReset()' >";
     echo "<option value='IN' ";
	 if($_POST['wrt_dd']=="IN") { echo "selected"; }
	 echo ">";
     echo "IN"."</option>";
	 echo "<option value='OUT' ";
	 if($_POST['wrt_dd']=="OUT") { echo "selected"; }
	 echo ">";
     echo "OUT"."</option>";
     echo "</select>";
}

////// get stock with velo Price status on the basis of partcode 
if($_POST['partcodestkPriceVelo']){
	$res_stock = mysqli_query($link1,"select ".$_POST['stk_type']." from client_inventory where location_code='".$_POST['locationcode']."' and partcode='".$_POST['partcodestkPriceVelo']."'");
	$row_stock = mysqli_fetch_array($res_stock);
	$price=mysqli_query($link1,"select  location_price  from partcode_master where  partcode='".$_POST['partcodestkPriceVelo']."'");
	$row_price = mysqli_fetch_array($price);
	if(mysqli_num_rows($res_stock)!=0 ){
		$stk=$row_stock[0];
		}
		else{ 
		$stk= 0;
		}
		echo  $stk."~".$_POST['indxx']."~".$row_price[0] ;
}

////// get stock with velo Price status on the basis of partcode 
if($_POST['partcodestkPriceConsume']){
	$res_stock = mysqli_query($link1,"select ".$_POST['stk_type']." from client_inventory where location_code='".$_POST['locationcode']."' and partcode='".$_POST['partcodestkPriceConsume']."'");
	$row_stock = mysqli_fetch_array($res_stock);
	$price=mysqli_query($link1,"select  location_price  from partcode_master where  partcode='".$_POST['partcodestkPriceConsume']."' and (part_group='CONSUMABLE' OR part_group='STATIONERY') ");
	$row_price = mysqli_fetch_array($price);
	if(mysqli_num_rows($res_stock)!=0 ){
		$stk=$row_stock[0];
		}
		else{ 
		$stk= 0;
		}
		echo  $stk."~".$_POST['indxx']."~".$row_price[0] ;
}

/////////////////////////////////////////////////Extended warranty check///////////////////////////////////
if($_POST['wrtTypChk']){
	$res_prod = mysqli_query($link1,"select * from amc where warranty_typ = '".$_POST['wrtTypChk']."' and job_or_brand_call = '".$_POST['jobno']."' ");
	$count_acees = mysqli_num_rows($res_prod);
	if($count_acees >0){
		$row_count = 1;
	}else{
		$row_count = 0;
	}
	echo $row_count;
}
/////////////////////////////////////////////////Remove Consume Partcheck///////////////////////////////////
if($_POST['rep_idRemove']){
    $today=date('Y-m-d');
	$flag = true;
	mysqli_autocommit($link1, false);
	$error_msg = "";
	$res_prod = mysqli_query($link1,"select id,job_no,repair_location,eng_id,partcode,part_qty from repair_detail where id = '".$_POST['rep_idRemove']."' and job_no = '".$_POST['jobno']."' and status!='12'");
	$count_acees = mysqli_num_rows($res_prod);
	if($count_acees >0){
		$data_details = mysqli_fetch_array($res_prod);
		if($_POST['jobno'] == $data_details[job_no]){
			
			$res_reapirdata = mysqli_query($link1,"Update repair_detail set status='12',remark='".$_POST['sto_type']."' where id='".$data_details[id]."' ");
			//// check if query is not executed
			if (!$res_reapirdata) {
				 $flag = false;
				 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
			}
            
            $flag = dailyActivity($_SESSION['userid'],$data_details[job_no],$_POST['sto_type']."at".$_SESSION['asc_code'],"Repair",$ip,$link1,$flag);
            if($_POST['sto_type']=="fresh")
            {
			$flag = stockLedger($data_details[job_no],$today,$data_details[partcode],$data_details[eng_id],$job_details['repair_location'],"IN","OK","Part Remove","Repair","1",$rd_partpricearr[$k],$data_details[repair_location],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);
				
										/*$res_invt = mysqli_query($link1,"UPDATE client_inventory set mount_qty = mount_qty+'1' where location_code='".$data_details[repair_location]."' and partcode='".$data_details[partcode]."'");
				//// check if query is not executed
				if (!$res_invt) {
					 $flag = false;
					 $error_msg = "Error details3: " . mysqli_error($link1) . ".";
				}*/
				
				$res_invt_user = mysqli_query($link1,"UPDATE user_inventory set okqty = okqty+'1' where location_code='".$data_details[repair_location]."' and partcode='".$data_details[partcode]."' and   	locationuser_code ='".$data_details[eng_id]."'");
				//// check if query is not executed
				if (!$res_invt_user) {
					 $flag = false;
					 $error_msg = "Error detailsuser: " . mysqli_error($link1) . ".";
				}
            $res_part = mysqli_query($link1,"select sno from part_to_credit where job_no ='".$_POST['jobno']."' and  partcode ='".$data_details[partcode]."' ");
	$count_part = mysqli_num_rows($res_part);
	if($count_part > 0){
            $res_p2cdata = mysqli_query($link1,"Update part_to_credit set status ='12' where job_no ='".$_POST['jobno']."' and  partcode ='".$data_details[partcode]."' ");
						if (!$res_p2cdata) {
							 $flag = false;
							 $error_msg = "Error details21: " . mysqli_error($link1) . ".";
						}
            
               $res_invt_user = mysqli_query($link1,"UPDATE user_inventory set faulty = faulty-'1' where location_code='".$data_details[repair_location]."' and partcode='".$data_details[partcode]."' and locationuser_code ='".$data_details[eng_id]."'");
				//// check if query is not executed
				if (!$res_invt_user) {
					 $flag = false;
					 $error_msg = "Error detailsuser: " . mysqli_error($link1) . ".";
				}
           
    }
             
            $flag = stockLedger($data_details[job_no],$today,$data_details[partcode],$data_details[eng_id],$job_details['repair_location'],"OUT","Faulty","Part Remove","Repair","1",$rd_partpricearr[$k],$data_details[repair_location],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);
                
             }
             if($_POST['sto_type']=="faulty")
            {
                 
            $res_partf = mysqli_query($link1,"select sno,imei,from_location,price ,cost,model_id,product_id,brand_id,eng_id,old_challan,ref_sno  from part_to_credit where job_no ='".$_POST['jobno']."' and  partcode ='".$data_details[partcode]."' ");
	$count_partF = mysqli_num_rows($res_partf);
	if($count_partF > 0){
            /*$res_p2cdata = mysqli_query($link1,"Update part_to_credit set status ='12' where job_no ='".$_POST['jobno']."' and  partcode ='".$data_details[partcode]."' ");
						if (!$res_p2cdata) {
							 $flag = false;
							 $error_msg = "Error details21: " . mysqli_error($link1) . ".";
						}*/
            $p2c_partdetails=mysqli_fetch_array($res_partf);
            $res_p2cdata = mysqli_query($link1,"INSERT INTO part_to_credit set job_no ='".$_POST['jobno']."',partcode ='".$data_details['partcode']."',imei='".$p2c_partdetails['imei']."',from_location='".$p2c_partdetails['from_location']."',  qty='1', price='".$p2c_partdetails['price']."',cost='".$p2c_partdetails['cost']."',consumedate='".$today."',model_id='".$p2c_partdetails['model_id']."',status ='4', product_id='".$p2c_partdetails['product_id']."', brand_id='".$p2c_partdetails['brand_id']."',type='EP2C',eng_id='".$p2c_partdetails['eng_id']."',eng_status='1',old_challan='".$p2c_partdetails[old_challan]."',ref_sno='".$p2c_partdetails[ref_sno]."',remark='Fresh Faulty' ");
						if (!$res_p2cdata) {
							 $flag = false;
							 $error_msg = "Error details21: " . mysqli_error($link1) . ".";
						}
               $res_invt_user = mysqli_query($link1,"UPDATE user_inventory set faulty = faulty+'1' where location_code='".$data_details[repair_location]."' and partcode='".$data_details[partcode]."' and locationuser_code ='".$data_details[eng_id]."'");
				//// check if query is not executed
				if (!$res_invt_user) {
					 $flag = false;
					 $error_msg = "Error detailsuser: " . mysqli_error($link1) . ".";
				}
           
    }
             
            $flag = stockLedger($data_details[job_no],$today,$data_details[partcode],$data_details[eng_id],$job_details['repair_location'],"IN","Faulty","Part Remove","Repair","1",$rd_partpricearr[$k],$data_details[repair_location],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);
                
             }
				$flag = dailyActivity($_SESSION['userid'],$data_details[job_no],"Part Remove"."at".$_SESSION['asc_code'],"Repair",$ip,$link1,$flag);
            $repair_type="Remove Part";
            
            $flag = callHistory($data_details[job_no],$_SESSION['asc_code'],"",$repair_type,$repair_type,$data_details[eng_id],$job_details['warranty_status'],$_POST['sto_type'],$_POST['travel'],"Y",$ip,$link1,$flag);
            
				if ($flag) {
		mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
		$msg = "Action has been successfully.";
	} else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	}
		 
		}
	}else{
		$msg="Request could not be processed. Please try again"; 
	}
	echo $msg;
}
/////////////////  Get city by selecting state	dropdown

if($_POST['statesaleperson']){



     echo "<select  name='rep_location' id='rep_location' class='form-control required' required>";



     $city_query="SELECT b.username,b.name FROM access_region a,admin_users b  where a.stateid='".$_POST['statesaleperson']."'and a.status='Y' and a.userid=b.username";
     $city_res=mysqli_query($link1,$city_query);
     while($row_loc= mysqli_fetch_array($city_res)){
           echo "<option value='".$row_loc['username']."'>";
           echo $row_loc['name']."</option>";
	 }
     echo "</select>";



}
if($_POST['modelbillingscrap']){
	$indx=$_POST['indxx'];
 	if($_REQUEST['dup_part']!=""){
		 	$dup_part="and partcode not in('".$_REQUEST['dup_part']."')";
		 }else{

			 $dup_part="";
		 }
echo "<select  name='partcode[$indx]' id='partcode[$indx]' class='form-control selectpicker'  data-live-search='true' onChange='return getAvlStk($indx)' style='width:140px;text-align:left;padding: 2px'><option value='' >Please Select Partcode</option>";
 echo $acc_query="select partcode, part_name, part_category,vendor_partcode,brand_id from partcode_master where model_id Like '%".$_POST['modelbilling']."%' and partcode in ( select partcode from client_inventory where location_code='".$_SESSION['asc_code']."' and scrap > 0) ".$dup_part." group by partcode order by part_name ";
     $acc_res=mysqli_query($link1,$acc_query);
     while($row_acc = mysqli_fetch_array($acc_res)){
           echo "<option data-tokens='".$row_acc['partcode']."|".$row_acc['part_name']."' value='".$row_acc['partcode']."'>";

           echo $row_acc['partcode']." - ".$row_acc['part_name']."(".$row_acc['part_category'].")</option>";

	 }
      echo "</select>~".$indx;
}
if($_POST['locstkscrap']){
    $stk_query="SELECT ".$_POST['stktype']." FROM client_inventory where  partcode='".$_POST['locstkscrap']."' and location_code='".$_POST['location']."' and scrap >0";
     $stk_res=mysqli_query($link1,$stk_query);
	 $stk_row = mysqli_fetch_array($stk_res);
   $part_row=mysqli_fetch_array(mysqli_query($link1,"SELECT wight  FROM partcode_master where partcode='".$_POST['locstkscrap']."'")); 
	if($part_row['wight']==''){
		$partwight=0;
	}
	else {
		$partwight=$part_row['wight'];
	}
	 echo $stk_row[0]."~".$_POST['indxx']."~".$partwight;
}
///////////////////////////Faulty Case Return Access Case
if($_POST['userstkfaulty']){
  $stk_query="SELECT ".$_POST['stktype']." FROM user_inventory where  partcode='".$_POST['userstkfaulty']."' and locationuser_code='".$_POST['location']."' and ".$_POST['stktype']." > 0";
     $stk_res=mysqli_query($link1,$stk_query);
	 $stk_row = mysqli_fetch_array($stk_res);
     $qty_faulty=$stk_row[0];  
    ////////////Fauty pending for dispatch 
    $pen_query="SELECT sum(qty) As qty  FROM part_to_credit WHERE eng_id = '".$_POST['location']."' and type='EP2C' and eng_status='1' AND partcode='".$_POST['userstkfaulty']."'  GROUP by partcode";
    $pend_res=mysqli_query($link1,$pen_query);
	$pend_row = mysqli_fetch_array($pend_res);
    $pending_faulty=$pend_row[0];
    ////Total pending faulty in inventory and minus pending stock for diaptch
     $remaing_qty = ($stk_row[0] - $pend_row[0]);
     if($remaing_qty > 0){
        $qty_faulty= $remaing_qty;
     }
    else {
        $qty_faulty="0";
    }
	 echo $qty_faulty."~".$_POST['indxx'];
}

if($_POST['stateuser']){

echo "<select name='locationcity' id='locationcity' class='form-control required' required><option value=''>--Please Select--</option>";
$city_query="SELECT cityid, city FROM city_master where stateid='".$_POST['stateuser']."' group by city order by city";
$city_res=mysqli_query($link1,$city_query);

while($row_city = mysqli_fetch_array($city_res)){
echo "<option value='".$row_city['cityid']."'>";
echo $row_city['city']."</option>";

}

echo "<option value='Others'>Others</option>";
echo "</select>";
}
if($_POST['cntryiduser']){

echo "<select name='locationstate' id='locationstate' class='form-control required' onchange='get_cityuserdiv();' required><option value=''>--Please Select--</option>";
$state_query="select stateid, state from state_master where countryid='".$_POST['cntryiduser']."' order by state";

$state_res=mysqli_query($link1,$state_query);
while($row_res = mysqli_fetch_array($state_res)){
echo "<option value='".$row_res['stateid']."'>";

echo $row_res['state']."</option>";

}

echo "</select>";


}

if($_POST['modelinfoasp']){

$indx  =$_POST['indxx'];
$stocktype  = $_POST['stk_type'];
  if($stocktype==''){$stocktype="okqty";}
     echo "<select  name='partcode[$indx]' id='partcode[$indx]' class='form-control required selectpicker' data-live-search='true'  onChange='getAvlStk($indx);  checkDuplicate($indx,this.value);'><option value='' >Please Select Partcode</option>";

 //$acc_query="SELECT partcode, part_name, part_category,vendor_partcode FROM partcode_master where status='1' and model_id like '%".$_POST['modelinfo']."%'";
 ////// updated by shekhar on 19 mar 2021 make this query same as direct dispatch
 $acc_query="select partcode, part_name, part_category,vendor_partcode,brand_id from partcode_master where (model_id Like '%".$_POST['modelinfoasp']."%' OR part_category='GLOBAL') and status='1'  group by partcode order by part_name";


     $acc_res=mysqli_query($link1,$acc_query);

     while($row_acc = mysqli_fetch_array($acc_res)){

           echo "<option data-tokens='".$row_acc['part_name']."|".$row_acc['partcode']."' value='".$row_acc['partcode']."'>";
           echo $row_acc['part_name']."(". $row_acc['partcode'].") (".$row_acc['part_category'].")</option>";



	 }
      echo "</select>~".$_POST['indxx'];

}

if($_POST['prodval']){



     echo "<select name='model_name' id='model_name' class='form-control required'  required><option value=''>--Please Select--</option>";
     $state_query="select model_id , model  from model_master where product_id='".$_POST['prodval']."' order by model";



     $state_res=mysqli_query($link1,$state_query);



     while($row_res = mysqli_fetch_array($state_res)){



           echo "<option value='".$row_res['model_id']."'>";



           echo $row_res['model']."</option>";



	 }

     echo "</select>";

}

if(isset($_POST['bin_id'])){
	$binid_query = "SELECT SUM(qty) as qty from bin_allocation where bin_id='".$_POST['bin_id']."' and location_code='".$_POST['loc_code']."' and partcode='".$_POST['prtcode']."' and stock_type='".$_POST['stocktype']."'";
	$binid_res = mysqli_query($link1,$binid_query);
	$binid_row = mysqli_fetch_assoc($binid_res);
	if($binid_row['qty']){ echo $binid_row['qty'];}else{ echo "0";}
}
///// get city on selection of state in pincode permission tab
if($_POST['pinstatearea']){
     echo "<select  name='pincity' id='pincity' class='form-control required' required onchange='get_pinareadiv();' style='width:250px;'><option value=''>--Please Select--</option>";
     $city_query="SELECT cityid, city FROM city_master where stateid='".$_POST['pinstatearea']."' group by city order by city";
     $city_res=mysqli_query($link1,$city_query);
     while($row_city = mysqli_fetch_array($city_res)){
           echo "<option value='".$row_city['cityid']."'>";
           echo $row_city['city']."</option>";
	 }
           echo "<option value='Others'>Others</option>";
     echo "</select>";
}
if($_POST['pincityarea']){
     echo "<select  name='pinarea' id='pinarea' class='form-control' onchange='getPincode();' style='width:250px;'><option value=''>--Please Select--</option>";
     $city_query="SELECT area FROM pincode_master where stateid='".$_POST['pincityarea']."' and cityid = '".$_POST['cty']."' and statusid = '1' group by area order by area";
     $city_res=mysqli_query($link1,$city_query);
     while($row_city = mysqli_fetch_array($city_res)){
           echo "<option value='".$row_city['area']."'>";
           echo $row_city['area']."</option>";
	 }
     echo "</select>";
}
//////// Pincode Permission develop by shekhar 
if(isset($_POST['state_pincode_area'])){
	if($_POST['search_pin']!=""){
		$report="SELECT DISTINCT(pincode) as pinc,area,id FROM pincode_master where pincode = '".$_POST['search_pin']."' ORDER BY pincode";
	}else{
		if($_POST['areaname']==""){ $area_st = " 1 "; }else{ $area_st = " area = '".$_POST['areaname']."' "; }
		$report="SELECT DISTINCT(pincode) as pinc,area,id FROM pincode_master where stateid='".$_POST['state_pincode_area']."' and cityid ='".$_POST['city_pincode']."' and ".$area_st." ORDER BY pincode";
	}
	
 	$rs_report=mysqli_query($link1,$report) or die(mysqli_error());
	
 	$i=1;
	$j=0;
 	if(mysqli_num_rows($rs_report)>0){
 		echo "<table id='myTable4' class='table table-hover'><tbody><tr><td align='right' calspan=4><input name='CheckAll' type='button' class='btn btn-primary' onClick='checkAll(document.frm10.report10)' value='Check All'/>&nbsp;<input name='UnCheckAll' type='button' class='btn btn-primary' onClick='uncheckAll(document.frm10.report10)' value='Uncheck All' /></td></tr>";
    	while($row_report=mysqli_fetch_array($rs_report)){
     		if($i%2==1){
        		echo "<tr>";
			}          
			//echo "select id from location_pincode_access where location_code='".$_POST['pinloc']."' and pincode='".$row_report['pinc']."' and statusid='1'";
  			$state_acc=mysqli_query($link1,"select id,area_type from location_pincode_access where location_code='".$_POST['pinloc']."' and pincode='".$row_report['pinc']."' and statusid='1'")or die(mysqli_error($link1));
        	$num1=mysqli_num_rows($state_acc);
			$row_stk=mysqli_fetch_array($state_acc);
  			echo "<td><input style='width:20px'  type='checkbox' id='report10' name='pincod[]' value='".$row_report['pinc']."'";
  			if($num1 > 0) echo "checked";
			echo "/>".$row_report['pinc']." &nbsp;  &nbsp; &nbsp; &nbsp;</td><td>Travel Type<select  name='travel_type$row_report[pinc]' id='travel_type$row_report[pinc]' class='form-control required'  style='width:200px;'><option value=''";
			 if($row_stk['area_type']==''){ echo 'selected';}
			 echo ">Please select</option><option value='Upcountry'";
			  if($row_stk[area_type]=='Upcountry'){ echo 'selected';}
			  echo ">Upcountry</option><option value='Local'";
			  if($row_stk[area_type]=='Local'){ echo 'selected';}
			  echo ">Local</option></select> <input style='width:100px'  type='hidden' id='area$row_report[pinc]' name='area$row_report[pinc]' value='".$row_report['area']."'/></td>";
			if($i/2==0){
				echo "</tr>";
			}
			$i++;
			$j++;
		}
		echo "</tbody></table>";
	}
}
/////////////////////////////////////////Location Access Pin code///////////////////
if($_POST['RVLocpin']){
	 $pin_query="SELECT stateid FROM  location_pincode_access where statusid='1' and pincode='".$_POST['RVLocpin']."' and location_code in (select location_code from access_brand where brand_id = '".$_POST['brand7']."' and status = 'Y') and location_code in ( select location_code from access_product where product_id = '".$_POST['product7']."' and status = 'Y' )  group by stateid ";
	 $loc_pin=mysqli_query($link1,$pin_query);
	 
    // echo "<select  name='rep_location' id='rep_location' class='form-control' onChange='displayAddress(this.value);' >";
	 echo "<select  name='rep_location' id='rep_location' class='form-control'  >";
	 if(mysqli_num_rows($loc_pin)>0){
	 	
		 $loc_code1=mysqli_query($link1,"SELECT location_code,locationname FROM  location_master where location_code = 'RVS' ");
	 	 $loc_info1 = mysqli_fetch_array($loc_code1);
	     echo "<option value='".$loc_info1['location_code']."'>";
	     echo $loc_info1['locationname']."-</option>";
		 
	 }else{
	 

	 
	 	//$pin_query="SELECT location_code FROM  location_pincode_access where statusid='1' and pincode='".$_POST['RVLocpin']."' and location_code in (select location_code from access_brand where brand_id = '".$_POST['brand7']."' and status = 'Y') and location_code in ( select location_code from access_product where product_id = '".$_POST['product7']."' and status = 'Y' ) group by location_code ";
		 $pin_query= "SELECT location_code,locationname,locationaddress,contactno1,emailid FROM  location_master where locationtype='ASP' and stateid='".$_POST['state7']."' and location_code in (select location_code from access_brand where brand_id = '".$_POST['brand7']."' and status = 'Y')";
		
		 $loc_pin=mysqli_query($link1,$pin_query);
		 while($loc_pin_acc = mysqli_fetch_array($loc_pin)){
		// $loc_code=mysqli_query($link1,"SELECT location_code,locationname FROM  location_master where location_code='".$loc_pin_acc['location_code']."' ");
	 	// $loc_info = mysqli_fetch_array($loc_code);
			   echo "<option value='".$loc_pin_acc['location_code']."'>";
			   echo $loc_pin_acc['locationname']."--</option>";
		 }
	 }
	  //echo "<option value='RVSASP0930' >TestASPcandour</option>";
     echo "</select>";
	 

}

/// ASP pincode based city- state & area get - ravi 17-12-21

if($_POST['Locpinstate']!="" || $_POST['cmLocSt']=="1"){
    echo "<select  name='locationstate' id='locationstate' class='form-control required' onchange='get_citydiv();'>";
	if($_POST['Locpinstate']==""){
		echo "<option value=''>--Please Select--</option>";
		$loc_code=mysqli_query($link1,"select stateid, state from state_master where countryid='1' order by state");
		while($loc_info= mysqli_fetch_array($loc_code)){
			echo "<option value='".$loc_info['stateid']."'>";
			echo $loc_info['state']."</option>";
		}
	}else{
		$pin_query="SELECT stateid FROM  pincode_master where statusid='1' and pincode='".$_POST['Locpinstate']."' group by stateid ";
		$loc_pin=mysqli_query($link1,$pin_query);
		$loc_pin_acc= mysqli_fetch_array($loc_pin);
		
		if(mysqli_num_rows($loc_pin)==0){
			echo "<option value=''>--Please Select--</option>";
			$loc_code=mysqli_query($link1,"select stateid, state from state_master where countryid='1' order by state");
			while($loc_info= mysqli_fetch_array($loc_code)){
				echo "<option value='".$loc_info['stateid']."'>";
				echo $loc_info['state']."</option>";
			}
		}else{
			///// get state details
			$loc_code = mysqli_query($link1,"SELECT stateid,state FROM  state_master where stateid='".$loc_pin_acc['stateid']."'");
			$loc_info = mysqli_fetch_array($loc_code);
			echo "<option value='".$loc_info['stateid']."'>";
			echo $loc_info['state']."</option>";
		}
	}
     echo "</select>";
}


if($_POST['Locpincity']!="" || $_POST['cmLocSt']=="2"){

	echo "<select  name='locationcity' id='locationcity' class='form-control required'  onChange='get_cityArea()'>";
	if($_POST['Locpincity']==""){
		echo "<option value=''>--Please Select--</option>";
	}else{
		$pin_city="SELECT cityid FROM  pincode_master where statusid='1' and pincode='".$_POST['Locpincity']."'  ";
		$loc_pin_city=mysqli_query($link1,$pin_city);
		while($loc_city_pin = mysqli_fetch_array($loc_pin_city)){
			$loc_city=mysqli_query($link1,"SELECT cityid,city FROM  city_master where cityid='".$loc_city_pin['cityid']."'");
			$row_city= mysqli_fetch_array($loc_city);
			echo "<option value='".$row_city['cityid']."'>";
			echo $row_city['city']."</option>";
		}
	}
     echo "</select>";
}

//////////////////  Check duplicate location code for selected location
if($_POST['loccode']){
	//echo "SELECT locationid from location_master where location_code='".strtoupper($_POST['loccode'])."'";
	$loccode_query="SELECT locationid from location_master where location_code='".strtoupper($_POST['loccode'])."'";
	$loccode_res=mysqli_query($link1,$loccode_query);
	$loccode_row = mysqli_num_rows($loccode_res);
	echo $loccode_row;
	
}


if($_POST['Locpinarea']!="" || $_POST['cmLocSt']=="3"){
    echo "<select  name='locationarea' id='locationarea' class='form-control'>";
	if($_POST['Locpinarea']==""){
		echo "<option value=''>--Please Select--</option>";
	}else{
	
		$pin_query="SELECT stateid FROM  pincode_master where statusid='1' and pincode='".$_POST['Locpinarea']."' group by stateid ";
		$loc_pin=mysqli_query($link1,$pin_query);
		$loc_pin_acc= mysqli_fetch_array($loc_pin);
		
		if(mysqli_num_rows($loc_pin)==0){
			echo "<option value=''>--Please Select--</option>";
		}else{ 
			if($_POST['city_id']){ $conti= " and cityid='".$_POST['city_id']."'";}else{ $conti= "";}
			$pin_city="SELECT area FROM  pincode_master where statusid='1' and pincode='".$_POST['Locpinarea']."' ".$conti;
			$loc_pin_city=mysqli_query($link1,$pin_city);
			while($loc_area_pin = mysqli_fetch_array($loc_pin_city)){ 
				echo "<option value='".$loc_area_pin['area']."'>";
				echo $loc_area_pin['area']."</option>";
			}
		}
	}
     echo "</select>";

}


if($_POST['disPnaPoF']){
   	$user_details = mysqli_fetch_array(mysqli_query($link1,"select okqty from client_inventory where location_code='".$_POST['sess_code']."' and partcode='".$_POST['disPnaPoF']."' "));
		if($user_details['okqty'] != ""){
			$rtn_str = $user_details['okqty'];
		}else{
			$rtn_str = 0;
		}   	
   	
   	$user_details1 = mysqli_fetch_array(mysqli_query($link1,"select okqty from client_inventory where location_code = '".$_POST['loc_code']."' and partcode='".$_POST['disPnaPoF']."' "));
		if($user_details1['okqty'] != ""){
			$rtn_str1 = $user_details1['okqty'];
		}else{
			$rtn_str1 = 0;
		}   	
   	
   	$user_details2 = mysqli_fetch_array(mysqli_query($link1,"select location_price from partcode_master where partcode='".$_POST['disPnaPoF']."'"));
		if($user_details2['location_price'] != ""){
			$loc_price = $user_details2['location_price'];
		}else{
			$loc_price = 0;
		}   	
   	
   	
   	echo $rtn_str."~".$rtn_str1."~".$_POST['disPnaPoF']."~".$loc_price."~".$_POST['indx'];
}

////////////////////////// okqty and consign qty selection /////////////////////
if($_POST['stock_type_val']){
	$asp_qty="SELECT ".$_POST['stock_type_val']." FROM client_inventory WHERE partcode='".$_POST['part']."' AND location_code='".$_POST['frm_code']."' ";
	$asp_qty_res=mysqli_fetch_array(mysqli_query($link1,$asp_qty));
	
    $loc_qty="SELECT ".$_POST['stock_type_val']." FROM client_inventory WHERE partcode='".$_POST['part']."' AND location_code='".$_POST['ses_code']."' ";
	$loc_qty_res=mysqli_fetch_array(mysqli_query($link1,$loc_qty));
	
	
	if($asp_qty_res['okqty'] > 0){
			$asp_val = $asp_qty_res['okqty'];
		}else{
			$asp_val = 0;
		}
		if($loc_qty_res['okqty'] > 0){
			$loc_val = $loc_qty_res['okqty'];
		}else{
			$loc_val = 0;
		}
	
	/*if($_POST['stock_type_val'] == "okqty"){
		if($asp_qty_res['okqty'] > 0){
			$asp_val = $asp_qty_res['okqty'];
		}else{
			$asp_val = 0;
		}
		if($loc_qty_res['okqty'] > 0){
			$loc_val = $loc_qty_res['okqty'];
		}else{
			$loc_val = 0;
		}
	}else{
		if($asp_qty_res['consqty'] > 0){
			$asp_val = $asp_qty_res['consqty'];
		}else{
			$asp_val = 0;
		}
		if($loc_qty_res['consqty'] > 0){
			$loc_val = $loc_qty_res['consqty'];
		}else{
			$loc_val = 0;
		}
	}
	*/
	echo $asp_val."~".$loc_val."~".$_POST['index'];
}
if($_POST['distdetails']){



	  $level_query="SELECT stateid,cityid, createdate FROM location_master where location_code='".$_POST['distdetails']."' and  statusid ='1'";
$check2=mysqli_query($link1,$level_query);
$abc = mysqli_fetch_array($check2);
$city= getAnyDetails($abc['cityid'],"city","cityid","city_master",$link1);
$state= getAnyDetails($abc["stateid"],"state","stateid","state_master",$link1);

$cretedate= explode(" ",$abc['createdate']);

echo $state."~".$city."~".$cretedate[0];

}
////////////////////////// ASP by City

if($_POST['locbycity']){
	echo "<select  name='asc_name' id='asc_name' class='form-control'><option value=''>All</option>";
	$location_query = "SELECT locationname, location_code FROM location_master where statusid='1' and cityid='".$_POST['locbycity']."' order by locationname";
    $loc_res=mysqli_query($link1,$location_query);
    while($loc_info = mysqli_fetch_array($loc_res)){
    	echo "<option value='".$loc_info['location_code']."'>";
        echo $loc_info['locationname']." ".$loc_info['location_code']."</option>";
	}
    echo "</select>";
}


/////////////////////////CitY For Appointment////////////////////////////////////
if($_POST['stateascapp']){



     echo "<select  name='asc_city' id='asc_city' class='form-control required'   onChange='get_cityASP()' required><option value=''>--Please Select--</option>";



     $city_query="SELECT cityid, city FROM city_master where stateid='".$_POST['stateascapp']."' group by city order by city";



     $city_res=mysqli_query($link1,$city_query);



     while($row_city = mysqli_fetch_array($city_res)){



           echo "<option value='".$row_city['cityid']."'>";



           echo $row_city['city']."</option>";



	 }



           echo "<option value='Others'>Others</option>";



     echo "</select>";



}
if($_POST['assignpincode']!=""){

	echo "<select  name='rep_location' id='rep_location' class='form-control required'>";
	if($_POST['assignpincode']==""){
		echo "<option value=''>--Please Select--</option>";
	}else{
		//and location_code='".$_SESSION['asc_code']."'
		$pin_loc="SELECT location_code FROM  location_pincode_access where statusid='1' and pincode='".$_POST['assignpincode']."' and location_code  in (select location_code from location_master where locationtype='ASP') group by location_code  order by id desc limit 1";
		$loc_pin=mysqli_query($link1,$pin_loc);
		//echo "<option value=''>--Please Select--</option>";
		while($loc_cpin = mysqli_fetch_array($loc_pin)){
			//$loc_city=mysqli_query($link1,"SELECT cityid,city FROM  city_master where cityid='".$loc_cpin['cityid']."' ");
			//$row_city= mysqli_fetch_array($loc_city);
			echo "<option value='".$loc_cpin['location_code']."'>";
			echo $loc_cpin['location_code']." | ".getAnyDetails($loc_cpin['location_code'],"locationname","location_code","location_master",$link1)."</option>";
		}
	}
     echo "</select>";
	// adding option for ENG assign
	 echo "Assign Engineer : <select  name='assign_eng' id='assign_eng' class='form-control '>";
	if($_POST['assignpincode']==""){
		echo "<option value=''>--Please Select--</option>";
	}else{
		//and location_code='".$_SESSION['asc_code']."'
		$pin_loc="SELECT location_code FROM  location_pincode_access where statusid='1' and pincode='".$_POST['assignpincode']."' and location_code  in (select userloginid from locationuser_master where type='Engineer') group by location_code  order by id desc limit 1";
		$loc_pin=mysqli_query($link1,$pin_loc);
		//echo "<option value=''>--Please Select--</option>";
		while($loc_cpin = mysqli_fetch_array($loc_pin)){
			//$loc_city=mysqli_query($link1,"SELECT cityid,city FROM  city_master where cityid='".$loc_cpin['cityid']."' ");
			//$row_city= mysqli_fetch_array($loc_city);
			echo "<option value='".$loc_cpin['location_code']."'>";
				echo $loc_cpin['location_code']." | ".getAnyDetails($loc_cpin['location_code'],"locusername","userloginid","locationuser_master",$link1)."</option>";
		}
	}
     echo "</select>";
}
/////////////////////////////////////////////////check duplicate serial replaced///////////////////////////////////
if($_POST['checkSerialDupliReplBtr']){
	$res_serial = mysqli_query($link1,"select replace_serial_no, serial_no, job_no from replacement_data where replace_serial_no='".strtoupper(trim($_POST['checkSerialDupliReplBtr']))."' and status != '12' ");
	$count_serial = mysqli_num_rows($res_serial);
	$serial_data=mysqli_fetch_array($res_serial);
	if($count_serial > 0){
				
		echo $serial_data['replace_serial_no']."~".$serial_data['serial_no']."~".$serial_data['job_no']."~".$_POST['checkSerialDupliReplBtr'];
	}else{
		echo "0~0~0"."~".$_POST['checkSerialDupliReplBtr'];
	}
}
if($_POST['replacemodel_eng']){
	
	//echo $_POST['replacemodel_eng'];
	//exit;
	if($_POST['app_reason']=="Replacement" || $_POST['app_reason']=="SReplacement"){
	$part_cat="UNIT";
	}else{
	$part_cat="BOX','UNIT";
	}
  //$sql_rplc = "SELECT ci.partcode, pm.part_name, pm.customer_price, ci.okqty, pm.vendor_partcode FROM client_inventory ci, partcode_master pm where ci.partcode=pm.partcode and pm.part_category='".$part_cat."' and   pm.model_id like '%".$_POST['model_id']."%' and  ci.okqty > 0  and ci.location_code='".$_POST['locationcode']."' order by pm.part_name";
   $sql_rplc = "SELECT ci.partcode, pm.part_name, pm.customer_price, ci.okqty, pm.vendor_partcode FROM client_inventory ci, partcode_master pm where ci.partcode=pm.partcode and pm.part_category in('".$part_cat."') and   pm.brand_id='".$_POST['brand_id']."' and   pm.model_id like '%".$_POST['modelcode1']."%' and  ci.okqty > 0  and ci.location_code='".$_POST['locationcode']."' order by pm.part_name";
 // echo $sql_rplc ;
	
	//echo "<select name='rep_part' class='required form-control' id='rep_part' style='width:150px;' onChange='return getstock(this.value);'><option value=''>Please Select Type</option>";
	echo "<select name='rep_part' class='required form-control' id='rep_part' style='width:150px;' ><option value=''>Please Select Type</option>";

	$res_rplc = mysqli_query($link1,$sql_rplc);

	while($row_rplc = mysqli_fetch_array($res_rplc)){

		echo "<option value='".$row_rplc[0]."'>";

		echo $row_rplc[1].'('.$row_rplc[4].')'." | ".$row_rplc[0]."</option>";

	}

	echo "</select>";

}

if($_POST['checkserialpending']){
$job_find="select job_no  from jobsheet_data where imei='".$_POST['checkserialpending']."' and status not in ('6','10','12','11') order by job_id desc";
$result_find=mysqli_query($link1,$job_find);
$result_find_count=mysqli_num_rows($result_find);	
$result_find_arr=mysqli_fetch_array($result_find);	
$job_no_d=$result_find_arr[0];
if($result_find_count > 0){
	$error_msg = "Error details1: There is already a call open on this serial." . $result_find_arr['job_no'] . ".";
}else{
    $error_msg = "";
}
 echo $error_msg;
}


?>