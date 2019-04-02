<!DOCTYPE html>
<?php require "common/config.php"; 
	  require "common/session.php"; ?>
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
        <title><?php echo SITE_NAME; ?> - Schedule</title>
		<link href="style/main.css" rel="stylesheet"/>
		<link href="style/material-icons.css" rel="stylesheet"/>
		<link href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700" rel="stylesheet"/>
		<script src="js/main.js" type="application/javascript"></script>
    </head>
    <body>
		<?php include "common/header.php" ?>
		<main class="content flex-container">
			<div class="card" id="Calendar">
				<iframe width="1000" height="500" src="https://calendar.google.com/calendar/embed?showTitle=0&amp;height=600&amp;wkst=1&amp;bgcolor=%23FFFFFF&amp;src=francisdominic.fajardo%40gmail.com&amp;color=%231B887A&amp;src=en.christian%23holiday%40group.v.calendar.google.com&amp;color=%2329527A&amp;src=addressbook%23contacts%40group.v.calendar.google.com&amp;color=%2328754E&amp;src=en.philippines%23holiday%40group.v.calendar.google.com&amp;color=%230F4B38&amp;ctz=Asia%2FManila" style="border-width:0" frameborder="0" scrolling="no"/>
			</div>
		</main>
    </body>
</html>
