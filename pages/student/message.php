<?php
include("../../backend/nodes.php");
if (!isset($_SESSION["username"])) {
  header("location: $SERVER_NAME/");
}
include_once("../../backend/nodes.php");
$systemInfo = systemInfo();
$user = get_user_by_username($_SESSION['username']);
if (!isset($_GET['i'])) {
  header('Location: ' . $_SERVER['HTTP_REFERER']);
}
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
  <link rel="stylesheet" href="../../assets/dist/css/message.css">
  <link href="../../assets/plugins/toggle/bootstrap4-toggle.min.css" rel="stylesheet" />

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
      <div class="container" style="padding-top: 8rem">
        <div class="row justify-content-center">
          <div class="col-md-8 col-sm-12">
            <div class="card p-0 w-100">
              <div class="row card-title d-flex align-items-center border-bottom p-1">
                <div class="col-1">
                  <a href="#" onclick="return history.back()" class="pull-left mr-4 h3">
                    <i class="fa fa-arrow-left "></i>
                  </a>
                </div>
                <div class="col-10">
                  <?php
                  $user_details = get_user_by_id($_GET["i"]);
                  ?>

                  <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">

                    <div class="mr-1">
                      <img src="<?= $user_details->avatar != null ? $SERVER_NAME . $user_details->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                    </div>
                    <div>
                      <h6>
                        <strong>
                          <?= ucwords("$user_details->first_name " . ($user_details->middle_name != null ? $user_details->middle_name[0] . "." : "") . " $user_details->last_name") ?>
                        </strong>
                      </h6>
                    </div>
                  </div>
                </div>
                <div class="col-1">
                  <div class='dropdown pull-right'>
                    <button class='btn' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' style='background-color: transparent; border-radius: 50px;'>
                      <i class='fa fa-ellipsis-v' style="font-size: 20px;"></i>
                    </button>
                    <div class='dropdown-menu p-2' aria-labelledby='dropdownMenuButton'>
                      <h6 style="text-align: center;">Realtime message</h6>
                      <div class="d-flex justify-content-center">
                        <input type="checkbox" checked data-toggle="toggle" data-on="Enabled" data-off="Disabled" data-onstyle="success" data-offstyle="danger" id="switch" data-width="100">
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card-body p-0">
                <div class="chat-box" id="chatBox">
                </div>
              </div>

              <div class="card-footer p-0">
                <form id="send-message" role="form" method="POST" enctype="multipart/form-data">
                  <div class="collapse" id="inputFiles">
                    <div class="col-lg-12">
                      <div class="control-group" id="fields">
                        <div class="controls">
                          <div class="entry input-group upload-input-group mb-2">
                            <div class="input-group">
                              <div class="custom-file">
                                <input type="file" class="custom-file-input rounded-circle" name="files[]" onchange="handleFileChange($(this))">
                                <label class="custom-file-label">Choose file</label>
                              </div>
                              <div class="input-group-append">
                                <button class="btn btn-upload btn-success btn-add" type="button">
                                  <i class="fa fa-plus"></i>
                                </button>
                              </div>
                            </div>

                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row typing-area">
                    <input type="text" class="incoming_id" name="incoming_id" value="<?= $_GET['i']; ?>" hidden>
                    <div class="col-10">
                      <textarea id="inputMessage" name="message" placeholder="Type a message here..." rows="3"></textarea>
                    </div>
                    <div class="col">
                      <button class="btn btn-default bg-navy m-1" type="button" data-toggle="collapse" data-target="#inputFiles" aria-expanded="false" aria-controls="inputFiles">
                        <i class="fa fa-paperclip"></i>
                      </button>
                      <button type="submit" class="btn btn-default bg-navy m-1">
                        <i class="fa fa-paper-plane"></i>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

        </div>
        <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->
    </div>

    <div class="modal fade" id="previewImgModal">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

          <div class="modal-body">
            <img src="<?= "$SERVER_NAME/assets/dist/img/no-image-available.png" ?>" id="previewImg" class="img-fluid banner-img bg-gradient-dark border" style='border-radius: 0'>
          </div>
          <div class="modal-footer justify-content-end">
            <button type="button" class="btn btn-secondary btn-gradient-secondary m-1" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- ./wrapper -->
  </div>
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
<script src="../../assets/plugins/toggle/bootstrap4-toggle.min.js"></script>
<script>
  function handleFileChange(_this) {
    if (_this[0].files && _this[0].files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        _this.siblings('.custom-file-label').html(_this[0].files[0].name)
      }

      reader.readAsDataURL(_this[0].files[0]);
    }
  }

  function handlePreview(imgUrl) {
    $("#previewImg").attr("src", imgUrl)
    $(`#previewImgModal`).modal({
      show: true,
      backdrop: 'static',
      keyboard: false,
      focus: true
    })
  }

  let realtimeMessage = setInterval(function() {
    $.get(`../../backend/nodes.php?action=getChat&&incoming=${$(".incoming_id").val()}`, function(data) {
      $("#chatBox").html(data)
      $("#chatBox").scrollTop($("#chatBox")[0].scrollHeight);
    });
  }, 1000)

  $('#switch').change(function(e) {
    if (e.target.checked) {
      swal.mixin({
        toast: true,
        position: 'top',
        showConfirmButton: false,
        timer: 3000,
      }).fire({
        icon: 'success',
        title: 'Realtime message enabled.'
      })
      realtimeMessage = setInterval(function() {
        $.get(`../../backend/nodes.php?action=getChat&&incoming=${$(".incoming_id").val()}`, function(data) {
          $("#chatBox").html(data)
          $("#chatBox").scrollTop($("#chatBox")[0].scrollHeight);
        });
      }, 1000)
    } else {
      swal.mixin({
        toast: true,
        position: 'top',
        showConfirmButton: false,
        timer: 3000,
      }).fire({
        title: 'Realtime message disabled.'
      })
      clearTimeout(realtimeMessage)
    }
  })

  $('#inputFiles').on('hidden.bs.collapse', function(e) {
    $('input[type=file]').val("")
    $('.custom-file-label').html("Choose file")
  })

  $("#send-message").on("submit", function(e) {
    const fileValue = $('input[type=file]').val();
    const messageValue = $('#inputMessage').val();

    if (fileValue == "" && messageValue == "") {
      swal.mixin({
        toast: true,
        position: 'top',
        showConfirmButton: false,
        timer: 3000,

      }).fire({
        icon: 'error',
        title: 'Please send with file or message.'
      })
    } else {
      swal.showLoading()
      $.ajax({
        url: '../../backend/nodes.php?action=insertMessage',
        type: "POST",
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
          swal.close()
          $("#switch").prop('checked', true);
          const resp = JSON.parse(data);
          if (!resp.success) {
            swal.mixin({
              toast: true,
              position: 'top',
              showConfirmButton: false,
              timer: 3000,
            }).fire({
              icon: 'error',
              title: resp.message
            })
          }
          $('#inputFiles').collapse('hide')
          $("#send-message")[0].reset();
        }
      });
    }
    e.preventDefault();
  })

  $(document).on('click', '.btn-add', function(e) {
    e.preventDefault();
    var controlForm = $('.controls:first'),
      currentEntry = $(this).parents('.entry:first'),
      newEntry = $(currentEntry.clone()).appendTo(controlForm);

    newEntry.find('input').val('');
    controlForm.find('.entry:not(:last) .btn-add')
      .removeClass('btn-add').addClass('btn-remove')
      .removeClass('btn-success').addClass('btn-danger')
      .html('<span class="fa fa-trash"></span>');
  }).on('click', '.btn-remove', function(e) {
    $(this).parents('.entry:first').remove();

    e.preventDefault();
    return false;
  });

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