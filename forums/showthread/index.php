<!DOCTYPE html>
<html>
<head>
	<title>Citadel - Forums</title>
	
    <link rel="stylesheet" href="/forums/files/css/bootstrap.css">
    <link rel="stylesheet" href="/forums/files/css/bootflat.css">
    <link rel="stylesheet" href="/forums/files/css/styles.css">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,800,700,400italic,600italic,700italic,800italic,300italic" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/forums/files/css/main.css">
    <link rel="stylesheet" type="text/css" href="/forums/showthread/files/css/main.css">
    <link rel="stylesheet" type="text/css" href="/forums/showthread/files/css/toastr.min.css">

    <script type="text/javascript" src="/forums/showthread/files/js/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src="/forums/showthread/files/js/toastr.min.js"></script>
    <script type="text/javascript" src="/forums/showthread/files/js/main.js"></script>
    <script type="text/javascript" src="/forums/showthread/files/js/bootstrap.min.js"></script>
</head>
<body>
	<div class="content container-fluid">
		<div class="home-header">
			<?php

			session_start();

			if (!isset($_SESSION["logged_in"])) {
			    die("ERROR: Must be logged in to view this page!");
			}
			if (!isset($_SESSION["uuid"])) {
			    die("ERROR: Must verify your Minecraft account to view this page!");
			}
			if (!isset($_GET["threadid"])) {
			    die("LOL nope.");
			}

            include $_SERVER['DOCUMENT_ROOT'].'/navL.php';

            include $_SERVER['DOCUMENT_ROOT'].'/forums/inc/markdown.php';
            $markdown = new Parsedown();

			$limit = 10;

			if (isset($_GET["page"])) {

            	$pageSet = filter_var($_GET["page"], FILTER_VALIDATE_INT);

            	$page = $_GET["page"]-1;
                $current_page = $_GET["page"];
            	if ($_GET["page"] <= 0) {
            		$page = 0;
                    $current_page = 1;
            	}
                $page2 = $page+1;
                $page = $page*10;
                $page2 = $page2*10;
            } else {
                $page = 0;
                $current_page = 1;
                $page2 = $page+1;
                $page = $page*10;
                $page2 = $page2*10;
            }

			$thread_id = $_GET['threadid'];
			if (isset($_SESSION["thread"])) {
                unset($_SESSION["thread"]);
            }
            $_SESSION["thread"] = $thread_id;

    		$check = $content->dbsearch("threads", "thread_uuid", str_replace("-"," ",$thread_id));
    		if (empty($check)) {
    			die("<center class=\"well\">ERROR: No thread by this name!</center>");
    		}
    		$user = $content->dbsearch("users", "user_uuid", $check[0]["thread_by"]);
            $likes = $check[0]["likes"];
            $dislikes = $check[0]["dislikes"];

    		if (isset($pageSet) AND !is_int($pageSet)) {
    			die("<center class=\"well\">ERROR: Invalid page!</center>");
    		} else {
    			?>
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="/forums/">Forums</a></li>
                  <li class="breadcrumb-item"><a href="/forums/<?php echo $check[0]["thread_topic"]; ?>/"><?php echo $check[0]["thread_topic"]; ?></a></li>
                  <li class="breadcrumb-item active"><?php echo $content->text($check[0]["thread_name"]); ?> By <?php echo $content->UUIDName($check[0]["thread_by"]); ?></li>
                </ol>
    			<div class="well" data="<?php echo $check[0]["thread_uuid"]; ?>.;thread.;">
					<img  class="img-rounded" src="https://visage.surgeplay.com/face/<?php echo $check[0]["thread_by"]; ?>">
					<legend>
		                By <?php echo $content->UUIDName($check[0]["thread_by"]); ?><br>
		                <small><?php echo $user[0]['user_group']." | ".$user[0]['user_class']; ?></small>
		            </legend>
		            <div class="container-fluid">
		            	<div class="row">
		                    <div class="col-xs-6"><h3><?php echo $content->text($check[0]["thread_name"]); ?></h3></div>
		                </div>
		                <div class="row">
		                    <div class="col-xs-6"><p class=\"\"><?php echo $markdown->setMarkupEscaped(true)->text($check[0]["thread_content"]); ?></p></div>
		                </div>
		                <div class="row">
		                    <button value="0" class="like btn btn-success glyphicon glyphicon-thumbs-up"> <?php echo $likes; ?></button>
		                    <button value="0" class="dislike btn btn-danger glyphicon glyphicon-thumbs-down"> <?php echo $dislikes; ?></button>
		                </div>
		            </div>
				</div>
    			<?php
    			$comments = $content->get_comments($check[0]["thread_uuid"], $page);
    			if (empty($comments)) {
    				echo "
                        <div class=\"well\">
                        <p>No comments, be the first!</p>
                        </div>";
    			} else {
    				for ($c=0; $c < count($comments); $c++) {
    					$date = date_create($comments[$c]["reply_date"]);
						$when = date_format($date, 'g:ia \o\n l jS F Y');
						$reply_uuid = $comments[$c]["reply_uuid"];
						$reply_content = $comments[$c]["reply_content"];
						$reply_likes = $comments[$c]["likes"];
						$reply_dislikes = $comments[$c]["dislikes"];
                        //$replier = $content->UUIDName($comments[$c]["reply_by"]);
						$replier = $content->dbsearch("users", "user_uuid", $comments[$c]["reply_by"])[0]["user_username"];
						$replier_uuid = $comments[$c]["reply_by"];
    					?>
    					<div class="well" data="<?php echo $reply_uuid; ?>.;reply.;">
    						<img style="width:3em;" class="player_icon_comment_head img-rounded" src="https://visage.surgeplay.com/face/48/<?php echo $replier_uuid; ?>">
    						<p>Comment by <?php echo $replier; ?> at <?php echo $when; ?></p>
    						<div class="container-fluid">
    							<?php echo $markdown->setMarkupEscaped(true)->text($reply_content); ?>
	                            <div class="row">
	                                <button value="0" class="like btn btn-success glyphicon glyphicon-thumbs-up"> <?php echo $reply_likes; ?></button>
	                                <button value="0" class="dislike btn btn-danger glyphicon glyphicon-thumbs-down"> <?php echo $reply_dislikes; ?></button>
	                            </div>
    						</div>
    					</div>
    					<?php
    				}
    				if (count($comments) < $limit && $page == 0) {
                        //Breaks the if()
                    } else if(count($comments) < $limit) {
                        $back = $current_page-1;
                        echo "
                        <div class=\"well\">
                            <a href=\"/forums/showthread/$thread_id/$back/\">Last 10 Comments</a>
                        </div>
                        ";
                    } else if($page > 0) {
                        $back = $current_page-1;
                        $next = $current_page+1;
                        echo "
                        <div class=\"well\">
                            <a href=\"/forums/showthread/$thread_id/$back/\">Last 10 Comments</a> | <a href=\"/forums/showthread/$thread_id/$next/\">Next 10 Comments</a>
                        </div>
                        ";
                    } else if($current_page == 1) {
                        $next = $current_page+1;
                        echo "
                        <div class=\"well\">
                            <a href=\"/forums/showthread/$thread_id/$next/\">Next 10 Comments</a>
                        </div>
                        ";
                    }
    			}
    			?>
    			<form class="form-horizontal" action="/forums/showthread/comment_reply_add.php" method="post">
                    <div class="form-group">
                        <div class="col-sm-10 comment_add">
                            <textarea rows="3" type="textarea" class="form-control" name="reply" id="reply" placeholder="Comment"></textarea>
                        </div>
                    </div><br>
                    <div class="form-group">
                        <div class=" col-sm-10">
                            <input type="hidden" class="form-control" name="page" id="page" value="<?php echo $current_page; ?>">
                            <input class="btn btn-success" type="submit" value="Add Comment" onClick="return create_comment()">
                        </div>
                    </div>
                </form>
    			<?php
    		}
    		?>
		</div>
	</div>
</body>
</html>