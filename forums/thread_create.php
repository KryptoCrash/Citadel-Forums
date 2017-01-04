<?php

session_start();

$admins = array(
              "a8b5c720-34c0-424f-a9bf-a2ec77defad2",
              "a8b5c72034c0424fa9bfa2ec77defad2",
            );

if (!isset($_SESSION['uuid'])) {
    header('Location: /forums');
}

if (isset($_POST["name"], $_POST["body"], $_SESSION["topic"])) {
    

	include $_SERVER['DOCUMENT_ROOT'].'/dbconn.php';
    include $_SERVER['DOCUMENT_ROOT'].'/forums/models.php';
         
    $user = new User($_SESSION["uuid"], $forumConn);
    $content = new Content($forumConn, $user);

	$topic = strtolower(str_replace('-', ' ', $_SESSION["topic"]));
	$thread_name = $_POST["name"];
	$thread_body = $_POST["body"];
	$thread_author_uuid = $_SESSION['uuid'];
    //$thread_author_uuid = "3f9e78fe-18bd-478c-a0b2-305198d23a0a";
    $thread_author_name_current = $_SESSION['username'];

    if ($topic == "staff posts") {
       if (!in_array($thread_author_uuid, $admins)) {
            header('Location: /forums');
            die("<center class=\"well\">ERROR: No perms!</center>");
        }
    }

    $thread_create = $content->create_thread($topic, $thread_name, $thread_body);
	$topic = strtolower(str_replace(' ', '-', $_SESSION["topic"]));
    unset($_SESSION["topic"]);
    header("Location: /forums/$topic");

} else {
	header('Location: /forums');
}