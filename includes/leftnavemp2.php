<?php 
if($_SESSION['id_type']=="ASP" || $_SESSION['id_type']=="CC" || $_SESSION['id_type']=="WH" || $_SESSION['id_type']=="DIST" || $_SESSION['id_type']=="L3"){
$arr_nav = array();	
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">

    <style> 
        a:link { 
            text-decoration: none; 
        } 
        a:hover { 
            text-decoration: none; 
        } 
    </style> 
</head>
<?php
///// get tab rights array
$tab_array = getTabRights($_SESSION['userid'],$link1);
function job_details($asc,$status,$type,$loc_type,$link1){
//echo "select count(job_id) as job_count from jobsheet_data where status='".$status."'  and eng_id='".$eng_name."' ";
if($loc_type=='CC'){
$asp_loc="and location_code='".$asc."'";

}else {

$asp_loc="and current_location='".$asc."'";
}
if($type=="UNREC"){
$st="status='1' and doa_rej_rmk=''";
} else if($type=="APP"){
$st="status='56' ";
}else{
$st="status in ('3','50','51','52','7','5','2') ";
}
//echo "select count(job_id) as job_count from jobsheet_data where ".$st."  '".$asp_loc."'";
$res_eng_p = mysqli_query($link1,"select count(job_id) as job_count from jobsheet_data where ".$st."  ".$asp_loc."");

$row_count = mysqli_fetch_array($res_eng_p);
if($row_count['job_count']!=''){
$count_job=$row_count['job_count'];

}else{
$count_job=0;
}

return $count_job;
}
////  we check if location user is logging
if($_SESSION['locusertype']=="LOCATION USER"){
	$tabstr = " and tabid  in ('".implode("','",array_keys($tab_array))."')";
}else{//// if location is logging
	$tabstr = "";
}
/////// default tab rights
if($_SESSION['id_type']=="ASP"){ 
	$tab_for=" tabfor in ('ASP','ASPL3')";
} elseif($_SESSION['id_type']=="L3" || $_SESSION['id_type']=="L4"){ 
	$tab_for=" tabfor in ('ASP','L3')";
} elseif($_SESSION['id_type']=="WH"){ 
	$tab_for=" tabfor in ('WH')";
}
elseif($_SESSION['id_type']=="CC"){ 
	$tab_for=" tabfor in ('CC')";
} else{ 
	$tab_for=" tabfor in ('')";
}
if($menutab=="H"){
?>
<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">CRM</a>
    </div>
    <ul class="nav navbar-nav ">
      <li <?php if($_REQUEST['pid']=="home" && $_REQUEST['hid']=="home"){ echo "class='active'";}?>><a href="home2.php?pid=home&hid=home"><i class="fa fa-home fa-lg"></i> Home</a></li>
      	<?php
		$arr_nav[] = "home";
		///////////////////// select main tab ///////// 
	    $res_maintab=mysqli_query($link1,"select maintabname , maintabicon from tab_master where status = '1' and ".$tab_for." ".$tabstr." group by maintabname order by maintabseq")or die("error1".mysqli_error($link1));
        $num_maintab=mysqli_num_rows($res_maintab);
        	if ($num_maintab > 0) { ///// start main tab if
				$i=1;
                while($row_maintab=mysqli_fetch_array($res_maintab)){ ////// start main tab while 
				$icon = $row_maintab['maintabicon'];
		?>
      <li class="dropdown <?php if($_REQUEST['hid'] == $row_maintab['maintabname']){ echo "active";} ?>"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa <?=$icon?> fa-lg"></i> <?=$row_maintab['maintabname']?> <span class="caret"></span></a>
        <ul class="dropdown-menu ">
        <?php 
			   //////////////////// select sub tab of main tab ///////////////
	           $res_subtab=mysqli_query($link1,"select tabid , subtabname , subtabicon , filename from tab_master where status = '1' and  maintabname = '".$row_maintab['maintabname']."' and ".$tab_for." order by subtabseq");
               $num_subtab=mysqli_num_rows($res_subtab);
               if ($num_subtab > 0) { /////start sub tab if
                    while($row_subtab=mysqli_fetch_array($res_subtab)){ /////// start sub tab while
					  ///// check permission of this tab is given or not
					  if($tab_array[$row_subtab['tabid']] == 1 || $_SESSION['locusertype']=="LOCATION"){
						  $arr_nav[] = $row_subtab['tabid'];
			   ?>
        <a href="<?=$row_subtab['filename'];?>.php?pid=<?=$row_subtab['tabid']?>&hid=<?=$row_maintab['maintabname']?>" style="color:white">  <li <?php if($_REQUEST['pid']==$row_subtab['tabid'] && $_REQUEST['hid'] == $row_maintab['maintabname']){ echo "class='active'";} ?>><i class="fa <?=$row_subtab['subtabicon']?> fa-lg"></i>&nbsp;<?=$row_subtab['subtabname']?></li></a>
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
			   <?php if($_SESSION['id_type']  == 'ASP')  { $arr_nav[] = "chngpwdasp";?>
			  <li <?php if($_REQUEST['pid']=="chngpwdasp" && $_REQUEST['hid']=="chngpwd"){ echo "class='active'";} ?> style="font-size:14px">               
                   <a  href="../asp/changepassword.php?pid=chngpwdasp&hid=chngpwd"><i class="fa fa-user-secret fa-lg"></i> Change Password
                 	</a> 
                 </li><?php }  else if ($_SESSION['id_type']  == 'WH') { $arr_nav[] = "chngpwdwh";?>
				 <li <?php if($_REQUEST['pid']=="chngpwdwh" && $_REQUEST['hid']=="chngpwd"){ echo "class='active'";} ?> style="font-size:14pX">      
                   <a href="../wh/changepassword.php?pid=chngpwdwh&hid=chngpwd"><i class="fa fa-user-secret fa-lg"></i> Change Password
                 	</a> 
                 </li><?php }?>
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
    <!--<h4><img src="../img/inner_logo.png" width="200"/></h4>--><div style="padding: 5px; text-align:center; background-color:#FFFFFF"><img style="width:76%;display:block;margin:0px auto;max-height:100px;" src="../images/blogo.png"/></div>
    <i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>
        <div class="menu-list">
            <br>
            <?php if (isset($_SESSION['uname'])) { ?>
               <!--<i class="fa fa-user fa-lg"></i>&nbsp;&nbsp;<span>-->Welcome <?php echo $_SESSION['uname']."  (".$_SESSION['userid'].")";?><br/>
               <?php echo date("l, F dS Y");?></span>
             <?php } ?><br/><br/>
            <ul id="menu-content" class="menu-content collapse out">
                <a href="home2.php?pid=home&hid=home" style="color:white">   <li <?php if($_REQUEST['pid']=="home" && $_REQUEST['hid']=="home"){ echo "class='active'";}?>>
               
                  <i class="fa fa-home fa-lg"></i> Home
                  
                </li></a>
              <?php
			  $arr_nav[] = "home";
			  ///////////////////// select main tab ///////// 
	          $res_maintab=mysqli_query($link1,"select maintabname , maintabicon from tab_master where status = '1' and ".$tab_for." ".$tabstr." group by maintabname order by maintabseq")or die("error1".mysqli_error($link1));
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
	           $res_subtab=mysqli_query($link1,"select tabid , subtabname , subtabicon , filename from tab_master where status = '1' and  maintabname = '".$row_maintab['maintabname']."' and ".$tab_for." order by subtabseq");
               $num_subtab=mysqli_num_rows($res_subtab);
               if ($num_subtab > 0) { /////start sub tab if
                    while($row_subtab=mysqli_fetch_array($res_subtab)){ /////// start sub tab while
					 ///// check permission of this tab is given or not
					  if($tab_array[$row_subtab['tabid']] == 1 || $_SESSION['locusertype']=="LOCATION"){
						  $arr_nav[] = $row_subtab['tabid'];
			   ?>
               <a href="<?=$row_subtab['filename'];?>.php?pid=<?=$row_subtab['tabid']?>&hid=<?=$row_maintab['maintabname']?>" style="color:white">     <li <?php if($_REQUEST['pid']==$row_subtab['tabid'] && $_REQUEST['hid'] == $row_maintab['maintabname']){ echo "class='active'";} ?>>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa <?=$row_subtab['subtabicon']?> fa-lg"></i>&nbsp;<?=$row_subtab['subtabname']?></li></a>
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
			   <?php if($_SESSION['id_type']  == 'ASP' || $_SESSION['id_type']  == 'CC')  { $arr_nav[] = "chngpwdasp";?>
			<a  href="../asp/changepassword.php?pid=chngpwdasp&hid=chngpwd" style="color:white">  <li <?php if($_REQUEST['pid']=="chngpwdasp" && $_REQUEST['hid']=="chngpwd"){ echo "class='active'";} ?> style="font-size:14px">               
                   <i class="fa fa-user-secret fa-lg" style="color:white"></i> Change Password
                 
                 </li>	</a> <?php }  else if ($_SESSION['id_type']  == 'WH') { $arr_nav[] = "chngpwdwh";?>
				   <a href="../wh/changepassword.php?pid=chngpwdwh&hid=chngpwd" style="color:white"> <li <?php if($_REQUEST['pid']=="chngpwdwh" && $_REQUEST['hid']=="chngpwd"){ echo "class='active'";} ?> style="font-size:14pX">      
                <i class="fa fa-user-secret fa-lg"></i> Change Password
                 	
                 </li></a> <?php }?>
              <a href="../logout.php" style="color:white">   <li>
                 
                  <i class="fa fa-power-off fa-lg"></i> Logout
                 
                </li> </a>
				<li>
                  <i></i>
                </li>
				<?php if($_SESSION['id_type']  == 'ASP')  {?>
				<li style="margin-left:10px;">
                  <!--<a href="">-->
                  <i class="fa fa-circle fa-lg" style="color:#FF0000"></i> Total Unrecognized Call<span style="display: inline-block;padding-left: 10px;padding-right: 10px;vertical-align: middle;float: right;"><?=job_details($_SESSION['asc_code'],'1',"UNREC",$_SESSION['id_type'],$link1);?></span>
                  <!--</a>-->
                </li>
				<li style="margin-left:10px;">
                  <!--<a href="">-->
                  <i class="fa fa-circle fa-lg" style="color:#FFFF00"></i> Not Assinged Call <span style="display: inline-block;padding-left: 10px;padding-right: 10px;vertical-align: middle;float: right;"><?=job_details($_SESSION['asc_code'],'56',"APP",$_SESSION['id_type'],$link1);?></span>
                  <!--</a>-->
                </li>
				<li style="margin-left:10px;">
                  <!--<a href="">-->
                  <i class="fa fa-circle fa-lg" style="color:#00FF00"></i> Work In Progress <span style="display: inline-block;padding-left: 10px;padding-right: 10px;vertical-align: middle;float: right;"><?=job_details($_SESSION['asc_code'],'56',"PEN",$_SESSION['id_type'],$link1);?></span>
                  <!--</a>-->
                </li>
				<?php }?>
                <?php if($_SESSION['id_type']  == 'CC')  {?>
                <?php /*?><li style="margin-left:10px;">
                  <!--<a href="">-->
                  <i class="fa fa-circle fa-lg" style="color:#FF0000"></i> Total Call Login<span style="display: inline-block;padding-left: 10px;padding-right: 10px;vertical-align: middle;float: right;"><?php $tcl_cnt = mysqli_num_rows(mysqli_query($link1,"SELECT job_id FROM jobsheet_data WHERE location_code = '".$_SESSION['asc_code']."'")); echo $tcl_cnt;?></span>
                  <!--</a>-->
                </li>
				<li style="margin-left:10px;">
                  <!--<a href="">-->
                  <i class="fa fa-circle fa-lg" style="color:#00FF00"></i> Total Share Call <span style="display: inline-block;padding-left: 10px;padding-right: 10px;vertical-align: middle;float: right;"><?php $tsc_cnt = mysqli_num_rows(mysqli_query($link1,"SELECT job_id FROM jobsheet_data WHERE created_by = '".$_SESSION['userid']."'")); echo $tsc_cnt;?></span>
                  <!--</a>-->
                </li>
                <li style="margin-left:10px;">
                  <!--<a href="">-->
                  <i class="fa fa-circle fa-lg" style="color:#FFFF00"></i> Total Share Call% <span style="display: inline-block;padding-left: 10px;padding-right: 10px;vertical-align: middle;float: right;"><?php $tsc_per = round(($tsc_cnt / $tcl_cnt)*100); echo $tsc_per;?></span>
                  <!--</a>-->
                </li><?php */?>
                <?php }?>
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