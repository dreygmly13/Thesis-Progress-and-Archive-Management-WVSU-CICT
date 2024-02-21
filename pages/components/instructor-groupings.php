<div class="card card-outline rounded-0 card-navy mt-2">
  <div class="card-header">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h4>Group List</h4>
      </div>

    </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="student_list" class="table table-bordered table-hover">
      <thead>
        <tr class="bg-gradient-dark text-light">
          <th>Group#</th>
          <th>Date created</th>
          <th>Date updated</th>
          <th>Leader</th>
          <th>Members</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $query = mysqli_query(
          $conn,
          "SELECT * FROM thesis_groups WHERE instructor_id='$user->id' ORDER BY `status` ASC"
        );
        while ($groups = mysqli_fetch_object($query)) :
          $leader = get_user_by_id($groups->group_leader_id);
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
            <td><?= date("Y-m-d H:i:s", strtotime($groups->date_created)) ?></td>
            <td><?= date("Y-m-d H:i:s", strtotime($groups->date_updated)) ?></td>
            <td>
              <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                <div class="mr-1">
                  <img src="<?= $leader->avatar != null ? $SERVER_NAME . $leader->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                </div>
                <div>
                  <?= $leaderName ?>
                </div>
              </div>
            </td>
            <td>
              <?php
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
              <?php endforeach; ?>
            </td>
            <td class="text-center py-1 px-2">
              <?php if ($groups->status == "1") : ?>
                <span class="rounded-pill badge badge-success bg-gradient-success px-3" style="font-size: 18px;">
                  Approved
                </span>
              <?php else : ?>
                <span class="rounded-pill badge badge-danger bg-gradient-danger px-3" style="font-size: 18px;">
                  Not yet approve
                </span>
              <?php endif; ?>
            </td>
            <td class="text-center py-1 px-2">
              <?php
              $thesisGroupId = $hasSubmittedGroup && $thesisGroupData != null ? $thesisGroupData->id : null;
              ?>
              <button type="button" class="btn btn-primary btn-gradient-primary m-1" onclick="handleApproved('<?= $thesisGroupId ?>')" <?= $groups->status == "1" ? "disabled" : "" ?>>
                Approved
              </button>
              <button type="button" class="btn btn-primary btn-gradient-primary m-1" onclick="return window.location.href = './message?i=<?= $leader->id ?>'">
                <i class="fa fa-paper-plane"></i>
                Chat
              </button>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>

    </table>
  </div>
  <!-- /.card-body -->
</div>