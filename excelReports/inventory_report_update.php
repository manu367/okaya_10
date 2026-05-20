<?php 
require_once("../includes/config.php");



	
 $intransitd=mysqli_query($link1,"SELECT b.qty,b.partcode,b.to_location  FROM billing_master a, billing_product_items b WHERE ( a.status =  '2' OR a.status =  '3')  AND a.challan_no = b.challan_no AND a.po_type IN ('PNA','PO') ");
				while ($intransit_data=mysqli_fetch_array($intransitd)){
				  if(mysqli_num_rows(mysqli_query($link1,"select partcode from client_inventory where partcode='".$intransit_data['partcode']."' and location_code='".$intransit_data['to_location']."'"))>0){
			 ///if product is exist in inventory then update its qty 
			$result=mysqli_query($link1,"update client_inventory set  	in_transit= in_transit+'".$intransit_data['qty']."',updatedate='".$datetime."' where partcode='".$intransit_data['partcode']."' and location_code='".$intransit_data['to_location']."'");
		  }		
		  else{			
			 //// if product is not exist then add in inventory
			 $result=mysqli_query($link1,"insert into client_inventory set location_code='".$pomaster['from_code']."',partcode='".$podata_row['partcode']."',in_transit='".$_POST[$post_dispqty]."',updatedate='".$datetime."'");
		  }	
				
	
	}

