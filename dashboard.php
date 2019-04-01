<!DOCTYPE html>
<?php require "common/config.php"; 
	  require "common/session.php";
	  require "common/page.php"; ?>
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
        <title><?php echo SITE_NAME; ?> - Dashboard</title>
		<link href="style/main.css" rel="stylesheet"/>
		<link href="style/material-icons.css" rel="stylesheet"/>
		<link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700" rel="stylesheet"/> 
		<script src="js/main.js" type="application/javascript"></script>
    </head>
    <body>
		<?php include "common/header.php" ?>
		<?php include "common/modal_template.php" ?>
		<main class="content flex-container">
			<div id="Sidebar">
				<div class="sticky">
				<div class="card">
					<div class="flex-container align-center">
						<?php $page->get_user_card($page->get_user_id()); ?>
					</div>
				</div>
				<div class="card">
					<div class="card-header">
						Groups
					</div>
					<ul class="sidebar-navigation flex-container column">
						<?php $page->get_user_groups($page->get_user_id()); ?>
					</ul>
				</div>
				</div>
			</div>
			<div id="Feed">
				<?php $page->get_newpost_card(); ?>
				<?php $page->get_user_posts(); ?>
				<?php $page->get_moreposts_card('dashboard'); ?>
			</div>
		</main>
    </body>
</html>
