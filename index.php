<!DOCTYPE html>
<?php require "common/config.php"; 
	  require "common/session.php"; ?>
<html lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
		<title><?php echo SITE_NAME; ?></title>
		<link href="style/main.css" rel="stylesheet"/>
		<link href="style/login.css" rel="stylesheet"/>
		<link href="style/material-icons.css" rel="stylesheet"/>
		<link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700" rel="stylesheet"/> 
	</head>
	<body class="body-gradient-effect">
		<main class="content flex-container align-center justify-center">
			<div class="card card-login flex-container column">
				<div class="card-header flex-container column align-center">
					<img id="FortschrittLogo" src="img/fortschritt3.png"/>
					<!--span id="LoginTitle"><?php echo SITE_NAME ?></span-->
					<span id="LoginSubtitle">Sign in with your <?php echo SITE_NAME ?> Account</span>
				</div>
				<div id="LoginForm" class="flex-container">
					<form class="flex-container column justify-fe" name="login" method="post" action="">
						Email
						<input type="text" name="email"/>
						Password
						<input type="password" name="password"/>
						<span>
							<input type="checkbox" name="rememberme" value="1" class="mr"/>
							<label>Remember Me</label>
						</span>
						<input type="submit" name="submit" value="Continue"/>
						<?php echo $login_response; ?>
					</form>
				</div>
			</div>
		</main>
	</body>
</html>
