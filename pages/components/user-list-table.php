<div class="card card-outline rounded-0 card-navy mt-2">
  <div class="card-header">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h4>Student List</h4>
      </div>

    </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="student_list" class="table table-bordered table-hover">
      <thead>
        <tr class="bg-gradient-dark text-light">
          <th>Group#</th>
          <th>Group list</th>
          <th>Instructor</th>
          <th>Panel</th>
          <th>Adviser</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $query = mysqli_query(
          $conn,
          "SELECT * FROM users WHERE `role` = 'student' and isLeader = '1' ORDER BY id DESC"
        );
        while ($leader = mysqli_fetch_object($query)) :
          $leaderName = ucwords("$leader->first_name " . ($leader->middle_name != null ? $leader->middle_name[0] . "." : "") . " $leader->last_name");
          $memberData = json_decode(getMemberData($leader->group_number, $leader->id));

          $thesisGroupQuery = mysqli_query(
            $conn,
            "SELECT * FROM thesis_groups WHERE group_leader_id='$leader->id' and group_number='$leader->group_number'"
          );

          $hasSubmittedGroup = mysqli_num_rows($thesisGroupQuery) > 0;
          $thesisGroupData = $hasSubmittedGroup ? mysqli_fetch_object($thesisGroupQuery) : null;
        ?>
          <tr>
            <td><?= $leader->group_number ?></td>
            <td>
              <h5>Leader:</h5>
              <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                <div class="mr-1">
                  <img src="<?= $leader->avatar != null ? $SERVER_NAME . $leader->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                </div>
                <div>
                  <?= $leaderName ?>
                </div>
              </div>
              <?php
              if (count($memberData) > 0) :
                echo "<h5>Members:</h5>";
                foreach ($memberData as $member) :
                  $memberName = ucwords("$member->first_name " . ($member->middle_name != null ? $member->middle_name[0] . "." : "") . " $member->last_name");
              ?>
                  <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                    <div class="mr-1">
                      <img src="<?= $member->avatar != null ? $SERVER_NAME . $member->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                    </div>
                    <div>
                      <?= $memberName ?>
                    </div>
                  </div>
              <?php endforeach;
              endif; ?>
            </td>
            <td>
              <?php
              if ($hasSubmittedGroup && $thesisGroupData != null) {
                if ($thesisGroupData->instructor_id != null) :
                  $instructor = get_user_by_id($thesisGroupData->instructor_id);
                  $instructorName = ucwords("$instructor->first_name " . ($instructor->middle_name != null ? $instructor->middle_name[0] . "." : "") . " $instructor->last_name");
              ?>
                  <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                    <div class="mr-1">
                      <img src="<?= $instructor->avatar != null ? $SERVER_NAME . $instructor->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                    </div>
                    <div>
                      <h6>
                        <strong>
                          <?= $instructorName ?>
                        </strong>
                      </h6>
                    </div>
                  </div>
              <?php
                else :
                  echo "<h6><em>No assigned instructor.</em> </h6>";
                endif;
              } else {
                echo "<h6><em>Not yet submitted group to instructor</em> </h6>";
              }
              ?>
            </td>
            <td>
              <?php
              if ($hasSubmittedGroup && $thesisGroupData != null) {
                if ($thesisGroupData->panel_ids != null) :
                  foreach (json_decode($thesisGroupData->panel_ids) as $panel_id) :
                    $panel = get_user_by_id($panel_id);
                    $panelName = ucwords("$panel->first_name " . ($panel->middle_name != null ? $panel->middle_name[0] . "." : "") . " $panel->last_name");
              ?>
                    <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                      <div class="mr-1">
                        <img src="<?= $panel->avatar != null ? $SERVER_NAME . $panel->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                      </div>
                      <div>
                        <h6>
                          <strong>
                            <?= $panelName ?>
                          </strong>
                        </h6>
                      </div>
                    </div>
              <?php
                  endforeach;
                else :
                  echo "<h6><em>No assigned panel.</em> </h6>";
                endif;
              } else {
                echo "<h6><em>Not yet submitted group to instructor</em> </h6>";
              }
              ?>
            </td>
            <td>
              <?php
              if ($hasSubmittedGroup && $thesisGroupData != null) {
                if ($thesisGroupData->adviser_id != null) :
                  $adviser = get_user_by_id($thesisGroupData->adviser_id);
                  $adviserName = ucwords("$adviser->first_name " . ($adviser->middle_name != null ? $adviser->middle_name[0] . "." : "") . " $adviser->last_name");
              ?>
                  <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                    <div class="mr-1">
                      <img src="<?= $adviser->avatar != null ? $SERVER_NAME . $adviser->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                    </div>
                    <div>
                      <h6>
                        <strong>
                          <?= $adviserName ?>
                        </strong>
                      </h6>
                    </div>
                  </div>
              <?php
                else :
                  echo "<h6><em>No adviser assigned yet.</em> </h6>";
                endif;
              } else {
                echo "<h6><em>Not yet submitted group to instructor</em> </h6>";
              }
              ?>
            </td>
            <td style="width: 140px;">
              <?php
              $thesisGroupId = $hasSubmittedGroup && $thesisGroupData != null ? $thesisGroupData->id : null;
              ?>
              <button type="button" class="btn btn-primary btn-gradient-primary" onclick="assignPanelClick('<?= $thesisGroupId ?>')" <?= $hasSubmittedGroup && $thesisGroupData != null ?>>
                <?= $thesisGroupData->panel_ids != null ? "Update panels" : "Assign Panel" ?>
              </button>
            </td>
          </tr>
          <div class="modal fade" id="assignPanel<?= $thesisGroupId ?>">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Assign Panel</h5>
                </div>
                <form method="POST" id="form-assignPanel<?= $thesisGroupId ?>">
                  <div class="modal-body">
                    <input type="text" name="groupId" value="<?= $thesisGroupId ?>" hidden readonly>

                    <div class="form-group">
                      <label class="control-label">Panel</label>
                      <select class="select2" name="panel_ids[]" multiple="multiple" data-placeholder="Select panel" style="width: 100%;">
                        <?php
                        $panels = getAllPanel();
                        foreach ($panels as $panel) :
                        ?>
                          <option value="<?= $panel->id ?>" <?= $thesisGroupData->panel_ids != null && in_array($panel->id, json_decode($thesisGroupData->panel_ids)) ? "selected" : "" ?>><?= ucwords("$panel->first_name " . ($panel->middle_name != null ? $panel->middle_name[0] . "." : "") . " $panel->last_name") ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-primary btn-gradient-primary m-1" onclick="handleAssignPanel('<?= $thesisGroupId ?>', $(this))">Save</button>
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