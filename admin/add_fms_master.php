<?php
require_once("../includes/config.php");

set_exception_handler(function ($exception) {
    if($exception instanceof  FMSExceptionHandler){
        $exception->location();
    }
});



class FMSExceptionHandler extends Exception{
    protected $type;
    protected $location;
    protected $pid,$hid;

    public function __construct($type,
                                $message,
                                $location,$pid,$hid){
        $this->type = $type;
        $this->location = $location;
        $this->pid = $pid;
        $this->hid = $hid;
        parent::__construct($message, 0, null);
    }
    public function location(){
        header("location:$this->location?pid=$this->pid&hid=$this->hid&type=$this->type&msg=$this->message");
        exit();
    }
}



$op  = isset($_REQUEST['op']) ? $_REQUEST['op'] : 'add';
$pid = isset($_REQUEST['pid']) ? (int)$_REQUEST['pid'] : 0;
$hid=isset($_REQUEST['hid'])?$_REQUEST['hid']:'';
$location="fms_master.php";
$is_edit = ($op === 'edit');
$fms_id=isset($_REQUEST['id'])?base64_decode($_REQUEST['id']):'';

class FMS_Operations{
    private $pid,$hid,$location;
    private $conn;
    function __construct($pid,$hid,$location,$conn){
        $this->pid = $pid;
        $this->hid = $hid;
        $this->location = $location;
        $this->conn = $conn;
    }
    public function updateOperation($data = [], $updateBy, $fms_id){
//        var_dump($data,$updateBy,$fms_id);exit();
        $fname       = $data['fmsname'];
        $details     = $data['details'];
        $steps       = (int)$data['steps'];
        $total_form  = (int)$data['total_form'];
        $updated_by  = $updateBy;
        $updated_ip  = $_SERVER['REMOTE_ADDR'];

        $sql = "UPDATE fms_master SET 
                fmsname     = '$fname',
                details     = '$details',
                steps       = $steps,
                total_form  = $total_form,
                updated_at  = CURRENT_TIMESTAMP,
                updated_by  = '$updated_by',
                updated_ip  = '$updated_ip'
            WHERE id = '$fms_id'";


        $rs=mysqli_query($this->conn, $sql);
        if(!rs){
            return ['status'=>false, "msg"=>'Some thins is wrong, Not Updated'];
        }
        return ['status'=>true, "msg"=>'Successfully Updated'];
    }
    public function addOperation($data = [], $updateBy){
        $fname       = mysqli_real_escape_string($this->conn, $data['fmsname']);
        $details     = mysqli_real_escape_string($this->conn, $data['details']);
        $steps       = (int)$data['steps'];
        $total_form  = (int)$data['total_form'];
        $updated_by  = mysqli_real_escape_string($this->conn, $updateBy);
        $updated_ip  = $_SERVER['REMOTE_ADDR'];

        $sql = "INSERT INTO fms_master 
            (fmsname, details, steps, total_form, updated_by, updated_ip, created_at, updated_at)
            VALUES 
            ('$fname', '$details', $steps, $total_form, '$updated_by', '$updated_ip', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        if(!mysqli_query($this->conn, $sql)){
            return ['status'=>false, "msg"=>mysqli_error($this->conn)];
        }

        return ['status'=>true, "msg"=>'Successfully Added'];
    }
    public function redirect($type,$msg){
        header("location:$this->location?pid=$this->pid&hid=$this->hid&type=$type&msg=$msg");
        exit();
    }
}

$fms=new FMS_Operations($pid,$hid,$location,$link1);


$show=null;
if(isset($_POST['add'])){
    $data=[];
    $data['fmsname']=$_POST['fmsname'];
    $data['details']=$_POST['fms_details'];
    $data['steps']=$_POST['steps'];
    $data['total_form']=$_POST['total_form'];
    $data['ip']=$_SERVER['REMOTE_ADDR'];
    try{
        $response=$fms->addOperation($data,$_SESSION['userid']);
        if($response['status']){
            $fms->redirect("success",$response['msg']);
        }else{
            $fms->redirect("error",$response['msg']);
        }
    }catch (Exception $e){
        throw new FMSExceptionHandler("error","some things is wrong",$location,$pid,$hid);
    }
}
if(isset($_POST['update'])){
    $data=[];
    $data['fmsname']=$_POST['fmsname'];
    $data['details']=$_POST['fms_details'];
    $data['steps']=$_POST['steps'];
    $data['total_form']=$_POST['total_form'];
    $data['ip']=$_SERVER['REMOTE_ADDR'];

    try{
        $resUp=$fms->updateOperation($data,$_SESSION['userid'],$fms_id);
//        $flag = dailyActivity($_SESSION['userid'],$jobno,"JOB","CREATE",$_SERVER['REMOTE_ADDR'],$link1,$flag);
        if($resUp['status']){
            $show=$resUp['msg'];
//            header("location:add_fms_master.php?pid=$pid&hid=$hid&msg=".$response['msg']);
//            exit();
        }else{
            $show=$resUp['msg'];
//            header("location:add_fms_master.php?pid=$pid&hid=$hid&msg=".$response['msg']);
//            exit();
        }
    }catch (Exception $e){
        throw new FMSExceptionHandler("error","some things is wrong",$location,$pid,$hid);
    }
}

if($fms_id){
    $load_fms=mysqli_query($link1,"select * from fms_master where id=$fms_id");
    $edit_data=mysqli_fetch_assoc($load_fms);

}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=siteTitle?></title>
    <meta http-equiv="refresh" content="1800">
    <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
    <script src="../js/jquery.js"></script>
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/abc.css" rel="stylesheet">
    <script src="../js/bootstrap.min.js"></script>
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script>
        $(document).ready(function(){
            $("#frm1").validate();
        });
    </script>
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <!-- Include Date Picker -->
    <script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
    <link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
    <style>
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .modal-box {
            background: #fff;
            width: 380px;
            border-radius: 12px;
            padding: 25px 20px;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0,0,0,0.25);
            animation: slideDown 0.3s ease;
        }

        .modal-body h2 {
            margin-bottom: 10px;
        }

        .modal-footer {
            margin-top: 20px;
        }



        @keyframes slideDown {
            from {
                transform: translateY(-40px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
<div id="customLoader">
    <div class="spinner"></div>
</div>
<div class="container-fluid">
    <div class="row content">
        <?php include("../includes/leftnav2.php"); ?>
        <div class="<?=$screenwidth?>">
            <h2 align="center"><i class="fa fa-users"></i> <?=$op==='edit'?'Update':'Add'?> FMS</h2><br/><br/>
            <?php if($show != null): ?>
                <div id="customModal" class="modal">
                    <div class="modal-box">

                        <div class="modal-body">
                            <h2><?php echo htmlspecialchars($show); ?></h2>
                        </div>

                        <div class="modal-footer">
                            <button class="btn" style="background-color: red;color: white" id="cancelBtn">Cancel</button>
                        </div>

                    </div>
                </div>
            <?php endif; ?>
            <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
                <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
                    <input type="hidden" name="csrf_token"
                           value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">

                    <!--                    userid and username -> read only -->
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="user_id" class="col-md-6 control-label">
                                Name<span class="red_small">*</span>
                            </label>
                            <div class="col-md-6">
                                <input name="fmsname" type="text" class="form-control"
                                       value="<?= $is_edit ? $edit_data['fmsname'] : '' ?>"
                                    <?= $is_edit ? '' : '' ?> required>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="col-md-6 control-label">Details<span class="red_small">*</span></label>
                            <div class="col-md-6">
                                <input name="fms_details" type="text" class="form-control"
                                       value="<?= $is_edit ? $edit_data['details'] : '' ?>"
                                       required>
                            </div>
                        </div>
                    </div>
                    <!--                    password id and mobile no-->
                    <div class="form-group">
                        <div class="col-md-6"><label class="col-md-6 control-label">No of Steps</label>
                            <div class="col-md-6">
                                <input name="steps" type="number" class="form-control"
                                       value="<?= $is_edit ? $edit_data['steps'] : '' ?>" required>

                            </div>
                        </div>
                        <div class="col-md-6"><label class="col-md-6 control-label">Total No form<span class="red_small">*</span></label>
                            <div class="col-md-6">
                                <input name="total_form" type="number" class="form-control"
                                       value="<?= $is_edit ? $edit_data['total_form'] : '' ?>" required>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="text-center mt-5">
                <button type="submit" name="<?= $is_edit ? 'update' : 'add' ?>"  class="btn btn-success">
                    <?= $is_edit ? 'Update' : 'Add' ?>
                </button>
                <span class="btn btn-primary" onclick="window.location.href='fms_master.php?pid=290&hid=Masters'">
                    <span id="operation_name">Cancel</span>
                </span>
            </div>
            </form>
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
        const modal = document.getElementById("customModal");
        const cancelBtn = document.getElementById("cancelBtn");

        function closeModal() {
            // modal.style.display = "none";
            window.location.href="./fms_master.php";
        }

        // ONLY cancel button closes modal
        cancelBtn.addEventListener("click", closeModal);
    });
</script>
</body>
</html>