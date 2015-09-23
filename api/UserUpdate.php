<?php
	require_once ("../include/functions.php");
	// file_put_contents ($logfile, "instrTxt: ".print_r($_REQUEST,1)."\n", FILE_APPEND);
	if (isset ($_REQUEST["id"]))      $id      = $_REQUEST ["id"];
	if (isset ($_REQUEST["user"]))    $user    = $_REQUEST ["user"];
	if (isset ($_REQUEST["pw"]))      $pw      = $_REQUEST ["pw"];
	if (isset ($_REQUEST["inst_id"])) $inst_id = $_REQUEST ["inst_id"];
	$result = new stdClass();
	if (isset($_REQUEST["id"]) && $id){
		if (isset($_REQUEST["user"]) && $user){
			$pr = "UPDATE users SET user=:user WHERE id=:id";
			$st = $db->prepare($pr);
			$ex ["user"] = $user;
			$ex ["id"]   = $id;
			// file_put_contents ($logfile, "nameTxt: $pr\n", FILE_APPEND);
			// file_put_contents ($logfile, "nameTxt: ".print_r ($ex,1)."\n", FILE_APPEND);
		}elseif (isset($_REQUEST["pw"]) && $pw){
			$pr = "UPDATE users SET pw=:pw WHERE id=:id";
			$st = $db->prepare($pr);
			$ex ["pw"] = md5 ($pw);
			$ex ["id"]   = $id;
		}elseif (isset($_REQUEST["inst_id"]) && $inst_id){
			$pr = "UPDATE users SET institute_id=:inst_id WHERE id=:id";
			$st = $db->prepare($pr);
			$ex ["inst_id"] = $inst_id;
			$ex ["id"]      = $id;
		}
		if (isset($st)){
			if ($st->execute($ex)){
				$result->query = "ok";
			}else{
				$result->query = "nok";
			}
		}else{
			$result->query = "nak";
		}
	}else{
		$result->query = "nak";
	}
	header('Content-type: application/json');
	echo json_encode ($result);
?>