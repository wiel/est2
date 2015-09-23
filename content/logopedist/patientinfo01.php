<?php
	require_once ("../../include/functions.php");
	check_role (array("logopedist"));

	if (isset ($_REQUEST["patient_id"]))      $patient_id = $_REQUEST ["patient_id"]; else $patient_id = 0;
	$patient = get_patient ($patient_id);
?>

<!--
patientinfo <?=$patient_id?>:
<pre>
<?php print_r ($patient); ?>
</pre>
-->

<link href="css/patientinfo.css" type="text/css" rel="stylesheet" />
<script src="/js/Sortable.js"></script>

<div class="patientinfo" id="patientinfo">
	<span class="patientinfotext"><?=$patient["user"]?></span>
	<span class="patientinstitutetext">bekijk gegevens bij <?=$patient["institute_name"]?></span>
</div>
<div class="courses_patient" id="courses_patient">
	<span class="courses_patienttext">cursussen (voor <?=$patient["user"]?>)</span>
</div>
<div class="courses_logopedist" id="courses_logopedist">
	<span class="courses_logopedisttext">cursussen (alle)</span>
</div>
<div class="courseslist_patient" id="courseslist_patient">
	<div id="edit-module-list-sortable1">
		<ul id="sortable1" class="connectedSortable">
		</ul>
	</div>
</div>
<div class="courseslist_logopedist" id="courseslist_logopedist">
	<div id="edit-module-list-sortable2">
		<ul id="sortable2" class="connectedSortable">
			<li class="draggable" id="c01">cursus  1 <img src="/images/editw.png" height="15px" alt="" align="middle" id="picc01" onclick="editCourse('c01');" style="cursor:pointer; display:none; margin-top:-10px" /></li>
			<li class="draggable" id="c02">cursus  2 <img src="/images/editw.png" height="15px" alt="" id="picc02" onclick="editCourse('c02');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="c03">cursus  3 <img src="/images/editw.png" height="15px" alt="" id="picc03" onclick="editCourse('c03');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="c04">cursus  4 <img src="/images/editw.png" height="15px" alt="" id="picc04" onclick="editCourse('c04');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="c05">cursus  5 <img src="/images/editw.png" height="15px" alt="" id="picc05" onclick="editCourse('c05');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="c06">cursus  6 <img src="/images/editw.png" height="15px" alt="" id="picc06" onclick="editCourse('c06');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="c07">cursus  7 <img src="/images/editw.png" height="15px" alt="" id="picc07" onclick="editCourse('c07');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="c08">cursus  8 <img src="/images/editw.png" height="15px" alt="" id="picc08" onclick="editCourse('c08');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="c09">cursus  9 <img src="/images/editw.png" height="15px" alt="" id="picc09" onclick="editCourse('c09');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d10">cursus 10 <img src="/images/editw.png" height="15px" alt="" id="picd10" onclick="editCourse('d10');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d11">cursus 11 <img src="/images/editw.png" height="15px" alt="" id="picd11" onclick="editCourse('d11');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d12">cursus 12 <img src="/images/editw.png" height="15px" alt="" id="picd12" onclick="editCourse('d12');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d13">cursus 13 <img src="/images/editw.png" height="15px" alt="" id="picd13" onclick="editCourse('d13');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d14">cursus 14 <img src="/images/editw.png" height="15px" alt="" id="picd14" onclick="editCourse('d14');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d15">cursus 15 <img src="/images/editw.png" height="15px" alt="" id="picd15" onclick="editCourse('d15');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d16">cursus 16 <img src="/images/editw.png" height="15px" alt="" id="picd16" onclick="editCourse('d16');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d17">cursus 17 <img src="/images/editw.png" height="15px" alt="" id="picd17" onclick="editCourse('d17');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d18">cursus 18 <img src="/images/editw.png" height="15px" alt="" id="picd18" onclick="editCourse('d18');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d19">cursus 19 <img src="/images/editw.png" height="15px" alt="" id="picd19" onclick="editCourse('d19');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d20">cursus 20 <img src="/images/editw.png" height="15px" alt="" id="picd20" onclick="editCourse('d20');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d21">cursus 21 <img src="/images/editw.png" height="15px" alt="" id="picd21" onclick="editCourse('d21');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d22">cursus 22 <img src="/images/editw.png" height="15px" alt="" id="picd22" onclick="editCourse('d22');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d23">cursus 23 <img src="/images/editw.png" height="15px" alt="" id="picd23" onclick="editCourse('d23');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d24">cursus 24 <img src="/images/editw.png" height="15px" alt="" id="picd24" onclick="editCourse('d24');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d25">cursus 25 <img src="/images/editw.png" height="15px" alt="" id="picd25" onclick="editCourse('d25');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d26">cursus 26 <img src="/images/editw.png" height="15px" alt="" id="picd26" onclick="editCourse('d26');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d27">cursus 27 <img src="/images/editw.png" height="15px" alt="" id="picd27" onclick="editCourse('d27');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d28">cursus 28 <img src="/images/editw.png" height="15px" alt="" id="picd28" onclick="editCourse('d28');" style="cursor:pointer; display:none;" /></li>
			<li class="draggable" id="d29">cursus 29 <img src="/images/editw.png" height="15px" alt="" id="picd29" onclick="editCourse('d29');" style="cursor:pointer; display:none;" /></li>
		</ul>
	</div>
</div>
<div class="audio" id="audio">
	<span class="audiotext">Audio bestanden</span>
</div>
<div class="audio_seen" id="audio_seen">
	<span class="audio_seentext">Reeds bekeken</span>
</div>


<script>
	var sortable1 = document.getElementById("sortable1");
	var sortable2 = document.getElementById("sortable2");
	Sortable.create(sortable1, { group: "sortable_edit_module" });
	new Sortable (sortable2, 
	{
		group: "sortable_edit_module",
		onSort: function (evt){
	   	var item = evt.item;
	   	var parentObject = $("#"+item.id).parent();
	   	// console.log("sort " + parentObject.attr('id'));
	   	if (parentObject.attr('id') == "sortable1"){
	   		$("#pic"+item.id).show();
	   	}else{
	   		$("#pic"+item.id).hide();
	   	}
			// str = getItems ();
			str = "12";
			if (str.length){
	   		// $(".opslaan").css("visibility", "visible");
	   	}else{
	   		// $(".opslaan").fadeOut("slow");
	   	}
		},
	});
	
	function editCourse(id)
	{
		alert ("edit "+id);
	}
</script>