<?php
$SERVER_NAME = "http://$_SERVER[SERVER_NAME]/west";

$links = array(
  array(
    "title" => "Register",
    "url" => "$SERVER_NAME/pages/register",
    "config" => "navbar"
  ),
  array(
    "title" => "Login",
    "url" => "$SERVER_NAME/pages/login",
    "config" => "navbar"
  ),
  array(
    "title" => "Home",
    "url" => "$SERVER_NAME/index",
    "config" => "sub_navbar"
  ),
  array(
    "title" => "Archives",
    "url" => "$SERVER_NAME/pages/archives",
    "config" => "sub_navbar"
  ),
  array(
    "title" => "About Us",
    "url" => "$SERVER_NAME/about-us",
    "config" => "sub_navbar"
  ),

);
