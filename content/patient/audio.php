<?php
	require_once ("../../include/functions.php");
	// require_once ("../../include/Browser.php");

	check_role (array("patient"));

	if (isset ($_REQUEST["module_id"])) $module_id = $_REQUEST ["module_id"]; else $module_id = 0;
	if (isset ($_REQUEST["audio_id"]))  $audio_id  = $_REQUEST ["audio_id"];  else $audio_id  = 0;
	
	$patient_id = $_COOKIE ["id"];
	// $courses    = get_courses (0, $patient_id, 0);
	// $modules    = get_modules ();
	$audios     = get_audios ();
	// $module_ids = explode (" ", $courses[$active_course_id]["module_ids"]);
?>
<link href="/css/audio.css" type="text/css" rel="stylesheet" />

<div class="audio_close"  onClick="hidePopup();">X</div>

<?=$audios[$audio_id]["name"]?><hr/>

<br/>
<audio controls src="/audio/<?=$audios[$audio_id]["file"]?>" />
<br/>
<br/>
<button onclick="startRecording(this);">opnemen</button>
<button onclick="stopRecording(this);" disabled>stop</button>

<span id="recordingslist" style="visibility:hidden;"></span>
<br/>
<br/>
<div  id="log"></div>
<br/>

<div class="vu_meter" id="vu_meter">
	<canvas id="canvas" width="20" height="100" style="display: block; background-color:#333333; margin: 3px;"></canvas>
	<span id="vu_max" style="visibility:hidden;"></span>
</div>

<div class="audio_cancel" onClick="hidePopup();">sluiten</div>

<input type="hidden" name="user_audio_id" id="user_audio_id" value="0" />
<input type="submit" name="approve" id="approve" value="versturen" onClick="approveAudio()" />

<script>
	var moduleId = <?=intval($module_id)?>;
	var audioId  = <?=intval($audio_id)?>;
</script>
<script src="/js/dateFormat.js"></script>
<script src="/js/rec.js"></script>
<script src="/js/just_vu.js"></script>
<script>
	function hidePopup(){
		$(".audio_popup").css("visibility", "hidden");
	}
	
	function approveAudio (){
		// alert ("approve " + document.getElementById('user_audio_id').value);
		$("#audio_item_<?=$module_id?>_<?=$audio_id?>").css("background-color", "#91b7b7");
		$.post( "content/patient/approve.php", {audio_id: document.getElementById('user_audio_id').value}, function( data ) {
  		console.log(data);
		});
	}
</script>
