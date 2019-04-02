<!DOCTYPE html>
<?php require "common/config.php"; 
	  require "common/session.php";
	  require "common/page.php"; ?>
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
        <title><?php echo SITE_NAME; ?> - Settings</title>
		<link href="style/main.css" rel="stylesheet"/>
		<link href="style/material-icons.css" rel="stylesheet"/>
		<link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700" rel="stylesheet"/>
		<link href="components/color-picker/color-picker.css" rel="stylesheet">
		<script src="components/color-picker/color-picker.js"></script>
		<script src="js/main.js" type="application/javascript"></script>
    </head>
    <body>
		<?php include "common/header.php" ?>
		<?php include "common/modal_template.php" ?>
		<main class="content story flex-container">
			<div id="Feed">
				<?php $page->get_settings_card($page->get_user_id()); ?>
			</div>
		</main>
		<script src="js/settings.js"></script>
    </body>
</html>
