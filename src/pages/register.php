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
      height: auto;
      width: 99vw;
    }

    @media only screen and (max-width: 800px) {
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
      <div class="content" style="padding: 0;  overflow: hidden;">
        <div class="row">
          <div class="col-md-5 col-sm-12 d-flex justify-content-center align-items-center bg-navy" id="left">
            <div class="card card-outline card-primary rounded-0 shadow col-lg-10 col-sm-12 mt-2">
              <div class="card-header">
                <h5 class="card-title text-center text-dark"><b>Registration</b></h5>
              </div>
              <div class="card-body text-dark">
                <form id="registration-form" method="POST">
                  <div class="form-group">
                    <label class="col-form-label">
                      Student ID
                    </label>
                    <input type="text" name="roll" class="form-control form-control-border" placeholder="eg. 2019M0144" required>

                  </div>
                  <div class="form-group">
                    <label class="col-form-label">
                      Name
                    </label>
                    <input type="text" class="form-control form-control-border" name="fname" placeholder="First name" required>
                    <br>
                    <input type="text" class="form-control form-control-border" name="mname" placeholder="Middle name (optional)">
                    <br>
                    <input type="text" class="form-control form-control-border" name="lname" placeholder="Last name" required>
                  </div>

                  <div class="form-group mb-0">
                    <label class="col-form-label">
                      School year
                    </label>
                  </div>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <span class="input-group-text" style="background-color: transparent; border: 0; border-bottom: 1px solid #ced4da;">S.Y.</span>
                    </div>
                    <input type="text" class="form-control form-control-border" name="sy" placeholder="eg. <?= (date("Y") - 1) . "-" . date("Y") ?>" required>
                  </div>

                  <div class="form-group">
                    <label class="col-form-label">
                      Year & Section
                    </label>
                    <div class="input-group">
                      <input type="number" name="year" class="form-control form-control-border mr-3" placeholder="3 or 4" required>

                      <input type="text" name="section" class="form-control form-control-border ml-3" placeholder="A or B" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-form-label">
                      Course
                    </label>
                    <select name="courseId" class="form-control form-control-border mr-3" required>
                      <option value="">-- select course --</option>
                      <?php
                      $query = mysqli_query(
                        $conn,
                        "SELECT * FROM courses"
                      );
                      while ($course = mysqli_fetch_object($query)) :
                      ?>
                        <option value="<?= $course->course_id ?>"><?= "($course->short_name)" . $course->name ?></option>
                      <?php endwhile; ?>
                    </select>
                  </div>

                  <div class="form-group">
                    <label class="col-form-label">
                      Email
                    </label>
                    <input type="email" name="email" id="inputEmail" class="form-control form-control-border" placeholder="Your email ..." required>
                    <div class="invalid-feedback" style="padding-left: 5px;">
                      <p id="inputEmailError" style="margin-bottom: 0;"></p>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-form-label">
                      Password
                    </label>
                    <input type="password" name="password" class="form-control form-control-border" placeholder="Your password ..." required>
                  </div>

                  <div class="form-group d-flex justify-content-end">
                    <button type="submit" class="btn bg-navy" id="btnReg">Register</button>
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
  $("#inputEmail").on("blur", function(e) {
    const emailEnd = e.target.value.split("@")[1];
    if (emailEnd !== "wvsu.edu.ph") {
      $(this).addClass("is-invalid")
      $("#inputEmailError").html("Please use your school email (@wvsu.edu.ph)")
      $("#btnReg").prop("disabled", true)
    }
  })

  $("#registration-form").on("submit", function(e) {
    swal.showLoading();
    $.post(
      "../backend/nodes?action=student_registration",
      $(this).serialize(),
      (data, status) => {
        const resp = JSON.parse(data)
        if (resp.success) {
          swal.fire({
            title: 'Success!',
            text: resp.message,
            icon: 'success',
          }).finally(() =>
            window.location.href = `${window.location.origin}/west/pages/student/index`
          )
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