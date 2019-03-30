<!DOCTYPE html>
<?php require "common/config.php"; 
	  require "common/session.php";
	  require "common/page.php"; ?>
<?php 
	$get_id = $page->get_user_id();
	if (isset($_GET["id"])) {
		$get_id = $_GET["id"];
	}
?>
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
			<?php $page->get_profile_card($get_id); ?>
			<div class="flex-container">
				<div id="Sidebar">
					<div class="card">
						<div class="card-header">
							Joined Groups
						</div>
						<ul class="sidebar-navigation flex-container column">
							<?php $page->get_user_groups($get_id); ?>
						</ul>
					</div>
				</div>
				<div id="Feed" userid="<?php echo $get_id; ?>">
					<div class="card">
						<div class="card-header">
							Your Posts
						</div>
					</div>
					<?php $page->get_user_posts(null, true, null, true, $get_id); ?>
					<?php $page->get_moreposts_card('profile'); ?>
				</div>
			</div>
		</main>
	</body>
</html>
