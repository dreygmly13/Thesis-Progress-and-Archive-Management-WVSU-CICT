<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-image: url('http://<?= $_SERVER['SERVER_NAME'] ?>/west/public/side-bg.jpg') !important; background-repeat: no-repeat; background-size: cover; background-position: center center;">
  <!-- Brand Logo -->
  <a href="./" class="brand-link" style="overflow: auto; background-color: #343a40;filter: blur(0)">
    <img src="<?= $SERVER_NAME . $systemInfo->logo ?>" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">
      <!-- nav title -->
    </span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar" style="filter: brightness(150%);">

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <?php
        include_once("links.php");

        $navBarLinks = array_filter(
          $links,
          fn ($val) => in_array($user->role, $val["allowedViews"]),
          ARRAY_FILTER_USE_BOTH
        );

        foreach ($navBarLinks as $key => $value) :
          $something = $value["url"] == str_replace(".php", "", $self);
        ?>
          <li class="nav-item">
            <a href="<?= $value["url"] ?>" class="nav-link <?= $value["url"] == str_replace(".php", "", $self) ? "active" : ""  ?>">
              <i class="nav-icon fas fa-<?= $value["config"]["icon"] ?>"></i>
              <p>
                <?= $value["title"] ?>
                <!-- <span class="right badge badge-danger">New</span> -->
              </p>
            </a>
          </li>
        <?php
        endforeach;
        ?>

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>