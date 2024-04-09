<?php
include_once("../backend/nodes.php");
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
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <style>
    .content-wrapper {
      background: url("<?= $SERVER_NAME . $systemInfo->cover ?>");
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center center;
    }

    .title {
      font-size: 5em !important;
      color: white !important;
      text-shadow: 4px 5px 3px #414447 !important;
    }

    #left {
      height: 100vh;
      width: 99vw;
    }

    @media only screen and (max-width: 980px) {
      #right {
        display: none !important;
      }

      #left {
        width: 100% !important;
        padding: 20px;
      }
    }
  </style>
</head>

<body class="hold-transition layout-top-nav">
  <div class="wrapper">

    <!-- Content Wrapper.-->
    <div class="content-wrapper">

      <!-- Main content -->
      <div class="content" style="padding: 0; overflow: hidden;">
        <div class="row">
          <div class="col-md-5 col-sm-12 d-flex justify-content-center align-items-center bg-navy" id="left">
            <div class="card card-outline card-primary rounded-0 shadow col-lg-10 col-sm-12">
              <div class="card-header">
                <h5 class="card-title text-center text-dark"><b>Login</b></h5>
              </div>
              <div class="card-body text-dark">
                <form id="login-form" method="POST">
                  <div class="form-group">
                    <label class="col-form-label">
                      Email or Roll
                    </label>
                    <input type="text" name="email" class="form-control form-control-sm form-control-border" required>

                  </div>

                  <div class="form-group">
                    <label class="col-form-label">
                      Password
                    </label>
                    <input type="password" name="password" class="form-control form-control-sm form-control-border" placeholder="Your password ..." required>
                  </div>

                  <div class="form-group d-flex justify-content-end">
                    <button type="submit" class="btn bg-navy">Login</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="col-md-7 d-flex justify-content-center align-items-center" id="right">
            <div class="w-100">
              <center>
                <img src="<?= $SERVER_NAME . $systemInfo->logo ?>" style="width: 150px; object-fit:scale-down; object-position:center center; border-radius:100%;">
              </center>
              <h1 class="text-center py-5 title">
                <b>
                  <?= $systemInfo->system_name ?>
                </b>
              </h1>
            </div>
          </div>
        </div>
      </div>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
  </div>
  <!-- ./wrapper -->

</body>

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../assets/dist/js/adminlte.min.js"></script>
<!-- Alert -->
<script src="../assets/plugins/sweetalert2/sweetalert2.all.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../assets/dist/js/demo.js"></script>

<script>
  $("#login-form").on("submit", function(e) {
    swal.showLoading();
    $.post(
      "../backend/nodes?action=login",
      $(this).serialize(),
      (data, status) => {
        const resp = JSON.parse(data)
        if (resp.success) {
          let location = `${window.location.origin}/west/pages/`

          if (resp.role === "student") {
            location = `${window.location.origin}/west/pages/student/index`
          } else if (resp.role === "instructor" || resp.role === "coordinator" || resp.role === "panel" || resp.role === "adviser") {
            location = `${window.location.origin}/west/pages/admin/index`
          }

          if (resp.isNew) {
            swal.fire({
              title: "Welcome!",
              text: "Your account is new would you like to update your password first?",
              icon: "question",
              showDenyButton: true,
              confirmButtonText: 'Yes',
              denyButtonText: 'No',
            }).then((res) => {
              if (res.isConfirmed) {
                window.location.href = "update-password"
              } else if (res.isDenied) {
                window.location.href = location
              }
            })
          } else {
            window.location.href = location
          }
        } else {
          swal.fire({
            title: 'Error!',
            text: resp.message,
            icon: 'error',
          })
        }
      }).fail(function(e) {
      swal.fire({
        title: 'Error!',
        text: e.statusText,
        icon: 'error',
      })
    });

    e.preventDefault();
  })
</script>

</html>