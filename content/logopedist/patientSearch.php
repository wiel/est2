<?php
	require_once ("../../include/functions.php");
	
	check_role (array("logopedist"));
	$str = $_REQUEST ["str"];
	$dt = date ("Y-m-d H:i:s");
	$logfile  = "$writable/patientSearch.log";
	// file_put_contents ($logfile, "$dt patientSearch - str: '$str'\n", FILE_APPEND);
	// error_log ("$dt patientSearch - str: '$str'");
	$pr  = "SELECT id,user,person_id,p_max,i_min,institute_id,patient_id FROM users WHERE role='patient' AND logopedist_id=:logopedist_id AND users.active=1 AND "; 
	// file_put_contents ($logfile, "$dt patientSearch -  pr: '$pr'\n", FILE_APPEND);
	$ex ["logopedist_id"] = $_SESSION['id'];
	if (substr($str,0,3) == "***"){
		$chars = str_split (substr($str,3));
		$pr .= "(";
		foreach ($chars as $pos => $char){
			$pr .= "user LIKE :user$pos OR ";
			$ex ["user$pos"] = $char."%";			
		}
		$pr .= "0)";
	}else{
		$pr .= "user LIKE :user";
		$ex ["user"] = "%".$str."%";
	}
	$ex_r = print_r ($ex, 1);
	// file_put_contents ($logfile, "$dt patientSearch - exr:\n$ex_r\n\n", FILE_APPEND);
	$st = $db->prepare($pr);
	$st->execute($ex);
	$patients = $st->fetchAll(PDO::FETCH_CLASS);
	header('Content-type: application/json');
	echo json_encode ($patients);
?>
	