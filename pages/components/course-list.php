<div class="card card-outline rounded-0 card-navy mt-2">
  <div class="card-header">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h4>Course List</h4>
      </div>
      <div class="col-sm-6 d-flex justify-content-end">
        <button type="button" class="btn btn-primary btn-gradient-primary" style="height: 38px;" onclick="handleOpenModal()">Add Course</button>
      </div>
    </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="course_list" class="table table-bordered table-hover">
      <thead>
        <tr class="bg-gradient-dark text-light">
          <th>Course ID</th>
          <th>Date Created</th>
          <th>Date Updated</th>
          <th>Name</th>
          <th>Short name</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $query = mysqli_query(
          $conn,
          "SELECT * FROM courses ORDER BY course_id ASC"
        );
        while ($course = mysqli_fetch_object($query)) :
        ?>
          <tr>
            <td><?= $course->course_id ?></td>
            <td><?= date("Y-m-d H:i:s", strtotime($course->date_created)) ?></td>
            <td><?= date("Y-m-d H:i:s", strtotime($course->date_updated)) ?></td>
            <td><?= $course->name ?></td>
            <td><?= $course->short_name ?></td>
            <td class="text-center">
              <button type="button" class="btn btn-warning btn-gradient-warning btn-sm m-1" onclick="handleOpenModal('<?= $course->course_id ?>')">
                Edit
              </button>
              <button type="button" class="btn btn-danger btn-gradient-danger btn-sm m-1" onclick="handleOnclickDeleteCourse('<?= $course->course_id ?>')">
                Delete
              </button>
            </td>
          </tr>
          <div class="modal fade" id="editCourse<?= $course->course_id ?>">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">
                    <i class="fa fa-edit"></i> Update Course Details
                  </h5>
                </div>
                <form method="POST" id="category-form<?= $course->course_id ?>">
                  <div class="modal-body">
                    <input type="text" name="courseId" value="<?= $course->course_id ?>" hidden readonly>
                    <input type="text" name="action" value="edit" hidden readonly>

                    <div class="form-group">
                      <label for="name" class="control-label">Name</label>
                      <input type="text" name="name" class="form-control" value="<?= $course->name ?>" required>
                    </div>

                    <div class="form-group">
                      <label for="name" class="control-label">Short name</label>
                      <input type="text" name="short_name" class="form-control" value="<?= $course->short_name ?>" required>
                    </div>
                  </div>
                  <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-primary btn-gradient-primary m-1" onclick="handleSave($(this))">Save</button>
                    <button type="button" class="btn btn-danger btn-gradient-danger m-1" data-dismiss="modal">Cancel</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </tbody>

    </table>
  </div>
  <!-- /.card-body -->
</div>
<div class="modal fade" id="addCourse">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-plus"></i> Add New Course</h5>
      </div>
      <form method="POST" id="category-form">
        <div class="modal-body">
          <input type="text" name="action" value="add" hidden readonly>

          <div class="form-group">
            <label for="name" class="control-label">Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="name" class="control-label">Short name</label>
            <input type="text" name="short_name" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer justify-content-end">
          <button type="button" class="btn btn-primary btn-gradient-primary m-1" onclick="handleSave($(this))">Save</button>
          <button type="button" class="btn btn-danger btn-gradient-danger m-1" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>