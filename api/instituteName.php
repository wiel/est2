<?php
	require_once ("../include/functions.php");
	// file_put_contents ($logfile, "instrTxt: ".print_r($_REQUEST,1)."\n", FILE_APPEND);
	$id   = $_REQUEST ["id"];
	$name = $_REQUEST ["name"];
	$pr = "UPDATE institutes SET name=:name WHERE id=:id";
	$st = $db->prepare($pr);
	$ex ["name"] = $name;
	$ex ["id"]   = $id;
	// file_put_contents ($logfile, "nameTxt: $pr\n", FILE_APPEND);
	// file_put_contents ($logfile, "nameTxt: ".print_r ($ex,1)."\n", FILE_APPEND);
	$result = new stdClass();
	if ($st->execute($ex)){
		$result->query = "ok";
	}else{
		$result->query = "nok";
	}
	header('Content-type: application/json');
	echo json_encode ($result);
?>