<?php

include("../includes/config.php");

?>

<!DOCTYPE html>

<html lang="en">

<head>

  <title><?=siteTitle?></title>

  <meta charset="utf-8">

  <link rel="shortcut icon" href="../img/titleimg.png" type="image/png">

  <meta name="viewport" content="width=device-width, initial-scale=1">

  <script src="../js/jquery.js"></script>

 <link href="../css/font-awesome.min.css" rel="stylesheet">

 <link href="../css/abc.css" rel="stylesheet">

 <link rel="stylesheet" href="../css/bootstrap.min.css">

 <script src="../js/bootstrap.min.js"></script>

 <link href="../css/abc2.css" rel="stylesheet">

</head>

<body>

<div class="container-fluid">

  <div class="row content">

	<?php 

    include("../includes/leftnavemp2.php");

    ?>

   

      <div class="col-sm-9">
       <span> <img src="../images/Banner.png" width="108%"></span>
         <br>
      
   </div>

  </div>

</div>

<?php

include("../includes/footer.php");

include("../includes/connection_close.php");

?>

</body>

</html>
