<?php
include("../backend/nodes.php");
if (isset($_SESSION["username"])) {
  $user = get_user_by_username($_SESSION['username']);
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
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">

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
    <?php
    if (!isset($_SESSION["username"])) {
      include_once("../components/navbar.php");
    } else {
      include_once("components/navbar.php");
    }
    ?>

    <!-- Content Wrapper.-->
    <div class="content-wrapper">

      <!-- Main content -->
      <div class="content" style="padding:9rem 0rem 0rem 0rem;">
        <?php
        $queryStr = "";
        $limit = 10;
        $offset = !isset($_GET["o"]) ? 0 : $_GET["o"];
        $currentPage = !isset($_GET["p"]) ? 1 : $_GET["p"];
        $filterBy = isset($_GET['s']) ? urldecode($_GET['s']) : "";

        if (isset($_GET['s'])) {
          $queryStr = "SELECT * FROM documents WHERE title LIKE '%$filterBy%' and publish_status='PUBLISHED' LIMIT $limit OFFSET $offset";
        } else {
          $queryStr = "SELECT * FROM documents WHERE publish_status='PUBLISHED' LIMIT $limit OFFSET $offset";
        }

        $query = mysqli_query($conn, $queryStr);

        $pageCount = getPageCount($filterBy, $limit);
        ?>
        <div class="container">
          <div class="content py-2">
            <div class="col-12">
              <div class="card card-outline card-primary shadow rounded-0">
                <div class="card-body rounded-0">
                  <h2>Archive List</h2>
                  <hr class="bg-navy">
                  <?php if (isset($_GET['s'])) : ?>
                    <h3 class="text-center"><b>Search Result for <?= "\"{$_GET['s']}\"" ?> keyword</b></h3>
                  <?php endif;
                  while ($data = mysqli_fetch_object($query)) :
                    $leader = $data->leader_id == null ? null : get_user_by_id($data->leader_id);
                    $leaderName = "";
                    if ($leader) {
                      $leaderName = ucwords("$leader->first_name " . ($leader->middle_name != null ? $leader->middle_name[0] . "." : "") . " $leader->last_name");
                    }
                    $description = $data->description;
                  ?>
                    <div class="list-group mt-3">
                      <a href="./preview-document?id=<?= $data->id ?>" class="text-decoration-none text-dark list-group-item list-group-item-action">
                        <div class="row">
                          <div class="col-lg-4 col-md-5 col-sm-12 text-center">
                            <img src="<?= $SERVER_NAME . $data->img_banner ?>" class="banner-img img-fluid bg-gradient-dark" alt="Banner Image">
                          </div>
                          <div class="col-lg-8 col-md-7 col-sm-12">
                            <h3 class="text-navy"><b><?= $data->title ?></b></h3>
                            <small class="text-muted">By <b class="text-info"><?= $leaderName == "" ? "N/A" : $leaderName ?></b></small>
                            <p class="truncate-5">
                              <?= strlen($description) > 250 ? substr($description, 0, 250) . "..." : $description ?>
                            </p>
                          </div>
                        </div>
                      </a>
                    </div>
                  <?php endwhile; ?>
                </div>
                <div class="card-footer clearfix rounded-0">
                  <div class="col-12">
                    <div class="row">
                      <div class="col-md-6"><span class="text-muted">Display Items: <?= mysqli_num_rows($query) ?> </span></div>
                      <div class="col-md-6">

                        <?php if ($pageCount > 0) : ?>
                          <nav class="pagination-a">
                            <ul class="pagination justify-content-end">
                              <li class="page-item <?= $currentPage == 1 ? "disabled" : "" ?>">
                                <button class="page-link" onclick="previousPage('<?= $currentPage ?>','<?= $filterBy ?>', '<?= $offset ?>', '<?= $limit ?>')">
                                  <span class="fa fa-chevron-left"></span>
                                </button>
                              </li>

                              <?php for ($i = 1; $i <= $pageCount; $i++) : ?>

                                <li class="page-item <?= $currentPage == $i ? "active" : "" ?>">
                                  <button class="page-link" onclick="changeLoc(<?= $i ?>, <?= $limit * intval($i - 1) ?>, '<?= $filterBy ?>')"><?= $i ?></button>
                                </li>
                              <?php endfor; ?>

                              <li class="page-item <?= $currentPage == $pageCount ? "disabled" : "" ?>">
                                <button class="page-link" onclick="nextItemPage('<?= $currentPage ?>','<?= $filterBy ?>', '<?= $offset ?>', '<?= $limit ?>')">
                                  <span class="fa fa-chevron-right"></span>
                                </button>
                              </li>

                            </ul>
                          </nav>
                        <?php endif; ?>
                      </div>
                    </div>
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
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../assets/dist/js/adminlte.min.js"></script>
<!-- Alert -->
<script src="../assets/plugins/sweetalert2/sweetalert2.all.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../assets/dist/js/demo.js"></script>

<script>
  function previousPage(page, filter, offset, limit) {
    const newOffset = Number(offset) - Number(limit);
    const newPage = Number(page) - 1;
    let path = "archives";

    if (filter == "") {
      path += `?p=${newPage}&&o=${newOffset}`
    } else {
      path += `?s=${filter}&&p=${newPage}&&o=${newOffset}`
    }

    window.location.href = path
  }

  function nextItemPage(page, filter, offset, limit) {
    const newOffset = Number(offset) + Number(limit);
    const newPage = Number(page) + 1;
    let path = "archives";

    if (filter == "") {
      path += `?p=${newPage}&&o=${newOffset}`
    } else {
      path += `?s=${filter}&&p=${newPage}&&o=${newOffset}`
    }

    window.location.href = path
  }

  function changeLoc(page, offset, filter) {
    let path = "archives";

    if (filter == "") {
      path += `?p=${page}&&o=${offset}`
    } else {
      path += `?s=${filter}&&p=${page}&&o=${offset}`
    }

    window.location.href = path
  }

  if (sessionStorage.getItem("searchInput")) {
    $("#searchInput").val(sessionStorage.getItem("searchInput"))
  }

  $("#searchInput").on("input", function(e) {
    sessionStorage.setItem("searchInput", e.target.value)
  })

  $(document).on('keypress', function(keyEvent) {
    if (keyEvent.which == 13 && $("#searchInput").val() !== "") {
      sessionStorage.setItem("searchInput", $("#searchInput").val())
      window.location.replace(`${window.location.origin}/west/pages/archives?s=${encodeURI($("#searchInput").val())}`)
    }
  });
</script>

</html>