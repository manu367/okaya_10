<?php

	///email///
$tomail = "$email";
$subject = "Po Dispatch";
$message = "
<html>
<head>
<title>HTML email</title>
</head>
<body>
<pre>
To,						
$name						
						
						
Your Invoice has been generated for invoice no: $invice_no and it is available in VSMS. You are requested to take the print out and complete the stock-in process in VSMS.

With Best Regards
For VELO Services Pvt Ltd.
						

						
					
</pre>
</body>
</html>
";

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
//$headers .= 'From: <techcarekolkata@hitech-mobiles.com>'. "\r\n";
//$headers .="CC:sikhar.chandra@candoursoft.com";


mail($tomail,$subject,$message,$headers);	
echo "successful";
////	

?>