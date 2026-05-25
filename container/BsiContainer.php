<?php
//interface CheckUser{
//    function whichtypeofUser();
//}
//interface UILoader{
//    function typeofUser(CheckUser $user);
//    function zoneDisplay($req);
//    function stateDisplay($req);
//    function bsiDisplay($req);
//    function bsiEnginnerDisplay($req);
//}
//
//class CheckUserImplementation implements CheckUser{
//    private $connection;
//    private $arrstate,$access_product ;
//    public function __construct($connection,$arrstate,$access_product){
//        $this->connection=$connection;
//        $this->arrstate=$arrstate;
//        $this->access_product=$access_product;
//    }
//    function whichtypeofUser()
//    {
//        $userid=$_SESSION['userid']??'';
//        $sapid=$_SESSION['sapid']??'';
//
//        if($userid===''){
//            exit();
//        }
//        if($userid===''||$sapid===''){
//            exit();
//        }
//
//        $user=$this->loaderUser($sapid,$userid);
//
//        if($user['type']==='admin'){
//            return new AdminUiLoader($this->connection,$user['data']['sapid'],$this->arrstate,$this->access_product);
//        }
//        else{
//            return new BSIUILoader($this->connection,$user['data']['sapid'],$this->arrstate,$this->access_product);
//        }
//
//    }
//    private function loaderUser($sapid,$userid){
//        $restunrData=['type'=>'','data'=>''];
//        $sql="select sapid,username,name , designation_id from admin_users where sapid='$sapid' and username='$userid'";
//        $result=mysqli_query($this->connection,$sql);
//        if(!$result){
//            exit('Error'.__LINE__);
//        }
//        if(mysqli_num_rows($result)===0){
//            exit('Error'.__LINE__);
//        }
//        $row=mysqli_fetch_assoc($result);
//        if($row['designation_id']==='45'){
//            $restunrData['type']='bsi';
//        }else{
//            $restunrData['type']='admin';
//        }
//        $restunrData['data']=$row;
//        return $restunrData;
//    }
//
//}
//
//
//class AdminUiLoader implements UILoader{
//
//    private $connection;
//    private $session_userid;
//    private $arrstate,$access_product;
//    public function __construct($connetion,$session_userid,$arrstate,$access_product ){
//        $this->connection=$connetion;
//        $this->session_userid=$session_userid;
//        if (is_array($arrstate)) {
//            $arrstate = implode(',', array_map('intval', $arrstate));
//        }
//        $this->arrstate=$arrstate;
//        $this->access_product=$access_product;
//    }
//    //  Helper: selected attribute
//    private function selected($a, $b)
//    {
//        return ($a == $b) ? 'selected' : '';
//    }
//
//    function typeofUser(CheckUser $user){
//        // another implement time use : when user want to any extra feturatres and call here only
//        // This function is used for Plugin System Architutre : dynamic binding (function , classes)
//    }
//
//    function zoneDisplay($req){
//        $zone   = $req['zone']??'';
//        $result = mysqli_query($this->connection, "SELECT zonename, zoneid FROM zone_master WHERE status='A' ORDER BY zonename");
//        echo '<div class="filter-group">';
//        echo '<label>Zone</label>';
//        echo '<select name="zone" onchange="this.form.submit();">';
//        echo '<option value="">Select Zone</option>';
//        while ($row = mysqli_fetch_assoc($result)) {
//            $sel = $this->selected($row['zoneid'], $zone);
//            echo '<option value="' . $row['zoneid'] . '" ' . $sel . '>'
//                . htmlspecialchars($row['zonename'])
//                . '</option>';
//        }
//        echo '</select>';
//        echo '</div>';
//        echo '<script>
//            function changeZone(zoneId) {
//                document.querySelector(\'select[name="state"]\').value = \'-1\';
//                document.getElementById(\'form1\').submit();
//            }
//        </script>';
//
//    }
//
//    function stateDisplay($req){
//        $zone          = $req['zone']??'';
//        $selectedState = $req['state']??'';
//        $condition = '';
//        if (!empty($zone)) {
//            $condition = " AND zoneid = '" . intval($zone) . "' ";
//        }
//        $query = "
//            SELECT stateid, state
//            FROM state_master
//            WHERE stateid IN ({$this->arrstate})
//            {$condition}
//            ORDER BY state
//        ";
//        $result = mysqli_query($this->connection, $query);
//
//
//        echo '<div class="filter-group">';
//        echo '<label>State</label>';
//        echo '<select name="state" onchange="this.form.submit();">';
////        if(mysqli_num_rows($result)===0){
////            echo '<option value="-1">No State Found</option>';
////        }
//        echo '<option value="">All</option>';
//        while ($row = mysqli_fetch_assoc($result)) {
//            $sel = $this->selected($selectedState, $row['stateid']);
//            echo '<option value="' . $row['stateid'] . '" ' . $sel . '>'
//                . htmlspecialchars($row['state'])
//                . '</option>';
//        }
//        echo '</select>';
//        echo '</div>';
//    }
//
//    function bsiDisplay($req){
//        $state = $req['state']??'';
//        $bsi           = $req['bsi'];
//        $allSelected   = (empty($bsi) || $bsi == '') ? 'selected' : '';
//
//        $condition_bsi = "AND ar.stateid = '" . mysqli_real_escape_string($this->connection, $state) . "'";
//
//
//        $sql    = "SELECT au.sapid, au.name
//                   FROM access_region ar
//                   LEFT JOIN admin_users au ON ar.userid = au.username
//                   WHERE au.designation_id='45'
//                     AND au.status='1'
//                     AND ar.status='Y'
//                     {$condition_bsi}
//                   GROUP BY au.username";
//        $result = mysqli_query($this->connection, $sql);
//        echo '<div class="filter-group">';
//        echo '<label>BSI</label>';
//        echo '<select name="bsi" onchange="this.form.submit()">';
//        echo '<option value="" ' . $allSelected . '>All</option>';
//
//        while ($row = mysqli_fetch_assoc($result)) {
//            $sel = $this->selected($bsi, $row['sapid']);
//            echo '<option value="' . htmlspecialchars($row['sapid']) . '" ' . $sel . '>'
//                . htmlspecialchars($row['name'])
//                . ' | '
//                . htmlspecialchars($row['sapid'])
//                . '</option>';
//        }
//
//        echo '</select>';
//        echo '</div>';
//    }
//
//    function bsiEnginnerDisplay($req){
//        $bsi    = $req['bsi']??'';
//        $state  = $req['state']??'';
//
//        if (empty($bsi) || empty($state)) return;
//
//        $enginner          = $req['enginner'];
//        $condition_enginner = "";
//        if (!empty($bsi)) {
//            $condition_enginner .= " AND mapped_bsi = '"
//                . mysqli_real_escape_string($this->connection, $bsi) . "' ";
//        }
//        if (!empty($state)) {
//            $condition_enginner .= " AND stateid = '"
//                . mysqli_real_escape_string($this->connection, $state) . "' ";
//        }
//        $sql    = "SELECT userloginid, locusername
//                   FROM locationuser_master
//                   WHERE statusid='1'
//                     AND eng_type <> ''
//                     {$condition_enginner}
//                   ORDER BY locusername";
//        $result = mysqli_query($this->connection, $sql);
//        echo '<div class="filter-group">';
//        echo '<label>Engineer</label>';
//        echo '<select name="enginner" onchange="this.form.submit()">';
//        echo '<option value="">All</option>';
//        while ($row = mysqli_fetch_assoc($result)) {
//            $sel = $this->selected($enginner, $row['userloginid']);
//            echo '<option value="' . htmlspecialchars($row['userloginid']) . '" ' . $sel . '>'
//                . htmlspecialchars($row['locusername'])
//                . ' | '
//                . htmlspecialchars($row['userloginid'])
//                . '</option>';
//        }
//        echo '</select>';
//        echo '</div>';
//    }
//}
//
//class BSIUILoader implements UILoader{
//    private $connection;
//    private $session_userid;
//    private $arrstate,$access_product;
//    public function __construct($connetion,$session_userid,$arrstate,$access_product ){
//        $this->connection=$connetion;
//        $this->session_userid=$session_userid;
//        if (is_array($arrstate)) {
//            $arrstate = implode(',', array_map('intval', $arrstate));
//        }
//        $this->arrstate=$arrstate;
//        $this->access_product=$access_product;
//    }
//    //  Helper: selected attribute
//    private function selected($a, $b)
//    {
//        return ($a == $b) ? 'selected' : '';
//    }
//    function typeofUser(CheckUser $user)
//    {
//        // another implement time use : when user want to any extra feturatres and call here only
//        // This function is used for Plugin System Architutre : dynamic binding (function , classes)
//    }
//
//    function zoneDisplay($req){
//        $zone   = $req['zone']??'';
//        $sql    = "SELECT zm.zoneid,zm.zonename FROM access_region ar
//                    LEFT JOIN admin_users au ON
//                           ar.userid = au.username
//                    Left JOIN state_master sm on
//                        ar.stateid=sm.stateid
//                    LEFT JOIN zone_master zm
//                        on sm.zoneid=zm.zoneid
//               WHERE au.designation_id='45' AND au.status='1' AND ar.status='Y' AND au.sapid='$this->session_userid' and zm.status='A'
//                   GROUP BY au.username";
//        $result = mysqli_query($this->connection, $sql);
//        echo '<div class="filter-group">';
//        echo '<label>Zone</label>';
//        echo '<select name="zone" onchange="this.form.submit();">';
//        while ($row = mysqli_fetch_assoc($result)) {
//            $sel = $this->selected($row['zoneid'], $zone);
//            echo '<option value="' . $row['zoneid'] . '" ' . $sel . '>'
//                . htmlspecialchars($row['zonename'])
//                . '</option>';
//        }
//        echo '</select>';
//        echo '</div>';
//    }
//
//    function stateDisplay($req){
//        $zone          = $req['zone']??'';
//        $selectedState = $req['state']??'';
//        $condition = '';
//        if (!empty($zone)) {
//            $condition = " AND zoneid = '" . intval($zone) . "' ";
//        }
//        $query = "
//            SELECT stateid, state
//            FROM state_master
//            WHERE stateid IN ({$this->arrstate})
//            {$condition}
//            ORDER BY state
//        ";
//        $result = mysqli_query($this->connection, $query);
//
//        echo '<div class="filter-group">';
//        echo '<label>State</label>';
//        echo '<select name="state" onchange="this.form.submit();">';
//        echo '<option value="">All</option>';
//        while ($row = mysqli_fetch_assoc($result)) {
//            $sel = $this->selected($selectedState, $row['stateid']);
//            echo '<option value="' . $row['stateid'] . '" ' . $sel . '>'
//                . htmlspecialchars($row['state'])
//                . '</option>';
//        }
//        echo '</select>';
//        echo '</div>';
//    }
//
//    function bsiDisplay($req){
//        $state = $req['state']??'';
//        $bsi           = $req['bsi'];
//        $allSelected   = (empty($bsi) || $bsi == '') ? 'selected' : '';
//
//        if($state!==''){
//            $condition_bsi = "AND ar.stateid = '" . mysqli_real_escape_string($this->connection, $state) . "'";
//        }
//        $sql    = "SELECT au.sapid, au.name
//                   FROM access_region ar
//                   LEFT JOIN admin_users au ON ar.userid = au.username
//                   WHERE au.designation_id='45'
//                     AND au.status='1'
//                     AND ar.status='Y'
//                     AND au.sapid='$this->session_userid'
//                     {$condition_bsi}
//                   GROUP BY au.username";
//
//        $result = mysqli_query($this->connection, $sql);
//        echo '<div class="filter-group">';
//        echo '<label>BSI</label>';
//        echo '<select name="bsi" onchange="this.form.submit()">';
//        while ($row = mysqli_fetch_assoc($result)) {
//            $sel = 'selected';
//            echo '<option value="' . htmlspecialchars($row['sapid']) . '" ' . $sel . '>'
//                . htmlspecialchars($row['name'])
//                . ' | '
//                . htmlspecialchars($row['sapid'])
//                . '</option>';
//        }
//        echo '</select>';
//        echo '</div>';
//    }
//
//    function bsiEnginnerDisplay($req){
//        $bsi    = $req['bsi']??'';
//        $state  = $req['state']??'';
//
//        $enginner          = $req['enginner'];
//        $condition_enginner = "";
//
//        if (!empty($bsi)) {
//            $condition_enginner .= " AND mapped_bsi = '"
//                . mysqli_real_escape_string($this->connection, $bsi) . "' ";
//        }
//        if (!empty($state)) {
//            $condition_enginner .= " AND stateid = '"
//                . mysqli_real_escape_string($this->connection, $state) . "' ";
//        }
//
//        $sql    = "SELECT userloginid, locusername
//                   FROM locationuser_master
//                   WHERE statusid='1'
//                     AND eng_type <> '' and mapped_bsi='$this->session_userid'
//                     {$condition_enginner}
//                   ORDER BY locusername";
//        $result = mysqli_query($this->connection, $sql);
//        echo '<div class="filter-group">';
//        echo '<label>Engineer</label>';
//        echo '<select name="enginner" onchange="this.form.submit()">';
//        echo '<option value="">All</option>';
//        while ($row = mysqli_fetch_assoc($result)) {
//            $sel = $this->selected($enginner, $row['userloginid']);
//            echo '<option value="' . htmlspecialchars($row['userloginid']) . '" ' . $sel . '>'
//                . htmlspecialchars($row['locusername'])
//                . ' | '
//                . htmlspecialchars($row['userloginid'])
//                . '</option>';
//        }
//        echo '</select>';
//        echo '</div>';
//    }
//}