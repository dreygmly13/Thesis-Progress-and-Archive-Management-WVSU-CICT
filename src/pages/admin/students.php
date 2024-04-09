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
              <?php
              include("../components/student-list.php");
              ?>
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

  <script>
    const table = $("#student_list").DataTable({
      "paging": true,
      "lengthChange": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "columns": [{
          "width": "5%"
        }, {
          "width": "30%"
        },
        {
          "width": "5%"
        },
        {
          "width": "20%"
        },
        {
          "width": "15%"
        }
      ]
    })

    function handleOpenRatingModal(modalId) {
      $(`#${modalId}`).modal({
        show: true,
        backdrop: 'static',
        keyboard: false,
        focus: true
      })
    }

    function handleSetLeader(inputStudentList, groupNumber) {
      const studentList = JSON.parse($(`#${inputStudentList}`).val());

      let options = "<option value='' disabled selected> -- select leader -- </option>"
      options += studentList.map((data) => {
        return `<option value="${data.id}">
                    ${data.name}
                  </option>`
      });
      const html = `
        <div class="form text-center">
          <div class="form-group">
            <label class="control-label text-navy">Select leader <span class="text-danger">*</span></label>
            <select id="inputLeader" class="form-control" style="text-transform: capitalize">
              ${studentList.length == 0  ? "<option value='' disabled selected> No available students </option>" : options}
            </select>
          </div>
        </div>
        `;

      swal.fire({
        icon: 'question',
        html: html,
        showDenyButton: true,
        confirmButtonText: 'Submit',
        denyButtonText: 'Cancel',
        allowOutsideClick: false,
        allowEscapeKey: false,
        preConfirm: () => {
          let error = 0;
          if (!$("#inputLeader").val()) {
            $("#inputLeader").addClass("is-invalid");
            swal.showValidationMessage("Please select leader")
            error++
          }

          return error == 0 ? true : false;
        },
      }).then((res) => {
        if (res.isConfirmed) {
          const leaderId = $("#inputLeader").val();
          $.post(
            `../../backend/nodes?action=assignLeader`, {
              leaderId: leaderId,
              groupNumber: groupNumber,
            },
            (data, status) => {
              const resp = JSON.parse(data)
              swal.fire({
                title: resp.success ? 'Success!' : 'Error!',
                text: resp.message,
                icon: resp.success ? 'success' : 'error',
              }).then(() => {
                location.reload();
              })
            }).fail(function(e) {
            swal.fire({
              title: 'Error!',
              text: e.statusText,
              icon: 'error',
            })
          });
        }
      })
      $("#inputLeader").on("change", function(e) {
        $("#inputLeader").removeClass("is-invalid");
        swal.resetValidationMessage()
      })
      if (studentList.length == 0) {
        swal.getConfirmButton().disabled = true
      }
    }
  </script>
</body>

</html>