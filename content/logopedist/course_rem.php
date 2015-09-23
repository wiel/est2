<?php
	require_once ("../../include/functions.php");
	check_role (array("logopedist"));

	if (isset ($_REQUEST["patient_id"])) $patient_id = $_REQUEST ["patient_id"]; else $patient_id = 0;
	if (isset ($_REQUEST["id"]))         $id         = $_REQUEST ["id"];         else $id = 0;
	if (!($logopedist_id=$_SESSION ["id"])){
		$logopedist_id = $_COOKIE ["id"];
	}
	remove_course ($id);
	
	$patientcourses = get_courses ($logopedist_id,  $patient_id);
	// print ("copy_course ($logopedist_id, $patient_id, $id)<br/>");
?>
		<ul id="sortable1" class="connectedSortable">
			<?php foreach ($patientcourses as $patientcourse): ?>
				<li class="draggable" id="<?=$patientcourse["id"]?>"><?=$patientcourse["name"]?>&nbsp;<img src="/images/editw.png" height="15px" alt="" align="middle" id="pic<?=$patientcourse["id"]?>" onclick="editCourse(<?=$logopedist_id?>,<?=$patient_id?>,<?=$patientcourse["id"]?>,1);" style="cursor:pointer; margin-top:-11px" /></li>
			<?php endforeach; ?>
		</ul>
<script>
	var sortable1 = document.getElementById("sortable1");
	var sortable2 = document.getElementById("sortable2");
	Sortable.create(sortable1, { group: "sortable_edit_module" });
</script>
