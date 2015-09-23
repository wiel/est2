<?php
	require_once ("../include/functions.php");
	// file_put_contents ($logfile, "instrTxt: ".print_r($_REQUEST,1)."\n", FILE_APPEND);
	$id   = $_REQUEST ["id"];
	$text = $_REQUEST ["text"];
	$pr = "UPDATE audio SET instruction=:instruction WHERE id=:id";
	$st = $db->prepare($pr);
	$ex ["instruction"] = $text;
	$ex ["id"]          = $id;
	file_put_contents ($logfile, "instrTxt: $pr\n", FILE_APPEND);
	file_put_contents ($logfile, "instrTxt: ".print_r ($ex,1)."\n", FILE_APPEND);
	$result = new stdClass();
	if ($st->execute($ex)){
		$result->query = "ok";
	}else{
		$result->query = "nok";
	}
	header('Content-type: application/json');
	echo json_encode ($result);
?>