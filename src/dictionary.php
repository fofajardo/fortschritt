<!DOCTYPE html>
<?php require "common/config.php"; 
	  require "common/session.php";
	  require "common/page.php"; ?>
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
        <title><?php echo SITE_NAME; ?> - Dictionary</title>
		<link href="style/main.css" rel="stylesheet"/>
		<link href="style/material-icons.css" rel="stylesheet"/>
		<link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700" rel="stylesheet"/>
		<script src="js/main.js" type="application/javascript"></script>
    </head>
    <body>
		<?php include "common/header.php" ?>
		<?php include "common/modal_template.php" ?>
		<main class="content story flex-container">
			<div id="Feed">
				<div class="card">
					<div class="card-content">
						<form class="flex-container align-center column" action="https://duckduckgo.com/" method="GET">
							<h1>Search for a word.</h1>
							<input name="q" class="search" type="search" placeholder="Search for a word"/>
						</form>
					</div>
				</div>
			</div>
		</main>
    </body>
</html>
