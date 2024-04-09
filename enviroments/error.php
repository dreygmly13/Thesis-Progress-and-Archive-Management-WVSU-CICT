<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Error 403 | Forbidden</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="./assets/plugins/fontawesome-free/css/all.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="./assets/dist/css/adminlte.min.css" />
  <style>
    html {
      display: flex;
      justify-content: center;
      align-items: center;
    }
  </style>
</head>

<body>
  <?php
  $headline = "";
  $content = "";
  $message = "";

  if (isset($_GET["403"])) {
    $headline = "403";
    $content = "Forbidden";
    $message = "You don't have permission to access this resource.";
  } else if (isset($_GET["404"])) {
    $headline = "404";
    $content = "Oops! Page not found";
    $message = "We could not find the page you were looking for.";
  } else if (isset($_GET["500"])) {
    $headline = "500";
    $content = "Internal Server Error";
    $message = "We will work on fixing that right away.";
  }
  ?>
  <section class="content">
    <div class="error-page">
      <h2 class="headline <?= isset($_GET["403"]) ? "text-warning" : "text-danger" ?> "><?= $headline ?></h2>

      <div class="error-content">
        <h3>
          <i class="fas fa-exclamation-triangle <?= isset($_GET["403"]) ? "text-warning" : "text-danger" ?>"></i>
          <?= $content ?>
        </h3>

        <p>
          <?= $message ?>
        </p>
        <p>
          <a href="#" onclick="return window.history.back()"><i class="fa fa-arrow-left"></i> Go Back</a>
        </p>
      </div>
      <!-- /.error-content -->
    </div>
    <!-- /.error-page -->
  </section>
</body>
<!-- jQuery -->
<script src="./assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="./assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script></script>

</html>