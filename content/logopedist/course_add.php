<?php
	require_once ("../../include/functions.php");
	check_role (array("logopedist"));

	if (isset ($_REQUEST["patient_id"])) $patient_id = $_REQUEST ["patient_id"];  else $patient_id = 0;
	if (isset ($_REQUEST["id"]))         $id         = $_REQUEST ["id"];          else $id = 0;
	if (isset ($_REQUEST["items"]))      $items      = trim ($_REQUEST["items"]); else $tems = "";
	if (!($logopedist_id=$_SESSION ["id"])){
		$logopedist_id = $_COOKIE ["id"];
	}
	$inseret_id = copy_course ($logopedist_id, $patient_id, $id, $items);
	
	$patientcourses = get_courses ($logopedist_id,  $patient_id);
	// print ("copy_course ($logopedist_id, $patient_id, $id)<br/>");
?>
		<ul id="sortable1" class="connectedSortable">
			<?php foreach ($patientcourses as $patientcourse): ?>
				<li class="draggable" id="<?=$patientcourse["id"]?>">
					<?=$patientcourse["name"]?>&nbsp;
					<img src="/images/editw.png" height="15px" alt="" align="middle" id="pic<?=$patientcourse["id"]?>" 
						onclick="editCourse(<?=$logopedist_id?>,<?=$patient_id?>,<?=$patientcourse["id"]?>,1);" 
						style="cursor:pointer; margin-top:-11px" 
					/> 
					<!--<?=$patientcourse["id"]?>-->
				</li>
			<?php endforeach; ?>
		</ul>
<script>
	var sortable1 = document.getElementById("sortable1");
	var sortable2 = document.getElementById("sortable2");
	Sortable.create(sortable1, { group: "sortable_edit_module" });
/*
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
				// $('.sortable1').load('/content/logopedist/course_add.php?logopedist_id=<?=$logopedist_id?>&patient_id=<?=$patient_id?>&id='+item.id);
	   	}else{
	   		console.log("remove " + item.id);
	   		remfromlist (item.id);
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
*/
</script>
