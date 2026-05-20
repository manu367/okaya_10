<?php
global $screenwidth;
require_once("../includes/config.php");

set_exception_handler(function (){
   echo "some thins is wrong";
});
set_error_handler(function (){
    echo "sc";
});


if(isset($_GET['model'])){
    $product_list = isset($_GET['product']) ? (array)$_GET['product'] : [];
    $brand_list   = isset($_GET['brand']) ? (array)$_GET['brand'] : [];
    echo rendermodel($link1,$product_list,$brand_list);
    exit();
}

$arrstate = getAccessState($_SESSION['userid'],$link1);
$access_brand = getAccessBrand($_SESSION['userid'],$link1);
$page=["state"=>[], "location"=>[], "product"=>[], "brand"=>[]];


$page["state"]=getAllState($link1,$arrstate);
$page['location']=getAllLocation($link1);
$page['product']=getAllProduct($link1);
$page['brand']=getAllBrand($link1,$access_brand);

function getAllState($link1,$arrstate){
    $local_state=[];
    $sql="select stateid, state from state_master  where stateid in (".$arrstate.") order by state";
    $state = mysqli_query($link1, $sql);
    while($stateinfo = mysqli_fetch_assoc($state)){
        $local_state[]=["stateid"=>$stateinfo['stateid'],"state"=>$stateinfo['state']];
    }
    return $local_state;
}
function getAllLocation($link1)
{
    $local_location=[];
    $location_query="SELECT locationname, location_code FROM location_master where locationtype != 'WH'  order by locationname ";
    $loc_res=mysqli_query($link1,$location_query);
    while($loc_info = mysqli_fetch_array($loc_res)){
        $local_location[]=["locationname"=>$loc_info['locationname'],"location_code"=>$loc_info['location_code']];
    }
    return $local_location;
}

function getAllProduct($link1){
    $local_product=[];
    $model_query="select product_id,product_name from product_master where status='1' order by product_name";
    $check1=mysqli_query($link1,$model_query);
    while($product = mysqli_fetch_array($check1)){
        $local_product[]=["product_id"=>$product['product_id'],"product_name"=>$product['product_name']];
    }
    return $local_product;
}
function getAllBrand($link1,$access_brand){
    $localbrand=[];
    $brand_sql="SELECT brand_id,brand FROM brand_master where status = '1' and brand_id in (".$access_brand.") order by brand";
    $brand=mysqli_query($link1,$brand_sql);
    while($brandinfo = mysqli_fetch_assoc($brand)){
        $localbrand[]=["brand_id"=>$brandinfo['brand_id'],"brand_name"=>$brandinfo['brand']];
    }
    return $localbrand;
}
function getModel($link1, $prodstr, $brandstr){
    $local_model=[];
    $prodList  = implode(',', array_map('intval', $prodstr));
    $brandList = implode(',', array_map('intval', $brandstr));

    $brand_sql = "SELECT DISTINCT model_id, model 
                  FROM model_master 
                  WHERE product_id IN ($prodList)
                  AND brand_id IN ($brandList)";

    $model_query = mysqli_query($link1, $brand_sql);
    while($model_info = mysqli_fetch_assoc($model_query)){
        $local_model[]=["model_id"=>$model_info['model_id'],"model"=>$model_info['model']];
    }
    return $local_model;
}

function renderState($state){
    $str="";
    if(count($state)<=0)return;
    foreach ($state as $key){
        $str.="<option value='".$key['stateid']."'>".$key['state']."</option>";
    }
    return $str;
}
function renderlocation($location){
    $str="";
    if(count($location)<=0)return;
    foreach ($location as $key){
        $str.="<option value='".$key['location_code']."'>".$key['locationname']."</option>";
    }
    return $str;
}
function renderbrand($brand){
    $str="";
    if(count($brand)<=0)return;
    foreach ($brand as $key){
        $str.="<option value='".$key['brand_id']."'>".$key['brand_name']."</option>";
    }
    return $str;
}
function renderProduct($product){
    $str="";
    if(count($product)<=0)return;
    foreach ($product as $key){
        $str.="<option value='".$key['product_id']."'>".$key['product_name']."</option>";
    }
    return $str;
}

function  rendermodel($link1,$product=[],$brandstr=[])
{
    // if filters empty → no query
    if(empty($product) || empty($brandstr)){
        return "<option value=''>No Record found</option>";
    }
    $model=getModel($link1,$product,$brandstr);
    if(empty($model)){
        return "<option value=''>No Record found</option>";
    }
    $str="";
    foreach ($model as $row){
        $str.="<option value='".$row['model_id']."'>".$row['model']."</option>";
    }
    return $str;
}

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
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
 <script type="text/javascript" language="javascript" >
     $(document).ready(function() {
         $("#form1").validate();
     });
     $(document).ready(function(){
         $('input[name="daterange"]').daterangepicker({
             locale: {
                 format: 'YYYY-MM-DD'
             }
         });
     });
     $(document).ready(function() {
         $('#prod_code').multiselect({
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
 </script>
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
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php
    include("../includes/leftnav2.php");
    ?>
   <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-snowflake-o"></i>Part Consume</h2>
      <?php if(isset($_REQUEST['msg'])){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>

	  <form class="form-horizontal" role="form" name="form1"  id="form1" action="" method="get">
          <div class="form-group">
              <div class="col-md-6"><label class="col-md-5 control-label">Date Range</label>
                  <div class="col-md-6 input-append date" align="left">
                      <input
                              type="text"
                              name="daterange"
                              id="date_rng"
                              class="form-control"/>
                  </div>
              </div>
              <div class="col-md-6">
                  <label class="col-md-5 control-label">Date Type</label>
                  <div class="col-md-5" align="left">
                      <select id="typedate"  name="typedate" class="form-control">
                          <option value="close_date">Close Date</option>
                          <option value="handover_date">Handover Date</option>
                      </select>
                  </div>
              </div>
          </div>
<!--          state and location-->
          <div class="form-group">
              <div class="col-md-6">
                  <label class="col-md-5 control-label">State<span style="color:#F00">*</span></label>
                  <div class="col-md-6" >
                      <select name="statename"
                              id="statename"
                              class="form-control">
                          <option>All</option>
                          <?= renderState($page['state'])?>
                      </select>
                  </div>
              </div>
              <div class="col-md-6">
                  <label class="col-md-5 control-label">Location</label>
                  <div class="col-md-5" id="citydiv">
                      <select
                              name="locationname"
                              id="locationname"
                              class="form-control">
                          <option>All</option>
                          <?= renderlocation($page['location'])?>
                      </select>
                  </div>
              </div>
          </div>
          <div class="form-group">
              <div class="col-md-6"><label class="col-md-5 control-label">Product</label>
                  <div class="col-md-5" id="location">
                      <select
                              name="prod_code[]"
                              id="prod_code"
                              multiple="multiple"
                              class="form-control">
                          <?=renderProduct($page['product'])?>
                      </select>
                  </div>
              </div>
              <div class="col-md-6"><label class="col-md-5 control-label">Brand</label>
                  <div class="col-md-5" >
                      <select
                              name="brand[]"
                              id="brand"
                              class="form-control"
                              multiple="multiple">
                          <?=renderbrand($page['brand'])?>
                      </select>
              </div>
          </div>
	    </div>

		<div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Model</label>
                <div class="col-md-5" >
                    <select name="model[]"
                            id="model"
                            class="form-control"
                            multiple="multiple">
                        <?=rendermodel($link1,$_REQUEST['prod_code'] ?? [],$_REQUEST['brand'] ?? [])?>
                 </select>
                </div>
            </div>

            <div class="col-md-6">
                <label class="col-md-5 control-label"></label>
                <div class="col-md-6" id="modeldiv">
                    <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                    <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                    <input id="go" type="button" class="btn btn-success" value="GO"  title="Go!">
                </div>
            </div>
        </div>
      </form>
    </div>

  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<script>

    const BASE_URL="partconsume_Report1.php";
    const gobutton=document.getElementById("go");
    const date_time=document.getElementById("date_rng");
    const dataType=document.getElementById("typedate");
    const state=document.getElementById("statename");
    const location=document.getElementById("locationname");
    const product=document.getElementById("prod_code");
    const brand=document.getElementById("brand");
    const model=document.getElementById("model");


    async function loadModels(){

        let product = $('#prod_code').val();
        let brand   = $('#brand').val();
        console.log(brand,product);

        if(!product || !brand){
            $('#model').html('');
            $('#model').multiselect('rebuild');
            return;
        }

        const params = new URLSearchParams();

        params.append("model",1);

        product.forEach(p => params.append("product[]",p));
        brand.forEach(b => params.append("brand[]",b));

        const response = await fetch(`${BASE_URL}?${params.toString()}`);
        const data = await response.text();

        $('#model').html(data);

        // VERY IMPORTANT after changing options
        $('#model').multiselect('rebuild');
    }

    // trigger when product OR brand changes
    $('#prod_code, #brand').on('change', loadModels);

    gobutton.addEventListener("click",()=>{
        alert();
    });

</script>
</body>
</html>