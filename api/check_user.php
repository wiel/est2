<?php
	require_once ("../include/functions.php");
	/* test */

	$user = urldecode ($_REQUEST ["user"]);
	$role = $_REQUEST ["role"];
	$cb   = $_REQUEST ["cb"];
	$logfile = "/var/www1/est/live/writable/debug.log";
	$date = date ("Y-m-d H:i:s");
	// file_put_contents ($logfile, "$date check_user: user: '$user' - role: '$role' - cb: '$cb'\n", FILE_APPEND);
	// error_log ("$date check_user: user: '$user' - role: '$role' - cb: '$cb'");

	if (!isset($_SESSION ["user"])){
		if (isset($_COOKIE ["user"])){
			$check_user = urldecode ($_COOKIE ["user"]);
			$_SESSION ["user"] = urldecode ($_COOKIE ["user"]);
		}else{
			$check_user = "";
		}
	}else{
		$check_user = urldecode ($_SESSION ["user"]);
	}
	if (!isset($_SESSION ["role"])){
		if (isset($_COOKIE ["role"])){
			$check_role = $_COOKIE ["role"];
			$_SESSION ["role"] = $_COOKIE ["role"];
		}else{
			$check_role = "";
		}
	}else{
		$check_role=$_SESSION ["role"];
	}

	if (!isset($_SESSION["pwmd"])){
		if (isset($_COOKIE ["pw"])){
			$pwmd = md5($_COOKIE ["pw"]);
			$_SESSION ["pwmd"] = $pwmd;
		}else{
			$pwmd = "";
		}
	}else{
		$pwmd = $_SESSION ["pwmd"];
	}
	// $pwmd = md5($_COOKIE ["pw"]);
	// $_SESSION ["pwmd"] = $pwmd;

	$result = new stdClass();
	$result->check = "nok";
	// if ($user && $role && $user==$_SESSION['user'] && $role==$_SESSION['role']){
	// file_put_contents ($logfile, "check_user: user: '$user' - role: '$role' - cb: '$cb' - check_user: '$check_user' - check_role: '$check_role'\n", FILE_APPEND);
	if ($user && $role && $user==$check_user && $role==$check_role){
		$pr = "SELECT role FROM users WHERE user=:user AND pw=:pw"; 
		// file_put_contents ($logfile, "pw-{$_COOKIE["pw"]} $pr\n", FILE_APPEND);
		$st = $db->prepare($pr);
		$ex ["user"] = $user;
		$ex ["pw"]   = $pwmd;
		// file_put_contents ($logfile, print_r($ex,1), FILE_APPEND);
		$st->execute($ex);
		$rw = $st->fetch(PDO::FETCH_ASSOC);
		if ($role == $rw ["role"]){
			// file_put_contents ($logfile, "role: req: '$role', db: '{$rw ["role"]}'\n", FILE_APPEND);
			$result->check = "ok";
		}
	}
		
	header('Content-type: application/json');
	echo json_encode ($result);
?>