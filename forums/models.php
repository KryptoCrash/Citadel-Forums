<?php

/**
 *
 * @description All functions used for Citadel Forums
 * @author Jonathan Barrow (halolink44@gmail.com)
 * @package ForumFunc
 * @version 1.0
 * @copyright 2017 Jonathan Barrow
 */
/**
 * All Mojang API links:
 * 	-https://api.mojang.com/user/profiles/<uuid>/names
 * 		-gives a list of players names based on UUID
 */

/**
* Forums user model
*/
class User {

	/**
	 * Almost all of the User model is unused and will be used LATER. The only thing being used is the uuid getter.
	 */
	
	private $dbh;
	private $id;
	private $uuid;
	private $username;
	private $email;
	private $has_linked;
	private $group;
	private $class;
	private $posts;
	private $join_date;

	function __construct($session, $database) {
		$this->dbh = $database;

		$data = $this->dbsearch("users", "user_uuid", $session);

		$this->id         = $data[0]["user_id"];
		$this->uuid       = $data[0]["user_uuid"];
		$this->username   = $data[0]["user_username"];
		$this->email      = $data[0]["user_email"];
		$this->group      = $data[0]["user_group"];
		$this->class      = $data[0]["user_class"];
		$this->posts      = $data[0]["user_posts"];
		$this->join_date  = $data[0]["user_join_date"];
	}

	public function id() {
		return $this->id;
	}
	public function uuid() {
		return $this->uuid;
	}
	public function username() {
		return $this->username;
	}
	public function email() {
		return $this->email;
	}
	public function group() {
		return $this->group;
	}
	public function class() {
		return $this->class;
	}
	public function posts() {
		return $this->posts;
	}
	public function join_date() {
		return $this->join_date;
	}

	public function logOut() {
		session_start();
		unset($_SESSION['full_uuid']);
		unset($_SESSION["name_current"]);

		header('Location: index.php');
	}

	private function dbsearch($table, $column, $var) {
		$query = $this->dbh->prepare("SELECT * FROM $table WHERE $column LIKE :var");
		$query->bindParam(":var", $var);
		try {
			$query->execute();
		}
		catch(PDOException $e) {
			die($e->getMessage());
		}
		return $query->fetchAll();
	}
}

/**
* Forums content model
*/
class Content {

	private $dbh; //dbh = database handler.
	private $user;

	public function __construct($database, User $user) {
		$this->dbh = $database;
		$this->user = $user;
	}

	private function gen_uuid($len=8) {
	    $hex = md5("floccinaucinihilipilificatio" . uniqid("", true));
	    $pack = pack('H*', $hex);
	    $tmp =  base64_encode($pack);
	    $uid = preg_replace("#(*UTF8)[^A-Za-z0-9]#", "", $tmp);
	    $len = max(4, min(128, $len));
	    while (strlen($uid) < $len) {
	        $uid .= gen_uuid(22);
	    }
	    return substr($uid, 0, $len);
	}

	public function time_elapsed_string($datetime, $full = false) {
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
	            $v = $diff->$k." ".$v.($diff->$k > 1 ? 's' : '');
	        } else {
	            unset($string[$k]);
	        }
	    }

	    if (!$full) $string = array_slice($string, 0, 1);
	    return $string ? implode(', ', $string) . ' ago' : 'just now';
	}

	public function verUsername($username) {
		if(strlen($username) > 16) {
	        return false;
	    }
	    $base = "https://api.mojang.com/users/profiles/minecraft/";
	    $url = $base.$username;
	    $json = file_get_contents($url);
	    $data = json_decode($json, true);
	    if(!isset($data['id'])) {
	        return false;
	    }
	    if(isset($data['legacy'])) {
	        return false;
	    }
	    if(isset($data['demo'])) {
	        return false;
	    } else {
			return true;
		}
	}

	public function text($input) {
		return htmlspecialchars($input);
	}

	private function get_mention($text) {
		$wordArray = $this->split_into_words($text);
		$finishArray = array();
		foreach ($wordArray as $word) {
			if ($this->startsWith($word, "@")) {
				if ($this->verUsername(substr($word, 1))) {
					$finishArray[] = substr($word, 1);
				}
			}
		}
		return array_unique($finishArray);
	}

	private function split_into_words($text) {
		/*splits the text given into separate words*/
		$text = str_replace("\n", " ", $text);
		$text = str_replace("\r", " ", $text);
		$result = explode(' ', $text);
		for ($i = 0; $i < count($result); $i++) {
	        $result[$i] = trim($result[$i]);
	    }
	    return $result;
	}

	private function startsWith($haystack, $needle) {
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}

	public function get_notifs($type) {
		$player = $this->user->uuid();
		$query = $this->dbh->prepare("SELECT * FROM notifications WHERE notification_type LIKE '$type' AND notification_for LIKE '$player' ORDER BY notification_date DESC");
		try {
			$query->execute();
		}
		catch(PDOException $e) {
			die($e->getMessage());
		}
		return $query->fetchAll();
	}

	public function dbsearch($table, $column, $var) {
		$query = $this->dbh->prepare("SELECT * FROM $table WHERE $column LIKE :var");
		$query->bindParam(":var", $var);
		try {
			$query->execute();
		}
		catch(PDOException $e) {
			die($e->getMessage());
		}
		return $query->fetchAll();
	}

	public function create_thread($topic, $thread_name, $thread_body) {
	
		$thread_uuid = $this->gen_uuid();
		$thread_author_uuid = $this->user->uuid();

		$thread_words = $this->split_into_words($thread_body);
		$thread_body_raw = $thread_body;
		foreach ($thread_words as $word) {
			if ($this->startsWith($word, "@")) {
				if ($this->verUsername(substr($word, 1))) {
					$thread_body_raw = str_replace($word, "[$word](/user/".$this->UUIDName($this->nameUUID(substr($word, 1))).")", $thread_body_raw);
				}
			}
		}

		$find_mentions = $this->get_mention($thread_body);
		if (!empty($find_mentions)) {
			foreach ($find_mentions as $mention) {
				if ($thread_author_uuid !== $this->nameUUID($mention)) {
					$query = "INSERT INTO `notifications` (`notification_for`, `notification_from`, `notification_content`, `notification_on`, `notification_type`) VALUES (:for, :_from, :content, :thread, :type)";

					$prepare = $this->dbh->prepare($query);
					$for = $this->nameUUID($find_mentions[$m][1]);
					$prepare->bindValue(':for', $this->nameUUID($mention));
					$prepare->bindValue(':_from', $thread_author_uuid);
					$prepare->bindValue(':content', "This user has mentioned you on a thread!");
					$prepare->bindValue(':thread', $thread_uuid);
					$prepare->bindValue(':type', "notif");

					try {
						$add = $prepare->execute();
					}
					catch(PDOException $e) {
						die($e->getMessage());
					}
				}
			}
		}

		$now = new DateTime();
		$query = "INSERT INTO `threads` (`thread_name`, `thread_content`, `thread_date`, `thread_topic`, `thread_by`, `thread_uuid`, `latest_post_by`, `latest_post_date`) VALUES (:name, :body, :_date, :topic, :thread_by, :thread_uuid, :latest_post_by, :latest_post_date)";
		$prepare = $this->dbh->prepare($query);
		$prepare->bindValue(':name', $thread_name);
		$prepare->bindValue(':body', $thread_body_raw);
		$prepare->bindValue(':_date', $now->format('Y-m-d H:i:s'));
		$prepare->bindValue(':topic', $topic);
		$prepare->bindValue(':thread_by', $thread_author_uuid);
		$prepare->bindValue(':thread_uuid', $thread_uuid);
		$prepare->bindValue(':latest_post_by', $thread_author_uuid);
		$prepare->bindValue(':latest_post_date', $now->format('Y-m-d H:i:s'));

		try {
			$add = $prepare->execute();
		}
		catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function add_comment($comment_content, $thread_id) {

		$comment_uuid = uniqid();
		$comment_author_uuid = $this->user->uuid();

		$comment_words = $this->split_into_words($comment_content);
		$comment_content_raw = $comment_content;
		foreach ($comment_words as $word) {
			if ($this->startsWith($word, "@")) {
				if ($this->verUsername(substr($word, 1))) {
					$comment_content_raw = str_replace($word, "[$word](/user/".$this->UUIDName($this->nameUUID(substr($word, 1))).")", $comment_content_raw);
				}
			}
		}
	
		$query = "INSERT INTO `replies` (`reply_content`, `reply_topic`, `reply_by`, `reply_uuid`) VALUES (:content, :thread_id, :comment_author, :uuid)";
		$prepare = $this->dbh->prepare($query);
		$prepare->bindValue(':content', $comment_content_raw);
		$prepare->bindValue(':thread_id', $thread_id);
		$prepare->bindValue(':comment_author', $comment_author_uuid);
		$prepare->bindValue(':uuid', $comment_uuid);

		try {
			$add = $prepare->execute();
		}
		catch(PDOException $e) {
			die($e->getMessage());
		}

		$query = "SELECT thread_replies FROM threads WHERE thread_uuid = :thread_id";
		$prepare = $this->dbh->prepare($query);
		$prepare->bindValue(':thread_id', $thread_id);

		try {
			$prepare->execute();
		}
		catch(PDOException $e) {
			die($e->getMessage());
		}
		$replies = (int)$prepare->fetchAll()[0]["thread_replies"];
		$replies = $replies+1;

		$query = "UPDATE threads SET thread_replies = $replies WHERE thread_uuid = :thread_id";
		$prepare = $this->dbh->prepare($query);
		$prepare->bindValue(':thread_id', $thread_id);

		try {
			$prepare->execute();
		}
		catch(PDOException $e) {
			die($e->getMessage());
		}

		$query = "UPDATE threads SET latest_post_by = :comment_author WHERE thread_uuid = :thread_id";
		$prepare = $this->dbh->prepare($query);
		$prepare->bindValue(':thread_id', $thread_id);
		$prepare->bindValue(':comment_author', $comment_author_uuid);

		try {
			$prepare->execute();
		}
		catch(PDOException $e) {
			die($e->getMessage());
		}

		$now = new DateTime();
		$time = $now->format('Y-m-d H:i:s');

		$query = "UPDATE threads SET latest_post_date = :latest_post_date WHERE thread_uuid = :thread_id";
		$prepare = $this->dbh->prepare($query);
		$prepare->bindValue(':thread_id', $thread_id);
		$prepare->bindValue(':latest_post_date', $now->format('Y-m-d H:i:s'));

		try {
			$prepare->execute();
		}
		catch(PDOException $e) {
			die($e->getMessage());
		}


		$get_thread_author = $this->dbsearch("threads", "thread_uuid", $thread_id);
		$thread_author = $get_thread_author[0]["thread_by"];

		if ($comment_author_uuid !== $thread_author) {
			$query = "INSERT INTO `notifications` (`notification_for`, `notification_from`, `notification_content`, `notification_on`, `notification_type`) VALUES (:for, :_from, :content, :thread, :type)";

			$prepare = $this->dbh->prepare($query);
			$prepare->bindValue(':for', $thread_author);
			$prepare->bindValue(':_from', $comment_author_uuid);
			$prepare->bindValue(':content', $comment_content);
			$prepare->bindValue(':thread', $thread_id);
			$prepare->bindValue(':type', "notif");

			try {
				$add = $prepare->execute();
			}
			catch(PDOException $e) {
				die($e->getMessage());
			}
		}

		$find_mentions = $this->get_mention($comment_content);
		if (!empty($find_mentions)) {
			//return $find_mentions[0][1];
			$mentions = sizeof($find_mentions);
			
			for ($m=0; $m < $mentions; $m++) {
				if ($comment_author_uuid !== $thread_author) {
					$query = "INSERT INTO `notifications` (`notification_for`, `notification_from`, `notification_content`, `notification_on`, `notification_type`) VALUES (:for, :_from, :content, :thread, :type)";

					$prepare = $this->dbh->prepare($query);
					$for = $this->nameUUID($find_mentions[$m][1]);
					$prepare->bindValue(':for', $for);
					$prepare->bindValue(':_from', $comment_author_uuid);
					$prepare->bindValue(':content', "This user has mentioned you on a thread!");
					$prepare->bindValue(':thread', $thread_id);
					$prepare->bindValue(':type', "notif");

					try {
						$add = $prepare->execute();
					}
					catch(PDOException $e) {
						die($e->getMessage());
					}
				}
			}
		}
	}

	public function create_category($name, $desc) {
	    
	    $query = "INSERT INTO `categories` (`cat_name`, `cat_hash`, `cat_description`) VALUES (:name, :hash, :_desc)";
	    $prepare = $this->dbh->prepare($query);
	    $prepare->bindValue(':name', $name);
	    $prepare->bindValue(':hash', md5($this->gen_uuid(11)));
	    $prepare->bindValue(':_desc', $desc);

	    try {
	        $add = $prepare->execute();
	    }
	    catch(PDOException $e) {
	        die($e->getMessage());
	    }
	}

	public function get_categories() {
		$query = $this->dbh->prepare("SELECT * FROM `categories` ORDER BY `cat_id` ASC");
		try {
			$query->execute();
		}
		catch(PDOException $e) {
			die($e->getMessage());
		}
		return $query->fetchAll();
	}

	public function get_threads($topic, $start_at) {
		$query = $this->dbh->prepare("SELECT * FROM `threads` WHERE `thread_topic` = :topic ORDER BY thread_id DESC LIMIT $start_at, 10");
		$query->bindValue(":topic", $topic);
		try {
			$query->execute();
		}
		catch(PDOException $e) {
			die($e->getMessage());
		}
		return $query->fetchAll();
	}

	public function get_comments($thread, $start_at) {

		$query = $this->dbh->prepare("SELECT * FROM `replies` WHERE `reply_topic` = :thread LIMIT $start_at, 10");
		$query->bindValue(":thread", $thread);
        try {
            $query->execute();
        }
        catch(PDOException $e) {
            die($e->getMessage());
        }
        $res = $query->fetchAll();
        $entries = sizeof($res);

        $returnArray = array();
        for ($e=0; $e < $entries; $e++) { 
            array_push($returnArray, $res[$e]);
        }
        return $returnArray;
	}

	public function get_ratings($user, $postID, $postType, $rating) {
		$query = $this->dbh->prepare("SELECT * FROM ratings WHERE rating_from LIKE '$user' AND rating_for_type = '$postType' AND rating_for_uuid = '$postID'");
		try {
			$query->execute();
		}
		catch(PDOException $e) {
			die($e->getMessage());
		}
		$results = $query->fetchAll();

		if (!empty($results)) {
			if ($results[0]['rating_for_uuid'] == $postID && $results[0]['rating_for_type'] == $postType && $results[0]['rating_from'] == $user) {
				if ($results[0]['rating_value'] == $rating) {
					return "same";
				} else {
					return "change";
				}
			} else {
				return "diff";
			}
		} else {
			return "new";
		}
	}

	public function send_message($to, $from, $message) {
	
		$query = "INSERT INTO `notifications` (`notification_for`, `notification_from`, `notification_content`, `notification_type`) VALUES (:for, :_from, :message, :type)";
		$prepare = $this->dbh->prepare($query);
		$prepare->bindValue(':for', $to);
		$prepare->bindValue(':_from', $from);
		$prepare->bindValue(':message', $message);
		$prepare->bindValue(':type', "dm");

		try {
			$add = $prepare->execute();
		}
		catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	public function rate_post($table, $column, $user, $postID, $postType, $rating) {
		/**
		 * There has GOT to be a better way to do this...
		 * Gets likes
		 * @var int
		 */
		
		$id = $postType."_uuid";
		if ($rating == 1) {
			$query = $this->dbh->prepare("SELECT likes FROM $table WHERE $id LIKE '$postID'");
			try {
				$query->execute();
			}
			catch(PDOException $e) {
				die($e->getMessage());
			}
			$likes = $query->fetchAll();
			$before = $likes[0][0];
			$likesAfter = $before+1;
			if ($likesAfter < 0) {
				$likesAfter = 0;
			}
			$query = $this->dbh->prepare("UPDATE $table SET likes='$likesAfter' WHERE $id = '$postID'");
			try {
				$query->execute();
			}
			catch(PDOException $e) {
				die($e->getMessage());
			}

			/**
			 * Gets dislikes
			 * @var int
			 */
			$query = $this->dbh->prepare("SELECT dislikes FROM $table WHERE $id LIKE '$postID'");
			try {
				$query->execute();
			}
			catch(PDOException $e) {
				die($e->getMessage());
			}
			$dislikes = $query->fetchAll();
			$before = $dislikes[0][0];
			$dislikesAfter = $before-1;
			if ($dislikesAfter < 0) {
				$dislikesAfter = 0;
			}
			$query = $this->dbh->prepare("UPDATE $table SET dislikes='$dislikesAfter' WHERE $id = '$postID'");
			try {
				$query->execute();
			}
			catch(PDOException $e) {
				die($e->getMessage());
			}

		} else if ($rating == -1) {
			$query = $this->dbh->prepare("SELECT likes FROM $table WHERE $id LIKE '$postID'");
			try {
				$query->execute();
			}
			catch(PDOException $e) {
				die($e->getMessage());
			}
			$likes = $query->fetchAll();
			$before = $likes[0][0];
			$likesAfter = $before-1;
			if ($likesAfter < 0) {
				$likesAfter = 0;
			}
			$query = $this->dbh->prepare("UPDATE $table SET likes='$likesAfter' WHERE $id = '$postID'");
			try {
				$query->execute();
			}
			catch(PDOException $e) {
				die($e->getMessage());
			}

			/**
			 * Gets dislikes
			 * @var int
			 */
			$query = $this->dbh->prepare("SELECT dislikes FROM $table WHERE $id LIKE '$postID'");
			try {
				$query->execute();
			}
			catch(PDOException $e) {
				die($e->getMessage());
			}
			$dislikes = $query->fetchAll();
			$before = $dislikes[0][0];
			$dislikesAfter = $before+1;
			if ($dislikesAfter < 0) {
				$dislikesAfter = 0;
			}
			$query = $this->dbh->prepare("UPDATE $table SET dislikes='$dislikesAfter' WHERE $id = '$postID'");
			try {
				$query->execute();
			}
			catch(PDOException $e) {
				die($e->getMessage());
			}
		}
		return json_encode(array('likes' => $likesAfter, 'dislikes' => $dislikesAfter));
	}

	public function UUIDName($uuid) {
	    if (strpos($uuid, "-") !== false) {
	        $uuid = explode("-", $uuid);
	        $uuid = implode("", $uuid);
	    }
	    $base = "https://api.mojang.com/user/profiles/";
	    $url = $base.$uuid."/names";
	    $json = file_get_contents($url);
	    $result = json_decode($json, true);
	    $num = sizeof($result);
	    $pos = $num-1;
	    $username = $result[$pos]["name"];
	    return $username;
	}

	public function nameUUID($username) {
	    if(strlen($username) > 16) {
	        return false;
	    }
	    $base = "https://api.mojang.com/users/profiles/minecraft/";
	    $url = $base.$username;
	    $json = file_get_contents($url);
	    $data = json_decode($json, true);
	    if(!isset($data['id'])) {
	        return false;
	    }
	    if(isset($data['legacy'])) {
	        return false;
	    }
	    if(isset($data['demo'])) {
	        return false;
	    }
	    $uuid = $data['id'];
	    $uuid_full = substr_replace(substr_replace(substr_replace(substr_replace($uuid, '-', 8, 0), '-', 13, 0), '-', 18, 0), '-', 23, 0);;
	    return $uuid_full;
	}

}