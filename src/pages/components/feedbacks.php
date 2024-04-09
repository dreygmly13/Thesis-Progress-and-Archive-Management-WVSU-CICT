<?php
if ($feedbackData->rating_type == "concept") :
?>
  <!-- <label class="control-label">Rating</label>
  <table class="table table-bordered">
    <thead>
      <caption style="color: black; text-align: center; caption-side: top; border: 1px solid #dee2e6">
        Group Rating
      </caption>
      <tr>
        <th>Criteria</th>
        <th>Max Points</th>
        <th>Panel score</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $groupRating = json_decode($feedbackData->group_grade, true);
      $total = 0;
      foreach ($groupRating as $ratingData) :
        $total += intval($ratingData["grade"]);
      ?>
        <tr>
          <td><?= $ratingData["title"] ?></td>
          <td class="text-center"><?= $ratingData["max"] ?></td>
          <td class="text-center"><?= $ratingData["grade"] ?></td>
        </tr>
      <?php endforeach; ?>
      <tr>
        <td colspan="2">Total</td>
        <td class="text-center"><?= $total ?></td>
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
      <?php
      $individualGrades = json_decode($feedbackData->individual_grade, true);
      foreach ($individualGrades as $individualGrade) :
        $user_details = get_user_by_id($individualGrade["id"]);
        $userDetailsFullName = ucwords("$user_details->first_name " . ($user_details->middle_name != null ? $user_details->middle_name[0] . "." : "") . " $user_details->last_name");
      ?>
        <tr>
          <td>
            <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
              <div class="mr-1">
                <img src="<?= $user_details->avatar != null ? $SERVER_NAME . $user_details->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
              </div>
              <div>
                <?= $userDetailsFullName ?>
              </div>
            </div>
          </td>
          <td class="text-center"><?= $individualGrade["grade"] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table> -->
<?php elseif ($feedbackData->rating_type == "final") : ?>
  <label class="control-label">Rating</label>
  <table class="table table-bordered">
    <thead>
      <caption class="p-0" style="color: black; text-align: center; caption-side: top; border: 1px solid #dee2e6">
        <strong>
          <pre>Rating Scale:	6=Excellent;    5=Very Good;    4=Good;    3=Fair;    2=Poor;    1=Very Poor</pre>
        </strong>
      </caption>
      <tr>
        <th>Areas</th>
        <th class="text-center">Rating</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $groupRating = json_decode($feedbackData->group_grade, true);
      $groupTotal = 0;
      $groupCount = 0;
      foreach ($groupRating as $ratingData) :
      ?>
        <tr>
          <td colspan="3">
            <strong>
              <?= $ratingData["title"] ?>
            </strong>
          </td>
        </tr>
        <?php
        for ($i = 0; $i < count($ratingData["ratings"]); $i++) :
          $rating = $ratingData["ratings"][$i];
          $groupCount++;
          $groupTotal += intval($rating["rating"]);
        ?>
          <tr>
            <td class="v-align-middle">
              <h6 style="font-weight: bold;">
                <?= $rating["title"] ?>
              </h6>
              <?= $rating["description"] ?>
            </td>
            <td class="text-center" style="vertical-align: middle;"><?= $rating["rating"] ?></td>
          </tr>
      <?php
        endfor;
      endforeach; ?>
      <tr>
        <td style="font-weight: bold;">Total</td>
        <td colspan="2" class="text-center" style="font-weight: bold;"><?= $groupTotal ?></td>
      </tr>
      <tr>
        <td style="font-weight: bold;">Average</td>
        <td colspan="2" class="text-center" style="font-weight: bold;"><?= $groupTotal / $groupCount ?></td>
      </tr>
    </tbody>
  </table>
<?php else : ?>
  <label class="control-label">Rating</label>
  <table class="table table-bordered">
    <thead>
      <caption class="p-0" style="color: black; text-align: center; caption-side: top; border: 1px solid #dee2e6">
        <strong>
          <pre>Rating Scale:	5=Excellent;    4=Very Good;    3=Good;    2=Poor;    1=Very Poor</pre>
        </strong>
      </caption>
      <tr>
        <th>Areas</th>
        <th class="text-center">Rating</th>
        <th>Remarks</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $groupRating = json_decode($feedbackData->group_grade, true);
      $groupTotal = 0;
      $groupCount = 0;
      foreach ($groupRating as $ratingData) :
      ?>
        <tr>
          <td colspan="3">
            <strong>
              <?= $ratingData["title"] ?>
            </strong>
          </td>
        </tr>
        <?php
        for ($i = 0; $i < count($ratingData["ratings"]); $i++) :
          $rating = $ratingData["ratings"][$i];
          $groupCount++;
          $groupTotal += intval($rating["rating"]);
        ?>
          <tr>
            <td><?= $rating["title"] ?></td>
            <td class="text-center" style="vertical-align: middle;"><?= $rating["rating"] ?></td>
            <?php if ($i == 0) : ?>
              <td rowspan="<?= count($ratingData["ratings"]) ?>">
                <?= $ratingData["remarks"] ?>
              </td>
            <?php endif; ?>
          </tr>
      <?php
        endfor;
      endforeach; ?>
      <tr>
        <td style="font-weight: bold;">Total</td>
        <td colspan="2" class="text-center" style="font-weight: bold;"><?= $groupTotal ?></td>
      </tr>
      <tr>
        <td style="font-weight: bold;">Average</td>
        <td colspan="2" class="text-center" style="font-weight: bold;"><?= $groupTotal / $groupCount ?></td>
      </tr>
      <tr>
        <td colspan="3">
          <strong>
            Individual Performance
          </strong>
        </td>
      </tr>
      <?php
      $individualGrades = json_decode($feedbackData->individual_grade, true);
      $individualTotal = 0;
      $count = 0;
      foreach ($individualGrades as $individualGrade) :
        $user_details = get_user_by_id($individualGrade["id"]);
        $individualTotal += intval($individualGrade["rating"]);
        $count++;
      ?>
        <tr>
          <td>
            <div class="mt-2 mb-2 d-flex justify-content-start align-items-center">
              <div class="mr-1">
                <img src="<?= $user_details->avatar != null ? $SERVER_NAME . $user_details->avatar : $SERVER_NAME . "/public/default.png" ?>" class="img-circle" style="width: 3rem; height: 3rem" alt="User Image">
              </div>
              <div>
                <?= ucwords("$user_details->first_name " . ($user_details->middle_name != null ? $user_details->middle_name[0] . "." : "") . " $user_details->last_name") ?>
              </div>
            </div>
          </td>
          <td class="text-center" style="vertical-align: middle;"><?= $individualGrade["rating"] ?></td>
          <td><?= $individualGrade["remarks"] ?></td>
        </tr>
      <?php endforeach ?>
      <tr>
        <td style="font-weight: bold;">Total</td>
        <td colspan="2" class="text-center" style="font-weight: bold;"><?= $individualTotal ?></td>
      </tr>
      <tr>
        <td style="font-weight: bold;">Average</td>
        <td colspan="2" class="text-center" style="font-weight: bold;"><?= $individualTotal / $count ?></td>
      </tr>
    </tbody>
  </table>
<?php endif; ?>