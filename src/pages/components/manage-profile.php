<div class="card card-outline card-primary shadow rounded-0">
  <div class="card-header rounded-0">
    <h5 class="card-title">Update Details</h5>
  </div>
  <div class="card-body rounded-0">
    <div class="container-fluid">
      <form method="POST" id="update-form" enctype="multipart/form-data">
        <input type="text" name="userId" value="<?= $user->id ?>" hidden readonly>
        <input type="text" name="role" value="<?= $user->role ?>" hidden readonly>
        <div class="row">
          <div class="col-lg-4">
            <div class="form-group">
              <label class="control-label text-navy">First name</label>
              <input type="text" name="fname" class="form-control form-control-border" value="<?= $user->first_name ?>" required>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label class="control-label text-navy">Middle name</label>
              <input type="text" name="mname" class="form-control form-control-border" value="<?= $user->middle_name ?>">
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label class="control-label text-navy">Last name</label>
              <input type="text" name="lname" class="form-control form-control-border" value="<?= $user->last_name ?>" required>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6">
            <div class="form-group">
              <label class="control-label text-navy">Roll</label>
              <input type="text" name="roll" class="form-control form-control-border" value="<?= $user->roll ?>" required>
            </div>
            <div class="form-group">
              <label class="control-label text-navy">Group number</label>
              <input type="number" name="group_number" class="form-control form-control-border" value="<?= $user->group_number ?>" required readonly>
            </div>
            <div class="form-group mb-0">
              <label class="col-form-label">
                School year
              </label>
            </div>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text" style="background-color: transparent; border: 0; border-bottom: 1px solid #ced4da;">S.Y.</span>
              </div>
              <input type="text" class="form-control form-control-border" name="sy" value="<?= str_replace("SY: ", "", $user->school_year) ?>" required>
            </div>
            <div class="form-group">
              <label class="control-label text-navy">Year and Section</label>
              <div class="row">
                <?php
                $year_and_section = $user->year_and_section;
                $year = explode("-", $year_and_section)[0];
                $section = explode("-", $year_and_section)[1];

                ?>
                <div class="col-md-6">
                  <input type="number" name="year" class="form-control form-control-border" value="<?= $year ?>" required>
                </div>
                <div class="col-md-6">
                  <input type="text" name="section" class="form-control form-control-border" value="<?= $section ?>" required>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-form-label">
                Course
              </label>
              <select name="courseId" class="form-control form-control-border mr-3">
                <option value="">-- select course --</option>
                <?php
                $query = mysqli_query(
                  $conn,
                  "SELECT * FROM courses"
                );
                while ($course = mysqli_fetch_object($query)) :
                  $selected = $course->course_id == $user->course_id ? "selected" : "";
                ?>
                  <option value="<?= $course->course_id ?>" <?= $selected  ?>><?= $course->name ?></option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="form-group">
              <label for="email" class="control-label text-navy">Email</label>
              <input type="email" name="email" id="inputEmail" class="form-control form-control-border" required value="<?= $user->email ?>">
              <div class="invalid-feedback" style="padding-left: 5px;">
                <p id="inputEmailError" style="margin-bottom: 0;"></p>
              </div>
            </div>
            <div class="form-group">
              <label for="password" class="control-label text-navy">New Password</label>
              <input type="password" name="password" id="password" placeholder="Password" class="form-control form-control-border">
            </div>

            <div class="form-group">
              <label for="cpassword" class="control-label text-navy">Confirm New Password</label>
              <input type="password" name="cpassword" placeholder="Confirm Password" class="form-control form-control-border">
            </div>

            <small class="text-muted">Leave the New Password and Confirm New Password Blank if you don't wish to change your password.</small>

            <div class="form-group mt-4">
              <label for="oldpassword">Please Enter your Current Password</label>
              <input type="password" name="oldpassword" id="oldpassword" placeholder="Current Password" class="form-control form-control-border">
            </div>
          </div>

          <div class="col-lg-6">
            <div class="form-group">
              <label for="img" class="control-label text-muted">Choose Image</label>
              <input type="file" name="avatar" class="form-control border-0" accept="image/png,image/jpeg" onchange="displayImg(this,$(this))">
            </div>
            <div class="form-group text-center">
              <img src="<?= $user->avatar == null ? $SERVER_NAME . "/public/default.png" : $SERVER_NAME . $user->avatar ?>" alt="My Avatar" id="cimg" class="img-fluid student-img" style="width: 217px; height: 217px;">
            </div>
          </div>
        </div>
        <hr>
        <div class="row">

        </div>
        <div class="row">
          <div class="col-lg-12">
            <div class="form-group text-center">
              <button type="submit" class="btn btn-default bg-navy m-1" id="updateBtn"> Update</button>
              <button type="button" onclick="return window.history.back()" class="btn btn-danger btn-gradient-danger m-1"> Cancel</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>