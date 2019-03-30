<!DOCTYPE html>
<?php require "common/config.php"; 
	  require "common/session.php";
	  require "common/page.php"; ?>
<?php 
	$get_id = -1;
	if (isset($_GET["id"])) {
		$get_id = $_GET["id"];
	}
?>
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
        <title><?php echo SITE_NAME; ?> - Post</title>
		<link href="style/main.css" rel="stylesheet"/>
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
		<link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700" rel="stylesheet"/>
		<script src="js/main.js" type="application/javascript"></script>
    </head>
    <body>
		<?php include "common/header.php" ?>
		<main class="content story flex-container">
			<div id="Feed">
				<?php $page->get_user_posts(null, true, $get_id, true, null, true); ?>
			</div>
		</main>
    </body>
</html>
