<?php

require_once "config.php";

class Database {
	// Connection
	private $conn;
	function create_connection() {
		$this->conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		if ($this->conn->connect_error) {
			die("Connection failed: " . $this->conn->connect_error);
		}
	}
	function close_connection() {
		$this->conn->close();
	}

	// Login
	function is_auth_valid($email, $password) {
		$userid = $this->get_userid($email, $password);
		if (isset($userid)) {
			return true;
		}
		return false;
	}
	
	// Get
	function get_userid($email, $password) {
		$this->create_connection();
		
		$sql = "SELECT userEmail, userPassword, userID FROM user WHERE userEmail = '$email'";
		$result = $this->conn->query($sql);
		
		$this->close_connection();
		
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_row()) {
				// $row[0] = if email is found
				// $row[1] = hashed password
				// $row[2] = user id
				if ((md5($password) == $row[1])) {
					return $row[2];
				}
			}
		}
		return null;
	}
	function get_profile_info($userid) {
		$this->create_connection();
		
		// Needed to show text with accent marks properly
		$sql = "SET NAMES utf8mb4";
		$this->conn->query($sql);
		
		$sql = "SELECT * FROM profile WHERE userID = '$userid'";
		$result = $this->conn->query($sql);
				
		$this->close_connection();
		
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_row()) {
				// $row[0] = user ID
				// $row[1] = full name
				// $row[2] = section ID
				// $row[3] = joined groups
				// $row[4] = (to be removed) link to profile picture
				// $row[5] = (to be removed) link to cover photo
				// $row[6] = grade level
				// $row[7] = access level
				return $row;
			}
		}
	}
	function get_section_displayname($sectionid) {
		$this->create_connection();

		$sql = "SELECT * FROM sections WHERE sectionID = '$sectionid'";
		$result = $this->conn->query($sql);

		$this->close_connection();
		
		// $row[0] = section ID
		// $row[1] = display name
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_row()) {
				return $row[1];
			}
		}
	}
	function get_group_displayname($groupid) {
		$this->create_connection();

		$sql = "SELECT * FROM groups WHERE groupID = '$groupid'";
		$result = $this->conn->query($sql);

		$this->close_connection();
		
		// $row[0] = section ID
		// $row[1] = display name
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_row()) {
				return $row[1];
			}
		}
	}
	function get_sections() {
		$this->create_connection();

		$sql = "SELECT * FROM sections";
		$result = $this->conn->query($sql);

		$this->close_connection();
		
		// $row[0] = section ID
		// $row[1] = display name
		if ($result->num_rows > 0) {
			return $result;
		}
	}
	function get_joined_groups($groupids) {
		$this->create_connection();

		$sql = "SELECT * FROM groups WHERE groupID in ($groupids)";
		$result = $this->conn->query($sql);
				
		$this->close_connection();
		
		// $row[0] = group ID
		// $row[1] = display name
		// $row[2] = admins ID
		if ($result->num_rows > 0) {
			return $result;
		}
	}
	function get_posts($groupid = null, $sectionid = null, $postid = null,
					   $userid = null, $sort_bydate = true, $limit = 10,
					   $offset = 0) {
		$this->create_connection();
		
		// Needed to show text with accent marks properly
		$sql = "SET NAMES utf8mb4";
		$this->conn->query($sql);

		$sql =  "SELECT postID, postDate, postContent, userID, sections.displayName, " .
				"fullName, profilePicture, accessLevel, groups.displayName " .
				"FROM posts " .
				"INNER JOIN sections ON posts.sectionID = sections.sectionID " .
				"INNER JOIN profile ON posts.postUserID = profile.userID " .
				"INNER JOIN groups ON posts.postGroupID = groups.groupID " .
				"WHERE 1 = 1 ";

		if ($sectionid) {
			$sql .= " AND posts.sectionID = '$sectionid'";
		}
		if ($groupid) {
			$sql .= " AND posts.postGroupID = '$groupid'";
		}
		if ($postid) {
			$sql .= " AND postID = '$postid'";
		}
		if ($userid) {
			$sql .= " AND userID = '$userid'";
		}
		if ($sort_bydate) {
			$sql .= " ORDER BY postDate DESC, postID DESC";
		}
		
		// Limit and offset must come after all parameters
		$sql .= " LIMIT $offset, $limit";

		$result = $this->conn->query($sql);

		$this->close_connection();
		
		// $row[0] = post ID
		// $row[1] = post date
		// $row[2] = post content
		// $row[3] = user ID
		// $row[4] = user section
		// $row[5] = user full name
		// $row[6] = user profile picture
		// $row[7] = user access level
		// $row[8] = group name where post is located
		if ($result->num_rows > 0) {
			return $result;
		}
	}
	function get_comments($postid, $limit = 10, $offset = 0) {
		$this->create_connection();
		
		// Needed to show text with accent marks properly
		$sql = "SET NAMES utf8mb4";
		$this->conn->query($sql);

		$sql =  "SELECT commentID, parentPostID, commentContent, commentDate, " .
				"profile.userID, profile.fullName, profile.profilePicture, " .
				"profile.accessLevel " .
				"FROM comments INNER JOIN profile ON commentUserID = profile.userID " .
				"WHERE parentPostID = $postid";
		
		// Limit and offset must come after all parameters
		$sql .= " LIMIT $offset, $limit";

		$result = $this->conn->query($sql);

		$this->close_connection();
		
		// $row[0] = comment ID
		// $row[1] = parent post ID
		// $row[2] = comment content
		// $row[3] = comment date
		// $row[4] = user ID
		// $row[5] = user full name
		// $row[6] = user profile picture
		// $row[7] = user access level
		if ($result->num_rows > 0) {
			return $result;
		}
	}

	// Posts
	function create_post($userid, $groupid, $content, $sectionid) {
		$this->create_connection();
		
		$datenow = date("Y-m-d");
		$sql = "INSERT INTO posts (postUserID, postDate, postGroupID, postContent, sectionID) " .
			   "VALUES ('$userid', '$datenow', '$groupid', '$content', '$sectionid')";
		$response = false;
		if ($this->conn->query($sql) === TRUE) {
			$response = true;
		} else {
			echo "Error: " . $sql . "<br>" . $this->conn->error;
		}

		$this->close_connection();
		return $response;
	}
	function delete_post($postid) {
		$this->create_connection();
		
		$sql = "DELETE FROM posts WHERE posts.postID = $postid";
		$response = false;
		if ($this->conn->query($sql) === TRUE) {
			$response = true;
		} else {
			echo "Error: " . $sql . "<br>" . $this->conn->error;
		}

		$this->close_connection();
		return $response;
	}
	function edit_post($postid, $content) {
		$this->create_connection();
				
		$sql = "UPDATE posts SET postContent = '$content' WHERE posts.postID = $postid";
		$response = false;
		if ($this->conn->query($sql) === TRUE) {
			$response = true;
		} else {
			echo "Error: " . $sql . "<br>" . $this->conn->error;
		}

		$this->close_connection();
		return $response;
	}

	// Comments
	function create_comment($userid, $content, $postid) {
		$this->create_connection();
		
		$datenow = date("Y-m-d");
		$sql = "INSERT INTO comments (parentPostID, commentUserID, commentContent, commentDate) " .
			   "VALUES ('$postid', '$userid', '$content', '$datenow')";
		$response = false;
		if ($this->conn->query($sql) === TRUE) {
			$response = true;
		} else {
			echo "Error: " . $sql . "<br>" . $this->conn->error;
		}

		$this->close_connection();
		return $response;
	}
	function delete_comment($commentid) {
		$this->create_connection();
		
		$sql = "DELETE FROM comments WHERE comments.commentID = $commentid";
		$response = false;
		if ($this->conn->query($sql) === TRUE) {
			$response = true;
		} else {
			echo "Error: " . $sql . "<br>" . $this->conn->error;
		}

		$this->close_connection();
		return $response;
	}
	function edit_comment($postid, $content) {
		// TODO: Stubbed function
	}

	// Materials
	function get_material_types() {
		$this->create_connection();

		$sql = "SELECT * FROM material_types";
		$result = $this->conn->query($sql);

		$this->close_connection();
		
		// $row[0] = type ID
		// $row[1] = display name
		if ($result->num_rows > 0) {
			return $result;
		}
	}
	function get_materials($typeid, $groupid, $userid) {
		$gradelevel = $this->get_profile_info($userid)[6];
		$this->create_connection();
		
		// Needed to show text with accent marks properly
		$sql = "SET NAMES utf8mb4";
		$this->conn->query($sql);

		$sql =  "SELECT materialID, materialTypeID, materialGroupID, " .
				"materialDisplayName, gradeLevel, fileName " .
				"FROM materials " .
				"WHERE materialTypeID = $typeid " .
				"AND materialGroupID = $groupid " .
				"AND gradeLevel = $gradelevel";

		$result = $this->conn->query($sql);

		$this->close_connection();
		
		// $row[0] = material ID
		// $row[1] = material type ID
		// $row[2] = material group ID
		// $row[3] = material display name
		// $row[4] = grade level where material should be visible
		// $row[5] = material file name on server
		if ($result->num_rows > 0) {
			return $result;
		}
	}
}

?>