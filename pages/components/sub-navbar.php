<!-- Sub Navbar -->
<nav class="navbar navbar-expand-md navbar-light navbar-white">
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

          $navBarLinks = array_filter(
            $links,
            fn ($val) => in_array($user->role, $val["allowedViews"]),
            ARRAY_FILTER_USE_BOTH
          );
          $leader = $isLeader ? $user : get_user_by_id($user->leader_id);
          foreach ($navBarLinks as $key => $value) :
            if (!$isNotYetAssignedGroup) {
              $query = mysqli_query(
                $conn,
                "SELECT * FROM thesis_groups WHERE group_leader_id='$leader->id' and group_number='$leader->group_number'"
              );
              $thesisGroupData = null;

              if (mysqli_num_rows($query) > 0) {
                $thesisGroupData = mysqli_fetch_object($query);
              }

              if ($value["title"] == "Document Status") {
                $toContinue = false;
                if (!hasSubmittedDocuments($leader)) {
                  $toContinue = true;
                }
                if ($thesisGroupData == null) {
                  $toContinue = true;
                }
                if (getApprovedDocument($leader) == null) {
                  $toContinue = true;
                }

                if ($toContinue) {
                  continue;
                }
              }

              if ($value["title"] == "Messages" && $thesisGroupData != null && $thesisGroupData->instructor_id == null) {
                continue;
              }
            }
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