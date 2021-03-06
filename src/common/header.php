<!-- START TEMPORARY WORKAROUND -->
<?php
	include_once $_SERVER['DOCUMENT_ROOT'] . "/common/page.php";
	$page->get_color_container();
?>
<!-- END -->
<header id="Header" class="flex-container sticky">
	<ul class="navigation flex-container align-center justify-se">
		<li>
			<a href="dashboard"><b><?php echo SITE_NAME; ?></b></a>
		</li>
		<li class="hastooltip">
			<a class="material-icons" href="dashboard">home</a>
			<span class="tooltip">Home</span>
		</li>
		<li class="hastooltip">
			<a class="material-icons" href="materials">local_library</a>
			<span class="tooltip">Materials</span>
		</li>
		<li class="hastooltip">
			<a class="material-icons" href="schedule">event</a>
			<span class="tooltip">Schedule</span>
		</li>
		<li class="hastooltip">
			<a class="material-icons" href="dictionary">book</a>
			<span class="tooltip">Dictionary</span>
		</li>
		<li>
			<form action="search" method="GET">
				<input name="q" id="Search" class="search" type="search" placeholder="Search posts, groups, users, and more"/>
			</form>
		</li>
		<!--li class="hastooltip">
			<a class="material-icons" href="messages">mail</a>
			<span class="tooltip">Messages</span>
		</li-->
		<li class="flex-container">
			<div class="hastooltip">
				<a class="material-icons" href="profile">account_circle</a>
				<span class="tooltip">Profile</span>
			</div>
			<a id="Profile-Dropdown-Link" class="material-icons dropdown-button"
			   onclick="Fortscript.showDropDown('Profile-Dropdown');" href="#">arrow_drop_down</a>
			<div id="Profile-Dropdown" class="dropdown-content">
				<a href="settings">Settings</a>
				<a href="logout">Logout</a>
			</div>
		</li>
	</ul>
</header>
