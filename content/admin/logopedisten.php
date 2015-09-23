<?php
	require_once ("../../include/functions.php");
	require_once ("../../include/Browser.php");

	check_role (array("admin","super"));

	// print_r ($_REQUEST);
	if (isset ($_REQUEST["action"]))  $action  = $_REQUEST["action"];
	if (isset ($_REQUEST["id"]))      $id      = $_REQUEST["id"];
	if (isset ($_REQUEST["user"]))    $user    = $_REQUEST["user"];
	if (isset ($_REQUEST["pw"]))      $pw      = $_REQUEST["pw"];
	if (isset ($_REQUEST["inst_id"])) $inst_id = $_REQUEST["inst_id"];
	
	if (isset($action) && $action == "submit"){
		// print ("user:    '$user'   <br/>\n");
		// print ("pw:      '$pw'     <br/>\n");
		// print ("inst_id: '$inst_id'<br/>\n");
		insert_logopedist ($user, $inst_id, $pw);
	}
	// print ("id: '$id'<br/>\n");
	// print ("action: '$action'<br/>\n");
	if (isset($action) && $action=="delete" && isset($id) && $id){
		del_logopedist ($id);
	}

	$institutes  = get_institutes ();
	$logopedists = get_logopedists ();
	
?>
	
logopedist toevoegen:
	<input type="text" name="user" id="user" value="inlognaam" size="20" onkeydown="handleUser($('#user'))" onFocus="handleUser($('#user'))" />
	<input type="text" name="pw"   id="pw"   value="password"  size="20" onFocus="handlePw($('#pw'))"     />
	<select name="inst_id" id="inst_id">
		<?php foreach ($institutes as $institute): ?>
			<option value="<?=$institute["id"]?>"><?=$institute["name"]?></option>
		<?php endforeach; ?>
	</select>
	<input type="submit" name="logopedist_submit" id="logopedist_submit" value="&nbsp; logopedist opslaan &nbsp;" onClick="logopedistSubmit()" />
	
<hr/>
bestaande logopedisten:
<div id="scrollable" class="scrollable">
	<table>
		<?php foreach ((array)$logopedists as $logopedist): ?>
			<tr>
				<td><input type="text" name="user<?=$logopedist["id"]?>" id="user<?=$logopedist["id"]?>" value="<?=$logopedist["user"]?>" size="39"></td>
				<td><input type="text" name="pw<?=$logopedist["id"]  ?>" id="pw<?=$logopedist["id"]  ?>" value="password" onFocus="handlePw($('#pw<?=$logopedist["id"]?>'))" size="40"></td>
				<td>
					<select name="inst_id<?=$logopedist["id"]?>" id="inst_id<?=$logopedist["id"]?>">
						<?php foreach ($institutes as $institute): ?>
							<option value="<?=$institute["id"]?>" <?php if($institute["id"]==$logopedist["institute_id"]){echo "selected=\"selected\"";}?>><?=$institute["name"]?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td style="position:relative;top:2px;cursor:pointer;"><img src="images/trash.png" alt="verwijder" title="verwijder '<?=$logopedist["user"]?>'" onClick="logopedistDelete(<?=$logopedist["id"]?>, '<?=$logopedist["user"]?>')"></td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>
	<?php // print_r($audios); ?>

<script>
	function logopedistSubmit(){
		// alert ("submit");
		$('#content').load('/content/admin/logopedisten.php', {action: "submit", user: $("#user").val(), pw: $("#pw").val(), inst_id: $("#inst_id").val()});
	}

	function logopedistUserUpdate(id)
	{
		jQuery.getJSON("/api/UserUpdate.php", {id: id, user: $("#user"+id).val()}, function(result){
			if (result.query=="ok"){
			}
		});
	}

	function logopedistPwUpdate(id)
	{
		if ($("#pw"+id).val() != "password"){
			jQuery.getJSON("/api/UserUpdate.php", {id: id, pw: $("#pw"+id).val()}, function(result){
				if (result.query=="ok"){
				}
			});
		}
	}

	function logopedistInstUpdate(id)
	{
		jQuery.getJSON("/api/UserUpdate.php", {id: id, inst_id: $("#inst_id"+id).val()}, function(result){
			if (result.query=="ok"){
			}
		});
	}

	function logopedistDelete(logopedistId, logopedistName){
		if (confirm("Wilt u zeker '"+logopedistName+"' verwijderen?")) {
			$('#content').load('/content/admin/logopedisten.php', {action: 'delete', id: logopedistId});
    }
		// $('#content').load('/content/admin/audio.php', {sentence: $("#sentence").val()});
	}

	<?php foreach ($logopedists as $logopedist): ?>
		$("#user<?=$logopedist["id"]?>").keyup(function(event){
			if(event.keyCode == 13){
			 	// alert ("enter");
			 	logopedistUserUpdate(<?=$logopedist["id"]?>);
			}
		});
		$("#user<?=$logopedist["id"]?>").blur(function(){
			// alert ("blur");
			logopedistUserUpdate(<?=$logopedist["id"]?>);
		});
		$("#pw<?=$logopedist["id"]?>").keyup(function(event){
			if(event.keyCode == 13){
			 		logopedistPwUpdate(<?=$logopedist["id"]?>);
			}
		});
		$("#pw<?=$logopedist["id"]?>").blur(function(){
				logopedistPwUpdate(<?=$logopedist["id"]?>);
		});
		$("#inst_id<?=$logopedist["id"]?>").change(function(){
			// alert ("inst change -> "+$("#inst_id"+<?=$logopedist["id"]?>).val());
			logopedistInstUpdate(<?=$logopedist["id"]?>);
		});
	<?php endforeach; ?>
</script>
