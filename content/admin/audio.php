<?php
	require_once ("../../include/functions.php");
	require_once ("../../include/Browser.php");

	check_role (array("admin","super"));

	if (isset ($_REQUEST["sentence"])) $sentence = $_REQUEST ["sentence"];
	if (isset($sentence) && strlen($sentence)){
		file_put_contents ($logfile, "audio sentence: $sentence\n", FILE_APPEND);
		readspeak ($sentence);
	}
	
	// print_r ($_REQUEST);
	if (isset ($_REQUEST["action"])) $action = $_REQUEST["action"];
	if (isset ($_REQUEST["id"]))     $id     = $_REQUEST["id"];
	if (isset ($_REQUEST["file"]))   $file   = $_REQUEST["file"];
	if (isset($_REQUEST["action"]) && $action=="delete" && isset($_REQUEST["id"]) && $id){
		del_audio ($id, $file);
	}
	$audios = get_audios ();
	$used_audio_ids = get_used_audio_ids ();
	
	$browser = new Browser();
	if( $browser->getBrowser() == Browser::BROWSER_FIREFOX){
		$player_offset = 1;
	}else{
		$player_offset = 4;
	}

?>
	
audio toevoegen:
	<input type="text"   name="sentence"        id="sentence" size="50">
	<input type="submit" name="sentence_submit" id="sentence_submit" value="&nbsp; zin versturen &nbsp;" onClick="sentenceSubmit()">
	
<hr/>
bestaande audio:
<div id="scrollable" class="scrollable">
	<table>
		<?php foreach ($audios as $audio): ?>
			<?php if(!$audio["instruction"]) $audio["instruction"]="instructietekst"; ?>
			<tr>
				<!--th><?=$audio["name"]?></th-->
				<th><input type="text" name="name<?=$audio["id"]?>" id="name<?=$audio["id"]?>" value="<?=$audio["name"]?>" size="18"></th>
				<td style="position:relative;top:<?=$player_offset?>px;"><audio style="height:26px;" src="/audio/<?=$audio["file"]?>" controls></td>
				<td><input type="text" name="instruction<?=$audio["id"]?>" id="instruction<?=$audio["id"]?>" value="<?=$audio["instruction"]?>" size="50" onFocus="handleInstrTxt(<?=$audio["id"]?>)"></td>
				<td style="position:relative;top:2px;cursor:pointer;">
					<?php if (!in_array($audio["id"],$used_audio_ids)): ?>
						<img src="images/trash.png" alt="verwijder" title="verwijder '<?=$audio["name"]?>'" onClick="audioDelete(<?=$audio["id"]?>, '<?=$audio["name"]?>', '<?=$audio["file"]?>')">
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>
	<?php // print_r($audios); ?>

<script>
	function sentenceSubmit(){
		// alert ("submit");
		$('#content').load('/content/admin/audio.php', {sentence: $("#sentence").val()});
	}

	$("#sentence").keyup(function(event){
		if(event.keyCode == 13){
			// alert ("submit");
			$('#content').load('/content/admin/audio.php', {sentence: $("#sentence").val()});
		}
	});

	function audioDelete(audioId, audioName, audioFile){
		if (confirm("Wilt u zeker '"+audioName+"' verwijderen?")) {
			$('#content').load('/content/admin/audio.php', {action: 'delete', id: audioId, file: audioFile});
    }
		// $('#content').load('/content/admin/audio.php', {sentence: $("#sentence").val()});
	}

	<?php foreach ($audios as $audio): ?>
		$("#instruction<?=$audio["id"]?>").keyup(function(event){
			if(event.keyCode == 13){
			 	// alert ("enter");
			 	instrSubmit(<?=$audio["id"]?>, "<?=$audio["name"]?>");
			}
		});
		$("#instruction<?=$audio["id"]?>").blur(function(){
			// alert ("blur");
			instrSubmit(<?=$audio["id"]?>, "<?=$audio["name"]?>");
		});
		$("#name<?=$audio["id"]?>").keyup(function(event){
			if(event.keyCode == 13){
			 	// alert ("enter");
			 	nameSubmit(<?=$audio["id"]?>);
			}
		});
		$("#name<?=$audio["id"]?>").blur(function(){
			// alert ("blur");
			nameSubmit(<?=$audio["id"]?>);
		});
	<?php endforeach; ?>
</script>
