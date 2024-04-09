<?php
include("../backend/nodes.php");
if (!isset($_SESSION["username"])) {
  header("location: $SERVER_NAME/pages/login");
}
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

    @media only screen and (max-width: 980px) {
      #right {
        display: none;
      }
    }
  </style>
</head>

<body class="hold-transition layout-top-nav">
  <div class="wrapper">

    <!-- Content Wrapper.-->
    <div class="content-wrapper">

      <!-- Main content -->
      <div class="content" style="padding: 0;">
        <div class="row" style="height: 100vh; width: 100vw;">
          <div class="col-md-5 col-sm-12 d-flex justify-content-center align-items-center bg-navy" style="height: 100%;">
            <div class="card card-outline card-primary rounded-0 shadow col-lg-10 col-sm-12">
              <div class="card-header">
                <h5 class="card-title text-center text-dark"><b>Update Password</b></h5>
              </div>
              <div class="card-body text-dark">
                <form id="update-password" method="POST">
                  <input type="text" name="id" value="<?= $user->id ?>" hidden readonly>
                  <div class="form-group">
                    <label class="col-form-label">
                      New Password
                    </label>
                    <input type="password" id="inputPassword" class="form-control form-control-sm form-control-border" placeholder="Your new password ..." required>

                  </div>

                  <div class="form-group">
                    <label class="col-form-label">
                      Confirm Password
                    </label>
                    <input type="password" name="password" id="cPassword" class="form-control form-control-sm form-control-border" placeholder="Confirm password ..." required>
                  </div>

                  <div class="form-group d-flex justify-content-end">
                    <button type="submit" class="btn bg-navy">Submit</button>
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
  $("#update-password").on("submit", function(e) {
    swal.showLoading();
    const inputPassword = $("#inputPassword").val()
    const cPassword = $("#cPassword").val()
    if (inputPassword !== cPassword) {
      swal.fire({
        title: 'Error!',
        text: "Password not match",
        icon: 'error',
      })
    } else {
      $.post(
        "../backend/nodes?action=updatePassword",
        $(this).serialize(),
        (data, status) => {
          const resp = JSON.parse(data)
          if (resp.success) {
            window.location.href = `${window.location.origin}/west/pages/admin/index`
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
    }
    e.preventDefault();
  })
</script>

</html>