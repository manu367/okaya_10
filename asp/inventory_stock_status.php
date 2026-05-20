<?php
require_once("../includes/config.php");
/// get access product
$get_accproduct = getAccessProduct($_SESSION['asc_code'],$link1);
/// get access brand
$get_accbrand = getAccessBrand($_SESSION['asc_code'],$link1);

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
 <!-- datatable plugin-->
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <!--  -->
  <style type="text/css">
	.modal-dialogTH{
		overflow-y: initial !important
	}
	.modal-bodyTH{
		max-height: calc(100vh - 212px);
		overflow-y: auto;
	}
	.modalTH {
	  width: 1000px;
	  margin: auto;
	}

</style>
 <script type="text/javascript" language="javascript" >
$(document).ready(function(){
	$('input[name="daterange"]').daterangepicker({
		locale: {
			format: 'YYYY-MM-DD'
		}
	});
});
$(document).ready(function() {
	$('#example-multiple-selected').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"200",
            enableFiltering: true
	});
});
/////////// function to get model on the basis of brand
  $(document).ready(function(){
	$('#brand').change(function(){
	  var brandid=$('#brand').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{filterbrand:brandid},
		success:function(data){
	    $('#modeldiv').html(data);
	    }
	  });
    });
  });
 ////// paging script 
$(document).ready(function() {
	var dataTable = $('#joblist-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		//"order": [[ 0, "asc" ]],
		"ajax":{
			url :"../pagination/stock-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "product_name": "<?=$_REQUEST['product_name']?>", "brand": "<?=$_REQUEST['brand']?>", "location_code": "<?=$_REQUEST['location_code']?>", "modelid": "<?=$_REQUEST['modelid']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".job-grid-error").html("");
				$("#job-grid").append('<tbody class="job-grid-error"><tr><th colspan="17">No data found in the server</th></tr></tbody>');
				$("#job-grid_processing").css("display","none");
				
			}
		}
	} );
} );
////// function for open model to see the task history
function checkMappedModel(partid,model){
	$.get('mapped_model.php?partcode=' + partid+ '&model='+model, function(html){
		 $('#mappedModel .modal-body').html(html);
		 $('#mappedModel').modal({
			show: true,
			backdrop:"static"
		});
	 });
}

////// function for open model to see the task history
function checkMappedPart(partid){
	$.get('checkAltPartcode.php?partcode=' + partid, function(html){
		 $('#mappedPart .modal-body').html(html);
		 $('#mappedPart').modal({
			show: true,
			backdrop:"static"
		});
	 });
}

////// function for open model to see the task history
function checkengstock(partid){
	$.get('checkengpartcode.php?partcode=' + partid, function(html){
		 $('#mappedstock .modal-body').html(html);
		 $('#mappedstock').modal({
			show: true,
			backdrop:"static"
		});
	 });
}
</script>
<!-- Include multiselect -->
<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
<!-- Include Date Range Picker -->
 <script type="text/javascript" src="../js/daterangepicker.js"></script>
 <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
 <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-cubes"></i> Stock Status</h2>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
        <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Product</label>	  
			<div class="col-md-6" align="left">
			 <select name="product_name" id="product_name" class="form-control">
                <option value="">All</option>
                <?php
				$dept_query="SELECT * FROM product_master where status = '1' and product_id in (".$get_accproduct.") order by product_name";
				$check_dept=mysqli_query($link1,$dept_query);
				while($br_dept = mysqli_fetch_array($check_dept)){
			    ?>
			    <option value="<?=$br_dept['product_id']?>"<?php if($_REQUEST['product_name'] == $br_dept['product_id']){ echo "selected";}?>><?php echo $br_dept['product_name']?></option>
			   <?php }?>
             </select>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-4 control-label">Brand</label>	  
			<div class="col-md-6" align="left">
			  <select name="brand" id="brand" class="form-control">
                <option value="">All</option>
                <?php
				$dept_query="SELECT * FROM brand_master where status = '1' and brand_id in (".$get_accbrand.") order by brand";
				$check_dept=mysqli_query($link1,$dept_query);
				while($br_dept = mysqli_fetch_array($check_dept)){
			    ?>
			    <option value="<?=$br_dept['brand_id']?>"<?php if($_REQUEST['brand'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>
			    <?php }?>
             </select>
            </div>
          </div>
	    </div><!--close form group-->
        <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Location</label>	  
			<div class="col-md-6">
				<select name="location_code" id="location_code" class="form-control">
                <?php
                $res_pro = mysqli_query($link1,"select location_code,locationname from location_master where location_code='".$_SESSION['asc_code']."'"); 
                while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                <option value="<?=$row_pro['location_code']?>" <?php if($_REQUEST['location_code'] == $row_pro['location_code']) { echo 'selected'; }?>><?=$row_pro['locationname']." (".$row_pro['location_code'].")"?></option>
                <?php } ?>
                 </select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6">
            	<div style="display:inline-block;float:left" id="modeldiv">
            <!--    <select name="modelid" id="modelid" class="form-control" style="width:150px;">
                	<option value="">All</option>
                    <?php 
					$model_query="SELECT model_id, model FROM model_master where brand_id='".$_REQUEST['brand']."' order by model";
     				$model_res=mysqli_query($link1,$model_query);
     				while($row_model = mysqli_fetch_array($model_res)){
					?>
           			<option value="<?=$row_model['model_id']?>"<?php if($_REQUEST['modelid']==$row_model['model_id']){ echo "selected";}?>><?php echo $row_model['model']?></option>
	 				<?php }?>
             	</select>-->
                </div>
                <div style="display:inline-block;float:right">
                	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               		<input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               		<input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                </div>
            </div>
          </div>
	    </div>
        <!--close form group-->
	  </form>
       <?php if ($_REQUEST['Submit']){?>
        <div class="form-group">
		  <div class="col-md-10"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6" align="left">
               <a href="../excelReports/asp_inventory_report.php?rname=<?=base64_encode("inventory_stock_status")?>&rheader=<?=base64_encode("Inventory Report")?>&daterange=<?=$_REQUEST['daterange']?>&location_code=<?=base64_encode($_REQUEST['location_code'])?>&job_status=<?=base64_encode($statusstr);?>&start_date=<?=$_REQUEST['start_date']?>&end_date=<?=$_REQUEST['end_date']?>" title="Export Login Logout details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Login Logout details in excel"></i></a>
            </div>
          </div>
	    </div>
        <div style="clear:both;"></div><!--close form group-->
        <?php }?>
        <form class="form-horizontal" role="form" name="form2">
        <div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;">
       <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table width="100%" id="joblist-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead class="bg-primary">
            <tr>
              <th>S.No</th>
			   <th>Brand</th>
			   <th>Product Category</th>
			   <th>Mapped Model</th>
              <th>Partcode</th>
              <th>Description</th>
               <th>Alternate Partcode</th>
			   <th>Engineer Stock</th>
              <th>Customer Price</th>
              <th>Engineer Fresh</th>
			  <th>Engineer Faulty</th>
              <th>Fresh ASP</th>
              <th>Defective ASP</th>
			  <th>Damage ASP</th>
              <th>Missing ASP</th>
              <th>Fresh In-transit ASP</th>
			   <th>Total</th>
            </tr>
          </thead>
          </table>
          </div>
      <!--</div>-->
      </form>
      <!-- Start Model Mapped Modal -->
          <div class="modal modalTH fade" id="mappedModel" role="dialog">
            <div class="modal-dialog modal-dialogTH">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" align="center">Model Details</h4>
                </div>
                <div class="modal-body modal-bodyTH">
                 <!-- here dynamic task details will show -->
                </div>
                <div class="modal-footer">
                  <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div><!--close Model Mapped modal-->
          
          
           <!-- Start Alternate Partcode -->
          <div class="modal modalTH fade" id="mappedPart" role="dialog">
            <div class="modal-dialog modal-dialogTH">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" align="center">Alternate Partcode Details</h4>
                </div>
                <div class="modal-body modal-bodyTH">
                 <!-- here dynamic task details will show -->
                </div>
                <div class="modal-footer">
                  <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div><!--close Alternate Partcode-->
		  
		    <!-- Start Engineer Stock Partcode -->
          <div class="modal modalTH fade" id="mappedstock" role="dialog">
            <div class="modal-dialog modal-dialogTH">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" align="center">Engineer Stock</h4>
                </div>
                <div class="modal-body modal-bodyTH">
                 <!-- here dynamic task details will show -->
                </div>
                <div class="modal-footer">
                  <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div><!--close Alternate Partcode-->
    </div>
    
  </div>
</div>
<script>
    // filter system  , search bar , deadline system, nots-popup
    // notes group ,  Pomodoro timer per task , drag and drop , dart and light mode

    function OnDemandPage(data){
        this.data = data || [];
        this._addObservers = [];
        this._deleteObservers = [];
    }

    OnDemandPage.prototype.addObserver = function(fn){
        this._addObservers.push(fn);
    };
    OnDemandPage.prototype.deleteObserver = function(fn){
        this._deleteObservers.push(fn);
    };

    OnDemandPage.prototype.safeAdd = function(item){
        if(this._addObservers.length === 0){
            throw new Error("No addObserver registered");
        }
        this.data.push(item);
        this._addObservers.forEach(fn => fn(item, this.data));
    };
    OnDemandPage.prototype.safeDelete = function(item){
        if(this._deleteObservers.length === 0){
            throw new Error("No deleteObserver registered");
        }
        const index = this.data.indexOf(item);
        if(index === -1){
            throw new Error("Item not found");
        }
        this.data.splice(index,1);
        this._deleteObservers.forEach(fn => fn(item, this.data));
    };
    OnDemandPage.prototype.show = function (){
        console.log(this.data);
    };
    const page = new OnDemandPage(["A","B"]);
    page.addObserver((item,data)=>{
        console.log("Added:", item);
    });

    page.deleteObserver((item,data)=>{
        console.log("Deleted:", item);
    });
    page.safeAdd("C");
    page.safeDelete("A");
    page.show();

    function TaskOperations(data=[]){
        this.data=data;
        this._sec_Gurd=[];
        this.back_Gurd=[];
    }
    TaskOperations.prototype.frontSecurity=(fn)=>{
        this._sec_Gurd.push(fn);
    }
    TaskOperations.prototype.backSecurity=(fn)=>{
        this.back_Gurd.push(fn);
    }
    TaskOperations.prototype.addTask = function(data){
        this.data.push(data);
    }
    TaskOperations.prototype.removeTask = function(data){
        this.data=this.data.filter(item=>item!==data);
    }
    TaskOperations.prototype.findTask = function(data){
        this.data.findIndex(item=>item===data);
    }
    function Shape(){}

    Shape.prototype.draw = function(){
        throw new Error("draw() must be implemented");
    };
    function Circle(){}

    Circle.prototype = Object.create(Shape.prototype);

    Circle.prototype.draw = function(){
        console.log("Circle draw");
    };
    const ddd=new Circle();
    ddd.draw();
    function Vehile(type,tayer){
        this.type = type;
        this.tayer = tayer;
    }
    Vehile.prototype.run=function(){
        if(this.tayer==4){
            console.log("4 vehicle",this.type);
        }
        if(this.tayer==2){
            console.log("2 vehicle",this.type);
        }
    }
    Vehile.prototype.engine=function(engine){
        console.log(this.type,"is a enginer type",engine);
    }


    function Car(){
        Vehile.call(this,'car',4);
    }
    Car.prototype=Object.create(Vehile.prototype);
    Car.prototype.constructor=Car;
    function Bike(){
        Vehile.call(this,'bike',2);
    }
    Bike.prototype=Object.create(Vehile.prototype);
    Bike.prototype.constructor=Bike;

    const bike=new Car();
    bike.run();
    bike.engine("double CC engine");

</script>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>