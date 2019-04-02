<?php

require_once "database.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/components/parsedown/parsedown.php";

$page = new Page();

class Page {
	private $messages = null;
	private function sanitize($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5);
		return $data;
	}
	
	function __construct() {
		$this->messages = array(
			"invalidaction" => "Invalid action.",
			"noquery" => "Search for something to continue.",
			"nousers" => "No users found.",
			"notfound" => "No more notes.",
			"nocomments" => "No comments.",
			"nomaterials" => "No materials available.",
			"nopermissions" => "You don't have the required permissions to access this content.",
			"notfoundmaterial" => "Material not found.");
	}
	function get_user_id() {
		$cookie_name = 'userid';
		if(!isset($_COOKIE[$cookie_name])) {
			// Default user...
			return 0;
		}
		return $_COOKIE[$cookie_name];
	}
	function get_message($key) {
		return $this->messages[$key];
	}
	function get_message_card($message) {
		echo '<div class="card" id="Card-NotFound">';
		echo '<div class="card-notfound">';
		echo $message;
		echo '</div>';
		echo '</div>';
	}
	function get_moreposts_card($where) {
		echo '<div class="card" id="Card-MoreNotes">';
		printf('<div class="card-notfound" onclick="Fortscript.addPosts(\'%s\');">', $where);
		echo 'Get more notes';
		echo '</div>';
		echo '</div>';		
	}
	function get_user_card($userid) {
		$database = new Database();
		$row = $database->get_profile_info($userid);
		
		// Profile picture
		echo '<div class="mr">';
		printf('<a href="profile?id=%s">', $userid);
		$this->get_user_profile_picture($row[1], $row[0]);
		echo '</a>';
		echo '</div>';
		
		// User information
		echo '<div class="profile-information flex-container column">';
		// Full Name
		echo '<span class="profile-username">';
		printf('<a href="profile?id=%s">', $userid);
		echo $row[1];
		echo '</a>';
		echo '</span>';
		// Grade and Section
		echo '<span class="subtitle">';
		printf('Grade %s', $database->get_section_displayname($row[2]));
		$this->get_access_level($row[7]);
		echo '</span>';
		echo '</div>';
	}
	function get_users($query) {
		$database = new Database();
		$result = $database->get_profile_info(null, true, $query);
		if (!isset($result)) {
			$this->get_message_card($this->messages["nousers"]);
			return;
		}
		while ($row = $result->fetch_row()) {
			echo '<div class="card">';
			echo '<div class="flex-container align-center">';
			$this->get_user_card($row[0]);
			echo '</div>';
			echo '</div>';
		}
	}
	function get_access_level($accessid) {
		echo '<div class="flex-container align-center subtitle bold">';
		switch ($accessid) {
			case 0:
				echo '<span class="material-icons md-18">person</span>';
				echo 'Student';
				break;
			case 1:
				echo '<span class="material-icons md-18">school</span>';
				echo 'Teacher';
				break;
			case 2:
				echo '<span class="material-icons md-18">school</span>';
				echo 'Principal';
				break;
		}
		echo '</div>';
	}
	function get_user_groups($userid) {
		$database = new Database();
		$profile_info = $database->get_profile_info($userid);
		
		$result = $database->get_joined_groups($profile_info[3]);
		while ($row = $result->fetch_row()) {
			printf('<a href="groups?id=%s">', $row[0]);
			echo '<li>';
			echo $row[1];
			echo '</li>';
			echo '</a>';
		}
	}
	function get_group_name($groupid = null) {
		$database = new Database();
		$group = $database->get_group_displayname($groupid);
		if (isset($group)) {
			echo $group;
		}
	}
	function get_user_posts($groupid = null, $has_card = true, $postid = null,
							$show_category = true, $userid = null,
							$is_story = false, $sort_bydate = true, $limit = 10, $offset = 0, $search_query = null) {
		$database = new Database();
		$sectionid = $database->get_profile_info($this->get_user_id())[2];
		$result = $database->get_posts($groupid, $sectionid, $postid, $userid, $sort_bydate, $limit, $offset, $search_query);

		if (!isset($result) || (isset($postid) && $postid == 0)) {
			$this->get_message_card($this->messages["notfound"]);
			return;
		}

		// $row[0] = post ID
		// $row[1] = post date
		// $row[2] = post content
		// $row[3] = user ID
		// $row[4] = user section
		// $row[5] = user full name
		// $row[6] = user profile picture
		// $row[7] = user access level
		// $row[8] = group name where post is located
		while ($row = $result->fetch_row()) {
			if ($has_card) {
				echo '<div class="card">';
			}
			echo '<div class="card-post">';
			
			printf('<div class="mr"><a href="profile?id=%s">', $row[3]);
				$this->get_user_profile_picture($row[5], $row[3]);
			echo '</a></div>';
			
			echo '<div class="post-content">';
			echo '<div class="post-header flex-container align-start justify-sb">';
			
			echo '<div class="post-header-information flex-container align-center">';
			printf('<span class="profile-username mr"><a href="profile?id=%s">%s</a></span>', $row[3], $row[5]);
			$this->get_access_level($row[7]);
			if ($show_category) {
				printf('<span class="post-category ml">to %s</span>', $row[8]);
			}
			echo '</div>';
			
			if ($this->get_user_id() == $row[3]) {
				printf('<div class="post-header-controls" postid="%s">', $row[0]);
				echo '<div class="button no-padding material-icons md-18 delete-post" onclick="Fortscript.deletePost(event)">delete</div>';
				echo '<div class="button no-padding material-icons md-18 edit-post" onclick="Fortscript.editPost(event)">edit</div>';
				echo '</div>';
			}
			echo '</div>';
			
			echo '<div class="largetitle">';
			echo nl2br($row[2]);
			echo '</div>';
			echo '</div>';
			
			echo '</div>';
			
			echo '<div class="card-footer subtitle flex-container no-padding">';
			
			if (!$is_story) {
				echo '<div class="post-controls">';
				echo '<div class="button flex-container align-center">';
				echo '<span class="material-icons md-18 mr">comment</span>';
				printf('<a href="story.php?id=%s">Comments</a>', $row[0]);
				echo '</div>';
				echo '</div>';
			}
			
			echo '<div class="post-datetime flex-container align-center justify-fe">';
			printf("Posted on: %s", date("Y-m-d", strtotime($row[1])));
			echo '</div>';
			
			echo '</div>';
			
			if ($has_card) {
				echo '</div>';
			}
			if ($is_story) {
				$this->get_newcomment_card();
				$this->get_post_comments($row[0]);
			}
		}
	}
	// TODO: Implement proper ajax for progressive posts
	function get_post_comments($postid, $limit = 999999, $offset = 0) {
		$database = new Database();
		$result = $database->get_comments($postid, $limit, $offset);

		if (!isset($result)) {
			$this->get_message_card($this->messages["nocomments"]);
			return;
		}

		// $row[0] = comment ID
		// $row[1] = parent post ID
		// $row[2] = comment content
		// $row[3] = comment date
		// $row[4] = user ID
		// $row[5] = user full name
		// $row[6] = user profile picture
		// $row[7] = user access level
		echo '<div class="card">';
		echo '<div class="card-header">';
		echo 'Comments';
		echo '</div>';
		while ($row = $result->fetch_row()) {
			echo '<div class="card-post">';
			
				printf('<div class="mr"><a href="profile?id=%s">', $row[4]);
					$this->get_user_profile_picture($row[5], $row[4]);
				echo '</a></div>';
				
				echo '<div class="post-content">';
					echo '<div class="post-header flex-container align-start justify-sb">';
						echo '<div class="post-header-information flex-container align-center">';
						printf('<span class="profile-username mr"><a href="profile?id=%s">%s</a></span>', $row[4], $row[5]);
						$this->get_access_level($row[7]);
						echo '</div>';
						
						if ($this->get_user_id() == $row[4]) {
							printf('<div class="post-header-controls" commentid="%s">', $row[0]);
							echo '<div class="button no-padding material-icons md-18 delete-post" onclick="Fortscript.deleteComment(event)">delete</div>';
							// echo '<div class="button no-padding material-icons md-18 edit-post" onclick="Fortscript.editPost(event)">edit</div>';
							echo '</div>';
						}
					echo '</div>';
					
					echo '<div class="largetitle">';
						echo nl2br($row[2]);
					echo '</div>';
				echo '</div>';
			
			echo '</div>';
			
			echo '<div class="card-footer subtitle flex-container no-padding">';			
			echo '<div class="post-datetime flex-container align-center justify-fe">';
			printf("Posted on: %s", date("Y-m-d", strtotime($row[3])));
			echo '</div>';
			echo '</div>';
		}
		echo '</div>';
	}
	function get_user_profile_picture($name, $userid) {
		echo '<div class="profile-picture flex-container">';
		$profile_image = sprintf('profiles/%s.jpg', $userid);
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $profile_image)) {
			printf('<img src="%s"/>', $profile_image);
		} else {
			echo substr($name, 0, 1);
		}
		echo '</div>';
	}
	function get_profile_card($userid) {
		printf('<div class="card profile-cover" style="background: rgb(150, 150, 150) url(\'profiles/%s_cover.jpg\'); background-size: cover;">', $userid);
		echo '<div class="flex-container profile">';
		$this->get_user_card($userid);
		echo '</div>';
		echo '</div>';
	}
	function get_section_options() {
		$database = new Database();
		$result = $database->get_sections();
		// $row[0] = section ID
		// $row[1] = display name
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_row()) {
				printf('<option value="%s">%s</option>', $row[0], $row[1]);
			}
		}
	}
	function get_group_options($only_subjects = false, $default_index = null) {
		$database = new Database();
		$profile_info = $database->get_profile_info($this->get_user_id());
		$result = $database->get_joined_groups($profile_info[3], $only_subjects);
		// $row[0] = group ID
		// $row[1] = display name
		// $row[2] = admins ID
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_row()) {
				if (isset($default_index) && $default_index == $row[0]) {
					printf('<option value="%s" selected>%s</option>', $row[0], $row[1]);
				} else {
					printf('<option value="%s">%s</option>', $row[0], $row[1]);
				}
			}
		}
	}
	function get_material_types_options($default_index = null) {
		$database = new Database();
		$result = $database->get_material_types();
		// $row[0] = type ID
		// $row[1] = display name
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_row()) {
				if (isset($default_index) && $default_index == $row[0]) {
					printf('<option value="%s" selected>%s</option>', $row[0], $row[1]);
				} else {
					printf('<option value="%s">%s</option>', $row[0], $row[1]);
				}
			}
		}
	}
	function get_newcomment_card() {
		echo '<div class="card">';
		echo '<div class="card-header">';
		echo 'Leave a comment';
		echo '</div>';
		echo '<div class="card-content flex-container column">';
		echo '<textarea id="Comment-area" placeholder="Type your comment here..."></textarea>';
		echo '<div class="flex-container mt">';
		echo '<input id="Comment-send" type="submit" name="submit" value="Post"/>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
	function get_newpost_card() {
		echo '<div class="card">';
		echo '<div class="card-header">';
		echo 'New Note';
		echo '</div>';
		echo '<div class="card-content flex-container column">';
		echo '<textarea id="Post-area" placeholder="Type your note here..."></textarea>';
		echo '<div class="flex-container mt">';
		echo '<span class="self-center mr">Group:</span>';
		echo '<select id="GroupSelector-Menu" class="mr">';
		$this->get_group_options();
		echo '</select>';
		echo '<input id="Post-send" type="submit" name="submit" value="Post"/>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
	function get_edit_card($postid) {
		$database = new Database();
		$content = $database->get_posts(null, null, $postid)->fetch_row()[2];
		echo '<div class="card">';
		echo '<div class="card-header">';
		echo 'Edit Note';
		echo '</div>';
		echo '<div class="card-content flex-container column">';
		printf('<textarea id="Edit-area" placeholder="Type your note here...">%s</textarea>', $content);
		echo '<input id="Edit-send" type="submit" name="submit" value="Edit" onclick="Fortscript.sendEditedPost();"/>';
		echo '</div>';
		echo '</div>';
	}

	function get_material_types_card($groupid) {
		$database = new Database();
		$result = $database->get_material_types();

		if (!isset($result)) {
			return;
		}
		
		// $row[0] = type ID
		// $row[1] = display name
		echo '<div class="card min-padding flex-container">';
		echo '<div class="card-header no-border mr">';
		echo 'Filter by:';
		echo '</div>';
		echo '<ul class="sidebar-navigation no-border flex-container">';
		while ($row = $result->fetch_row()) {
			printf('<li><a href="materials?group=%s&type=%s">%s', $groupid, $row[0], $row[1]);
			echo '</a></li>';
		}
		echo '</ul>';
		echo '</div>';
	}
	function get_material_groups_card($typeid) {
		$database = new Database();
		$profile_info = $database->get_profile_info($this->get_user_id());
		$result = $database->get_joined_groups($profile_info[3], true);
		
		while ($row = $result->fetch_row()) {
			printf('<a href="materials?group=%s&type=%s">', $row[0], $typeid);
			echo '<li>';
			echo $row[1];
			echo '</li>';
			echo '</a>';
		}
	}
	function get_material_list_card($typeid, $groupid) {
		$database = new Database();
		$user_can_edit = $database->user_can_edit($this->get_user_id());
		$result = $database->get_materials($typeid, $groupid, $this->get_user_id());

		echo '<div class="card">';
		
		echo '<div class="card-header flex-container justify-sb align-center">';
		echo $this->get_group_name($groupid);
		if ($user_can_edit) {
			echo '<a href="materials?action=new" class="flex-container button no-padding">';
			echo '<span class="material-icons">add</span>';
			echo 'Add Material';
			echo '</a>';
		}
		echo '</div>';
		
		if (!isset($result)) {
			$this->get_message_card($this->messages["nomaterials"]);
			return;
		}
		
		echo '<div class="card-post">';
		echo '<div class="post-content">';
		echo '<ul class="sidebar-navigation flex-container column">';
		// $row[0] = material ID
		// $row[1] = material type ID
		// $row[2] = material group ID
		// $row[3] = material display name
		// $row[4] = material description
		// $row[5] = grade level where material should be visible
		// $row[6] = material file name on server
		while ($row = $result->fetch_row()) {
			printf('<a href="materials?id=%s"><li>%s</li></a>', $row[0], $row[3]);
		}
		echo '</ul>';
		echo '</div>';
		echo '</div>';
		
		echo '</div>';
	}
	function get_material_view_card($materialid) {
		$database = new Database();
		$parsedown = new Parsedown();
		$parsedown->setSafeMode(true);
		$user_can_edit = $database->user_can_edit($this->get_user_id());
		$result = $database->get_materials(null, null, $this->get_user_id(), $materialid);
		
		if (!isset($result)) {
			$this->get_message_card($this->messages["notfoundmaterial"]);
			return;
		}
		
		echo '<div class="card">';
		
		// $row[0] = material ID
		// $row[1] = material type ID
		// $row[2] = material group ID
		// $row[3] = material display name
		// $row[4] = material description
		// $row[5] = grade level where material should be visible
		// $row[6] = material file name on server
		while ($row = $result->fetch_row()) {
			echo '<div class="card-post">';
			echo '<div class="post-content flex-container column">';
			printf('<span class="material-title self-center">%s</span>', $row[3]);
			echo '<span class="material-subject self-center">';
			echo $this->get_group_name($row[2]);
			echo '</span>';
			echo '<div class="flex-container align-center justify-center">';
			if (strlen(trim($row[6])) > 0) {
				printf('<a href="files/%s">', $row[6]);
				echo '<div class="button has-border">';
				echo '<span class="material-icons mr">open_in_browser</span>';
				echo 'View attached file';
				echo '</div></a>';
			}
			if ($user_can_edit) {
				printf('<a href="materials?id=%s&action=edit">', $row[0]);
				echo '<div class="button has-border">';
				echo '<span class="material-icons mr">create</span>';
				echo 'Edit';
				echo '</div></a>';
				printf('<a href="materials?id=%s&action=delete">', $row[0]);
				echo '<div class="button has-border">';
				echo '<span class="material-icons mr">delete</span>';
				echo 'Delete';
				echo '</div></a>';
			}
			echo '</div>';
			// Check if we need description
			if (strlen(trim($row[4])) > 0) {
				echo '<div class="material-content">';
				echo $parsedown->text($row[4]);
				echo '</div>';
			}
			echo '</div>';
			echo '</div>';
		}
		
		echo '</div>';
	}
	function get_material_edit_card($materialid) {
		$database = new Database();
		$user_can_edit = $database->user_can_edit($this->get_user_id());
		$result = $database->get_materials(null, null, $this->get_user_id(), $materialid);
	
		if (!isset($result)) {
			$this->get_message_card($this->messages["notfoundmaterial"]);
			return;
		}
		if (!$user_can_edit) {
			$this->get_message_card($this->messages["nopermissions"]);
			return;
		}
		
		echo '<div class="card">';
		
		// $row[0] = material ID
		// $row[1] = material type ID
		// $row[2] = material group ID
		// $row[3] = material display name
		// $row[4] = material description
		// $row[5] = grade level where material should be visible
		// $row[6] = material file name on server
		$row = $result->fetch_row();
		echo '<div class="card-post">';
		echo '<form id="main-form" action="/common/post.php" method="POST" enctype="multipart/form-data" class="post-content flex-container column">';
		
		echo '<span class="material-title">Edit Material</span>';
		// action for post.php
		echo '<input type="hidden" name="action" value="edit_material" />';
		printf('<input type="hidden" name="id" value="%s" />', $row[0]);
		
		echo '<div class="flex-container align-center mb">';
			echo '<span class="column-1 bold">Title:</span>';
			printf('<input name="title" class="column-2" type="text" id="Material-Title" value="%s"/>', $row[3]);
		echo '</div>';
		
		echo '<div class="flex-container align-center mb">';
			echo '<span class="column-1 bold">Group:</span>';
			echo '<select name="group" class="column-2" id="Material-Group">';
			$this->get_group_options(true, $row[2]);
			echo '</select>';
		echo '</div>';
		
		echo '<div class="flex-container align-center mb">';
			echo '<span class="column-1 bold">Category:</span>';
			echo '<select name="category" class="column-2" id="Material-Type">';
			$this->get_material_types_options($row[1]);
			echo '</select>';
		echo '</div>';
		
		echo '<div class="flex-container align-center mb">';
			echo '<span class="column-1 bold">Attachment:</span>';
			printf('<input name="file" class="column-2" type="file" id="Material-File" value="%s"></input>', $row[3]);
		echo '</div>';
		
		echo '<div class="flex-container align-center mb">';
			echo '<span class="column-1 bold">Current attachment:</span>';
			if (strlen(trim($row[6])) > 0) {
				printf('<span class="column-2">%s</span>', $row[6]);
			}
		echo '</div>';
		
		echo '<span class="bold">Content or Description: (uses <a href="https://www.markdowntutorial.com/">Markdown</a> for formatting!)</span>';
		printf('<textarea name="content" class="resizable">%s</textarea>', $row[4]);
		
		echo '<button type="submit" class="button">Submit</button>';
		echo '</form>';
		echo '</div>';
		
		echo '</div>';
	}
	function get_material_add_card() {
		$database = new Database();
		$user_can_edit = $database->user_can_edit($this->get_user_id());

		if (!$user_can_edit) {
			$this->get_message_card($this->messages["nopermissions"]);
			return;
		}
		
		echo '<div class="card">';
		
		// $row[0] = material ID
		// $row[1] = material type ID
		// $row[2] = material group ID
		// $row[3] = material display name
		// $row[4] = material description
		// $row[5] = grade level where material should be visible
		// $row[6] = material file name on server
		$row = array(
			null, 1, 1, null, null, null, null
		);
		echo '<div class="card-post">';
		echo '<form id="main-form" action="/common/post.php" method="POST" enctype="multipart/form-data" class="post-content flex-container column">';
		
		printf('<span class="material-title">Create new Material</span>');
		// action for post.php
		echo '<input type="hidden" name="action" value="new_material" />';
		
		echo '<div class="flex-container align-center mb">';
			echo '<span class="column-1 bold">Title:</span>';
			printf('<input name="title" class="column-2" type="text" id="Material-Title" value="%s"/>', $row[3]);
		echo '</div>';
		
		echo '<div class="flex-container align-center mb">';
			echo '<span class="column-1 bold">Group:</span>';
			echo '<select name="group" class="column-2" id="Material-Group">';
			$this->get_group_options(true, $row[2]);
			echo '</select>';
		echo '</div>';
		
		echo '<div class="flex-container align-center mb">';
			echo '<span class="column-1 bold">Category:</span>';
			echo '<select name="category" class="column-2" id="Material-Type">';
			$this->get_material_types_options($row[1]);
			echo '</select>';
		echo '</div>';
		
		echo '<div class="flex-container align-center mb">';
			echo '<span class="column-1 bold">Attachment:</span>';
			printf('<input name="file" class="column-2" type="file" id="Material-File" value="%s"></input>', $row[3]);
		echo '</div>';
		
		echo '<div class="flex-container align-center mb">';
			echo '<span class="column-1 bold">Current attachment:</span>';
			if (strlen(trim($row[6])) > 0) {
				printf('<span class="column-2">%s</span>', $row[6]);
			}
		echo '</div>';
		
		echo '<span class="bold">Content or Description: (uses <a href="https://www.markdowntutorial.com/">Markdown</a> for formatting!)</span>';
		printf('<textarea name="content" class="resizable">%s</textarea>', $row[4]);
		
		echo '<button type="submit" class="button">Submit</button>';
		echo '</form>';
		echo '</div>';
		
		echo '</div>';
	}
	function get_material_delete_card($materialid) {
		$database = new Database();
		$user_can_edit = $database->user_can_edit($this->get_user_id());
		$result = $database->get_materials(null, null, $this->get_user_id(), $materialid);
	
		if (!isset($result)) {
			$this->get_message_card($this->messages["notfoundmaterial"]);
			return;
		}
		if (!$user_can_edit) {
			$this->get_message_card($this->messages["nopermissions"]);
			return;
		}
		
		echo '<div class="card">';
		
		// $row[0] = material ID
		$row = $result->fetch_row();
		echo '<div class="card-post">';
		echo '<form id="main-form" action="/common/post.php" method="POST" enctype="multipart/form-data" class="post-content flex-container column">';
		
		// action for post.php
		echo '<input type="hidden" name="action" value="delete_material" />';
		printf('<input type="hidden" name="id" value="%s" />', $row[0]);
		echo '<span class="material-title">Are you sure you want to permanently delete this material?</span>';
		
		echo '<button type="submit" class="button">Yes</button>';
		echo '<input type="button" class="button" onclick="location.assign(Fortscript.removeParameter(\'action\', location.href));" value="No"/>';
		echo '</form>';
		echo '</div>';
		
		echo '</div>';
	}
	// TODO: Add considerations for grade level.

	function get_settings_card($userid) {
		$database = new Database();
		$profile_info = $database->get_profile_info($userid);
		
		// $row[0] = user ID
		// $row[1] = full name
		// $row[2] = section ID
		// $row[3] = joined groups
		// $row[6] = grade level
		// $row[7] = access level

		echo '<div class="card">';
			echo '<div class="card-header">';
			echo 'Settings';
			echo '</div>';
			echo '<div><form action="/common/post" method="POST" enctype="multipart/form-data" class="card-content flex-container column">';
			
				echo '<input type="hidden" name="action" value="save_settings" />';
				echo '<div class="flex-container align-center mb">';
					echo '<span class="column-1 bold">Name:</span>';
					echo $profile_info[1];
				echo '</div>';
				echo '<div class="flex-container align-center mb">';
					echo '<span class="column-1 bold">Section:</span>';
					echo $database->get_section_displayname($profile_info[2]);
				echo '</div>';
				echo '<div class="flex-container align-center mb">';
					echo '<span class="column-1 bold">Grade Level:</span>';
					echo $profile_info[6];
				echo '</div>';
				echo '<div class="flex-container align-center mb">';
					echo '<span class="column-1 bold">Access Level:</span>';
					echo $this->get_access_level($profile_info[7]);
				echo '</div>';
				echo '<div class="flex-container align-center mb">';
					echo '<span class="column-1 bold">Email:</span>';
					echo '<input type="button" value="Change Email"/>';
				echo '</div>';
				echo '<div class="flex-container align-center mb">';
					echo '<span class="column-1 bold">Password:</span>';
					echo '<input type="button" value="Change Password"/>';
				echo '</div>';
				echo '<div class="flex-container align-center mb">';
					echo '<span class="column-1 bold">Profile Picture:</span>';
					echo '<input name="profile" type="file"/>';
				echo '</div>';
				echo '<div class="flex-container align-center mb">';
					echo '<span class="column-1 bold">Cover Photo:</span>';
					echo '<input name="cover" type="file"/>';
				echo '</div>';
				echo '<div class="flex-container align-center mb">';
					echo '<span class="column-1 bold">Page Accent Color:</span>';
					printf('<input id="Picker-Accent-Color" name="accent_color" type="colorpicker" value="%s"/>', $profile_info[8]);
				echo '</div>';
				echo '<div class="flex-container align-center mb">';
					echo '<span class="column-1 bold">Page Background Color:</span>';
					printf('<input id="Picker-Background-Color" name="page_color" type="colorpicker" value="%s"/>', $profile_info[9]);
				echo '</div>';
				echo '<div class="flex-container align-center mb">';
					echo '<span class="column-1 bold">Header Background Color:</span>';
					printf('<input id="Picker-Header-Color" name="header_color" type="colorpicker" value="%s"/>', $profile_info[10]);
				echo '</div>';
				echo '<button type="submit" class="button">Submit</button>';
			echo '</form></div>';
		echo '</div>';
	}
	
	function get_search_results($userid, $query) {
		if (strlen($this->sanitize($query)) > 0) {
			echo '<div class="card">';
			echo '<div class="card-header">';
			echo 'Posts';
			echo '</div>';
			$this->get_user_posts(null, true, null, true, null, false, true, 9999999, 0, $query);
			echo '</div>';
			
			echo '<div class="card">';
			echo '<div class="card-header">';
			echo 'Users';
			echo '</div>';
			$this->get_users($query);
			echo '</div>';
		} else {
			$this->get_message_card($this->messages["noquery"]);
		}
	}
	
	function get_color_container() {
		$database = new Database();
		$result = $database->get_profile_info($this->get_user_id());
		printf('<div id="ColorContainer" accent="%s" page="%s" header="%s"></div>', $result[8], $result[9], $result[10]);
	}
}

?>