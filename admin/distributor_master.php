<?php
require_once("../includes/config.php");
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" />
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/jquery.js"></script>
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<title><?=siteTitle?></title>
    <style>
        .custom-modal{
            display:none;
            position:fixed;
            left:0; top:0;
            width:100%; height:100%;
            background:rgba(0,0,0,0.5);
            z-index:9999;
        }

        .custom-modal-box{
            background:#fff;
            width:400px;
            margin:10% auto;
            border-radius:6px;
            animation:pop .3s;
        }

        .custom-modal-header{
            padding:12px;
            background:#dc3545;
            color:#fff;
            display:flex;
            justify-content:space-between;
            align-items:center;
            font-weight:bold;
        }

        .custom-modal-body{
            max-height:300px;
            overflow:auto;
            padding:10px;
        }

        .custom-modal-footer{
            padding:10px;
            text-align:right;
        }

        .custom-modal-footer button{
            padding:6px 12px;
            margin-left:5px;
        }

        .close-modal{
            cursor:pointer;
            font-size:20px;
        }

        @keyframes pop{
            from{transform:scale(.7);opacity:0}
            to{transform:scale(1);opacity:1}
        }
        .dynamic-page {
            position: fixed;
            left: 0;
            bottom: -100%;
            width: 100%;
            height: 100%;
            background: white;
            z-index: 9999;
            overflow-y: auto;

            transition: bottom 0.7s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .dynamic-page.show {
            bottom: 0;
        }
        .dynamic-page {
            -ms-overflow-style: none;   /* IE / Edge */
            scrollbar-width: none;      /* Firefox */
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
        }

        .dynamic-page::-webkit-scrollbar {
            width: 0px;
            height: 0px;
        }

    </style>
<script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</script>
</head>
<body>
<script>

</script>
<div class="container-fluid">
  <div class="row content">
	<?php
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">

      <h2 align="center"><i class="fa  fa-shopping-basket "></i> Distributer Master</h2>
        <div style="display: flex;justify-content: end">
            <button class="btn btn-danger" onclick="openmodelbox()">Edit Column</button>
        </div>
     <?php if($_REQUEST['msg']){?><br>
      <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      <?php }?>
	    <div class="form-group">
		  <div class="col-md-6">
			<div class="col-md-5" align="left">

            </div>
          </div>
	    </div><!--close form group-->
      <form class="form-horizontal" role="form">
        <div style="display:inline-block;float:right"><button title="Add Distributer" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='op_distributor.php?op=Add<?=$pagenav?>'"><span>Add Distriupter Supplier</span></button>&nbsp;&nbsp;</div>
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="example" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
          <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>Name</th>
              <th>Code</th>
              <th>SAP Code</th>
              <th>User</th>
              <th>Type</th>
              <th>Brand</th>
              <th>Email</th>
              <th>Address</th>
              <th>City</th>
              <th>State</th>
              <th>Country</th>
              <th>Pincode</th>
              <th>Company</th>
              <th>Phone</th>
              <th>Mobile</th>
              <th>GST</th>
              <th>Status</th>
              <th>Updated By</th>
              <th>Updated On</th>
              <th>Sale Segment</th>
              <th>Edit</th>
          </thead>
           <tbody>
           </tbody>
          </table>
          </div>
      <!--</div>-->
      </form>
    </div>

  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>


<div id="myModal" class="custom-modal">
    <div class="custom-modal-box">
        <div class="custom-modal-header">
            <span>Select Tables</span>
            <span class="close-modal" onclick="closemodelbox()">&times;</span>
        </div>

        <div class="custom-modal-body" id="tableList"></div>

        <div class="custom-modal-footer">
            <button onclick="closemodelbox()">Close</button>
            <button class="btn btn-danger" onclick="saveSelection()">Save</button>
        </div>
    </div>
</div>
<script>
    async function helloBroHowareyou(data,next,callback){
        const a={
            name:10,
            node:null
        };
        return callback(a);
    }
</script>
<div id="dynamicPage" class="dynamic-page"></div>
</body>
<!--used this script for column dynamics-->
<script>
    const columnMap = {
        distributorname:"Name",
        distributorcode:"Code",
        sap_hanacode:"SAP Code",
        userid:"User",
        type:"Type",
        brand:"Brand",
        email:"Email",
        address1:"Address",
        cityid:"City",
        stateid:"State",
        countryid:"Country",
        pincode:"Pincode",
        companyid:"Company",
        phone:"Phone",
        mobile:"Mobile",
        gst_no:"GST",
        status:"Status",
        updateby:"Updated By",
        update_date:"Updated On",
        sale_segment:"Sale Segment"
    };
    const colIndex = {
        distributorname:1, distributorcode:2, sap_hanacode:3, userid:4,
        type:5, brand:6, email:7, address1:8, cityid:9, stateid:10,
        countryid:11, pincode:12, companyid:13, phone:14, mobile:15,
        gst_no:16, status:17, updateby:18, update_date:19, sale_segment:20,
        edit:21
    };
    function openmodelbox(){
        let saved = JSON.parse(localStorage.getItem("distCols"));
        let list=document.getElementById("tableList");
        list.innerHTML="";
        for(let k in columnMap){
            let chk = !saved || saved.includes(k) ? "checked":"";
            list.innerHTML+=`
      <label style="display:block;padding:6px">
        <input type="checkbox" value="${k}" ${chk}> ${columnMap[k]}
      </label>`;
        }
        myModal.style.display="block";
    }
    function saveSelection(){
        let sel=[];
        document.querySelectorAll("#tableList input:checked").forEach(i=>sel.push(i.value));
        localStorage.setItem("distCols", JSON.stringify(sel));
        closemodelbox();
        reloadTable();
    }
    function closemodelbox(){ document.getElementById("myModal").style.display="none"; }
    function reloadTable(){
        let table=$('#example').DataTable();
        table.destroy();
        initTable();
    }
    function initTable(){
        let userCols=JSON.parse(localStorage.getItem("distCols"));
        let useCols=userCols && userCols.length? userCols:Object.keys(columnMap);
        useCols.push("edit");

        let defs=[];
        for(let k in colIndex){
            defs.push({targets:colIndex[k],visible:useCols.includes(k)});
        }

        $('#example').DataTable({
            processing:true,
            serverSide:true,
            order:[[0,"desc"]],
            ajax:{url:"../pagination/distributor-grid-data.php",type:"POST"},
            columnDefs:defs
        });
    }
    document.addEventListener("DOMContentLoaded",initTable);
    function Outer(){
        let count=0;
        return function(){
            count++;
            return count;
        }
    }
</script>
</html>