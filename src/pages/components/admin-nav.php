<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <span class="nav-link" style="color: black">
        <?= ucwords("thesis " . $user->role) ?>
      </span>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">

    <li class="nav-item">
      <div class="btn-group nav-link" style="display: flex;align-items: center;">
        <button type="button" class="btn btn-default dropdown-toggle" style="background-color: transparent;border-color: transparent;" data-toggle="dropdown" aria-expanded="false">
          <span>
            <img src="<?= $user->avatar != null ? $SERVER_NAME . $user->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 27px; height: 27px;" alt="User Image">
          </span>
          <span class="ml-3">
            <?= ucwords("$user->first_name " . ($user->middle_name != null ? $user->middle_name[0] . "." : "") . " $user->last_name") ?>
          </span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div class="dropdown-menu" role="menu" style="left: 0px; right: inherit;">
          <a class="dropdown-item" href="../components/admin-profile.php?u=<?= $user->username ?>">
            <span class="fa fa-user"></span>
            My Account
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="http://<?= $_SERVER['SERVER_NAME'] ?>/west/backend/nodes.php?action=logout">
            <span class="fas fa-sign-out-alt"></span>
            Logout
          </a>
        </div>
      </div>
    </li>
  </ul>
</nav>