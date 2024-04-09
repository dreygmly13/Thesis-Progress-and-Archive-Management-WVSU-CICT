<div class="card card-outline rounded-0 card-navy mt-2">
  <div class="card-header">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h4>Unassigned students</h4>
      </div>
      <div class="col-sm-6 d-flex justify-content-end">
        <button class="btn btn-primary" style="height: 40px;" onclick="setGroupNumber()">Set group number</button>
      </div>
    </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="student_list" class="table table-bordered table-hover">
      <thead>
        <tr class="bg-gradient-dark text-light">
          <th></th>
          <td></td>
          <th>Roll</th>
          <th>Student Name</th>
          <th>Email</th>
          <th>Section</th>
          <th>Course</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $handledSections = getInstructorHandledSections($user->id);

        $courses = array();

        foreach ($handledSections as $course) {
          array_push($courses, $course["id"]);
        }
        $courses = (implode('\', \'', $courses));
        $finCourse = "'" . $courses . "'";

        $sections = array();

        foreach ($handledSections as $index => $value) {
          foreach ($value["sections"] as $s) {
            array_push($sections, $s);
          }
        }
        $sections = (implode('\', \'', array_unique($sections)));
        $fin = "'" . $sections . "'";

        $query = mysqli_query(
          $conn,
          "SELECT * FROM users WHERE `role`='student' and group_number is NULL and course_id in(" . $finCourse . ") and year_and_section in(" . $fin . ")"
        );

        while ($student = mysqli_fetch_object($query)) :
          $studentsData = get_user_by_id($student->id);
          $studentsName = ucwords("$studentsData->first_name " . ($studentsData->middle_name != null ? $studentsData->middle_name[0] . "." : "") . " $studentsData->last_name");
        ?>
          <tr>
            <td></td>
            <td><?= $student->id ?></td>
            <td><?= $studentsData->roll ?></td>
            <td>
              <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                <div class="mr-1">
                  <img src="<?= $studentsData->avatar != null ? $SERVER_NAME . $studentsData->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                </div>
                <div>
                  <?= $studentsName ?>
                </div>
              </div>
            </td>
            <td><?= $studentsData->email ?></td>
            <td><?= $studentsData->year_and_section ?></td>
            <td><?= getCourseData($studentsData->course_id)->name ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>

    </table>
  </div>
  <!-- /.card-body -->
</div>