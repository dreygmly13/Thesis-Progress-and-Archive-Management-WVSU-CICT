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
  <!-- summernote -->
  <link rel="stylesheet" href="../../assets/plugins/summernote/summernote-bs4.min.css">
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
              <div class="card card-outline rounded-0 card-navy mt-2">
                <div class="card-header">
                  <div class="row mb-2">
                    <div class="col-sm-6">
                      <h4>To Publish Documents</h4>
                    </div>

                  </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table id="pending_documents" class="table table-bordered table-hover">
                    <thead>
                      <tr class="bg-gradient-dark text-light">
                        <th>Date updated</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $documentQuery = mysqli_query(
                        $conn,
                        "SELECT * FROM documents  WHERE publish_status='TO PUBLISH'"
                      );
                      while ($document = mysqli_fetch_object($documentQuery)) :
                        $leader = $document->leader_id ? get_user_by_id($document->leader_id) : null;
                        $description = nl2br($document->description);
                      ?>
                        <tr>
                          <td><?= date("M d, Y h:i:s A", strtotime($document->date_updated)) ?></td>
                          <td><?= $document->title ?></td>
                          <td>
                            <?= strlen($description) > 250 ? substr($description, 0, 250) . "..." : $description ?>
                          </td>
                          <td class="text-center">
                            <?php
                            if ($document->leader_id != null) :
                              $panelRatingQ = mysqli_query(
                                $conn,
                                "SELECT * FROM panel_ratings WHERE leader_id='$leader->id'"
                              );
                              $disabled = mysqli_num_rows($panelRatingQ) > 0 ? "" : "disabled";
                            ?>
                              <button type="button" class="btn btn-primary btn-gradient-primary m-1" onclick="return window.location.href='students?rating&&leaderId=<?= $leader->id ?>&&groupNumber=<?= $leader->group_number ?>'" <?= $disabled ?>>
                                Preview rating
                              </button>
                            <?php endif; ?>

                            <button type="button" class="btn btn-secondary btn-gradient-secondary m-1" onclick="handleOpenModal('<?= $document->id ?>')">
                              Preview
                            </button>
                            <button type="button" class="btn btn-success btn-gradient-success m-1" onclick="handlePublishDocument('<?= $document->id ?>')">
                              Publish
                            </button>
                          </td>
                        </tr>
                        <div class="modal fade" id="preview<?= $document->id ?>">
                          <div class="modal-dialog modal-xl modal-dialog-scrollable ">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">
                                  <?= ucwords($document->title) ?> <br>
                                  <small class="text-muted">Published on <?= date("F d, Y h:i:s A", strtotime($document->date_updated)) ?></small>
                                </h5>

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true" style="font-size: 30px">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
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
                                          <?= ucwords("$leader->first_name " . ($leader->middle_name != null ? $leader->middle_name[0] . "." : "") . " $leader->last_name") ?>
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
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-gradient-secondary" onclick="return window.open('./preview-document?d=<?= urlencode($document->project_document) ?>')">
                                  Open document in new tab
                                </button>
                                <button type="button" class="btn btn-success btn-gradient-success" onclick="handlePublishDocument('<?= $document->id ?>')">
                                  Publish
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>

                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
                <!-- /.card-body -->
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
  <!-- overlayScrollbars -->
  <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../../assets/dist/js/adminlte.min.js"></script>
  <!-- Alert -->
  <script src="../../assets/plugins/sweetalert2/sweetalert2.all.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../../assets/dist/js/demo.js"></script>
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

  <script src="../../assets/plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="../../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="../../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="../../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

  <script>
    $(function() {
      $("#pending_documents").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "columns": [{
            "width": "10%"
          }, {
            "width": "20%"
          },
          {
            "width": "50%"
          },
          {
            "width": "15%"
          }
        ]
      });
    });

    function handlePublishDocument(documentId) {
      swal.showLoading();
      $.post(
        "../../backend/nodes?action=publishDocument", {
          documentId: documentId
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
    }

    function handleOpenModal(modalId = null) {
      if (modalId) {
        $(`#preview${modalId}`).modal({
          show: true,
          backdrop: 'static',
          keyboard: false,
          focus: true
        })
      }
    }
  </script>
</body>

</html>