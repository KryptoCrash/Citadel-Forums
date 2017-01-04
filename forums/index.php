<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">

	<title>Citadel - Forums</title>
	
    <link rel="stylesheet" href="/forums/files/css/bootstrap.css">
    <link rel="stylesheet" href="/forums/files/css/bootflat.css">
    <link rel="stylesheet" href="/forums/files/css/styles.css">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,800,700,400italic,600italic,700italic,800italic,300italic" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/forums/files/css/main.css">

    <script src="files/js/main.js"></script>
</head>
<body>
	<div class="content">
		<div class="home-header">
	    	<?php

			session_start();

			if (!isset($_SESSION["logged_in"])) {
			    die("<center class=\"well\">ERROR: Must be logged in to view this page!</center>");
			}
			if (!isset($_SESSION["uuid"])) {
			    die("<center class=\"well\">ERROR: Must verify your Minecraft account to view this page!</center>");
			}
			include $_SERVER['DOCUMENT_ROOT'].'/navL.php';
			?>
			<center>
				<div class="categories">
					<?php
					$categories = $content->get_categories();
					for ($c=0; $c < count($categories); $c++) {
						?>
						<div>
						<?php
						echo "
	                        <div class=\"cat\">
	                            <a href=/forums/".strtolower(str_replace(' ','-',$categories[$c]['cat_name'])).">".$categories[$c]['cat_name']."</a> ".$categories[$c]['cat_description']."
	                        </div>
	                    ";
						?>
						</div>
						<?php
					}
					?>
				</div>
			</center>
		</div>
    </div>
</body>
</html>