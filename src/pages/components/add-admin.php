<div class="card card-outline card-primary shadow rounded-0 mt-2 container">
  <div class="card-header rounded-0">
    <h5 class="card-title">Add Admin </h5>
  </div>
  <div class="card-body rounded-0">
    <div class="container-fluid">
      <form method="POST" id="add-admin" enctype="multipart/form-data">
        <input type="number" name="group_number" value="<?= $user->group_number ?>" hidden readonly>
        <div class="row">
          <div class="col-lg-4">
            <div class="form-group">
              <label class="control-label text-navy">First name</label>
              <input type="text" name="fname" class="form-control" required>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label class="control-label text-navy">MiddleName</label>
              <input type="text" name="mname" class="form-control">
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label class="control-label text-navy">LastName</label>
              <input type="text" name="lname" class="form-control" required>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6">
            <div class="form-group ">
              <label class="control-label text-navy">Role</label>
              <select name="role" id="inputRole" class="form-control">
                <option value="" selected disabled>-- select admin role --</option>
                <?php
                foreach ($ADMIN_ROLES as $role) :
                ?>
                  <option value="<?= $role ?>"><?= ucwords($role) ?></option>
                <?php
                endforeach;
                ?>
              </select>
            </div>
            <div class="input-group" id="courseYearSection" style="display: none;">
              <div id="divCourses"></div>
              <div>
                <div class="form-group col-12">
                  <div class="d-flex justify-content-between align-items-center mt-2">
                    <label>
                      Course:
                    </label>
                  </div>
                  <select name="courseId[]" class="selectCourse form-control" required>
                    <option value="">-- select course --</option>
                    <?php
                    $query = mysqli_query(
                      $conn,
                      "SELECT * FROM courses"
                    );
                    while ($course = mysqli_fetch_object($query)) :
                    ?>
                      <option value="<?= $course->course_id ?>"><?= "($course->short_name) " . $course->name ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
                <div class="form-group col-12">
                  <label>
                    Year & Sections <br>
                    <small>
                      Please separate year & sections by comma(,)
                    </small>
                  </label>
                  <input type="text" name="sections[]" placeholder="eg. 4-A, 4-B" style="text-transform:uppercase" class="sections form-control" required>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-end">
                <button type="button" class="btn btn-primary btn-sm mt-2" onclick="handleAddCourse()">
                  Add Course
                </button>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label text-navy">Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>

          </div>

          <div class="col-lg-6">
            <div class="form-group">
              <label for="img" class="control-label text-muted">Choose Image</label>
              <input type="file" name="avatar" class="form-control border-0" accept="image/png,image/jpeg" onchange="displayImg(this,$(this))">
            </div>
            <div class="form-group text-center">
              <img src="<?= $SERVER_NAME ?>/assets/dist/img/no-image-available.png" alt="My Avatar" id="cimg" class="img-fluid student-img bg-gradient-dark border" style="width: 217px; height: 217px;">
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-lg-12">
            <div class="form-group text-center">
              <button type="submit" class="btn btn-default bg-navy m-1"> Add</button>
              <button type="button" onclick="return window.history.back()" class="btn btn-danger btn-gradient-danger m-1"> Cancel</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>