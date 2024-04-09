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
              <?php include("../components/pending-document.php"); ?>
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
  <!-- Summernote -->
  <script src="../../assets/plugins/summernote/summernote-bs4.min.js"></script>

  <script>
    $(document).ready(function() {
      $('.summernote').summernote({
        height: 200,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
          ['fontname', ['fontname']],
          ['fontsize', ['fontsize']],
          ['color', ['color']],
          ['para', ['ol', 'ul', 'paragraph', 'height']],
          ['table', ['table']],
          ['insert', ['link', 'picture']],
          ['view', ['undo', 'redo', 'fullscreen', 'codeview', 'help']]
        ]
      })
    })

    function handleMarkResolved(token, documentId, role) {
      swal.fire({
        title: 'Are you sure',
        icon: 'question',
        html: `you want to mark this as resolved?`,
        showDenyButton: true,
        confirmButtonText: 'Yes',
        denyButtonText: 'No',
      }).then((res) => {
        if (res.isConfirmed) {
          swal.showLoading();
          $.post(
            "../../backend/nodes?action=markFeedbackResolved", {
              id: documentId,
              token: token,
              role: role,
            },
            (data, status) => {
              const resp = JSON.parse(data)
              if (resp.success) {
                swal.fire({
                  title: 'Success!',
                  text: resp.message,
                  icon: 'success',
                }).then(() => window.location.reload())
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
      })
    }

    function handleFileFeedback(el) {
      swal.showLoading()
      $.post(
        "../../backend/nodes?action=fileFeedback",
        $(el[0].form).serialize(),
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

    function handleApproved(documentId, userRole) {
      swal.fire({
        title: 'Are you sure',
        icon: 'question',
        html: `you want to approved this document?`,
        showDenyButton: true,
        confirmButtonText: 'Yes',
        denyButtonText: 'No',
      }).then((res) => {
        if (res.isConfirmed) {
          swal.showLoading();
          $.post(
            "../../backend/nodes?action=approvedDocument", {
              id: documentId,
              role: userRole,
            },
            (data, status) => {
              const resp = JSON.parse(data)
              if (resp.success) {
                swal.fire({
                  title: 'Success!',
                  text: resp.message,
                  icon: 'success',
                }).then(() => window.location.reload())
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
      })
    }

    $(function() {
      $("#pending_documents").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "columns": [{
            "width": "10%"
          }, {
            "width": "1%"
          },
          {
            "width": "20%"
          },
          {
            "width": "30%"
          },
          {
            "width": "30%"
          },
          {
            "width": "1%"
          }
        ]
      });
    });

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