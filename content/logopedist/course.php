<?php
	require_once ("../../include/functions.php");
	require_once ("../../include/Browser.php");

	check_role (array("logopedist"));

	if (isset ($_REQUEST["logopedist_id"])) $logopedist_id = $_REQUEST ["logopedist_id"];
	if (isset ($_REQUEST["patient_id"]))    $patient_id    = $_REQUEST ["patient_id"];
	// if (isset ($_REQUEST["origin_id"]))     $origin_id     = $_REQUEST ["origin_id"];       else $origin_id = 0;
	if (isset ($_REQUEST["modules"]))       $course_modules = trim ($_REQUEST ["modules"]);
	if (isset ($_REQUEST["name"]))          $name          = $_REQUEST ["name"];
	if (isset ($_REQUEST["action"]))        $action        = $_REQUEST ["action"];          else $action = "";
	if (isset ($_REQUEST["id"]))            $id            = $_REQUEST ["id"];              else $id = 0;
	if (isset ($_REQUEST["cp"]))            $cp            = $_REQUEST ["cp"];              else $cp = 0;
	
	$course_inserted = false;
	if (isset($action) && $action == "submit"){
		// print ("insert_module ($name, $course_modules)<hr/>\n");
		// print ("submit_module ($id)<hr/>\n");
		insert_course ($name, $course_modules, $logopedist_id, $patient_id, $id);
		$course_inserted = true;
	}

	$logopedist_info = get_logopedist ($logopedist_id);
	$patient_info    = get_patient    ($patient_id);
	$modules         = get_modules ();
	$goals           = get_goals ();
	/*
	if ($origin_id){
		$origin_info   = get_course_info ($origin_id);
		$course_module_ids = explode (" ", $origin_info["module_ids"]);
		foreach ($modules as $module){
			if (in_array ($module["id"], $course_module_ids){
			$course_modules [$module["id"]] = $module;
			unset ($modules[$module["id"]];
		}
	}else{
		$origin_info   = array();
		$course_modules_ids = array();
	}
	*/

	$course_edit = false;
	if (isset($action) && $action=="edit" && isset($id) && $id){
		unset ($course_modules);
		$course_info = get_course_info ($id);
		$course_module_ids = 	explode (" ", $course_info["module_ids"]);
		$course_name       = $course_info ["name"];
		// print_r ($course_info);
		foreach ($course_module_ids as $course_module_id){
			$course_modules [$course_module_id] = $modules [$course_module_id];
			unset ($modules [$course_module_id]);
		}
		// print ("<br/>\n'$course_name'<br/>\n");
		$course_edit = true;
		$save_title = "Cursus aanpassen voor '{$patient_info["user"]}'";
	}else{
		$course_name        = "";
		$save_title = "Nieuwe cursus aanmaken voor '{$patient_info["user"]}'";
	}
?>
<link href="/css/course.css" type="text/css" rel="stylesheet" />
<style>
	::-webkit-scrollbar {
		/* width:         24px; */
		background:       #f8fcfd;
	}
	
	::-webkit-scrollbar-thumb {
		background:       #004d43;
		-webkit-border-radius: .8ex;
		/* -webkit-box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.75); */
	}
</style>
	<script src="js/Sortable.js"></script>
	<div class="legenda" id="legenda">
		<table>
			<?php foreach ($goals as $goal): ?>
				<tr><td><?=substr($goal["name"],0,2)?></td><td style="color:#004d43;"><?=$goal["name"]?></td></tr>
			<?php endforeach; ?>
		</table>
	</div>
	<div class="lists" id="lists">
		<div class="lists_title" id="lists_title">
			<span style="position:relative; top:30%"><?=$save_title?></span>
		</div>
		<table class="lists_table" id="lists_table">
			<tr>
				<td style="width:50%; background-color:#689995; border:none;">
					<div id="edit-module-list-sortable1">
						<ul id="sortable1" class="connectedSortable" style="padding-bottom: 443px;">
							<?php foreach ($modules as $module): ?>
								<li class="draggable" id="<?=$module["id"]?>">
									<table>
										<tr>
											<td style="font-size:12px; width:15px;"><span style="position:relative; top:2px;"><?=substr($module["goal_name"],0,2)?></span></td>
											<td><b><?=$module["name"]?></b></td>
										</tr>
									</table>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</td>
				<td style="width:50%; background-color:#689995;border:none;">
					<div id="edit-module-list-sortable2">
						<ul id="sortable2" class="connectedSortable" style="padding-bottom: 443px;">
							<?php if ($course_edit): ?>
								<?php foreach ($course_modules as $course_module): ?>
									<li class="draggable" id="<?=$course_module["id"]?>">
										<table>
											<tr>
												<td style="font-size:12px; width:15px;"><span style="position:relative; top:2px;"><?=substr($course_module["goal_name"],0,2)?></span></td>
												<td><b><?=$course_module["name"]?></b></td>
											</tr>
										</table>
									</li>
								<?php endforeach; ?>
							<?php endif; ?>
						</ul>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="height:40px; background-color:#689995; border:none;">
					<div class="annuleer" onClick="moduleCancel();">
						<span style="position:relative; top:10%">annuleren</span>
					</div>
					<div class="opslaan" onClick="moduleSave();">
						<span style="position:relative; top:10%">opslaan</span>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<div class="blur_box" id="blur_box">
	</div>
	<div class="name_box">
		<span style="position:relative; left:11px; top:10px">Module opslaan als:</span>
		<input class="name_field" id="name_field" type="text" name="course_name" value="<?=$course_name?>" />
		<br/>
	</div>
	<div class="name_cancel" onClick="saveCancel();">
		<span style="position:relative; top:15%">annuleren</span>
	</div>
	<div class="name_save" onClick="courseSubmit();">
		<span style="position:relative; top:15%">opslaan</span>
	</div>
	
	<script>
		// $(".leftcolumn").css("visibility", "hidden");
		// $(".goal_item").css("visibility", "hidden");
		$(".leftcolumn").fadeOut("slow");
		// $(".goal_item").fadeOut("slow");
		
		<?php if ($cp): ?>
			$('.opslaan').css('visibility','visible').hide().fadeIn();
			var hidden1 = false;
		<?php else: ?>
			var hidden1 = true;
		<?php endif; ?>

		var sortable1 = document.getElementById("sortable1");
		var sortable2 = document.getElementById("sortable2");
		var hidden1 = true;
		<?php if ($action=="edit" && isset($id) && $id): ?>
			var hidden2 = false;
		<?php else: ?>
			var hidden2 = true;
		<?php endif; ?>
		// console.log("hidden2 " + hidden2);
		
		Sortable.create(sortable1, { group: "sortable_edit_module" });
		new Sortable (sortable2, 
		{
			group: "sortable_edit_module",
			onSort: function (evt){
	    	var item = evt.item;
				str = getItems ();
				if (str.length){
	    		// $(".opslaan").css("visibility", "visible");
	    		if (hidden1){
	    			$('.opslaan').css('visibility','visible').hide().fadeIn();
		    		// $(".opslaan").fadeIn("slow");
	    			hidden1 = false;
	    		}
	    	}else{
	    		// $(".opslaan").css("visibility", "hidden");
	    		// $('.opslaan').css('visibility','hidden').show().fadeOut();
	    		// $(".opslaan").fadeOut("slow").show().css('visibility','hidden');
	    		$(".opslaan").fadeOut("slow");
					hidden1 = true;
	    	}
			},
		});

		function moduleCancel(){
			$(".leftcolumn").css('visibility','visible').hide().fadeIn();
			$('.patient_box').load('/content/logopedist/patientinfo.php', {patient_id: "<?=$patient_id?>"});
			// $(".goal_item").css("visibility", "visible");
			// $('.goal_item').css('visibility','visible').hide().fadeIn();
		}
		
		function moduleSave(){
			$(".blur_box").css("visibility", "visible");
			$(".name_box").css("visibility", "visible");
			$(".name_field").css("visibility", "visible");
			// $(".purpose_field").css("visibility", "visible");
			// $(".instruction_field").css("visibility", "visible");
			// $(".remark_field").css("visibility", "visible");
			// $(".reference_field").css("visibility", "visible");
			$(".name_cancel").css("visibility", "visible");
			<?php if ($course_name): ?>
				$(".name_save").css("visibility", "visible");
			<?php endif; ?>
			$(".name_field").val('<?=$course_name?>');
			$(".name_field").focus();
		}
	
		function courseSubmit(){
			str = getItems ();
			//alert("str: "+str);
			name        = $("#name_field").val();
			//alert(str+" + "+name);
			id = <?=isset($id) ? intval($id) : 0?>;
			//alert(str+" + "+name+" + "+id);
			$('#content').load('/content/logopedist/patienten.php', {modules: str, name: name, id: id, action: "submit", patient_id: <?=$patient_id?>, logopedist_id: <?=$logopedist_id?>});
		}
		
		function getItems (){
			div = document.getElementById('sortable2');
			els = div.getElementsByTagName('li');
			str = "";
		
			for (var i=0, len=els.length; i<len; i++) {
				str += els[i].id + " ";
			}
			return str;
		}
		
		$("#name_field").keyup(function(event){
			str = $("#name_field").val();
			// alert ("typed: "+str);
			if (str.length){
				// console.log("hidden2 " + hidden2);
	   		// $(".name_save").css("visibility", "visible");
	    	if (hidden2){
	    		$('.name_save').css('visibility','visible').hide().fadeIn();
		    	// $(".name_save").fadeIn("slow");
	    		hidden2 = false;
	    	}
	    }else{
	    	// $(".name_save").css("visibility", "hidden");
	    	// $('.name_save').css('visibility','hidden').show().fadeOut();
	    	$(".name_save").fadeOut("slow");
				hidden2 = true;
	   	}
			if(event.keyCode == 13){
	    	// alert ("enter");
	    	courseSubmit();
	    }
		});
	
		function saveCancel(){
			$(".name_save").css("visibility", "hidden");
			$(".name_cancel").css("visibility", "hidden");
			$(".name_field").css("visibility", "hidden");
			$(".name_box").css("visibility", "hidden");
			$(".blur_box").css("visibility", "hidden");
		}
		
	</script>
