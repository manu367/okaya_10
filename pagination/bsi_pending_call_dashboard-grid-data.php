<?php
require_once("../includes/config.php");
global $link1;
header("Content-type: application/json");

class DashoardLaoder_1
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function bsiLoader($condition = [])
    {
        $data = [];

        $bsiCondition = "";

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

            // FIX: Pass only engineer filter, NOT zone/state to fetchBSIEnginners
            $enginners = $this->fetchBSIEnginners($bsiId, $condition);

            $engineerRows = [];
            $bsiTotals    = $this->defaultStatusBucket();

            foreach ($enginners as $eng) {
                $engId   = $eng['userloginid'];
                $engName = $eng['locusername'];

                // FIX: Pass only segment/sub_segment/date_range/engineer to job fetcher
                // Zone & State should NOT filter jobs — they already filtered which BSI/engineers to show
                $jobCondition = [
                    'date_range'  => $condition['date_range']  ?? '',
                    'segment'     => $condition['segment']     ?? '',
                    'sub_segment' => $condition['sub_segment'] ?? '',
                    // enginner filter for job level (if specific engineer chosen)
                    'enginner'    => $condition['enginner']    ?? '',
                ];

                $jobs = $this->fetchEngineerJobs($engId, $jobCondition);
                if (empty($jobs)) {
                    continue;
                }

                $engineerData = $this->defaultStatusBucket();

                foreach ($jobs as $job) {
                    $status = $job['status'];
                    $aging  = (int)$job['aging_days'];
                    $bucket = $this->calculateBucket($aging);

                    if ($bucket === false) {
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


    public function fetchBSIEnginners($bsiId, $condition = [])
    {
        // FIX: Escape bsiId properly
        $bsiId = mysqli_real_escape_string($this->connection, $bsiId);
        $where = " WHERE lum.mapped_bsi = '{$bsiId}' ";

        if (!empty($condition['enginner'])) {
            $engid = mysqli_real_escape_string($this->connection, $condition['enginner']);
            $where .= " AND lum.userloginid = '{$engid}' ";
        }

        $sql    = "SELECT lum.locusername, lum.userloginid FROM locationuser_master lum {$where}";
        $result = mysqli_query($this->connection, $sql);

        if (!$result) return [];

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }


    public function fetchEngineerJobs($engId, $condition = [])
    {
        $engId = mysqli_real_escape_string($this->connection, $engId);
        $where = "";

        if (!empty($condition['date_range'])) {
            $date  = (int)$condition['date_range'];
            $where .= " AND jd.open_date >= NOW() - INTERVAL {$date} DAY ";
        }

        // FIX PROBLEM 2: Zone aur State conditions yahan SE HATA DIYE
        // Kyunki BSI/engineer already zone+state ke basis par filter ho chuke hain
        // Agar yahan bhi filter lagao to ek hi engineer ke jobs galat count honge

        if (!empty($condition['enginner'])) {
            $engid  = mysqli_real_escape_string($this->connection, $condition['enginner']);
            $where .= " AND jd.eng_id = '{$engid}' ";
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
                DATEDIFF(NOW(), jd.open_date) as aging_days
            FROM jobsheet_data jd
            WHERE jd.eng_id = '{$engId}'
              AND jd.status IN ('0','1','2','3','7','81')
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

    public function calculateBucket($aging)
    {
        if ($aging < 0)                   return false;
        if ($aging <= 1)                  return 'b1';
        if ($aging == 2)                  return 'b2';
        if ($aging >= 3  && $aging <= 5)  return 'b3';
        if ($aging >= 6  && $aging <= 10) return 'b4';
        if ($aging >= 11 && $aging <= 15) return 'b5';
        if ($aging >= 16)                 return 'b6';

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