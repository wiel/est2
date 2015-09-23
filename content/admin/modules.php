<?php
	require_once ("../../include/functions.php");
	require_once ("../../include/Browser.php");

	$max_audio_str_ln = 60;

	check_role (array("admin","super"));

	if (isset ($_REQUEST["goal"]))        $active_goal_id = $_REQUEST ["goal"]; else $active_goal_id = 0;
	if (isset ($_REQUEST["action"]))      $action         = $_REQUEST ["action"];
	if (isset ($_REQUEST["id"]))          $id             = $_REQUEST ["id"];
	if (isset ($_REQUEST["name"]))        $name           = $_REQUEST ["name"];
	if (isset ($_REQUEST["purpose"]))     $purpose        = $_REQUEST ["purpose"];
	if (isset ($_REQUEST["instruction"])) $instruction    = $_REQUEST ["instruction"];
	if (isset ($_REQUEST["remark"]))      $remark         = $_REQUEST ["remark"];
	if (isset ($_REQUEST["reference"]))   $reference      = $_REQUEST ["reference"];
	if (isset ($_REQUEST["audios"]))      $mod_audios = trim ($_REQUEST ["audios"]);
	// print ("active_goal_id: $active_goal_id<hr/>\n");
	
	// $active_goal_id = 2;
	if (isset($action) && $action == "submit"){
		// print ("insert_module ($name, $mod_audios)<hr/>\n");
		// print ("submit_module ($id)<hr/>\n");
		insert_module ($name, $mod_audios, $id, $active_goal_id, $purpose, $instruction, $remark, $reference);
	}
	
	if (isset($action) && $action == "delete" && isset($id) && $id){
		// print ("delete_module ($id)<hr/>\n");
		delete_module ($id);
	}

	$audios          = get_audios ();
	$modules         = get_modules ($active_goal_id);
	$goals           = get_goals ();
	$used_module_ids = get_used_module_ids ();
	
	$mod_edit = false;
	if (isset($action) && $action=="edit" && isset($id) && $id){
		unset ($mod_audios);
		$mod_info = get_mod_info ($id);
		$mod_audio_ids = 	explode (" ", $mod_info["audio_ids"]);
		$mod_name        = $mod_info ["name"];
		$mod_purpose     = $mod_info ["purpose"];
		$mod_instruction = $mod_info ["instruction"];
		$mod_remark      = $mod_info ["remark"];
		$mod_reference   = $mod_info ["reference"];
		// print_r ($mod_info);
		foreach ($mod_audio_ids as $mod_audio_id){
			$mod_audios [$mod_audio_id] = $audios [$mod_audio_id];
			unset ($audios [$mod_audio_id]);
		}
		// print ("<br/>\n'$mod_name'<br/>\n");
		$mod_edit = true;
	}else{
		$mod_name        = "";
		$mod_purpose     = "";
		$mod_instruction = "";
		$mod_remark      = "";
		$mod_reference   = "";
	}

	// print ("<br/>\n$name<br/>\n");
	$edit_action = $mod_edit ? "aanpassen" : "aanmaken";
?>
<link href="/css/modules.css" type="text/css" rel="stylesheet" />
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
	<div class="leftcolumn">
		<div class="leftcolumnhead">
			doelen:
		</div>
		<?php foreach ($goals as $goal_id => $goal): ?>
			<?php if ($goal_id==$active_goal_id): ?>
				<div class="goal_item_active">
			<?php else: ?>
				<div class="goal_item" onClick="goalSubmit(<?=$goal["id"]?>);">
			<?php endif; ?>
				<div style="position:relative;top:7px;"><?=$goal["name"]?></div>
			</div>
			<div style="height:6px;"></div>
		<?php endforeach; ?>
	</div>
	<script>
		function goalSubmit(goal){
			// alert("goal: "+goal);
			$('#content').load('/content/admin/modules.php', {goal: goal});
		}
	</script>
		
<?php if (isset($action) && $action=="edit"): ?>
	<script src="js/Sortable.js"></script>
	<div class="lists" id="lists">
		<div class="lists_title" id="lists_title">
			<span style="position:relative; top:30%">Module <?=$edit_action?></span>
		</div>
		<table class="lists_table" id="lists_table">
			<tr>
				<td style="width:50%; background-color:#689995; border:none;">
					<div id="edit-module-list-sortable1">
						<ul id="sortable1" class="connectedSortable">
							<?php foreach ($audios as $audio): ?>
								<li class="draggable" id="<?=$audio["id"]?>">
									<b><?=$audio["name"]?></b>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</td>
				<td style="width:50%; background-color:#689995;border:none;">
					<div id="edit-module-list-sortable2">
						<ul id="sortable2" class="connectedSortable" style="padding-bottom: 393px;">
							<?php if ($mod_edit): ?>
								<?php foreach ($mod_audios as $mod_audio): ?>
									<li class="draggable" id="<?=$mod_audio["id"]?>">
										<b><?=$mod_audio["name"]?></b>
									</li>
								<?php endforeach; ?>
							<?php endif; ?>
						</ul>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="height:40px;background-color:#689995;border:none;">
					<div class="annuleer" onClick="moduleCancel(<?=$active_goal_id?>);">
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
		<input class="name_field" id="name_field" type="text" name="module_name" value="<?=$mod_name?>" />
		<br/>
		<span style="position:relative; left:11px; top:50px">Doel van de oefening:</span>
		<textarea class="purpose_field" id="purpose_field"><?=$mod_purpose?></textarea>
		<br/>
		<span style="position:relative; left:11px; top:118px">Instructie:</span>
		<textarea class="instruction_field" id="instruction_field"><?=$mod_instruction?></textarea>
		<br/>
		<span style="position:relative; left:11px; top:186px">Opmerkingen:</span>
		<textarea class="remark_field" id="remark_field"><?=$mod_remark?></textarea>
		<br/>
		<span style="position:relative; left:11px; top:254px">Verwijzing:</span>
		<textarea class="reference_field" id="reference_field"><?=$mod_reference?></textarea>
	</div>
	<div class="name_cancel" onClick="saveCancel();">
		<span style="position:relative; top:15%">annuleren</span>
	</div>
	<div class="name_save" onClick="moduleSubmit(<?=$active_goal_id?>);">
		<span style="position:relative; top:15%">opslaan</span>
	</div>
	
	<script>
		$(".leftcolumnhead").css("visibility", "hidden");
		$(".goal_item").css("visibility", "hidden");
		// $(".leftcolumnhead").fadeOut("slow");
		// $(".goal_item").fadeOut("slow");

		var sortable1 = document.getElementById("sortable1");
		var sortable2 = document.getElementById("sortable2");
		var hidden1 = true;
		<?php if ($action=="edit" && isset($id) && $id): ?>
			var hidden2 = false;
		<?php else: ?>
			var hidden2 = true;
		<?php endif; ?>
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

		function moduleCancel(goalId){
			$('#content').load('/content/admin/modules.php', {action: "list", goal: goalId});
			// $(".goal_item").css("visibility", "visible");
			$(".leftcolumnhead").css('visibility','visible').hide().fadeIn();
			$('.goal_item').css('visibility','visible').hide().fadeIn();
		}
		
		function moduleSave(){
			$(".blur_box").css("visibility", "visible");
			$(".name_box").css("visibility", "visible");
			$(".name_field").css("visibility", "visible");
			$(".purpose_field").css("visibility", "visible");
			$(".instruction_field").css("visibility", "visible");
			$(".remark_field").css("visibility", "visible");
			$(".reference_field").css("visibility", "visible");
			$(".name_cancel").css("visibility", "visible");
			<?php if ($mod_name): ?>
				$(".name_save").css("visibility", "visible");
			<?php endif; ?>
			$(".name_field").val('<?=$mod_name?>');
			$(".name_field").focus();
		}
	
		function moduleSubmit(goalId){
			str = getItems ();
			name        = $("#name_field").val();
			purpose     = $("#purpose_field").val();
			instruction = $("#instruction_field").val();
			remark      = $("#remark_field").val();
			reference   = $("#reference_field").val();
			id = <?=isset($id)?intval($id):0?>;
			// alert(str+" + "+name);
			$('#content').load('/content/admin/modules.php', {audios: str, name: name, purpose: purpose, instruction: instruction, remark: remark, reference: reference, id: id, action: "submit", goal: goalId});
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
			// alert ("typed: "+value);
			if (str.length){
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
	    	moduleSubmit(<?=$active_goal_id?>);
	    }
		});
	
		function saveCancel(){
			$(".name_save").css("visibility", "hidden");
			$(".name_cancel").css("visibility", "hidden");
			$(".name_field").css("visibility", "hidden");
			$(".purpose_field").css("visibility", "hidden");
			$(".instruction_field").css("visibility", "hidden");
			$(".remark_field").css("visibility", "hidden");
			$(".reference_field").css("visibility", "hidden");
			$(".name_box").css("visibility", "hidden");
			$(".blur_box").css("visibility", "hidden");
		}
		
	</script>
<?php elseif ($active_goal_id): ?>
	<div class="right3columns">
		<input type="submit" value="nieuwe module aanmaken in '<?=$goals[$active_goal_id]["name"]?>'" onClick="editSubmit(<?=$active_goal_id?>)">
		<br/>
		<div style="height:12px;"></div>
		<?php if (count($modules)): ?>
			&nbsp;bestaande modules in '<?=$goals[$active_goal_id]["name"]?>':
			<div style="height:4px;"></div>
			<div id="scrollable" class="scrollable">
				<table>
					<?php foreach ($modules as $module): ?>
						<tr>
							<th style="background-color: #91aba8; border: 1px solid #d1e5e4; width:630px; vertical-align:middle;">&nbsp;<?=$module["name"]?>&nbsp;</th>
							<!--td style="background-color: #91aba8; border: 1px solid #d1e5e4; font-size:12px; width:188px;">
								<?php 
									unset ($aids);
									$aids = explode (" ", $module["audio_ids"]);
									$audio_string = "";
									foreach ($aids as $aid){
										$audio_string .= " ({$audios[$aid]["name"]})";
										// print (" ({$audios[$aid]["name"]})");
									}
									if (strlen($audio_string) > $max_audio_str_ln){
										$audio_string = substr ($audio_string,0, $max_audio_str_ln-3) . "...";
									}
									print (trim($audio_string));
								?>
							</td-->
							<td style="background-color: #91aba8; border: 1px solid #d1e5e4; cursor:pointer;"><img src="images/editw.png" style="padding-top:4px;" alt="aanpassen" title="'<?=$module["name"]?>' aanpassen" onClick="moduleEdit(<?=$module["id"]?>, '<?=$module["name"]?>', <?=$module["goal_id"]?>)"></td>
							<?php if (!in_array($module["id"],$used_module_ids)): ?>
								<td style="background-color: #91aba8; border: 1px solid #d1e5e4; cursor:pointer;"><img src="images/trash.png" style="padding-top:3px;" alt="verwijder" title="verwijder '<?=$module["name"]?>'" onClick="moduleDelete(<?=$module["id"]?>, '<?=$module["name"]?>')"></td>
							<?php endif; ?>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
		<?php else: ?>
			&nbsp;nog geen modules in <?=$goals[$active_goal_id]["name"]?>.
		<?php endif; ?>
	</div>
	<script>
		function editSubmit(goalId){
			// alert ("edit submit");
			$('#content').load('/content/admin/modules.php', {action: "edit", goal: goalId});
		}
		function moduleDelete(moduleId, moduleName){
			if (confirm("Wilt u zeker '"+moduleName+"' verwijderen?")) {
				$('#content').load('/content/admin/modules.php', {action: 'delete', id: moduleId});
	    }
		}
		function moduleEdit(moduleId, moduleName, goalId){
			// alert ("Edit "+moduleName);
			$('#content').load('/content/admin/modules.php', {action: 'edit', id: moduleId, goal: goalId});
		}
	</script>
<?php endif; ?>