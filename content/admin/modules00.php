<?php
	require_once ("../../include/functions.php");
	require_once ("../../include/Browser.php");

	check_role (array("admin","super"));

	if (isset ($_REQUEST["action"])) $action     = $_REQUEST ["action"];
	if (isset ($_REQUEST["id"]))     $id         = $_REQUEST ["id"];
	if (isset ($_REQUEST["name"]))   $name       = $_REQUEST ["name"];
	if (isset ($_REQUEST["audios"])) $mod_audios = trim ($_REQUEST ["audios"]);
	
	if (isset($action) && $action == "submit"){
		// print ("insert_module ($name, $mod_audios)<hr/>\n");
		// print ("submit_module ($id)<hr/>\n");
		insert_module ($name, $mod_audios, $id);
	}
	
	if (isset($action) && $action == "delete" && isset($id) && $id){
		// print ("delete_module ($id)<hr/>\n");
		delete_module ($id);
	}

	$audios  = get_audios ();
	$modules = get_modules ();
	
	$mod_edit = false;
	if (isset($action) && $action=="edit" && isset($id) && $id){
		unset ($mod_audios);
		$mod_info = get_mod_info ($id);
		$mod_audio_ids = 	explode (" ", $mod_info["audio_ids"]);
		$mod_name = $mod_info ["name"];
		// print_r ($mod_info);
		foreach ($mod_audio_ids as $mod_audio_id){
			$mod_audios [$mod_audio_id] = $audios [$mod_audio_id];
			unset ($audios [$mod_audio_id]);
		}
		// print ("<br/>\n'$mod_name'<br/>\n");
		$mod_edit = true;
	}

	// print ("<br/>\n$name<br/>\n");
	$edit_action = $mod_edit ? "aanpassen" : "aanmaken";
?>
<link href="css/modules.css" type="text/css" rel="stylesheet" />
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
		<span style="position:relative; top:15%">Module opslaan als:</span>
		<br/>
		<input class="name_field" id="name_field" type="text" name="module_name" value="<?=$mod_name?>" />
	</div>
	<div class="name_cancel" onClick="saveCancel();">
		<span style="position:relative; top:15%">annuleren</span>
	</div>
	<div class="name_save" onClick="moduleSubmit();">
		<span style="position:relative; top:15%">opslaan</span>
	</div>
	
	<script>
		var sortable1 = document.getElementById("sortable1");
		var sortable2 = document.getElementById("sortable2");
		var hidden1 = true;
		<?php if ($action=="edit" && $id): ?>
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

		function moduleCancel(){
			$('#content').load('/content/admin/modules.php', {action: "list"});
		}
		
		function moduleSave(){
			$(".blur_box").css("visibility", "visible");
			$(".name_box").css("visibility", "visible");
			$(".name_field").css("visibility", "visible");
			$(".name_cancel").css("visibility", "visible");
			<?php if ($mod_name): ?>
				$(".name_save").css("visibility", "visible");
			<?php endif; ?>
			$(".name_field").val('<?=$mod_name?>');
			$(".name_field").focus();
		}
	
		function moduleSubmit(){
			str = getItems ();
			name = $("#name_field").val();
			id = <?=intval($id)?>;
		
			// alert(str+" + "+name);
			
			$('#content').load('/content/admin/modules.php', {audios: str, name: name, id: id, action: "submit"});
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
	    	moduleSubmit();
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
<?php else: ?>
	<input type="submit" value="&nbsp; nieuwe module aanmaken &nbsp;" onClick="editSubmit()">
	<hr/>
	bestaande modules:
	<div id="scrollable" class="scrollable">
		<table>
			<?php foreach ($modules as $module): ?>
				<tr>
					<th style="white-space:nowrap;"><?=$module["name"]?></th>
					<td style="background-color: #fefefe; border: 1px solid #91b7b7; font-size: 12px;">
						<?php 
							unset ($aids);
							$aids = explode (" ", $module["audio_ids"]);
							foreach ($aids as $aid){
								print (" ({$audios[$aid]["name"]})");
							}
						?>
					</td>
					<td style="position:relative;top:3px;cursor:pointer;"><img src="images/editw.png" alt="aanpassen" title="'<?=$module["name"]?>' aanpassen" onClick="moduleEdit(<?=$module["id"]?>, '<?=$module["name"]?>')"></td>
					<td style="position:relative;top:2px;cursor:pointer;"><img src="images/trash.png" alt="verwijder" title="verwijder '<?=$module["name"]?>'" onClick="moduleDelete(<?=$module["id"]?>, '<?=$module["name"]?>')"></td>
				</tr>
			<?php endforeach; ?>
		</table>
	</div>
	<script>
		function editSubmit(){
			// alert ("edit submit");
			$('#content').load('/content/admin/modules.php', {action: "edit"});
		}
		function moduleDelete(moduleId, moduleName){
			if (confirm("Wilt u zeker '"+moduleName+"' verwijderen?")) {
				$('#content').load('/content/admin/modules.php', {action: 'delete', id: moduleId});
	    }
		}
		function moduleEdit(moduleId, moduleName){
			// alert ("Edit "+moduleName);
			$('#content').load('/content/admin/modules.php', {action: 'edit', id: moduleId});
		}
	</script>
<?php endif; ?>