<?php
$leader = $isLeader ? $user : get_user_by_id($user->leader_id);
$documents = getAllSubmittedDocument($leader);
foreach ($documents as $document) :
  $color = "warning";

  if ($document->concept_status == "APPROVED") {
    $color = "success";
  } else if ($document->concept_status == "DECLINED") {
    $color = "danger";
  }
?>
  <div class="card card-outline card-primary shadow rounded-0">
    <div class="card-header">
      <div class="card-title">
        <h2>
          <strong>
            <?= ucwords($document->title) ?>
          </strong>
          <span class="badge badge-<?= $color ?> rounded-pill px-4" style="font-size: 18px">
            <em><?= strtoupper($document->concept_status) ?></em>
          </span>
        </h2>
      </div>
      <div class="card-tools">
        <a data-toggle="collapse" href="#document<?= $document->id ?>" aria-expanded="true" aria-controls="document<?= $document->id ?>" class="btn btn-link">
          <i class="fa fa-window-minimize"></i>
        </a>
        <a href="<?= $SERVER_NAME . "/pages/student/update-documents?doc_id=$document->id" ?>" class="btn btn-link">
          <i class="fa fa-edit"></i>
        </a>
      </div>
    </div>
    <div id="document<?= $document->id ?>" class="collapse show" aria-labelledby="heading-example">
      <div class="card-body rounded-0">
        <div class="container-fluid">
          <center>
            <img src="<?= $SERVER_NAME . $document->img_banner ?>" alt="Banner Image" id="banner-img" class="img-fluid border bg-gradient-dark">
          </center>
          <fieldset>
            <legend class="text-navy"> Type:</legend>
            <div class="pl-4">
              <?php
              $typeQ = mysqli_query(
                $conn,
                "SELECT * FROM types WHERE id=$document->type_id"
              );
              echo mysqli_num_rows($typeQ) > 0 ? mysqli_fetch_object($typeQ)->name : "";
              ?>
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
                  <?= ucwords("$leader->first_name " . ($leader->middle_name != null ? $leader->middle_name[0] . "." : "") . " $leader->last_name") ?>
                </div>
              </div>
            </div>
          </fieldset>
          <fieldset>
            <legend class="text-navy">Project Members:</legend>
            <div class="pl-4">
              <?php
              $memberData = json_decode(getMemberData($leader->group_number, $leader->id));
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
                <iframe src="<?= $SERVER_NAME . $document->project_document ?>#embedded=true&toolbar=0&navpanes=0" class="embed-responsive-item" id="pdfPreview" allowfullscreen></iframe>
              </div>
            </div>
          </fieldset>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>