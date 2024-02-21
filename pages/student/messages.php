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
    <?php include_once("../components/navbar.php"); ?>

    <!-- Content Wrapper.-->
    <div class="content-wrapper">

      <!-- Main content -->
      <div class="container" style="padding-top: 9rem">
        <div class="row justify-content-center">
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Messages</h3>
              </div>
              <?php
              $query = mysqli_query(
                $conn,
                "SELECT * FROM thesis_groups WHERE group_leader_id='$user->id'"
              );
              $adviserName = "";
              $instructorName = "";

              $adviser = null;
              $instructor = null;

              if (mysqli_num_rows($query) > 0) {
                $thesisGroupData = mysqli_fetch_object($query);
                $adviser = $thesisGroupData->adviser_id == null ? null : get_user_by_id($thesisGroupData->adviser_id);
                $instructor = $thesisGroupData->instructor_id == null ? null : get_user_by_id($thesisGroupData->instructor_id);

                if ($adviser != null) {
                  $adviserName = ucwords("$adviser->first_name " . ($adviser->middle_name != null ? $adviser->middle_name[0] . "." : "") . " $adviser->last_name");
                  $adviserId = $adviser->id;
                }

                if ($instructor != null) {
                  $instructorName = ucwords("$instructor->first_name " . ($instructor->middle_name != null ? $instructor->middle_name[0] . "." : "") . " $instructor->last_name");
                  $instructorId = $instructor->id;
                }
              }
              ?>
              <div class="card-body p-0" style="display: block;">
                <ul class="nav nav-pills flex-column">
                  <?php if ($instructorName != "" && $instructor != null) : ?>
                    <li class="nav-item active">
                      <a href="./message?i=<?= $instructor->id  ?>" class="nav-link">
                        Instructor:
                        <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                          <div class="mr-3">
                            <img src="<?= $instructor->avatar != null ? $SERVER_NAME . $instructor->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                          </div>
                          <div>
                            <h6>
                              <strong>
                                <?= $instructorName ?>
                              </strong>
                            </h6>
                          </div>
                        </div>
                      </a>
                    </li>
                  <?php endif;
                  if ($adviserName != "" && $adviser != null) : ?>
                    <li class="nav-item">
                      <a href="./message?i=<?= $adviser->id  ?>" class="nav-link">
                        Adviser:
                        <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                          <div class="mr-3">
                            <img src="<?= $adviser->avatar != null ? $SERVER_NAME . $adviser->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                          </div>
                          <div>
                            <h6>
                              <strong>
                                <?= $adviserName ?>
                              </strong>
                            </h6>
                          </div>
                        </div>
                      </a>
                    </li>
                  <?php endif; ?>
                </ul>
              </div>
              <!-- /.card-body -->
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