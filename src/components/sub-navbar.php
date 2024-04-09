<!-- Sub Navbar -->
<nav class="navbar navbar-expand-md nav-white bg-white" style="position:fixed; top: 3.5rem; width: 100%;z-index: 999;">
  <div class="container-fluid">
    <a href="./" class="navbar-brand">
      <img src="<?= "http://{$_SERVER['SERVER_NAME']}/west" ?>/public/logo-1657357283.png" alt="Site Logo" class="brand-image img-circle elevation-3" style="opacity: .8;height: 33px;">
      <span class="ml-2" style="color: black">WVSU</span>
    </a>

    <button class="btn btn-default navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <i class="fa fa-bars"></i>
    </button>

    <div class="collapse navbar-collapse order-3" id="navbarCollapse">
      <!-- Left navbar links -->

      <div class="container">
        <ul class="navbar-nav linkNav">
          <?php
          include_once("links.php");
          $self = "http://{$_SERVER['SERVER_NAME']}{$_SERVER['PHP_SELF']}";

          $navBarLinks = array_filter(
            $links,
            fn ($val) => $val["config"] == "sub_navbar",
            ARRAY_FILTER_USE_BOTH
          );
          foreach ($navBarLinks as $key => $value) :
          ?>
            <li class="nav-item">
              <a class="nav-link <?= $value["url"] == str_replace(".php", "", $self) ? "active" : ""  ?>" style="color: black" href="<?= $value["url"] ?>"><?= $value["title"] ?></a>
            </li>
          <?php
          endforeach;
          ?>
        </ul>

        <ul class="navbar-nav divSearch">
          <li class="nav-item dropdown">
            <a id="searchNav" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle" style="color: black;">
              <i class="fa fa-search"></i>
            </a>

            <ul aria-labelledby="searchNav" class="dropdown-menu border-0 shadow p-1" style="left: 0px; right: inherit; width: 300px">
              <div class="search-field">
                <input type="search" id="searchInput" class="form-control rounded-0" placeholder="Search..." value="">
              </div>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>
<!-- /.Sub Navbar -->