<!-- Navbar -->
<div class="position-fixed top-0 w-100"  style="z-index: 999;">
  <nav class="main-header navbar navbar-expand-md bg-navy " style="width: 100%;">
    <div class="row w-100" style="padding: 7px">
      <div class="col-6">
        <span class="mr-2 text-white"><i class="fa fa-phone mr-1"></i> <?= $systemInfo->contact ?></span>
      </div>
      <div class="col-6 d-flex justify-content-end">
        <div>
          <span class="mx-2"><?= $user->email ?></span>
          <span class="mx-1"><a href="http://<?= $_SERVER['SERVER_NAME'] ?>/west/backend/nodes.php?action=logout"><i class="fa fa-power-off"></i></a></span>
        </div>
      </div>
    </div>
  </nav>
  <?php include_once("sub-navbar.php") ?>

</div>
<!-- /.navbar -->