<?php
	require_once ("../../include/functions.php");
	require_once ("../../include/Browser.php");

	check_role (array("patient"));

	if (isset ($_REQUEST["course"])) $active_course_id = $_REQUEST ["course"]; else $active_course_id = 0;
	if (isset ($_REQUEST["action"])) $action           = $_REQUEST ["action"];
	if (isset ($_REQUEST["id"]))     $id               = $_REQUEST ["id"];
	
	$patient_id = $_COOKIE ["id"];
	$courses    = get_courses (0, $patient_id, 0);
	$modules    = get_modules ();
	$audios     = get_audios ();
	if ($active_course_id){
		$module_ids = explode (" ", $courses[$active_course_id]["module_ids"]);
	}
?>
<link href="/css/oefeningen.css" type="text/css" rel="stylesheet" />
<div class="leftcolumn">
	<div class="leftcolumnhead">
		<b>Cursussen:</b>
	</div>
	<?php foreach ($courses as $course_id => $course): ?>
		<?php if ($course_id==$active_course_id): ?>
			<div class="course_item_active">
		<?php else: ?>
			<div class="course_item" onClick="courseSubmit(<?=$course["id"]?>);">
		<?php endif; ?>
			<div style="position:relative;top:7px;"><?=$course["name"]?></div>
		</div>
		<div style="height:6px;"></div>
	<?php endforeach; ?>
</div>
	
<div class="right3columns scroll_right">
	<?php if ($active_course_id): ?>
		<?php foreach ($module_ids as $module_id): ?>
			<?php $audio_ids[$module_id] = explode (" ", $modules[$module_id]["audio_ids"]); ?>
			<div class="course_head"><?=$modules[$module_id]["name"]?></div>
			<?php foreach ($audio_ids[$module_id] as $audio_id): ?>
				<div class="audio_item" id="audio_item_<?=$module_id?>_<?=$audio_id?>" onClick="audioSubmit(<?=$module_id?>,<?=$audio_id?>);"><?=$audios[$audio_id]["name"]?></div>
			<?php endforeach; ?>
			<div style="height:8px"></div>
		<?php endforeach; ?>
	<?php else: ?>
		&nbsp;Kies links een cursus. 
	<?php endif; ?>
</div>
	
<div class="audio_popup">
</div>
	
<script>
	function courseSubmit(course){
		// alert("course: "+course);
		$('#content').load('/content/patient/oefeningen.php', {course: course});
	}

	function audioSubmit(module_id, audio_id){
		// alert("audio_id: "+audio_id);
		$(".audio_popup").css("visibility", "visible");
		$(".audio_popup").load('/content/patient/audio.php', {module_id: module_id, audio_id: audio_id});
	}
</script>
