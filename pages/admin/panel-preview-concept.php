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
  <!-- summernote -->
  <link rel="stylesheet" href="../../assets/plugins/summernote/summernote-bs4.min.css" />
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

    $leader = get_user_by_id($_GET["leader_id"]);
    $documents = getAllSubmittedDocument($leader);
    ?>

    <!-- /.navbar -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="card card-outline card-primary shadow rounded-0">
            <div class="card-body">
              <div class="row mb-2">
                <fieldset>
                  <legend class="text-navy">Project Leader:</legend>
                  <div class="pl-4">
                    <div class="ml-2 mt-2 mb-2 d-flex justify-content-start align-items-center">
                      <div class="mr-1">
                        <img src="<?= $leader->avatar != null ? $SERVER_NAME . $leader->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                      </div>
                      <div>
                        <?= ucwords("$leader->first_name " . ($leader->middle_name != null ? $leader->middle_name[0] . "." : "") . " $leader->last_name") ?>
                      </div>
                    </div>
                  </div>
                </fieldset>
                <div class="col-md-12">
                  <?php $memberData = json_decode(getMemberData($leader->group_number, $leader->id));
                  if (count($memberData) > 0) : ?>
                    <legend class="text-navy">Project Members:</legend>
                    <div class="pl-4">
                      <?php
                      foreach ($memberData as $member) :
                        $memberName = ucwords("$member->first_name " . ($member->middle_name != null ? $member->middle_name[0] . "." : "") . " $member->last_name");
                      ?>
                        <div style="float:left">
                          <div class="m-2 d-flex justify-content-start align-items-center">
                            <div class="mr-1">
                              <img src="<?= $member->avatar != null ? $SERVER_NAME . $member->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                            </div>
                            <div>
                              <?= $memberName ?>
                            </div>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>

          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <section class="content">
            <div class="container-fluid">
              <?php
              foreach ($documents as $document) :
                $ids = json_encode(
                  array(
                    "document_id" => $document->id,
                    "leader_id" => $leader->id,
                    "panel_id" => $user->id,
                  )
                );
              ?>
                <div class="card card-outline card-primary shadow rounded-0">
                  <div class="card-header">
                    <div class="card-title">
                      <h2>
                        <strong>
                          <?= ucwords($document->title) ?>
                        </strong>
                      </h2>
                    </div>
                    <div class="card-tools">
                      <a data-toggle="collapse" href="#document<?= $document->id ?>" aria-expanded="true" aria-controls="document<?= $document->id ?>" class="btn btn-link">
                        <i class="fa fa-window-minimize"></i>
                      </a>
                    </div>
                  </div>
                  <div id="document<?= $document->id ?>" class="collapse show" aria-labelledby="heading-example">
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
                          <legend class="text-navy">Description:</legend>
                          <div class="pl-4">
                            <?= nl2br($document->description) ?>
                          </div>
                        </fieldset>

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
                    <div class="card-footer d-flex justify-content-end">
                      <?php
                      if ($user->role == "panel") :
                        $disabled = false;
                        $ratingQ = mysqli_query(
                          $conn,
                          "SELECT * FROM panel_ratings WHERE document_id='$document->id' and panel_id='$user->id' and rating_type='concept'"
                        );
                        if (mysqli_num_rows($ratingQ) > 0) {
                          $disabled = true;
                        }
                      ?>
                        <button type="button" class="btn btn-success m-1" onclick="handleRateConcept('<?= $document->id ?>')" <?= $disabled ? "disabled" : "" ?>>Rate Concept</button>
                        <?php
                        if ($disabled) :
                        ?>
                          <button type="button" class="btn btn-primary m-1" onclick="return window.location.href = 'assigned-groups?update&&documentId=<?= $document->id ?>&&type=concept'">Preview Concept Rating</button>
                      <?php endif;
                      endif; ?>
                      <button type="button" class="btn btn-secondary btn-gradient-secondary m-1" onclick="return window.open('./preview-document?d=<?= urlencode($document->project_document) ?>')">
                        Open document in new tab
                      </button>
                    </div>
                  </div>
                </div>
                <!-- Modal Rate -->
                <div class="modal fade" id="modalConcept<?= $document->id ?>">
                  <div class="modal-dialog modal-lg modal-dialog-scrollable ">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">
                          Rate Group Concept
                        </h5>
                        <button type="button" class="close" aria-label="Close" onclick="handleCloseModal('<?= $document->id ?>', 'modalConcept')">
                          <span aria-hidden="true" style="font-size: 30px">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body" id="modalRateBody">
                        <form id="modalConcept_form<?= $document->id ?>" method="POST">
                          <div class="form-group">
                            <label class="control-label">Comments/Suggestions</label>
                            <textarea type="text" class="form-control form-control-sm summernote" name="comment"></textarea>
                          </div>
                          <div class="form-group">
                            <label class="control-label">Action Taken <span class="text-danger">*</span></label>
                            <div class="row">
                              <div class="col-md-6 text-center">
                                <div class="icheck-success d-inline">
                                  <input type="radio" name="action" id="actionApprovedConcept<?= $document->id ?>" value="Approved" required>
                                  <label for="actionApprovedConcept<?= $document->id ?>">Approved</label>
                                </div>
                              </div>
                              <div class="col-md-6 text-center">
                                <div class="icheck-danger d-inline">
                                  <input type="radio" name="action" id="actionDisapprovedConcept<?= $document->id ?>" value="Disapproved" required>
                                  <label for="actionDisapprovedConcept<?= $document->id ?>">Disapproved</label>
                                </div>
                              </div>
                            </div>
                          </div>
                        </form>
                        <input type="text" id="ids<?= $document->id ?>" value='<?= $ids ?>' hidden readonly>
                      </div>
                      <div class="modal-footer">
                        <button type="button" onclick="handleSave('<?= $document->id ?>')" class="btn btn-primary btn-gradient-primary m-1">
                          Submit
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </section>
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

  </div>
  <!-- ./wrapper -->

  <!-- jQuery -->
  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <script src="../../assets/plugins/jquery-validation/jquery.validate.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../../assets/dist/js/adminlte.min.js"></script>
  <!-- Summernote -->
  <script src="../../assets/plugins/summernote/summernote-bs4.min.js"></script>
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="../../assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css" />
  <!-- Alert -->
  <script src="../../assets/plugins/sweetalert2/sweetalert2.all.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../../assets/dist/js/demo.js"></script>
</body>
<script>
  const defaultRatingData = {
    comment: ``,
    type: "concept",
    actionTaken: "",
    documentId: "",
    leaderId: "",
    panelId: "",
  };

  let modalRatingData = {
    comment: ``,
    type: "concept",
    actionTaken: "",
    documentId: "",
    leaderId: "",
    panelId: "",
  };

  const validateConfig = {
    errorElement: "span",
    errorPlacement: function(error, element) {
      error.addClass("invalid-feedback");
      element.closest(".form-group").append(error);
    },
    highlight: function(element, errorClass, validClass) {
      $(element).addClass("is-invalid");
    },
    unhighlight: function(element, errorClass, validClass) {
      $(element).removeClass("is-invalid");
    },
  }

  $("input[name=action]").on("change", function(e) {
    modalRatingData.actionTaken = e.target.value;
  });

  $(".summernote").on("summernote.change", function(e) {
    // callback as jquery custom event
    modalRatingData.comment = $(this).summernote("code");
  });

  function handleSave(ids) {
    const dataIds = JSON.parse($(`#ids${ids}`).val());
    modalRatingData.documentId = dataIds.document_id;
    modalRatingData.leaderId = dataIds.leader_id;
    modalRatingData.panelId = dataIds.panel_id;
    const formName = `modalConcept_form${dataIds.document_id}`

    $(`#${formName}`).validate(validateConfig);

    console.log(modalRatingData)

    if ($(`#${formName}`).valid()) {
      swal.showLoading();
      $.post(
        "../../backend/nodes?action=saveRating", {
          data: JSON.stringify(modalRatingData)
        },
        (data, status) => {
          const resp = JSON.parse(data)
          if (resp.success) {
            swal.fire({
              title: 'Success!',
              text: resp.message,
              icon: 'success',
            }).then(() => {
              window.location.reload()
            })
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
    } else {
      swal.fire({
        title: "Error!",
        text: "Please check error fields",
        icon: "error",
      });
    }
  }

  function handleRateConcept(documentId) {
    $(`#modalConcept${documentId}`).modal({
      show: true,
      backdrop: "static",
      keyboard: false,
      focus: true,
    });
  }

  function handleCloseModal(modalId, modalName) {
    $(`#${modalName}${modalId}`).modal("toggle");
    $(`#${modalName}_form${modalId}`)[0].reset();
    $(".summernote").summernote("reset");
    modalRatingData = defaultRatingData;
  }

  $(".summernote").summernote({
    height: 200,
    toolbar: [
      ["style", ["style"]],
      [
        "font",
        [
          "bold",
          "italic",
          "underline",
          "strikethrough",
          "superscript",
          "subscript",
          "clear",
        ],
      ],
      ["fontname", ["fontname"]],
      ["fontsize", ["fontsize"]],
      ["color", ["color"]],
      ["para", ["ol", "ul", "paragraph", "height"]],
      ["table", ["table"]],
      ["insert", ["link", "picture"]],
      ["view", ["undo", "redo", "fullscreen", "codeview", "help"]],
    ],
  });
</script>


</html>