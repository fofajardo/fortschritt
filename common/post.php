<?php
	require_once "storage.php";
	require_once "page.php";
	
	$responses = array(
		"noaction" => "Invalid request: You did not provide an action.",
		"fieldrequirement" => "Your note contains too few characters.",
		"postcreated" => "Your note was posted.",
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
					$content = $_REQUEST["content"];
					$groupid = $_REQUEST["group"];
					// 
					if (strlen(trim($content)) < 10) {
						$response = $responses["fieldrequirement"];
						break;
					}
					
					$storage = new Storage();
					
					$userid = $page->get_user_id();
					$sectionid = $storage->get_profile_info($userid)[2];
					
					if ($storage->create_post($userid, $groupid, $content, $sectionid) === true) {
						$response = $responses["postcreated"];
					}
				}
				break;
			case "edit":
				if (isset($_REQUEST["content"]) && isset($_REQUEST["post"])) {
					$postid = $_REQUEST["post"];
					$response = 'TODO: Post edit not yet implemented.';
				}
				break;
			case "delete":
				if (isset($_REQUEST["post"])) {
					$postid = $_REQUEST["post"];
					$response = 'TODO: Post delete not yet implemented.';
				}
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