<?php
	require_once ("../include/functions.php");
	
	$user = $_REQUEST ["user"];
	$pw   = $_REQUEST ["pw"];
	// $logfile = "/var/www1/est/live/writable/debug.log";
	$date = date ("Y-m-d H:i:s");
	// file_put_contents ($logfile, "$date login: user: $user - pw: $pw\n", FILE_APPEND);
	// error_log ("$date login: user: $user - pw: $pw");

	$pr = "SELECT role,id FROM users WHERE user='$user' AND pw=MD5('$pw')"; 
	// print ("$pr<hr/>\n");
	$st = $db->prepare($pr);
	$st->execute();
	$rw = $st->fetch(PDO::FETCH_ASSOC);
	$role = $rw ["role"];
	$id   = $rw ["id"];
	$_SESSION['user'] = urldecode($user);
	$_SESSION['pwmd'] = md5 ($pw);
	$_SESSION['role'] = $role;
	$_SESSION['id']   = $id;
	setcookie ("user", urldecode($user), time()+3600*24*30, "/", ".ru.nl");
	setcookie ("pw",   $pw,   time()+3600*24*30, "/", ".ru.nl");
	setcookie ("role", $role, time()+3600*24*30, "/", ".ru.nl");
	setcookie ("id",   $id  , time()+3600*24*30, "/", ".ru.nl");
	$result = new stdClass();
	$result->login = $role;
	header('Content-type: application/json');
	echo json_encode ($result);
?>
	