<?php
include("../../backend/nodes.php");
if (!isset($_SESSION["username"])) {
  header("location: $SERVER_NAME/");
}
include_once("../../backend/nodes.php");
$systemInfo = systemInfo();
$user = get_user_by_username($_SESSION['username']);
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
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css">
  <style>
    #searchNav::after {
      content: none
    }

    #header {
      height: 70vh;
      width: calc(100%);
      position: relative;
      top: -2rem;
    }

    #header:before {
      content: "";
      position: absolute;
      height: calc(100%);
      width: calc(100%);
      background: url("<?= $SERVER_NAME . $systemInfo->cover ?>");
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center center;
      filter: brightness(50%);
    }

    #header>div {
      position: absolute;
      height: calc(100%);
      width: calc(100%);
      z-index: 2;
    }

    .site-title {
      font-size: 5em !important;
      color: white !important;
      text-shadow: 4px 5px 3px #414447 !important;
    }

    .linkNav {
      margin: auto !important;
    }

    @media screen and (max-width: 800px) {
      .site-title {
        font-size: 3em !important;
      }

      .divSearch {
        width: 100% !important;
      }

      .linkNav {
        margin: 0 !important;
      }
    }
  </style>
</head>

<body class="hold-transition layout-top-nav">
  <div class="wrapper">
    <?php include_once("../components/navbar.php"); ?>

    <!-- Content Wrapper.-->
    <div class="content-wrapper">

      <!-- Main content -->
      <div class="content" style="padding:9rem 0rem 0rem 0rem;">
        <div id="header" class="shadow mb-4">
          <div class="d-flex justify-content-center h-100 w-100 align-items-center flex-column">
            <p class="w-100 text-center site-title"><?= $systemInfo->system_name ?></p>
          </div>
        </div>
      </div>
      <!-- /.content -->

      <section class="content ">
        <div class="container">
          <div class="col-lg-12 py-5">
            <div class="card card-outline card-navy shadow rounded-0">
              <div class="card-body rounded-0">
                <div class="container-fluid">
                  <h3 class="text-center">Welcome</h3>
                  <hr>
                  <div class="welcome-content">
                    <?= nl2br($systemInfo->home_content) ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
    <!-- /.content-wrapper -->
  </div>
  <!-- ./wrapper -->

</body>

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="../../assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../assets/dist/js/adminlte.min.js"></script>
<!-- Alert -->
<script src="../../assets/plugins/sweetalert2/sweetalert2.all.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../assets/dist/js/demo.js"></script>

<script>
  if (sessionStorage.getItem("searchInput")) {
    $("#searchInput").val(sessionStorage.getItem("searchInput"))
  }

  $("#searchInput").on("input", function(e) {
    sessionStorage.setItem("searchInput", e.target.value)
  })

  $(document).on('keypress', function(keyEvent) {
    if (keyEvent.which == 13 && $("#searchInput").val() !== "") {
      sessionStorage.setItem("searchInput", $("#searchInput").val())
      window.location.href = `${window.location.origin}/west/pages/archives?s=${$("#searchInput").val()}`
    }
  });
</script>

</html>