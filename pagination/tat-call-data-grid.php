<?php
require_once("../includes/config.php");
require_once("tat-call-dbops-grid.php");
global $link1;
header("Content-type: application/json");
$arrstate = getAccessState($_SESSION['userid'],$link1);
function loaderUser_12($link1,$sapid,$userid){
    $restunrData=['type'=>'','data'=>''];
    $sql="select sapid,username,name , designation_id from admin_users where sapid='$sapid' and username='$userid'";
    $result=mysqli_query($link1,$sql);
    if(!$result){
        exit('Error'.__LINE__);
    }
    if(mysqli_num_rows($result)===0){
        exit('Error'.__LINE__);
    }
    $row=mysqli_fetch_assoc($result);
    if($row['designation_id']==='45'){
        $restunrData['type']='bsi';
    }else{
        $restunrData['type']='admin';
    }
    $restunrData['data']=$row;
    return $restunrData;
}

$access_brand = getAccessBrand($_SESSION['userid'],$link1);
$access_product = getAccessProduct($_SESSION['userid'],$link1);
$userid=$_SESSION['userid']??'';
$sapid=$_SESSION['sapid']??'';
$user=loaderUser_12($link1,$sapid,$userid);

class TATRetriverData{
    private $connection,$user,$typeofuser,$accessproduct,$accessstate,$conditions;
    private $wrapper;
    private $operations;
    public function __construct($connection,$user,$typeofuser,$accessproduct,$accessstate,$conditions){
        $this->connection=$connection;
        $this->user=$user;
        $this->typeofuser=$typeofuser;
        $this->accessproduct=$accessproduct;
        $this->accessstate=$accessstate;
        $this->conditions=$conditions;
        $this->wrapper=[];
        $this->operations=new TATCAllDBOps($connection,$user,$typeofuser,$accessproduct,$accessstate);
    }
    public function card1_totaljobs(){
        if(count($this->conditions)<=0){
            return "0";
        }
        if($this->operations===null){
            return "0";
        }

        if($this->operations instanceof TATCAllDBOps){
            return $this->operations->totalTat($this->conditions);
        }
        return "0";
    }
    public function card2_avgtat(){
        if(count($this->conditions)<=0){
            return 0;
        }
        if($this->operations===null){
            return 0;
        }
        if($this->operations instanceof TATCAllDBOps){
            return $this->operations->avgTat($this->conditions);
        }
        return "0";
    }
    public function card3_TAT_1(){
        if(count($this->conditions)<=0){
            return [0,"0%"];
        }
        if($this->operations===null){
            return [0,"0%"];
        }
        if($this->operations instanceof TATCAllDBOps){
            return $this->operations->tat_1_data($this->conditions);
        }
        return  [0,"0%"];
    }
    public function card4_TAT_2(){
        if(count($this->conditions)<=0){
            return ['0','0%'];
        }
        if($this->operations===null){
            return ['0','0%'];
        }
        if($this->operations instanceof TATCAllDBOps){
            return $this->operations->tat_2_data($this->conditions);
        }
        return ['0','0%'];
    }
    public function card5_TAT_3(){
        if(count($this->conditions)<=0){
            return  ['0','0%'];
        }
        if($this->operations===null){
            return  ['0','0%'];
        }
        if($this->operations instanceof TATCAllDBOps){
            return $this->operations->tat_3_data($this->conditions);
        }
        return  ['0','0%'];
    }
    public function ananlysisTreandTAT(){
        return $this->operations->ananlysisTreandTAT($this->conditions);
    }
    public function callBucket_Tat1(){
        return $this->operations->call_by_tat_1_bucket($this->conditions);
    }
    public function callBucket_Tat2(){
        return $this->operations->call_by_tat_2_bucket($this->conditions);
    }
    public function callBucket_Tat3(){
        return $this->operations->call_by_tat_3_bucket($this->conditions);
    }
    public function avgTatByProduct(){
        return [
            "battery"=>[
                "name"=>"Battery",
                "avg_tat"=>"38.3HRS",
                "min"=>"4",
                "max"=>"125",
                "status"=>"breached",
                "status_color"=>"red"
            ],
            "inverter"=>[
                "name"=>"Inverter",
                "avg_tat"=>"38.3HRS",
                "min"=>"4",
                "max"=>"125",
                "status"=>"Within TAT",
                "status_color"=>"green"
            ],
            "solor"=>[
                "name"=>"Solor",
                "avg_tat"=>"38.3HRS",
                "min"=>"4",
                "max"=>"125",
                "status"=>"At Risk",
                "status_color"=>"yellow"
            ]
        ];
    }
    public function zonewiseTat(){
        return [
            ["name"=>"North","per"=>"90%"],
            ["name"=>"South","per"=>"90%"],
            ["name"=>"East","per"=>"90%"],
            ["name"=>"West","per"=>"90%"],
            ["name"=>"Central","per"=>"90%"]
        ];
    }
    public function retriveData(){
        return [
            "card_data"=>[
                "total_jobs"=>$this->card1_totaljobs(),
                "avg_tat"=>$this->card2_avgtat(),
                "tat_1"=>$this->card3_TAT_1(),
                "tat2"=>$this->card4_TAT_2(),
                "tat_3"=>$this->card5_TAT_3()
            ],
            "line_chart" => $this->ananlysisTreandTAT(),
            "call_by_tat_1"=>$this->callBucket_Tat1(),
            "call_by_tat_2"=>$this->callBucket_Tat2(),
            "call_by_tat_3"=>$this->callBucket_Tat3(),
            "avg_tat_by_product"=>$this->avgTatByProduct(),
            "zone_wise_tat"=>$this->zonewiseTat()
        ];
    }
}

if(isset($_REQUEST['tat_data'])){
    $condition=[];
    $condition['date_range']=$_REQUEST['data_range']??'';
    $condition['zone']=$_REQUEST['zone']??'';
    $condition['state']=$_REQUEST['state']??'';
    $condition['bsi']=$_REQUEST['bsi']??'';
    $condition['eng_type']=$_REQUEST['eng_type']??'';
    $condition['product']=$_REQUEST['product']??'';
    echo json_encode((new TATRetriverData($link1,$user['data']??'',$user['type']??'',$access_product,$arrstate,$condition))->retriveData());exit();
}