<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">

	<title>Citadel - Forums</title>
	
    <link rel="stylesheet" href="/forums/files/css/bootstrap.css">
    <link rel="stylesheet" href="/forums/files/css/bootflat.css">
    <link rel="stylesheet" href="/forums/files/css/styles.css">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,800,700,400italic,600italic,700italic,800italic,300italic" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="files/css/main.css">

</head>
<body>
	<div class="content">
		<div class="home-header container">
	    	<?php

			session_start();

			if (!isset($_SESSION["logged_in"])) {
			    die("<center class=\"well\">ERROR: Must be logged in to view this page!</center>");
			}
			if (!isset($_SESSION["uuid"])) {
			    die("<center class=\"well\">ERROR: Must verify your Minecraft account to view this page!</center>");
			}
			if (!isset($_GET["cat"])) {
			    die("LOL nope.");
			}

			include $_SERVER['DOCUMENT_ROOT'].'/navL.php';

			if (isset($_SESSION["topic"])) {
                unset($_SESSION["topic"]);
            }
            $_SESSION["topic"] = $_GET['cat'];

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

			$check = $content->dbsearch("categories", "cat_name", str_replace("-"," ",$_GET['cat']));
    		if (empty($check)) {
    			die("<center class=\"well\">ERROR: No topic by this name!</center>");
    		}

    		if (isset($pageSet) AND !is_int($pageSet)) {
    			if ($_GET["page"] !== "create") {
    				die("<center class=\"well\">ERROR: Invalid page!</center>");
	            }
	            if (strtolower($_GET['cat']) == "staff-posts") {
                    if (!in_array($_SESSION["uuid"], $admins)) {
                        die("<center class=\"well\">ERROR: No perms!</center>");
                    }
                }
	            ?>
	            <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="/forums/">Forums</a></li>
                  <li class="breadcrumb-item"><a href="/forums/<?php echo $_GET["cat"]; ?>/"><?php echo $_GET["cat"]; ?></a></li>
                  <li class="breadcrumb-item active">create</li>
                </ol>
	            <div class="well">
	            	<form action="/forums/thread_create.php" method="post" class="form-horizontal center">
		              	<div class="form-group">
		                    <div class="col-sm-10 name">
		                        <input type="text" class="form-control" name="name" id="thread_name" placeholder="Thread Name">
		                    </div>
		              	</div>
		              	<div class="form-group">
		                    <div class="col-sm-10 content">
		                        <textarea rows="3" type="textarea" class="form-control" name="body" id="thread_content" placeholder="Thread Body"></textarea>
		                    </div>
		              	</div><br>
		              	<div class="form-group">
		                    <div class="col-sm-10">
		                        <input class="btn btn-success" type="submit" value="Create" onClick="return create_thread()">
		                    </div>
		              	</div>
		        	</form>
	            </div>
	            <?php
    		} else {
    			?>
    			<ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="/forums/">Forums</a></li>
                  <li class="breadcrumb-item active"><?php echo $_GET["cat"]; ?></li>
                </ol>
    			<center>
    				<div class="well">
		    			<div>
			                <h4>Threads</h4>
			                <?php
			                if (strtolower($_GET['cat']) == "staff-posts") {
			                    if (in_array($_SESSION["uuid"], $admins)) {
			                        echo "<a class=\"btn btn-success\" href=\"/forums/".$_GET["cat"]."/create\">Create Thread</a><br>";
			                    }
			                } else {
			                    echo "<a class=\"btn btn-success\" href=\"/forums/".$_GET["cat"]."/create\">Create Thread</a><br>";
			                }
			                ?>
			            </div>
			            <div class="threads">
			            	<div class="table-responsive">
			            		<table class="footable table table-hover table-condensed">
								    <thead>
								      	<tr>
									        <th>Thread</th>
									        <th>Info</th>
									        <th>Latest</th>
								      	</tr>
								    </thead>
								    <tbody>
								    	<?php
						                $threads = $content->get_threads(str_replace("-", " ",$_GET['cat']), $page);
						                if (empty($threads)) {
						                	die("No threads!");
						                }

						                for ($t=0; $t < count($threads); $t++) {
						                	$thread_title     = $content->text($threads[$t]["thread_name"]);
							                $thread_id        = $content->text($threads[$t]["thread_uuid"]);
							                $thread_by_uuid   = $threads[$t]["thread_by"];
							                //$thread_by_name   = $content->UUIDName($thread_by_uuid);
							                $thread_by_name   = $content->dbsearch("users", "user_uuid", $thread_by_uuid)[0]["user_username"];
							                $thread_topic     = $threads[$t]["thread_topic"];
							                $thread_topic_url = str_replace(" ", "-", $thread_topic);
							                $thread_likes     = $threads[$t]["likes"];
							                $thread_dislikes  = $threads[$t]["dislikes"];
							                $thread_replies   = $threads[$t]["thread_replies"];
							                $thread_date      = date('M j Y g:i A', strtotime($threads[$t]["thread_date"]));
							                $thread_ago       = $content->time_elapsed_string($threads[$t]["thread_date"]);
							                $latest_by        = $threads[$t]["latest_post_by"];
							                //$latest_by_name   = $content->UUIDName($latest_by);
							                $latest_by_name   = $content->dbsearch("users", "user_uuid", $latest_by)[0]["user_username"];
							                $latest_date      = $content->time_elapsed_string($threads[$t]["latest_post_date"]);

						                	echo "
											<tr>
										        <td>
										        	<img class=\"user-icon-head\" src=\"https://visage.surgeplay.com/face/48/$thread_by_uuid\">
										        	<a href=\"/forums/showthread/$thread_id\">$thread_title</a>
										        	<br>
										        	<small>
										        		<a href=\"/user/$thread_by_name\">$thread_by_name</a>
										        	</small>
										        	<small>
										        		<span data-placement=\"top\" data-toggle=\"tooltip\" title=\"\" data-original-title=\"$thread_date\">$thread_ago</span></a>
										        	</small>
										        </td>
										        <td>
										        	<b>$thread_replies</b>
											        replies
											        <br>
											        <small>
											            <b>$thread_likes</b>
											            Likes
											        </small>
											        <small>
											            <b>$thread_dislikes</b>
											            Dislikes
											        </small>
										        </td>
										        <td>
										        	<img class=\"user-icon-head\" src=\"https://visage.surgeplay.com/head/48/$latest_by\">
										        	<small><a href=\"/user/$thread_by_name\">$latest_by_name</a> $latest_date</small>
										        </td>
										    </tr>
						                	";
						                }
						            	?>
								    </tbody>
								</table>
								<?php
								if (count($threads) < $limit && $page == 0) {
			                        //Breaks the if()
			                    } else if(count($threads) < $limit) {
			                        $back = $current_page-1;
			                        echo "
			                        <div class=\"well\">
			                            <a href=\"/forums/".$_GET['cat']."/$back/\">Last 10 Threads</a>
			                        </div>
			                        ";
			                    } else if($page > 0) {
			                        $back = $current_page-1;
			                        $next = $current_page+1;
			                        echo "
			                        <div class=\"well\">
			                            <a href=\"/forums/".$_GET['cat']."/$back/\">Last 10 Threads</a> | <a href=\"/forums/".$_GET['cat']."/$next/\">Next 10 Threads</a>
			                        </div>
			                        ";
			                    } else if($current_page == 1) {
			                        $next = $current_page+1;
			                        echo "
			                        <div class=\"well\">
			                            <a href=\"/forums/".$_GET['cat']."/$next/\">Next 10 Threads</a>
			                        </div>
			                        ";
			                    }
								?>
							</div>
			            </div>
			        </div>
    			</center>
    			<?php
    		}
			?>
		</div>
    </div>
    <script src="/forums/files/js/jquery-2.1.4.min.js"></script>
    <script src="/forums/files/js/bootstrap.min.js"></script>
    <script src="/forums/files/js/main.js"></script>
</body>
</html>