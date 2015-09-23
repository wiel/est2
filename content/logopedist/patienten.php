<?php
	require_once ("../../include/functions.php");
	require_once ("../../include/Browser.php");

	check_role (array("logopedist"));

	if (isset ($_REQUEST["logopedist_id"])) $logopedist_id = $_REQUEST ["logopedist_id"];
	if (isset ($_REQUEST["patient_id"]))    $patient_id    = $_REQUEST ["patient_id"];      else $patient_id = 0;
	if (isset ($_REQUEST["modules"]))       $course_modules = trim ($_REQUEST ["modules"]);
	if (isset ($_REQUEST["name"]))          $name          = $_REQUEST ["name"];
	if (isset ($_REQUEST["action"]))        $action        = $_REQUEST ["action"];          else $action = "";
	if (isset ($_REQUEST["id"]))            $id            = $_REQUEST ["id"];              else $id = 0;
	
	$course_inserted = false;
	if (isset($action) && $action == "submit"){
		// print ("insert_module ($name, $course_modules)<hr/>\n");
		// print ("submit_module ($id)<hr/>\n");
		insert_course ($name, $course_modules, $logopedist_id, $patient_id, $id);
		$course_inserted = true;
	}

?>
<style>
	::-webkit-scrollbar {
		background:       #f8fcfd;
	}
	::-webkit-scrollbar-thumb {
		background:       #fec424;
		-webkit-border-radius: .5ex;
	}
</style>
<link href="css/patienten.css"     type="text/css" rel="stylesheet" />
	<div class="leftcolumn">
		<div class="leftcolumnhead">
			<input type="text" value="zoeken" name="search" id="search" style="padding-left:4px;" onFocus="handleSearch($('#search'))" /> <img src="/images/search.png" style="position:relative; top:3px; left:6px;" alt="" onClick="searchSubmit($('#search').val()); $('.patient_box').css('visibility','hidden');">
		</div>
		<div class="goal_item" onClick="showall('')">
			<span style="position:relative; top:7px;">Pati&euml;nt zoeken</span>
		</div>
		<div class="goal_item" id="abcde">
			<div class="inner_item" onClick="search('abcde')">
				<span style="position:relative; top:7px;">ABCDE</span>
			</div>
		</div>
		<div class="goal_item" id="fghij">
			<div class="inner_item" onClick="search('fghij')">
				<span style="position:relative; top:7px;">FGHIJ</span>
			</div>
		</div>
		<div class="goal_item" id="klmno">
			<div class="inner_item" onClick="search('klmno')">
				<span style="position:relative; top:7px;">KLMNO</span>
			</div>
		</div>
		<div class="goal_item" id="pqrstu">
			<div class="inner_item" onClick="search('pqrstu')">
				<span style="position:relative; top:7px;">PQRSTU</span>
			</div>
		</div>
		<div class="goal_item" id="vwxyz">
			<div class="inner_item" onClick="search('vwxyz')">
				<span style="position:relative; top:7px;">VWXYZ</span>
			</div>
		</div>
		<div class="result_item" id="result_item">
			<div class="inner_result" id="inner_result">
				<span style="position:relative; top:7px;"></span>
			</div>
		</div>
	</div>
	<div class="patient_box" id="patient_box">
		<br/>
	</div>
	<script>
		var ids = ["abcde", "fghij", "klmno", "pqrstu", "vwxyz"];
		var active_search = "";

		<?php if ($patient_id): ?>
			showPatient (<?=$patient_id?>);
		<?php endif; ?>
			
		$("#search").keyup(function(event){
	    if(event.keyCode == 13){
	    	// alert ("enter, submit: '"+$('#search').val()+"'");
	    	searchSubmit($('#search').val());
				$(".patient_box").css("visibility", "hidden");
	    }
		});

		function search(id){
			// $("#vwxyz").fadeOut("slow");
			// alert ("active_search: '"+active_search+"', id: '"+id+"'");
			// console.log ("active_search: '"+active_search+"', id: '"+id+"'");
			if (id == active_search){
				showall("");
			}else{
				ids.forEach(function(entry) {
					if (entry != id){
						$("#"+entry).hide();
					}
				});
				active_search = id;
				searchSubmit("***"+id)
			}
		}

		function showall(id){
			// $("#vwxyz").fadeOut("slow");
			ids.forEach(function(entry) {
				$("#"+entry).show();
			});
			active_search = "";
			$(".result_item").css("visibility", "hidden");
			$(".patient_box").css("visibility", "hidden");
		}

		function hideall(){
			ids.forEach(function(entry) {
				$("#"+entry).hide();
			});
		}

		function searchSubmit (string)
		{
			// console.log ("searchSubmit: '"+string+"'");
			// alert ("obj.val(): '"+obj.val()+"'");
			// search("0");
			if (string.substring(0,3) == "***"){
				active_search = string.substring(3);
			}else{
				hideall();
			}
			$(".result_item").css("visibility", "visible");
			jQuery.getJSON("/content/logopedist/patientSearch.php", {str: string, cb: Math.random()}, function(result){
				// console.log ("patients: "+result);
				html = "";
				$.each(result, function(index, value) {
					// console.log(value);
					html += '<span style="padding-left:4px; cursor:pointer;" onClick="showPatient('+value.id+');">' + value.user + '</span><br/>\n';
				});
				// console.log ("inner_result: " + html);
				$('#inner_result').html(html);
			});
		}
		
		function showPatient (patientId)
		{
			// alert("show patient "+patientId);
			$(".patient_box").css("visibility", "visible");
			$('.patient_box').load('/content/logopedist/patientinfo.php?patient_id='+patientId);
		}
	</script>
