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
	$materialid = 0;
	if (isset($_GET["id"])) {
		$materialid = $_GET["id"];
	}
	$action = "view";
	if (isset($_GET["action"])) {
		$action = $_GET["action"];
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
					if ($materialid == 0 && $action != "new") {
						$page->get_material_types_card($groupid);
						$page->get_material_list_card($typeid, $groupid);
					} else {
						switch ($action) {
							case "view":
								$page->get_material_view_card($materialid);
								break;
							case "edit":
								$page->get_material_edit_card($materialid);
								break;
							case "delete":
								$page->get_material_delete_card($materialid);
								break;
							case "new":
								$page->get_material_add_card();
								break;
							default:
								$page->get_message_card($page->get_message("invalidaction"));
								break;
						}
					}
				?>
			</div>
		</main>
    </body>
</html>