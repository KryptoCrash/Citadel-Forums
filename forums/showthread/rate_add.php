<?php
if (isset($_POST["postType"], $_POST["postUUID"], $_POST["value"])) {
	session_start();
	$rater = $_SESSION["uuid"];
	$postType = $_POST["postType"];
	$postUUID = $_POST["postUUID"];
	$rating = $_POST["value"];
	if ($rating == 1 OR $rating == -1) {
		   include $_SERVER['DOCUMENT_ROOT'].'/dbconn.php';
         include $_SERVER['DOCUMENT_ROOT'].'/forums/models.php';
         
         $user = new User($_SESSION["uuid"], $forumConn);
         $content = new Content($forumConn, $user);
   		
   		if ($postType == "thread") {
   			$table = "threads";
   		} else if ($postType == "reply") {
   			$table = "replies";
   		} else {
   			echo "null";
   			die;
   		}
   		if ($rating == 1) {
   			$column = "likes";
   		} else if ($rating == -1) {
   			$column = "dislikes";
   		} else {
   			echo "null";
   			die;
   		}
   		$ratingCheck = $content->get_ratings($rater, $postUUID, $postType, $rating);
   		if ($ratingCheck == "same") {
   			echo "same";
   		} else if ($ratingCheck == "change") {
   			$query = $forumConn->prepare("UPDATE ratings SET rating_value='$rating' WHERE rating_from = '$rater' AND rating_for_uuid = '$postUUID' AND rating_for_type = '$postType'");
			try {
				$query->execute();
			}
			catch(PDOException $e) {
				die($e->getMessage());
			}
   			$rate = $content->rate_post($table, $column, $rater, $postUUID, $postType, $rating);
   			echo $rate;
   		} else if ($ratingCheck == "new") {
   			$query = "INSERT INTO `ratings` (`rating_for_uuid`, `rating_for_type`, `rating_from`, `rating_value`) VALUES (:ratingid, :type, :_from, :val)";
   			$prepare = $forumConn->prepare($query);
   			$prepare->bindValue(':ratingid', $postUUID);
   			$prepare->bindValue(':type', $postType);
   			$prepare->bindValue(':_from', $rater);
   			$prepare->bindValue(':val', $rating);
   			try {
   				$add = $prepare->execute();
   			}
   			catch(PDOException $e) {
   				die($e->getMessage());
   			}
			   $rate = $content->rate_post($table, $column, $rater, $postUUID, $postType, $rating);
   			echo $rate;
   		} else if ($ratingCheck == "diff") {

   		}
		//echo "<pre>".print_r($ratingCheck, true)."</pre>";
	} else {
		echo "null";
	}
} else {
	echo "null";
}