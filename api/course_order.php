<?php
	require_once ("../include/functions.php");
	check_role (array("logopedist"));

	if (isset ($_REQUEST["course_ids"]))  $ids = trim ($_REQUEST["course_ids"]);  else $ids = array();
	order_courses ($ids);
	$result = "ok";
	
	header('Content-type: application/json');
	echo json_encode ($result);
?>