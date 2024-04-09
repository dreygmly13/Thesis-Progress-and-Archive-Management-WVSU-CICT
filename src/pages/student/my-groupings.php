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

  <link rel="stylesheet" href="../../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

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
      <div class="container" style="padding-top: 9rem">
        <?php
        if (isset($_GET['page'])) {
          if ($_GET["page"] == "member_profile" && isset($_GET["u"])) {
            include("../components/member_profile.php");
          }
        } else {
          include("../components/groupings.php");
        }
        ?>
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

<script src="../../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

<script>
  $(function() {
    $("#groupings-table").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
    });
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
      window.location.replace(`${window.location.origin}/west/pages/archives?s=${$("#searchInput").val()}`)
    }
  });

  $("#btnSendInvite").on("click", function() {
    swal.showLoading();
    const declineAdviserId = $($(this)[0].lastElementChild).val();

    $.get(
      `../../backend/nodes?action=getAllAdviser${declineAdviserId ? "&&declineAdviserId="+declineAdviserId: ""}`,
      (data, status) => {
        const resp = JSON.parse(data)
        let options = "<option value='' disabled selected> -- select adviser -- </option>"
        options += resp.adviser.map((data) => {
          return `<option value="${data.id}">
                    ${data.first_name} ${data.middle_name ? data.middle_name.charAt(0)+"." : ""} ${data.last_name}
                  </option>`
        });
        const html = `
        <div class="form text-left">
          ${!declineAdviserId ? 
            `<div class="form-group">
              <label class="control-label text-navy">Proposed title</label>
              <input type="text" id="title" class="form-control" placeholder="Enter proposed title...">
            </div>` 
            : ""}
          <div class="form-group">
            <label class="control-label text-navy">Select adviser</label>
            <select id="inputAdviserId" class="form-control" style="text-transform: capitalize">
              ${resp.adviser.length == 0  ? "<option value='' disabled selected> No available adviser </option>" : options}
            </select>
          </div>
        </div>
        `;

        swal.fire({
          // title: 'Select Adviser',
          icon: 'question',
          html: html,
          showDenyButton: true,
          confirmButtonText: 'Submit',
          denyButtonText: 'Cancel',
          allowOutsideClick: false,
          allowEscapeKey: false,
          preConfirm: () => {
            let error = 0;
            if (!$("#title").val()) {
              $("#title").addClass("is-invalid");
              error++
            }
            if (!$("#inputAdviserId option:selected").val()) {
              $("#inputAdviserId").addClass("is-invalid");
              error++
            }

            return error == 0 ? true : false;
          },
        }).then((res) => {
          if (res.isConfirmed) {
            if ($("#inputAdviserId option:selected").val() && $("#title").val()) {
              sendAdviserInvite($("#inputAdviserId option:selected").val(), $("#title").val())
            }
          }
        })
        if (resp.adviser.length == 0) {
          swal.getConfirmButton().disabled = true
        }
      }).fail(function(e) {
      swal.fire({
        title: 'Error!',
        text: e.statusText,
        icon: 'error',
      })
    });
  })

  $("#btnCancelInvite").on("click", function() {
    swal.fire({
      title: 'Are you sure',
      icon: 'question',
      text: "You want to cancel this your invitation?",
      showDenyButton: true,
      confirmButtonText: 'Yes',
      denyButtonText: 'No',
    }).then((res) => {
      if (res.isConfirmed) {
        handleCancelInvite()
      }
    })
  })

  function handleCancelInvite() {
    $.post(
      `../../backend/nodes?action=cancelAdvisorInvite`,
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

  function sendAdviserInvite(inputAdviserId, inputTitle) {
    $.post(
      `../../backend/nodes?action=sendAdviserInvite`, {
        adviserId: inputAdviserId,
        title: inputTitle,
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

  $("#btnEditInstructor").on("click", function() {
    swal.showLoading();
    $.get(
      "../../backend/nodes?action=getCurrentInstructorWithOther",
      (data, status) => {
        const resp = JSON.parse(data)

        if (resp.success) {
          let options = resp.otherInstructors.map((data) => {
            return `<option value="${data.id}" >
                    ${data.first_name} ${data.middle_name ? data.middle_name.charAt(0)+"." : ""} ${data.last_name}
                  </option>`
          });

          swal.fire({
            title: 'Select your instructor',
            icon: 'question',
            html: `<span style="margin-bottom: 12px; float: left">
                      Your current instructor is: <strong>${resp.currentInstructor}</strong>
                    </span>
                    <select id="inputInstructorId" class="form-control" style="text-transform: capitalize">
                      ${options.length == 0  ? "<option value='' disabled selected> No available instructor </option>" : options}
                    </select>`,
            showDenyButton: true,
            confirmButtonText: 'Submit',
            denyButtonText: 'Cancel',
          }).then((res) => {
            if (res.isConfirmed) {
              submitToInstructor($("#inputInstructorId option:selected").val(), "edit")
            }
          })
          if (options.length == 0) {
            swal.getConfirmButton().disabled = true
          }

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
  })

  $("#btnSubmitToInstructor").on("click", function() {
    swal.showLoading();

    $.get(
      "../../backend/nodes?action=getAllInstructor",
      (data, status) => {
        const resp = JSON.parse(data)
        let options = resp.instructors.map((data) => {
          return `<option value="${data.id}" >
                    ${data.first_name} ${data.middle_name ? data.middle_name.charAt(0)+"." : ""} ${data.last_name}
                  </option>`
        });

        swal.fire({
          title: 'Select your instructor',
          icon: 'question',
          html: ` <select id="inputInstructorId" class="form-control" style="text-transform: capitalize">
                    ${options.length == 0  ? "<option value='' disabled selected> No available instructor </option>" : options}
                  </select>`,
          showDenyButton: true,
          confirmButtonText: 'Submit',
          denyButtonText: 'Cancel',
        }).then((res) => {
          if (res.isConfirmed) {
            submitToInstructor($("#inputInstructorId option:selected").val())
          }
        })

        if (options.length == 0) {
          swal.getConfirmButton().disabled = true
        }
      }).fail(function(e) {
      swal.fire({
        title: 'Error!',
        text: e.statusText,
        icon: 'error',
      })
    });
  })

  function submitToInstructor(selectedId) {
    $.post(
      `../../backend/nodes?action=sendToInstructor`, {
        instructorId: selectedId
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

  function handleDeleteMember(memberId) {
    swal.fire({
      title: 'Delete member',
      icon: 'warning',
      text: "Are you sure you want to delete this member?",
      showDenyButton: true,
      confirmButtonText: 'Yes',
      denyButtonText: 'No',
    }).then((res) => {
      if (res.isConfirmed) {
        $.post(
          "../../backend/nodes?action=deleteUser", {
            id: memberId
          },
          (data, status) => {
            const resp = JSON.parse(data)
            if (resp.success) {
              swal.fire({
                title: 'Success!',
                text: resp.message,
                icon: 'success',
              }).then(() => {
                window.location.href = './my-groupings'
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
    })
  }

  $("#add-group-mate").on("submit", function(e) {
    swal.showLoading()
    $.ajax({
      url: "../../backend/nodes?action=addGroupMate",
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
            icon: 'success',
            html: resp.message,
            showDenyButton: true,
            confirmButtonText: 'Yes',
            denyButtonText: 'No',
          }).then((res) => {
            if (res.isConfirmed) {
              $("#add-group-mate")[0].reset()
              $('#cimg').attr('src', "../../assets/dist/img/no-image-available.png")
            } else if (res.isDenied) {
              window.location.href = "./my-groupings"
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

  $("#update-member").on("submit", function(e) {
    swal.showLoading()
    $.ajax({
      url: "../../backend/nodes?action=updateUser",
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
          }).then(() => {
            window.location.href = "./my-groupings"
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

  function displayImg(input, _this) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $('#cimg').attr('src', e.target.result);
      }

      reader.readAsDataURL(input.files[0]);
    } else {
      $('#cimg').attr('src', "../../assets/dist/img/no-image-available.png");
    }
  }
</script>

</html>