<?php
session_start();
if (!isset($_SESSION['uuid']) AND !isset($_SESSION['username'])) {
   header('Location: ../');
}

if (isset($_POST["reply"], $_POST["page"], $_SESSION["thread"])) {

	include $_SERVER['DOCUMENT_ROOT'].'/dbconn.php';
    include $_SERVER['DOCUMENT_ROOT'].'/forums/models.php';
         
    $user = new User($_SESSION["uuid"], $forumConn);
    $content = new Content($forumConn, $user);

	$thread_id = strtolower(str_replace('-', ' ', $_SESSION["thread"]));
	$reply_content = $_POST["reply"];
	$reply_author_uuid = $_SESSION['uuid'];
    //$reply_author_uuid = "3f9e78fe-18bd-478c-a0b2-305198d23a0a";
    $reply_author_name_current = $_SESSION['username'];
    $pageNum = $_POST["page"];

    $comment_add = $content->add_comment($reply_content, $thread_id, $reply_author_uuid);
    unset($_SESSION["thread"]);
    header("Location: ../showthread/$thread_id/$pageNum/");
} else {
	header('Location: ../');
}