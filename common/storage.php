<?php

require_once "config.php";

class Storage {
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
		$sql = "SET NAMES utf8";
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
		$sql = "SET NAMES utf8";
		$this->conn->query($sql);

		$sql =  "SELECT postID, postDate, postContent, userID, sections.displayName, " .
				"fullName, profilePicture, accessLevel, groups.displayName " .
				"FROM posts " .
				"INNER JOIN sections ON posts.sectionID = sections.sectionID " .
				"INNER JOIN profile ON posts.postUserID = profile.userID " .
				"INNER JOIN groups ON posts.postGroupID = groups.groupID " .
				"WHERE 1 = 1 ";
//echo $sql;
//printf("%s,%s,%s,%s,%s,%s,%s", $groupid, $sectionid, $postid, $userid, $sort_bydate, $limit, $offset);
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

	// Set
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
}

?>