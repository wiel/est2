<?php
	require_once ("../../include/functions.php");
	require_once ("../../include/Browser.php");

	check_role (array("logopedist"));

	if (isset ($_REQUEST["name"]))           $name           = $_REQUEST ["name"];           else $name = "";
	if (isset ($_REQUEST["pw"]))             $pw             = $_REQUEST ["pw"];
	if (isset ($_REQUEST["institute_id"]))   $institute_id   = $_REQUEST ["institute_id"];
	if (isset ($_REQUEST["patient_instid"])) $patient_instid = $_REQUEST ["patient_instid"];
	if (isset ($_REQUEST["action"]))         $action         = $_REQUEST ["action"];         else $action = "";
	if (isset ($_REQUEST["patient_id"]))     $patient_id     = $_REQUEST ["patient_id"];     else $patient_id = 0;
	// print ("active_goal_id: $active_goal_id<hr/>\n");
	
	// $active_goal_id = 2;
	$insert_result = "";
	if (isset($action) && $action == "submit"){
		$insert_result = insert_patient ($name, $pw, $institute_id, $patient_instid, $patient_id);
	}
	
	if (isset($action) && $action == "remove"){
		$insert_result = remove_patient ($patient_id, $name);
	}
	
	if (isset($action) && $action == "edit"){
		$patient = get_patient ($patient_id);
	}else{
		$patient = array();
		$patient ["user"]       = "";
		$patient ["patient_id"] = "";
	}
	
	$institutes = get_institutes ();
	$logopedist = get_user ();
	foreach ($institutes as $institute_id => $institute){
		if ($action == "edit"){
			if ($institute_id == $patient["institute_id"]){
				$institutes [$institute_id]["selected"] = "selected=\"selected\"";
			}else{
				$institutes [$institute_id]["selected"] = "";
			}
		}else{
			if ($institute_id == $logopedist["institute_id"]){
				$institutes [$institute_id]["selected"] = "selected=\"selected\"";
			}else{
				$institutes [$institute_id]["selected"] = "";
			}
		}
	}
	
?>
<link href="css/patient.css" type="text/css" rel="stylesheet" />
	<div class="leftcolumn">
		<!--pre>
			insert_result: <?=$insert_result?>
			action: <?=$action?>
			<?php print_r ($_REQUEST); ?>
		</pre-->
	</div>
	
	<div class="name_box">
		<span style="position:relative; left:11px; top:10px">Inlognaam pati&euml;nt:</span>
		<input class="name_field" id="name_field" type="text" name="patient_name" value="<?=$patient["user"]?>" />
		<br/>
		<span style="position:relative; left:11px; top:50px">wachtwoord<?php if($action=="edit") print(" (leeg laten om niet te wijzigen)"); ?>:</span>
		<input class="pw1_field" id="pw1_field" type="password" name="pw1" value="" />
		<br/>
		<span style="position:relative; left:11px; top:90px">wachtwoord nogmaals ter controle:</span>
		<input class="pw2_field" id="pw2_field" type="password" name="pw2" value="" />
		<br/>
		<span style="position:relative; left:11px; top:130px">Instituut:</span>
		<select class="institute_field" id="institute_field" name="institute_id">
			<?php foreach ($institutes as $institute_id => $institute): ?>
				<option value="<?=$institute_id?>" <?=$institute["selected"]?>><?=$institute["name"]?></option>
			<?php endforeach; ?>
		</select>
		<br/>
		<span style="position:relative; left:11px; top:170px">Pati&euml;nt id:</span>
		<input class="patientid_field" id="patientid_field" type="text" name="patient_instid" value="<?=$patient["patient_id"]?>" />
	</div>
	<div class="name_remove" id="name_remove" onClick="patientRemove();">
		<span style="position:relative; top:15%">verwijderen</span>
	</div>
	<div class="name_cancel" id="name_cancel" onClick="saveCancel();">
		<span style="position:relative; top:15%">annuleren</span>
	</div>
	<div class="name_save" onClick="patientSubmit();">
		<span style="position:relative; top:15%">opslaan</span>
	</div>
	
	<script>
		
		<?php if ($action=="edit"): ?>
   		$(".name_save").css("visibility", "visible");
   		$(".name_remove").css("visibility", "visible");
		<?php endif; ?>

		function patientSubmit(){
			name           = $("#name_field").val();
			pw1            = $("#pw1_field").val();
			pw2            = $("#pw2_field").val();
			institute_id   = $("#institute_field").val();
			// alert ("institute_id: "+institute_id);
			patient_instid = $("#patientid_field").val();
			if (pw1==pw2){
				cb = Math.random();
				$('#content').load('/content/logopedist/patient.php', {
					name: name, 
					pw: pw1, 
					institute_id: institute_id, 
					patient_instid: patient_instid, 
					action: "submit",
					patient_id: "<?=$patient_id?>",
					cb: cb
				});
			}else{
				alert("controle wachtwoord komt niet overeen met het wachtwoord.");
			}
		}
		
		function patientRemove(){
			if (confirm("Wilt u zeker patient '<?=$patient["user"]?>' verwijderen?")) {
				cb = Math.random();
				$('#content').load('/content/logopedist/patient.php', {
					action: "remove",
					patient_id: "<?=$patient_id?>",
					name: "<?=$patient["user"]?>",
					cb: cb
				});
			}
		}
		
		$("#name_field").keyup(function(event){
			str = $("#name_field").val();
				pw1 = $("#pw1_field").val();
				pw2 = $("#pw2_field").val();
			// alert ("typed: "+str);
			<?php if ($action=="edit"): ?>
				if (str.length){
			<?php else: ?>
				if (str.length && pw1.length && pw2.length){
			<?php endif;?>
		   		$(".name_save").css("visibility", "visible");
		    }else{
		   		$(".name_save").css("visibility", "hidden");
		   	}
		});

		$("#pw1_field").keyup(function(event){
			str = $("#name_field").val();
			pw1 = $("#pw1_field").val();
			pw2 = $("#pw2_field").val();
			// alert ("typed: "+str);
			if (str.length && pw1.length && pw2.length){
	   		$(".name_save").css("visibility", "visible");
	    }else{
	   		$(".name_save").css("visibility", "hidden");
	   		}
		});

		$("#pw2_field").keyup(function(event){
			str = $("#name_field").val();
			pw1 = $("#pw1_field").val();
			pw2 = $("#pw2_field").val();
			// alert ("typed: "+str);
			if (str.length && pw1.length && pw2.length){
	   		$(".name_save").css("visibility", "visible");
	    }else{
	   		$(".name_save").css("visibility", "hidden");
	   		}
		});
	
		function saveCancel(){
			// alert ("cancel");
			<?php if ($patient_id): ?>
				$('#content').load('/content/logopedist/patienten.php?patient_id=<?=$patient_id?>');
				$('.name_box').load('/content/logopedist/patientinfo.php?patient_id=<?=$patient_id?>');
   			$(".name_cancel").css("visibility", "hidden");
   			$(".name_save").css("visibility", "hidden");
			<?php else: ?>
				$('#content').load('/content/logopedist/index.php');
			<?php endif; ?>
		}

		<?php if ($insert_result): ?>
			<?php if ($insert_result=="success"): ?>
				alert ("patiënt '<?=$name?>' toegevoegd.");
			<?php elseif ($insert_result=="update success"): ?>
				$('#content').load('/content/logopedist/patienten.php?patient_id=<?=$patient_id?>');
				$('.name_box').load('/content/logopedist/patientinfo.php?patient_id=<?=$patient_id?>');
   			$(".name_cancel").css("visibility", "hidden");
			<?php elseif (strstr($insert_result, "verwijderd")): ?>
				alert ("<?=$insert_result?>");
				$('#content').load('/content/logopedist/patienten.php');
			<?php elseif (strstr($insert_result, "Er bestaat al een gebruiker")): ?>
				alert ("Aanpassing mislukt.'\n<?=$insert_result?>");
				<?php if($patient_id): ?>
					// alert ("content load");
					$('#content').load('/content/logopedist/patienten.php?patient_id=<?=$patient_id?>');
					$('.name_box').load('/content/logopedist/patient.php?patient_id=<?=$patient_id?>');
				<?php endif; ?>
			<?php else: ?>
				alert ("patiënt toevoegen mislukt.\n<?=$insert_result?>");
			<?php endif; ?>
		<?php endif; ?>
	</script>
	