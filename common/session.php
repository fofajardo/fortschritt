<?php

require_once "storage.php";

// TODO: Rewrite session file logic
// We must store a session cookie instead of storing username + password

$login_response = '';

// Logic for logout page
if ($_SERVER['PHP_SELF'] == "/logout.php") {
	$time = time() - 3600;
	setcookie('email', '', $time);
	setcookie('password', '', $time);
	setcookie('userid', '', $time);
	header('Location: /');
}

// Logic for login page
if ($_SERVER['PHP_SELF'] == "/index.php") {
	if(isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
		header('Location: dashboard');
	}
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$storage = new Storage();
		if (!empty($_POST['email']) && !empty($_POST['password'])) {
			$valid_auth = $storage->is_auth_valid($_POST['email'], $_POST['password']);
			$userid = $storage->get_userid($_POST['email'], $_POST['password']);
			if ($valid_auth) {
				if (isset($_POST['rememberme'])) {
					/* Set cookie to last 1 year */
					setcookie('email', $_POST['email'], time()+60*60*24*365, '/');
					setcookie('password', md5($_POST['password']), time()+60*60*24*365, '/');
					setcookie('userid', $userid, time()+60*60*24*365, '/');
				} else {
					/* Cookie expires when browser closes */
					setcookie('email', $_POST['email'], false, '/');
					setcookie('password', md5($_POST['password']), false, '/');
					setcookie('userid', $userid, false, '/');
				}
				header('Location: dashboard');
			} else {
				$login_response = 'Incorrect email/password';
			}
		} else {
			$login_response = 'You must supply an email and password.';
		}
	}
} else {
	// Logic for all pages except the login page
	// Logs out the person if session cookies are missing
	if(!isset($_COOKIE['email']) || !isset($_COOKIE['password'])) {
		header('Location: /');
	}
}

?>