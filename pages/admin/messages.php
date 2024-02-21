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
      <!-- Content Header (Page header) -->


      <!-- Main content -->
      <section class="content">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-md-8">
              <div class="card mt-2">
                <div class="card-header">
                  <h3 class="card-title">Messages</h3>
                </div>
                <?php

                $chatQ = $conn->query("SELECT * FROM chat WHERE (outgoing_id = $user->id OR incoming_id = $user->id) ORDER BY chat_id DESC");

                $chatIds = array();

                $fetchAllChat = $chatQ->fetch_all(MYSQLI_ASSOC);
                foreach ($fetchAllChat as $chatData) {
                  if (!in_array($chatData["incoming_id"], $chatIds) && !in_array($chatData["outgoing_id"], $chatIds)) {
                    if ($chatData["incoming_id"] != $user->id && $chatData["outgoing_id"] == $user->id) {
                      array_push($chatIds, $chatData["incoming_id"]);
                    } else if ($chatData["outgoing_id"] != $user->id && $chatData["incoming_id"] == $user->id) {
                      array_push($chatIds, $chatData["outgoing_id"]);
                    }
                  }
                }

                ?>
                <div class="card-body p-0" style="display: block;">
                  <ul class="nav nav-pills flex-column">
                    <?php
                    foreach ($chatIds as $chatId) :
                      $leader = get_user_by_id($chatId);;
                      $getLatestMessageData = getLatestMessageData($user->id, $chatId);

                      $latestMessage = "";

                      if ($getLatestMessageData) {
                        if ($getLatestMessageData->outgoing_id == $user->id) {
                          $latestMessage .= "You: ";
                          if ($getLatestMessageData->message_type == "image" || $getLatestMessageData->message_type == "file") {
                            $latestMessage .= "send a file.";
                          } else {
                            $latestMessage .= strlen($getLatestMessageData->message) > 50 ? substr($getLatestMessageData->message, 0, 50) . "..." : $getLatestMessageData->message;
                          }
                        } else {
                          if ($getLatestMessageData->message_type == "image" || $getLatestMessageData->message_type == "file") {
                            $latestMessage .= "send a file.";
                          } else {
                            $latestMessage .= strlen($getLatestMessageData->message) > 50 ? substr($getLatestMessageData->message, 0, 50) . "..." : $getLatestMessageData->message;
                          }
                        }
                      }

                    ?>
                      <li class="nav-item ">
                        <a href="./message?i=<?= $leader->id ?>" class="nav-link">
                          <small class='text-primary' style="float: right;">
                            <?= time_elapsed_string($getLatestMessageData->date_created) ?>
                          </small>
                          <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                            <div class="mr-3">
                              <img src="<?= $user->avatar != null ? $SERVER_NAME . $user->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                            </div>
                            <div>
                              <h6>
                                <strong>
                                  <?= ucwords("$leader->first_name " . ($leader->middle_name != null ? $leader->middle_name[0] . "." : "") . " $leader->last_name") ?>
                                </strong>
                              </h6>
                              <small>
                                <?= $latestMessage ?>
                              </small>
                            </div>
                          </div>
                        </a>
                      </li>
                    <?php endforeach;
                    if (count($chatIds) == 0) :
                    ?>
                      <h4 style='text-align:center'>No Messages to show.</h4>
                    <?php endif; ?>
                  </ul>
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
</body>

<script>
  $.get(`../../backend/nodes.php?action=getConvo`, function(data) {
    $("#divConvoData").html(data)
  });
</script>

</html>