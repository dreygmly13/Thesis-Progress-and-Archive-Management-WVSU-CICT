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
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-md-8 col-sm-12">
              <div class="content">
                <div class="card card-outline card-navy shadow rounded-0 mt-2">
                  <div class="card-header rounded-0">
                    <h5 class="card-title">
                      Upload Documents
                    </h5>
                  </div>
                  <div class="card-body rounded-0">
                    <div class="container-fluid">
                      <form method="POST" id="uploadForm" enctype="multipart/form-data">
                        <div class="form-group">
                          <label class="control-label text-navy">Document Title</label>
                          <input type="text" name="title" placeholder="Project Title" class="form-control form-control-border" required>
                        </div>

                        <div class="form-group">
                          <label class="control-label text-navy">Field Type</label>
                          <select name="type" class="form-control form-control-border" required>
                            <option value="" selected disabled>-- Select field type --</option>
                            <?php
                            $query = mysqli_query(
                              $conn,
                              "SELECT * FROM types"
                            );
                            while ($type = mysqli_fetch_object($query)) :
                            ?>
                              <option value="<?= $type->id ?>"><?= $type->name ?></option>
                            <?php endwhile; ?>
                          </select>
                        </div>

                        <div class="form-group">
                          <label class="control-label text-navy">Year</label>
                          <select name="year" class="form-control form-control-border" required>
                            <?php
                            for ($i = 0; $i < 51; $i++) :
                              $year = date("Y", strtotime(date("Y") . " -{$i} years"));
                            ?>
                              <option value="<?= $year ?>"><?= $year ?></option>
                            <?php endfor; ?>
                          </select>
                        </div>

                        <div class="form-group">
                          <label class="control-label text-navy">Description</label>
                          <textarea rows="3" name="description" placeholder="abstract" class="form-control form-control-border summernote" required></textarea>
                        </div>

                        <div class="form-group">
                          <label class="control-label">Project Image/Banner Image</label>
                          <div class="custom-file">
                            <input type="file" class="custom-file-input rounded-circle" name="banner" onchange="displayImg(this,$(this))" accept="image/*" required>
                            <label class="custom-file-label">Choose file</label>
                          </div>
                        </div>

                        <div class="form-group text-center">
                          <img src="<?= "$SERVER_NAME/assets/dist/img/no-image-available.png" ?>" alt="My Avatar" id="cimg" class="img-fluid banner-img bg-gradient-dark border">
                        </div>

                        <div class="form-group">
                          <label class="control-label">Project Document (PDF File Only)</label>
                          <div class="custom-file">
                            <input type="file" name="pdfFile" class="custom-file-input rounded-circle" name="banner" onchange="displayPDF(this,$(this))" accept="application/pdf" required>
                            <label class="custom-file-label">Choose file</label>
                          </div>
                        </div>

                        <div class="form-group">
                          <div class="embed-responsive embed-responsive-4by3" id="divIframe" style="display: none;">
                            <iframe class="embed-responsive-item" id="pdfPreview" allowfullscreen></iframe>
                          </div>
                        </div>

                        <div class="form-group d-flex justify-content-end">
                          <button type="submit" class="btn btn-primary btn-gradient-primary m-1" id="btnSave" disabled>
                            Save
                          </button>
                          <button type="button" class="btn btn-danger btn-gradient-danger m-1" onclick="return window.history.back()">
                            Cancel
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
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
</body>

<script>
  $("#uploadForm").on("submit", function(e) {
    swal.showLoading()
    $.ajax({
      url: "../../backend/nodes?action=saveOldDocuments",
      type: "POST",
      data: new FormData(this),
      contentType: false,
      cache: false,
      processData: false,
      success: function(data) {
        const resp = JSON.parse(data);
        if (resp.success) {
          swal.fire({
            title: resp.message,
            icon: 'question',
            html: `Would you like to upload another?`,
            showDenyButton: true,
            confirmButtonText: 'Yes',
            denyButtonText: 'No',
          }).then((res) => {
            if (res.isConfirmed) {
              window.location.reload();
            } else {
              window.location.href = "published-documents"
            }
          })
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
    e.preventDefault();
  })

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
      ['view', ['undo', 'redo', 'help']]
    ]
  })

  function displayImg(input, _this) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $(`#cimg`).attr('src', e.target.result);
        _this.siblings('.custom-file-label').html(input.files[0].name)
      }

      reader.readAsDataURL(input.files[0]);
    } else {
      $('#cimg').attr('src', "../../assets/dist/img/no-image-available.png")
    }
  }

  function displayPDF(input, _this) {
    if (input.files && input.files[0]) {
      if (input.files[0].name.split('.').pop().toLowerCase() === "pdf") {
        var reader = new FileReader();
        reader.onload = function(e) {
          $(`#pdfPreview`).attr('src', `${e.target.result}#embedded=true&toolbar=0&navpanes=0`);
          _this.siblings('.custom-file-label').html(input.files[0].name)
          $("#btnSave").prop("disabled", false)
          $("#divIframe").show()
        }
      } else {
        swal.mixin({
          toast: true,
          position: 'top',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
          }
        }).fire({
          icon: 'error',
          title: 'Upload pdf only'
        }).then(() => {
          $("#btnSave").prop("disabled", true)
        })
      }

      reader.readAsDataURL(input.files[0]);
    } else {
      $("#divIframe").hide()
    }
  }
</script>

</html>