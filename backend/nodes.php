<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include("conn.php");
date_default_timezone_set("Asia/Manila");
$dateNow = date("Y-m-d H:i:s");
$separator = "!I_I!";
$SERVER_NAME = "http://$_SERVER[SERVER_NAME]/west";
$ADMIN_ROLES = array(
  "instructor",
  "coordinator",
  "panel",
  "adviser",
);

$feedbacksDefault = json_encode(
  array(
    "feedback" => array(),
    "isApproved" => "false",
  )
);

/*
Feedback format

array(
  "message" => "",
  "token" => "",
  "isResolved" => false,
  "date" => "",
)
*/

if (isset($_GET['action'])) {
  try {
    switch ($_GET['action']) {
      case "logout":
        logout();
        break;
      case "student_registration":
        student_registration();
        break;
      case "login":
        login();
        break;
      case "updateUser":
        updateUser();
        break;
      case "addGroupMate":
        addGroupMate();
        break;
      case "deleteUser":
        deleteUser();
        break;
      case "getAllInstructor":
        getAllInstructor();
        break;
      case "getAllAdviser":
        getAllAdviser();
        break;
      case "sendToInstructor":
        sendToInstructor();
        break;
      case "getCurrentInstructorWithOther":
        getCurrentInstructorWithOther();
        break;
      case "addAdmin":
        addAdmin();
        break;
      case "editAdmin":
        updateUser();
        break;
      case "updatePassword":
        updatePassword();
        break;
      case "updateGroupPanel":
        updateGroupPanel();
        break;
      case "updateSystem":
        updateSystem();
        break;
      case "saveCategory":
        saveCategory();
        break;
      case "deleteCategory":
        deleteCategory();
        break;
      case "saveSchedule":
        saveSchedule();
        break;
      case "deleteSchedule":
        deleteSchedule();
        break;
      case "sendAdviserInvite":
        sendAdviserInvite();
        break;
      case "cancelAdvisorInvite":
        cancelAdvisorInvite();
        break;
      case "handleAdviserInvite":
        handleAdviserInvite();
        break;
      case "saveType":
        saveType();
        break;
      case "deleteType":
        deleteType();
        break;
      case "saveDocument":
        saveDocument();
        break;
      case "approvedDocument":
        approvedDocument();
        break;
      case "fileFeedback":
        fileFeedback();
        break;
      case "markFeedbackResolved":
        markFeedbackResolved();
        break;
      case "saveOldDocuments":
        saveOldDocuments();
        break;
      case "getChat":
        print_r(getChat());
        break;
      case "insertMessage":
        print_r(insertMessage());
        break;
      case "saveRating":
        saveRating();
        break;
      case "getPanelRatingType":
        getPanelRatingType();
        break;
      case "updatePanelRating":
        updatePanelRating();
        break;
      case "updateDocument":
        updateDocument();
        break;
      case "assignGroupNumber":
        assignGroupNumber();
        break;
      case "assignLeader":
        assignLeader();
        break;
      case "publishDocument":
        publishDocument();
        break;
      case "saveCourse":
        saveCourse();
        break;
      case "deleteCourse":
        deleteCourse();
        break;
      default:
        null;
        break;
    }
  } catch (Exception $e) {
    $response["success"] = false;
    $response["message"] = $e->getMessage();
  }
}

function deleteCourse()
{
  global $conn, $_POST;

  $query = mysqli_query(
    $conn,
    "DELETE FROM courses WHERE course_id = '$_POST[id]'"
  );

  if ($query) {
    $response["success"] = true;
    $response["message"] = "Course deleted successfully";
  } else {
    $response["success"] = false;
    $response["message"] = mysqli_error($conn);
  }

  returnResponse($response);
}

function saveCourse()
{
  global $conn;

  $action = $_POST["action"];
  $courseId = isset($_POST['courseId']) ? $_POST['courseId'] : null;
  $name = strtoupper($_POST["name"]);
  $short_name = strtoupper($_POST["short_name"]);

  if (!hasCourse($name, $short_name, $courseId)) {
    $qStr = $action == "add" && $courseId == null ? "INSERT INTO courses(`name`, short_name) VALUES('$name', '$short_name')" : "UPDATE courses SET `name`='$name', short_name='$short_name' WHERE course_id='$courseId'";
    $query = mysqli_query(
      $conn,
      $qStr
    );

    if ($query) {
      $response["success"] = true;
      $response["message"] = "Course " . ($action == "add" ? "added" : "updated") . " successfully";
    } else {
      $response["success"] = false;
      $response["message"] = mysqli_error($conn);
    }
  } else {
    $response["success"] = false;
    $response["message"] = "Course already exist.";
  }

  returnResponse($response);
}

function hasCourse($courseName, $shortName, $courseId = null)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM courses WHERE " . ($courseId != null ? "course_id !='$courseId' and " : "") . "  (`name` LIKE '%$courseName%' or `name` LIKE '%$shortName%')"
  );

  if (mysqli_num_rows($query) > 0) {
    return true;
  }

  return false;
}

function publishDocument()
{
  global $conn, $_POST;

  $query = mysqli_query(
    $conn,
    "UPDATE documents SET publish_status='PUBLISHED' WHERE id='$_POST[documentId]'"
  );

  if ($query) {
    $response["success"] = true;
    $response["message"] = "Document successfully published.";
  } else {
    $response["success"] = false;
    $response["message"] = mysqli_error($conn);
  }

  returnResponse($response);
}

function getBarData()
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT 
    t.name,
    d.year 
    FROM documents d 
    INNER JOIN types t
    ON
    d.type_id = t.id 
    WHERE d.publish_status = 'PUBLISHED'"
  );

  $data = array();

  while ($row = mysqli_fetch_object($query)) {
    array_push($data, $row);
  }

  return $data;
}

function getTotalCategories()
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM category_list"
  );

  if (mysqli_num_rows($query) > 0) {
    return mysqli_num_rows($query);
  }

  return 0;
}

function getTodayScheduledTask()
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM schedule_list"
  );

  $count = 0;
  while ($row = mysqli_fetch_object($query)) {
    if (date("m-d-Y", strtotime($row->schedule_from)) == date("m-d-Y")) {
      $count++;
    }
  }

  return $count;
}

function getUpcomingScheduledTask()
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM schedule_list"
  );

  $count = 0;
  while ($row = mysqli_fetch_object($query)) {
    if (date("m-d-Y", strtotime($row->schedule_from)) > date("m-d-Y")) {
      $count++;
    }
  }

  return $count;
}

function assignLeader()
{
  global $conn, $_POST, $_SESSION;

  $currentUser = get_user_by_username($_SESSION['username']);

  $leaderId = $_POST["leaderId"];
  $groupNumber = $_POST["groupNumber"];

  $getStudentsQ = mysqli_query(
    $conn,
    "SELECT * FROM users WHERE `role`='student' and group_number='$groupNumber' and isLeader is NULL and leader_id is NULL"
  );

  $memberIds = array();

  $query = null;
  while ($student = mysqli_fetch_object($getStudentsQ)) {
    if ($student->id == $leaderId) {
      $query = mysqli_query(
        $conn,
        "UPDATE users SET isLeader='1' WHERE id='$student->id'"
      );
    } else {
      $query = mysqli_query(
        $conn,
        "UPDATE users SET leader_id='$leaderId' WHERE id='$student->id'"
      );
      array_push($memberIds, $student->id);
    }
  }

  if ($query) {
    $response["success"] = true;
    $response["message"] = "Leader successfully assigned";
    addThesisGroup($leaderId, $groupNumber, json_encode($memberIds), $currentUser->id);
  } else {
    $response["success"] = false;
    $response["message"] = mysqli_error($conn);
  }

  returnResponse($response);
}

function addThesisGroup($leaderId, $groupNumber, $memberIds, $instructorId)
{
  global $conn;

  mysqli_query(
    $conn,
    "INSERT INTO thesis_groups(group_leader_id, group_number, group_member_ids, instructor_id) VALUES('$leaderId', '$groupNumber', '$memberIds', '$instructorId')"
  );
}

function assignGroupNumber()
{
  global $conn, $_POST;

  $groupNumber = $_POST["groupNumber"];
  $userIds = $_POST["userIds"];

  $query = mysqli_query(
    $conn,
    "UPDATE users SET group_number='$groupNumber' WHERE id in(" . implode(', ', $userIds) . ")"
  );

  if ($query) {
    $response["success"] = true;
    $response["message"] = "Student(s) successfully assigned groups";
  } else {
    $response["success"] = false;
    $response["message"] = mysqli_error($conn);
  }

  returnResponse($response);
}

function getInstructorHandledSections($userId)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM instructor_sections WHERE instructor_id='$userId'"
  );

  if (mysqli_num_rows($query) > 0) {
    return json_decode(mysqli_fetch_object($query)->sections, true);
  }

  return null;
}

function isNotYetAssignedGroup($userId)
{
  $currentUser = get_user_by_id($userId);

  if ($currentUser->isLeader == null && $currentUser->leader_id == null && $currentUser->group_number == null) {
    return true;
  }

  return false;
}

function isMember($userId)
{
  $currentUser = get_user_by_id($userId);

  if ($currentUser->isLeader == null && $currentUser->leader_id != null) {
    return true;
  }

  return false;
}

function isLeader($userId)
{
  $currentUser = get_user_by_id($userId);

  if ($currentUser->isLeader != null && $currentUser->leader_id == null) {
    return true;
  }

  return false;
}

function updateDocument()
{
  global $conn, $_POST, $_FILES, $separator;

  $documentId = $_POST['documentId'];

  $submittedDocument = getDocumentById($documentId);

  $title = $_POST["title"];
  $type = $_POST["type"];
  $year = $_POST["year"];
  $description = mysqli_escape_string($conn, nl2br($_POST["description"]));
  $banner = $_FILES["banner"];
  $pdf = $_FILES["pdfFile"];

  $query = null;

  if (intval($banner["error"]) == 0 && intval($pdf["error"]) == 0) {
    $bannerFile = date("mdY-his") . $separator . basename($banner['name']);
    $bannerDir = "../media/documents/banner/";
    $bannerUrl = "/media/documents/banner/$bannerFile";

    $pdfFile = date("mdY-his") . $separator . basename($pdf['name']);
    $pdfDir = "../media/documents/files/";
    $pdfUrl = "/media/documents/files/$pdfFile";

    if (!is_dir($bannerDir)) {
      mkdir($bannerDir, 0777, true);
    }

    if (!is_dir($pdfDir)) {
      mkdir($pdfDir, 0777, true);
    }

    if (move_uploaded_file($banner['tmp_name'], "$bannerDir/$bannerFile") && move_uploaded_file($pdf['tmp_name'], "$pdfDir/$pdfFile")) {
      $query = mysqli_query(
        $conn,
        "UPDATE documents SET title='$title', `type_id`='$type', `year`='$year', `description`='$description', img_banner='$bannerUrl', project_document='$pdfUrl' WHERE id='$documentId'"
      );
    }
  } else {
    $query = mysqli_query(
      $conn,
      "UPDATE documents SET title='$title', `type_id`='$type', `year`='$year', `description`='$description' WHERE id='$documentId'"
    );
  }

  if ($query) {
    resetPanelComments($documentId, $submittedDocument->leader_id);
    $response["success"] = true;
    $response["message"] = "Document successfully updated.";
  } else {
    $response["success"] = false;
    $response["message"] = mysqli_error($conn);
  }

  returnResponse($response);
}

function resetPanelComments($documentId, $leaderId)
{
  global $conn;
  $documentStatus = getDocumentStatus($leaderId, $documentId);

  $typeDisapproved = "";

  foreach ($documentStatus as $index => $value) {
    if ($value["status"] == "DISAPPROVED") {
      $typeDisapproved = $index;
      break;
    }
  }

  if ($typeDisapproved != "") {
    $resetFeedbackAndRatingQ = mysqli_query(
      $conn,
      "UPDATE documents SET adviser_feedback=NULL, instructor_feedback=NULL, panel_rate_status=NULL WHERE id='$documentId'"
    );

    if ($resetFeedbackAndRatingQ) {
      $panelIds = getPanelAssigned($leaderId);

      if ($panelIds != null) {
        mysqli_query(
          $conn,
          "DELETE FROM panel_ratings WHERE " . ($typeDisapproved != "" ? "rating_type='$typeDisapproved' and" : "") . " panel_id in(" . (implode(', ', $panelIds)) . ")"
        );
      }
    }
  }
}

function getPanelAssigned($leaderId)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM thesis_groups WHERE group_leader_id='$leaderId'"
  );

  if (mysqli_num_rows($query) > 0) {
    return json_decode(mysqli_fetch_object($query)->panel_ids, true);
  }

  return null;
}

function getDocumentStatus($leaderId, $documentId)
{
  global $conn;

  $status = array();
  $query = mysqli_query(
    $conn,
    "SELECT * FROM thesis_groups WHERE group_leader_id='$leaderId'"
  );
  if (mysqli_num_rows($query) > 0) {
    $panel_ids = json_decode(mysqli_fetch_object($query)->panel_ids);
    $ratingTypes = array("concept", "20percent", "50percent", "final");

    foreach ($ratingTypes as $ratingType) {
      $approved = 0;
      $disapproved = 0;

      $query = mysqli_query(
        $conn,
        "SELECT * FROM panel_ratings WHERE leader_id='$leaderId' and document_id='$documentId' and rating_type='$ratingType'"
      );

      while ($row = mysqli_fetch_object($query)) {
        if (strtolower($row->action) == "approved") {
          $approved++;
        } else if (strtolower($row->action) == "disapproved") {
          $disapproved++;
        }

        if (!in_array(panelNameType($ratingType), $status)) {
          $status[$ratingType] = array(
            "title" => panelNameType($ratingType),
            "status" => ""
          );
        }
      }
      if (($approved + $disapproved) == count($panel_ids)) {
        $documentRateStatus = $approved > $disapproved ? "APPROVED" : "DISAPPROVED";
        $status[$ratingType]["status"] = $documentRateStatus;
      }
    }
  }

  return $status;
}

function updatePanelRating()
{
  global $conn, $_POST;

  $ratingId = $_POST["ratingId"];

  $panelRatings = getPanelRatingById($ratingId);
  $nameType = panelNameType($panelRatings->rating_type);

  $comment = nl2br($_POST["comment"]);
  $action = $panelRatings->rating_type == "final" ? "Approved" : $_POST["action"];

  $newIndividualGrade = $panelRatings->rating_type == "final" || $panelRatings->rating_type == "concept" ? "null" : json_encode(newIndividualGrade($panelRatings, $panelRatings->rating_type, $_POST));

  $newGroupGrade = 'null';

  if ($panelRatings->rating_type == "final") {
    $newGroupGrade = mysqli_escape_string($conn, json_encode(newFinalGroupGrade($panelRatings, $_POST)));
  } else if ($panelRatings->rating_type != "final" && $panelRatings->rating_type != "concept") {
    $newGroupGrade = json_encode(newGroupGrade($panelRatings, $_POST, $panelRatings->rating_type));
  }

  $query = mysqli_query(
    $conn,
    "UPDATE panel_ratings SET comment='$comment', `action`='$action', group_grade='$newGroupGrade', individual_grade='$newIndividualGrade' WHERE rating_id='$ratingId'"
  );

  if ($query) {
    $response["success"] = true;
    $response["message"] = "$nameType rating updated successfully.";
    updateDocumentPanelStatus($panelRatings->leader_id, $panelRatings->rating_type, $panelRatings->document_id);
    updateDocumentsToPublished($panelRatings->document_id, $panelRatings->leader_id, $panelRatings->rating_type);
    if (hasRateAllPanelInConcept(get_user_by_id($panelRatings->leader_id))) {
      updateDocumentConcept(get_user_by_id($panelRatings->leader_id));
    }
  } else {
    $response["success"] = false;
    $response["message"] = mysqli_error($conn);
  }

  returnResponse($response);
}

function newFinalGroupGrade($panelRatings, $post)
{
  $oldGroupGrades = json_decode($panelRatings->group_grade, true);
  $newGroupGrade = array();

  foreach ($oldGroupGrades as $index => $value) {
    $newGroupGrade[$index] = array(
      "title" => $value["title"],
      "ratings" => array()
    );
    foreach ($value["ratings"] as $rating) {
      array_push($newGroupGrade[$index]["ratings"], array(
        "title" => $rating["title"],
        "description" => $rating["description"],
        "name" => $rating["name"],
        "rating" => $post[$rating["name"]]
      ));
    }
  }

  return $newGroupGrade;
}

function newIndividualGrade($panelRatings, $ratingType, $post = null)
{
  $oldIndividualGrade = json_decode($panelRatings->individual_grade, true);
  $newIndividualGrade = array();

  if ($ratingType == "concept") {
    for ($i = 0; $i < count($oldIndividualGrade); $i++) {
      $gradeName = $oldIndividualGrade[$i]["id"] . "_grade";
      array_push($newIndividualGrade, array(
        "id" => $oldIndividualGrade[$i]["id"],
        "name" => $oldIndividualGrade[$i]["name"],
        "grade" => $post[$gradeName]
      ));
    }
  } else {
    for ($i = 0; $i < count($oldIndividualGrade); $i++) {
      $remarksName = $oldIndividualGrade[$i]["id"] . "_remarks";
      array_push($newIndividualGrade, array(
        "id" => $oldIndividualGrade[$i]["id"],
        "name" => $oldIndividualGrade[$i]["name"],
        "rating" => $post[$oldIndividualGrade[$i]["id"]],
        "remarks" => $post[$remarksName]
      ));
    }
  }

  return $newIndividualGrade;
}

function newGroupGrade($panelRatings, $post, $ratingType)
{
  $oldGroupGrades = json_decode($panelRatings->group_grade, true);
  $newGroupGrade = array();

  if ($ratingType == "concept") {
    foreach ($oldGroupGrades as $oldGroupGrade) {
      array_push($newGroupGrade, array(
        "title" => $oldGroupGrade["title"],
        "name" => $oldGroupGrade["name"],
        "max" => intval($oldGroupGrade["max"]),
        "grade" => $post[$oldGroupGrade["name"]]
      ));
    }
  } else {
    $count = 0;
    foreach ($oldGroupGrades as $index => $value) {
      $remarksName = $index . "_remarks";
      $newGroupGrade[$index] = array(
        "title" => $value["title"],
        "remarks" => $post[$remarksName],
        "ratings" => array()
      );
      foreach ($value["ratings"] as $rating) {
        array_push($newGroupGrade[$index]["ratings"], array(
          "title" => $rating["title"],
          "name" => $rating["name"],
          "rating" => $post[$rating["name"]]
        ));
      }
      $count++;
    }
  }

  return $newGroupGrade;
}

function getPanelRatingType()
{
  global $conn;

  $ratingTypes = array();

  $query = mysqli_query(
    $conn,
    "SELECT * FROM panel_ratings WHERE panel_id='$_POST[panel_id]' and document_id='$_POST[document_id]' GROUP BY rating_type"
  );

  while ($row = mysqli_fetch_object($query)) {
    array_push($ratingTypes, array($row->rating_type => panelNameType($row->rating_type)));
  }

  returnResponse($ratingTypes);
}

function getPanelRatingById($ratingId)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM panel_ratings WHERE rating_id='$ratingId'"
  );

  if (mysqli_num_rows($query) > 0) {
    return mysqli_fetch_object($query);
  }

  return null;
}

function getPanelRating($panelId, $documentId)
{
  global $conn;

  $feedbackData = array();
  $query = mysqli_query(
    $conn,
    "SELECT * FROM panel_ratings WHERE document_id='$documentId' and panel_id='$panelId'"
  );

  if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_object($query)) {
      array_push($feedbackData, $row);
    }
  }
  return $feedbackData;
}

function saveRating()
{
  global $conn, $_POST;
  $post = json_decode($_POST["data"], true);

  $documentId = $post["documentId"];
  $panelId = $post["panelId"];
  $type = $post["type"];

  $nameType = panelNameType($type);

  if (!hasPanelRating($panelId, $type, $documentId)) {
    $leaderId = $post["leaderId"];

    $comment = isset($post["comment"]) ? nl2br($post["comment"]) : NULL;
    $actionTaken = $type != "final" ? $post["actionTaken"] : 'Approved';
    $individualGrade = isset($post["individualGrade"]) && $type != "final" ? json_encode($post["individualGrade"]) : 'null';

    $groupGrade = 'null';
    if (isset($post["otherGroupGrade"]) && $type != "final") {
      $groupGrade = json_encode($post["otherGroupGrade"]);
    } else if (isset($post["finalGroupGrade"]) && $type == "final") {
      $groupGrade = mysqli_escape_string($conn, json_encode($post["finalGroupGrade"]));
    }

    $query = mysqli_query(
      $conn,
      "INSERT INTO panel_ratings(document_id, leader_id, panel_id, rating_type, comment, `action`, group_grade, individual_grade) VALUES('$documentId', '$leaderId', '$panelId', '$type', '$comment', '$actionTaken', '$groupGrade', '$individualGrade')"
    );

    if ($query) {
      $response["success"] = true;
      $response["message"] = "Group successfully rated.";
      updateDocumentPanelStatus($leaderId, $type, $documentId);
      updateDocumentsToPublished($documentId, $leaderId, $type);
      if (hasRateAllPanelInConcept(get_user_by_id($leaderId))) {
        updateDocumentConcept(get_user_by_id($leaderId));
      }
    } else {
      $response["success"] = false;
      $response["message"] = mysqli_error($conn);
    }
  } else {
    $response["success"] = false;
    $response["message"] = "You already rate the group for $nameType";
  }

  returnResponse($response);
}

function updateDocumentConcept($leader)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * 
    FROM 
    panel_ratings p
    INNER JOIN
    documents d
    ON p.document_id = d.id
    WHERE p.rating_type = 'concept'
    and p.action = 'Approved'
    and p.leader_id = '$leader->id'"
  );

  $docIds = array();

  while ($row = mysqli_fetch_object($query)) {
    if (!array_key_exists($row->document_id, $docIds)) {
      $docIds[$row->document_id] = array("approved" => 1);
    } else {
      $docIds[$row->document_id]["approved"]++;
    }
  }

  $value = max($docIds);
  $key = array_search($value, $docIds);

  try {
    $docQ = mysqli_query(
      $conn,
      "SELECT id, leader_id FROM documents WHERE leader_id='$leader->id'"
    );

    $allDocData = mysqli_fetch_all($docQ);

    $allDocIds = array();
    foreach ($allDocData as $docData => $data) {
      array_push($allDocIds, $data[0]);
    }

    foreach ($allDocIds as $docId => $id) {
      mysqli_query(
        $conn,
        "UPDATE documents SET concept_status='" . ($key == $id ? "APPROVED" : "DECLINED") . "' WHERE id='$id'"
      );
    }
  } catch (Exception $e) {
  }
}

function hasRateAllPanelInConcept($leader)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM thesis_groups WHERE group_leader_id='$leader->id'"
  );

  if (mysqli_num_rows($query) > 0) {
    $panel_ids = mysqli_fetch_object($query)->panel_ids;
    if ($panel_ids) {
      $ratingCount = 0;
      foreach (json_decode($panel_ids) as $panel_id) {
        $ratingQ = mysqli_query(
          $conn,
          "SELECT * FROM panel_ratings WHERE rating_type='concept' and panel_id='$panel_id' and leader_id='$leader->id'"
        );
        if (mysqli_num_rows($ratingQ) == 3) {
          $ratingCount++;
        }
      }
      if ($ratingCount == count(json_decode($panel_ids))) {
        return true;
      }
    }
    return false;
  }
  return false;
}

function panelNameType($type)
{
  $nameType = "";

  switch ($type) {
    case "concept":
      $nameType = "Concept Proposal";
      break;
    case "20percent":
      $nameType = "20% Progress";
      break;
    case "50percent":
      $nameType = "50% Progress";
      break;
    case "final":
      $nameType = "Final";
      break;
    default:
      null;
  }

  return $nameType;
}

function updateDocumentsToPublished($documentId, $leaderId, $type)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM thesis_groups WHERE group_leader_id='$leaderId'"
  );
  if (mysqli_num_rows($query) > 0) {
    $panel_ids = json_decode(mysqli_fetch_object($query)->panel_ids);
    $approved = 0;
    $disapproved = 0;
    $countFinalType = 0;

    foreach ($panel_ids as $panel_id) {
      $panel_ratingQ = mysqli_query(
        $conn,
        "SELECT panel_id, `rating_type`, document_id, leader_id, `action` FROM panel_ratings WHERE panel_id='$panel_id' and `rating_type`='$type' and document_id='$documentId' and leader_id='$leaderId'"
      );
      while ($row = mysqli_fetch_object($panel_ratingQ)) {
        if (strtolower($row->action) == "approved") {
          $approved++;
        } else if (strtolower($row->action) == "disapproved") {
          $disapproved++;
        }

        if ($row->rating_type == "final") {
          $countFinalType++;
        }
      }
    }

    if (($approved + $disapproved) == count($panel_ids) && $approved > $disapproved && $countFinalType == count($panel_ids)) {
      mysqli_query(
        $conn,
        "UPDATE documents SET publish_status='TO PUBLISH' WHERE id='$documentId'"
      );
    }
  }
}

function updateDocumentPanelStatus($leaderId, $type, $documentId)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM thesis_groups WHERE group_leader_id='$leaderId'"
  );
  if (mysqli_num_rows($query) > 0) {
    $panel_ids = json_decode(mysqli_fetch_object($query)->panel_ids);
    $approved = 0;
    $disapproved = 0;

    foreach ($panel_ids as $panel_id) {
      $panel_ratingQ = mysqli_query(
        $conn,
        "SELECT panel_id, `rating_type`, document_id, leader_id, `action` FROM panel_ratings WHERE panel_id='$panel_id' and `rating_type`='$type' and document_id='$documentId' and leader_id='$leaderId'"
      );
      while ($row = mysqli_fetch_object($panel_ratingQ)) {
        if (strtolower($row->action) == "approved") {
          $approved++;
        } else if (strtolower($row->action) == "disapproved") {
          $disapproved++;
        }
      }
    }

    if (($approved + $disapproved) == count($panel_ids)) {
      $documentRateStatus = $approved > $disapproved ? "APPROVED" : "DISAPPROVED";

      mysqli_query(
        $conn,
        "UPDATE documents SET panel_rate_status='$documentRateStatus' WHERE id='$documentId'"
      );
    }
  }
}

function hasPanelRating($panelId, $ratingType, $documentId)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM panel_ratings WHERE document_id='$documentId' and panel_id='$panelId' and rating_type='$ratingType'"
  );

  if (mysqli_num_rows($query) > 0) {
    return true;
  }
  return false;
}

function generateTextareaTdRadio($len, $name, $grade = null, $type = "")
{
  $tds = array();

  for ($i = 1; $i <= $len; $i++) {
    array_push($tds, "<td class='v-align-middle'>
                        <div class='form-group' style='margin: 0;'>
                          <input type='radio' value='$i' name='$name' class='radio-big rating-radio " . ($type != "" ? $type : "") . "' " . ($grade != null && $i == $grade ? "checked" : "") . " required>
                        </div>
                      </td>");
  }

  return implode("\n", $tds);
}

function getPanelAssignedGroup($userId)
{
  global $conn;
  $assignedGroupData = array();
  $query = mysqli_query(
    $conn,
    "SELECT 
    tg.group_leader_id,
    tg.group_number,
    tg.panel_ids,
    d.* FROM thesis_groups tg 
    INNER JOIN documents d 
    ON tg.group_leader_id = d.leader_id"
  );

  if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_object($query)) {
      $panel_ids = json_decode($row->panel_ids);
      if ($panel_ids) {
        if (in_array($userId, $panel_ids)) {
          array_push($assignedGroupData, $row);
        }
      }
    }
  }

  return count($assignedGroupData) > 0 ? $assignedGroupData : null;
}

function getLatestMessageData($currentUserId, $otherId)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM chat c 
    WHERE (c.outgoing_id = $currentUserId AND c.incoming_id = $otherId)
    OR (c.outgoing_id = $otherId AND c.incoming_id = $currentUserId) ORDER BY chat_id DESC LIMIT 1"
  );

  if (mysqli_num_rows($query) > 0) {
    return mysqli_fetch_object($query);
  }

  return null;
}

function time_elapsed_string($datetime, $full = false)
{
  $now = new DateTime;
  $ago = new DateTime($datetime);
  $diff = $now->diff($ago);

  $diff->w = floor($diff->d / 7);
  $diff->d -= $diff->w * 7;

  $string = array(
    'y' => 'year',
    'm' => 'month',
    'w' => 'week',
    'd' => 'day',
    'h' => 'hour',
    'i' => 'minute',
    's' => 'second',
  );
  foreach ($string as $k => &$v) {
    if ($diff->$k) {
      $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
    } else {
      unset($string[$k]);
    }
  }

  if (!$full) $string = array_slice($string, 0, 1);
  return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function insertMessage()
{
  global $conn, $_POST, $_FILES, $_SESSION, $separator;
  date_default_timezone_set("Asia/Manila");
  error_reporting(0);
  $resp = array("success" => false, "message" => "");

  $sender = get_user_by_username($_SESSION['username']);
  $message = mysqli_escape_string($conn, $_POST["message"]);

  $isFileUploaded = true;

  if ($_FILES["files"]["error"][0] == 0) {
    $file_ary = reArrayFiles($_FILES['files']);
    foreach ($file_ary as $file) {

      $uploadFileName = mysqli_escape_string($conn, date("mdY-his") . $separator . basename($file['name']));
      $messageType = strpos(strval($file["type"]), "image") !== false ? "image" : "file";
      $uploadFile = uploadFile($file["tmp_name"], "../media/chat/$uploadFileName");

      if ($uploadFile) {
        mysqli_query(
          $conn,
          "INSERT INTO chat(incoming_id, outgoing_id, `message`, message_type) VALUES('$_POST[incoming_id]', '$sender->id', '$uploadFileName', '$messageType')"
        );
      } else {
        $isFileUploaded = false;
        $resp["message"] = "File could not upload.\nPlease rename file and resend it.";
      }
    }
    if ($_POST["message"] != "") {
      mysqli_query(
        $conn,
        "INSERT INTO chat(incoming_id, outgoing_id, `message`, message_type) VALUES('$_POST[incoming_id]', '$sender->id', '$message', 'text')"
      );
    }
  } else {
    mysqli_query(
      $conn,
      "INSERT INTO chat(incoming_id, outgoing_id, `message`, message_type) VALUES('$_POST[incoming_id]', '$sender->id', '$message', 'text')"
    );
  }

  if (mysqli_error($conn) == "" && $isFileUploaded) {
    $resp["success"] = true;
  }

  return json_encode($resp);
}

function uploadFile($tmp_file, $fileName)
{
  if (!is_dir("../media/chat/")) {
    mkdir("../media/chat/", 0777, true);
  }

  return move_uploaded_file($tmp_file, $fileName);
}

function reArrayFiles($file_post)
{
  $file_ary = array();
  $file_count = count($file_post['name']);
  $file_keys = array_keys($file_post);

  for ($i = 0; $i < $file_count; $i++) {
    foreach ($file_keys as $key) {
      $file_ary[$i][$key] = $file_post[$key][$i];
    }
  }

  return $file_ary;
}

function getChat()
{
  global $conn, $_GET, $_SESSION, $SERVER_NAME;
  $currentUser = get_user_by_username($_SESSION['username']);

  $html = "";
  $empty = "
  <h5 style='text-align:center'>No message to show.<br> <small>Start chatting now.</small></h5>
  ";

  $query = mysqli_query(
    $conn,
    "SELECT * FROM chat c LEFT JOIN users u ON u.id = c.outgoing_id
    WHERE (c.outgoing_id = $currentUser->id AND c.incoming_id = {$_GET['incoming']})
    OR (c.outgoing_id = {$_GET['incoming']} AND c.incoming_id = $currentUser->id) ORDER BY chat_id"
  );

  while ($chat = mysqli_fetch_object($query)) {
    $profile = $chat->avatar != null ? $SERVER_NAME . $chat->avatar : $SERVER_NAME . "/public/default.png";

    $time = date_format(
      date_create($chat->date_created),
      "M d, Y h:i A"
    );
    if ($chat->outgoing_id === $currentUser->id) {
      $html .= '<div class="chat outgoing chatItem ">
                  <div class="details">
                      <p>
                        ' . formatChatMessage($chat->message, $chat->message_type) . '
                        <span class="time">
                          <br>
                          <small>' . $time . '</small>
                        </span>
                      </p>
                      
                  </div>
                  </div>';
    } else {
      $html .= '<div class="chat incoming chatItem">
                  <img src="' . $profile . '" alt="" class="avatar">
                  <div class="details">
                      <p>
                        ' . formatChatMessage($chat->message, $chat->message_type) . '
                      <span class="time">
                        <br>
                        <small>' . $time . '</small>
                      </span>
                      </p>
                  </div>
                  </div>';
    }
  }

  return $html == "" ? $empty : $html;
}

function formatChatMessage($message, $messageType)
{
  global $separator, $SERVER_NAME;

  $respMessage = "";

  if ($messageType == "file") {
    $fileName = explode($separator, $message)[1];
    $respMessage = "<a class='text-primary text-underline' href='$SERVER_NAME/media/chat/$message' download='" . $fileName . "'>" . $fileName . "</a>";
  } else if ($messageType == "image") {
    // $fileName = explode($separator, $message)[1];
    $respMessage = "<img src='$SERVER_NAME/media/chat/$message' class='img-fluid banner-img bg-gradient-dark border' style='border-radius: 0' onclick='handlePreview(\"$SERVER_NAME/media/chat/$message\")'>";
  } else {
    $respMessage = $message;
  }

  return $respMessage;
}

function getPageCount($searchVal = "", $limit)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM documents WHERE " . ($searchVal == "" ? "" : "title LIKE '%$searchVal%' and ") . " publish_status='PUBLISHED'"
  );

  return ceil(mysqli_num_rows($query) / $limit);
}

function saveOldDocuments()
{
  global $conn, $_POST, $_FILES, $separator;

  $title = $_POST["title"];
  $type = $_POST["type"];
  $year = $_POST["year"];
  $description = mysqli_escape_string($conn, nl2br($_POST["description"]));
  $banner = $_FILES["banner"];
  $pdf = $_FILES["pdfFile"];

  if (intval($banner["error"]) == 0 && intval($pdf["error"]) == 0) {

    $bannerFile = date("mdY-his") . $separator . basename($banner['name']);
    $bannerDir = "../media/documents/banner/";
    $bannerUrl = "/media/documents/banner/$bannerFile";

    $pdfFile = date("mdY-his") . $separator . basename($pdf['name']);
    $pdfDir = "../media/documents/files/";
    $pdfUrl = "/media/documents/files/$pdfFile";

    if (!is_dir($bannerDir)) {
      mkdir($bannerDir, 0777, true);
    }

    if (!is_dir($pdfDir)) {
      mkdir($pdfDir, 0777, true);
    }

    if (move_uploaded_file($banner['tmp_name'], "$bannerDir/$bannerFile") && move_uploaded_file($pdf['tmp_name'], "$pdfDir/$pdfFile")) {
      $query = mysqli_query(
        $conn,
        "INSERT INTO documents(title, `type_id`, `year`, `description`, img_banner, project_document, publish_status) VALUES('$title', '$type', '$year', '$description', '$bannerUrl', '$pdfUrl', 'PUBLISHED')"
      );

      if ($query) {
        $response["success"] = true;
        $response["message"] = "Document successfully save.";
      } else {
        $response["success"] = false;
        $response["message"] = mysqli_error($conn);
      }
    }
  } else {
    $response["success"] = false;
    $response["message"] = "An error occurred when uploading documents. Please try again later.";
  }

  returnResponse($response);
}

function markFeedbackResolved()
{
  global $conn, $_POST;

  $id = $_POST["id"];
  $token = $_POST["token"];
  $role = $_POST["role"];
  $column = ($role . "_feedback");

  $document = getDocumentById($id);
  $feedbackData = json_decode($document->$column, true);

  $newFeedBack = array(
    "feedback" => array(),
    "isApproved" => $feedbackData["isApproved"],
  );

  foreach ($feedbackData["feedback"] as $feedback) {
    if ($feedback["token"] == $token) {
      $feedback["isResolved"] = "true";
      array_push($newFeedBack["feedback"], $feedback);
    } else {
      array_push($newFeedBack["feedback"], $feedback);
    }
  }

  $query = mysqli_query(
    $conn,
    "UPDATE documents SET $column='" . json_encode($newFeedBack) . "' WHERE id='$id'"
  );

  if ($query) {
    $response["success"] = true;
    $response["message"] = "Mark resolved successfully.";
    markApprovedIsResolvedAll($id, $role);
  } else {
    $response["success"] = false;
    $response["message"] = mysqli_error($conn);
  }

  returnResponse($response);
}

function markApprovedIsResolvedAll($document_id, $role)
{
  global $conn;

  $document = getDocumentById($document_id);
  $column = ($role . "_feedback");
  $feedbackData = json_decode($document->$column, true);

  $newFeedBack = array(
    "feedback" => array(),
    "isApproved" => $feedbackData["isApproved"],
  );

  $countIsResolved = 0;

  foreach ($feedbackData["feedback"] as $feedback) {
    if ($feedback["isResolved"] == "true") {
      $countIsResolved++;
    }
    array_push($newFeedBack["feedback"], $feedback);
  }

  if (count($feedbackData["feedback"]) == $countIsResolved) {
    $newFeedBack["isApproved"] = "true";
    mysqli_query(
      $conn,
      "UPDATE documents SET $column='" . json_encode($newFeedBack) . "' WHERE id='$document_id'"
    );
  }
}

function fileFeedback()
{
  global $conn, $_POST, $dateNow;

  $document_id = $_POST["document_id"];
  $role = $_POST["role"];
  $feedbackArr = array(
    "message" => nl2br($_POST["feedback"]),
    "token" => uniqid(),
    "isResolved" => "false",
    "date" => $dateNow,
  );

  $column = ($role . "_feedback");

  $document = getDocumentById($document_id);
  $feedback = $document->$column == null ? array(
    "feedback" => array(),
    "isApproved" => "false",
  ) : json_decode($document->$column, true);

  array_push($feedback["feedback"], $feedbackArr);

  $query = mysqli_query(
    $conn,
    "UPDATE documents SET $column='" . json_encode($feedback) . "' WHERE id='$document_id'"
  );

  if ($query) {
    $response["success"] = true;
    $response["message"] = "Filed feedback successfully.";
  } else {
    $response["success"] = false;
    $response["message"] = mysqli_error($conn);
  }

  returnResponse($response);
}

function approvedDocument()
{
  global $conn, $_POST;

  $documentId = $_POST['id'];
  $role = $_POST["role"];
  $column = ($role . "_feedback");

  $feedback = json_encode(array(
    "feedback" => array(),
    "isApproved" => "true",
  ));

  if (!isDocumentApproved($documentId, $role)) {
    $query = mysqli_query(
      $conn,
      "UPDATE documents SET $column='$feedback' WHERE id='$documentId'"
    );
    if ($query) {
      $response["success"] = true;
      $response["message"] = "Document successfully approved.";
    } else {
      $response["success"] = false;
      $response["message"] = mysqli_error($conn);
    }
  } else {
    $response["success"] = false;
    $response["message"] = "Document already approved";
  }

  returnResponse($response);
}

function isDocumentApproved($documentId, $role)
{
  global $conn;

  $column = ($role . "_feedback");
  $query = mysqli_query(
    $conn,
    "SELECT * FROM documents WHERE id='$documentId'"
  );

  if (mysqli_num_rows($query) > 0) {
    $data = mysqli_fetch_object($query);

    if ($data->$column != null) {
      $feedback = json_decode($data->$column);

      if ($feedback->isApproved == "true") {
        return true;
      }
      return false;
    }
    return false;
  }
  return false;
}

function getDocumentsDataWithUsers($userId, $idOf)
{
  global $conn;

  $queryStr = "SELECT 
  tg.group_leader_id,
  tg.group_number,
  tg.instructor_id,
  tg.adviser_id,
  d.* FROM thesis_groups tg 
  INNER JOIN documents d 
  ON tg.group_leader_id = d.leader_id";

  if ($idOf == "adviser") {
    $queryStr .= " WHERE tg.adviser_id='$userId'";
  } else if ($idOf == "instructor") {
    $queryStr .= " WHERE tg.instructor_id='$userId'";
  }

  $data = array();

  $query = mysqli_query($conn, $queryStr);
  if (mysqli_num_rows($query)) {
    while ($row = mysqli_fetch_object($query)) {
      array_push($data, $row);
    }
  }

  return $data;
}

function saveDocument()
{
  global $conn, $_POST, $_FILES, $_SESSION;

  $currentUser = get_user_by_username($_SESSION['username']);

  $title = $_POST["title"];
  $type = $_POST["type"];
  $year = $_POST["year"];
  $description = mysqli_escape_string($conn, nl2br($_POST["description"]));
  $banner = $_FILES["banner"];
  $pdf = $_FILES["pdfFile"];

  // $feedback = mysqli_escape_string($conn, $feedbacksDefault);

  if (intval($banner["error"]) == 0 && intval($pdf["error"]) == 0) {

    $bannerFile = date("mdY-his") . "_" . basename($banner['name']);
    $bannerDir = "../media/documents/banner/";
    $bannerUrl = "/media/documents/banner/$bannerFile";

    $pdfFile = date("mdY-his") . "_" . basename($pdf['name']);
    $pdfDir = "../media/documents/files/";
    $pdfUrl = "/media/documents/files/$pdfFile";

    if (!is_dir($bannerDir)) {
      mkdir($bannerDir, 0777, true);
    }

    if (!is_dir($pdfDir)) {
      mkdir($pdfDir, 0777, true);
    }

    if (move_uploaded_file($banner['tmp_name'], "$bannerDir/$bannerFile") && move_uploaded_file($pdf['tmp_name'], "$pdfDir/$pdfFile")) {
      $query = mysqli_query(
        $conn,
        "INSERT INTO documents(leader_id, title, `type_id`, `year`, `description`, img_banner, project_document, publish_status) VALUES('$currentUser->id', '$title', '$type', '$year', '$description', '$bannerUrl', '$pdfUrl', 'PENDING')"
      );

      if ($query) {
        $response["success"] = true;
        $response["message"] = "Document successfully submitted.";
      } else {
        $response["success"] = false;
        $response["message"] = "An error occurred when uploading documents. Please try again later.";
      }
    }
  } else {
    $response["success"] = false;
    $response["message"] = "An error occurred when uploading documents. Please try again later.";
  }

  returnResponse($response);
}

function getApprovedDocument($currentUser)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM documents WHERE leader_id ='$currentUser->id' and concept_status='APPROVED'"
  );

  if (mysqli_num_rows($query) > 0) {
    return mysqli_fetch_object(
      $query
    );
  }

  return null;
}

function getAllSubmittedDocument($currentUser)
{
  global $conn;

  $documents = array();

  $query = mysqli_query(
    $conn,
    "SELECT * FROM documents WHERE leader_id ='$currentUser->id'"
  );

  while ($row = mysqli_fetch_object($query)) {
    array_push($documents, $row);
  }

  return $documents;
}

function getDocumentById($id)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM documents WHERE id ='$id'"
  );

  if (mysqli_num_rows($query) > 0) {
    return mysqli_fetch_object(
      $query
    );
  }

  return null;
}

function getDocumentByLeaderId($leaderId)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM documents WHERE leader_id ='$leaderId'"
  );

  if (mysqli_num_rows($query) > 0) {
    return mysqli_fetch_object(
      $query
    );
  }

  return null;
}

function hasSubmittedThreeDocuments($currentUser)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM documents WHERE leader_id ='$currentUser->id'"
  );

  if (mysqli_num_rows($query) === 3) {
    return true;
  }
  return false;
}

function hasSubmittedDocuments($currentUser)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM documents WHERE leader_id ='$currentUser->id'"
  );

  if (mysqli_num_rows($query) > 0) {
    return true;
  }
  return false;
}

function saveType()
{
  global $conn, $_POST;

  $action = $_POST["action"];
  $name = ucwords($_POST["name"]);
  $id = isset($_POST["id"]) ? $_POST["id"] : null;

  if (!isTypeExist(strtolower($name), $id)) {
    $query = null;

    if ($action == "add") {
      $query = mysqli_query(
        $conn,
        "INSERT INTO types(`name`) VALUES('$name')"
      );
    } else if ($action == "edit" && $id != null) {
      $query = mysqli_query(
        $conn,
        "UPDATE types SET `name`='$name' WHERE id='$id'"
      );
    }
    if ($query) {
      $message = $action == "add" ? "added" : "updated";
      $response["success"] = true;
      $response["message"] = "Type $message successfully.";
    } else {
      $response["success"] = false;
      $response["message"] = "Error while saving type, Please try again later.";
    }
  } else {
    $response["success"] = false;
    $response["message"] = "Type already exist.";
  }

  returnResponse($response);
}

function deleteType()
{
  global $conn, $_POST;

  $query = mysqli_query(
    $conn,
    "DELETE FROM types WHERE id='$_POST[id]'"
  );

  if ($query) {
    $response["success"] = true;
    $response["message"] = "Type successfully deleted.";
  } else {
    $response["success"] = false;
    $response["message"] = mysqli_error($conn);
  }

  returnResponse($response);
}

function isTypeExist($name, $id = null)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM types WHERE LOWER(`name`) like '$name' " . ($id != null ? " and id != '$id'" : "")
  );

  if (mysqli_num_rows($query) > 0) {
    return true;
  }
  return false;
}

function handleAdviserInvite()
{
  global $conn, $_POST, $_SESSION;

  $currentUser = get_user_by_username($_SESSION['username']);

  $inviteId = $_POST['invite_id'];
  $leaderId = $_POST['leader_id'];
  $action = $_POST['action'];

  $query = mysqli_query(
    $conn,
    "UPDATE invite SET `status`='" . ($action == "approve" ? "APPROVED" : "DECLINED") . "' WHERE id='$inviteId'"
  );

  if ($query) {
    $response["success"] = true;
    $response["message"] = "Invitation successfully " . $action . "d.";
    if ($action == "approve") {
      mysqli_query(
        $conn,
        "UPDATE thesis_groups SET adviser_id='$currentUser->id' WHERE	group_leader_id='$leaderId'"
      );
    }
  } else {
    $response["success"] = false;
    $response["message"] = mysqli_error($conn);
  }

  returnResponse($response);
}

function cancelAdvisorInvite()
{
  global $conn, $_SESSION;
  $user = get_user_by_username($_SESSION['username']);

  $query = mysqli_query(
    $conn,
    "DELETE FROM invite WHERE leader_id='$user->id'"
  );

  if ($query) {
    $response["success"] = true;
    $response["message"] = "Invitation successfully cancelled.";
  } else {
    $response["success"] = false;
    $response["message"] = mysqli_error($conn);
  }

  returnResponse($response);
}

function sendAdviserInvite()
{
  global $conn, $_POST, $_SESSION;
  $user = get_user_by_username($_SESSION['username']);
  $adviserInviteData = adviserInviteData($_POST['adviserId'], $user->id);

  if ($adviserInviteData == null) {
    $query = mysqli_query(
      $conn,
      "INSERT INTO invite(adviser_id, leader_id, `status`, proposed_title) VALUES('$_POST[adviserId]', '$user->id', 'PENDING', '$_POST[title]')"
    );

    if ($query) {
      $response["success"] = true;
      $response["message"] = "Invitation successfully submitted.";
    } else {
      $response["success"] = false;
      $response["message"] = mysqli_error($conn);
    }
  } else {
    if ($adviserInviteData->status == "PENDING") {
      $response["success"] = false;
      $response["message"] = "You already have a pending invite.";
    } else if ($adviserInviteData->status == "DECLINED") {
      $response["success"] = false;
      $response["message"] = "Your already declined by this adviser.";
    } else {
      $response["success"] = false;
      $response["message"] = "An error occurred while inviting this adviser.";
    }
  }

  returnResponse($response);
}

function adviserInviteData($adviserId, $leaderId)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM invite WHERE adviser_id='$adviserId' and leader_id='$leaderId'"
  );

  if (mysqli_num_rows($query) > 0) {
    return mysqli_fetch_object($query);
  } else {
    return null;
  }
}

function deleteSchedule()
{
  global $conn, $_POST;

  $query = mysqli_query(
    $conn,
    "DELETE FROM schedule_list WHERE id='$_POST[id]'"
  );

  if ($query) {
    $response["success"] = true;
    $response["message"] = "Schedule successfully deleted.";
  } else {
    $response["success"] = false;
    $response["message"] = mysqli_error($conn);
  }

  returnResponse($response);
}

function saveSchedule()
{
  global $conn, $_POST, $_SESSION;

  $id = isset($_POST["id"]) ? $_POST["id"] : null;
  $category_id = $_POST["category_id"];
  $leader_id = $_POST["leader_id"];
  $title = $_POST["title"];
  $description = $_POST["description"];
  $schedule_from = $_POST["schedule_from"];
  $schedule_to = $_POST["schedule_to"] != "" ? $_POST["schedule_to"] : null;

  $user = get_user_by_username($_SESSION['username']);

  if (!checkIsHasSchedule($schedule_from, $id)) {
    $query = null;
    if ($id) {
      $query = mysqli_query(
        $conn,
        "UPDATE schedule_list SET " . ($schedule_to != null ? "schedule_to='$schedule_to', is_whole=0, " : "schedule_to='NULL', is_whole=1, ") . " category_id='$category_id', leader_id='$leader_id', title='$title', description='$description', schedule_from='$schedule_from' WHERE id = '$id'"
      );
    } else {
      $user = get_user_by_username($_SESSION['username']);
      $query = mysqli_query(
        $conn,
        "INSERT INTO schedule_list(
          " . ($schedule_to != null ? "schedule_to, " : "") . "
          is_whole,
          `user_id`, 
          category_id, 
          leader_id,
          title, 
          `description`, 
          schedule_from
        ) VALUES(
          " . ($schedule_to != null ? "'$schedule_to', " : "") . "
          " . ($schedule_to == null ? "'1'," : "'0',") . "
          '$user->id', 
          '$category_id', 
          '$leader_id',
          '$title', 
          '$description', 
          '$schedule_from'
        )"
      );
    }

    if ($query) {
      $message = $id == null ? "added" : "updated";
      $response["success"] = true;
      $response["message"] = "Schedule successfully $message";
    } else {
      $response["success"] = false;
      $response["message"] = "Error while saving schedule, Please try again later.";
    }
  } else {
    $response["success"] = false;
    $response["message"] = "Date conflict to the other schedules.";
  }

  returnResponse($response);
}

function checkIsHasSchedule($schedule_from, $id = null)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM schedule_list " . ($id != null ? "WHERE id !='$id'" : "")
  );

  while ($schedule = mysqli_fetch_object($query)) {
    if ($schedule->is_whole == 1) {
      $start = date("m-d-Y", strtotime($schedule->schedule_from));
      $scheduleFrom = date("m-d-Y", strtotime($schedule_from));

      if ($start == $scheduleFrom) {
        return true;
        break;
      }
    } else {
      $scheduleFrom = strtotime($schedule_from);
      $start = strtotime($schedule->schedule_from);
      $end = strtotime($schedule->schedule_to);

      if (($scheduleFrom >= $start) && ($scheduleFrom <= $end)) {
        return true;
        break;
      }
    }
  }

  return false;
}

function getCategoryById($id)
{
  global $conn;

  return mysqli_fetch_object(
    mysqli_query(
      $conn,
      "SELECT * FROM category_list WHERE id = '$id'"
    )
  );
}

function getAllSchedules()
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM schedule_list"
  );

  $array = array();

  while ($schedule = mysqli_fetch_object($query)) {
    array_push($array, $schedule);
  }

  if (count($array) > 0) {
    return $array;
  }
  return null;
}

function deleteCategory()
{
  global $conn, $_POST;

  $query = mysqli_query(
    $conn,
    "DELETE FROM category_list WHERE id='$_POST[id]'"
  );

  if ($query) {
    $response["success"] = true;
    $response["message"] = "Category successfully deleted.";
  } else {
    $response["success"] = false;
    $response["message"] = mysqli_error($conn);
  }

  returnResponse($response);
}

function saveCategory()
{
  global $conn, $_POST;

  $action = $_POST["action"];
  $name = ucwords($_POST["name"]);
  $id = isset($_POST["id"]) ? $_POST["id"] : null;

  if (!isCategoryExist(strtolower($name), $id)) {
    $query = null;

    if ($action == "add") {
      $query = mysqli_query(
        $conn,
        "INSERT INTO category_list(`name`) VALUES('$name')"
      );
    } else if ($action == "edit" && $id != null) {
      $query = mysqli_query(
        $conn,
        "UPDATE category_list SET `name`='$name' WHERE id='$id'"
      );
    }
    if ($query) {
      $message = $action == "add" ? "added" : "updated";
      $response["success"] = true;
      $response["message"] = "Category $message successfully.";
    } else {
      $response["success"] = false;
      $response["message"] = "Error while saving category, Please try again later.";
    }
  } else {
    $response["success"] = false;
    $response["message"] = "Category already exist.";
  }
  returnResponse($response);
}

function isCategoryExist($name, $id = null)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM category_list WHERE LOWER(`name`) like '$name' " . ($id != null ? " and id!='$id'" : "")
  );

  if (mysqli_num_rows($query) > 0) {
    return true;
  }
  return false;
}

function updateSystem()
{
  global $conn, $_POST, $_FILES;

  $name = $_POST["name"];
  $content = nl2br($_POST["content"]);
  $contact = $_POST["contact"];
  $system_logo = $_FILES["system_logo"];
  $cover = $_FILES["cover"];

  $queryStr = "UPDATE system_config SET ";

  $system_logo_url = "";
  $cover_url = "";

  if (intval($system_logo["error"]) == 0) {
    $uploadFile = date("mdY-his") . "_" . basename($system_logo['name']);
    $target_dir = "../public/";

    if (!is_dir($target_dir)) {
      mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($system_logo['tmp_name'], "$target_dir/$uploadFile")) {
      $system_logo_url = "/public/$uploadFile";
      $queryStr .= "logo='$system_logo_url', ";
    }
  }

  if (intval($cover["error"]) == 0) {
    $uploadFile = date("mdY-his") . "_" . basename($cover['name']);
    $target_dir = "../public/";

    if (!is_dir($target_dir)) {
      mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($cover['tmp_name'], "$target_dir/$uploadFile")) {
      $cover_url = "/public/$uploadFile";
      $queryStr .= "cover='$cover_url', ";
    }
  }

  $queryStr .= "system_name = '$name', home_content='$content', contact='$contact'";
  $query = mysqli_query($conn, $queryStr);

  if ($query) {
    $response["success"] = true;
    $response["message"] = "System updated successfully";
  } else {
    $response["success"] = false;
    $response["message"] = mysqli_error($conn);
  }

  returnResponse($response);
}

function updateGroupPanel()
{
  global $conn, $_POST;

  $group_id = $_POST["groupId"];
  $panel_ids = json_encode($_POST["panel_ids"]);

  $query = mysqli_query(
    $conn,
    "UPDATE thesis_groups SET panel_ids='$panel_ids' WHERE id=$group_id"
  );

  if ($query) {
    $response["success"] = true;
    $response["message"] = "Assigned panels successfully.";
  } else {
    $response["success"] = false;
    $response["message"] = mysqli_error($conn);
  }

  returnResponse($response);
}

function addAdmin()
{
  global $conn, $_POST, $_FILES, $dateNow;

  $fname = $_POST["fname"];
  $mname = $_POST["mname"] == "" ? null : $_POST["mname"];;
  $lname = $_POST["lname"];
  $email = $_POST["email"];
  $avatar = $_FILES["avatar"];
  $password = password_hash($email, PASSWORD_ARGON2I);
  $sections = isset($_POST["sections"]) ?  $_POST["sections"] : null;
  $courseIds = isset($_POST["courseId"]) ? $_POST["courseId"] : null;

  $role = $_POST["role"];

  $courseSectionHandled = array();

  if ($role == "instructor" && $courseIds != null) {
    for ($i = 0; $i < count($sections); $i++) {
      $courseData = getCourseData($courseIds[$i]);
      array_push($courseSectionHandled, array(
        "id" => $courseData->course_id,
        "name" => $courseData->name,
        "shortName" => $courseData->short_name,
        "sections" => formatSections($sections[$i])
      ));
    }
  }


  $username = generateUsername($fname, $lname);

  if (!isEmailAlreadyUse($email) && !hasSectionsAssigned($courseSectionHandled) && !hasCourseDuplicate($courseIds)) {
    $query = null;
    if (intval($avatar["error"]) == 0) {
      $uploadFile = date("mdY-his") . "_" . basename($avatar['name']);
      $target_dir = "../media/avatar";

      if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
      }

      if (move_uploaded_file($avatar['tmp_name'], "$target_dir/$uploadFile")) {
        $img_url = "/media/avatar/$uploadFile";

        $query = mysqli_query(
          $conn,
          "INSERT INTO 
          users(first_name, middle_name, last_name, avatar, username, email, `password`, `role`, date_added, is_new)
          VALUES('$fname', " . ($mname ? "'$mname'" : 'NULL') . ", '$lname', '$img_url', '$username', '$email', '$password', '$role', '$dateNow', TRUE)"
        );
      } else {
        $response["message"] = "Error Uploading file.";
      }
    } else {
      $query = mysqli_query(
        $conn,
        "INSERT INTO 
        users(first_name, middle_name, last_name, username, email, `password`, `role`, date_added, is_new)
        VALUES('$fname', " . ($mname ? "'$mname'" : 'NULL') . ", '$lname', '$username', '$email', '$password', '$role', '$dateNow', TRUE)"
      );
    }

    if ($query) {
      $response["success"] = true;
      $response["message"] = "Admin added successfully<br>Would you like to add another?";
      if ($role == "instructor") {
        $instructorId = mysqli_insert_id($conn);
        addUpdateInstructorSections($instructorId, json_encode($courseSectionHandled), "insert");
      }
    } else {
      $response["success"] = false;
      $response["message"] = mysqli_error($conn);
    }
  } else {
    $response["success"] = false;
    if (isEmailAlreadyUse($email)) {

      $response["message"] = "Email already use by other user.";
    } else if (hasCourseDuplicate($_POST["courseId"])) {

      $response["message"] = "Course assigned duplicate.";
    } else {

      $response["message"] = "Course section was already assigned to other instructor";
    }
  }

  returnResponse($response);
}

function formatSections($section)
{
  $newSections = array();
  $split = preg_split('/,/', preg_replace('/\s+/', '', $section));

  foreach ($split as $value) {
    if ($value != "") {
      array_push($newSections, strtoupper($value));
    }
  }

  return $newSections;
}

function getCourseData($courseId)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM courses WHERE course_id='$courseId'"
  );

  if (mysqli_num_rows($query) > 0) {
    return mysqli_fetch_object($query);
  }

  return null;
}

function hasCourseDuplicate($courseIds)
{
  if (is_array($courseIds)) {
    $count = array_count_values($courseIds);

    foreach ($count as $index => $value) {
      if ($value > 1) {
        return true;
      }
    }
  }
  return false;
}

function hasSectionsAssigned($sections, $instructorId = null)
{
  global $conn;

  $hasSection = false;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM instructor_sections " . ($instructorId != null ? "WHERE instructor_id != '$instructorId'" : "") . ""
  );

  while ($row = mysqli_fetch_object($query)) {
    $has = false;
    foreach (json_decode($row->sections, true) as $dbSection) {
      $err = false;
      foreach ($sections as $value) {
        if ($dbSection["id"] == $value["id"]) {
          foreach ($value["sections"] as $section) {
            if (in_array($section, $dbSection["sections"])) {
              $err = true;
              break;
            }
          }
        }
      }
      if ($err) {
        $has = true;
        break;
      }
    }
    if ($has) {
      $hasSection = true;
      break;
    }
  }

  return $hasSection;
}

function addUpdateInstructorSections($instructorId, $sections, $action)
{
  global $conn;

  if (count(json_decode($sections, true)) > 0) {
    if ($action == "insert") {
      mysqli_query(
        $conn,
        "INSERT INTO instructor_sections(instructor_id, sections) VALUES('$instructorId', '$sections')"
      );
    } else {
      mysqli_query(
        $conn,
        "UPDATE instructor_sections SET sections='$sections' WHERE instructor_id='$instructorId'"
      );
    }
  }
}

function sendToInstructor()
{
  global $conn, $_POST, $_SESSION;

  $currentUser = get_user_by_username($_SESSION["username"]);

  $isGroupListSubmitted = isGroupListSubmitted($currentUser);
  $instructorId = $_POST['instructorId'];

  if ($isGroupListSubmitted) {
    $query = mysqli_query(
      $conn,
      "UPDATE thesis_groups SET instructor_id='$instructorId' WHERE group_leader_id='$currentUser->id' and group_number='$currentUser->group_number'"
    );
  } else {
    $group_mate_id = getGroupMateIds($currentUser->group_number, $currentUser->id);
    $query = mysqli_query(
      $conn,
      "INSERT INTO thesis_groups(group_number, group_leader_id, group_member_ids, instructor_id) VALUES('$currentUser->group_number', '$currentUser->id', '$group_mate_id', '$instructorId')"
    );
  }

  if ($query) {
    $response["success"] = true;
    $response["message"] = "Group list submitted to instructor";
  } else {
    $response["success"] = false;
    $response["message"] = mysqli_error($conn);
  }

  returnResponse($response);
}

function getCurrentInstructorWithOther()
{
  global $conn, $_SESSION;
  $currentUser = get_user_by_username($_SESSION["username"]);

  $query = mysqli_query(
    $conn,
    "SELECT * FROM thesis_groups WHERE group_leader_id='$currentUser->id' and group_number='$currentUser->group_number'"
  );

  if (mysqli_num_rows($query) > 0) {
    $thesisGroupData = mysqli_fetch_object($query);
    $currentInstructor = get_user_by_id($thesisGroupData->instructor_id);

    $response["otherInstructors"] = array();

    $otherInstructorQuery = mysqli_query(
      $conn,
      "SELECT id, first_name, last_name, middle_name FROM users WHERE `role`='instructor' and id != '$currentInstructor->id'"
    );

    while ($row = mysqli_fetch_object($otherInstructorQuery)) {
      array_push($response["otherInstructors"], $row);
    }

    $response["currentInstructor"] = ucwords("$currentInstructor->first_name " . $currentInstructor->middle_name ? $currentInstructor->middle_name[0] : "" . ". $currentInstructor->last_name");
    $response["success"] = true;
  } else {
    $response["success"] = false;
    $response["message"] = "Error updating instructor.<br>Please try again later.";
  }

  returnResponse($response);
}

function getMemberData($group_number = null, $leader_id)
{
  global $conn;
  $arr = array();

  $query = mysqli_query(
    $conn,
    "SELECT * FROM users WHERE " . ($group_number == null ? "" : "group_number='$group_number' and ") . "leader_id='$leader_id'"
  );

  while ($row = mysqli_fetch_object($query)) {
    array_push($arr, $row);
  }

  return json_encode($arr);
}
function getGroupMateIds($group_number, $leader_id)
{
  global $conn;

  $arr = array();

  $query = mysqli_query(
    $conn,
    "SELECT * FROM users WHERE group_number='$group_number' and leader_id='$leader_id'"
  );

  while ($row = mysqli_fetch_object($query)) {
    array_push($arr, $row->id);
  }

  return json_encode($arr);
}

function getInstructorData($currentUser)
{
  global $conn;
  $query = mysqli_query(
    $conn,
    "SELECT * FROM thesis_groups WHERE group_number='$currentUser->group_number' and group_leader_id='$currentUser->id'"
  );

  if (mysqli_num_rows($query) > 0) {
    $data = mysqli_fetch_object($query);
    if ($data->instructor_id) {
      return get_user_by_id($data->instructor_id);
    } else {
      return null;
    }
  } else {
    return null;
  }
}

function isGroupListSubmitted($currentUser)
{
  global $conn;
  $query = mysqli_query(
    $conn,
    "SELECT * FROM thesis_groups WHERE group_number='$currentUser->group_number' and group_leader_id='$currentUser->id'"
  );

  if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_object($query)) {
      if ($row->group_leader_id == $currentUser->id) {
        return true;
        break;
      }
    }
  } else {
    return false;
  }
}

function getAllAdviser()
{
  global $conn, $_GET;
  $declineAdviserId = isset($_GET["declineAdviserId"]) ? $_GET["declineAdviserId"] : null;

  $query = mysqli_query(
    $conn,
    "SELECT id, first_name, last_name, middle_name FROM users WHERE `role`='adviser' " . ($declineAdviserId != null ? " and id != '$declineAdviserId'" : "") . ""
  );

  $response["adviser"] = array();

  while ($row = mysqli_fetch_object($query)) {
    array_push($response["adviser"], $row);
  }

  returnResponse($response);
}

function getAllInstructor()
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT id, first_name, last_name, middle_name FROM users WHERE `role`='instructor'"
  );

  $response["instructors"] = array();

  while ($row = mysqli_fetch_object($query)) {
    array_push($response["instructors"], $row);
  }

  returnResponse($response);
}

function getAllPanel()
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT id, first_name, last_name, middle_name FROM users WHERE `role`='panel'"
  );

  $panels = array();

  while ($row = mysqli_fetch_object($query)) {
    array_push($panels, $row);
  }

  return $panels;
}

function deleteUser()
{
  global $conn, $_POST;

  $user = get_user_by_id($_POST['id']);

  $query = mysqli_query(
    $conn,
    "DELETE FROM users WHERE id='$_POST[id]'"
  );

  if ($query) {
    $response["success"] = true;
    $response["message"] = "User successfully deleted.";
    if ($user->role == "student") {
      updateGroupList();
    }
    if ($user->role == "instructor") {
      removeInstructorToGroupList($user->id);
    }
    if ($user->avatar != null) {
      unlink("..$user->avatar");
    }
  } else {
    $response["success"] = false;
    $response["message"] = mysqli_error($conn);
  }

  returnResponse($response);
}

function removeInstructorToGroupList($instructorId)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "UPDATE thesis_groups SET instructor_id=NULL WHERE instructor_id='$instructorId'"
  );

  return $query;
}

function updateGroupList()
{
  global $conn;
  $currentUser = get_user_by_username($_SESSION['username']);

  if (!isGroupListSubmitted($currentUser)) {
    $group_mate_id = getGroupMateIds($currentUser->group_number, $currentUser->id);
    mysqli_query(
      $conn,
      "UPDATE thesis_groups set group_member_ids = '$group_mate_id' WHERE group_leader_id='$currentUser->id' and group_number='$currentUser->group_number'"
    );
  }
}

function addGroupMate()
{
  global $conn, $_POST, $_FILES, $dateNow;

  $group_number = $_POST["group_number"];

  $fname = $_POST["fname"];
  $mname = $_POST["mname"]  == "" ? null : $_POST["mname"];;
  $lname = $_POST["lname"];
  $roll = $_POST["roll"];
  $email = $_POST["email"];
  $year = $_POST["year"];
  $section = $_POST["section"];
  $avatar = $_FILES["avatar"];

  $role = "student";

  $username = generateUsername($fname, $lname);

  if (!isEmailAlreadyUse($email) && !isStudentRollExist($roll)) {
    $query = null;
    $currentUser = get_user_by_username($_SESSION['username']);
    if (intval($avatar["error"]) == 0) {
      $uploadFile = date("mdY-his") . "_" . basename($avatar['name']);
      $target_dir = "../media/avatar";

      if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
      }

      if (move_uploaded_file($avatar['tmp_name'], "$target_dir/$uploadFile")) {
        $img_url = "/media/avatar/$uploadFile";

        $query = mysqli_query(
          $conn,
          "INSERT INTO 
          users(roll, first_name, middle_name, last_name, group_number, year_and_section, avatar, username, email, `role`, leader_id, date_added)
          VALUES('$roll', '$fname', " . ($mname ? "'$mname'" : 'NULL') . ", '$lname', '$group_number', '$year-$section', '$img_url', '$username', '$email', '$role', '$currentUser->id', '$dateNow')"
        );
      } else {
        $response["message"] = "Error Uploading file.";
      }
    } else {
      $query = mysqli_query(
        $conn,
        "INSERT INTO 
        users(roll, first_name, middle_name, last_name, group_number, year_and_section, username, email, `role`, leader_id, date_added)
        VALUES('$roll', '$fname', " . ($mname ? "'$mname'" : 'NULL') . ", '$lname', '$group_number', '$year-$section', '$username', '$email', '$role', '$currentUser->id', '$dateNow')"
      );
    }

    if ($query) {
      $response["success"] = true;
      $response["message"] = "Group mate added successfully<br>Would you like to add another?";
      updateGroupList();
    } else {
      $response["success"] = false;
      $response["message"] = mysqli_error($conn);
    }
  } else {
    if (!isEmailAlreadyUse($email)) {
      $response["success"] = false;
      $response["message"] = "Email already use by other user.";
    } else {
      $response["success"] = false;
      $response["message"] = "Roll already use by other user.";
    }
  }

  returnResponse($response);
}

function updateUser()
{
  global $_POST, $_FILES;

  $userId = $_POST["userId"];
  $email = $_POST["email"];
  $avatar = $_FILES["avatar"];

  $password = isset($_POST["password"]) ? $_POST["password"] : "";
  $cpassword = isset($_POST["cpassword"]) ? $_POST["cpassword"] : "";
  $oldpassword = isset($_POST["oldpassword"]) ? $_POST["oldpassword"] : "";

  $sections = isset($_POST["sections"]) ?  $_POST["sections"] : null;
  $courseIds = null;

  if ($_POST["role"] == "instructor" && isset($_POST["courseId"])) {
    $courseIds = $_POST["courseId"];
  } else if (isset($_POST["courseId"])) {
    $courseIds = strtoupper($_POST["courseId"]);
  }

  $courseSectionHandled = array();

  if ($_POST["role"] == "instructor" && $sections != null) {
    for ($i = 0; $i < count($sections); $i++) {
      $courseData = getCourseData($courseIds[$i]);
      array_push($courseSectionHandled, array(
        "id" => $courseData->course_id,
        "name" => $courseData->name,
        "shortName" => $courseData->short_name,
        "sections" => formatSections($sections[$i])
      ));
    }
  }

  if (!isEmailAlreadyUseWithId($email, $userId) && !hasSectionsAssigned($courseSectionHandled, $userId) && !hasCourseDuplicate($courseIds)) {
    if ($password != "" || $cpassword != "" || $oldpassword != "") {
      $verifyPassword = json_decode(validatePassword($userId, $password, $cpassword, $oldpassword));
      if ($verifyPassword->validate) {
        $passwordHash = $verifyPassword->hash;

        if (intval($avatar["error"]) == 0) {
          $uploadFile = date("mdY-his") . "_" . basename($avatar['name']);
          $target_dir = "../media/avatar";

          if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
          }

          if (move_uploaded_file($avatar['tmp_name'], "$target_dir/$uploadFile")) {
            $img_url = "/media/avatar/$uploadFile";

            updateUserDB($_POST, $img_url, $passwordHash, $courseSectionHandled);
            exit();
          } else {
            $response["message"] = "Error Uploading file.";
          }
        } else {
          updateUserDB($_POST, null, $passwordHash, $courseSectionHandled);
          exit();
        }
      } else {
        $response["success"] = false;
        $response["message"] = $verifyPassword->message;
      }
    } else {
      if (intval($avatar["error"]) == 0) {
        $uploadFile = date("mdY-his") . "_" . basename($avatar['name']);
        $target_dir = "../media/avatar";

        if (!is_dir($target_dir)) {
          mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($avatar['tmp_name'], "$target_dir/$uploadFile")) {
          $img_url = "/media/avatar/$uploadFile";

          updateUserDB($_POST, $img_url, null, $courseSectionHandled);
          exit();
        } else {
          $response["message"] = "Error Uploading file.";
        }
      } else {
        updateUserDB($_POST, null, null, $courseSectionHandled);
        exit();
      }
    }
  } else {
    $response["success"] = false;
    if (isEmailAlreadyUseWithId($email, $userId)) {

      $response["message"] = "Email already use by other user.";
    } else if (hasCourseDuplicate($courseIds)) {

      $response["message"] = "Course assigned duplicate.";
    } else {

      $response["message"] = "Course section was already assigned to other instructor";
    }
  }

  returnResponse($response);
}

function updateUserDB($post, $img_url = null, $hash, $instructorSections)
{
  global $conn;

  $userId = $post["userId"];
  $role = $post["role"];

  $fname = $post["fname"];
  $mname = $post["mname"]  == "" ? null : $_POST["mname"];;
  $lname = $post["lname"];
  $email = $post["email"];

  $roll = isset($post["roll"]) ? $post["roll"] : null;
  $group_number = isset($post["group_number"]) ? $post["group_number"] : null;
  $year = isset($post["year"]) ? $post["year"] : null;
  $section = isset($post["section"]) ? strtoupper($post["section"]) : null;
  $sy = isset($post["sy"]) ? "SY: $post[sy]" : null;
  $courseId = null;

  if ($role == "instructor" && isset($post["courseId"])) {
    $courseId = $post["courseId"];
  } else if (isset($post["courseId"])) {
    $courseId = strtoupper($post["courseId"]);
  }

  $username = generateUsername($fname, $lname);
  $currentUser = get_user_by_id($userId);

  $query = "";
  if ($role == "student") {
    $query = "UPDATE users SET
    " . ($roll == null ? '' : "roll='$roll',") . "
    " . ($courseId == null ? '' : "course_id='$courseId',") . "
    first_name='$fname',
    middle_name=" . ($mname ? "'$mname'" : 'NULL') . ",
    last_name='$lname',
    " . ($sy == null ? '' : "school_year='$sy',") . "
    " . ($group_number == null ? '' : "group_number='$group_number',") . "
    " . ($year == null && $section == null ? '' : "year_and_section='$year-$section',") . "
    " . ($img_url == null ? '' : "avatar='$img_url', ") . "
    username='$username',
    email='$email'
    " . ($hash == null ? '' : ", password='$hash'") . " WHERE id='$userId'";
  } else {
    $query = "UPDATE users SET
    " . ($roll == null ? '' : "roll='$roll',") . "
    first_name='$fname',
    middle_name=" . ($mname ? "'$mname'" : 'NULL') . ",
    last_name='$lname',
    " . ($img_url == null ? '' : "avatar='$img_url', ") . "
    email='$email',
    username='$username'
    " . ($hash == null ? '' : ", password='$hash'") . "  WHERE id='$userId'";
  };

  $insertQuery = mysqli_query($conn, $query);

  if ($insertQuery) {
    $response["success"] = true;
    $response["message"] = $role != "student" ? "Admin updated successfully." : "User updated successfully.";
    if ($role == "instructor") {
      addUpdateInstructorSections($userId, json_encode($instructorSections), "update");
    }
    if ($_SESSION["username"] == $currentUser->username) {
      $_SESSION["username"] = $username;
    }
  } else {
    $response["success"] = false;
    $response["message"] = "Error updating user.";
  }

  returnResponse($response);
}

function validatePassword($user_id, $password, $confirm_password, $old_password)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM users WHERE id='$user_id'"
  );

  $arr = array();

  if (mysqli_num_rows($query) > 0) {
    $user = get_user_by_id($user_id);

    if ($password == $confirm_password && $password != $old_password) {
      if (password_verify($old_password, $user->password)) {
        $arr["validate"] = true;
        $arr["hash"] = password_hash($password, PASSWORD_ARGON2I);
      } else {
        $arr["validate"] = false;
        $arr["message"] = "Password Error";
      }
    } else if ($password == $old_password) {
      $arr["validate"] = false;
      $arr["message"] = "New password and Old password should not be the same.";
    } else {
      $arr["validate"] = false;
      $arr["message"] = "New password and Confirm password not match.";
    }
  } else {
    $arr["validate"] = false;
    $arr["message"] = "Could not find user.";
  }
  return json_encode($arr);
}

function isEmailAlreadyUseWithId($email, $userId)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM users WHERE email='$email' and id != '$userId'"
  );

  if (mysqli_num_rows($query) > 0) {
    return true;
  } else {
    return false;
  }
}

function login()
{
  global $conn, $_POST;

  $email = $_POST["email"];
  $password = $_POST["password"];

  $query = mysqli_query(
    $conn,
    "SELECT * FROM users WHERE email='$email' or roll='$email'"
  );

  if (mysqli_num_rows($query) > 0) {
    $user = get_user_by_email($email) ? get_user_by_email($email) : mysqli_fetch_object($query);
    if (password_verify($password, $user->password)) {
      $response["success"] = true;
      $response["role"] = $user->role;
      $response["isNew"] = $user->is_new ? true : false;
      $_SESSION["username"] = $user->username;
    } else {
      $response["success"] = false;
      $response["message"] = "Password error";
    }
  } else {
    $response["success"] = false;
    $response["message"] = "User doesn't exist.";
  }

  returnResponse($response);
}

function get_user_by_email($email)
{
  global $conn;
  return mysqli_fetch_object(
    mysqli_query(
      $conn,
      "SELECT * FROM users WHERE email = '$email'"
    )
  );
}

function get_user_by_username($username)
{
  global $conn;
  return mysqli_fetch_object(
    mysqli_query(
      $conn,
      "SELECT * FROM users WHERE username = '$username'"
    )
  );
}

function get_user_by_id($user_id)
{
  global $conn;

  return mysqli_fetch_object(
    mysqli_query(
      $conn,
      "SELECT * FROM users WHERE id = '$user_id'"
    )
  );
}

function student_registration()
{
  global $conn, $_SESSION, $_POST, $dateNow;

  $roll = $_POST["roll"];
  $fname = $_POST["fname"];
  $mname = $_POST["mname"] == "" ? null : $_POST["mname"];
  $lname = $_POST["lname"];
  $sy = "SY: $_POST[sy]";
  $year = $_POST["year"];
  $section = strtoupper($_POST["section"]);
  $courseId = $_POST['courseId'];
  $email = $_POST["email"];
  $password = password_hash($_POST["password"], PASSWORD_ARGON2I);

  $username = generateUsername($fname, $lname);

  $role = "student";

  if (!isEmailAlreadyUse($email) || !isStudentRollExist($roll)) {
    $query = mysqli_query(
      $conn,
      "INSERT INTO 
      users(roll, course_id, first_name, middle_name, last_name, school_year, year_and_section, username, email, `password`, `role`, date_added)
      VALUES('$roll', '$courseId', '$fname', " . ($mname ? "'$mname'" : 'NULL') . ", '$lname', '$sy', '$year-$section', '$username', '$email', '$password', '$role', '$dateNow')"
    );

    if ($query) {
      $response["success"] = true;
      $response["message"] = "User registered successfully";
      $response["role"] = $role;
      $_SESSION["username"] = $username;
    } else {
      $response["success"] = false;
      $response["message"] = mysqli_error($conn);
    }
  } else {

    if (!isEmailAlreadyUse($email)) {
      $response["success"] = false;
      $response["message"] = "Email already use by other user.";
    } else {
      $response["success"] = false;
      $response["message"] = "Roll already use by other user.";
    }
  }

  returnResponse($response);
}

function isEmailAlreadyUse($email)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM users WHERE email='$email'"
  );

  if (mysqli_num_rows($query) > 0) {
    return true;
  } else {
    return false;
  }
}

function isStudentRollExist($roll)
{
  global $conn;

  $query = mysqli_query(
    $conn,
    "SELECT * FROM users WHERE roll='$roll'"
  );

  if (mysqli_num_rows($query) > 0) {
    return true;
  } else {
    return false;
  }
}

function logout()
{
  global $_SESSION;
  $_SESSION = array();

  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
      session_name(),
      '',
      time() - 42000,
      $params["path"],
      $params["domain"],
      $params["secure"],
      $params["httponly"]
    );
  }

  session_destroy();
  header("location: ../");
}

function updatePassword()
{
  global $conn, $_POST;

  $password = password_hash($_POST['password'], PASSWORD_ARGON2I);

  $query = mysqli_query(
    $conn,
    "UPDATE users SET `password`='$password', is_new=FALSE WHERE id='$_POST[id]'"
  );

  if ($query) {
    $response["success"] = true;
    $response["message"] = "Password updated successfully";
    $currentUser = get_user_by_id($_POST['id']);
    $response["role"] = $currentUser->role;
  } else {
    $response["success"] = false;
    $response["message"] = "Something went wrong while updating password. Please try again later";
  }

  returnResponse($response);
}

function systemInfo()
{
  global $conn;

  return (mysqli_fetch_object(
    mysqli_query(
      $conn,
      "SELECT * FROM system_config"
    )
  )
  );
}

function generateUsername($fname, $lname)
{
  return strtolower("$fname-$lname-") . preg_replace('/[^A-Za-z0-9\-]/', '', base64_encode(random_bytes(9)));
}

function returnResponse($params)
{
  print_r(
    json_encode($params)
  );
}

function pr($data)
{
  echo "<pre>";
  print_r($data); // or var_dump($data);
  echo "</pre>";
}
