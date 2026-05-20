<?php
require_once("../includes/config.php");

class CallCenter implements SplSubject{
    private  $observers=[];
    private  $data = '';
    public function attach(SplObserver $observer){$this->observers[]=$observer;}
    public function detach(SplObserver $observer){}
    public function notify(){foreach ($this->observers as $observer){$observer->update($this);}}
    public function setData($news){$this->data.=$news."<br/>";}
    public function getData(){return $this->data;}
}



class Admin implements SplObserver {
    private $name="";
    function __construct($name){$this->name=$name;}
    public function update(SplSubject $subject){echo "Admin : ".$subject->getData();}
}
class ASP implements SplObserver {
    private $name="";
    function __construct($name){$this->name=$name;}
    public function update(SplSubject $subject){echo "<br/>"."ASP : ".$subject->getData();}
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
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    <title><?=siteTitle?></title>
    <script>
        $(document).ready(function () {
            $('#myacc-users-grid').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "../pagination/enginner_master_grid-data.php",
                    type: "POST",
                    data: {
                        pid: "<?= $_REQUEST['pid'] ?? '' ?>",
                        hid: "<?= $_REQUEST['hid'] ?? '' ?>"
                    },
                    beforeSend: function () {
                        //$("#customLoader").fadeIn(200);
                    },
                    complete: function () {
                        //$("#customLoader").fadeOut(200);
                    },
                    error: function () {
                        $("#myacc-users-grid").append(
                            '<tbody><tr><td colspan="10">No data found</td></tr></tbody>'
                        );
                       // $("#myacc-users-grid_processing").hide();
                    }
                }
            });
        });
    </script>
</head>
<body>
<div class="container-fluid">
    <div class="row content">
        <?php
        include("../includes/leftnav2.php");
        ?>
        <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
            <h2 align="center" style="font-style: italic"><i class="fa fa-users"></i> Enginner Master</h2>
            <span><?=$_SESSION['csrf_manu']?></span>
            <?php
            openssl_encrypt("this is mnu pathak")
            ?>
<!--            pid and hid hidden form-->
            <form class="form-horizontal" role="form" name="form1" action="" method="get">
                <div class="form-group">
                    <div class="col-md-6"><label class="col-md-5 control-label"></label>
                        <div class="col-md-5">
                            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                        </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-5 control-label"></label>
                        <div class="col-md-5" align="left">
                        </div>
                    </div>
                </div><!--close form group-->
            </form>
            <div style="text-align: end">
                <button
                    class="btn btn-primary"
                    onclick="window.location.href='enginner_master_op.php?op=add&pid=<?=isset($_REQUEST['pid'])?>&hid=<?=isset($_REQUEST['hid'])?>'">
                    Add User
                </button>
            </div>
<!--            main form for showing data-->
            <form class="form-horizontal" role="form">
                &nbsp;&nbsp;
                <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>

                    <table  width="100%" id="myacc-users-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
                        <thead>
                        <tr class="<?=$tableheadcolor?>">
                            <th>S.No</th>
                            <th>Login Id</th>
                            <th>UserName</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Mapped BSI</th>
                            <th>Mapped RM</th>
                            <th>Spare Location Code</th>
                            <th>View/Edit</th>

                        </tr>
                        </thead>
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
<div id="container">
    <button>Button 1</button>
    <button>Button 2</button>
    <button>Button 3</button>
</div>
<script>
    document.getElementById("container").addEventListener("click", function(e) {
        if (e.target.tagName === "BUTTON") {
            console.log(e.target);
        }
    });

    // document.getElementById("myacc-users-grid").addEventListener("click",function (e){
    //     if(e.target.tagName==="TD"){
    //        alert(e.target.textContent);
    //     }
    // });
    document.addEventListener("mouseup", function () {
        let selection = window.getSelection();

        if (selection.rangeCount > 0) {
            let element = selection.getRangeAt(0).startContainer.parentElement;

            let cssText = "";

            for (let i = 0; i < element.style.length; i++) {
                let prop = element.style[i];
                cssText += prop + ": " + element.style.getPropertyValue(prop) + ";\n";
            }

            console.log("Selected Element:", element);
            console.log(cssText || "No inline styles");
        }
    });
    // document.addEventListener("mouseup", function () {
    //     let selection = window.getSelection();
    //
    //     if (selection.rangeCount > 0) {
    //         let element = selection.getRangeAt(0).startContainer.parentElement;
    //         let styles = window.getComputedStyle(element);
    //         let cssText = "";
    //         for (let i = 0; i < styles.length; i++) {
    //             let prop = styles[i];
    //             cssText += prop + ": " + styles.getPropertyValue(prop) + ";\n";
    //         }
    //         console.log("Selected Element:", element);
    //         console.log(cssText);
    //     }
    // });
    function currying(a){
        return function (b){
            return function(fn){
                fn(a,b);
            }
        }
    }
    currying(10)(20)(function(a,b){
        console.log(parseInt(a));
        console.log(parseInt(b));
    });

</script>
</body>
</html>