<?php 
if($_SESSION['id_type']=="admin"){
	$arr_nav = array();
///// get tab rights array
$tab_array = getTabRights($_SESSION['userid'],$link1);
if($menutab=="H"){
?>
<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">CRM</a>
    </div>
    <ul class="nav navbar-nav ">
      <li <?php if($_REQUEST['pid']=="homeadmin" && $_REQUEST['hid']=="home"){ echo "class='active'";} if($_SESSION['utype']=="ASP"){ $hmdir="asp";}else{ $hmdir="admin";}?>><a href="../<?=$hmdir?>/home2.php?pid=homeadmin&hid=home"><i class="fa fa-home fa-lg"></i> Home</a></li>
      	<?php
		$arr_nav[] = "homeadmin";
	    $res_maintab=mysqli_query($link1,"select maintabname , maintabicon from tab_master where status = '1' and tabfor='admin' group by maintabname order by maintabseq")or die("error1".mysqli_error($link1));
        $num_maintab=mysqli_num_rows($res_maintab);
        	if ($num_maintab > 0) {
				$i=1;
                while($row_maintab=mysqli_fetch_array($res_maintab)){ ////// start main tab while 
				$icon = $row_maintab['maintabicon'];
		?>
      <li class="dropdown <?php if($_REQUEST['hid'] == $row_maintab['maintabname']){ echo "active";} ?>"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa <?=$icon?> fa-lg"></i> <?=$row_maintab['maintabname']?> <span class="caret"></span></a>
        <ul class="dropdown-menu ">
        <?php
	           $res_subtab=mysqli_query($link1,"select tabid , subtabname , subtabicon , filename from tab_master where status = '1' and tabfor='admin' and  maintabname = '".$row_maintab['maintabname']."' order by subtabseq");
               $num_subtab=mysqli_num_rows($res_subtab);
               if ($num_subtab > 0) {
                    while($row_subtab=mysqli_fetch_array($res_subtab)){ /////// start sub tab while
					  ///// check permission of this tab is given or not
					  if($tab_array[$row_subtab['tabid']] == 1){
						  $arr_nav[] = $row_subtab['tabid'];
			   ?>
          <li <?php if($_REQUEST['pid']==$row_subtab['tabid'] && $_REQUEST['hid'] == $row_maintab['maintabname']){ echo "class='active'";} ?>><a href="<?=$row_subtab['filename'];?>.php?pid=<?=$row_subtab['tabid']?>&hid=<?=$row_maintab['maintabname']?>"><i class="fa <?=$row_subtab['subtabicon']?> fa-lg"></i>&nbsp;<?=$row_subtab['subtabname']?></a></li>
          <?php 
					  }
					}///// end sub tab while
			   }//// end sub tab if
		  ?>
        </ul>
        </li>
        <?php
			    $i++;
				}////// end main tab while
			  }//// end main tab if
			  ?>
       <li>
          <a href="../logout.php">
          <i class="fa fa-power-off fa-lg"></i> Logout
          </a>
        </li>       
    </ul>
  </div>
</nav>
<?php 
}else{
?>
<div class="col-sm-3 nav-side-menu" style="padding-left:0px;padding-right:0px;border: 1px solid #2e353d;">
    <!--<h4><img src="../img/inner_logo.png" width="200"/></h4>--><div class="brand" style="padding: 5px; text-align:center;background-color:#FFFFFF"><img src="../images/blogo.png" style="width:76%;display:block;margin:0px auto;max-height:100px;" /></div>
    <i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>
        <div class="menu-list">
            <?php  
			if (isset($_SESSION['uname'])) { ?>
               <!--<i class="fa fa-user fa-lg"></i>&nbsp;&nbsp;--><span>Welcome <?php echo $_SESSION['uname']."  (".$_SESSION['userid'].")";?><br/>
               <?php echo date("l, F dS Y");?></span>
             <?php } ?><br/><br/>
            <ul id="menu-content" class="menu-content collapse out">
                <li <?php if($_REQUEST['pid']=="home" && $_REQUEST['hid']=="home"){ echo "class='active'";} if($_SESSION['utype']=="ASP"){ $hmdir="asp";}else{ $hmdir="admin";}?>>
                  <a href="../<?=$hmdir?>/home2.php?pid=homeadmin&hid=home">
                  <i class="fa fa-home fa-lg"></i> Home
                  </a>
                </li>
              <?php
			   $arr_nav[] = "homeadmin";
			  ///////////////////// select main tab ///////// 
	          $res_maintab=mysqli_query($link1,"select maintabname , maintabicon from tab_master where status = '1' and tabfor='admin' group by maintabname order by maintabseq")or die("error1".mysqli_error($link1));
              $num_maintab=mysqli_num_rows($res_maintab);
              if ($num_maintab > 0) { ///// start main tab if
			  	$i=1;
                while($row_maintab=mysqli_fetch_array($res_maintab)){ ////// start main tab while 
				$icon = $row_maintab['maintabicon'];
			  ?>
                <li data-toggle="collapse" data-target="#products<?=$i?>" class="collapsed <?php if($_REQUEST['hid'] == $row_maintab['maintabname']){ echo "active";} ?>">
                  <a href="#"><i class="fa <?=$icon?> fa-lg"></i> <?=$row_maintab['maintabname']?> <span class="arrow"></span></a>
                </li>
                <ul <?php if($_REQUEST['hid'] == $row_maintab['maintabname']){ echo "class='collapsed'";}else{ echo "class='collapse'";} ?> id="products<?=$i?>">
               <?php 
			   //////////////////// select sub tab of main tab ///////////////
	           $res_subtab=mysqli_query($link1,"select tabid , subtabname , subtabicon , filename from tab_master where status = '1' and tabfor='admin' and  maintabname = '".$row_maintab['maintabname']."' order by subtabseq");
               $num_subtab=mysqli_num_rows($res_subtab);
               if ($num_subtab > 0) { /////start sub tab if
                    while($row_subtab=mysqli_fetch_array($res_subtab)){ /////// start sub tab while
					  ///// check permission of this tab is given or not
					  if($tab_array[$row_subtab['tabid']] == 1){
						  $arr_nav[] = $row_subtab['tabid'];
			   ?>
                  <a href="<?=$row_subtab['filename'];?>.php?pid=<?=$row_subtab['tabid']?>&hid=<?=$row_maintab['maintabname']?>" style="color:white; text-decoration: none; ">  <li <?php if($_REQUEST['pid']==$row_subtab['tabid'] && $_REQUEST['hid'] == $row_maintab['maintabname']){ echo "class='active'";} ?>>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa <?=$row_subtab['subtabicon']?> fa-lg"></i>&nbsp;<?=$row_subtab['subtabname']?></li></a>
               <?php 
					  }
					}///// end sub tab while
			   }//// end sub tab if
			   ?>
                </ul>
                <?php
			    $i++;
				}////// end main tab while
			  }//// end main tab if
			  ?>
                 <li>
                  <a href="../logout.php">
                  <i class="fa fa-power-off fa-lg"></i> Logout
                  </a>
                </li>
            </ul>
     </div>
</div>
<?php
}
if($_REQUEST["pid"]!=""){
	if(in_array($_REQUEST["pid"], $arr_nav)){
		/*	echo $_REQUEST["pid"];
		print_r($arr_nav);*/
	}else{
		session_destroy();
		$msg='3';
		header("Location:../index.php?msg=".$msg);
		exit;
	}
}
}else{
session_destroy();
$msg='3';
header("Location:../index.php?msg=".$msg);
exit;
}
?>