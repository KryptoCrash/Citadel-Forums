<?php
$admins = array(
              "a8b5c720-34c0-424f-a9bf-a2ec77defad2",
              "a8b5c72034c0424fa9bfa2ec77defad2",
            );
ini_set('max_execution_time', 3000);
?>
<div class="navbar navbar-inverse navbar-custom" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header"><a class="navbar-brand" href="/">Citadel</a>
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-content"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse navbar-content">
            <ul class="nav navbar-nav navbar-left">
                <li><a style="color: #4fc1e9;" href="/forums">Forums</a></li>
                <li class="dropdown">
                    <a style="color: #4fc1e9;" href="#" class="dropdown-toggle" data-toggle="dropdown"><p>Market<b class="caret"></b></p></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="/market/sell">Sell</a></li>
                        <li><a href="/market/buy">Buy</a></li>
                    </ul>
                </li>
                <li><a style="color: #4fc1e9;" href="/donate">Donate</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php
                $full_uuid = $_SESSION['uuid'];
                $name_current = $_SESSION['username'];

                include $_SERVER['DOCUMENT_ROOT'].'/dbconn.php';
                include $_SERVER['DOCUMENT_ROOT'].'/forums/models.php';
                 
                $user = new User($_SESSION["uuid"], $forumConn);
                $content = new Content($forumConn, $user);

                $notifications_all = $content->dbsearch("notifications", "notification_for", $full_uuid);
                $messages = $content->get_notifs("dm");
                $notifications = $content->get_notifs("notif");
                if (empty($notifications_all)) {
                    echo "<li><a style=\"color: #ffffff;\">No notifications.</a></li>";
                } else {
                    $nC = 0;
                    $notificationCount = sizeof($notifications_all);
                    for ($nnC=0; $nnC < $notificationCount; $nnC++) { 
                        if ($notifications_all[$nnC]["notification_read"] == 0) {
                            $nC++;
                        }
                    }
                    if ($nC > 0) {
                        echo "<li><a style=\"color: #4fc1e9;\" href=\"/notifications\">You have $nC new notifications.</a></li>";
                    } else {
                        echo "<li><a style=\"color: #ffffff;\">No new notifications.</a></li>";
                    }
                }
                ?>
                <li><a style="color: #ffffff;">Welcome back <?php echo $_SESSION["username"];?></a></li>
                <li><a style="color: #4fc1e9;" href="/logout">Logout</a></li>
                <li><img id="user-icon-head" src="https://visage.surgeplay.com/head/48/<?php echo $_SESSION["uuid"];?>"></li>
            </ul>
        </div>
    </div>
</div>
<style type="text/css">
  #user-icon-head, .user-icon-head {
      margin-bottom: 0;
      margin-top: .5em;
  }
  .open > .dropdown-menu {
      -webkit-transform: scale(1, 1);
      transform: scale(1, 1);   
  }
  .dropdown-menu {
      -webkit-transform-origin: top;
      transform-origin: top;
      -webkit-animation-fill-mode: forwards;  
      animation-fill-mode: forwards; 
      -webkit-transform: scale(1, 0);
      display: block;
      transition: all 0.2s ease-out;
      -webkit-transition: all 0.2s ease-out;
  }
  .dropup .dropdown-menu {
      -webkit-transform-origin: bottom;
      transform-origin: bottom;  
  }

  .dropup > .dropdown-menu:after {
      border-bottom: 0;
      border-top: 6px solid rgba(39, 45, 51, 0.9);
      top: auto;
      display: inline-block;
      bottom: -6px;
      content: '';
      position: absolute;
      left: 50%;
      border-right: 6px solid transparent;
      border-left: 6px solid transparent;
  }
</style>