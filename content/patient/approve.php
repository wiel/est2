<?php
	require_once ("../../include/functions.php");
	require_once ("../../include/Browser.php");

	check_role (array("patient"));

	if (isset ($_REQUEST["audio_id"])) $audio_id = $_REQUEST ["audio_id"]; else $audio_id = 0;
	
	if ($audio_id){
		approve_audio ($audio_id);
	}
?>
approve <?=$audio_id?> ok.