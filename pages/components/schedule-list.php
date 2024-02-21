<div class="card card-outline rounded-0 card-navy mt-2">
  <?php
  if ($user->role != "student" && $user->role != "instructor") :
  ?>
    <div class="card-header">
      <h3 class="card-title">List of Rooms</h3>
      <div class="card-tools">
        <a data-toggle="modal" data-target="#addSchedule" class="btn btn-primary btn-gradient-primary">
          <span class="fas fa-plus"></span> Create New
        </a>
      </div>
    </div>
  <?php endif; ?>
  <div class="card-body">
    <div class="container-fluid">
      <div id="calendar"></div>
    </div>
  </div>
</div>

<div class="modal fade" id="addSchedule" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-plus"></i> Add New Task Schedule</h5>
      </div>
      <form method="POST" id="schedule-form">
        <div class="modal-body">
          <div class="container-fluid">
            <div class="form-group">
              <label for="category_id" class="control-label">Category</label>
              <select name="category_id" class="form-control" required>
                <option value="" selected disabled>-- select category --</option>
                <?php
                $category_q = mysqli_query(
                  $conn,
                  "SELECT * FROM category_list"
                );
                while ($category = mysqli_fetch_object($category_q)) :
                ?>
                  <option value="<?= $category->id ?>"><?= $category->name ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="leader_id" class="control-label">Leader</label>
              <select name="leader_id" class="form-control" required>
                <option value="" selected disabled>-- select leader --</option>
                <?php
                $leaderQ = mysqli_query(
                  $conn,
                  "SELECT * FROM users u INNER JOIN courses c ON u.course_id = c.course_id WHERE u.role='student' and u.isLeader='1'"
                );
                while ($leaderData = mysqli_fetch_object($leaderQ)) :
                  $leader = get_user_by_id($leaderData->id);
                ?>
                  <option value="<?= $leader->id ?>">
                    <?=
                    ucwords("$leader->first_name " . ($leader->middle_name != null ? $leader->middle_name[0] . "." : "") . " $leader->last_name") . " (Group #$leader->group_number $leaderData->short_name $leader->year_and_section)"
                    ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="title" class="control-label">Task Title</label>
              <input type="text" name="title" id="title" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="description" class="control-label">Description</label>
              <textarea name="description" class="form-control" required></textarea>
            </div>
            <div class="form-group">
              <label for="schedule_from" class="control-label">Schedule Start</label>
              <input type="datetime-local" min="<?= date("Y-m-d\TH:i") ?>" name="schedule_from" class="form-control" required />
            </div>
            <div class="form-group">
              <label for="schedule_to" class="control-label">Schedule End <small>(Leave it blank if you want it whole day)</small></label>
              <input type="datetime-local" name="schedule_to" class="form-control" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary btn-gradient-primary m-1">Save</button>
          <button type="button" class="btn btn-danger btn-gradient-danger m-1" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<?php
$query = mysqli_query(
  $conn,
  "SELECT * FROM schedule_list"
);

while ($schedule = mysqli_fetch_object($query)) :
  $category = getCategoryById($schedule->category_id);
  $taskBy = get_user_by_id($schedule->user_id);

  $leader = get_user_by_id($schedule->leader_id);
  $leaderName = ucwords("$leader->first_name " . ($leader->middle_name != null ? $leader->middle_name[0] . "." : "") . " $leader->last_name");
  $memberData = json_decode(getMemberData($leader->group_number, $leader->id));
  $courseData = getCourseData($leader->course_id);

  $groupQ = mysqli_query(
    $conn,
    "SELECT * FROM thesis_groups WHERE group_leader_id='$leader->id'"
  );
  $groupData = mysqli_fetch_object($groupQ);
  $panel_ids = $groupData->panel_ids ? json_decode($groupData->panel_ids) : null;



  $typeVal = "";
  if ($category->name == "Concept Presentation") {
    $typeVal = "concept";
  } else if ($category->name == "Thesis Defense 20%") {
    $typeVal = "20percent";
  } else if ($category->name == "Thesis Defense 50%") {
    $typeVal = "50percent";
  } else {
    $typeVal = "final";
  }

?>
  <div class="modal fade" id="preview<?= $schedule->id ?>">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fa fa-calendar-day"></i>
            Scheduled Task Details
          </h5>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <dl>
              <dt class="text-muted">User</dt>
              <dd class="pl-4"><?= ucwords("$taskBy->first_name " . ($taskBy->middle_name != null ? $taskBy->middle_name[0] . "." : "") . " $taskBy->last_name") ?></dd>
              <dt class="text-muted">Group #</dt>
              <dd class="pl-4"><?= $leader->group_number ?></dd>
              <dt class="text-muted">Course</dt>
              <dd class="pl-4"><?= $courseData->name ?></dd>
              <dt class="text-muted">Year and section</dt>
              <dd class="pl-4"><?= $leader->year_and_section ?></dd>
              <dt class="text-muted">Group List</dt>
              <dd class="pl-4">
                <h6>Leader:</h6>
                <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                  <div class="mr-1">
                    <img src="<?= $leader->avatar != null ? $SERVER_NAME . $leader->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                  </div>
                  <div>
                    <?= $leaderName ?>
                  </div>
                </div>
                <?php if (count($memberData) > 0) : ?>
                  <h6>Members:</h6>
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
              </dd>
              <?php if ($user->role == "panel") : ?>
                <dd class="pl-4">
                  <h6>Panels:</h6>
                  <?php
                  if ($panel_ids) :
                    foreach ($panel_ids as $panel_id) :
                      $panelData = get_user_by_id($panel_id);
                      $panelName = ucwords("$panelData->first_name " . ($panelData->middle_name != null ? $panelData->middle_name[0] . "." : "") . " $panelData->last_name");
                  ?>
                      <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                        <div class="mr-1">
                          <img src="<?= $panelData->avatar != null ? $SERVER_NAME . $panelData->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                        </div>
                        <div>
                          <?= $panelName ?>
                        </div>
                      </div>
                  <?php endforeach;
                  endif; ?>
                </dd>
              <?php endif; ?>
              <dt class="text-muted">Category</dt>
              <dd class="pl-4"><?= $category->name ?></dd>
              <dt class="text-muted">Schedule Start</dt>
              <dd class="pl-4"><?= date("F d, Y h:i A", strtotime($schedule->schedule_from)) ?></dd>
              <dt class="text-muted">Schedule End</dt>
              <dd class="pl-4">
                <?php
                if ($schedule->is_whole == 0) {
                  echo date('F d, Y h:i A', strtotime($schedule->schedule_to));
                } else {
                  echo "Whole day";
                }
                ?>
              </dd>
              <dt class="text-muted">Title</dt>
              <dd class="pl-4"><?= $schedule->title ?></dd>
              <dt class="text-muted">Description</dt>
              <dd class="pl-4"><?= $schedule->description ?></dd>
            </dl>
          </div>
        </div>
        <div class="modal-footer">
          <?php if ($taskBy->username == $_SESSION['username']) : ?>
            <button type="button" class="btn btn-primary btn-gradient-primary m-1" data-dismiss="modal" onclick="handleOnClickEdit('<?= $schedule->id ?>', 'openEdit')">Edit</button>
            <button type="button" class="btn btn-danger btn-gradient-danger m-1" onclick="handleDeleteSchedule('<?= $schedule->id ?>')">Delete</button>
            <?php endif;
          if ($user->role != "student") :
            if ($panel_ids && in_array($user->id, $panel_ids) && hasSubmittedThreeDocuments($leader) && $category->name == "Concept Presentation") :
            ?>
              <button type="button" class="btn btn-primary btn-gradient-primary m-1" onclick="return window.location.href = 'panel-preview-concept?leader_id=<?= $leader->id ?>'">Preview Concept</button>
            <?php else : ?>
              <?php if (($user->role == "instructor" || $user->role == "adviser") && $category->name == "Concept Presentation") : ?>
                <button type="button" class="btn btn-primary btn-gradient-primary m-1" onclick="return window.location.href = 'panel-preview-concept?leader_id=<?= $leader->id ?>'">Preview Concept</button>
              <?php else : ?>
                <button type="button" class="btn btn-primary btn-gradient-primary m-1" onclick="handleOpenModal('<?= $schedule->id ?>', 'documentPreview')">Preview Document</button>
              <?php endif; ?>

          <?php endif;
          endif;
          ?>

          <button type="button" class="btn btn-dark m-1" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <?php
  if ($typeVal != "concept") :
    $document = getApprovedDocument($leader);

    $ids = json_encode(
      array(
        "document_id" => $document->id,
        "leader_id" => $leader->id,
        "panel_id" => $user->id,
      )
    );
  ?>

    <!-- Preview Document -->
    <div class="modal fade" id="previewDocument<?= $schedule->id ?>">
      <div class="modal-dialog modal-xl modal-dialog-scrollable ">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <?= ucwords($document->title) ?>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" style="font-size: 30px">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <center>
              <img src="<?= $SERVER_NAME . $document->img_banner ?>" alt="Banner Image" id="banner-img" class="img-fluid border bg-gradient-dark">
            </center>
            <fieldset>
              <legend class="text-navy"> Field type:</legend>
              <div class="pl-4">
                <?= mysqli_fetch_object(mysqli_query($conn, "SELECT `name`, id FROM types WHERE id='$document->type_id'"))->name ?>
              </div>
            </fieldset>
            <fieldset>
              <legend class="text-navy"> Year:</legend>
              <div class="pl-4">
                <?= $document->year ?>
              </div>
            </fieldset>
            <fieldset>
              <legend class="text-navy">Description:</legend>
              <div class="pl-4">
                <?= nl2br($document->description) ?>
              </div>
            </fieldset>
            <fieldset>
              <legend class="text-navy">Project Leader:</legend>
              <div class="pl-4">
                <div class="ml-2 mt-2 mb-2 d-flex justify-content-start align-items-center">
                  <div class="mr-1">
                    <img src="<?= $leader->avatar != null ? $SERVER_NAME . $leader->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                  </div>
                  <div>
                    <?= $leaderName ?>
                  </div>
                </div>
              </div>
            </fieldset>
            <fieldset>
              <?php
              if (count($memberData) > 0) :
                echo "<legend class='text-navy'>Project Members:</legend>";
                foreach ($memberData as $member) :
                  $memberName = ucwords("$member->first_name " . ($member->middle_name != null ? $member->middle_name[0] . "." : "") . " $member->last_name");
              ?>
                  <div class="pl-4">
                    <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                      <div class="mr-1">
                        <img src="<?= $member->avatar != null ? $SERVER_NAME . $member->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                      </div>
                      <div>
                        <?= $memberName ?>
                      </div>
                    </div>
                  </div>
              <?php endforeach;
              endif; ?>
            </fieldset>
            <fieldset>
              <legend class="text-navy"> Document:</legend>
              <div class="pl-4">
                <div class="embed-responsive embed-responsive-4by3">
                  <iframe src="<?= $SERVER_NAME . $document->project_document ?>#embedded=true&toolbar=0&navpanes=0" class="embed-responsive-item"></iframe>
                </div>
              </div>
            </fieldset>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-gradient-secondary" onclick="return window.open('./preview-document?d=<?= urlencode($document->project_document) ?>')">
              Open document in new tab
            </button>

            <?php if ($user->role == "panel") :
              if (hasPanelRating($user->id, $typeVal, $document->id)) : ?>
                <button type="button" onclick="return window.location.href ='./assigned-groups?update&&documentId=<?= $document->id ?>&&type=<?= $typeVal ?>'" class="btn btn-primary btn-gradient-primary m-1">
                  Preview Rating
                </button>
              <?php else :
                $typeParam = $typeVal == "final" ? "final" : "";
              ?>
                <button type="button" onclick="handleOpenModal('<?= $schedule->id ?>', 'modalRate', '<?= $typeParam ?>')" class="btn btn-success btn-gradient-success m-1">
                  Rate
                </button>
            <?php endif;
            endif; ?>

          </div>
        </div>
      </div>
    </div>

    <!-- Rate Modal -->
    <div class="modal fade" id="modalRate<?= $schedule->id ?>">
      <div class="modal-dialog modal-lg modal-dialog-scrollable ">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              Rate Group
            </h5>
            <button type="button" class="close" aria-label="Close" onclick="handleCloseModal('<?= $schedule->id ?>', 'modalRate')">
              <span aria-hidden="true" style="font-size: 30px">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="modalRateBody">
            <form id="modalRate_form<?= $schedule->id ?>" method="POST">
              <div class="form-group">
                <label class="control-label">Comments/Suggestions</label>
                <textarea type="text" class="form-control form-control-sm summernote"></textarea>
              </div>
              <div class="form-group">
                <label class="control-label">Action Taken <span class="text-danger">*</span></label>
                <div class="row">
                  <div class="col-md-6 text-center">
                    <div class="icheck-success d-inline">
                      <input type="radio" name="action" id="actionApproved<?= $schedule->id ?>" value="Approved" required>
                      <label for="actionApproved<?= $schedule->id ?>">Approved</label>
                    </div>
                  </div>
                  <div class="col-md-6 text-center">
                    <div class="icheck-danger d-inline">
                      <input type="radio" name="action" id="actionDisapproved<?= $schedule->id ?>" value="Disapproved" required>
                      <label for="actionDisapproved<?= $schedule->id ?>">Disapproved</label>
                    </div>
                  </div>
                </div>
              </div>
              <table class="table table-bordered">
                <thead>
                  <caption style="color: black; text-align: center; caption-side: top; border: 1px solid #dee2e6">
                    Rating <span class="text-danger">*</span> <br>

                    <strong>
                      <pre>Rating Scale:	5=Excellent;    4=Very Good;    3=Good;    2=Poor;    1=Very Poor</pre>
                    </strong>
                  </caption>
                  <tr>
                    <th rowspan="2" style="border-bottom: 1px solid transparent;">Areas</th>
                    <th colspan="5" class="text-center">Rating</th>
                    <th rowspan="2">Remarks</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td></td>
                    <td>1</td>
                    <td>2</td>
                    <td>3</td>
                    <td>4</td>
                    <td>5</td>
                    <td></td>
                  </tr>
                  <!-- DOCUMENTATION  -->
                  <tr>
                    <td colspan="7">
                      <strong>
                        Documentation
                      </strong>
                    </td>
                  </tr>
                  <tr>
                    <td class="v-align-middle">Significant Improvement from previous document</td>
                    <?= generateTextareaTdRadio(5, "documentation_a") ?>
                    <td rowspan="3">
                      <div class='form-group'>
                        <textarea class="form-control" cols="50" rows="5" name="documentation_remarks" oninput="handleAddRemarks('documentation', $(this))" ></textarea>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="v-align-middle">Applied and Implemented previous suggestions/recommendations</td>
                    <?= generateTextareaTdRadio(5, "documentation_b") ?>
                  </tr>
                  <tr>
                    <td class="v-align-middle">Well researched as shown by the use of references</td>
                    <?= generateTextareaTdRadio(5, "documentation_c") ?>
                  </tr>
                  <!-- SYSTEM/PROGRAM -->
                  <tr>
                    <td colspan="7">
                      <strong>
                        System/Program
                      </strong>
                    </td>
                  </tr>
                  <tr>
                    <td class="v-align-middle">Significant improvement from previous system presented </td>
                    <?= generateTextareaTdRadio(5, "system_a") ?>
                    <td rowspan="4">
                      <div class='form-group'>
                        <textarea class="form-control" cols="50" rows="10" name="system_remarks" oninput="handleAddRemarks('system', $(this))" ></textarea>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="v-align-middle">Continuity of the development of the system (not another system presented)</td>
                    <?= generateTextareaTdRadio(5, "system_b") ?>
                  </tr>
                  <tr>
                    <td class="v-align-middle">Applied/integrated previous comments/suggestions/recommendation</td>
                    <?= generateTextareaTdRadio(5, "system_c") ?>
                  </tr>
                  <tr>
                    <td class="v-align-middle">Completeness deliverable</td>
                    <?= generateTextareaTdRadio(5, "system_d") ?>
                  </tr>
                  <!-- GROUP PRESENTATION  -->
                  <tr>
                    <td colspan="7">
                      <strong>
                        Group Presentation
                      </strong>
                    </td>
                  </tr>
                  <tr>
                    <td class="v-align-middle">Preparedness/Use of Visual Aids</td>
                    <?= generateTextareaTdRadio(5, "presentation_a") ?>
                    <td rowspan="4">
                      <div class='form-group'>
                        <textarea class="form-control" cols="50" rows="6" name="presentation_remarks" oninput="handleAddRemarks('presentation', $(this))" ></textarea>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="v-align-middle">Collaboration/cooperation</td>
                    <?= generateTextareaTdRadio(5, "presentation_b") ?>
                  </tr>
                  <tr>
                    <td class="v-align-middle">Mastery of the Study</td>
                    <?= generateTextareaTdRadio(5, "presentation_c") ?>
                  </tr>
                  <tr>
                    <td class="v-align-middle">Over-all Impact of the presentation</td>
                    <?= generateTextareaTdRadio(5, "presentation_d") ?>
                  </tr>
                  <!-- INDIVIDUAL PERFORMANCE  -->
                  <tr>
                    <td colspan="7">
                      <strong>
                        Individual Performance
                      </strong>
                    </td>
                  </tr>
                  <tr>
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
                    <?= generateTextareaTdRadio(5, "individual_$leader->id") ?>
                    <td>
                      <div class='form-group'>
                        <textarea class="form-control" cols="50" rows="2" id="individual_remarks<?= $leader->id ?>" name="remarks<?= $leader->id ?>" oninput="handleIndividualRemarks('<?= $leader->id ?>', '<?= $leaderName ?>', $(this).val())" ></textarea>
                      </div>
                    </td>
                    <input type="text" id="individual_name<?= $leader->id ?>" value="<?= $leaderName ?>" hidden readonly>
                  </tr>
                  <?php
                  if (count($memberData) > 0) :
                    foreach ($memberData as $member) :
                      $memberName = ucwords("$member->first_name " . ($member->middle_name != null ? $member->middle_name[0] . "." : "") . " $member->last_name");
                  ?>
                      <tr>
                        <td>
                          <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                            <div class="mr-1">
                              <img src="<?= $member->avatar != null ? $SERVER_NAME . $member->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                            </div>
                            <div>
                              <?= $memberName ?>
                            </div>
                          </div>
                        </td>
                        <?= generateTextareaTdRadio(5, "individual_$member->id") ?>
                        <td>
                          <div class='form-group'>
                            <textarea class="form-control" cols="50" rows="2" id="individual_remarks<?= $member->id ?>" name="remarks<?= $member->id ?>" oninput="handleIndividualRemarks('<?= $member->id ?>', '<?= $memberName ?>', $(this).val())" ></textarea>
                          </div>
                        </td>
                        <input type="text" id="individual_name<?= $member->id ?>" value="<?= $memberName ?>" hidden readonly>
                      </tr>
                  <?php endforeach;
                  endif; ?>

                </tbody>
              </table>
            </form>
            <input type="text" id="ids<?= $schedule->id ?>" value='<?= $ids ?>' hidden readonly>

            <input type="text" id="type<?= $schedule->id ?>" value="<?= $typeVal ?>" hidden readonly>
          </div>
          <div class="modal-footer">
            <button type="button" onclick="handleSaveRating('<?= $schedule->id ?>', 'insert')" class="btn btn-primary btn-gradient-primary m-1">
              Submit
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Rate Final Modal -->
    <div class="modal fade" id="modalFinalRate<?= $schedule->id ?>">
      <div class="modal-dialog modal-lg modal-dialog-scrollable ">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              Rate Group Final
            </h5>
            <button type="button" class="close" aria-label="Close" onclick="handleCloseModal('<?= $schedule->id ?>', 'modalFinalRate')">
              <span aria-hidden="true" style="font-size: 30px">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="modalFinalRateBody">
            <form id="modalFinalRate_form<?= $schedule->id ?>" method="POST">
              <div class="form-group">
                <label class="control-label">Comments/Suggestions</label>
                <textarea type="text" class="form-control form-control-sm summernote"></textarea>
              </div>
              <table class="table table-bordered">
                <thead>
                  <caption style="color: black; text-align: center; caption-side: top; border: 1px solid #dee2e6">
                    Rating <span class="text-danger">*</span> <br>

                    <strong>
                      <pre>Rating Scale:	6=Excellent;    5=Very Good;    4=Good;    3=Fair;    2=Poor;    1=Very Poor</pre>
                    </strong>
                  </caption>
                  <tr>
                    <th rowspan="2" style="border-bottom: 1px solid transparent;">Areas</th>
                    <th colspan="6" class="text-center">Rating</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td></td>
                    <td>1</td>
                    <td>2</td>
                    <td>3</td>
                    <td>4</td>
                    <td>5</td>
                    <td>6</td>
                  </tr>
                  <!-- Technical  -->
                  <tr>
                    <td colspan="5">
                      <strong>
                        General Technical Criteria
                      </strong>
                    </td>
                  </tr>
                  <tr>
                    <td class="v-align-middle">
                      <h6 style="font-weight: bold;">
                        RELIABILITY
                      </h6>
                      Extent to which a software can be expected to perform Its intended function with required precision (i.e. dependable. the probability of failure is low)
                    </td>
                    <?= generateTextareaTdRadio(6, "technical_a", null, "final") ?>
                  </tr>
                  <tr>
                    <td class="v-align-middle">
                      <h6 style="font-weight: bold;">
                        EFFICIENCY
                      </h6>
                      Minimal amount of computing resources and code required by the software to perform its function
                    </td>
                    <?= generateTextareaTdRadio(6, "technical_b", null, "final") ?>
                  </tr>
                  <tr>
                    <td class="v-align-middle">
                      <h6 style="font-weight: bold;">
                        USABILITY
                      </h6>
                      Effort required to learn and operate the in a user friendly manner (i.e. interface is consistent and stimulates user's, appropriate environment, easy to use)
                    </td>
                    <?= generateTextareaTdRadio(6, "technical_c", null, "final") ?>
                  </tr>
                  <tr>
                    <td class="v-align-middle">
                      <h6 style="font-weight: bold;">
                        UNDERSTANDABILITY
                      </h6>
                      Degree to which source provides meaningful documentation, interactions of the sofWare components can be quickly understood, and the design is well
                    </td>
                    <?= generateTextareaTdRadio(6, "technical_d", null, "final") ?>
                  </tr>
                  <tr>
                    <td class="v-align-middle">
                      <h6 style="font-weight: bold;">
                        APPROPRIATENESS OF FEEDBACK TO USER
                      </h6>
                      Instructions of error message and understandable and directions are clear as to what the user must do to use the software effectively
                    </td>
                    <?= generateTextareaTdRadio(6, "technical_e", null, "final") ?>
                  </tr>
                  <tr>
                    <td class="v-align-middle">
                      <h6 style="font-weight: bold;">
                        NAVIGATION AND ORGANIZATION
                      </h6>
                      Users can progress Intuitively throughout the entre software in a logical path to find information. All buttons and navigational tools work
                    </td>
                    <?= generateTextareaTdRadio(6, "technical_f", null, "final") ?>
                  </tr>
                  <tr>
                    <td colspan="5">
                      <strong>
                        General Presentation Criteria
                      </strong>
                    </td>
                  </tr>
                  <tr>
                    <td class="v-align-middle">
                      <h6 style="font-weight: bold;">
                        PREPARATION
                      </h6>
                      Proponents hae adequately prepared for the presentation as indicated by smooth, comprehensive. concise and efficient delivery and quick and accurate responses to jurors' questions
                    </td>
                    <?= generateTextareaTdRadio(6, "presentation_a", null, "final") ?>
                  </tr>
                  <tr>
                    <td class="v-align-middle">
                      <h6 style="font-weight: bold;">
                        SYNTHESIS
                      </h6>
                      Proponents have a grasp of the objectives ot the thesis and SAD principles and methods
                    </td>
                    <?= generateTextareaTdRadio(6, "presentation_b", null, "final") ?>
                  </tr>
                  <tr>
                    <td colspan="5">
                      <strong>
                        Specific Technical Criteria for Multimedia Technologies (Educational, Interactive or Game)
                      </strong>
                    </td>
                  </tr>
                  <tr>
                    <td class="v-align-middle">
                      <h6 style="font-weight: bold;">
                        CONTENT AND DESIGN
                      </h6>
                      <div>
                        <div>
                          <label>
                            <i>(For Educational/Interactive) </i>
                          </label>
                        </div>
                        There is clear attention given to balance, proportion, harmony and restraint. The synergy reaches the intended audience with style.
                      </div>
                      <div>
                        <div>
                          <label>
                            <i>(For Game)</i>
                          </label>
                        </div>
                        The user easily the goal of the game, functionality (the way the game works) changes relative to adjustments made by the user, and it uses facts, statistics, reference materials or tools in the actual activity.
                      </div>
                    </td>
                    <?= generateTextareaTdRadio(6, "multimedia_a", null, "final") ?>
                  </tr>
                  <tr>
                    <td class="v-align-middle">
                      <h6 style="font-weight: bold;">
                        USE OF ENHANCEMENT
                      </h6>
                      Graphics, video, audio, or other enhancements are used effectively to enrich the experience. Enhancement contribute significantly to convey the intended
                    </td>
                    <?= generateTextareaTdRadio(6, "multimedia_b", null, "final") ?>
                  </tr>
                  <tr>
                    <td colspan="5">
                      <strong>
                        Specific Technical Criteria for Information Systems & Prototype Software Systems
                      </strong>
                    </td>
                  </tr>
                  <tr>
                    <td class="v-align-middle">
                      <h6 style="font-weight: bold;">
                        CORRECTNESS
                      </h6>
                      Extent to which a program satisfies Its specification and fulfill end-user's objective (I.e. specifications and software are equivalent)
                    </td>
                    <?= generateTextareaTdRadio(6, "information_a", null, "final") ?>
                  </tr>
                  <tr>
                    <td class="v-align-middle">
                      <h6 style="font-weight: bold;">
                        INTEGRITY
                      </h6>
                      Extent to which access to software or data can be controlled by the security feature of the program.
                    </td>
                    <?= generateTextareaTdRadio(6, "information_b", null, "final") ?>
                  </tr>
                </tbody>
              </table>
            </form>
            <input type="text" id="ids<?= $schedule->id ?>" value='<?= $ids ?>' hidden readonly>

            <input type="text" id="type<?= $schedule->id ?>" value="<?= $typeVal ?>" hidden readonly>
          </div>
          <div class="modal-footer">
            <button type="button" onclick="handleSaveRating('<?= $schedule->id ?>', 'final')" class="btn btn-primary btn-gradient-primary m-1">
              Submit
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="editScheduleModal<?= $schedule->id ?>">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="fa fa-edit"></i>
              Edit Schedule Details
            </h5>
          </div>
          <form method="POST" id="schedule-form">
            <div class="modal-body">
              <div class="container-fluid">
                <input type="text" name="id" value="<?= $schedule->id ?>" hidden readonly>
                <div class="form-group">
                  <label for="category_id" class="control-label">Category</label>
                  <select name="category_id" class="form-control" required>
                    <?php
                    $category_q = mysqli_query(
                      $conn,
                      "SELECT * FROM category_list"
                    );
                    while ($category = mysqli_fetch_object($category_q)) :
                    ?>
                      <option value="<?= $category->id ?>" <?= $schedule->category_id == $category->id ? "selected" : "" ?>>
                        <?= $category->name ?>
                      </option>
                    <?php endwhile; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="leader_id" class="control-label">Leader</label>
                  <select name="leader_id" class="form-control" required>
                    <option value="" selected disabled>-- select leader --</option>
                    <?php
                    $leaderQ = mysqli_query(
                      $conn,
                      "SELECT * FROM users u INNER JOIN courses c ON u.course_id = c.course_id WHERE u.role='student' and u.id='$schedule->leader_id'"
                    );
                    while ($leaderData = mysqli_fetch_object($leaderQ)) :
                      $leader = get_user_by_id($leaderData->id);
                    ?>
                      <option value="<?= $leader->id ?>" <?= $leader->id == $schedule->leader_id ? "selected" : "" ?>>
                        <?=
                        ucwords("$leader->first_name " . ($leader->middle_name != null ? $leader->middle_name[0] . "." : "") . " $leader->last_name") . " (Group #$leader->group_number $leaderData->short_name $leader->year_and_section)"
                        ?>
                      </option>
                    <?php endwhile; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="title" class="control-label">Task Title</label>
                  <input type="text" name="title" value="<?= $schedule->title ?>" class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="description" class="control-label">Description</label>
                  <textarea name="description" class="form-control" required><?= $schedule->description ?></textarea>
                </div>
                <div class="form-group">
                  <label for="schedule_from" class="control-label">Schedule Start</label>
                  <input type="datetime-local" value="<?= date("Y-m-d\TH:i", strtotime($schedule->schedule_from)) ?>" name="schedule_from" class="form-control" required />
                </div>
                <div class="form-group">
                  <label for="schedule_to" class="control-label">Schedule End <small>(clear field if you want it whole day)</small></label>
                  <input type="datetime-local" min="<?= date("Y-m-d\TH:i") ?>" value="<?= isset($schedule->schedule_to) ? date("Y-m-d\TH:i", strtotime($schedule->schedule_to)) : "" ?>" name="schedule_to" class="form-control" />
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary btn-gradient-primary m-1" onclick="handleSaveEditForm($(this))">
                Save
              </button>
              <button type="button" class="btn btn-danger btn-gradient-danger m-1" data-dismiss="modal" onclick="handleOnClickEdit('<?= $schedule->id ?>', 'openPreview')">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
<?php endif;
endwhile; ?>