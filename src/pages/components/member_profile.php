<?php $member = get_user_by_username($_GET["u"]); ?>

<div class="card card-outline card-primary shadow rounded-0">
  <form method="POST" id="update-member" enctype="multipart/form-data">

    <div class="card-header">
      <h5 class="card-title"><?= ucwords("$member->last_name's") ?> Profile</h5>
      <div class="card-tools">
        <button type="button" onclick="return window.history.back()" class="btn btn-default border btn-sm m-1">
          <i class="fa fa-angle-left"></i>
          Back to List
        </button>
      </div>
    </div>
    <div class="card-body rounded-0">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-4">
            <div class="form-group">
              <label class="control-label text-navy">First name</label>
              <input type="text" class="form-control form-control-border" value="<?= $member->first_name ?>" readonly>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label class="control-label text-navy">MiddleName</label>
              <input type="text" class="form-control form-control-border" value="<?= $member->middle_name ?>" readonly>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label class="control-label text-navy">LastName</label>
              <input type="text" class="form-control form-control-border" value="<?= $member->last_name ?>" readonly>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6">
            <div class="form-group">
              <label class="control-label text-navy">Roll</label>
              <input type="text" class="form-control form-control-border" value="<?= $member->roll ?>" readonly>
            </div>

            <div class="form-group">
              <label class="control-label text-navy">Email</label>
              <input type="email" class="form-control form-control-border" value="<?= $member->email ?>" readonly>
            </div>

            <div class="form-group">
              <label class="control-label text-navy">Group number</label>
              <input type="number" class="form-control form-control-border" value="<?= $member->group_number ?>" readonly>
            </div>
            <div class="form-group mb-0">
              <label class="col-form-label">
                School year
              </label>
            </div>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text" style="border: 0; border-bottom: 1px solid #ced4da;">S.Y.</span>
              </div>
              <input type="text" class="form-control form-control-border" value="<?= str_replace("SY: ", "", $member->school_year) ?>" readonly>
            </div>
            <div class="form-group">
              <label class="control-label text-navy">Year and Section</label>
              <div class="row">
                <?php
                $year_and_section = $member->year_and_section;
                $year = explode("-", $year_and_section)[0];
                $section = explode("-", $year_and_section)[1];

                ?>
                <div class="col-md-6">
                  <input type="number" class="form-control form-control-border" value="<?= $year ?>" readonly>
                </div>
                <div class="col-md-6">
                  <input type="text" class="form-control form-control-border" value="<?= $section ?>" readonly>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-form-label">
                Course
              </label>
              <input type="text" class="form-control form-control-border" value="<?= getCourseData($member->course_id)->name ?>" readonly>
            </div>

          </div>

          <div class="col-lg-6">

            <div class="form-group text-center mt-4">
              <img src="<?= $member->avatar == null ? $SERVER_NAME . "/public/default.png" : $SERVER_NAME . $member->avatar ?>" alt="My Avatar" id="cimg" class="img-fluid student-img" style="width: 217px; height: 217px;">
            </div>
          </div>
        </div>
        <hr>
      </div>
    </div>
  </form>

</div>