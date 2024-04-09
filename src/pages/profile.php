<?php
include("../backend/nodes.php");
if (isset($_SESSION["username"])) {
  $user = get_user_by_username($_SESSION['username']);
  $middleName = $user->middle_name != null ? $user->middle_name[0] : "";
} else {
  header("location: ../");
}
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
    #searchNav::after {
      content: none
    }

    .linkNav {
      margin: auto !important;
    }

    @media screen and (max-width: 800px) {

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
    <?php
    if (!isset($_SESSION["username"])) {
      include_once("../components/navbar.php");
    } else {
      include_once("components/navbar.php");
    }
    ?>

    <!-- Content Wrapper.-->
    <div class="content-wrapper">

      <!-- Main content -->
      <div class="container">

        <div class="content" style="padding-top: 9rem">
          <?php
          if (isset($_GET["page"])) {
            if ($_GET["page"] == "my_archive") {
              include("components/student-achieve.php");
            } else if ($_GET["page"] == "manage_profile") {
              include("components/manage-profile.php");
            }
          } else {
          ?>
            <div class="card card-outline card-primary shadow rounded-0">
              <div class="card-header rounded-0">
                <h5 class="card-title">Your Information:</h5>
                <div class="card-tools">
                  <?php
                  if (!$isNotYetAssignedGroup && hasSubmittedDocuments($isLeader ? $user : get_user_by_id($user->leader_id))) :
                  ?>
                    <a href="<?= "$self?page=my_archive" ?>" class="btn btn-primary btn-gradient-primary"><i class="fa fa-archive"></i> Our Archives</a>
                  <?php endif; ?>
                  <a href="<?= "$self?page=manage_profile" ?>" class="btn btn-default bg-navy "><i class="fa fa-edit"></i> Update Account</a>
                </div>
              </div>
              <div class="card-body rounded-0">
                <div class="container-fluid">
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-lg-4 col-sm-12">
                        <img src="<?= $user->avatar == null ? $SERVER_NAME . "/public/default.png" : $SERVER_NAME . $user->avatar ?>" alt="Student Image" class="img-fluid student-img" style="width: 217px; height: 217px;">
                      </div>
                      <div class="col-lg-8 col-sm-12">
                        <dl>
                          <dt class="text-navy">Student Name:</dt>
                          <dd class="pl-4">
                            <?= ucwords("$user->last_name, $user->first_name $middleName") ?>
                          </dd>
                          <dt class="text-navy">Email:</dt>
                          <dd class="pl-4"><?= $user->email ?></dd>
                          <dt class="text-navy">Group number:</dt>
                          <dd class="pl-4"><?= $user->group_number ?></dd>
                          <dt class="text-navy">Year and Section:</dt>
                          <dd class="pl-4"><?= strtoupper($user->year_and_section) ?></dd>
                          <dt class="text-navy">Course:</dt>
                          <dd class="pl-4"><?= getCourseData($user->course_id)->name ?></dd>
                        </dl>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php
          }
          ?>
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
<div></div>
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
      window.location.replace(`${window.location.origin}/west/pages/archives?s=${$("#searchInput").val()}`)
    }
  });

  $("#inputEmail").on("blur", function(e) {
    const emailEnd = e.target.value.split("@")[1];
    if (emailEnd !== "wvsu.edu.ph") {
      $(this).addClass("is-invalid")
      $("#inputEmailError").html("Please use your school email (@wvsu.edu.ph)")
      $("#updateBtn").prop("disabled", true)
    }
  })

  $("#update-form").on("submit", function(e) {
    swal.showLoading()
    $.ajax({
      url: "../backend/nodes?action=updateUser",
      type: "POST",
      data: new FormData(this),
      contentType: false,
      cache: false,
      processData: false,
      success: function(data) {
        const resp = JSON.parse(data);
        if (resp.success) {
          swal.fire({
            title: 'Success!',
            text: resp.message,
            icon: 'success',
          })
        } else {
          swal.fire({
            title: 'Error!',
            text: resp.message,
            icon: 'error',
          })
        };
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


  function displayImg(input, _this) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $('#cimg').attr('src', e.target.result);
      }

      reader.readAsDataURL(input.files[0]);
    } else {
      $('#cimg').attr('src', "../public/default.png");
    }
  }
</script>

</html>