<?php
	// Database
	define("DB_HOST", "fortschritt.com");
	define("DB_NAME", "learntable");
	define("DB_USERNAME", "root");
	define("DB_PASSWORD", "");
	
	// Use bcrypt instead of MD5 for password hashing
	// TODO: implement bcrypt
    define("BCRYPT", true);
    define("BCRYPT_ROUNDS", 12);
	
	// Site
	define("IS_MAINTENANCE", false);
	define("SITE_NAME", "Fortschritt");
?>