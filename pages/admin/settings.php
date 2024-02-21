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
          <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 col-sm-12 mt-2">
              <div class="card card-outline rounded-0 card-navy">
                <div class="card-header">
                  <h5 class="card-title">System Information</h5>
                </div>
                <form action="POST" id="system-frm" enctype="multipart/form-data">

                  <div class="card-body">
                    <div class="form-group">
                      <label class="control-label">System Name</label>
                      <input type="text" class="form-control form-control-sm" name="name" value="<?= $systemInfo->system_name ?>">
                    </div>

                    <div class="form-group">
                      <label class="control-label">Contact</label>
                      <input type="text" class="form-control form-control-sm" name="contact" value="<?= $systemInfo->contact ?>">
                    </div>

                    <div class="form-group">
                      <label for="content[about_us]" class="control-label">Welcome Content</label>
                      <textarea type="text" class="form-control form-control-sm summernote" name="content" id="welcome"><?= nl2br($systemInfo->home_content) ?></textarea>
                    </div>

                    <div class="form-group">
                      <label for="" class="control-label">System Logo</label>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input rounded-circle" name="system_logo" onchange="displayImg(this,$(this), 'systemLogo')">
                        <label class="custom-file-label">Choose file</label>
                      </div>
                    </div>

                    <div class="form-group d-flex justify-content-center">
                      <img src="<?= $SERVER_NAME . $systemInfo->logo ?>" alt="" id="systemLogo" class="img-fluid img-thumbnail" style="border: 0;box-shadow: none;">
                    </div>

                    <div class="form-group">
                      <label for="" class="control-label">Website Cover</label>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input rounded-circle" name="cover" onchange="displayImg(this,$(this), 'websiteCover')">
                        <label class="custom-file-label">Choose file</label>
                      </div>
                    </div>

                    <div class="form-group d-flex justify-content-center">
                      <img src="<?= $SERVER_NAME . $systemInfo->cover ?>" alt="" id="websiteCover" class="img-fluid img-thumbnail">
                    </div>

                  </div>
                  <div class="card-footer">
                    <div class="col-md-12">
                      <div class="row justify-content-end">
                        <button type="submit" class="btn btn-sm btn-primary m-1">Update</button>
                        <button type="button" class="btn btn-sm btn-danger m-1" onclick="return window.history.back()">Cancel</button>
                      </div>
                    </div>
                  </div>
                </form>
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
  <!-- Summernote -->
  <script src="../../assets/plugins/summernote/summernote-bs4.min.js"></script>

  <script>
    $("#system-frm").on("submit", function(e) {
      swal.showLoading();
      $.ajax({
        url: "../../backend/nodes?action=updateSystem",
        type: "POST",
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
          const resp = JSON.parse(data);
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
          };
        },
        error: function(data) {
          swal.fire({
            title: 'Oops...',
            text: 'Something went wrong.',
            icon: 'error',
          })
        }
      });

      e.preventDefault()
    })

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

    function displayImg(input, _this, elId) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $(`#${elId}`).attr('src', e.target.result);
          _this.siblings('.custom-file-label').html(input.files[0].name)
        }

        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>
</body>

</html>