<?php
include("../../backend/nodes.php");
if (isset($_SESSION["username"])) {
  $user = get_user_by_username($_SESSION['username']);
  $middleName = $user->middle_name != null ? $user->middle_name[0] : "";
} else {
  header("location: ../../");
}
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
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../../assets/plugins/summernote/summernote-bs4.min.css">
  <style>
    #searchNav::after {
      content: none
    }

    .banner-img {
      object-fit: scale-down;
      object-position: center center;
      height: 30vh;
      width: calc(100%);
    }

    #outerContainer #mainContainer div.toolbar {
      display: none !important;
      /* hide PDF viewer toolbar */
    }

    #outerContainer #mainContainer #viewerContainer {
      top: 0 !important;
      /* move doc up into empty bar space */
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
      <div class="container" style="padding-top: 9rem">
        <div class="row justify-content-center">
          <div class="col-md-8 col-sm-12">
            <div class="content">
              <div class="card card-outline card-primary shadow rounded-0">
                <div class="card-header rounded-0">
                  <h5 class="card-title">
                    Submit Documents
                  </h5>
                </div>
                <div class="card-body rounded-0">
                  <div class="container-fluid">
                    <form method="POST" id="archive-form" enctype="multipart/form-data">
                      <div class="form-group">
                        <label class="control-label text-navy">Project Leader</label>
                        <div class="ml-2 mt-2 mb-2 d-flex justify-content-start align-items-center">
                          <div class="mr-1">
                            <img src="<?= $user->avatar != null ? $SERVER_NAME . $user->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                          </div>
                          <div>
                            <?= ucwords("$user->first_name " . ($user->middle_name != null ? $user->middle_name[0] . "." : "") . " $user->last_name") ?>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label text-navy">Project Members</label>
                        <?php
                        $memberData = json_decode(getMemberData($user->group_number, $user->id));
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
                        <button type="submit" class="btn btn-primary btn-gradient-primary m-1" id="btnSave" disabled> Save</button>
                        <button type="button" class="btn btn-danger btn-gradient-danger m-1" onclick="return window.history.back()"> Cancel</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
  </div>
  <!-- ./wrapper -->


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
<!-- Summernote -->
<script src="../../assets/plugins/summernote/summernote-bs4.min.js"></script>

<script>
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

  $("#archive-form").on("submit", function(e) {
    $.ajax({
      url: "../../backend/nodes?action=saveDocument",
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
          }).then(() => window.location.href = "my-groupings")
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

  if (sessionStorage.getItem("searchInput")) {
    $("#searchInput").val(sessionStorage.getItem("searchInput"))
  }

  $("#searchInput").on("input", function(e) {
    sessionStorage.setItem("searchInput", e.target.value)
  })

  $(document).on('keypress', function(keyEvent) {
    if (keyEvent.which == 13 && $("#searchInput").val() !== "") {
      sessionStorage.setItem("searchInput", $("#searchInput").val())
      window.location.replace(`${window.location.origin}/west/pages/archives?s=${$("#searchInput").val()}`)
    }
  });

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