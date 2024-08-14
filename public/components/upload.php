<?php
require_once 'conf.php';
require "db_conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $target_dir = "uploads/";
  $target_file = $target_dir . basename($_FILES["profilePic"]["name"]);
  $uploadOk = 1;
  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));




  // Check if image file is an actual image or fake image
  $check = getimagesize($_FILES["profilePic"]["tmp_name"]);
  if ($check !== false) {
    $uploadOk = 1;
  } else {
    // echo "File is not an image.";      
    $uploadOk = 0;
  }

  // Check file size
  if ($_FILES["profilePic"]["size"] > 500000) {
    // echo "Sorry, your file is too large.";
    $uploadOk = 0;
  }

  // Allow certain file formats
  if (
    $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif"
  ) {
    // echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
  }

  $to = $target_dir . time() . "-" . rand(1, 999999) . "." . $imageFileType;


  if ($uploadOk == 1) {


    if (!empty($_SESSION["user_id"]["profilePicture"])) {

      $to = $_SESSION["user_id"]["profilePicture"];

    }

    if (move_uploaded_file($_FILES["profilePic"]["tmp_name"], $to)) {

      $db->updateProfilePicture($_SESSION["user_id"], $to);


      header("Location: ../index.php");


      exit;

    } else {
      echo ("File move failed: " . error_get_last()['message']);
    }
  }
}
