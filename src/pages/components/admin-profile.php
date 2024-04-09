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
  <!-- DataTables -->
  <link rel="stylesheet" href="../../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
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
      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card card-outline card-primary shadow rounded-0 mt-2 container">
                <div class="card-header rounded-0">
                  <h5 class="card-title">My Profile</h5>
                </div>
                <div class="card-body rounded-0">
                  <div class="container-fluid">
                    <form method="POST" id="my-profile-form" enctype="multipart/form-data">
                      <input type="text" name="userId" value="<?= $user->id ?>" hidden readonly>
                      <input type="text" name="role" value="<?= $user->role ?>" hidden readonly>
                      <div class="row">
                        <div class="col-lg-4">
                          <div class="form-group">
                            <label class="control-label text-navy">First name</label>
                            <input type="text" name="fname" value="<?= $user->first_name ?>" class="form-control form-control-border" required>
                          </div>
                        </div>
                        <div class="col-lg-4">
                          <div class="form-group">
                            <label class="control-label text-navy">MiddleName</label>
                            <input type="text" name="mname" value="<?= $user->middle_name ?>" class="form-control form-control-border">
                          </div>
                        </div>
                        <div class="col-lg-4">
                          <div class="form-group">
                            <label class="control-label text-navy">LastName</label>
                            <input type="text" name="lname" value="<?= $user->last_name ?>" class="form-control form-control-border" required>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-lg-6">
                          <div class="form-group">
                            <label for="email" class="control-label text-navy">Email</label>
                            <input type="email" name="email" class="form-control form-control-border" required value="<?= $user->email ?>">
                          </div>
                          <div class="form-group">
                            <label for="password" class="control-label text-navy">New Password</label>
                            <input type="password" name="password" id="password" placeholder="Password" class="form-control form-control-border">
                          </div>

                          <div class="form-group">
                            <label for="cpassword" class="control-label text-navy">Confirm New Password</label>
                            <input type="password" name="cpassword" placeholder="Confirm Password" class="form-control form-control-border">
                          </div>

                          <small class="text-muted">Leave the New Password and Confirm New Password Blank if you don't wish to change your password.</small>

                          <div class="form-group mt-4">
                            <label for="oldpassword">Please Enter your Current Password</label>
                            <input type="password" name="oldpassword" id="oldpassword" placeholder="Current Password" class="form-control form-control-border">
                          </div>
                        </div>

                        <div class="col-lg-6">
                          <div class="form-group">
                            <label for="img" class="control-label text-muted">Choose Image</label>
                            <input type="file" name="avatar" class="form-control border-0" accept="image/png,image/jpeg" onchange="displayImg(this,$(this))">
                          </div>
                          <div class="form-group text-center">
                            <img src="<?= $user->avatar ? $SERVER_NAME . $user->avatar : "$SERVER_NAME/assets/dist/img/no-image-available.png" ?>" alt="My Avatar" id="cimg" class="img-fluid student-img bg-gradient-dark border" style="width: 217px; height: 217px;">
                          </div>
                        </div>
                      </div>
                      <hr>
                      <div class="row">
                        <div class="col-lg-12">
                          <div class="form-group text-center">
                            <button type="submit" class="btn btn-default bg-navy m-1"> Update</button>
                            <button type="button" onclick="return window.history.back()" class="btn btn-danger btn-gradient-danger m-1"> Cancel</button>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
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
  <!-- AdminLTE for demo purposes -->
  <script src="../../assets/dist/js/demo.js"></script>
  <!-- Alert -->
  <script src="../../assets/plugins/sweetalert2/sweetalert2.all.min.js"></script>

  <script src="../../assets/plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="../../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="../../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="../../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

  <script>
    $(function() {
      $("#student_list").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
      });
    });

    $("#my-profile-form").on("submit", function(e) {
      $.ajax({
        url: "../../backend/nodes?action=updateUser",
        type: "POST",
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
          const resp = JSON.parse(data);

          swal.fire({
            title: resp.success ? 'Success!' : 'Error!',
            text: resp.message,
            icon: resp.success ? 'success' : 'error',
          }).then(() => window.location.reload())
        },
        error: function(data) {
          swal.fire({
            title: 'Oops...',
            text: 'Something went wrong.',
            icon: 'error',
          })
        }
      });

      e.preventDefault();
    })
  </script>
</body>

</html>