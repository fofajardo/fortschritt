<!DOCTYPE html>
<?php require "common/config.php"; 
	  require "common/session.php";
	  require "common/page.php"; ?>
<?php 
	$groupid = 1;
	if (isset($_GET["group"])) {
		$groupid = $_GET["group"];
	}
	$typeid = 1;
	if (isset($_GET["type"])) {
		$typeid = $_GET["type"];
	}
?>
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
        <title><?php echo SITE_NAME; ?> - Materials</title>
		<link href="style/main.css" rel="stylesheet"/>
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700" rel="stylesheet"> 
		<script src="js/main.js" type="application/javascript"></script>
    </head>
    <body>
		<?php include "common/header.php" ?>
		<?php include "common/modal_template.php" ?>
		<main class="content flex-container">
			<div id="Sidebar">
				<div class="card sticky">
					<div class="card-header">
						Groups
					</div>
					<ul class="sidebar-navigation flex-container column">
						<?php $page->get_material_groups_card($typeid); ?>
					</ul>
				</div>
			</div>
			<div id="Feed">
				<?php
					$page->get_material_types_card($groupid);
					$page->get_materials_card($typeid, $groupid);
				?>
			</div>
		</main>
    </body>
</html>