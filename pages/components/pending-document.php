<div class="card card-outline rounded-0 card-navy mt-2">
  <div class="card-header">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h4>Pending Documents</h4>
      </div>

    </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="pending_documents" class="table table-bordered table-hover">
      <thead>
        <tr class="bg-gradient-dark text-light">
          <th>Last updated</th>
          <th>Group#</th>
          <th>Group list</th>
          <th>My feedback</th>
          <th><?= $user->role == "adviser" ? "Instructor" : "Adviser" ?></th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $documentData = getDocumentsDataWithUsers($user->id, $user->role);
        foreach ($documentData as $data) :
          $leader = get_user_by_id($data->group_leader_id);
          $leaderName = ucwords("$leader->first_name " . ($leader->middle_name != null ? $leader->middle_name[0] . "." : "") . " $leader->last_name");
          $memberData = json_decode(getMemberData($leader->group_number, $leader->id));

          $adviserFeedbackData = json_decode($data->adviser_feedback);
          $instructorFeedbackData = json_decode($data->instructor_feedback);

          $myFeedback =  $user->role == "adviser" ? $adviserFeedbackData : $instructorFeedbackData;
          $otherFeedback = $user->role == "adviser" ? $instructorFeedbackData : $adviserFeedbackData;
        ?>
          <tr>
            <td><?= date("M d, Y h:i:s A", strtotime($data->date_updated)) ?></td>
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
            <td>
              <?php
              if ($myFeedback == null) :
              ?>
                <p class='text-center'>
                  <span class="badge badge-warning rounded-pill px-4" style="font-size: 18px">
                    <em>No feedback yet</em>
                  </span>
                </p>
                <?php
              else :
                foreach ($myFeedback->feedback as $myFeed) :
                ?>
                  <blockquote class="blockquote my-2 mx-0" style="font-size: 14px; overflow: hidden;">
                    <?php
                    if ($myFeed->isResolved == "true") : ?>
                      <span>&#8226; <strong><?= $myFeed->date ?></strong></span>
                      <p>
                        <s>
                          <?= nl2br($myFeed->message) ?>
                        </s>
                      </p>
                      <span class="badge badge-success rounded-pill px-2" style="float:right;font-size: 14px">Resolved</span>
                    <?php else : ?>
                      <span>&#8226;<strong><?= $myFeed->date ?></strong></span>
                      <a href="#" onclick="handleMarkResolved('<?= $otherFeed->token ?>', '<?= $data->id ?>', '<?= $user->role ?>')">
                        <span class="badge badge-success rounded-pill px-2" style="float:right;font-size: 14px">Mark as resolved</span>
                      </a>
                      <p>
                        <?= nl2br($myFeed->message) ?>
                      </p>
                      <span class="badge badge-warning rounded-pill px-2" style="float:right;font-size: 14px">To update</span>
                    <?php endif; ?>
                  </blockquote>
                <?php
                endforeach;
              endif;
              if ($myFeedback != null && $myFeedback->isApproved == "true") :
                ?>
                <p class='text-center'>
                  <span class="badge badge-success rounded-pill px-4" style="font-size: 18px">
                    <em>Approved</em>
                  </span>
                </p>
              <?php endif; ?>
            </td>
            <td>
              <?php
              if ($otherFeedback == null) :
              ?>
                <p class='text-center'>
                  <span class="badge badge-warning rounded-pill px-4" style="font-size: 18px">
                    <em>No feedback yet</em>
                  </span>
                </p>
                <?php
              else :
                foreach ($otherFeedback->feedback as $otherFeed) :
                ?>
                  <blockquote class="blockquote my-2 mx-0" style="font-size: 14px; overflow: hidden;">
                    <?php
                    if ($otherFeed->isResolved == "true") : ?>
                      <span>&#8226; <strong><?= $otherFeed->date ?></strong></span>
                      <p>
                        <s>
                          <?= nl2br($otherFeed->message) ?>
                        </s>
                      </p>
                      <span class="badge badge-success rounded-pill px-2" style="float:right;font-size: 14px">Resolved</span>
                    <?php else : ?>
                      <span>&#8226;<strong><?= $otherFeed->date ?></strong></span>
                      <p>
                        <?= nl2br($otherFeed->message) ?>
                      </p>
                      <span class="badge badge-warning rounded-pill px-2" style="float:right;font-size: 14px">To update</span>
                    <?php endif; ?>
                  </blockquote>
                <?php
                endforeach;
              endif;
              if ($otherFeedback != null && $otherFeedback->isApproved == "true") :
                ?>
                <p class='text-center'>
                  <span class="badge badge-success rounded-pill px-4" style="font-size: 18px">
                    <em>Approved</em>
                  </span>
                </p>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <button type="button" class="btn btn-secondary btn-gradient-secondary m-1" onclick="handleOpenModal('<?= $data->id ?>')">
                Preview
              </button>
            </td>
          </tr>
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
                    <legend class="text-navy">Project Members:</legend>
                    <div class="pl-4">
                      <?php
                      foreach ($memberData as $member) :
                        $memberName = ucwords("$member->first_name " . ($member->middle_name != null ? $member->middle_name[0] . "." : "") . " $member->last_name");
                      ?>
                        <div class="ml-2 mt-2 mb-2 d-flex justify-content-start align-items-center">
                          <div class="mr-1">
                            <img src="<?= $member->avatar != null ? $SERVER_NAME . $member->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
                          </div>
                          <div>
                            <?= $memberName ?>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
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
                  <?php
                  $disabled = "";

                  if ($user->role == "adviser") {
                    if ($adviserFeedbackData != null && $adviserFeedbackData->isApproved == "true") {
                      $disabled = "disabled";
                    }
                  } else {
                    if ($instructorFeedbackData != null && $instructorFeedbackData->isApproved == "true") {
                      $disabled = "disabled";
                    }
                  }

                  ?>
                  <button type="button" class="btn btn-secondary btn-gradient-secondary" onclick="return window.open('./preview-document?d=<?= urlencode($data->project_document) ?>')">
                    Open document in new tab
                  </button>
                  <button type="button" data-toggle="modal" data-target="#modalFeedback" class="btn btn-primary btn-gradient-primary" <?= $disabled ?>>
                    File feedback
                  </button>

                  <button type="button" class="btn btn-success btn-gradient-success" <?= $disabled ?> onclick="handleApproved('<?= $data->id ?>', '<?= $user->role ?>')">
                    Approve
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="modal fade" id="modalFeedback">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">File feedback</h5>
                </div>
                <form method="POST" id="feedback-form">
                  <div class="modal-body">
                    <input type="text" name="document_id" value="<?= $data->id ?>" hidden readonly>
                    <input type="text" name="role" value="<?= $user->role ?>" hidden readonly>

                    <div class="form-group">
                      <textarea type="text" class="form-control form-control-sm summernote" name="feedback"></textarea>
                    </div>
                  </div>
                  <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-primary btn-gradient-primary m-1" onclick="handleFileFeedback($(this))">File</button>
                    <button type="button" class="btn btn-danger btn-gradient-danger m-1" data-dismiss="modal">Cancel</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <!-- /.card-body -->
</div>