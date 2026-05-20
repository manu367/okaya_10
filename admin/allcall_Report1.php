<?php
require_once("../includes/config.php");
global $link1;
global $screenwidth;
$access_product = getAccessProduct($_SESSION['userid'],$link1);
$access_brand = getAccessBrand($_SESSION['userid'],$link1);

$data=["status"=>null, "region"=>null, "state"=>null, "location"=>null, "product"=>null, "brand"=>null, "model"=>null, "call_type"=>null];
$co=array_reduce([12,20,11,13,19,2,4,6],function ($carry,$item){
    if($item%2===0){
        $carry['even'][]=$item;
    }else{
        $carry['odd'][]=$item;
    }
    return $carry;
},["odd"=>[],"even"=>[]]);
if(isset($_POST['status_v'])){
    $data["status"]=$_POST['status_v'];
}
if(isset($_POST['region'])){
    $data["region"]=$_POST['region'];
}
$statestr="''";
if(isset($_POST['statename'])){
    $data["state"]=$_POST['statename'];
    $statestr=arraytoString($data['state']);
}
if(isset($_POST['locationname'])){
    $data["location"]=$_POST['locationname'];
}
function arraytoString($arr){
    $str = "";
    if(count($arr) > 0){
        $str = "'" . $arr[0] . "'";
        for($i=1; $i<count($arr); $i++){
            $str .= ",'" . $arr[$i] . "'";
        }
    }
    return $str;
}
$product = isset($_REQUEST['product']) ? $_REQUEST['product'] : [];
$brand   = isset($_REQUEST['brand']) ? $_REQUEST['brand'] : [];
$modelarray = isset($_REQUEST['model']) ? $_REQUEST['model'] : [];
$productstr = !empty($product) ? implode(",", $product) : "0";
$brandstr   = !empty($brand) ? implode(",", $brand) : "0";
$datatime=isset($_POST['daterange'])?$_POST['daterange']:date("Y-m-d");
// 2 array comparision using map
$array1 = [1, 2, 3];
$array2 = [1, 4, 3];
$result = array_map(function($a, $b) {
    return $a === $b;
}, $array1, $array2);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/abc.css" rel="stylesheet">
    <script src="../js/jquery.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../js/moment.js"></script>
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script type="text/javascript">
        $(document).ready(function() {
            $("#form1").validate();
        });
    </script>
    <script src="../js/frmvalidate.js"></script>
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <script type="text/javascript" src="../js/common_js.js"></script>
    <!-- Include Date Range Picker -->
    <script type="text/javascript" src="../js/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
    <!-- Include Date Picker -->
    <link rel="stylesheet" href="../css/datepicker.css">
    <!-- Include multiselect -->
    <script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
    <link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
    <script src="../js/bootstrap-datepicker.js"></script>
    <title><?=siteTitle?></title>
    <script type="text/javascript" language="javascript" >
        $(document).ready(function(){
            $('input[name="daterange"]').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        });
        $(document).ready(function() {
            $('#statename').multiselect({
                includeSelectAllOption: true,
                buttonWidth:"200"

            });
        });

        $(document).ready(function() {
            $('#locationname').multiselect({
                includeSelectAllOption: true,
                buttonWidth:"200"

            });
        });

        $(document).ready(function() {
            $('#product').multiselect({
                includeSelectAllOption: true,
                buttonWidth:"200"

            });
        });
        $(document).ready(function() {
            $('#brand').multiselect({
                includeSelectAllOption: true,
                buttonWidth:"200"

            });
        });
        $(document).ready(function() {
            $('#model').multiselect({
                includeSelectAllOption: true,
                buttonWidth:"200"

            });
        });

        $(document).ready(function() {
            $('#call_type').multiselect({
                includeSelectAllOption: true,
                buttonWidth:"200"

            });
        });

    </script>
<!--    <script src="../js/allreport.js"></script>-->
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
    <div class="row content">
        <?php
        include("../includes/leftnav2.php");
        ?>
        <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
            <h2 align="center"><i class="fa fa-volume-control-phone"></i> All Call</h2>
            <form class="form-horizontal mt-3" id="form1" name="form1" action="" method="post">
<!--                date-rande and statu-->
            <div class="form-group">
                    <div id= "dt_range" class="col-md-6">
                        <label class="col-md-5 control-label">Date Range</label>
                        <div class="col-md-6 input-append date" align="left">
                            <input type="text"
                                   name="daterange"
                                   id="date_rng" class="form-control"
                                   value="<?=$_REQUEST['daterange']?>" />
                        </div>
                    </div>

                <div class="col-md-6"><label class="col-md-5 control-label">Status:</label>
                        <div class="col-md-6" align="left">
                            <select class="form-control" name="status_v" onChange="document.form1.submit();">
                                <option value="">All</option>
                                <option value="1"
                                        <?php if(isset($_REQUEST['status_v']) && $_REQUEST['status_v']=='active'){ echo 'selected'; } ?>>
                                    Active
                                </option>
                                <option value="0"
                                        <?php if(isset($_REQUEST['status_v']) && $_REQUEST['status_v']=='pending'){ echo 'selected'; } ?>>
                                    Pending
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div id= "dt_range" class="col-md-6"><label class="col-md-5 control-label">Region</label>
                        <div class="col-md-6 input-append date" align="left">
                            <select name="region" class="form-control" onChange="document.form1.submit();">
                                <option value="">All</option>
                                <?php
                                $query = mysqli_query($link1,"SELECT zonename, zoneid FROM zone_master");
                                if($query){
                                    $count = mysqli_num_rows($query);
                                    if($count > 0){
                                        while($row = mysqli_fetch_assoc($query)){
                                            ?>
                                            <option value="<?php echo $row['zoneid']; ?>"
                                                    <?php if(isset($_REQUEST['region']) && $_REQUEST['region'] == $row['zoneid']){ echo 'selected'; } ?>>

                                                <?php echo $row['zonename']; ?>
                                            </option>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-5 control-label">State:</label>
                        <div class="col-md-6" align="left">
                            <select   name="statename[]" id="statename" multiple="multiple" class="form-control required" required  onChange="document.form1.submit();">
                                <?php
                                $query = mysqli_query($link1,"SELECT * FROM state_master ORDER BY state");

                                if($query){
                                    while($row = mysqli_fetch_assoc($query)){
                                        ?>
                                        <option value="<?php echo $row['stateid']; ?>"
                                                <?php
                                                if(isset($_REQUEST['statename']) && in_array($row['stateid'], $_REQUEST['statename'])){
                                                    echo "selected";
                                                }
                                                ?>>

                                            <?php echo $row['state']; ?>

                                        </option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
<!--                location and product-->

                <div class="form-group">
                    <div id= "dt_range" class="col-md-6">
                        <label class="col-md-5 control-label">Location</label>
                        <div class="col-md-6 input-append date" align="left">
                            <select name="locationname[]"
                                    multiple="multiple" id="locationname"
                                    onChange="document.form1.submit();"
                                    class="form-control"
                            >
                                <?php
                                $sql_loc = "SELECT locationname, location_code FROM location_master WHERE locationtype NOT IN ('WH','CC') 
                                                          AND statusid='1' AND stateid IN ($statestr) ORDER BY locationname";
                                $query = mysqli_query($link1, $sql_loc);
                                if($query){
                                    while($row = mysqli_fetch_assoc($query)){
                                        ?>
                                        <option value="<?php echo $row['location_code']; ?>"
                                                <?php
                                                if(isset($_REQUEST['locationname']) && in_array($row['location_code'], $_REQUEST['locationname'])){
                                                    echo "selected";
                                                }
                                                ?>>
                                            <?php echo $row['locationname']; ?>
                                        </option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="col-md-5 control-label">Product:</label>
                        <div class="col-md-6" align="left">
                            <select name="product[]"
                                    id="product"
                                    multiple="multiple" class="form-control required" required
                                    onChange="document.form1.submit();">
                                <?php
                                $sql = "SELECT product_id, product_name FROM product_master WHERE status='1' AND product_id IN ($access_product) ORDER BY product_name";
                                $query = mysqli_query($link1,$sql);
                                if($query){
                                    while($row = mysqli_fetch_assoc($query)){
                                        ?>
                                        <option value="<?php echo $row['product_id']; ?>"
                                                <?php if(isset($_REQUEST['product']) && in_array($row['product_id'],$_REQUEST['product'])) echo "selected"; ?> >
                                            <?php echo $row['product_name']; ?>
                                        </option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div id= "dt_range" class="col-md-6">
                        <label class="col-md-5 control-label">Brand</label>
                        <div class="col-md-6 input-append date" align="left">
                            <select name="brand[]"
                                    id="brand"  class="form-control required" required="required" multiple="multiple" onChange="document.form1.submit();">
                                <?php
                                $sql = "SELECT * FROM brand_master WHERE status = '1' AND brand_id IN ($access_brand) ORDER BY brand";
                                $query = mysqli_query($link1,$sql);
                                if($query){
                                    while($row = mysqli_fetch_assoc($query)){
                                        ?>
                                        <option value="<?php echo $row['brand_id']; ?>" <?php if (isset($_REQUEST['brand'])&& in_array($row['brand_id'], $_REQUEST['brand'])){ echo 'selected'; } ?>>
                                            <?php echo $row['brand']; ?>
                                        </option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-5 control-label">Model:</label>
                        <div class="col-md-6" align="left">
                            <select name="model[]" multiple="multiple" id="model"  class="form-control" onChange="document.form1.submit();" >
                                <?php
                                $model_query = mysqli_query($link1,"SELECT DISTINCT model_id, model FROM model_master WHERE product_id IN ($productstr) AND brand_id IN ($brandstr) ORDER BY model");

                                if($model_query){
                                    while($model_res = mysqli_fetch_assoc($model_query)){
                                        ?>
                                        <option value="<?= $model_res['model_id']; ?>"
                                                <?= (isset($_REQUEST['model']) && in_array($model_res['model_id'], $modelarray)) ? 'selected' : '' ?>>
                                            <?= $model_res['model']." | ".$model_res['model_id']; ?>
                                        </option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
<!--                call type-->
                <div class="form-group">
                    <div id= "dt_range" class="col-md-6"><label class="col-md-5 control-label">Call type</label>
                        <div class="col-md-6 input-append date" align="left">
                            <select name="call_type[]" id="call_type" multiple="multiple"  class="form-control" onChange="document.form1.submit();">
                                <?php
                                $sql="select  status_id , main_status_id,system_status from jobstatus_master"
                                        ." where status_id in ('1','2','3','5','6','7','8','11','12','48','49','50','55','56') and (status_id = main_status_id )";
                                $res_status = mysqli_query($link1,$sql)or die(mysqli_error($link1));
                                if($res_status){
                                    while($row = mysqli_fetch_assoc($res_status)){
                                        ?>
                                        <option value="<?php echo $row['status_id']; ?>"
                                                <?php if(isset($_REQUEST['call_type']) && in_array($row['status_id'],$_REQUEST['call_type'])) echo "selected"; ?> >
                                            <?php echo $row['system_status']; ?>
                                        </option>
                                        <?php
                                    }
                                }

                                ?>
                            </select>
                        </div>
                    </div>
                    <div  class="col-md-6"></div>
                </div>

                <div class="text-center">
                    <button id="showFIle" type="submit" class="btn btn-success" onclick="" lick="document.form1.submit();">Go</button>
                </div>
            </form>

            <div id="file_showing" style="text-align: center;margin-top: 50px;cursor: pointer">
                <a id="anchro_tag" href="" download="">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         xmlns:xlink="http://www.w3.org/1999/xlink"
                         width="90" height="66" viewBox="0 0 190 166" id="excel">
                        <defs>
                            <rect id="j" width="142" height="166" rx="8"></rect>
                            <rect id="m" width="71" height="42" y="41"></rect>
                            <rect id="q" width="71" height="42" x="71" y="41"></rect>
                            <rect id="u" width="71" height="42" y="-1"></rect>
                            <rect id="y" width="71" height="42" x="71" y="-1"></rect>
                            <rect id="C" width="71" height="42" y="83"></rect>
                            <rect id="G" width="71" height="42" x="71" y="83"></rect>
                            <rect id="K" width="71" height="42" y="125"></rect>
                            <rect id="O" width="71" height="42" x="71" y="125"></rect>
                            <rect id="R" width="142" height="166" rx="8"></rect>
                            <rect id="U" width="96" height="96" rx="8"></rect>
                            <rect id="aa" width="96" height="98" y="-1" rx="8"></rect>
                            <filter id="o" width="102.8%" height="104.8%" x="-1.4%" y="-2.4%" filterUnits="objectBoundingBox">
                                <feOffset dx="-1" in="SourceAlpha" result="shadowOffsetInner1"></feOffset>
                                <feComposite in="shadowOffsetInner1" in2="SourceAlpha" k2="-1" k3="1" operator="arithmetic" result="shadowInnerInner1"></feComposite>
                                <feColorMatrix in="shadowInnerInner1" result="shadowMatrixInner1" values="0 0 0 0 1   0 0 0 0 1   0 0 0 0 1  0 0 0 0.1 0"></feColorMatrix>
                                <feGaussianBlur in="SourceAlpha" result="shadowBlurInner2" stdDeviation=".5"></feGaussianBlur>
                                <feOffset dy="1" in="shadowBlurInner2" result="shadowOffsetInner2"></feOffset>
                                <feComposite in="shadowOffsetInner2" in2="SourceAlpha" k2="-1" k3="1" operator="arithmetic" result="shadowInnerInner2"></feComposite>
                                <feColorMatrix in="shadowInnerInner2" result="shadowMatrixInner2" values="0 0 0 0 0   0 0 0 0 0   0 0 0 0 0  0 0 0 0.08 0"></feColorMatrix>
                                <feMerge>
                                    <feMergeNode in="shadowMatrixInner1"></feMergeNode>
                                    <feMergeNode in="shadowMatrixInner2"></feMergeNode>
                                </feMerge>
                            </filter>
                            <filter id="s" width="143.7%" height="173.8%" x="-21.8%" y="-36.9%" filterUnits="objectBoundingBox">
                                <feOffset dy="-1" in="SourceAlpha" result="shadowOffsetInner1"></feOffset>
                                <feComposite in="shadowOffsetInner1" in2="SourceAlpha" k2="-1" k3="1" operator="arithmetic" result="shadowInnerInner1"></feComposite>
                                <feColorMatrix in="shadowInnerInner1" result="shadowMatrixInner1" values="0 0 0 0 1   0 0 0 0 1   0 0 0 0 1  0 0 0 0.05 0"></feColorMatrix>
                                <feGaussianBlur in="SourceAlpha" result="shadowBlurInner2" stdDeviation="10"></feGaussianBlur>
                                <feOffset dy="11" in="shadowBlurInner2" result="shadowOffsetInner2"></feOffset>
                                <feComposite in="shadowOffsetInner2" in2="SourceAlpha" k2="-1" k3="1" operator="arithmetic" result="shadowInnerInner2"></feComposite>
                                <feColorMatrix in="shadowInnerInner2" result="shadowMatrixInner2" values="0 0 0 0 0   0 0 0 0 0   0 0 0 0 0  0 0 0 0.14 0"></feColorMatrix>
                                <feOffset dx="1" dy="1" in="SourceAlpha" result="shadowOffsetInner3"></feOffset>
                                <feComposite in="shadowOffsetInner3" in2="SourceAlpha" k2="-1" k3="1" operator="arithmetic" result="shadowInnerInner3"></feComposite>
                                <feColorMatrix in="shadowInnerInner3" result="shadowMatrixInner3" values="0 0 0 0 0   0 0 0 0 0   0 0 0 0 0  0 0 0 0.15 0"></feColorMatrix>
                                <feMerge>
                                    <feMergeNode in="shadowMatrixInner1"></feMergeNode>
                                    <feMergeNode in="shadowMatrixInner2"></feMergeNode>
                                    <feMergeNode in="shadowMatrixInner3"></feMergeNode>
                                </feMerge>
                            </filter>
                            <filter id="w" width="101.4%" height="102.4%" x="-.7%" y="-1.2%" filterUnits="objectBoundingBox">
                                <feOffset dy="-1" in="SourceAlpha" result="shadowOffsetInner1"></feOffset>
                                <feComposite in="shadowOffsetInner1" in2="SourceAlpha" k2="-1" k3="1" operator="arithmetic" result="shadowInnerInner1"></feComposite>
                                <feColorMatrix in="shadowInnerInner1" values="0 0 0 0 1   0 0 0 0 1   0 0 0 0 1  0 0 0 0.15 0"></feColorMatrix>
                            </filter>
                            <filter id="A" width="102.8%" height="104.8%" x="-1.4%" y="-2.4%" filterUnits="objectBoundingBox">
                                <feOffset dy="-1" in="SourceAlpha" result="shadowOffsetInner1"></feOffset>
                                <feComposite in="shadowOffsetInner1" in2="SourceAlpha" k2="-1" k3="1" operator="arithmetic" result="shadowInnerInner1"></feComposite>
                                <feColorMatrix in="shadowInnerInner1" result="shadowMatrixInner1" values="0 0 0 0 1   0 0 0 0 1   0 0 0 0 1  0 0 0 0.15 0"></feColorMatrix>
                                <feOffset dx="2" in="SourceAlpha" result="shadowOffsetInner2"></feOffset>
                                <feComposite in="shadowOffsetInner2" in2="SourceAlpha" k2="-1" k3="1" operator="arithmetic" result="shadowInnerInner2"></feComposite>
                                <feColorMatrix in="shadowInnerInner2" result="shadowMatrixInner2" values="0 0 0 0 0   0 0 0 0 0.709803922   0 0 0 0 0.37254902  0 0 0 0.63 0"></feColorMatrix>
                                <feMerge>
                                    <feMergeNode in="shadowMatrixInner1"></feMergeNode>
                                    <feMergeNode in="shadowMatrixInner2"></feMergeNode>
                                </feMerge>
                            </filter>
                            <filter id="E" width="102.8%" height="104.8%" x="-1.4%" y="-2.4%" filterUnits="objectBoundingBox">
                                <feOffset dx="-1" in="SourceAlpha" result="shadowOffsetInner1"></feOffset>
                                <feComposite in="shadowOffsetInner1" in2="SourceAlpha" k2="-1" k3="1" operator="arithmetic" result="shadowInnerInner1"></feComposite>
                                <feColorMatrix in="shadowInnerInner1" result="shadowMatrixInner1" values="0 0 0 0 1   0 0 0 0 1   0 0 0 0 1  0 0 0 0.7 0"></feColorMatrix>
                                <feOffset dx="2" dy="1" in="SourceAlpha" result="shadowOffsetInner2"></feOffset>
                                <feComposite in="shadowOffsetInner2" in2="SourceAlpha" k2="-1" k3="1" operator="arithmetic" result="shadowInnerInner2"></feComposite>
                                <feColorMatrix in="shadowInnerInner2" result="shadowMatrixInner2" values="0 0 0 0 0   0 0 0 0 0   0 0 0 0 0  0 0 0 0.08 0"></feColorMatrix>
                                <feMerge>
                                    <feMergeNode in="shadowMatrixInner1"></feMergeNode>
                                    <feMergeNode in="shadowMatrixInner2"></feMergeNode>
                                </feMerge>
                            </filter>
                            <filter id="I" width="101.4%" height="102.4%" x="-.7%" y="-1.2%" filterUnits="objectBoundingBox">
                                <feOffset dy="-1" in="SourceAlpha" result="shadowOffsetInner1"></feOffset>
                                <feComposite in="shadowOffsetInner1" in2="SourceAlpha" k2="-1" k3="1" operator="arithmetic" result="shadowInnerInner1"></feComposite>
                                <feColorMatrix in="shadowInnerInner1" result="shadowMatrixInner1" values="0 0 0 0 0   0 0 0 0 0   0 0 0 0 0  0 0 0 0.12 0"></feColorMatrix>
                                <feOffset dx="1" dy="1" in="SourceAlpha" result="shadowOffsetInner2"></feOffset>
                                <feComposite in="shadowOffsetInner2" in2="SourceAlpha" k2="-1" k3="1" operator="arithmetic" result="shadowInnerInner2"></feComposite>
                                <feColorMatrix in="shadowInnerInner2" result="shadowMatrixInner2" values="0 0 0 0 0   0 0 0 0 0   0 0 0 0 0  0 0 0 0.05 0"></feColorMatrix>
                                <feMerge>
                                    <feMergeNode in="shadowMatrixInner1"></feMergeNode>
                                    <feMergeNode in="shadowMatrixInner2"></feMergeNode>
                                </feMerge>
                            </filter>
                            <filter id="M" width="102.8%" height="104.8%" x="-1.4%" y="-2.4%" filterUnits="objectBoundingBox">
                                <feOffset dx="-1" in="SourceAlpha" result="shadowOffsetInner1"></feOffset>
                                <feComposite in="shadowOffsetInner1" in2="SourceAlpha" k2="-1" k3="1" operator="arithmetic" result="shadowInnerInner1"></feComposite>
                                <feColorMatrix in="shadowInnerInner1" result="shadowMatrixInner1" values="0 0 0 0 1   0 0 0 0 1   0 0 0 0 1  0 0 0 0.15 0"></feColorMatrix>
                                <feOffset dx="1" in="SourceAlpha" result="shadowOffsetInner2"></feOffset>
                                <feComposite in="shadowOffsetInner2" in2="SourceAlpha" k2="-1" k3="1" operator="arithmetic" result="shadowInnerInner2"></feComposite>
                                <feColorMatrix in="shadowInnerInner2" result="shadowMatrixInner2" values="0 0 0 0 1   0 0 0 0 1   0 0 0 0 1  0 0 0 0.15 0"></feColorMatrix>
                                <feOffset dx="2" dy="1" in="SourceAlpha" result="shadowOffsetInner3"></feOffset>
                                <feComposite in="shadowOffsetInner3" in2="SourceAlpha" k2="-1" k3="1" operator="arithmetic" result="shadowInnerInner3"></feComposite>
                                <feColorMatrix in="shadowInnerInner3" result="shadowMatrixInner3" values="0 0 0 0 0   0 0 0 0 0   0 0 0 0 0  0 0 0 0.08 0"></feColorMatrix>
                                <feMerge>
                                    <feMergeNode in="shadowMatrixInner1"></feMergeNode>
                                    <feMergeNode in="shadowMatrixInner2"></feMergeNode>
                                    <feMergeNode in="shadowMatrixInner3"></feMergeNode>
                                </feMerge>
                            </filter>
                            <filter id="Q" width="102.8%" height="104.8%" x="-1.4%" y="-2.4%" filterUnits="objectBoundingBox">
                                <feOffset dx="-1" in="SourceAlpha" result="shadowOffsetInner1"></feOffset>
                                <feComposite in="shadowOffsetInner1" in2="SourceAlpha" k2="-1" k3="1" operator="arithmetic" result="shadowInnerInner1"></feComposite>
                                <feColorMatrix in="shadowInnerInner1" result="shadowMatrixInner1" values="0 0 0 0 1   0 0 0 0 1   0 0 0 0 1  0 0 0 0.08 0"></feColorMatrix>
                                <feOffset dx="2" dy="1" in="SourceAlpha" result="shadowOffsetInner2"></feOffset>
                                <feComposite in="shadowOffsetInner2" in2="SourceAlpha" k2="-1" k3="1" operator="arithmetic" result="shadowInnerInner2"></feComposite>
                                <feColorMatrix in="shadowInnerInner2" result="shadowMatrixInner2" values="0 0 0 0 0   0 0 0 0 0   0 0 0 0 0  0 0 0 0.01 0"></feColorMatrix>
                                <feMerge>
                                    <feMergeNode in="shadowMatrixInner1"></feMergeNode>
                                    <feMergeNode in="shadowMatrixInner2"></feMergeNode>
                                </feMerge>
                            </filter>
                            <filter id="S" width="117%" height="117%" x="-8.5%" y="-8.5%" filterUnits="objectBoundingBox">
                                <feGaussianBlur in="SourceGraphic" stdDeviation="3"></feGaussianBlur>
                            </filter>
                            <filter id="Z" width="103.1%" height="103.1%" x="-1.6%" y="-1.5%" filterUnits="objectBoundingBox">
                                <feGaussianBlur in="SourceAlpha" result="shadowBlurInner1" stdDeviation=".25"></feGaussianBlur>
                                <feOffset dx="-1" in="shadowBlurInner1" result="shadowOffsetInner1"></feOffset>
                                <feComposite in="shadowOffsetInner1" in2="SourceAlpha" k2="-1" k3="1" operator="arithmetic" result="shadowInnerInner1"></feComposite>
                                <feColorMatrix in="shadowInnerInner1" result="shadowMatrixInner1" values="0 0 0 0 0.0199267733   0 0 0 0 0.65973108   0 0 0 0 0.312408742  0 0 0 1 0"></feColorMatrix>
                                <feGaussianBlur in="SourceAlpha" result="shadowBlurInner2" stdDeviation=".5"></feGaussianBlur>
                                <feOffset dx="2" in="shadowBlurInner2" result="shadowOffsetInner2"></feOffset>
                                <feComposite in="shadowOffsetInner2" in2="SourceAlpha" k2="-1" k3="1" operator="arithmetic" result="shadowInnerInner2"></feComposite>
                                <feColorMatrix in="shadowInnerInner2" result="shadowMatrixInner2" values="0 0 0 0 0   0 0 0 0 0.466666667   0 0 0 0 0.250980392  0 0 0 1 0"></feColorMatrix>
                                <feMerge>
                                    <feMergeNode in="shadowMatrixInner1"></feMergeNode>
                                    <feMergeNode in="shadowMatrixInner2"></feMergeNode>
                                </feMerge>
                            </filter>
                            <filter id="ab" width="141.7%" height="137.9%" x="-20.8%" y="-17%" filterUnits="objectBoundingBox">
                                <feOffset dy="1" in="SourceAlpha" result="shadowOffsetOuter1"></feOffset>
                                <feGaussianBlur in="shadowOffsetOuter1" result="shadowBlurOuter1" stdDeviation="3"></feGaussianBlur>
                                <feColorMatrix in="shadowBlurOuter1" result="shadowMatrixOuter1" values="0 0 0 0 0   0 0 0 0 0   0 0 0 0 0  0 0 0 0.1 0"></feColorMatrix>
                                <feOffset dy="1" in="SourceAlpha" result="shadowOffsetOuter2"></feOffset>
                                <feGaussianBlur in="shadowOffsetOuter2" result="shadowBlurOuter2" stdDeviation=".5"></feGaussianBlur>
                                <feColorMatrix in="shadowBlurOuter2" result="shadowMatrixOuter2" values="0 0 0 0 0   0 0 0 0 0   0 0 0 0 0  0 0 0 0.1 0"></feColorMatrix>
                                <feMerge>
                                    <feMergeNode in="shadowMatrixOuter1"></feMergeNode>
                                    <feMergeNode in="shadowMatrixOuter2"></feMergeNode>
                                </feMerge>
                            </filter>
                            <linearGradient id="l" x1="6.294%" x2="103.402%" y1="0%" y2="103.5%">
                                <stop offset="0%" stop-color="#007438"></stop>
                                <stop offset="97.34%" stop-color="#008B44"></stop>
                            </linearGradient>
                            <linearGradient id="p" x1="107.466%" x2="6.294%" y1="107.831%" y2="0%">
                                <stop offset="0%" stop-color="#00D576"></stop>
                                <stop offset="97.026%" stop-color="#00A054"></stop>
                            </linearGradient>
                            <linearGradient id="t" x1="6.294%" x2="107.466%" y1="0%" y2="107.831%">
                                <stop offset="0%" stop-color="#007E42"></stop>
                                <stop offset="95.983%" stop-color="#009A50"></stop>
                            </linearGradient>
                            <linearGradient id="x" x1="6.294%" x2="107.466%" y1="0%" y2="107.831%">
                                <stop offset="0%" stop-color="#00AD61"></stop>
                                <stop offset="100%" stop-color="#00E18C"></stop>
                            </linearGradient>
                            <linearGradient id="B" x1="107.466%" x2="9.303%" y1="107.831%" y2="3.207%">
                                <stop offset="0%" stop-color="#003D20"></stop>
                                <stop offset="100%" stop-color="#004F2A"></stop>
                            </linearGradient>
                            <linearGradient id="F" x1="107.466%" x2="9.303%" y1="107.831%" y2="3.207%">
                                <stop offset="0%" stop-color="#00C165"></stop>
                                <stop offset="100%" stop-color="#008D46"></stop>
                            </linearGradient>
                            <linearGradient id="J" x1="107.466%" x2="9.303%" y1="107.831%" y2="3.207%">
                                <stop offset="0%" stop-color="#00522E"></stop>
                                <stop offset="100%" stop-color="#003B20"></stop>
                            </linearGradient>
                            <linearGradient id="N" x1="107.466%" x2="9.303%" y1="107.831%" y2="3.207%">
                                <stop offset="0%" stop-color="#006D3D"></stop>
                                <stop offset="100%" stop-color="#004C29"></stop>
                            </linearGradient>
                            <linearGradient id="V" x1="113.177%" x2="2.151%" y1="104.673%" y2="9.713%">
                                <stop offset="0%" stop-color="#008034"></stop>
                                <stop offset="100%" stop-color="#004F21"></stop>
                            </linearGradient>
                            <linearGradient id="ad" x1="29.468%" x2="97.963%" y1="50%" y2="50%">
                                <stop offset="0%" stop-color="#F0F0F0"></stop>
                                <stop offset="100%" stop-color="#FFF"></stop>
                            </linearGradient>
                            <pattern id="n" width="512" height="512" x="-512" y="-471" patternUnits="userSpaceOnUse">
                                <use xlink:href="#a"></use>
                            </pattern>
                            <pattern id="r" width="512" height="512" x="-441" y="-471" patternUnits="userSpaceOnUse">
                                <use xlink:href="#b"></use>
                            </pattern>
                            <pattern id="v" width="512" height="512" x="-512" y="-513" patternUnits="userSpaceOnUse">
                                <use xlink:href="#c"></use>
                            </pattern>
                            <pattern id="z" width="512" height="512" x="-441" y="-513" patternUnits="userSpaceOnUse">
                                <use xlink:href="#d"></use>
                            </pattern>
                            <pattern id="D" width="512" height="512" x="-512" y="-429" patternUnits="userSpaceOnUse">
                                <use xlink:href="#e"></use>
                            </pattern>
                            <pattern id="H" width="512" height="512" x="-441" y="-429" patternUnits="userSpaceOnUse">
                                <use xlink:href="#f"></use>
                            </pattern>
                            <pattern id="L" width="512" height="512" x="-512" y="-387" patternUnits="userSpaceOnUse">
                                <use xlink:href="#g"></use>
                            </pattern>
                            <pattern id="P" width="512" height="512" x="-441" y="-387" patternUnits="userSpaceOnUse">
                                <use xlink:href="#h"></use>
                            </pattern>
                            <pattern id="X" width="512" height="512" x="-512" y="-512" patternUnits="userSpaceOnUse">
                                <use xlink:href="#i"></use>
                            </pattern>
                            <radialGradient id="W" cx="86.601%" cy="84.21%" r="62.398%" fx="86.601%" fy="84.21%">
                                <stop offset="0%" stop-color="#018137"></stop>
                                <stop offset="100%" stop-color="#007E35" stop-opacity="0"></stop>
                            </radialGradient>
                            <polygon id="ac" points="25 73.107 41.756 46.96 26.572 23 38.144 23 47.977 41.099 57.61 23 69.082 23 53.83 47.336 70.587 73.107 58.647 73.107 47.777 53.778 36.873 73.107"></polygon>
                        </defs>
                        <g fill="none" fill-rule="evenodd">
                            <g transform="translate(48)">
                                <mask id="k" fill="#fff">
                                    <use xlink:href="#j"></use>
                                </mask>
                                <g mask="url(#k)">
                                    <use xlink:href="#m" fill="url(#l)"></use>
                                    <use xlink:href="#m" fill="url(#n)" fill-opacity=".012"></use>
                                    <use xlink:href="#m" fill="#000" filter="url(#o)"></use>
                                </g>
                                <g mask="url(#k)">
                                    <use xlink:href="#q" fill="url(#p)"></use>
                                    <use xlink:href="#q" fill="url(#r)" fill-opacity=".012"></use>
                                    <use xlink:href="#q" fill="#000" filter="url(#s)"></use>
                                </g>
                                <g mask="url(#k)">
                                    <use xlink:href="#u" fill="url(#t)"></use>
                                    <use xlink:href="#u" fill="url(#v)" fill-opacity=".012"></use>
                                    <use xlink:href="#u" fill="#000" filter="url(#w)"></use>
                                </g>
                                <g mask="url(#k)">
                                    <use xlink:href="#y" fill="url(#x)"></use>
                                    <use xlink:href="#y" fill="url(#z)" fill-opacity=".012"></use>
                                    <use xlink:href="#y" fill="#000" filter="url(#A)"></use>
                                </g>
                                <g mask="url(#k)">
                                    <use xlink:href="#C" fill="url(#B)"></use>
                                    <use xlink:href="#C" fill="url(#D)" fill-opacity=".012"></use>
                                    <use xlink:href="#C" fill="#000" filter="url(#E)"></use>
                                </g>
                                <g mask="url(#k)">
                                    <use xlink:href="#G" fill="url(#F)"></use>
                                    <use xlink:href="#G" fill="url(#H)" fill-opacity=".012"></use>
                                    <use xlink:href="#G" fill="#000" filter="url(#I)"></use>
                                </g>
                                <g mask="url(#k)">
                                    <use xlink:href="#K" fill="url(#J)"></use>
                                    <use xlink:href="#K" fill="url(#L)" fill-opacity=".012"></use>
                                    <use xlink:href="#K" fill="#000" filter="url(#M)"></use>
                                </g>
                                <g mask="url(#k)">
                                    <use xlink:href="#O" fill="url(#N)"></use>
                                    <use xlink:href="#O" fill="url(#P)" fill-opacity=".012"></use>
                                    <use xlink:href="#O" fill="#000" filter="url(#Q)"></use>
                                </g>
                            </g>
                            <g transform="translate(48)">
                                <mask id="T" fill="#fff">
                                    <use xlink:href="#R"></use>
                                </mask>
                                <path fill="#000" fill-opacity=".1" d="M-40,37 L40,37 C44.418278,37 48,40.581722 48,45 L48,125 C48,129.418278 34.418278,143 30,143 L-50,143 C-54.418278,143 -58,139.418278 -58,135 L-58,55 C-58,50.581722 -44.418278,37 -40,37 Z" filter="url(#S)" mask="url(#T)"></path>
                            </g>
                            <g transform="translate(0 35)">
                                <mask id="Y" fill="#fff">
                                    <use xlink:href="#U"></use>
                                </mask>
                                <use xlink:href="#U" fill="url(#V)"></use>
                                <use xlink:href="#U" fill="url(#W)"></use>
                                <use xlink:href="#U" fill="url(#X)" fill-opacity=".013"></use>
                                <g fill="#000" mask="url(#Y)">
                                    <use xlink:href="#aa" filter="url(#Z)"></use>
                                </g>
                                <g mask="url(#Y)">
                                    <use xlink:href="#ac" fill="#000" filter="url(#ab)"></use>
                                    <use xlink:href="#ac" fill="url(#ad)"></use>
                                </g>
                            </g>
                        </g>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let anchro_tag=document.getElementById("anchro_tag");
        const daterange = "<?= isset($datatime)?$datatime:date('Y-m-d') ?>";
        const location = "<?= isset($_REQUEST['locationname']) ? base64_encode(implode(",", $_REQUEST['locationname'])) : "" ?>";
        const modelid   = "<?= isset($_REQUEST['model']) ? base64_encode(implode(',',$_REQUEST['model'])) : '' ?>";
        const statename    = "<?= isset($_REQUEST['statename']) ? base64_encode(implode(',',$_REQUEST['statename'])) : '' ?>";
        const status_v   = "<?= isset($_REQUEST['status_v']) ? base64_encode($_REQUEST['status_v']) : '' ?>";
        const proid     = "<?= isset($productstr) ? base64_encode($productstr) : '' ?>";
        const brand     = "<?= isset($brandstr) ? base64_encode($brandstr) : '' ?>";
        anchro_tag.href = "../excelReports/allcallexcel.php"
            + "?daterange=" + daterange
            + "&location_code=" + location
            + "&modelid=" + modelid
            + "&status=" + status_v
            + "&pending=" + status_v
            + "&state=" + statename
            + "&proid=" + proid
            + "&brand=" + brand;
    });

    function Observer(){
        this.observers = [];
    }

    function Subject(){}
    function PageLoader(){
        Observer.call(this);
        this.data = null;
    }
    function StateMaster(){
        Subject.call(this);
        this.data = [];
    }
    Observer.prototype.attach = function(observer){
        throw new Error("attach method must be implemented");
    }
    Observer.prototype.detach = function(observer){
        throw new Error("detach method must be implemented");
    }
    Observer.prototype.notify = function(){
        throw new Error("notify method must be implemented");
    }
    Subject.prototype.update = function(observer){
        throw new Error("update method must be implemented");
    }
    PageLoader.prototype = Object.create(Observer.prototype);
    PageLoader.prototype.constructor = PageLoader;
    PageLoader.prototype.attach = function(observer){
        this.observers.push(observer);}
    PageLoader.prototype.notify = function(){
        this.observers.forEach((observer)=>{
            observer.update(this);});
    }
    PageLoader.prototype.detach = function(observer){
        this.observers = this.observers.filter(obs => obs !== observer);
    }
    PageLoader.prototype.setData = function(data){
        if(data){this.data = data;}
    }
    PageLoader.prototype.getData = function(){
        return this.data;
    }
    StateMaster.prototype = Object.create(Subject.prototype);
    StateMaster.prototype.constructor = StateMaster;
    StateMaster.prototype.update = function(observer){
        this.data.push(observer.getData());
        this.showData();
    }
    StateMaster.prototype.showData = function(){
        this.data.forEach((data)=>{
            console.log(data);
        });
    }
    const pageloader = new PageLoader();
    const state = new StateMaster();
    const city = new StateMaster();
    const product=new StateMaster();
    const location12=new StateMaster();
    const brand=new StateMaster();
    const model=new StateMaster();
    pageloader.attach(state);
    pageloader.attach(city);
    pageloader.attach(product);
    pageloader.attach(location12);
    pageloader.attach(location12);
    pageloader.attach(brand);
    pageloader.attach(model);
    pageloader.setData("State is Change now");
    pageloader.notify();
    console.log(pageloader);
</script>
</body>
</html>