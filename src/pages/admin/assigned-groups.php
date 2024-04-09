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
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $systemInfo->system_name ?></title>
  <link rel="icon" href="<?= $SERVER_NAME . $systemInfo->logo ?>" />

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css" />
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="../../assets/dist/css/adminlte.min.css" />
  <!-- DataTables -->
  <link rel="stylesheet" href="../../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css" />
  <link rel="stylesheet" href="../../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css" />
  <link rel="stylesheet" href="../../assets/plugins/select2/css/select2.min.css" />
  <!-- summernote -->
  <link rel="stylesheet" href="../../assets/plugins/summernote/summernote-bs4.min.css" />
  <style>
    .v-align-middle {
      vertical-align: middle !important;
    }

    .radio-big {
      width: 19px;
      height: 19px;
    }

    .swalCustom {
      margin: 1em 2em 3px;
      width: auto;
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
              include("../components/assigned-group.php");
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
  <script src="../../assets/plugins/jquery-validation/jquery.validate.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="../../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../../assets/dist/js/adminlte.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../../assets/dist/js/demo.js"></script>
  <!-- Alert -->
  <script src="../../assets/plugins/sweetalert2/sweetalert2.all.min.js"></script>

  <script src="../../assets/plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="../../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="../../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="../../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
  <!-- Summernote -->
  <script src="../../assets/plugins/summernote/summernote-bs4.min.js"></script>
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="../../assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css" />
  <script>
    const defaultRatingData = {
      comment: ``,
      type: "",
      actionTaken: "",
      groupGrade: [{
          title: "Complexity and Innovativeness of the proposal",
          name: "complexity",
          max: 20,
          grade: 0,
        },
        {
          title: "Content and appropriateness of the Document",
          name: "content",
          max: 50,
          grade: 0,
        },
        {
          title: "Group Delivery and presentation",
          name: "delivery",
          max: 30,
          grade: 0,
        },
      ],
      otherGroupGrade: {
        documentation: {
          title: "Documentation",
          remarks: "",
          ratings: [{
            title: "Significant Improvement from previous document",
            name: "documentation_a",
            rating: "",
          }, {
            title: "Applied and Implemented previous suggestions/recommendations",
            name: "documentation_b",
            rating: "",
          }, {
            title: "Well researched as shown by the use of references",
            name: "documentation_c",
            rating: "",
          }]
        },
        system: {
          title: "System/Program",
          remarks: "",
          ratings: [{
            title: "Significant improvement from previous system presented",
            name: "system_a",
            rating: "",
          }, {
            title: "Continuity of the development of the system (not another system presented)",
            name: "system_b",
            rating: "",
          }, {
            title: "Applied/integrated previous comments/suggestions/recommendation",
            name: "system_c",
            rating: "",
          }, {
            title: "Completeness deliverable",
            name: "system_d",
            rating: "",
          }]
        },
        presentation: {
          title: "Group Presentation",
          remarks: "",
          ratings: [{
            title: "Preparedness/Use of Visual Aids",
            name: "presentation_a",
            rating: "",
          }, {
            title: "Collaboration/cooperation",
            name: "presentation_b",
            rating: "",
          }, {
            title: "Mastery of the Study",
            name: "presentation_c",
            rating: "",
          }, {
            title: "Over-all Impact of the presentation",
            name: "presentation_d",
            rating: "",
          }]
        },
      },
      individualGrade: [],
      documentId: "",
      leaderId: "",
      panelId: "",
    };

    let modalRatingData = {
      comment: ``,
      type: "",
      actionTaken: "",
      groupGrade: [{
          title: "Complexity and Innovativeness of the proposal",
          name: "complexity",
          max: 20,
          grade: 0,
        },
        {
          title: "Content and appropriateness of the Document",
          name: "content",
          max: 50,
          grade: 0,
        },
        {
          title: "Group Delivery and presentation",
          name: "delivery",
          max: 30,
          grade: 0,
        },
      ],
      otherGroupGrade: {
        documentation: {
          title: "Documentation",
          remarks: "",
          ratings: [{
            title: "Significant Improvement from previous document",
            name: "documentation_a",
            rating: "",
          }, {
            title: "Applied and Implemented previous suggestions/recommendations",
            name: "documentation_b",
            rating: "",
          }, {
            title: "Well researched as shown by the use of references",
            name: "documentation_c",
            rating: "",
          }]
        },
        system: {
          title: "System/Program",
          remarks: "",
          ratings: [{
            title: "Significant improvement from previous system presented",
            name: "system_a",
            rating: "",
          }, {
            title: "Continuity of the development of the system (not another system presented)",
            name: "system_b",
            rating: "",
          }, {
            title: "Applied/integrated previous comments/suggestions/recommendation",
            name: "system_c",
            rating: "",
          }, {
            title: "Completeness deliverable",
            name: "system_d",
            rating: "",
          }]
        },
        presentation: {
          title: "Group Presentation",
          remarks: "",
          ratings: [{
            title: "Preparedness/Use of Visual Aids",
            name: "presentation_a",
            rating: "",
          }, {
            title: "Collaboration/cooperation",
            name: "presentation_b",
            rating: "",
          }, {
            title: "Mastery of the Study",
            name: "presentation_c",
            rating: "",
          }, {
            title: "Over-all Impact of the presentation",
            name: "presentation_d",
            rating: "",
          }]
        },
      },
      individualGrade: [],
      documentId: "",
      leaderId: "",
      panelId: "",
    };

    const validateConfig = {
      errorElement: "span",
      errorPlacement: function(error, element) {
        error.addClass("invalid-feedback");
        element.closest(".form-group").append(error);
      },
      highlight: function(element, errorClass, validClass) {
        $(element).addClass("is-invalid");
      },
      unhighlight: function(element, errorClass, validClass) {
        $(element).removeClass("is-invalid");
      },
    }

    function handleSave(ids) {
      const dataIds = JSON.parse($(`#ids${ids}`).val());
      modalRatingData.documentId = dataIds.document_id;
      modalRatingData.leaderId = dataIds.leader_id;
      modalRatingData.panelId = dataIds.panel_id;
      const formName =
        modalRatingData.type === "concept" ?
        `modalConcept_form${dataIds.document_id}` :
        `modalRate_form${dataIds.document_id}`;

      $(`#${formName}`).validate(validateConfig);

      console.log(modalRatingData)

      if ($(`#${formName}`).valid()) {
        swal.showLoading();
        $.post(
          "../../backend/nodes?action=saveRating", {
            data: JSON.stringify(modalRatingData)
          },
          (data, status) => {
            const resp = JSON.parse(data)
            if (resp.success) {
              swal.fire({
                title: 'Success!',
                text: resp.message,
                icon: 'success',
              }).then(() => {
                window.location.reload()
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
      } else {
        swal.fire({
          title: "Error!",
          text: "Please check error fields",
          icon: "error",
        });
      }
    }

    $(".rating-radio").on("change", function(e) {
      const radioName = e.target.name
      if (radioName.includes("documentation")) {
        const documentation = modalRatingData.otherGroupGrade.documentation
        const index = documentation.ratings.findIndex(object => {
          return object.name === radioName;
        });

        documentation.ratings[index].rating = e.target.value

      } else if (radioName.includes("system")) {
        const system = modalRatingData.otherGroupGrade.system
        const index = system.ratings.findIndex(object => {
          return object.name === radioName;
        });

        system.ratings[index].rating = e.target.value

      } else if (radioName.includes("presentation")) {
        const presentation = modalRatingData.otherGroupGrade.presentation
        const index = presentation.ratings.findIndex(object => {
          return object.name === radioName;
        });

        presentation.ratings[index].rating = e.target.value

      } else if (radioName.includes("individual")) {
        const rate = e.target.value;
        const id = e.target.name.split("_").pop();

        const individualGrade = modalRatingData.individualGrade;
        const indexOfIndividualGrade =
          individualGrade[individualGrade.map((data) => data.id).indexOf(id)];

        if (indexOfIndividualGrade === undefined) {
          modalRatingData.individualGrade.push({
            id: id,
            name: $(`#individual_name${id}`).val(),
            rating: rate,
            remarks: $(`#individual_remarks${id}`).val()
          });
        } else {
          individualGrade[
            individualGrade.map((data) => data.id).indexOf(id)
          ].rating = rate;
        }

      }
    })

    function handleIndividualRemarks(id, name, remarks) {
      const individualGrade = modalRatingData.individualGrade;
      const indexOfIndividualGrade =
        individualGrade[individualGrade.map((data) => data.id).indexOf(id)];

      if (indexOfIndividualGrade === undefined) {
        modalRatingData.individualGrade.push({
          id: id,
          name: name,
          rating: "",
          remarks: remarks
        });
      } else {
        individualGrade[
          individualGrade.map((data) => data.id).indexOf(id)
        ].remarks = remarks;
      }

    }

    function handleAddRemarks(title, el) {
      if (title === "documentation") {
        modalRatingData.otherGroupGrade.documentation.remarks = $(el).val()
      } else if (title === "system") {
        modalRatingData.otherGroupGrade.system.remarks = $(el).val()
      } else if (title === "presentation") {
        modalRatingData.otherGroupGrade.presentation.remarks = $(el).val()
      }
    }

    function handleGroupGradeChange(name, grade) {
      const groupGrade = modalRatingData.groupGrade;

      if (grade === "") {
        groupGrade[
          groupGrade.map((data) => data.name).indexOf(name)
        ].grade = 0;
      } else {
        groupGrade[groupGrade.map((data) => data.name).indexOf(name)].grade =
          grade;
      }

      if (groupGrade.length > 0) {
        total = 0;
        for (let i = 0; i < groupGrade.length; i++) {
          total += Number(groupGrade[i].grade);
        }
        $(".gradeTotal").html(total)
      }

      console.log(modalRatingData);
    }

    function handleIndividualGrade(id, name, grade) {
      const individualGrade = modalRatingData.individualGrade;
      const indexOfIndividualGrade =
        individualGrade[individualGrade.map((data) => data.id).indexOf(id)];
      const inputGrade = grade ? grade : 0;

      if (indexOfIndividualGrade === undefined) {
        modalRatingData.individualGrade.push({
          id: id,
          name: name,
          grade: inputGrade,
        });
      } else {
        individualGrade[
          individualGrade.map((data) => data.id).indexOf(id)
        ].grade = inputGrade;
      }

      console.log(individualGrade);
    }

    $(".summernote").on("summernote.change", function(e) {
      // callback as jquery custom event
      modalRatingData.comment = $(this).summernote("code");
    });

    function handleTypeChange(e) {
      modalRatingData.type = $(e).val();
    }

    function changeRating(modalName, modalId) {
      handleCloseModal(modalId, modalName)
      handleOpenRatingModal(modalId)
    }

    function handleOpenRatingModal(modalId) {
      swal.fire({
        icon: "question",
        html: '<label class="control-label">Rating type <span class="text-danger">*</span></label>',
        input: "select",
        inputOptions: {
          concept: "Concept Proposal",
          "20percent": "20% Progress",
          "50percent": "50% Progress",
          final: "Final",
        },
        inputPlaceholder: "-- select rating type --",
        showDenyButton: true,
        confirmButtonText: "Okay",
        denyButtonText: "Cancel",
        allowOutsideClick: false,
        allowEscapeKey: false,
        inputValidator: (value) => {
          return new Promise((resolve) => {
            if (value === "") {
              resolve("Please select rating type.");
            } else {
              swal.close();
              modalRatingData.type = value;
              if (value === "concept") {
                window.location.href = "panel-preview-concept?leader_id=<?= $leader->id ?>"
                // $(`#modalConcept${modalId}`).modal({
                //   show: true,
                //   backdrop: "static",
                //   keyboard: false,
                //   focus: true,
                // });
              } else {
                $(`#modalRate${modalId}`).modal({
                  show: true,
                  backdrop: "static",
                  keyboard: false,
                  focus: true,
                });
              }
            }
          });
        },
      });
    }

    $(".updateRating").on("submit", function(e) {
      $(".updateRating").validate(validateConfig);
      if ($(`.updateRating`).valid()) {
        swal.showLoading();
        $.post(
          "../../backend/nodes?action=updatePanelRating",
          $(this).serialize(),
          (data, status) => {
            const resp = JSON.parse(data)
            if (resp.success) {
              swal.fire({
                title: 'Success!',
                text: resp.message,
                icon: 'success',
              }).then(() => {
                window.history.back()
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
      } else {
        swal.fire({
          title: "Error!",
          text: "Please check error fields",
          icon: "error",
        });
      }

      e.preventDefault()
    })

    function handleRedirectPanelRating(documentId, panelId) {
      swal.showLoading()
      $.post(
        "../../backend/nodes?action=getPanelRatingType", {
          panel_id: panelId,
          document_id: documentId
        },
        (data, status) => {
          swal.close()
          const resp = JSON.parse(data)
          if (resp.length > 0) {
            swal.fire({
              icon: "question",
              html: '<label class="control-label">Rating type <span class="text-danger">*</span></label>',
              input: "select",
              inputOptions: Object.assign({}, ...resp),
              inputPlaceholder: "-- select rating type to update --",
              showDenyButton: true,
              confirmButtonText: "Okay",
              denyButtonText: "Cancel",
              allowOutsideClick: false,
              allowEscapeKey: false,
              inputValidator: (value) => {
                return new Promise((resolve) => {
                  if (value === "") {
                    resolve("Please select rating type.");
                  } else {
                    window.location.href = `./assigned-groups?update&&documentId=${documentId}&&type=${value}`
                  }
                });
              },
            });
          } else {
            swal.fire({
              text: "You don't have any rating to update on this group.",
              icon: 'info',
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

    $("input[name=action]").on("change", function(e) {
      modalRatingData.actionTaken = e.target.value;
    });

    function handleCloseModal(modalId, modalName) {
      $(`#${modalName}${modalId}`).modal("toggle");
      $(`#${modalName}_form${modalId}`)[0].reset();
      $(`#${modalName}_form${modalId}`).validate(validateConfig).resetForm();
      $(".summernote").summernote("reset");
      modalRatingData = defaultRatingData;
      $(".gradeTotal").html("")
    }

    $(".summernote").summernote({
      height: 200,
      toolbar: [
        ["style", ["style"]],
        [
          "font",
          [
            "bold",
            "italic",
            "underline",
            "strikethrough",
            "superscript",
            "subscript",
            "clear",
          ],
        ],
        ["fontname", ["fontname"]],
        ["fontsize", ["fontsize"]],
        ["color", ["color"]],
        ["para", ["ol", "ul", "paragraph", "height"]],
        ["table", ["table"]],
        ["insert", ["link", "picture"]],
        ["view", ["undo", "redo", "fullscreen", "codeview", "help"]],
      ],
    });

    function handleOpenModal(modalId = null) {
      if (modalId) {
        $(`#preview${modalId}`).modal({
          show: true,
          backdrop: "static",
          keyboard: false,
          focus: true,
        });
      }
    }

    $("#assigned_group").DataTable({
      responsive: true,
      lengthChange: false,
      autoWidth: false,
      columns: [{
          width: "1%",
        },
        {
          width: "20%",
        },
        {
          width: "15%",
        },
        {
          width: "40%",
        },
        {
          width: "15%",
        },
      ],
    });
  </script>
</body>

</html>