<?php
interface CheckUserTAT{
    function whichtypeofUser();
}
class CheckUserImplementationTAT implements CheckUserTAT{
    private $connection;
    private $arrstate,$access_product ;
    public function __construct($connection,$arrstate,$access_product){
        $this->connection=$connection;
        $this->arrstate=$arrstate;
        $this->access_product=$access_product;
    }
    function whichtypeofUser()
    {
        $userid=$_SESSION['userid']??'';
        $sapid=$_SESSION['sapid']??'';

        if($userid===''){
            exit();
        }
        if($userid===''||$sapid===''){
            exit();
        }

        $user=$this->loaderUser($sapid,$userid);

        if($user['type']==='admin'){
            return new AdminFilterUILoader($this->connection,$user['data']['sapid'],$this->arrstate,$this->access_product);
        }
        else{
            return new BSIFilterUILoader($this->connection,$user['data']['sapid'],$this->arrstate,$this->access_product);
        }

    }
    private function loaderUser($sapid,$userid){
        $restunrData=['type'=>'','data'=>''];
        $sql="select sapid,username,name , designation_id from admin_users where sapid='$sapid' and username='$userid'";
        $result=mysqli_query($this->connection,$sql);
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

}

interface UILoaderTAT{
    function typeofUser(CheckUser $user);
    function zoneDisplay($req);
    function stateDisplay($req);
    function bsiDisplay($req);
    function enginnertype($req);
    function product($req);
    function errorUI();
}

class AdminFilterUILoader implements UILoaderTAT{

    private $connection;
    private $session_userid;
    private $arrstate,$access_product;
    public function __construct($connetion,$session_userid,$arrstate,$access_product ){
        $this->connection=$connetion;
        $this->session_userid=$session_userid;
        if (is_array($arrstate)) {
            $arrstate = implode(',', array_map('intval', $arrstate));
        }
        $this->arrstate=$arrstate;
        $this->access_product=$access_product;

    }

    private function selected($a, $b)
    {
        return ($a == $b) ? 'selected' : '';
    }
    function typeofUser(CheckUser $user)
    {
        // TODO: Implement typeofUser() method.
    }

    function zoneDisplay($req){
        $zone   = $req['zone']??'';
        $result = mysqli_query($this->connection, "SELECT zonename, zoneid FROM zone_master WHERE status='A' ORDER BY zonename");
        echo "<div>";
        echo "<label class='filter-label'>Zone</label>";
        echo "<select class='filter-select' id='zone_filter' name='zone' onchange='this.form.submit();'>";
        echo '<option value="">All Zones</option>';
        while ($row = mysqli_fetch_assoc($result)) {
            $sel = $this->selected($row['zoneid'], $zone);
            echo '<option value="' . $row['zoneid'] . '" ' . $sel . '>'
                . htmlspecialchars($row['zonename'])
                . '</option>';
        }
        echo "</select>";
        echo "</div>";
    }

    function stateDisplay($req)
    {
        $zone          = $req['zone']??'';
        $selectedState = $req['state']??'';
        $condition = '';
        if (!empty($zone)) {
            $condition = " AND zoneid = '" . intval($zone) . "' ";
        }
        $query = "
            SELECT stateid, state
            FROM state_master
            WHERE stateid IN ({$this->arrstate})
            {$condition}
            ORDER BY state
        ";
        $result = mysqli_query($this->connection, $query);

        echo '<div>';
        echo '<label class="filter-label">State</label>';
        echo '<select class="filter-select" id="state_filter" name="state" onchange="this.form.submit();">';
        echo '<option value="">Select</option>';
        while ($row = mysqli_fetch_assoc($result)) {
            $sel = $this->selected($selectedState, $row['stateid']);
            echo '<option value="' . $row['stateid'] . '" ' . $sel . '>'
                . htmlspecialchars($row['state'])
                . '</option>';
        }
        echo '</select>';
        echo '</div>';
    }

    function bsiDisplay($req)
    {
        $state = $req['state']??'';
        $bsi           = $req['bsi'];
        $allSelected   = (empty($bsi) || $bsi == '') ? 'selected' : '';

        $condition_bsi = "AND ar.stateid = '" . mysqli_real_escape_string($this->connection, $state) . "'";

        $sql    = "SELECT au.sapid, au.name
                   FROM access_region ar
                   LEFT JOIN admin_users au ON ar.userid = au.username
                   WHERE au.designation_id='45'
                     AND au.status='1'
                     AND ar.status='Y'
                     {$condition_bsi}
                   GROUP BY au.username";
        $result = mysqli_query($this->connection, $sql);

        echo '<div>';
        echo '<label class="filter-label">BSI</label>';
        echo '<select class="filter-select" id="bsi_filter">';
        echo '<option value="">All BSIs</option>';
        while ($row = mysqli_fetch_assoc($result)) {
            $sel = $this->selected($bsi, $row['sapid']);
            echo '<option value="' . htmlspecialchars($row['sapid']) . '" ' . $sel . '>'
                . htmlspecialchars($row['name'])
                . ' | '
                . htmlspecialchars($row['sapid'])
                . '</option>';
        }
        echo '</select>';
        echo '</div>';
    }

    function enginnertype($req){
        $enginner_type = $req['enginner_type']??'';
        $query="SELECT eng_type FROM `locationuser_master` WHERE eng_type is not null and eng_type <> '' GROUP by eng_type";
        $result = mysqli_query($this->connection, $query);
        echo '<div>';
        echo '<label class="filter-label">Engineer Type</label>';
        echo '<select class="filter-select" id="engineer-type" name="enginner_type" onchange="this.form.submit()">';
        echo '<option value="">All</option>';
        while ($row = mysqli_fetch_assoc($result)) {
            $value=$row['eng_type'];
            $sel = $this->selected($enginner_type, $row['eng_type']);
            if($value==='ASP'){
                $value='ASC';
            }
            echo '<option value="'.$row['eng_type'].'" '.$sel.'>'.$value.'</option>';
        }
        echo '</select>';
        echo '</div>';
    }

    function product($req)
    {
        $product_value = $req['product_value'] ?? '';

        echo '<div>';
        echo '<label class="filter-label">Product</label>';
        echo '<select class="filter-select"  id="product_filter"  name="product_value"  onchange="this.form.submit()">';
        echo '<option value="">All Products</option>';
        echo '<option value="1" ' . $this->selected($product_value, '1') . '>Inverter</option>';
        echo '<option value="2" ' . $this->selected($product_value, '2') . '>Battery</option>';
        echo '<option value="3" ' . $this->selected($product_value, '3') . '>Solar Product</option>';
        echo '</select>';
        echo '</div>';
    }
     function errorUI($message = "Something went wrong!")
    {
        echo '
    <style>

        .error-ui-overlay{
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100vh;
            background:rgba(255,255,255,0.96);
            z-index:999999;
            display:flex;
            justify-content:center;
            align-items:center;
            padding:20px;
            box-sizing:border-box;
        }

        .error-ui-card{
            width:100%;
            max-width:500px;
            background:#fff;
            border-radius:14px;
            padding:35px 30px;
            text-align:center;
            box-shadow:0 10px 40px rgba(0,0,0,0.12);
            border:1px solid #f1f1f1;
            animation:errorFade .3s ease;
            position:relative;
        }

        .error-ui-icon{
            width:80px;
            height:80px;
            margin:0 auto 20px;
            border-radius:50%;
            background:#ffe9e9;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:40px;
            color:#ff3b3b;
            font-weight:bold;
        }

        .error-ui-title{
            font-size:28px;
            font-weight:700;
            color:#222;
            margin-bottom:12px;
        }

        .error-ui-message{
            font-size:15px;
            line-height:1.7;
            color:#666;
            margin-bottom:25px;
        }

        .error-ui-btn{
            display:inline-block;
            padding:12px 24px;
            background:#111;
            color:#fff;
            border-radius:8px;
            text-decoration:none;
            transition:.2s;
            font-size:14px;
            font-weight:600;
            border:none;
            cursor:pointer;
        }

        .error-ui-btn:hover{
            transform:translateY(-2px);
            opacity:.9;
        }

        @keyframes errorFade{
            from{
                opacity:0;
                transform:scale(.95);
            }
            to{
                opacity:1;
                transform:scale(1);
            }
        }

        @media(max-width:576px){

            .error-ui-card{
                padding:25px 20px;
            }

            .error-ui-title{
                font-size:22px;
            }

            .error-ui-message{
                font-size:14px;
            }
        }

    </style>

    <div class="error-ui-overlay">

        <div class="error-ui-card">

            <div class="error-ui-icon">
                !
            </div>

            <div class="error-ui-title">
                Error
            </div>

            <div class="error-ui-message">
                '.$message.'
            </div>

            <button 
                onclick="location.reload();" 
                class="error-ui-btn">
                Reload Page
            </button>

        </div>

    </div>
    ';
    }
}
class BSIFilterUILoader implements UILoaderTAT{

    private $connection;
    private $session_userid;
    private $arrstate,$access_product;
    public function __construct($connetion,$session_userid,$arrstate,$access_product ){
        $this->connection=$connetion;
        $this->session_userid=$session_userid;
        if (is_array($arrstate)) {
            $arrstate = implode(',', array_map('intval', $arrstate));
        }
        $this->arrstate=$arrstate;
        $this->access_product=$access_product;
    }
    //  Helper: selected attribute
    private function selected($a, $b)
    {
        return ($a == $b) ? 'selected' : '';
    }
    function typeofUser(CheckUser $user)
    {
        // TODO: Implement typeofUser() method.
    }

    function zoneDisplay($req)
    {
        $zone   = $req['zone']??'';
        $sql    = "SELECT zm.zoneid,zm.zonename FROM access_region ar 
                    LEFT JOIN admin_users au ON 
                           ar.userid = au.username 
                    Left JOIN state_master sm on 
                        ar.stateid=sm.stateid 
                    LEFT JOIN zone_master zm 
                        on sm.zoneid=zm.zoneid 
               WHERE au.designation_id='45' AND au.status='1' AND ar.status='Y' AND au.sapid='$this->session_userid' and zm.status='A'
                   GROUP BY au.username";
        $result = mysqli_query($this->connection, $sql);
        echo "<div>";
        echo "<label class='filter-label'>Zone</label>";
        echo "<select class='filter-select' id='zone_filter' name='zone' onchange='this.form.submit();'>";
        while ($row = mysqli_fetch_assoc($result)) {
            $sel = $this->selected($row['zoneid'], $zone);
            echo '<option value="' . $row['zoneid'] . '" ' . $sel . '>'
                . htmlspecialchars($row['zonename'])
                . '</option>';
        }
        echo "</select>";
        echo "</div>";
    }

    function stateDisplay($req){
        $zone          = $req['zone']??'';
        $selectedState = $req['state']??'';
        $condition = '';
        if (!empty($zone)) {
            $condition = " AND zoneid = '" . intval($zone) . "' ";
        }
        $query = "
            SELECT stateid, state
            FROM state_master
            WHERE stateid IN ({$this->arrstate})
            {$condition}
            ORDER BY state
        ";
        $result = mysqli_query($this->connection, $query);

        echo '<div>';
        echo '<label class="filter-label">State</label>';
        echo '<select class="filter-select" id="state_filter" name="state" onchange="this.form.submit();">';
        echo '<option value="">Select</option>';
        while ($row = mysqli_fetch_assoc($result)) {
            $sel = $this->selected($selectedState, $row['stateid']);
            echo '<option value="' . $row['stateid'] . '" ' . $sel . '>'
                . htmlspecialchars($row['state'])
                . '</option>';
        }
        echo '</select>';
        echo '</div>';
    }

    function bsiDisplay($req)
    {
        $state = $req['state']??'';
        $bsi           = $req['bsi'];
        $allSelected   = (empty($bsi) || $bsi == '') ? 'selected' : '';

        if($state!==''){
            $condition_bsi = "AND ar.stateid = '" . mysqli_real_escape_string($this->connection, $state) . "'";
        }

        $sql    = "SELECT au.sapid, au.name
                   FROM access_region ar
                   LEFT JOIN admin_users au ON ar.userid = au.username
                   WHERE au.designation_id='45'
                     AND au.status='1'
                     AND ar.status='Y'
                     AND au.sapid='$this->session_userid'
                     {$condition_bsi}
                   GROUP BY au.username";

        $result = mysqli_query($this->connection, $sql);
        echo '<div>';
        echo '<label class="filter-label">BSI</label>';
        echo '<select class="filter-select" id="bsi_filter" name="bsi" onchange="this.form.submit()">';
        while ($row = mysqli_fetch_assoc($result)) {
            $sel = $this->selected($bsi, $row['sapid']);
            echo '<option value="' . htmlspecialchars($row['sapid']) . '" ' . $sel . '>'
                . htmlspecialchars($row['name'])
                . ' | '
                . htmlspecialchars($row['sapid'])
                . '</option>';
        }
        echo '</select>';
        echo '</div>';
    }


    function enginnertype($req){
        $enginner_type = $req['enginner_type']??'';
        $query="SELECT eng_type FROM `locationuser_master` WHERE eng_type is not null and eng_type <> '' GROUP by eng_type";
        $result = mysqli_query($this->connection, $query);
        echo '<div>';
        echo '<label class="filter-label">Engineer Type</label>';
        echo '<select class="filter-select" id="engineer-type" name="enginner_type" onchange="this.form.submit()">';
        echo '<option value="">All</option>';
        while ($row = mysqli_fetch_assoc($result)) {
            $value=$row['eng_type'];
            $sel = $this->selected($enginner_type, $row['eng_type']);
            if($value==='ASP'){
                $value='ASC';
            }
            echo '<option value="'.$row['eng_type'].'" '.$sel.'>'.$value.'</option>';
        }
        echo '</select>';
        echo '</div>';
    }

    function product($req)
    {
        $product_value = $req['product_value'] ?? '';

        echo '<div>';
        echo '<label class="filter-label">Product</label>';
        echo '<select class="filter-select"  id="product_filter"  name="product_value"  onchange="this.form.submit()">';
        echo '<option value="">All Products</option>';
        echo '<option value="1" ' . $this->selected($product_value, '1') . '>Inverter</option>';
        echo '<option value="2" ' . $this->selected($product_value, '2') . '>Battery</option>';
        echo '<option value="3" ' . $this->selected($product_value, '3') . '>Solar Product</option>';
        echo '</select>';
        echo '</div>';
    }
    function errorUI($message = "Something went wrong!")
    {
        echo '
    <style>

        .error-ui-overlay{
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100vh;
            background:rgba(255,255,255,0.96);
            z-index:999999;
            display:flex;
            justify-content:center;
            align-items:center;
            padding:20px;
            box-sizing:border-box;
        }

        .error-ui-card{
            width:100%;
            max-width:500px;
            background:#fff;
            border-radius:14px;
            padding:35px 30px;
            text-align:center;
            box-shadow:0 10px 40px rgba(0,0,0,0.12);
            border:1px solid #f1f1f1;
            animation:errorFade .3s ease;
            position:relative;
        }

        .error-ui-icon{
            width:80px;
            height:80px;
            margin:0 auto 20px;
            border-radius:50%;
            background:#ffe9e9;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:40px;
            color:#ff3b3b;
            font-weight:bold;
        }

        .error-ui-title{
            font-size:28px;
            font-weight:700;
            color:#222;
            margin-bottom:12px;
        }

        .error-ui-message{
            font-size:15px;
            line-height:1.7;
            color:#666;
            margin-bottom:25px;
        }

        .error-ui-btn{
            display:inline-block;
            padding:12px 24px;
            background:#111;
            color:#fff;
            border-radius:8px;
            text-decoration:none;
            transition:.2s;
            font-size:14px;
            font-weight:600;
            border:none;
            cursor:pointer;
        }

        .error-ui-btn:hover{
            transform:translateY(-2px);
            opacity:.9;
        }

        @keyframes errorFade{
            from{
                opacity:0;
                transform:scale(.95);
            }
            to{
                opacity:1;
                transform:scale(1);
            }
        }

        @media(max-width:576px){

            .error-ui-card{
                padding:25px 20px;
            }

            .error-ui-title{
                font-size:22px;
            }

            .error-ui-message{
                font-size:14px;
            }
        }

    </style>

    <div class="error-ui-overlay">

        <div class="error-ui-card">

            <div class="error-ui-icon">
                !
            </div>

            <div class="error-ui-title">
                Error
            </div>

            <div class="error-ui-message">
                '.$message.'
            </div>

            <button 
                onclick="location.reload();" 
                class="error-ui-btn">
                Reload Page
            </button>

        </div>

    </div>
    ';
    }
}








interface LoadCard{
    function loadcard();
}

/*
 Component (card-1 (fullwidth), card2,card3,card4 (33.33 x 3) , card5 , card 6 (40% , 60%), card 7.... nth Card (50% width) )
 $ui->render(1,'card')
 */
class UIRender {
    public static function render($order=0){
        if($order===1){
            // card 1 render here
        }
        else if ($order===2){
            // Calls by Aging Bucket
        }
        else if ($order===3){
            // TAT Status Split
        }
    }
}

