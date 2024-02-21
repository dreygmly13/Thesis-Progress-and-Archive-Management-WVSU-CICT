<?php
$self = "http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}";

$isNotYetAssignedGroup = null;
$isMember = null;
$isLeader = null;

if ($_SESSION["username"]) {
  $isNotYetAssignedGroup = isNotYetAssignedGroup($user->id);
  $isMember = isMember($user->id);
  $isLeader = isLeader($user->id);
}

$links = array(
  array(
    "title" => "Home",
    "url" => "$SERVER_NAME/pages/student/index",
    "allowedViews" => array("student")
  ),
  array(
    "title" => "Thesis Documents",
    "url" => "$SERVER_NAME/pages/archives",
    "allowedViews" => array("student")
  ),
  array(
    "title" => "Profile",
    "url" => "$SERVER_NAME/pages/profile",
    "allowedViews" => array("student")
  ),
  array(
    "title" => "Groupings",
    "url" => "$SERVER_NAME/pages/student/my-groupings",
    "allowedViews" => array(!$isNotYetAssignedGroup ? "student" : "")
  ),
  array(
    "title" => "Schedules",
    "url" => "$SERVER_NAME/pages/student/schedule",
    "allowedViews" => array(!$isNotYetAssignedGroup ? "student" : "")
  ),
  array(
    "title" => "Document Status",
    "url" => "$SERVER_NAME/pages/student/document-status",
    "allowedViews" => array(!$isNotYetAssignedGroup ? "student" : "")
  ),
  array(
    "title" => "Messages",
    "url" => "$SERVER_NAME/pages/student/messages",
    "allowedViews" => array(!$isNotYetAssignedGroup && $isLeader ? "student" : "")
  ),
  array(
    "title" => "Dashboard",
    "url" => "$SERVER_NAME/pages/admin/index",
    "allowedViews" => array("coordinator", "instructor", "panel", "adviser"),
    "config" => array(
      "icon" => "tachometer-alt"
    )
  ),
  array(
    "title" => "Courses",
    "url" => "$SERVER_NAME/pages/admin/course",
    "allowedViews" => array("coordinator"),
    "config" => array(
      "icon" => "university"
    )
  ),
  array(
    "title" => "Messages",
    "url" => "$SERVER_NAME/pages/admin/messages",
    "allowedViews" => array("instructor", "adviser"),
    "config" => array(
      "icon" => "paper-plane"
    )
  ),
  array(
    "title" => "Task Category",
    "url" => "$SERVER_NAME/pages/admin/task-category",
    "allowedViews" => array("coordinator", "panel", "adviser", "instructor"),
    "config" => array(
      "icon" => "th-list"
    )
  ),
  array(
    "title" => "Scheduled task",
    "url" => "$SERVER_NAME/pages/admin/scheduled-task",
    "allowedViews" => array("coordinator", "instructor", "panel", "adviser"),
    "config" => array(
      "icon" => "calendar-week"
    )
  ),
  array(
    "title" => "Assigned groups",
    "url" => "$SERVER_NAME/pages/admin/assigned-groups",
    "allowedViews" => array("panel"),
    "config" => array(
      "icon" => "users-cog"
    )
  ),
  array(
    "title" => "Upload documents",
    "url" => "$SERVER_NAME/pages/admin/upload-documents",
    "allowedViews" => array("coordinator"),
    "config" => array(
      "icon" => "upload"
    )
  ),
  array(
    "title" => "To publish documents",
    "url" => "$SERVER_NAME/pages/admin/to-publish-documents",
    "allowedViews" => array("coordinator"),
    "config" => array(
      "icon" => "file-import"
    )
  ),
  array(
    "title" => "Published documents",
    "url" => "$SERVER_NAME/pages/admin/published-documents",
    "allowedViews" => array("coordinator"),
    "config" => array(
      "icon" => "cloud"
    )
  ),
  array(
    "title" => "Thesis type",
    "url" => "$SERVER_NAME/pages/admin/type",
    "allowedViews" => array("coordinator"),
    "config" => array(
      "icon" => "th-list"
    )
  ),
  array(
    "title" => "Students",
    "url" => "$SERVER_NAME/pages/admin/user-lists",
    "allowedViews" => array("coordinator"),
    "config" => array(
      "icon" => "users-cog"
    )
  ),
  array(
    "title" => "Unassigned Students",
    "url" => "$SERVER_NAME/pages/admin/unassigned",
    "allowedViews" => array("instructor"),
    "config" => array(
      "icon" => "users-cog"
    )
  ),
  array(
    "title" => "Assign Leader",
    "url" => "$SERVER_NAME/pages/admin/assign-leader",
    "allowedViews" => array("instructor"),
    "config" => array(
      "icon" => "users-cog"
    )
  ),
  array(
    "title" => "Students",
    "url" => "$SERVER_NAME/pages/admin/students",
    "allowedViews" => array("instructor"),
    "config" => array(
      "icon" => "list"
    )
  ),
  array(
    "title" => "Invites",
    "url" => "$SERVER_NAME/pages/admin/invites",
    "allowedViews" => array("adviser"),
    "config" => array(
      "icon" => "users-cog"
    )
  ),
  array(
    "title" => "Pending Documents",
    "url" => "$SERVER_NAME/pages/admin/pending-documents",
    "allowedViews" => array("instructor", "adviser"),
    "config" => array(
      "icon" => "file-upload"
    )
  ),
  array(
    "title" => "Admins",
    "url" => "$SERVER_NAME/pages/admin/admin-lists",
    "allowedViews" => array("coordinator"),
    "config" => array(
      "icon" => "user-shield"
    )
  ),
  array(
    "title" => "Settings",
    "url" => "$SERVER_NAME/pages/admin/settings",
    "allowedViews" => array("coordinator"),
    "config" => array(
      "icon" => "tools"
    )
  ),

);
