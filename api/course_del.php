<?php
	require_once ("../include/functions.php");
	check_role (array("logopedist"));

	if (isset ($_REQUEST["course_id"]))  $id = $_REQUEST ["course_id"];  else $id = 0;
	if (!($logopedist_id=$_SESSION ["id"])){
		$logopedist_id = $_COOKIE ["id"];
	}
	delete_course ($id, $logopedist_id);
	$result = "ok";
	
	header('Content-type: application/json');
	echo json_encode ($result);
?>