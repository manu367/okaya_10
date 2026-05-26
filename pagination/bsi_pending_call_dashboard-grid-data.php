<?php
require_once("../includes/config.php");
global $link1;
//header("Content-type: application/json");

class DashoardLaoder_1{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function bsiLoader($condition = [])
    {
        $data = [];

        $bsiCondition = "";
        $totalJobsDebug = 0;
        if (!empty($condition['bsi'])) {
            $bsi = mysqli_real_escape_string($this->connection, $condition['bsi']);
            $bsiCondition .= " AND au.sapid = '{$bsi}' ";
        }
        if (!empty($condition['zone'])) {
            $zone = (int)$condition['zone'];
            $bsiCondition .= " AND sm.zoneid= '{$zone}' ";
        }

        if (!empty($condition['state'])) {
            $state = (int)$condition['state'];
            $bsiCondition .= " AND sm.stateid= '{$state}' ";
        }


        $allBSI = $this->getAllBSIFromtheEnginner($bsiCondition);

        if (empty($allBSI)) {
            return [];
        }

        foreach ($allBSI as $bsi) {

            $bsiId         = $bsi['sapid'];
            $bsiName       = $bsi['name'];
            $bsiusername_1 = $bsi['username'];
            $zone          = $this->getzone($bsiusername_1, $condition);

            $enginners = $this->fetchBSIEnginners($bsiId, $condition);


            if(count($enginners)===0){
                continue;
            }

            $engineerRows = [];
            $bsiTotals    = $this->defaultStatusBucket();


            foreach ($enginners as $eng) {
                $engId   = $eng['userloginid'];
                $engName = $eng['locusername'];

                $jobCondition = [
                    'date_range'  => $condition['date_range']  ?? '',
                    'segment'     => $condition['segment']     ?? '',
                    'sub_segment' => $condition['sub_segment'] ?? '',
                    'enginner'    => $condition['enginner']    ?? '',
                    'zone'         => $condition['zone']        ??'',
                    'state'       => $condition['state'] ?? ''
                ];

                $jobs = $this->fetchEngineerJobs($engId, $jobCondition);

                if (empty($jobs)) {
                    continue;
                }

                $engineerData = $this->defaultStatusBucket();

                foreach ($jobs as $job) {
                    $totalJobsDebug++;
                    $status = $job['status'];
                    $aging_hours = (int)$job['aging_hours'];
                    $aging_days  = (int)$job['aging_days'];
                    $bucket      = $this->calculateBucket($aging_hours, $aging_days);

                    if ($bucket === false) {
                        echo "BUCKET FAILED : ".$job['job_id']."<br>";
                        continue;
                    }

                    if ($status == '2') {
                        $engineerData['assigned'][$bucket]++;
                        $engineerData['assigned_total']++;
                    } elseif ($status == '3') {
                        $engineerData['pna'][$bucket]++;
                        $engineerData['pna_total']++;
                    } elseif ($status == '7') {
                        $engineerData['wip'][$bucket]++;
                        $engineerData['wip_total']++;
                    } elseif ($status == '81') {
                        $engineerData['replacement'][$bucket]++;
                        $engineerData['replacement_total']++;
                    }

                    $engineerData['grand_total']++;
                }

                $this->mergeTotals($bsiTotals, $engineerData);

                $engineerRows[] = [
                    'engineer_id'   => $engId,
                    'engineer_name' => $engName,
                    'data'          => $engineerData
                ];
            }

            $data[] = [
                'bsi_id'    => $bsiId,
                'bsi_name'  => $bsiName,
                'bsi_zone'  => $zone ?? '',
                'engineers' => $engineerRows,
                'totals'    => $bsiTotals
            ];
        }
        return $data;
    }


    private function getzone($bsi, $condition = [])
    {
        $where = " WHERE ar.userid = '" . mysqli_real_escape_string($this->connection, $bsi) . "' AND ar.status='Y'";

        if (!empty($condition['zone'])) {
            $zone  = (int)$condition['zone'];
            $where .= " AND zm.zoneid = '{$zone}' ";
        }

        if (!empty($condition['state'])) {
            $state = (int)$condition['state'];
            $where .= " AND sm.stateid = '{$state}' ";
        }

        $sql = "
            SELECT zm.zonename
            FROM access_region ar
            LEFT JOIN state_master sm ON ar.stateid = sm.stateid
            LEFT JOIN zone_master zm  ON sm.zoneid  = zm.zoneid
            {$where}
            LIMIT 1
        ";
        $result = mysqli_query($this->connection, $sql);
        if (!$result) return '';

        $row = mysqli_fetch_assoc($result);
        return $row['zonename'] ?? '';
    }


    public function fetchBSIEnginners($bsiId, $condition = []){

        if(empty($bsiId)){
            return [];
        }
        $bsiId = mysqli_real_escape_string($this->connection, $bsiId);
        $where = " WHERE  lum.mapped_bsi = '{$bsiId}' and lum.mapped_bsi is not null and lum.mapped_bsi <>'' and lum.statusid='1' ";

        if (!empty($condition['enginner'])) {
            $engid = mysqli_real_escape_string($this->connection, $condition['enginner']);
            $where .= " AND lum.userloginid = '{$engid}' ";
        }

        $sql    = "SELECT DISTINCT lum.locusername, lum.userloginid FROM locationuser_master lum {$where}";
        $result = mysqli_query($this->connection, $sql);

        if (!$result) return [];

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }


    public function fetchEngineerJobs($engId, $condition = []){
        $engId = mysqli_real_escape_string($this->connection, $engId);
        $where = "and jd.eng_id is NOT null and jd.eng_id <> '' AND jd.eng_id in ( SELECT lum.userloginid FROM locationuser_master lum WHERE lum.mapped_bsi is not null and lum.mapped_bsi <>'' and lum.statusid='1')";


        if (!empty($condition['date_range'])) {
            $date  = $condition['date_range'];
            $where .= " AND DATE(jd.open_date) <= '$date'";
        }

        if (!empty($condition['enginner'])) {
            $engid  = mysqli_real_escape_string($this->connection, $condition['enginner']);
            $where .= " AND jd.eng_id = '{$engid}' ";
        }
        if(isset($condition['zone']) && !empty($condition['zone'])) {
            $zoneid  = mysqli_real_escape_string($this->connection, $condition['zone']);
            $where .= " AND jd.state_id IN ( SELECT zm.stateid  FROM state_master zm  WHERE zm.zoneid IN ('{$zoneid}'))";
        }
        if (isset($condition['state']) && !empty($condition['state'])) {
            $stateid = mysqli_real_escape_string($this->connection, $condition['state']);
            $where .= " AND jd.state_id = '{$stateid}' ";
        }

        if (!empty($condition['segment'])) {
            $segment = (string)mysqli_real_escape_string($this->connection, $condition['segment']);
            if ($segment === '1') {
                $where .= " AND jd.product_id IN ('1') ";
            } elseif ($segment === '2') {
                $where .= " AND jd.product_id NOT IN ('1','6','10','11','12','14') ";
            } elseif ($segment === '3') {
                $where .= " AND jd.product_id IN ('6','10','11','12') ";
            }
        }

        if (!empty($condition['sub_segment'])) {
            $subSegment = (int)$condition['sub_segment'];
            $where     .= " AND jd.product_id = '{$subSegment}' ";
        }


        $sql = "
        SELECT
            jd.*,
            DATEDIFF(NOW(), jd.open_date) AS aging_days,
            TIMESTAMPDIFF(HOUR, CONCAT(jd.open_date, ' ', jd.open_time), NOW()) AS aging_hours
        FROM jobsheet_data jd
        WHERE jd.eng_id = '{$engId}'
          AND jd.status IN ('1','2','3','7','81')
          {$where}
    ";

        $result = mysqli_query($this->connection, $sql);
        if (!$result) return [];

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    public function defaultStatusBucket()
    {
        $emptyBuckets = [
            'b1' => 0,
            'b2' => 0,
            'b3' => 0,
            'b4' => 0,
            'b5' => 0,
            'b6' => 0,
        ];

        return [
            'assigned'    => $emptyBuckets,
            'replacement' => $emptyBuckets,
            'wip'         => $emptyBuckets,
            'pna'         => $emptyBuckets,

            'assigned_total'    => 0,
            'replacement_total' => 0,
            'wip_total'         => 0,
            'pna_total'         => 0,
            'grand_total'       => 0
        ];
    }

    public function calculateBucket($aging_hours, $aging_days){
        if ($aging_hours < 0)                        return false;
        if ($aging_hours <= 24)                      return 'b1'; // ≤ 24 hrs = 0-1
        if ($aging_hours > 24 && $aging_hours <= 48) return 'b2'; // > 24 hrs & ≤ 48 hrs = 2dasy ke liye

        if ($aging_days >= 3  && $aging_days <= 5)   return 'b3';
        if ($aging_days >= 6  && $aging_days <= 10)  return 'b4';
        if ($aging_days >= 11 && $aging_days <= 15)  return 'b5';
        if ($aging_days >= 16)                       return 'b6';

        return false;
    }

    public function mergeTotals(&$total, $eng)
    {
        foreach (['assigned', 'replacement', 'wip', 'pna'] as $type) {
            foreach ($eng[$type] as $bucket => $count) {
                $total[$type][$bucket] += $count;
            }
        }

        $total['assigned_total']    += $eng['assigned_total'];
        $total['replacement_total'] += $eng['replacement_total'];
        $total['wip_total']         += $eng['wip_total'];
        $total['pna_total']         += $eng['pna_total'];
        $total['grand_total']       += $eng['grand_total'];
    }

    public function getAllBSIFromtheEnginner($condition = "")
    {
        $sql = "
            SELECT au.sapid, au.username, au.name, au.status 
            FROM access_region ar 
            LEFT JOIN admin_users au ON ar.userid = au.username 
            LEFT JOIN state_master sm ON ar.stateid = sm.stateid 
            WHERE au.designation_id='45' 
              AND au.status='1' 
              AND ar.status='Y' 
              {$condition}
            GROUP BY au.username
        ";
        $result = mysqli_query($this->connection, $sql);
        if (!$result) return [];
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
}

$dashoboard = new DashoardLaoder_1($link1);
$show = ["status" => false, "data" => null];

if (isset($_REQUEST['submit_data'])) {
    $condition = [];
    $condition['date_range']  = $_REQUEST['date_range']  ?? '';
    $condition['zone']        = $_REQUEST['zone']        ?? '';
    $condition['state']       = $_REQUEST['state']       ?? '';
    $condition['bsi']         = $_REQUEST['bsi']         ?? '';
    $condition['enginner']    = $_REQUEST['enginner']    ?? '';
    $condition['segment']     = $_REQUEST['segment']     ?? '';
    $condition['sub_segment'] = $_REQUEST['sub_segment'] ?? '';
    $data = $dashoboard->bsiLoader($condition);
    $show['status'] = true;
    $show['data']   = $data;
    echo json_encode($show);
    exit();
}