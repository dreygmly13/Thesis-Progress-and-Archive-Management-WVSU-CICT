<?php
if (isset($_GET['rating']) && isset($_GET['leaderId']) && isset($_GET['groupNumber'])) :
?>
  <div class="card card-outline rounded-0 card-navy mt-2">
    <div class="card-header">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h4>
            <strong>Group #<?= $_GET["groupNumber"] ?></strong>
            panel comments and ratings
          </h4>
        </div>
        <div class="col-sm-6 d-flex justify-content-end">
          <button type="button" onclick="return window.history.back()" class="btn btn-secondary btn-gradient-secondary m-1">
            Go back
          </button>
        </div>

      </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      <div class="row mt-2 justify-content-start">
        <?php
        $leader = get_user_by_id($_GET['leaderId']);
        $document = getApprovedDocument($leader);
        $panelsQ = mysqli_query(
          $conn,
          "SELECT * FROM thesis_groups WHERE group_leader_id='$_GET[leaderId]' and group_number='$_GET[groupNumber]'"
        );
        $panelIds = json_decode(mysqli_fetch_object($panelsQ)->panel_ids);
        foreach ($panelIds as $panelId) :
          $panel = get_user_by_id($panelId);
        ?>
          <div class="col-md-4 col-sm-12">
            <div class="card">
              <div class="card-header bg-gradient-dark text-light">
                <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                  <div class="mr-1">
                    <img src="<?= $panel->avatar != null ? $SERVER_NAME . $panel->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                  </div>
                  <div>
                    <?= ucwords("$panel->first_name " . ($panel->middle_name != null ? $panel->middle_name[0] . "." : "") . " $panel->last_name") ?>
                  </div>
                </div>
              </div>
              <div class="card-body" style="background-color: #f2f2f2;">
                <?php
                $feedbacks = getPanelRating($panelId, $document->id);
                foreach ($feedbacks as $feedbackData) :
                  $isConcept = $feedbackData->rating_type == "concept" ? true : false;
                  $isFinal = $feedbackData->rating_type == "final" ? true : false;
                ?>
                  <blockquote class="blockquote my-2 mx-0" style="font-size: 14px; overflow: hidden;">
                    <?php if (!$isConcept && !$isFinal) :
                      if ($feedbackData->action == "Approved") : ?>
                        <span class="badge badge-success rounded-pill px-2" style="float:right;font-size: 14px">
                          Approved
                        </span>
                      <?php else : ?>
                        <span class="badge badge-danger rounded-pill px-2" style="float:right;font-size: 14px">
                          Disapproved
                        </span>
                    <?php endif;
                    endif; ?>
                    <span>
                      &#8226;
                      <strong>
                        <?= panelNameType($feedbackData->rating_type) ?>
                      </strong>
                    </span>
                    <p class="mt-3">
                      <button type="button" class="btn btn-link" style="font-size: 14px;" onclick="handleOpenRatingModal('feedback<?= $feedbackData->rating_id ?>')">
                        Comments/Suggestions and Ratings
                      </button>
                    </p>
                  </blockquote>

                  <div class="modal fade" id="feedback<?= $feedbackData->rating_id ?>">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">
                            <?= panelNameType($feedbackData->rating_type) ?>
                          </h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" style="font-size: 30px">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <div class="row">
                            <div class="col-12">
                              <?php if (!$isConcept && !$isFinal) :
                                echo "Action taken:";
                                if ($feedbackData->action == "Approved") : ?>
                                  <span class="badge badge-success rounded-pill px-2" style="float:right;font-size: 14px">
                                    Approved
                                  </span>
                                <?php else : ?>
                                  <span class="badge badge-danger rounded-pill px-2" style="float:right;font-size: 14px">
                                    Disapproved
                                  </span>
                              <?php endif;
                              endif; ?>
                            </div>
                            <div class="col-12 mt-2">
                              <label class="form-label">Comment/Suggestions</label>
                              <div class="jumbotron mb-0 p-3">
                                <?= nl2br($feedbackData->comment) ?>
                              </div>
                            </div>
                            <div class="col-12 mt-2">
                              <?php include("../components/feedbacks.php"); ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach;
                if (count($feedbacks) == 0) :
                ?>
                  <div style="text-align: center;">
                    <span class="badge badge-primary rounded-pill px-2" style="font-size: 18px">
                      No comments yet
                    </span>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php
        endforeach;
        ?>
      </div>
    </div>
    <!-- /.card-body -->
  </div>
<?php else : ?>
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
            <th>Group Number</th>
            <th>Group list</th>
            <th>Section</th>
            <th>Course</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $query = mysqli_query(
            $conn,
            "SELECT * FROM thesis_groups WHERE instructor_id='$user->id' ORDER BY group_number"
          );

          while ($group = mysqli_fetch_object($query)) :
            $leader = get_user_by_id($group->group_leader_id);
            $leaderName = ucwords("$leader->first_name " . ($leader->middle_name != null ? $leader->middle_name[0] . "." : "") . " $leader->last_name");
            $memberData = json_decode(getMemberData($leader->group_number, $leader->id));
          ?>
            <tr>
              <td style="vertical-align: middle; text-align:center;font-size: 30px"><?= $group->group_number ?></td>
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
                <?php if (count($memberData) > 0) : ?>
                  <h5>Members:</h5>
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
                <?php endif; ?>
              </td>
              <td style="vertical-align: middle; text-align:center;font-size: 30px"><?= $leader->year_and_section ?></td>
              <td style="vertical-align: middle; text-align:center;font-size: 20px"><?= getCourseData($leader->course_id)->name ?></td>
              <td class="text-center">
                <?php
                $panelRatingQ = mysqli_query(
                  $conn,
                  "SELECT * FROM panel_ratings WHERE leader_id='$leader->id'"
                );
                $disabled = mysqli_num_rows($panelRatingQ) > 0 ? "" : "disabled";
                ?>
                <button type="button" class="btn btn-secondary btn-gradient-secondary m-1" onclick="return window.location.href='students?rating&&leaderId=<?= $leader->id ?>&&groupNumber=<?= $leader->group_number ?>'" <?= $disabled ?>>
                  Preview rating
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
<?php endif; ?>