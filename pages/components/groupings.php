<div class="card card-outline card-primary rounded-0 shadow">
  <div class="card-header">
    <h3 class="card-title">
      My Group list
      <div class='mt-2'>
        <?php
        $leader = $isLeader ? $user : get_user_by_id($user->leader_id);
        $query = mysqli_query(
          $conn,
          "SELECT * FROM thesis_groups WHERE group_leader_id='$leader->id' and group_number='$leader->group_number'"
        );

        $inviteQuery = mysqli_query(
          $conn,
          "SELECT * FROM invite WHERE leader_id='$leader->id'"
        );

        $inviteData = null;
        $thesisGroupData = null;
        $hasSubmittedDocuments = hasSubmittedDocuments($leader);
        $hasSubmittedThreeDocuments = hasSubmittedThreeDocuments($leader);

        if (mysqli_num_rows($inviteQuery) > 0) {
          $inviteData = mysqli_fetch_object($inviteQuery);
          $inviteAdviser = get_user_by_id($inviteData->adviser_id);

          $adviserDisplay = "<h6> Adviser: <strong>" . ucwords("$inviteAdviser->first_name " . ($inviteAdviser->middle_name != null ? $inviteAdviser->middle_name[0] . "." : "") . " $inviteAdviser->last_name") . "</strong> <em>%status%</em> </h6>";

          if ($inviteData->status == "PENDING") {
            $adviserDisplay = str_replace("%status%", "(PENDING INVITATION)", $adviserDisplay);
          } else if ($inviteData->status == "DECLINED") {
            $adviserDisplay = str_replace("%status%", "(INVITATION DECLINED)", $adviserDisplay);
          } else {
            $adviserDisplay = str_replace("<em>%status%</em>", "", $adviserDisplay);
          }

          echo $adviserDisplay;
        } else {
          echo "<h6> Adviser: <em>No assigned adviser.</em> </h6>";
        }

        if (mysqli_num_rows($query) > 0) {

          $thesisGroupData = mysqli_fetch_object($query);

          if ($thesisGroupData->instructor_id != null) {
            $instructor = get_user_by_id($thesisGroupData->instructor_id);
            echo "<h6> Instructor: <strong>" . ucwords("$instructor->first_name " . ($instructor->middle_name != null ? $instructor->middle_name[0] . "." : "") . " $instructor->last_name") . "</strong> </h6>";
          } else {
            echo "<h6> Instructor: <em>No assigned instructor.</em> </h6>";
          }

          if ($thesisGroupData->panel_ids != null) {
        ?>
            <h6>Panel:
              <a data-toggle="modal" data-target="#assignedPanels" class="btn btn-link">
                View assigned panels
              </a>
            </h6>

            <div class="modal fade" id="assignedPanels">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Assigned Panels</h5>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                      <label class="control-label">Panels</label>
                      <?php
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
                      ?>
                    </div>
                  </div>
                  <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-secondary btn-gradient-secondary m-1" data-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>
        <?php
          } else {
            echo "<h6> Panel: <em>No assigned panel.</em> </h6>";
          }
        } else {
          echo "<h6> Instructor: <em>No assigned instructor.</em> </h6>";
          echo "<h6> Panel: <em>No assigned panel.</em> </h6>";
        }
        ?>

      </div>
    </h3>

    <div class="card-tools">
      <?php if ($inviteData == null) : ?>

        <button type="button" id="btnSendInvite" class="btn btn-primary btn-gradient-primary btn-sm">
          <i class="fa fa-paper-plane"></i>
          Send adviser invite
        </button>

      <?php elseif ($inviteData != null && $inviteData->status == "DECLINED") : ?>

        <button type="button" id="btnSendInvite" class="btn btn-warning btn-gradient-warning btn-sm">
          <i class="fa fa-user-times"></i>
          Change Adviser
          <input type="text" value="<?= $inviteData->adviser_id ?>" hidden readonly>
        </button>

      <?php elseif ($inviteData != null && $inviteData->status == "PENDING") : ?>

        <button type="button" id="btnCancelInvite" class="btn btn-danger btn-gradient-danger btn-sm">
          <i class="fa fa-user-times"></i>
          Cancel Invite
        </button>

      <?php endif; ?>

      <?php if ($thesisGroupData != null && $thesisGroupData->instructor_id != null  && !$hasSubmittedThreeDocuments) : ?>

        <button type="button" id="btnSubmitDocuments" onclick="return window.location.href = './submit-documents'" class="btn btn-success btn-sm">
          <i class="fa fa-check"></i>
          Submit Documents
        </button>
      <?php endif; ?>

    </div>

  </div>
  <div class="card-body">
    <div class="container-fluid">
      <table id="groupings-table" class="table table-bordered table-hover table-striped">
        <thead>
          <tr class="bg-gradient-dark text-light">
            <th>#</th>
            <th>Date Created</th>
            <th>Student roll</th>
            <th>Role</th>
            <th>Name</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="text-center">1</td>
            <td><?= date("Y-m-d H:i", strtotime($leader->date_added)) ?></td>
            <td>
              <?= $leader->roll ?>
            </td>
            <td>Leader</td>
            <td>
              <p class="m-0 truncate-1"><?= ucwords("$leader->last_name, $leader->first_name $middleName") ?></p>
            </td>
            <td align="center">
              <a href="<?= "$SERVER_NAME/pages/profile?page=manage_profile" ?>" class="btn btn-flat btn-default btn-sm border"><i class="fa fa-eye"></i> View</a>
            </td>
          </tr>
          <?php
          $count = 2;
          $memberData = json_decode(getMemberData($leader->group_number, $leader->id));
          foreach ($memberData as $member) :
            $memberMiddleName = $member->middle_name != null ? $member->middle_name[0] : "";
          ?>
            <tr>
              <td class="text-center"><?= $count ?></td>
              <td><?= date("Y-m-d H:i", strtotime($member->date_added)) ?></td>
              <td>
                <?= $member->roll ?>
              </td>
              <td>Member</td>
              <td>
                <p class="m-0 truncate-1"><?= ucwords("$member->last_name, $member->first_name $memberMiddleName") ?></p>
              </td>
              <td align="center">
                <a href="<?= "$SERVER_NAME/pages/student/my-groupings?page=member_profile&&u=$member->username" ?>" class="btn btn-flat btn-default btn-sm border"><i class="fa fa-eye"></i> View</a>
              </td>
            </tr>
          <?php
          endforeach;
          $count++;
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>