<?php
include("../includes/config.php");

function getAlHappyCAll($link1)
{
    $sql = "SELECT COUNT(*) AS total FROM jobsheet_data WHERE status = 2";
    $result = mysqli_query($link1,$sql);

    if($result){
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
    return 0;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=siteTitle?></title>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script src="../js/bootstrap.min.js"></script>
 <script type="text/javascript" src="../js/moment.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
    <script type="text/javascript" src="../js/daterangepicker.js"></script>
    <style>
        .cards{
            display:flex;
            flex-wrap:wrap;
            gap:20px;
            justify-content:center;
            padding:20px;
        }

        .card{
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            width:200px;
            height:110px;
            background:#fff;
            border-radius:12px;
            box-shadow:0 4px 10px rgba(0,0,0,0.08);
            cursor:pointer;
            transition:all .25s ease;
            border-top:4px solid #2e353d;
            padding:15px;
        }

        .card-top{
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .card a{
            text-decoration:none;
            font-size:14px;
        }

        .card:hover{
            transform:translateY(-6px);
            box-shadow:0 12px 22px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
    <?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-bar-chart"></i>Dashboard Page</h2>
      <br/>
      <div class="container-fluid">
<!--         KPI Cards-->
          <div class="cards">
              <div class="card">
                  <div class="card-top">
                      <h4>Calls</h4>
                      <span style="font-size: large">01</span>
                  </div>
                  <a href="job_list.php">View All</a>
              </div>

              <div class="card">
                  <div class="card-top">
                      <h4>Happy Calling</h4>
                      <span style="font-size: large"><?=getAlHappyCAll($link1)?></span>
                  </div>
                  <a href="handover_job_list.php">View All</a>
              </div>

              <div class="card">
                  <div class="card-top">
                      <h4>Stocks</h4>
                      <span style="font-size: large">01</span>
                  </div>
                  <a href="inventory_stock_status.php">View All</a>
              </div>

              <div class="card">
                  <div class="card-top">
                      <h4>Account</h4>
                      <span style="font-size: large">Rs:10000</span>
                  </div>
                  <a href="myaccount_add_payment.php">View All</a>
              </div>
          </div>
      </div>
    </div>
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
