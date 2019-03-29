<!DOCTYPE html>
<?php require "common/config.php"; 
	  require "common/session.php";
	  require "common/page.php"; ?>
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
        <title><?php echo SITE_NAME; ?> - Profile</title>
		<link href="style/main.css" rel="stylesheet"/>
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
		<link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700" rel="stylesheet"/>
		<script src="js/main.js" type="application/javascript"></script>
    </head>
    <body>
		<?php include "common/header.php" ?>
		<?php include "common/modal_template.php" ?>
		<main class="content">
			<div class="card profile-cover">
				<div class="flex-container profile">
					<?php $page->get_user_card(); ?>
				</div>
			</div>
			<div class="flex-container">
				<div id="Sidebar">
					<div class="card">
						<div class="card-header">
							Groups
						</div>
						<ul class="sidebar-navigation flex-container column">
							<?php $page->get_user_groups(); ?>
						</ul>
					</div>
				</div>
				<div id="Feed" userid="<?php echo $page->get_user_id() ?>">
					<div class="card">
						<div class="card-header">
							Your Posts
						</div>
					</div>
					<?php $page->get_user_posts(null, true, null, true, $page->get_user_id()); ?>
					<?php $page->get_moreposts_card('profile'); ?>
				</div>
			</div>
		</main>
	</body>
</html>
