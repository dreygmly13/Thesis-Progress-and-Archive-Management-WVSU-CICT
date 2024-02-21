<?php
// $listOfFiles = listOfFilesUploadedInDb();

function folderFiles($dir, $arrFilesInDb)
{
  $folder = scandir($dir);

  unset($folder[array_search('.', $folder, true)]);
  unset($folder[array_search('..', $folder, true)]);

  if (count($folder) < 1)
    return;

  foreach ($folder as $ff) {
    if (is_dir($dir . '/' . $ff)) {
      folderFiles($dir . '/' . $ff, $arrFilesInDb);
    }
    if (!in_array($ff, $arrFilesInDb) && !is_dir($dir . '/' . $ff)) {
      unlink(__DIR__ . "/" . $dir . "/" . $ff);
    }
  }
  return "true";
}

function listOfFilesUploadedInDb()
{
  include_once("backend/conn.php");
  $listOfFiles = [];

  $user_q = mysqli_query($conn, "SELECT * FROM users");
  while ($user = mysqli_fetch_object($user_q)) {
    $exploded = explode("/", $user->avatar);
    array_push($listOfFiles, $exploded[count($exploded) - 1]);
  }

  $documents_q = mysqli_query($conn, "SELECT * FROM documents");
  while ($document = mysqli_fetch_object($documents_q)) {
    $exploded_banner = explode("/", $document->img_banner);
    $exploded_pdf =  explode("/", $document->project_document);

    array_push($listOfFiles, $exploded_banner[count($exploded_banner) - 1]);
    array_push($listOfFiles, $exploded_pdf[count($exploded_pdf) - 1]);
  }

  $chat_q = mysqli_query($conn, "SELECT * FROM chat");
  while ($chat = mysqli_fetch_object($chat_q)) {
    if ($chat->message_type == "image" || $chat->message_type == "file") {
      array_push($listOfFiles, $chat->message);
    }
  }

  // $reports_q = mysqli_query($conn, "SELECT * FROM reports");
  // while ($b = mysqli_fetch_object($reports_q)) {
  //   $exploded = explode("/", $b->report_file_name);
  //   array_push($listOfFiles, $exploded[count($exploded) - 1]);
  // }
  return $listOfFiles;
}
