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
        <?php
        $leader = $isLeader ? $user : get_user_by_id($user->leader_id);
        $document = getApprovedDocument($leader);
        ?>
        <div class="card card-outline card-primary shadow rounded-0">
          <div class="card-header">
            <h3 class="card-title">
              <?php $class =  $document->publish_status == "PENDING" ? "badge-warning bg-gradient-warning" : "badge-success bg-gradient-success" ?>
              Publish status:
              <span class="rounded-pill badge px-3 <?= $class ?>" style="font-size: 15px;">
                <?= $document->publish_status ?>
              </span>
              <br>
              Field type:
              <?= mysqli_fetch_object(mysqli_query($conn, "SELECT `name`, id FROM types WHERE id='$document->type_id'"))->name ?>
            </h3>

            <?php if ($document->publish_status  == "PENDING" && $isLeader) : ?>
              <div class="card-tools">
                <button type="button" onclick="handleRedirectToUpdatePage('update-documents?doc_id=<?= $document->id ?>')" class="btn btn-primary btn-gradient-primary btn-sm">
                  <i class="fa fa-edit"></i>
                  Update Document
                </button>
              </div>
            <?php endif; ?>
          </div>
          <div class="card-body">

            <!-- Document Status -->
            <?php
            $documentStatus = getDocumentStatus($leader->id, $document->id);
            if (count($documentStatus) > 0) :
            ?>
              <table class="table table-bordered table-hover table-striped">
                <caption style="color: black; text-align: center; caption-side: top; border: 1px solid #dee2e6">
                  <h5>Documents Status</h5>
                </caption>
                <thead>
                  <tr class="bg-gradient-dark text-light">
                    <?php foreach ($documentStatus as $index => $value) : ?>
                      <th><?= $value["title"] ?></th>
                    <?php endforeach; ?>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($documentStatus as $index => $value) : ?>
                    <td>
                      <?php if ($value["status"]) : ?>
                        <span class="badge badge-<?= $value["status"] == "APPROVED" ? "success" : "danger" ?> rounded-pill px-4" style="font-size: 18px">
                          <em><?= $value["status"] ?></em>
                        </span>
                      <?php else : ?>
                        <span class="badge badge-warning rounded-pill px-4" style="font-size: 18px">
                          <em>PENDING</em>
                        </span>
                      <?php endif; ?>
                    </td>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>

            <!-- Feedbacks -->
            <table class="table table-bordered table-hover table-striped mt-4">
              <caption style="color: black; text-align: center; caption-side: top; border: 1px solid #dee2e6">
                <h5>Feedbacks</h5>
              </caption>
              <thead>
                <tr class="bg-gradient-dark text-light">
                  <th>Adviser</th>
                  <th>Instructor</th>
                </tr>
              </thead>
              <colgroup>
                <col class="col-md-6">
                <col class="col-md-6">
              </colgroup>
              <tbody>
                <?php
                $adviserFeedbackData = json_decode($document->adviser_feedback);
                $instructorFeedbackData = json_decode($document->instructor_feedback);
                ?>
                <tr>
                  <td>
                    <?php
                    if ($adviserFeedbackData == null) :
                    ?>
                      <p class='text-center'>
                        <span class="badge badge-warning rounded-pill px-4" style="font-size: 18px">
                          <em>Pending</em>
                        </span>
                      </p>
                      <?php
                    else :
                      foreach ($adviserFeedbackData->feedback as $adFed) :
                      ?>
                        <blockquote class="blockquote my-2 mx-0" style="font-size: 14px; overflow: hidden;">
                          <?php
                          if ($adFed->isResolved == "true") : ?>
                            <span>&#8226; <strong><?= $adFed->date ?></strong></span>
                            <p>
                              <s>
                                <?= nl2br($adFed->message) ?>
                              </s>
                            </p>
                            <span class="badge badge-success rounded-pill px-2" style="float:right;font-size: 14px">Resolved</span>
                          <?php else : ?>
                            <span>&#8226;<strong><?= $adFed->date ?></strong></span>
                            <p>
                              <?= nl2br($adFed->message) ?>
                            </p>
                            <span class="badge badge-warning rounded-pill px-2" style="float:right;font-size: 14px">To update</span>
                          <?php endif; ?>
                        </blockquote>
                      <?php
                      endforeach;
                    endif;
                    if ($adviserFeedbackData != null && $adviserFeedbackData->isApproved == "true") :
                      ?>
                      <p class='text-center'>
                        <span class="badge badge-success rounded-pill px-4" style="font-size: 18px">
                          <em>Approved</em>
                        </span>
                      </p>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php
                    if ($instructorFeedbackData == null) :
                    ?>
                      <p class='text-center'>
                        <span class="badge badge-warning rounded-pill px-4" style="font-size: 18px">
                          <em>Pending</em>
                        </span>
                      </p>
                      <?php
                    else :
                      foreach ($instructorFeedbackData->feedback as $insFed) :
                      ?>
                        <blockquote class="blockquote my-2 mx-0" style="font-size: 14px; overflow: hidden;">
                          <?php
                          if ($insFed->isResolved == "true") : ?>
                            <span>&#8226; <strong><?= $insFed->date ?></strong></span>
                            <p>
                              <s>
                                <?= nl2br($insFed->message) ?>
                              </s>
                            </p>
                            <span class="badge badge-success rounded-pill px-2" style="float:right;font-size: 14px">Resolved</span>
                          <?php else : ?>
                            <span>&#8226;<strong><?= $insFed->date ?></strong></span>
                            <p>
                              <?= nl2br($insFed->message) ?>
                            </p>
                            <span class="badge badge-warning rounded-pill px-2" style="float:right;font-size: 14px">To update</span>
                          <?php endif; ?>
                        </blockquote>
                      <?php
                      endforeach;
                    endif;
                    if ($instructorFeedbackData != null && $instructorFeedbackData->isApproved == "true") :
                      ?>
                      <p class='text-center'>
                        <span class="badge badge-success rounded-pill px-4" style="font-size: 18px">
                          <em>Approved</em>
                        </span>
                      </p>
                    <?php endif; ?>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Panel Suggestions -->
            <h5 class="mt-4">Panel Comments/Suggestions:</h5>
            <div class="row mt-2 justify-content-start">
              <?php
              $panelsQ = mysqli_query(
                $conn,
                "SELECT * FROM thesis_groups WHERE group_leader_id='$leader->id'"
              );
              $panelIds = json_decode(mysqli_fetch_object($panelsQ)->panel_ids);
              foreach ($panelIds as $panelId) :
                $panel = get_user_by_id($panelId);
              ?>
                <div class="col-md-4 col-sm-12">
                  <div class="card">
                    <div class="card-header bg-gradient-dark text-light">
                      <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                        <div class="mr-1">
                          <img src="<?= $panel->avatar != null ? $SERVER_NAME . $panel->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                        </div>
                        <div>
                          <?= ucwords("$panel->first_name " . ($panel->middle_name != null ? $panel->middle_name[0] . "." : "") . " $panel->last_name") ?>
                        </div>
                      </div>
                    </div>
                    <div class="card-body" style="background-color: #f2f2f2;">
                      <?php
                      $feedbacks = getPanelRating($panelId, $document->id);
                      foreach ($feedbacks as $feedbackData) :
                      ?>
                        <blockquote class="blockquote my-2 mx-0" style="font-size: 14px; overflow: hidden;">
                          <!-- <?php if ($feedbackData->action == "Approved") : ?>
                            <span class="badge badge-success rounded-pill px-2" style="float:right;font-size: 14px">
                              Approved
                            </span>
                          <?php else : ?>
                            <span class="badge badge-danger rounded-pill px-2" style="float:right;font-size: 14px">
                              Disapproved
                            </span>
                          <?php endif; ?> -->
                          <span>
                            &#8226;
                            <strong>
                              <?= panelNameType($feedbackData->rating_type) ?>
                            </strong>
                          </span>
                          <p class="mt-3">
                            <button type="button" class="btn btn-link" style="font-size: 14px;" onclick="handleOpenRatingModal('feedback<?= $feedbackData->rating_id ?>')">
                              Comments/Suggestions
                            </button>
                          </p>
                        </blockquote>

                        <div class="modal fade" id="feedback<?= $feedbackData->rating_id ?>">
                          <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">
                                  <?= panelNameType($feedbackData->rating_type) ?>
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true" style="font-size: 30px">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                <div class="row">
                                  <!-- <div class="col-12">
                                    Action taken:
                                    <?php if ($feedbackData->action == "Approved") : ?>
                                      <span class="badge badge-success rounded-pill px-2" style="font-size: 14px">
                                        Approved
                                      </span>
                                    <?php else : ?>
                                      <span class="badge badge-danger rounded-pill px-2" style="font-size: 14px">
                                        Disapproved
                                      </span>
                                    <?php endif; ?>
                                  </div> -->
                                  <div class="col-12 mt-2">
                                    <label class="form-label">Comment/Suggestions</label>
                                    <div class="jumbotron mb-0 p-3">
                                      <?= nl2br($feedbackData->comment) ?>
                                    </div>
                                  </div>

                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      <?php endforeach;
                      if (count($feedbacks) == 0) :
                      ?>
                        <div style="text-align: center;">
                          <span class="badge badge-primary rounded-pill px-2" style="font-size: 18px">
                            No comments yet
                          </span>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              <?php
              endforeach;
              ?>
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
  function handleRedirectToUpdatePage(url) {
    swal.fire({
      icon: 'warning',
      html: "Please <strong>take note</strong> of the <strong>feedbacks</strong> and <strong>comments</strong> before updating documents.<br>This will be <strong>reset</strong> after updating documents.",
    }).then((res) => {
      if (res.isConfirmed) {
        window.location.href = url
      }
    })
  }

  function handleOpenRatingModal(modalId) {
    $(`#${modalId}`).modal({
      show: true,
      backdrop: 'static',
      keyboard: false,
      focus: true
    })
  }

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