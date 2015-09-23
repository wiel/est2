<?php
	$_SESSION['user'] = "";
	$_SESSION['pwmd'] = "";
	$_SESSION['role'] = "";
	$logfile = "/var/www1/est/live/writable/debug.log";
	// file_put_contents ($logfile, "logoff - user: $user - role: $role\n", FILE_APPEND);
	setcookie ("user", "", time()+3600*24*30, "/", ".ru.nl");
	setcookie ("pw",   "", time()+3600*24*30, "/", ".ru.nl");
	setcookie ("role", "", time()+3600*24*30, "/", ".ru.nl");
	$result = new stdClass();
	$result->login = strval("");
	header('Content-type: application/json');
	echo json_encode ($result);
?>