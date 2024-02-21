<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "west";

$response = array(
  "success" => false,
  "message" => ""
);

try {
  $conn = mysqli_connect($host, $user, $password, $db);
} catch (Exception $e) {
  $response["message"] = $e->getMessage();
  returnResponse($response);
}
