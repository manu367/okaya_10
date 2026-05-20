<?php
include("../includes/config.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title><?=siteTitle?></title>
  <meta charset="utf-8">
  <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <script type="text/javascript">
    <!--
    var b_timer = null; // blink timer
    var b_on = true; // blink state
    var blnkrs = null; // array of spans
    function blink() {
    var tmp = document.getElementsByTagName("span");
    if (tmp) {
    blnkrs = new Array();
    var b_count = 0;
    for (var i = 0; i < tmp.length; ++i) {
    if (tmp[i].className == "blink") {
    blnkrs[b_count] = tmp[i];
    ++b_count;
    }
    }
    // time in m.secs between blinks
    // 500 = 1/2 second
    blinkTimer(500);
    }
    }
    function blinkTimer(ival) {
    if (b_timer) {
    window.clearTimeout(b_timer);
    b_timer = null;
    }
    blinkIt();
    b_timer = window.setTimeout('blinkTimer(' + ival + ')', ival);
    }
    function blinkIt() {
    for (var i = 0; i < blnkrs.length; ++i) {
    if (b_on == true) {
    blnkrs[i].style.visibility = "hidden";
    }
    else {
    blnkrs[i].style.visibility = "visible";
    }
    }
    b_on =!b_on;
    }
    //-->
    </script>
    <style type="text/css">
<!--
.style1 {font-family: Papyrus}
-->
</style>
</head>
<body onLoad="blink();">
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
 
   <!-- <div class="col-sm-9">
       <span> <img src="../images/Banner.png" width="108%"></span>
         <br>
      
   </div>-->
    </div>
  </div>

<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
