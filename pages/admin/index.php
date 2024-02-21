<?php
include("../../backend/nodes.php");
if (!isset($_SESSION["username"])) {
  header("location: $SERVER_NAME/");
}
$user = get_user_by_username($_SESSION['username']);
$systemInfo = systemInfo();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $systemInfo->system_name ?></title>
  <link rel="icon" href="<?= $SERVER_NAME . $systemInfo->logo ?>" />

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <!-- Site wrapper -->
  <div class="wrapper">
    <!-- Navbar -->
    <?php
    include("../components/admin-nav.php");
    include("../components/admin-side-bar.php");
    ?>

    <!-- /.navbar -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Dashboard</h1>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-gradient-secondary elevation-1"><i class="fas fa-th-list"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Total Categories</span>
                  <span class="info-box-number text-right">
                    <?= getTotalCategories() ?>
                  </span>
                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-gradient-navy elevation-1"><i class="fas fa-calendar-day"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Today's Scheduled Tasks</span>
                  <span class="info-box-number text-right">
                    <?= getTodayScheduledTask() ?>
                  </span>
                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-calendar"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Upcoming Scheduled Tasks</span>
                  <span class="info-box-number text-right">
                    <?= getUpcomingScheduledTask() ?>
                  </span>
                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
            <!-- /.col -->
          </div>

          <div class="row">
            <div class="col-sm-12">
              <input type="text" id="barData" value='<?= json_encode(getBarData()) ?>' hidden readonly>
              <div id="chart"></div>
            </div>
          </div>
        </div>
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

  </div>
  <!-- ./wrapper -->

  <!-- jQuery -->
  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../../assets/dist/js/adminlte.min.js"></script>
  <!-- Alert -->
  <script src="../../assets/plugins/sweetalert2/sweetalert2.all.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../../assets/dist/js/demo.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</body>

<script>
  var barChart = document.getElementById("barChart");

  const barData = JSON.parse($("#barData").val())

  const result = Object.values(barData.reduce((r, e) => {
    let k = `${e.name}|${e.year}`;
    if (!r[k]) r[k] = {
      ...e,
      count: 1
    }
    else r[k].count += 1;
    return r;
  }, {}))

  let data = [];
  let years = [];

  result.forEach((res) => {
    if (!years.includes(res.year)) {
      years.push(res.year)
    }

    const toLoop = Number(years.indexOf(res.year) + 1);
    const yearIndex = years.indexOf(res.year);

    if (data.some((d) => d.name === res.name)) {
      const index = data.map((d) => d.name).indexOf(res.name)
      data[index].data.push(res.count)
    } else {

      let a = [];
      for (let i = 0; i < toLoop; i++) {
        if (i == yearIndex) {
          a.push(res.count)
        } else {
          a.push(0)
        }
      }
      data.push({
        name: res.name,
        data: a
      })
    }
  })


  for (let i = 0; i < data.length; i++) {
    const seriesData = data[i].data
    for (let j = seriesData.length; j < years.length; j++) {
      data[i].data.push(0)
    }
  }

  console.log(years)
  console.log(result)
  console.log(data)

  var options = {
    series: data,
    chart: {
      type: 'bar',
      height: 480,
      stacked: true,
      toolbar: {
        tools: {
          download: false,
        },
      }
    },
    plotOptions: {
      bar: {
        horizontal: false,
        dataLabels: {
          total: {
            enabled: true,
            offsetX: 0,
            style: {
              fontSize: '13px',
              fontWeight: 900
            }
          }
        }
      },
    },
    stroke: {
      width: 1,
      colors: ['#fff']
    },
    title: {
      text: 'Fields types per year'
    },
    xaxis: {
      categories: years,
      labels: {
        formatter: (val) => val
      }
    },
    yaxis: {
      title: {
        text: undefined
      },
      labels: {
        formatter: (val) => val
      }
    },
    tooltip: {
      y: {
        formatter: (val) => val
      }
    },
    fill: {
      opacity: 1
    },
    legend: {
      position: 'top',
      horizontalAlign: 'left',
      offsetX: 40
    }
  };

  var chart = new ApexCharts(document.querySelector("#chart"), options);
  chart.render();
</script>

</html>