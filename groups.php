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
        <title><?php echo SITE_NAME; ?> - <?php $page->get_group_name($get_id); ?> - Groups</title>
		<link href="style/main.css" rel="stylesheet"/>
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
		<link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700" rel="stylesheet"/>
		<script src="js/main.js" type="application/javascript"></script>
    </head>
    <body>
		<?php include "common/header.php" ?>
		<?php include "common/modal_template.php" ?>
		<main class="content flex-container">
			<div id="Sidebar">
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
			<div id="Feed" groupid="<?php echo $get_id ?>">
				<div class="card">
					<div class="card-header">
						<span><?php $page->get_group_name($get_id); ?></span>
					</div>
				</div>
				<?php $page->get_user_posts($get_id, true, null, false); ?>
				<?php $page->get_moreposts_card('groups'); ?>
			</div>
		</main>
    </body>
</html>
