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
                      <h4>Group List</h4>
                    </div>

                  </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table id="student_list" class="table table-bordered table-hover">
                    <thead>
                      <tr class="bg-gradient-dark text-light">
                        <th>Group#</th>
                        <th>Date created</th>
                        <th>Leader</th>
                        <th>Members</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = mysqli_query(
                        $conn,
                        "SELECT * FROM invite WHERE adviser_id='$user->id' ORDER BY `status` DESC"
                      );
                      while ($invite = mysqli_fetch_object($query)) :
                        $leader = get_user_by_id($invite->leader_id);
                        $leaderName = ucwords("$leader->first_name " . ($leader->middle_name != null ? $leader->middle_name[0] . "." : "") . " $leader->last_name");
                        $memberData = json_decode(getMemberData($leader->group_number, $leader->id));

                        $thesisGroupQuery = mysqli_query(
                          $conn,
                          "SELECT * FROM thesis_groups WHERE group_leader_id='$leader->id' and group_number='$leader->group_number'"
                        );

                        $hasSubmittedGroup = mysqli_num_rows($thesisGroupQuery) > 0;
                        $thesisGroupData = $hasSubmittedGroup ? mysqli_fetch_object($thesisGroupQuery) : null;
                      ?>
                        <tr>
                          <td><?= $leader->group_number ?></td>
                          <td><?= date("Y-m-d H:i:s", strtotime($invite->date_created)) ?></td>
                          <td>
                            <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                              <div class="mr-1">
                                <img src="<?= $leader->avatar != null ? $SERVER_NAME . $leader->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                              </div>
                              <div>
                                <?= $leaderName ?>
                              </div>
                            </div>
                          </td>
                          <td>
                            <?php
                            foreach ($memberData as $member) :
                              $memberName = ucwords("$member->first_name " . ($member->middle_name != null ? $member->middle_name[0] . "." : "") . " $member->last_name");
                            ?>
                              <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                                <div class="mr-1">
                                  <img src="<?= $member->avatar != null ? $SERVER_NAME . $member->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                                </div>
                                <div>
                                  <?= $memberName ?>
                                </div>
                              </div>
                            <?php endforeach; ?>
                          </td>
                          <td class="text-center py-1 px-2">
                            <?php
                            $class = "";
                            if ($invite->status == "PENDING") {
                              $class = "rounded-pill badge badge-warning bg-gradient-warning px-3";
                            } else if ($invite->status == "DECLINED") {
                              $class = "rounded-pill badge badge-danger bg-gradient-danger px-3";
                            } else {
                              $class = "rounded-pill badge badge-success bg-gradient-success px-3";
                            }
                            ?>
                            <span class="<?= $class ?>" style="font-size: 18px;">
                              <?= $invite->status ?>
                            </span>
                          </td>
                          <td class="text-center">
                            <button type="button" class="btn btn-primary btn-gradient-primary m-1" onclick="handleBtnClick('<?= $invite->id ?>', '<?= $invite->leader_id ?>','approve')" <?= $invite->status == "DECLINED" || $invite->status == "APPROVED"  ? "disabled" : "" ?>>
                              Approved
                            </button>
                            <button type="button" class="btn btn-danger btn-gradient-danger m-1" onclick="handleBtnClick('<?= $invite->id ?>', '<?= $invite->leader_id ?>', 'decline')" <?= $invite->status == "DECLINED" || $invite->status == "APPROVED"  ? "disabled" : "" ?>>
                              Decline
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
  <script src="../../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

  <script>
    $(function() {
      $("#student_list").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
      });
    });

    function handleBtnClick(inviteId, leaderId, action) {
      swal.fire({
        title: 'Are you sure',
        icon: 'question',
        html: `you want to <strong>"${action}"</strong> this invite?`,
        showDenyButton: true,
        confirmButtonText: 'Yes',
        denyButtonText: 'No',
      }).then((res) => {
        if (res.isConfirmed) {
          $.post(
            `../../backend/nodes?action=handleAdviserInvite`, {
              invite_id: inviteId,
              action: action,
              leader_id: leaderId
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
    }
  </script>
</body>

</html>