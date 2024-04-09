<div class="card mt-2">
  <div class="card-header">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h4>Admin List</h4>
      </div>
      <div class="col-sm-6 d-flex justify-content-end">
        <button type="button" class="btn btn-primary btn-gradient-primary" style="height: 38px;" onclick="handleAddAdmin()">Add Admin</button>
      </div>
    </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="admin_list" class="table table-bordered table-hover">
      <thead>
        <tr class="bg-gradient-dark text-light">
          <th>Date Added</th>
          <th>Date Updated</th>
          <th>Name</th>
          <th>Handled sections</th>
          <th>Email</th>
          <th>Role</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $query = mysqli_query(
          $conn,
          "SELECT * FROM users WHERE `role` != 'student' and id != '$user->id' ORDER BY id DESC"
        );
        while ($admin = mysqli_fetch_object($query)) :
          $adminName = ucwords("$admin->first_name " . ($admin->middle_name != null ? $admin->middle_name[0] . "." : "") . " $admin->last_name");
        ?>
          <tr>
            <td><?= date("Y-m-d H:i", strtotime($admin->date_added)) ?></td>
            <td><?= date("Y-m-d H:i", strtotime($admin->date_updated)) ?></td>
            <td>
              <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                <div class="mr-1">
                  <img src="<?= $admin->avatar != null ? $SERVER_NAME . $admin->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                </div>
                <div>
                  <?= ucwords("$admin->first_name " . ($admin->middle_name != null ? $admin->middle_name[0] . "." : "") . " $admin->last_name") ?>
                </div>
              </div>
            </td>
            <td>
              <?php
              if ($admin->role == "instructor") :
                $handledSections = mysqli_query(
                  $conn,
                  "SELECT * FROM instructor_sections WHERE instructor_id='$admin->id'"
                );
                if (mysqli_num_rows($handledSections) > 0) :
                  $sectionData = mysqli_fetch_object($handledSections);
                  $sections = json_decode($sectionData->sections, true);
                  foreach ($sections as $section) :
              ?>
                    <div>
                      <div>
                        <label>
                          <?= $section["name"] ?><br>
                        </label>
                      </div>
                      <?php
                      foreach ($section["sections"] as $sectionName) :
                      ?>
                        <span class="badge badge-primary rounded-pill px-4 m-1" style="font-size: 18px">
                          <em><?= $sectionName ?></em>
                        </span>
                      <?php
                      endforeach;
                      ?>
                    </div>
                  <?php
                  endforeach;
                else :
                  ?>
                  ---
                <?php
                endif;
              else : ?>
                ---
              <?php endif; ?>
            </td>
            <td><?= $admin->email ?></td>
            <td><?= ucwords($admin->role) ?></td>
            <?php
            $thesisGroupQ = mysqli_query(
              $conn,
              "SELECT * FROM thesis_groups WHERE panel_ids is not NULL"
            );
            $disabled = "";

            while ($thesisGroup = mysqli_fetch_object($thesisGroupQ)) {
              $panelIds = $thesisGroup->panel_ids;
              if (in_array($admin->id, json_decode($panelIds, true))) {
                $disabled = "disabled";
                break;
              }
            }
            ?>
            <td class="text-center">
              <button type="button" class="btn btn-warning btn-gradient-warning m-1" onclick="handleOnclickEditAdmin('<?= $admin->username ?>')">
                Edit
              </button>
              <button type="button" class="btn btn-danger btn-gradient-danger m-1" onclick="handleOnclickDeleteAdmin('<?= $admin->id ?>')" <?= $disabled ?>>
                Delete
              </button>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>

    </table>
  </div>
  <!-- /.card-body -->
</div>