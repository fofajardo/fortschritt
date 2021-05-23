<?php
	require_once "database.php";
	require_once "page.php";
	require_once "storage.php";
	
	function sanitize($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5);
		return $data;
	}
	
	$responses = array(
		"noaction" => "Invalid request: You did not provide an action.",
		"settingsaved" => "Your settings were saved.",
		"fieldrequirement" => "Your note contains too few characters.",
		"postcreated" => "Your note was posted.",
		"postdeleted" => "Your note was deleted.",
		"postedited" => "Your note was edited.",
		"materialcreated" => "The material was posted.",
		"materialdeleted" => "The material was deleted.",
		"materialedited" => "The material was edited.",
		"commentcreated" => "Your comment was posted.",
		"commentdeleted" => "Your comment was deleted.",
		"actionfailed" => "Action failed.");
	
	// Default response to return
	$response = $responses["actionfailed"];
	
	// No action eh? No post for you!
	if (!isset($_REQUEST["action"])) {
		$response = $responses["noaction"];
	}

	// Response codes:
	// true = success
	// false = failed
	if (isset($_REQUEST["action"])) {
		$action = $_REQUEST["action"];
		switch ($action) {
			case "new":
				if (isset($_REQUEST["content"]) && isset($_REQUEST["group"])) {
					$content = sanitize($_REQUEST["content"]);
					$groupid = sanitize($_REQUEST["group"]);
					
					if (strlen(trim($content)) < 10) {
						$response = $responses["fieldrequirement"];
						break;
					}
					
					$database = new Database();
					
					$userid = sanitize($page->get_user_id());
					$sectionid = sanitize($database->get_profile_info($userid)[2]);
					
					if ($database->create_post($userid, $groupid, $content, $sectionid) === true) {
						$response = $responses["postcreated"];
					}
				}
				break;
			case "edit":
				if (isset($_REQUEST["content"]) && isset($_REQUEST["post"])) {
					$postid = sanitize($_REQUEST["post"]);
					$content = sanitize($_REQUEST["content"]);
					
					if (strlen(trim($content)) < 10) {
						$response = $responses["fieldrequirement"];
						break;
					}
					
					$database = new Database();
					if ($database->edit_post($postid, $content)) {
						$response = $responses["postedited"];
					}
				}
				break;
			case "delete":
				if (isset($_REQUEST["post"])) {
					$postid = sanitize($_REQUEST["post"]);
					
					$database = new Database();
					if ($database->delete_post($postid)) {
						$response = $responses["postdeleted"];
					}
				}
				break;
			case "get_edit_modal":
				if (isset($_REQUEST["post"])) {
					$response = $page->get_edit_card($_REQUEST["post"]);
				}
				break;
			case "new_comment":
				if (isset($_REQUEST["content"]) && isset($_REQUEST["post"])) {
					$postid = sanitize($_REQUEST["post"]);
					$content = sanitize($_REQUEST["content"]);
					$userid = sanitize($page->get_user_id());
					
					if (strlen(trim($content)) < 10) {
						$response = $responses["fieldrequirement"];
						break;
					}
					
					$database = new Database();
					if ($database->create_comment($userid, $content, $postid)) {
						$response = $responses["commentcreated"];
					}
				}
				break;
			case "delete_comment":
				if (isset($_REQUEST["post"])) {
					$commentid = sanitize($_REQUEST["post"]);
					
					$database = new Database();
					if ($database->delete_comment($commentid)) {
						$response = $responses["commentdeleted"];
					}
				}
				break;
			case "new_material":
				// title, group, content, file
				if (isset($_REQUEST["title"]) && isset($_REQUEST["category"]) &&
					isset($_REQUEST["group"]) && isset($_REQUEST["content"])) {
					$content = sanitize($_REQUEST["content"]);
					$groupid = sanitize($_REQUEST["group"]);
					$title   = sanitize($_REQUEST["title"]);
					$typeid = sanitize($_REQUEST["category"]);
					$filename = null;
					
					if (strlen(trim($title)) < 4) {
						$response = $responses["fieldrequirement"];
						break;
					}
					
					$database = new Database();
					
					$userid = sanitize($page->get_user_id());
					$can_edit = $database->user_can_edit($userid);
					if (!$can_edit) {
						return;
					}
					
					$storage = new Storage();
					if (isset($_FILES["file"]) && $storage->manage_file($_FILES["file"], 'files')) {
						$filename = sanitize($_FILES['file']['name']);
					}
					
					if ($database->create_material($title, $groupid, $content, $typeid, $filename) === true) {
						$response = $responses["materialcreated"];
					}
				}
				break;
			case "edit_material":
				// title, group, content, file
				if (isset($_REQUEST["id"]) && isset($_REQUEST["title"]) &&
					isset($_REQUEST["group"]) && isset($_REQUEST["content"])) {
					$materialid = sanitize($_REQUEST["id"]);
					$content = sanitize($_REQUEST["content"]);
					$groupid = sanitize($_REQUEST["group"]);
					$title   = sanitize($_REQUEST["title"]);
					$typeid = sanitize($_REQUEST["category"]);
					$filename = null;
					
					if (strlen(trim($title)) < 4) {
						$response = $responses["fieldrequirement"];
						break;
					}
					
					$database = new Database();
					
					$userid = sanitize($page->get_user_id());
					$can_edit = $database->user_can_edit($userid);
					if (!$can_edit) {
						return;
					}
					
					$storage = new Storage();
					if (isset($_FILES["file"]) && $storage->manage_file($_FILES["file"], 'files')) {
						$filename = sanitize($_FILES['file']['name']);
					}
					
					if ($database->edit_material($materialid, $title, $groupid, $content, $typeid, $filename) === true) {
						$response = $responses["materialedited"];
					}
				}
				break;
			case "delete_material":
				if (isset($_REQUEST["id"])) {
					$materialid = sanitize($_REQUEST["id"]);
					
					$database = new Database();
					
					$userid = sanitize($page->get_user_id());
					$can_edit = $database->user_can_edit($userid);
					if (!$can_edit) {
						return;
					}
					
					if ($database->delete_material($materialid) === true) {
						$response = $responses["materialdeleted"];
					}
				}
				break;
			case "save_settings":
				$database = new Database();
				$storage = new Storage();
				
				/* profile (file)
					cover (file)
					page_color
					accent_color
					header_color */
				$page_color = sanitize($_REQUEST["page_color"]);
				$accent_color = sanitize($_REQUEST["accent_color"]);
				$header_color = sanitize($_REQUEST["header_color"]);
				
				$storage->manage_file($_FILES["profile"], 'profiles', $page->get_user_id() . '.jpg');
				$storage->manage_file($_FILES["cover"], 'profiles', $page->get_user_id() . '_cover.jpg');
				$database->edit_profile_info($page->get_user_id(), null, null, null, null, null, $page_color, $accent_color, $header_color);
				
				header('Location: /');
				break;
			case "get_posts":
				$groupid = empty($_REQUEST['group']) ? null : $_REQUEST['group'];
				$show_category = empty($_REQUEST['show-category']) ? true : $_REQUEST['show-category'];
				$userid = empty($_REQUEST['user']) ? null : $_REQUEST['user'];
				$limit = empty($_REQUEST['limit']) ? 10 : $_REQUEST['limit'];
				$offset = empty($_REQUEST['offset']) ? 0 : $_REQUEST['offset'];
				
				$response = $page->get_user_posts($groupid, true,
							null, $show_category, $userid,
							false, true, $limit, $offset);
				break;
		}
	}

	return print($response);
?>