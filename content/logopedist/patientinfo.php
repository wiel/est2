<?php
	require_once ("../../include/functions.php");
	check_role (array("logopedist"));

	if (isset ($_REQUEST["patient_id"]))      $patient_id = $_REQUEST ["patient_id"]; else $patient_id = 0;
	$patient = get_patient ($patient_id);

	if (!($logopedist_id=$_SESSION ["id"])){
		$logopedist_id = $_COOKIE ["id"];
	}
	
	$patientcourses = get_courses ($logopedist_id,  $patient_id);
	$othercourses   = get_courses ($logopedist_id, -$patient_id);
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
	<span class="patientinfotext">
		<?=$patient["user"]?>&nbsp;
		<img src="/images/editw.png" height="15px" alt="" align="middle" 
			onclick="editPatient(<?=$patient["id"]?>);" 
			style="cursor:pointer; margin-top:-11px" 
		/>
	</span>
	<span class="patientinstitutetext">Bekijk gegevens bij <?=$patient["institute_name"]?></span>
</div>
<div class="patient_minmax" id="patient_minmax">
	<span class="patient_minmaxtext">
		Minimale intensiteit: <input type="text" name="i_min" value="40" style="width:20px; height:15px; background-color:#d1e5e4; border:none; padding-left:1px;" /> &nbsp; 
		Maximale toonhoogte:  <input type="text" name="p_max" value="60" style="width:20px; height:15px; background-color:#d1e5e4; border:none; padding-left:1px;" />
	</span>
</div>
<div class="courses_patient" id="courses_patient">
	<span class="courses_patienttext">Cursussen (voor <?=$patient["user"]?>)</span>
	<div class="add_course" id="add_course">
		<span class="add_coursetext" onClick="addCourse(<?=$logopedist_id?>,<?=$patient["id"]?>)">+ cursus toevoegen</span>
	</div>
</div>
<div class="courses_logopedist" id="courses_logopedist">
	<span class="courses_logopedisttext">Cursussen (van andere pati&euml;nten)</span>
</div>
<div class="courseslist_patient" id="courseslist_patient">
	<div class="edit-module-list-sortable1"  id="edit-module-list-sortable1" dir="ltr">
		<ul id="sortable1" class="connectedSortable">
			<?php foreach ($patientcourses as $patientcourse): ?>
				<li class="draggable" id="<?=$patientcourse["id"]?>">
					<?=$patientcourse["name"]?>&nbsp;
					<img src="/images/editw.png" height="15px" alt="" align="middle" id="pic<?=$patientcourse["id"]?>" 
						onclick="editCourse(<?=$logopedist_id?>,<?=$patient["id"]?>,<?=$patientcourse["id"]?>,0);" 
						style="cursor:pointer; margin-top:-11px" 
					/>
					<!--<?=$patientcourse["id"]?>-->
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<div class="courseslist_logopedist" id="courseslist_logopedist">
	<div id="edit-module-list-sortable2">
		<ul id="sortable2" class="connectedSortable">
			<?php foreach ($othercourses as $othercourse): ?>
				<li class="draggable" id="<?=$othercourse["id"]?>">
					<?=$othercourse["name"]?> 
					<?php if($othercourse["user"]) print ("({$othercourse["user"]}) "); ?>
					&nbsp;
					<img src="/images/editw.png" height="15px" alt="" align="middle" id="pic<?=$othercourse["id"]?>" 
						onclick="editCourse(<?=$logopedist_id?>,<?=$patient["id"]?>,<?=$othercourse["id"]?>,1);" 
						style="cursor:pointer; display:none; margin-top:-11px" 
					/> 
					<!--<?=$othercourse["id"]?>-->
					<?php if(!$othercourse["user"] || !$othercourse["u_active"]): ?>
						<img src="/images/trash.png" height="15px" alt="" align="middle" id="trash<?=$othercourse["id"]?>" 
							onclick="delCourse(<?=$logopedist_id?>,<?=$othercourse["id"]?>);" 
							style="cursor:pointer; display:inline; margin-top:-11px" 
						</span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<div class="audio" id="audio">
	<span class="audiotext">Audio bestanden</span>
</div>
<div class="audio_list" id="audio_list">
	<span class="audio_listtext">
		audio 1<br/>
		audio 2<br/>
		audio 3<br/>
	</span>
</div>
<div class="audio_seen" id="audio_seen">
	<span class="audio_seentext">Reeds bekeken</span>
</div>
<div class="audioseen_list" id="audioseen_list">
	<span class="audioseen_listtext">
		audio 21<br/>
	</span>
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
	   	var li_cnt = $("#sortable1 li").length;
	   	// console.log("lcnt " + li_cnt);
		  pad_b = 169 - li_cnt * 23;
		  if (pad_b < 4) pad_b = 4;
		  // console.log("pad_b " + pad_b);
	   	$('#sortable1').css({"padding":"4px 4px "+pad_b+"px 4px"});
	   	// console.log("margin left "+$("#edit-module-list-sortable1").css("margin-left"));
	   	if (parentObject.attr('id') == "sortable1"){
	  		console.log("addtolist " + item.id);
	   		addtolist (item.id);
	   	}else{
	   		console.log("remove " + item.id);
	   		remfromlist (item.id);
	   		$("#pic"+item.id).hide();
	   	}
		}
	});

	new Sortable (sortable1, 
	{
		group: "sortable_edit_module",
		onUpdate: function (evt){
	   	console.log("update");
			str = getItems ();
			if (str.length){
				jQuery.getJSON ("/api/course_order.php", {course_ids: str, cb: Math.random()}, function(result){
				  // alert ("result: "+result.check);
				});
			}
		},
	});
	
	function getItems ()
	{
		div = document.getElementById('sortable1');
		els = div.getElementsByTagName('li');
		var str = "";
		for (var i=0, len=els.length; i<len; i++) {
			str += els[i].id + " ";
		}
	  console.log("<?=$patient_id?> str " + str);
	  return str;
	}

	function addtolist (id)
	{
	  console.log("copy " + id);
		var items = getItems();
		$('.edit-module-list-sortable1').load('/content/logopedist/course_add.php', {
			logopedist_id: <?=$logopedist_id?>,
			patient_id:    <?=$patient_id?>,
			items:         items,
			id:            id
		});
 		// $("#pic"+id).show();
 	}

	function remfromlist (id)
	{
	  console.log("remove " + id);
		$('.edit-module-list-sortable1').load('/content/logopedist/course_rem.php?logopedist_id=<?=$logopedist_id?>&patient_id=<?=$patient_id?>&id='+id);
 		$("#pic"+id).hide();
 	}

	function editPatient (patientId)
	{
		// alert ("Edit patient "+patientId);
		// $('.patient_box').load('/content/logopedist/patient.php?action=edit&patient_id='+patientId);
		$('#content').load('/content/logopedist/patient.php?action=edit&patient_id='+patientId);
	}
	
	function addCourse (logopedistId, patientId)
	{
		// alert ("addCourse logopedistId "+logopedistId+", patientId "+patientId);
		$('.patient_box').load('/content/logopedist/course.php?logopedist_id='+logopedistId+'&patient_id='+patientId);
	}
		
	function editCourse(logopedistId, patientId, id, cp)
	{
		// alert ("edit "+id+", logopedist "+logopedistId+", patient "+patientId);
		// $('.patient_box').load('/content/logopedist/course.php?logopedist_id='+logopedistId+'&patient_id='+patientId+'&id='+id+'&action=edit&cp='+cp);
		$('.patient_box').load('/content/logopedist/course.php', {
			logopedist_id: logopedistId, 
			patient_id:    patientId, 
			id:            id, 
			action:        "edit",
			cp:            cp
		});
	}

	function delCourse (logopedistId, id)
	{
		// alert ("delete "+id+", logopedist "+logopedistId);
		if (confirm("Wilt u zeker deze cursus verwijderen?")) {
			jQuery.getJSON("/api/course_del.php", {course_id: id, cb: Math.random()}, function(result){
			  // alert ("result: "+result.check);
			  if (result.check!="ok"){
					// alert ("login not ok, result: "+result.check+"\nuser: "+user+"\nrole: "+role);
			  }else{
					// alert ("result: "+result.check+"\nuser: "+user+"\nrole: "+role);
				}
			});
			$('#'+id).hide();
		}
	}

	function example_submit_items()
	{
		div = document.getElementById('sortable1');
		els = div.getElementsByTagName('li');
		str = "";
	
		for (var i=0, len=els.length; i<len; i++) {
			str += els[i].id + " ";
		}
		// alert(str);
		document.getElementById("playlist").value = str;
		document.forms["send_playlist"].submit();
	}

</script>