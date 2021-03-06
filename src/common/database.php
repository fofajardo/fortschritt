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
	function user_can_edit($userid) {
		$this->create_connection();
				
		$sql = "SELECT accessLevel FROM profile WHERE userID = '$userid'";
		$result = $this->conn->query($sql);
				
		$this->close_connection();
		
		if ($result->num_rows > 0) {
			$accesslevel = $result->fetch_row()[0];
			if ($accesslevel >= 1) {
				return true;
			}
			return false;
		}
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
	function get_profile_info($userid = null, $all_results = false, $query = null) {
		$this->create_connection();
		
		// Needed to show text with accent marks properly
		$sql = "SET NAMES utf8mb4";
		$this->conn->query($sql);
		
		$sql = "SELECT * FROM profile";
		if ($userid) {
			$sql .= " WHERE userID = '$userid'";
		}
		if ($query) {
			$sql .= " WHERE fullName LIKE '%$query%'";
		}
		$result = $this->conn->query($sql);
				
		$this->close_connection();
		
		if ($result->num_rows > 0) {
			if ($all_results) {
				return $result;
			} else {
				while ($row = $result->fetch_row()) {
					// $row[0] = user ID
					// $row[1] = full name
					// $row[2] = section ID
					// $row[3] = joined groups
					// $row[4] = (to be removed) link to profile picture
					// $row[5] = (to be removed) link to cover photo
					// $row[6] = grade level
					// $row[7] = access level
					// $row[8] = page accent color
					// $row[9] = page background color
					// $row[10] = header color
					return $row;
				}
			}
		}
	}
	function get_user_info($userid) {
		$this->create_connection();
		
		// Needed to show text with accent marks properly
		$sql = "SET NAMES utf8mb4";
		$this->conn->query($sql);
		
		$sql = "SELECT * FROM user WHERE userID = '$userid'";
		$result = $this->conn->query($sql);
				
		$this->close_connection();
		
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_row()) {
				// $row[0] = user ID
				// $row[1] = user name
				// $row[2] = user password (hashed)
				// $row[3] = user email
				// $row[4] = last login (not updated)
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
	function get_joined_groups($groupids, $only_subjects = false) {
		$this->create_connection();

		$sql = "SELECT * FROM groups WHERE groupID in ($groupids)";
		if ($only_subjects) {
			$sql .= " AND canStoreMaterials = 1";
		}
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
					   $offset = 0, $search_query = null) {
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
		if ($search_query) {
			$sql .= " AND postContent LIKE '%$search_query%'";
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
		$sql .= " ORDER BY commentDate ASC, commentID ASC";
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
	function get_materials($typeid, $groupid, $userid, $materialid = null) {
		if ($userid) {
			$gradelevel = $this->get_profile_info($userid)[6];
		}
		$this->create_connection();
		
		// Needed to show text with accent marks properly
		$sql = "SET NAMES utf8mb4";
		$this->conn->query($sql);

		$sql =  "SELECT materialID, materialTypeID, materialGroupID, " .
				"materialDisplayName, materialDescription, gradeLevel, fileName " .
				"FROM materials " .
				"WHERE 1 = 1";
		if ($typeid) {
			$sql .= " AND materialTypeID = $typeid";
		}
		if ($groupid) {
			$sql .= " AND materialGroupID = $groupid";
		}
		if ($userid) {
			$sql .= " AND gradeLevel = $gradelevel";
		}
		if ($materialid) {
			$sql .= " AND materialID = $materialid";
		}
		$result = $this->conn->query($sql);

		$this->close_connection();
		
		// $row[0] = material ID
		// $row[1] = material type ID
		// $row[2] = material group ID
		// $row[3] = material display name
		// $row[4] = material description
		// $row[5] = grade level where material should be visible
		// $row[6] = material file name on server
		if ($result->num_rows > 0) {
			return $result;
		}
	}
	// title, group, content, file
	function edit_material($materialid, $title, $groupid, $content, $typeid, $filename = null) {
		$this->create_connection();
				
		$sql = "UPDATE materials SET materialDisplayName = '$title', " .
			   "materialDescription = '$content', " .
			   "materialGroupID = '$groupid', " .
			   "materialTypeID = '$typeid'";
		if ($filename) {
			$sql .= ", fileName = '$filename'";
		}
		$sql .= " WHERE materialID = $materialid";
		$response = false;
		if ($this->conn->query($sql) === TRUE) {
			$response = true;
		} else {
			echo "Error: " . $sql . "<br>" . $this->conn->error;
		}

		$this->close_connection();
		return $response;
	}
	function create_material($title, $groupid, $content, $typeid, $filename = null) {
		$this->create_connection();
				
		// FIXME: NO GRADE LEVEL SETTING!!!
		$sql = "INSERT INTO materials (materialTypeID, materialGroupID, materialDisplayName, materialDescription, gradeLevel, fileName) " .
			   "VALUES ('$typeid', '$groupid', '$title', '$content', '9', '$filename')";
			   
		$response = false;
		if ($this->conn->query($sql) === TRUE) {
			$response = true;
		} else {
			echo "Error: " . $sql . "<br>" . $this->conn->error;
		}

		$this->close_connection();
		return $response;
	}
	function delete_material($materialid) {
		$this->create_connection();
		
		$sql = "DELETE FROM materials WHERE materialID = $materialid";
		$response = false;
		if ($this->conn->query($sql) === TRUE) {
			$response = true;
		} else {
			echo "Error: " . $sql . "<br>" . $this->conn->error;
		}

		$this->close_connection();
		return $response;
	}

	function edit_user_info($userid, $username = null, $password = null, $email = null, $logindate = null) {
		$this->create_connection();
				
		$sql = "UPDATE user SET userID = $userid";
		if ($username) {
			$sql .= ", userName = '$username'";
		}
		if ($password) {
			$sql .= ", userPassword = '$password'";
		}
		if ($email) {
			$sql .= ", userEmail = '$email'";
		}
		if ($logindate) {
			$sql .= ", lastLogin = '$logindate'";
		}
		
		$sql .= " WHERE userID = $userid";
		$response = false;
		if ($this->conn->query($sql) === TRUE) {
			$response = true;
		} else {
			echo "Error: " . $sql . "<br>" . $this->conn->error;
		}

		$this->close_connection();
		return $response;
	}
	function edit_profile_info($userid, $fullname = null, $sectionid = null,
			 $joined_groups = null, $gradelevel = null, $accesslevel = null,
			 $pagecolor = null, $accentcolor = null, $headercolor = null) {
		// STUB
		$this->create_connection();
				
		$sql = "UPDATE profile SET userID = $userid";
		if ($fullname) {
			$sql .= ", fullName = '$fullname'";
		}
		if ($sectionid) {
			$sql .= ", sectionID = '$sectionid'";
		}
		if ($joined_groups) {
			$sql .= ", joinedGroups = '$joined_groups'";
		}
		if ($gradelevel) {
			$sql .= ", gradeLevel = '$gradelevel'";
		}
		if ($accesslevel) {
			$sql .= ", accessLevel = '$accesslevel'";
		}
		if ($pagecolor) {
			$sql .= ", pageColor = '$pagecolor'";
		}
		if ($accentcolor) {
			$sql .= ", accentColor = '$accentcolor'";
		}
		if ($headercolor) {
			$sql .= ", headerColor = '$headercolor'";
		}
		
		$sql .= " WHERE userID = $userid";
		$response = false;
		if ($this->conn->query($sql) === TRUE) {
			$response = true;
		} else {
			echo "Error: " . $sql . "<br>" . $this->conn->error;
		}

		$this->close_connection();
		return $response;
	 }
}

?>