<?php
include("../backend/nodes.php");
if (isset($_SESSION["username"])) {
  $user = get_user_by_username($_SESSION['username']);
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

        <div class="content" style="padding:9rem 0rem 0rem 0rem;">
          <?php
          $document = getDocumentById($_GET['id']);
          $leader = $document->leader_id ? get_user_by_id($document->leader_id) : null;
          ?>
          <div class="card card-outline card-primary shadow rounded-0">
            <div class="card-header">
              <h3>
                <strong>
                  <?= ucwords($document->title) ?>
                </strong>
              </h3>
              <small class="text-muted">Submitted by <b class="text-info"><?= $leader ? ucwords("$leader->first_name " . ($leader->middle_name != null ? $leader->middle_name[0] . "." : "") . " $leader->last_name") : "N/A" ?></b> on <?= date("F d, Y h:i:s A", strtotime($document->date_updated)) ?></small>
            </div>
            <div class="card-body rounded-0">
              <div class="container-fluid">
                <center>
                  <img src="<?= $SERVER_NAME . $document->img_banner ?>" alt="Banner Image" id="banner-img" class="img-fluid border bg-gradient-dark">
                </center>
                <fieldset>
                  <legend class="text-navy"> Type:</legend>
                  <div class="pl-4">
                    <?php
                    $typeQ = mysqli_query(
                      $conn,
                      "SELECT * FROM types WHERE id=$document->type_id"
                    );
                    echo mysqli_num_rows($typeQ) > 0 ? mysqli_fetch_object($typeQ)->name : "";
                    ?>
                  </div>
                </fieldset>
                <fieldset>
                  <legend class="text-navy"> Year:</legend>
                  <div class="pl-4">
                    <?= $document->year ?>
                  </div>
                </fieldset>
                <fieldset>
                  <legend class="text-navy">Description:</legend>
                  <div class="pl-4">
                    <?= nl2br($document->description) ?>
                  </div>
                </fieldset>
                <?php
                if ($document->leader_id != null) :
                ?>
                  <fieldset>
                    <legend class="text-navy">Project Leader:</legend>
                    <div class="pl-4">
                      <div class="ml-2 mt-2 mb-2 d-flex justify-content-start align-items-center">
                        <div class="mr-1">
                          <img src="<?= $leader->avatar != null ? $SERVER_NAME . $leader->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                        </div>
                        <div>
                          <?= ucwords("$leader->first_name " .($leader->middle_name != null ? $leader->middle_name[0] . "." : "") . " $leader->last_name") ?>
                        </div>
                      </div>
                    </div>
                  </fieldset>
                  <fieldset>
                    <legend class="text-navy">Project Members:</legend>
                    <div class="pl-4">
                      <?php
                      $memberData = json_decode(getMemberData($leader->group_number, $leader->id));
                      foreach ($memberData as $member) :
                        $memberName = ucwords("$member->first_name " . ($member->middle_name != null ? $member->middle_name[0] . "." : "") . " $member->last_name");
                      ?>
                        <div class="ml-2 mt-2 mb-2 d-flex justify-content-start align-items-center">
                          <div class="mr-1">
                            <img src="<?= $member->avatar != null ? $SERVER_NAME . $member->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                          </div>
                          <div>
                            <?= $memberName ?>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </fieldset>
                <?php endif; ?>
                <fieldset>
                  <legend class="text-navy"> Document:</legend>
                  <div class="pl-4">
                    <div class="embed-responsive embed-responsive-4by3">
                      <iframe src="<?= $SERVER_NAME . $document->project_document ?>#embedded=true&toolbar=0&navpanes=0" class="embed-responsive-item" id="pdfPreview" allowfullscreen></iframe>
                    </div>
                  </div>
                </fieldset>
              </div>
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
  if (sessionStorage.getItem("searchInput")) {
    $("#searchInput").val(sessionStorage.getItem("searchInput"))
  }

  $("#searchInput").on("input", function(e) {
    sessionStorage.setItem("searchInput", e.target.value)
  })

  $(document).on('keypress', function(keyEvent) {
    if (keyEvent.which == 13 && $("#searchInput").val() !== "") {
      sessionStorage.setItem("searchInput", $("#searchInput").val())
      window.location.replace(`${window.location.origin}/west/pages/archives?s=${encodeURI($("#searchInput").val())}`)
    }
  });
</script>

</html>