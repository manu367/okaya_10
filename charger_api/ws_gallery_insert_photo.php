<?php
 $target_path ="../../charger_pdf/";
 $target_path = $target_path.basename($_FILES['uploadedfile']['name']);
 if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'],$target_path)){
   // echo "uploaded";
 }
 else{
   // echo "uploaded error";
 }

 ?>