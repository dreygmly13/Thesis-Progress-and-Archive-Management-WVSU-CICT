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

  <link rel="stylesheet" href="../../assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

  <link rel="stylesheet" href="../../assets/plugins/datatables-select/css/select.bootstrap4.min.css">

  <link rel="stylesheet" href="../../assets/plugins/datatables-searchbuilder/css/searchBuilder.bootstrap4.min.css">
  <style>
    .dt-button-collection {
      width: auto !important;
      left: 0 !important;
    }
  </style>
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
              <?php include("../components/unassigned-students.php"); ?>
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
  <!-- Alert -->
  <script src="../../assets/plugins/sweetalert2/sweetalert2.all.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../../assets/dist/js/demo.js"></script>

  <script src="../../assets/plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="../../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="../../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="../../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
  <script src="../../assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
  <script src="../../assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
  <script src="../../assets/plugins/jszip/jszip.min.js"></script>
  <script src="../../assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
  <script src="../../assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
  <script src="../../assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

  <script src="../../assets/plugins/datatables-select/js/dataTables.select.js"></script>
  <script src="../../assets/plugins/datatables-select/js/select.bootstrap4.min.js"></script>

  <script src="../../assets/plugins/datatables-searchbuilder/js/dataTables.searchBuilder.js"></script>

  <script>
    const table = $("#student_list").DataTable({
      "paging": true,
      "lengthChange": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      language: {
        searchBuilder: {
          button: 'Advance search',
        }
      },
      columnDefs: [{
          orderable: false,
          className: 'select-checkbox',
          targets: 0,
        },
        {
          targets: 1,
          visible: false,
          searchable: false,
        },
      ],
      select: {
        style: 'multi',
      },
      "buttons": [{
        text: 'Deselect all',
        action: function() {
          table.rows(['.selected']).deselect()
        }
      }, {
        extend: "searchBuilder",
        title: "Filter by"
      }],

    })

    table.buttons().container().appendTo('#student_list_wrapper .col-md-6:eq(0)');

    function setGroupNumber() {
      const userIds = $.map(table.rows(['.selected']).data(), (data) => data[1])
      if (userIds.length > 0) {
        swal.fire({
          title: 'Group Number',
          input: 'text',
          icon: "question",
          confirmButtonText: "Submit",
          inputValidator: (value) => {
            if (!value) {
              return 'You need to write something!'
            }
            if (value && isNaN(value)) {
              return 'Please input numbers only.'
            }
          }
        }).then((res) => {
          if (res.isConfirmed) {
            swal.showLoading();
            $.post(
              `../../backend/nodes?action=assignGroupNumber`, {
                groupNumber: res.value,
                userIds: userIds
              },
              (data, status) => {
                const resp = JSON.parse(data)
                swal.fire({
                  title: resp.success ? 'Success!' : 'Error!',
                  text: resp.message ? resp.message : "",
                  icon: resp.success ? 'success' : 'error',
                }).then(() => window.location.reload());
              }).fail(function(e) {
              swal.fire({
                title: 'Error!',
                text: e.statusText,
                icon: 'error',
              })
            });

          }
        })
      } else {
        swal.fire({
          title: 'Error!',
          text: "Please select students first.",
          icon: 'error',
        })
      }
    }
  </script>
</body>

</html>