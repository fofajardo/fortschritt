<?php

require_once "storage.php";

$page = new Page();

class Page {
	function get_user_id() {
		$cookie_name = 'userid';
		if(!isset($_COOKIE[$cookie_name])) {
			// Default user...
			return 0;
		}
		return $_COOKIE[$cookie_name];
	}
	function get_notfound_card() {
		echo '<div class="card" id="Card-NotFound">';
		echo '<div class="card-notfound">';
		echo 'No more notes.';
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
	function get_user_card() {
		$storage = new Storage();
		$row = $storage->get_profile_info($this->get_user_id());
		
		// Profile picture
		echo '<div class="mr">';
		printf('<div class="profile-picture flex-container">%s</div>',
				substr($row[1], 0, 1));
		echo '</div>';
		
		// User information
		echo '<div class="profile-information flex-container column">';
		// Full Name
		echo '<span class="profile-username">';
		echo $row[1];
		echo '</span>';
		// Grade and Section
		echo '<span class="subtitle">';
		printf('Grade %s', $storage->get_section_displayname($row[2]));
		echo '</span>';
		echo '</div>';
	}
	function get_user_groups() {
		$storage = new Storage();
		$profile_info = $storage->get_profile_info($this->get_user_id());
		
		$result = $storage->get_joined_groups($profile_info[3]);
		while ($row = $result->fetch_row()) {
			printf('<a href="groups?id=%s">', $row[0]);
			echo '<li>';
			echo $row[1];
			echo '</li>';
			echo '</a>';
		}
	}
	function get_group_name($groupid = null) {
		$storage = new Storage();
		$group = $storage->get_group_displayname($groupid);
		if (isset($group)) {
			echo $group;
		}
	}
	function get_user_posts($groupid = null, $has_card = true, $postid = null,
							$show_category = true, $userid = null,
							$is_story = false, $sort_bydate = true, $limit = 10, $offset = 0) {
		$storage = new Storage();
		$sectionid = $storage->get_profile_info($this->get_user_id())[2];
		$result = $storage->get_posts($groupid, $sectionid, $postid, $userid, $sort_bydate, $limit, $offset);

		if (!isset($result) || (isset($postid) && $postid == 0)) {
			$this->get_notfound_card();
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
			
			echo '<div class="mr">';
			printf('<div class="profile-picture flex-container">%s</div>',
					substr($row[5], 0, 1));
			echo '</div>';
			
			echo '<div class="post-content">';
			echo '<div class="post-header flex-container align-start justify-sb">';
			
			echo '<div class="post-header-information">';
			printf('<span class="profile-username mr">%s</span>', $row[5]);
			if ($show_category) {
				printf('<span class="post-category">to %s</span>', $row[8]);
			}
			echo '</div>';
			
			if ($this->get_user_id() == $row[3]) {
				printf('<div class="post-header-controls" postid="%s">', $row[0]);
				echo '<div class="button no-padding material-icons md-18 delete-post" onclick="Fortscript.deletePost(event)">delete_outline</div>';
				echo '<div class="button no-padding material-icons md-18 edit-post" onclick="Fortscript.editPost(event)">edit</div>';
				echo '</div>';
			}
			echo '</div>';
			echo '<div class="subtitle">';
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
		}	
	}
	function get_section_options() {
		$storage = new Storage();
		$result = $storage->get_sections();
		// $row[0] = section ID
		// $row[1] = display name
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_row()) {
				printf('<option value="%s">%s</option>', $row[0], $row[1]);
			}
		}
	}
	function get_group_options() {
		$storage = new Storage();
		$profile_info = $storage->get_profile_info($this->get_user_id());
		$result = $storage->get_joined_groups($profile_info[3]);
		// $row[0] = group ID
		// $row[1] = display name
		// $row[2] = admins ID
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_row()) {
				printf('<option value="%s">%s</option>', $row[0], $row[1]);
			}
		}
	}
	function get_newpost_card() {
		echo '<div class="card">';
		echo '<div class="card-header">';
		echo 'New Note';
		echo '</div>';
		echo '<div class="card-content flex-container column">';
		echo '<textarea id="Post-area" placeholder="Type your note here..."></textarea>';
		echo '<div class="flex-container mt">';
		echo '<select id="GroupSelector-Menu" class="mr">';
		$this->get_group_options();
		echo '</select>';
		echo '<input id="Post-send" type="submit" name="submit" value="Post"/>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
}

?>