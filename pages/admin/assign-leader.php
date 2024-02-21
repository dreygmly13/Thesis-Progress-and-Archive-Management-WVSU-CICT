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
              <div class="card card-outline rounded-0 card-navy mt-2">
                <div class="card-header">
                  <div class="row mb-2">
                    <div class="col-sm-6">
                      <h4>Assign Leader</h4>
                    </div>

                  </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table id="student_list" class="table table-bordered table-hover">
                    <thead>
                      <tr class="bg-gradient-dark text-light">
                        <th>Group Number</th>
                        <th>Student Name</th>
                        <th>Section</th>
                        <th>Course</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $handledSections = getInstructorHandledSections($user->id);

                      $courses = array();

                      foreach ($handledSections as $course) {
                        array_push($courses, $course["id"]);
                      }
                      $courses = (implode('\', \'', $courses));
                      $finCourse = "'" . $courses . "'";

                      $sections = array();

                      foreach ($handledSections as $index => $value) {
                        foreach ($value["sections"] as $s) {
                          array_push($sections, $s);
                        }
                      }
                      $sections = (implode('\', \'', array_unique($sections)));
                      $fin = "'" . $sections . "'";

                      $query = mysqli_query(
                        $conn,
                        "SELECT * FROM users WHERE `role`='student' and group_number is not NULL and course_id in(" . $finCourse . ") and year_and_section in(" . $fin . ") and isLeader is NULL and leader_id is NULL GROUP BY year_and_section, group_number"
                      );

                      while ($group = mysqli_fetch_object($query)) :
                        $courseData = getCourseData($group->course_id);
                      ?>
                        <tr>
                          <td style="vertical-align: middle; text-align:center;font-size: 30px"><?= $group->group_number ?></td>
                          <td>
                            <?php
                            $studentQ = mysqli_query(
                              $conn,
                              "SELECT * FROM users WHERE group_number='$group->group_number' and isLeader is NULL and leader_id is NULL"
                            );
                            $options = array();
                            while ($student = mysqli_fetch_object($studentQ)) :
                              $studentsName = ucwords("$student->first_name " . ($student->middle_name != null ? $student->middle_name[0] . "." : "") . " $student->last_name");
                              array_push($options, array("id" => $student->id, "name" => $studentsName));
                            ?>
                              <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                                <div class="mr-1">
                                  <img src="<?= $student->avatar != null ? $SERVER_NAME . $student->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                                </div>
                                <div>
                                  <?= $studentsName ?>
                                </div>
                              </div>
                            <?php endwhile; ?>
                          </td>
                          <td style="vertical-align: middle; text-align:center;font-size: 30px"><?= $group->year_and_section ?></td>
                          <td style="vertical-align: middle; text-align:center;font-size: 20px"><?= $courseData->name ?></td>
                          <td>
                            <input type="text" id="students_<?= $group->group_number ?>" value='<?= json_encode($options) ?>' hidden readonly>
                            <button type="button" class="btn btn-primary btn-gradient-primary" onclick="handleSetLeader('students_<?= $group->group_number ?>', '<?= $group->group_number ?>')">
                              Set group leader
                            </button>
                          </td>
                        </tr>
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
          swal.showLoading()
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