<?php
	require_once ("../../include/functions.php");
	require_once ("../../include/Browser.php");

	check_role (array("admin","super"));

	// print_r ($_REQUEST);
	if (isset ($_REQUEST["action"])) $action = $_REQUEST["action"]; else $action = "";
	if (isset ($_REQUEST["id"]))     $id     = $_REQUEST["id"];
	if (isset ($_REQUEST["name"]))   $name   = $_REQUEST["name"];
	if ($action=="submit"){
		insert_institute ($name, $id);
	}
	if (isset($_REQUEST["action"]) && $action=="delete" && isset($_REQUEST["id"]) && $id){
		del_institute ($id);
	}
	$institutes = get_institutes ();
?>
	
instituut toevoegen:
	<input type="text"   name="name"        id="name" size="74">
	<input type="submit" name="name_submit" id="name_submit" value="&nbsp; instituut opslaan &nbsp;" onClick="instituteSubmit()">
	
<hr/>
bestaande instituten:
<div id="scrollable" class="scrollable">
	<table>
		<?php foreach ((array)$institutes as $institute): ?>
			<tr>
				<th><input type="text" name="name<?=$institute["id"]?>" id="name<?=$institute["id"]?>" value="<?=$institute["name"]?>" size="109"></th>
				<td style="position:relative;top:2px;cursor:pointer;"><img src="images/trash.png" alt="verwijder" title="verwijder '<?=$institute["name"]?>'" onClick="instituteDelete(<?=$institute["id"]?>, '<?=$institute["name"]?>')"></td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>
	<?php // print_r($audios); ?>

<script>
	function instituteSubmit(){
		// alert ("submit");
		$('#content').load('/content/admin/instituten.php', {action: "submit", name: $("#name").val()});
	}

	function instituteUpdate(id)
	{
		// alert ("instituteUpdate: "+$("#name"+id).val());
		jQuery.getJSON("/api/instituteName.php", {id: id, name: $("#name"+id).val()}, function(result){
			if (result.query=="ok"){
				// alert ("Instituut '"+name+"' opgeslagen.");
			}
		});
	}

	$("#name").keyup(function(event){
		if(event.keyCode == 13){
			// alert ("submit: "+$("#name").val());
			instituteSubmit()
			// $('#content').load('/content/admin/instituten.php', {action: "submit", name: $("#name").val()});
		}
	});

	function instituteDelete(instituteId, instituteName){
		if (confirm("Wilt u zeker '"+instituteName+"' verwijderen?")) {
			$('#content').load('/content/admin/instituten.php', {action: 'delete', id: instituteId});
    }
		// $('#content').load('/content/admin/audio.php', {sentence: $("#sentence").val()});
	}

	<?php foreach ($institutes as $institute): ?>
		$("#name<?=$institute["id"]?>").keyup(function(event){
			if(event.keyCode == 13){
			 	// alert ("enter");
			 	instituteUpdate(<?=$institute["id"]?>);
			}
		});
		$("#name<?=$institute["id"]?>").blur(function(){
			// alert ("blur");
			instituteUpdate(<?=$institute["id"]?>);
		});
	<?php endforeach; ?>
</script>
