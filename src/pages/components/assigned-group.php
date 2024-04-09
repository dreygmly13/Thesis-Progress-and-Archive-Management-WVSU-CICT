<?php
if (isset($_GET['update'])) :
  $panelRatingQuery = mysqli_query(
    $conn,
    "SELECT * FROM panel_ratings WHERE document_id='$_GET[documentId]' and panel_id='$user->id' and rating_type='$_GET[type]'"
  );
  $ratingData = null;

  if (mysqli_num_rows($panelRatingQuery) > 0) {
    $ratingData = mysqli_fetch_object($panelRatingQuery);
  }

  $groupGrades = json_decode($ratingData->group_grade, true);
  $leader = get_user_by_id($ratingData->leader_id);
?>
  <div class="container">
    <div class="card card-outline rounded-0 card-navy mt-2">
      <div class="card-header">
        <h5 class="card-title"><?= panelNameType($ratingData->rating_type) ?> Rating</h5>
      </div>
      <form class="updateRating" method="POST" novalidate>
        <input type="text" name="ratingId" value="<?= $ratingData->rating_id ?>" hidden readonly>
        <!-- /.card-header -->
        <div class="card-body">
          <div class="form-group">
            <label class="control-label">Comments/Suggestions</label>
            <textarea type="text" class="form-control form-control-sm summernote" name="comment"><?= nl2br($ratingData->comment) ?></textarea>
          </div>
          <?php if ($ratingData->rating_type != "final") : ?>
            <div class="form-group">
              <label class="control-label">Action Taken <span class="text-danger">*</span></label>
              <div class="row">
                <div class="col-md-6 text-center">
                  <div class="icheck-success d-inline">
                    <input type="radio" name="action" id="actionApprovedConcept" value="Approved" <?= $ratingData->action == "Approved" ? "checked" : "" ?> required>
                    <label for="actionApprovedConcept">Approved</label>
                  </div>
                </div>
                <div class="col-md-6 text-center">
                  <div class="icheck-danger d-inline">
                    <input type="radio" name="action" id="actionDisapprovedConcept" value="Disapproved" <?= $ratingData->action == "Disapproved" ? "checked" : "" ?> required>
                    <label for="actionDisapprovedConcept">Disapproved</label>
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($ratingData->rating_type != "concept" && $ratingData->rating_type != "final") : ?>
            <table class="table table-bordered">
              <thead>

                <caption class="p-0" style="color: black; text-align: center; caption-side: top; border: 1px solid #dee2e6">
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
                <?php
                $groupRating = json_decode($ratingData->group_grade, true);
                foreach ($groupRating as $index => $groupRatingData) :
                ?>
                  <tr>
                    <td colspan="7">
                      <strong>
                        <?= $groupRatingData["title"] ?>
                      </strong>
                    </td>
                  </tr>
                  <?php
                  for ($i = 0; $i < count($groupRatingData["ratings"]); $i++) :
                    $rating = $groupRatingData["ratings"][$i];

                    $radioName = $groupRatingData["ratings"][$i]["name"];
                    $checkedVal = $groupRatingData["ratings"][$i]["rating"];
                  ?>
                    <tr>
                      <td><?= $rating["title"] ?></td>
                      <?= generateTextareaTdRadio(5, $radioName, $checkedVal) ?>
                      <?php if ($i == 0) : ?>
                        <td rowspan="<?= count($groupRatingData["ratings"]) ?>">
                          <div class='form-group'>
                            <textarea class="form-control" cols="50" rows="<?= count($groupRatingData["ratings"]) + 2 ?>" name="<?= $index . "_remarks" ?>" ><?= $groupRatingData["remarks"] ?></textarea>
                          </div>
                        </td>
                      <?php endif; ?>
                    </tr>
                <?php
                  endfor;
                endforeach; ?>
                <tr>
                  <td colspan="3">
                    <strong>
                      Individual Performance
                    </strong>
                  </td>
                </tr>
                <?php
                $individualRatings = json_decode($ratingData->individual_grade, true);
                foreach ($individualRatings as $individualRating) :
                  $user_details = get_user_by_id($individualRating["id"]);
                ?>
                  <tr>
                    <td style="vertical-align: middle;">
                      <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
                        <div class="mr-1">
                          <img src="<?= $user_details->avatar != null ? $SERVER_NAME . $user_details->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                        </div>
                        <div>
                          <?= ucwords("$user_details->first_name " . ($user_details->middle_name != null ? $user_details->middle_name[0] . "." : "") . " $user_details->last_name") ?>
                        </div>
                      </div>
                    </td>
                    <?= generateTextareaTdRadio(5, $user_details->id, $individualRating["rating"]) ?>
                    <td>
                      <div class='form-group'>
                        <textarea class="form-control" cols="50" name="<?= $user_details->id ?>_remarks" ><?= $individualRating["remarks"] ?></textarea>
                      </div>
                    </td>
                  </tr>
                <?php endforeach ?>
              </tbody>
            </table>
          <?php endif; ?>

          <?php if ($ratingData->rating_type == "final") : ?>
            <table class="table table-bordered">
              <thead>

                <caption class="p-0" style="color: black; text-align: center; caption-side: top; border: 1px solid #dee2e6">
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
                <?php
                $groupRating = json_decode($ratingData->group_grade, true);
                foreach ($groupRating as $index => $groupRatingData) :
                ?>
                  <tr>
                    <td colspan="5">
                      <strong>
                        <?= $groupRatingData["title"] ?>
                      </strong>
                    </td>
                  </tr>
                  <?php
                  for ($i = 0; $i < count($groupRatingData["ratings"]); $i++) :
                    $rating = $groupRatingData["ratings"][$i];

                    $radioName = $groupRatingData["ratings"][$i]["name"];
                    $checkedVal = $groupRatingData["ratings"][$i]["rating"];
                  ?>
                    <tr>
                      <td class="v-align-middle">
                        <h6 style="font-weight: bold;">
                          <?= $rating["title"] ?>
                        </h6>
                        <?= $rating["description"] ?>
                        <?= generateTextareaTdRadio(6, $radioName, $checkedVal, 'final') ?>
                      </td>
                    </tr>
                <?php
                  endfor;
                endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>

        </div>
        <div class="card-footer d-flex justify-content-end">
          <button type="submit" class="btn btn-primary btn-gradient-primary m-1">
            Update
          </button>

          <button type="button" onclick="return window.history.back()" class="btn btn-secondary btn-gradient-secondary m-1">
            Go back
          </button>
        </div>
      </form>
    </div>
  </div>
<?php else : ?>
  <div class="card card-outline rounded-0 card-navy mt-2">
    <!-- /.card-header -->
    <div class="card-body">
      <table id="assigned_group" class="table table-bordered table-hover">
        <thead>
          <tr class="bg-gradient-dark text-light">
            <th>Group#</th>
            <th>Group list</th>
            <th>Title</th>
            <th>Description</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $assignedGroups = getPanelAssignedGroup($user->id);

          if ($assignedGroups) :
            foreach ($assignedGroups as $data) :
              $leader = get_user_by_id($data->group_leader_id);
              $leaderName = ucwords("$leader->first_name " . ($leader->middle_name != null ? $leader->middle_name[0] . "." : "") . " $leader->last_name");
              $memberData = json_decode(getMemberData($leader->group_number, $leader->id));

              $description = nl2br($data->description);

              $panelRatingQuery = mysqli_query(
                $conn,
                "SELECT * FROM panel_ratings WHERE panel_id='$user->id' and document_id='$data->id'"
              );
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
                <td><?= $data->title ?></td>
                <td><?= strlen($description) > 250 ? substr($description, 0, 250) . "..." : $description ?></td>
                <td>
                  <button type="button" class="btn btn-secondary btn-gradient-secondary m-1" onclick="handleOpenModal('<?= $data->id ?>')">
                    Preview
                  </button>
                  <button type="button" onclick="handleOpenRatingModal('<?= $data->id ?>')" class="btn btn-success btn-gradient-success m-1">
                    Rate
                  </button>
                  <?php if (mysqli_num_rows($panelRatingQuery) > 0) : ?>
                    <button type="button" onclick="handleRedirectPanelRating('<?= $data->id ?>', '<?= $user->id ?>')" class="btn btn-primary btn-gradient-primary m-1">
                      Preview Rating
                    </button>
                  <?php endif; ?>
                </td>
              </tr>

              <!-- Preview Document -->
              <div class="modal fade" id="preview<?= $data->id ?>">
                <div class="modal-dialog modal-xl modal-dialog-scrollable ">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">
                        <?= ucwords($data->title) ?>
                      </h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: 30px">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <center>
                        <img src="<?= $SERVER_NAME . $data->img_banner ?>" alt="Banner Image" id="banner-img" class="img-fluid border bg-gradient-dark">
                      </center>
                      <fieldset>
                        <legend class="text-navy"> Field type:</legend>
                        <div class="pl-4">
                          <?= mysqli_fetch_object(mysqli_query($conn, "SELECT `name`, id FROM types WHERE id='$data->type_id'"))->name ?>
                        </div>
                      </fieldset>
                      <fieldset>
                        <legend class="text-navy"> Year:</legend>
                        <div class="pl-4">
                          <?= $data->year ?>
                        </div>
                      </fieldset>
                      <fieldset>
                        <legend class="text-navy">Description:</legend>
                        <div class="pl-4">
                          <?= nl2br($data->description) ?>
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
                            <iframe src="<?= $SERVER_NAME . $data->project_document ?>#embedded=true&toolbar=0&navpanes=0" class="embed-responsive-item"></iframe>
                          </div>
                        </div>
                      </fieldset>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary btn-gradient-secondary" onclick="return window.open('./preview-document?d=<?= urlencode($data->project_document) ?>')">
                        Open document in new tab
                      </button>

                      <button type="button" onclick="handleOpenRatingModal('<?= $data->id ?>')" class="btn btn-success btn-gradient-success m-1">
                        Rate
                      </button>
                    </div>
                  </div>
                </div>
              </div>


          <?php endforeach;
          endif; ?>
        </tbody>

      </table>
    </div>
    <!-- /.card-body -->
  </div>

  <?php
  if ($assignedGroups) :
    foreach ($assignedGroups as $data) :
      $leader = get_user_by_id($data->group_leader_id);
      $leaderName = ucwords("$leader->first_name " . ($leader->middle_name != null ? $leader->middle_name[0] . "." : "") . " $leader->last_name");
      $memberData = json_decode(getMemberData($leader->group_number, $leader->id));

      $description = nl2br($data->description);
      $ids = json_encode(
        array(
          "document_id" => $data->id,
          "leader_id" => $leader->id,
          "panel_id" => $user->id,
        )
      );
  ?>
      <!-- Rate Concept Document -->
      <div class="modal fade" id="modalConcept<?= $data->id ?>">
        <div class="modal-dialog modal-lg modal-dialog-scrollable ">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                Rate Group
              </h5>
              <button type="button" class="close" aria-label="Close" onclick="handleCloseModal('<?= $data->id ?>', 'modalConcept')">
                <span aria-hidden="true" style="font-size: 30px">&times;</span>
              </button>
            </div>
            <div class="modal-body" id="modalRateBody">
              <form id="modalConcept_form<?= $data->id ?>" method="POST">
                <div class="form-group">
                  <label class="control-label">Comments/Suggestions</label>
                  <textarea type="text" class="form-control form-control-sm summernote"></textarea>
                </div>
                <div class="form-group">
                  <label class="control-label">Action Taken <span class="text-danger">*</span></label>
                  <div class="row">
                    <div class="col-md-6 text-center">
                      <div class="icheck-success d-inline">
                        <input type="radio" name="action" id="actionApprovedConcept<?= $data->id ?>" value="Approved" required>
                        <label for="actionApprovedConcept<?= $data->id ?>">Approved</label>
                      </div>
                    </div>
                    <div class="col-md-6 text-center">
                      <div class="icheck-danger d-inline">
                        <input type="radio" name="action" id="actionDisapprovedConcept<?= $data->id ?>" value="Disapproved" required>
                        <label for="actionDisapprovedConcept<?= $data->id ?>">Disapproved</label>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label">Rating</label>
                  <table class="table table-bordered">
                    <thead>
                      <caption style="color: black; text-align: center; caption-side: top; border: 1px solid #dee2e6">
                        Group Rating <span class="text-danger">*</span>
                      </caption>
                      <tr>
                        <th>Criteria</th>
                        <th>Max Points</th>
                        <th>Panel</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Complexity and Innovativeness of the proposal</td>
                        <td>20</td>
                        <td>
                          <div class="form-group">
                            <input type="number" name="complexity" class="form-control" oninput="handleGroupGradeChange('complexity', $(this).val())" min="1" max="20" required>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td>Content and appropriateness of the Document</td>
                        <td>50</td>
                        <td>
                          <div class="form-group">
                            <input type="number" name="content" class="form-control" oninput="handleGroupGradeChange('content', $(this).val())" min="1" max="50" required>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td>Group Delivery and presentation</td>
                        <td>30</td>
                        <td>
                          <div class="form-group">
                            <input type="number" name="delivery" class="form-control" oninput="handleGroupGradeChange('delivery', $(this).val())" min="1" max="30" required>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2">Total</td>
                        <td class="gradeTotal text-center"></td>
                      </tr>
                    </tbody>
                  </table>

                  <table class="table table-bordered">
                    <thead>
                      <caption style="color: black; text-align: center; caption-side: top; border: 1px solid #dee2e6">
                        Individual Grade <span class="text-danger">*</span>
                      </caption>
                      <tr>
                        <th>Group members:</th>
                        <th>Grade (Max 100%)</th>
                      </tr>
                    </thead>
                    <tbody>
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
                        <td>
                          <div class="form-group">
                            <input type="number" name="leader" class="form-control" oninput="handleIndividualGrade('<?= $leader->id ?>', ' <?= $leaderName ?>', $(this).val())" min="1" max="100" required>
                          </div>
                        </td>
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
                            <td>
                              <div class="form-group">
                                <input type="number" name="member<?= $member->id ?>" class="form-control" oninput="handleIndividualGrade('<?= $member->id ?>', ' <?= $memberName ?>', $(this).val())" min="1" max="100" required>
                              </div>
                            </td>
                          </tr>
                      <?php endforeach;
                      endif; ?>
                    </tbody>
                  </table>
                </div>
              </form>
              <input type="text" id="ids<?= $data->id ?>" value='<?= $ids ?>' hidden readonly>
            </div>
            <div class="modal-footer">
              <button type="button" onclick="handleSave('<?= $data->id ?>', 'insert')" class="btn btn-primary btn-gradient-primary m-1">
                Submit
              </button>
              <button type="button" onclick="changeRating('modalConcept', '<?= $data->id ?>')" class="btn btn-secondary btn-gradient-secondary m-1">
                Change rating
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Rate 20% - 50% Modal -->
      <div class="modal fade" id="modalRate<?= $data->id ?>">
        <div class="modal-dialog modal-lg modal-dialog-scrollable ">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                Rate Group
              </h5>
              <button type="button" class="close" aria-label="Close" onclick="handleCloseModal('<?= $data->id ?>', 'modalRate')">
                <span aria-hidden="true" style="font-size: 30px">&times;</span>
              </button>
            </div>
            <div class="modal-body" id="modalRateBody">
              <form id="modalRate_form<?= $data->id ?>" method="POST">
                <div class="form-group">
                  <label class="control-label">Comments/Suggestions</label>
                  <textarea type="text" class="form-control form-control-sm summernote"></textarea>
                </div>
                <div class="form-group">
                  <label class="control-label">Action Taken <span class="text-danger">*</span></label>
                  <div class="row">
                    <div class="col-md-6 text-center">
                      <div class="icheck-success d-inline">
                        <input type="radio" name="action" id="actionApproved<?= $data->id ?>" value="Approved" required>
                        <label for="actionApproved<?= $data->id ?>">Approved</label>
                      </div>
                    </div>
                    <div class="col-md-6 text-center">
                      <div class="icheck-danger d-inline">
                        <input type="radio" name="action" id="actionDisapproved<?= $data->id ?>" value="Disapproved" required>
                        <label for="actionDisapproved<?= $data->id ?>">Disapproved</label>
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
                          <textarea class="form-control" cols="50" rows="5" name="documentation_remarks" oninput="handleAddRemarks('documentation', $(this))"></textarea>
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
              <input type="text" id="ids<?= $data->id ?>" value='<?= $ids ?>' hidden readonly>
            </div>
            <div class="modal-footer">
              <button type="button" onclick="handleSave('<?= $data->id ?>', 'insert')" class="btn btn-primary btn-gradient-primary m-1">
                Submit
              </button>
              <button type="button" onclick="changeRating('modalRate', '<?= $data->id ?>')" class="btn btn-secondary btn-gradient-secondary m-1">
                Change rating
              </button>
            </div>
          </div>
        </div>
      </div>

<?php endforeach;
  endif;
endif; ?>