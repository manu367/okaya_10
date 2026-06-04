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
        $bsi           = $req['bsi']??'';
        $allSelected   = (empty($bsi) || $bsi == '') ? 'selected' : '';

        $condition_bsi = '';
        if (!empty($state)) {
            $condition_bsi = "AND ar.stateid = '"
                . mysqli_real_escape_string($this->connection, $state)
                . "'";
        }
//        $condition_bsi = "AND ar.stateid = '" . mysqli_real_escape_string($this->connection, $state) . "'";

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
        echo '<select name="bsi" class="filter-select" id="bsi_filter">';
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
    private $connection,$session_userid,$arrstate,$access_product;
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
        $bsi           = $req['bsi']??'';
        $allSelected   = (empty($bsi) || $bsi == '') ? 'selected' : '';

        $condition_bsi = '';
        if (!empty($state)) {
            $condition_bsi = "AND ar.stateid = '"
                . mysqli_real_escape_string($this->connection, $state)
                . "'";
        }
//        if($state!==''){
//            $condition_bsi = "AND ar.stateid = '" . mysqli_real_escape_string($this->connection, $state) . "'";
//        }

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
    function uiloader();
}

class KPIsCardLoader implements LoadCard {
    public function uiloader()
    {
        echo '<div class="tat-kpi-grid" style="margin-top:20px;">
                    <!-- Total Jobs -->
                    <div id="total_jobs" class="tat-kpi-card tat-kc-blue tat-anim" style="animation-delay:.05s">
                        <div class="tat-kpi-top">
                            <div class="tat-kpi-label">Total Closed Jobs</div>
                            <div class="tat-kpi-icon">📋</div>
                        </div>
                        <div class="tat-kpi-value">—</div>
                        <div class="tat-kpi-sub">This period</div>
                    </div>

                    <!-- Avg TAT -->
                    <div id="avg_tat_card" class="tat-kpi-card tat-kc-purple tat-anim" style="animation-delay:.09s">
                        <div class="tat-kpi-top">
                            <div class="tat-kpi-label">Avg TAT</div>
                            <div class="tat-kpi-icon">📊</div>
                        </div>
                        <div class="tat-kpi-value">—</div>
                        <div class="tat-kpi-sub">Average TAT days</div>
                    </div>

                    <!-- TAT-1 -->
                    <div id="within_tat_card" class="tat-kpi-card tat-kc-amber tat-anim" style="animation-delay:.13s">
                        <div class="tat-kpi-top">
                            <div class="tat-kpi-label">TAT-1 Close Jobs</div>
                            <div class="tat-kpi-icon">🎯</div>
                        </div>
                        <div class="tat-kpi-value" id="tat1_val">—</div>
                        <div class="tat-kpi-sub">— jobs</div>
                        <div class="tat-kpi-sub" id="tat1_pct" style="display: none">— calls</div>
                        <div class="tat-kpi-bar-bg" style="display: none">
                            <div class="tat-kpi-bar-fill tat-kpi-bar-amber" id="tat1_bar" style="width:0%"></div>
                        </div>
                    </div>

                    <!-- TAT-2 -->
                    <div id="sla_breached_card" class="tat-kpi-card tat-kc-red tat-anim" style="animation-delay:.17s">
                        <div class="tat-kpi-top">
                            <div class="tat-kpi-label">TAT-2 Close Job</div>
                            <div class="tat-kpi-icon">💾</div>
                        </div>
                        <div class="tat-kpi-value" id="tat2_val">—</div>
                        <div class="tat-kpi-sub">— of jobs</div>
                        <div class="tat-kpi-sub" id="tat2_pct" style="display: none">— of jobs</div>
                        <div class="tat-kpi-bar-bg hidden">
                            <div class="tat-kpi-bar-fill tat-kpi-bar-red" id="tat2_bar" style="width:0%"></div>
                        </div>
                    </div>

                    <!-- TAT-3 -->
                    <div id="at_risk_card" class="tat-kpi-card tat-kc-green tat-anim" style="animation-delay:.21s">
                        <div class="tat-kpi-top">
                            <div class="tat-kpi-label">TAT-3 Close Job</div>
                            <div class="tat-kpi-icon">📝</div>
                        </div>
                        <div class="tat-kpi-value" id="tat3_val">—</div>
                        <div class="tat-kpi-sub">— of jobs</div>
                        <div class="tat-kpi-sub hidden" id="tat3_pct">— of jobs</div>
                        <div class="tat-kpi-bar-bg hidden">
                            <div class="tat-kpi-bar-fill tat-kpi-bar-green" id="tat3_bar" style="width:0%"></div>
                        </div>
                    </div>

                </div>';
    }
}

class TrendAnalaysisChart implements LoadCard{
    public function uiloader(){
        echo '<div class="tat-row tat-col-1" style="margin-bottom:14px;">
                    <div class="tat-card tat-anim" style="animation-delay:.25s">
                        <div class="tat-ct">TAT Trend Analysis</div>
                        <div class="tat-cs">Daily TAT trend over selected period — multiple series</div>
                        <div id="tatTrendChart" style="height:280px;"></div>
                    </div>
                </div>';
    }
}

class BucketCard implements LoadCard{
    public function uiloader(){
        echo '<div class="tat-row tat-col-21" style="margin-bottom:14px;">

                    <!-- TAT-1 Bucket -->
                    <div class="tat-card tat-anim" style="animation-delay:.33s">
                        <div class="tat-ct">Calls by TAT-1 Bucket</div>
                        <div class="tat-cs">TAT distribution across time ranges</div>
                        <div class="tat-bucket-list" id="tatBucketWrap">
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> <=24 HRS</div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#0aaa6e" data-count="0" data-pct="0%" data-color="#0aaa6e"></div></div>
                                <div class="tat-bucket-val" style="color:#0aaa6e">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"><=36 HRS</div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#2355f5" data-count="0" data-pct="0%" data-color="#2355f5"></div></div>
                                <div class="tat-bucket-val" style="color:#2355f5">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"><=48 HRS</div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#7c3aed" data-count="0" data-pct="0%" data-color="#7c3aed"></div></div>
                                <div class="tat-bucket-val" style="color:#7c3aed">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> <=72 HRS</div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#e6900a" data-count="0" data-pct="0%" data-color="#e6900a"></div></div>
                                <div class="tat-bucket-val" style="color:#e6900a">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> >72HRS </div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#e8344a" data-count="0" data-pct="0%" data-color="#e8344a"></div></div>
                                <div class="tat-bucket-val" style="color:#e8344a">—</div>
                            </div>
                        </div>
                    </div>

                    <!-- TAT-2 Bucket -->
                    <div class="tat-card tat-anim" style="animation-delay:.36s">
                        <div class="tat-ct">Calls by TAT-2 Bucket</div>
                        <div class="tat-cs">TAT distribution across time ranges</div>
                        <div class="tat-bucket-list" id="tatBucketWrap-2">
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> <=3 Days </div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#0aaa6e" data-count="0" data-pct="0%" data-color="#0aaa6e"></div></div>
                                <div class="tat-bucket-val" style="color:#0aaa6e">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> <=5 Days </div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#2355f5" data-count="0" data-pct="0%" data-color="#2355f5"></div></div>
                                <div class="tat-bucket-val" style="color:#2355f5">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"><=7 Days</div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#7c3aed" data-count="0" data-pct="0%" data-color="#7c3aed"></div></div>
                                <div class="tat-bucket-val" style="color:#7c3aed">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> >7 Days </div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#e6900a" data-count="0" data-pct="0%" data-color="#e6900a"></div></div>
                                <div class="tat-bucket-val" style="color:#e6900a">—</div>
                            </div>
                        </div>
                    </div>

                    <!-- TAT-3 Bucket -->
                    <div class="tat-card tat-anim" style="animation-delay:.39s">
                        <div class="tat-ct">Calls by TAT-3 Bucket</div>
                        <div class="tat-cs">TAT distribution across time ranges</div>
                        <div class="tat-bucket-list" id="tatBucketWrap-3">
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> <=7 Days  </div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#0aaa6e" data-count="0" data-pct="0%" data-color="#0aaa6e"></div></div>
                                <div class="tat-bucket-val" style="color:#0aaa6e">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> <=15 Days </div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#2355f5" data-count="0" data-pct="0%" data-color="#2355f5"></div></div>
                                <div class="tat-bucket-val" style="color:#2355f5">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> <=21 Days </div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#7c3aed" data-count="0" data-pct="0%" data-color="#7c3aed"></div></div>
                                <div class="tat-bucket-val" style="color:#7c3aed">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"><=30 Days</div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#e6900a" data-count="0" data-pct="0%" data-color="#e6900a"></div></div>
                                <div class="tat-bucket-val" style="color:#e6900a">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> >30 Days </div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#e8344a" data-count="0" data-pct="0%" data-color="#e8344a"></div></div>
                                <div class="tat-bucket-val" style="color:#e8344a">—</div>
                            </div>
                        </div>
                    </div>

                </div>';
    }
}

class ZoneandAvgTat implements LoadCard{
    public function  uiloader(){
        echo '<div class="tat-row tat-col-2" style="margin-bottom:14px;">

                    <!-- Avg TAT by Product -->
                    <div class="tat-card tat-anim" style="animation-delay:.41s">
                        <div class="tat-ct">Avg TAT by Product</div>
                        <div class="tat-cs">Product-wise turnaround comparison</div>
                        <table class="tat-tbl" id="tat_table">
                            <thead>
                            <tr>
                                <th>Product</th>
                                <th>Avg TAT</th>
                                <th>Min</th>
                                <th>Max</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:20px;">Apply filters to load data</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Zone Wise TAT -->
                    <div class="tat-card tat-anim" style="animation-delay:.45s">
                        <div class="tat-ct">Zone Wise TAT</div>
                        <div class="tat-cs">Avg TAT breach % per zone</div>
                        <div class="tat-zone-list" id="tat-zone-list"></div>
                    </div>

                </div>';
    }
}
class FooterUiRendering implements  LoadCard{
    public function uiloader(){
        echo '<div class="tat-footer">
                    <div class="tat-fleg">
                        <div class="tat-fleg-item"><div class="tat-fleg-dot" style="background:var(--green)"></div>Within TAT (&lt;24 HRS)</div>
                        <div class="tat-fleg-item"><div class="tat-fleg-dot" style="background:var(--amber)"></div>At Risk (24–72 HRS)</div>
                        <div class="tat-fleg-item"><div class="tat-fleg-dot" style="background:var(--red)"></div>Breached (&gt;72 HRS)</div>
                    </div>
                    <div>Updated: '.date('d M Y, h:i A').'</div>
                </div>';
    }
}
class LoaderCardFactory{
    public static function cardfactory($value=0){
        if($value===1){
            return new KPIsCardLoader();
        }
        if($value===2){
            return new TrendAnalaysisChart();
        }
        if($value===3){
            return new BucketCard();
        }
        if($value===4){
            return new ZoneandAvgTat();
        }
        if($value===5){
            return new FooterUiRendering();
        }
        return 0;
    }
}

class UIRender {
    public static function render($orders=[1,2,3,4,5]){
        echo '<section class="" id="dashboard_home">';
        if(!is_array($orders)){
            return;
        }
        foreach ($orders as $order) {
            (LoaderCardFactory::cardfactory($order))->uiloader();
        }
        echo '<section>';
    }
}