<?php
	session_start();
	$db = new PDO('mysql:host=mysql-est2.science.ru.nl;dbname=est2;charset=utf8', 'est2_user', 'oothuephahwee');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	
	$docroot  = "/var/www1/est/live";
	$writable = "$docroot/writable";
	$logfile  = "$writable/debug.log";
	
	function check_role ($accepted_roles)
	{
		// print_r ($_SESSION);
		// print_r ($_COOKIE);
		if (!($user=$_SESSION ["user"])){
			$user = $_COOKIE ["user"];
		}
		if (!($role=$_SESSION ["role"])){
			$role = $_COOKIE ["role"];
		}
		
		if (!in_array($role, $accepted_roles)){
			print ("No access for '$user'-'$role'");
			exit;
		}
	}
	
	function get_user ()
	{
		global $db;
		global $logfile;
		
		if (!($user=$_SESSION ["user"])){
			$user = $_COOKIE ["user"];
		}
		$pr = "SELECT * FROM users WHERE user=:user";
		$st = $db->prepare($pr);
		$ex ["user"] = $user;
		$st->execute($ex);
		return $st->fetch (PDO::FETCH_ASSOC);
	}

	function get_audios()
	{
		global $db;
		global $logfile;
		
		$pr = "SELECT * FROM audio ORDER BY `name`";
		$st = $db->prepare($pr);
		$st->execute();
		while ($rw = $st->fetch (PDO::FETCH_ASSOC)){
			$array [$rw["id"]] = $rw;
		}
		return $array;
	}

	function get_used_audio_ids ()
	{
		global $db;
		global $logfile;
		
		$pr = "SELECT audio_ids FROM modules";
		$st = $db->prepare($pr);
		$st->execute();
		$audio_ids = array();
		while ($rw = $st->fetch (PDO::FETCH_ASSOC)){
			$audio_ids = array_unique (array_merge ($audio_ids, explode(" ",$rw["audio_ids"])));
		}
		// file_put_contents ($logfile, print_r($audio_ids,1), FILE_APPEND);
		return $audio_ids;
	}

	function del_audio($id, $file)
	{
		global $db;
		global $logfile;
		
		$pr = "DELETE FROM audio WHERE id=:id";
		$st = $db->prepare($pr);
		$ex ["id"] = $id;
		// print ("$pr<br/>\n");
		// print_r ($ex);
		$st->execute($ex);
		$filepath = "/var/www1/est/live/writable/audio/$file";
		// print ("<br/>\nunlink ($filepath)<br/>");
		unlink ($filepath);
	}
	
	function get_users()
	{
		global $db;
		global $logfile;
		
		$pr = "SELECT * FROM users ORDER BY `id`";
		$st = $db->prepare($pr);
		$st->execute();
		// return $st->fetchAll (PDO::FETCH_ASSOC);
		while ($rw = $st->fetch (PDO::FETCH_ASSOC)){
			$array [$rw["id"]] = $rw;
		}
		return $array;
	}

	function get_goals()
	{
		global $db;
		global $logfile;
		
		$pr = "SELECT * FROM goals ORDER BY `id`";
		$st = $db->prepare($pr);
		$st->execute();
		// return $st->fetchAll (PDO::FETCH_ASSOC);
		while ($rw = $st->fetch (PDO::FETCH_ASSOC)){
			$array [$rw["id"]] = $rw;
		}
		return $array;
	}

	function get_modules ($goal_id=0)
	{
		global $db;
		global $logfile;
		
		$array = array();
		if ($goal_id){
			$pr = "SELECT * FROM modules WHERE goal_id=:goal_id ORDER BY `name`";
			$st = $db->prepare($pr);
			$ex ["goal_id"] = $goal_id;
			$st->execute($ex);
			// return $st->fetchAll (PDO::FETCH_ASSOC);
			while ($rw = $st->fetch (PDO::FETCH_ASSOC)){
				$array [$rw["id"]] = $rw;
			}
		}else{
			$pr = "SELECT modules.*,goals.name as goal_name FROM modules,goals WHERE goal_id=goals.id ORDER BY goal_id,modules.name";
			$st = $db->prepare($pr);
			$st->execute();
			// return $st->fetchAll (PDO::FETCH_ASSOC);
			while ($rw = $st->fetch (PDO::FETCH_ASSOC)){
				$array [$rw["id"]] = $rw;
			}
		}
		return $array;
	}

	function get_used_module_ids ()
	{
		global $db;
		global $logfile;
		
		$pr = "SELECT module_ids FROM courses";
		$st = $db->prepare($pr);
		$st->execute();
		$module_ids = array();
		while ($rw = $st->fetch (PDO::FETCH_ASSOC)){
			$module_ids = array_unique (array_merge ($module_ids, explode(" ",$rw["module_ids"])));
		}
		file_put_contents ($logfile, print_r($module_ids,1), FILE_APPEND);
		return $module_ids;
	}

	function get_courses ($logopedist_id=0, $patient_id=0, $course_id=0)
	{
		global $db;
		global $logfile;
		
		$users = get_users ();
		$users [0] ["user"] = "";
		$array = array();
		if ($course_id){
			$pr = "SELECT * FROM courses WHERE courses.id=:id ORDER BY `order`";
			$ex ["id"] = $course_id;
		}elseif ($logopedist_id && $patient_id>0){
			$pr = "SELECT * FROM courses WHERE courses.logopedist_id=:logopedist_id AND courses.patient_id=:patient_id ORDER BY `order`";
			$ex ["logopedist_id"] = $logopedist_id;
			$ex ["patient_id"]    = $patient_id;
		}elseif ($logopedist_id && $patient_id<0){ // get all courses from logopedist EXCEPT those for patient (with negative id)
			$pr = "SELECT * FROM courses WHERE courses.logopedist_id=:logopedist_id AND courses.patient_id!=:patient_id ORDER BY `name`";
			$ex ["logopedist_id"] =  $logopedist_id;
			$ex ["patient_id"]    = -$patient_id;
		}elseif ($logopedist_id){
			$pr = "SELECT * FROM courses WHERE courses.logopedist_id=:logopedist_id ORDER BY `order`";
			$ex ["logopedist_id"] = $logopedist_id;
		}elseif ($patient_id){
			$pr = "SELECT * FROM courses WHERE courses.patient_id=:patient_id ORDER BY `order`";
			$ex ["patient_id"]    = $patient_id;
		}else{
			$pr = "SELECT * FROM courses ORDER BY `order`";
		}
		$st = $db->prepare($pr);
		$st->execute($ex);
		// return $st->fetchAll (PDO::FETCH_ASSOC);
		while ($rw = $st->fetch (PDO::FETCH_ASSOC)){
			$array [$rw["id"]] = $rw;
			$array [$rw["id"]] ["user"]     = $users [$rw["patient_id"]] ["user"];
			$array [$rw["id"]] ["u_active"] = $users [$rw["patient_id"]] ["active"];
			// error_log ("id: {$rw["patient_id"]}, user: {$users[$rw["patient_id"]]["user"]}");
		}
		return $array;
	}
	
	function order_courses ($order_ids){
		global $db;
		global $logfile;
		
		$course_ids = explode (" ", $order_ids);
		$order = 1;
		foreach ($course_ids as $course_id){
			$pr = "UPDATE courses SET `order`='$order' WHERE id=:id";
			$ex ["id"] = $course_id;
			$st = $db->prepare($pr);
			$st->execute($ex);
			unset ($ex);
			$order++;
		}
	}

	function insert_module ($name, $audios, $id, $goal_id, $purpose, $instruction, $remark, $reference)
	{
		global $db;
		global $logfile;
		
		$ex ["name"]        = $name;
		$ex ["audio_ids"]   = $audios;
		$ex ["purpose"]     = $purpose;
		$ex ["instruction"] = $instruction;
		$ex ["remark"]      = $remark;
		$ex ["reference"]   = $reference;
		if ($id){
			$pr = "UPDATE modules SET name=:name, audio_ids=:audio_ids, purpose=:purpose, instruction=:instruction, remark=:remark, reference=:reference WHERE id=:id";
			$ex ["id"] = $id;
		}else{
			$pr = "INSERT INTO modules SET name=:name, audio_ids=:audio_ids, purpose=:purpose, instruction=:instruction, remark=:remark, reference=:reference, goal_id=:goal_id";
			$ex ["goal_id"] = $goal_id;
		}
		// print ("$pr -- ");
		// print_r ($ex);
		$st = $db->prepare($pr);
		$st->execute($ex);
	}
	
	function insert_course ($name, $course_modules, $logopedist_id, $patient_id, $id=0)
	{
		global $db;
		global $logfile;
		
		$ex ["name"]           = $name;
		$ex ["module_ids"]     = $course_modules;
		$ex ["logopedist_id"]  = $logopedist_id;
		$ex ["patient_id"]     = $patient_id;
		if ($id){
			$pr = "UPDATE courses SET name=:name, module_ids=:module_ids, logopedist_id=:logopedist_id, patient_id=:patient_id WHERE id=:id";
			$ex ["id"] = $id;
		}else{
			$pr = "INSERT INTO courses SET name=:name, module_ids=:module_ids, logopedist_id=:logopedist_id, patient_id=:patient_id";
		}
		// print ("$pr -- ");
		// print_r ($ex);
		$st = $db->prepare($pr);
		$st->execute($ex);
		return $db->lastInsertId();
	}
	
	function copy_course ($logopedist_id, $patient_id, $id=0, $order_ids="")
	{
		global $db;
		global $logfile;
		
		$pr = "SELECT * FROM courses WHERE id=:id";
		$ex ["id"] = $id;
		$st = $db->prepare($pr);
		$st->execute($ex);
		if ($rw = $st->fetch (PDO::FETCH_ASSOC)){
			$insert_id = insert_course ("copy of {$rw["name"]}", $rw ["module_ids"], $logopedist_id, $patient_id);
		}
		$order_ids = str_replace ($id, $insert_id, $order_ids);
		if (strlen($order_ids)){
			order_courses ($order_ids);
		}
		return $insert_id;
	}
	
	function remove_course ($id=0)
	{
		global $db;
		global $logfile;
		
		$pr = "UPDATE courses SET patient_id=0 WHERE id=:id";
		$ex ["id"] = $id;
		$st = $db->prepare($pr);
		$st->execute($ex);
	}
	
	function delete_course ($id=0, $logopedist_id=0)
	{
		global $db;
		global $logfile;
		
		$pr = "DELETE FROM courses WHERE id=:id";
		$ex ["id"] = $id;
		$st = $db->prepare($pr);
		$st->execute($ex);
	}
	
	function insert_patient ($name, $pw, $institute_id, $patient_instid="", $patient_id=0)
	{
		global $db;
		global $logfile;
		
		if ($patient_id){
			$pr = "SELECT * FROM users WHERE user=:user AND id!=:id";
			$ex ["user"]         = $name;
			$ex ["id"]           = $patient_id;
			$st = $db->prepare($pr);
			$st->execute($ex);
			if ($st->fetch()) {
				return ("Er bestaat al een gebruiker met naam '$name'");
			}
			unset ($ex);
			if ($pw){
				$ex ["user"]          = $name;
				$ex ["pw"]            = md5 ($pw);
				$ex ["institute_id"]  = $institute_id;
				$ex ["patient_id"]    = $patient_instid;
				$ex ["id"]            = $patient_id;
				$pr = "UPDATE users SET user=:user, pw=:pw, institute_id=:institute_id, patient_id=:patient_id WHERE id=:id";
			}else{
				$ex ["user"]          = $name;
				$ex ["institute_id"]  = $institute_id;
				$ex ["patient_id"]    = $patient_instid;
				$ex ["id"]            = $patient_id;
				$pr = "UPDATE users SET user=:user, institute_id=:institute_id, patient_id=:patient_id WHERE id=:id";
			}
			// print ("$pr -- ");
			// print_r ($ex);
			$st = $db->prepare($pr);
			$st->execute($ex);
			return ("update success");
		}else{
			$pr = "SELECT * FROM users WHERE user=:user";
			$ex ["user"]         = $name;
			$st = $db->prepare($pr);
			$st->execute($ex);
			if ($st->fetch()) {
				return ("Er bestaat al een gebruiker '$name'");
			}
			unset ($ex);
			$ex ["user"]          = $name;
			$ex ["pw"]            = md5 ($pw);
			$ex ["role"]          = "patient";
			$ex ["institute_id"]  = $institute_id;
			$ex ["patient_id"]    = $patient_instid;
			$ex ["logopedist_id"] = $_SESSION['id'];
			$pr = "INSERT INTO users SET user=:user, pw=:pw, role=:role, institute_id=:institute_id, patient_id=:patient_id, logopedist_id=:logopedist_id, active=1";
			// print ("$pr -- ");
			// print_r ($ex);
			$st = $db->prepare($pr);
			$st->execute($ex);
			return ("success");
		}
	}

	function remove_patient ($patient_id=0, $name="")
	{
		global $db;
		global $logfile;

		$ex ["id"]            = $patient_id;
		$pr = "UPDATE users SET user=CONCAT(user,'_inactive'), active=0 WHERE id=:id";
		$st = $db->prepare($pr);
		$st->execute($ex);
		return ("patient '$name' verwijderd.");
	}
	
	function get_mod_info ($id)
	{
		global $db;
		global $logfile;

		$pr = "SELECT * FROM modules WHERE id=:id";
		$st = $db->prepare($pr);
		$ex ["id"] = $id;
		$st->execute($ex);
		return $st->fetch (PDO::FETCH_ASSOC);
	}
	
	function get_course_info ($id)
	{
		global $db;
		global $logfile;

		$pr = "SELECT * FROM courses WHERE id=:id";
		$st = $db->prepare($pr);
		$ex ["id"] = $id;
		$st->execute($ex);
		return $st->fetch (PDO::FETCH_ASSOC);
	}
	
	function delete_module ($id)
	{
		global $db;
		global $logfile;
		
		$pr = "DELETE FROM modules WHERE id=:id";
		$st = $db->prepare($pr);
		$ex ["id"] = $id;
		// print ("$pr<br/>\n");
		// print_r ($ex);
		$st->execute($ex);
	}
	
	function get_institutes()
	{
		global $db;
		global $logfile;
		
		$pr = "SELECT * FROM institutes ORDER BY `name`";
		$st = $db->prepare($pr);
		$st->execute();
		// return $st->fetchAll (PDO::FETCH_ASSOC);
		while ($rw = $st->fetch (PDO::FETCH_ASSOC)){
			$array [$rw["id"]] = $rw;
		}
		return $array;
	}

	function insert_institute ($name, $id)
	{
		global $db;
		global $logfile;
		
		$ex ["name"] = $name;
		if ($id){
			$pr = "UPDATE institutes SET name=:name WHERE id=:id";
			$ex ["id"] = $id;
		}else{
			$pr = "INSERT INTO institutes SET name=:name";
		}
		$st = $db->prepare($pr);
		$st->execute($ex);
	}
	
	function del_institute ($id)
	{
		global $db;
		global $logfile;
		
		$pr = "DELETE FROM institutes WHERE id=:id";
		$st = $db->prepare($pr);
		$ex ["id"] = $id;
		// print ("$pr<br/>\n");
		// print_r ($ex);
		$st->execute($ex);
	}
	
	function insert_logopedist ($user, $institute_id, $pw="", $id=0)
	{
		global $db;
		global $logfile;
		
		$ex ["user"]         = $user;
		$ex ["institute_id"] = $institute_id;
		if ($id){
			$pr = "UPDATE users SET user=:user, institute_id=:institute_id, pw=:pw WHERE id=:id";
			$ex ["id"] = $id;
		}else{
			$pr = "INSERT INTO users SET user=:user, institute_id=:institute_id, role='logopedist', active=1, pw=:pw";
			$ex ["pw"] = md5 ($pw);
		}
		$st = $db->prepare($pr);
		$st->execute($ex);
	}
	
	function get_logopedists()
	{
		global $db;
		global $logfile;
		
		$pr = "SELECT * FROM users WHERE role='logopedist' ORDER BY `user`";
		$st = $db->prepare($pr);
		$st->execute();
		// return $st->fetchAll (PDO::FETCH_ASSOC);
		while ($rw = $st->fetch (PDO::FETCH_ASSOC)){
			$array [$rw["id"]] = $rw;
		}
		return $array;
	}

	function get_patient ($id)
	{
		global $db;
		global $logfile;
		
		$pr = "SELECT users.id,user,person_id,logopedist_id,p_max,i_min,institute_id,patient_id,institutes.name AS institute_name
		       FROM users,institutes 
		       WHERE role='patient' 
		       AND users.id=:id AND
		       institutes.id = institute_id
		       ORDER BY `user`";
		$st = $db->prepare($pr);
		$ex ["id"] = $id;
		$st->execute($ex);
		// return $st->fetchAll (PDO::FETCH_ASSOC);
		return $st->fetch (PDO::FETCH_ASSOC);
	}

	function get_logopedist ($id)
	{
		global $db;
		global $logfile;
		
		$pr = "SELECT users.id,user,person_id,logopedist_id,p_max,i_min,institute_id,patient_id,institutes.name AS institute_name
		       FROM users,institutes 
		       WHERE role='logopedist' 
		       AND users.id=:id AND
		       institutes.id = institute_id
		       ORDER BY `user`";
		$st = $db->prepare($pr);
		$ex ["id"] = $id;
		$st->execute($ex);
		// return $st->fetchAll (PDO::FETCH_ASSOC);
		return $st->fetch (PDO::FETCH_ASSOC);
	}
	
	function insert_audio ($user_id, $audio_id, $module_id, $basedir, $basename, $visualization)
	{
		global $db;
		global $logfile;
		
		$pr = "INSERT INTO user_audio SET
		       user_id=:user_id,  
		       audio_id=:audio_id, 
		       module_id=:module_id,
		       basedir=:basedir,
		       basename=:basename,
		       visualization=:visualization";
		$st = $db->prepare($pr);
		$ex ["user_id"]       = $user_id;
		$ex ["audio_id"]      = $audio_id;
		$ex ["module_id"]     = $module_id;
		$ex ["basedir"]       = $basedir;
		$ex ["basename"]      = $basename;
		$ex ["visualization"] = $visualization;
		$st->execute($ex);
		return $db->lastInsertId();
	}

	function approve_audio ($user_audio_id)
	{
		global $db;
		global $logfile;
		
		$pr = "UPDATE user_audio SET approved=1 WHERE id=:id";
		$st = $db->prepare($pr);
		$ex ["id"] = $user_audio_id;
		$st->execute($ex);
	}

	function del_logopedist ($id)
	{
		global $db;
		global $logfile;
		
		$pr = "DELETE FROM users WHERE id=:id AND role='logopedist'";
		$st = $db->prepare($pr);
		$ex ["id"] = $id;
		// print ("$pr<br/>\n");
		// print_r ($ex);
		$st->execute($ex);
	}
	
	function readspeak ($sentence)
	{
		global $db;
		global $writable;
		global $logfile;
		
		$apikey = 'dda2100792a535f79a726f8f9e06eaed';
		$language = 'nl_nl';
		$voice= 'Female01';
		$filename = straightname ("$sentence.mp3");
		$tempname = straightname ("$sentence.tmp.mp3");
		// $filepath = "$writable/audio/$filename";
		$filepath = "/var/www1/est/live/writable/audio/$filename";
		$temppath = "/var/www1/est/live/writable/audio/$tempname";
		file_put_contents ($logfile, "filepath: $filepath\n", FILE_APPEND);
		$api_url = 'http://tts.readspeaker.com/a/produce';
		$url = $api_url . '?key='.$apikey.'&lang='.$language.'&voice='.$voice.'&speed=80&mp3bitrate=128&text='.urlencode($sentence);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($status == 200 && ! curl_errno($ch)) {
			// Everything is fine, close curl and save file
			curl_close($ch);
			file_put_contents ($temppath, $data);
			// add some silence at the beginning
			$cmd = "/usr/local/ffmpeg/bin/ffmpeg -i concat:\"/var/www1/est/live/writable/audio/pre_silence.mp3|$temppath\" -c copy $filepath";
			file_put_contents ($logfile, "$cmd\n", FILE_APPEND);
			exec ($cmd, $output);
			file_put_contents ($logfile, print_r($output,1), FILE_APPEND);
			file_put_contents ($logfile, "saved audio $filepath\n", FILE_APPEND);
			unlink ($temppath);
			$pr = "INSERT INTO audio SET name=:name, file=:file";
			$st = $db->prepare($pr);
			$ex ["name"] = $sentence;
			$ex ["file"] = $filename;
			$st->execute($ex);
		} else {
			// Cannot translate text to speech because of text-to-speech API error
			error_log(__FILE__ . ': API error while text-to-speech. error code=' . $status);
			curl_close($ch);
			print (': API error while text-to-speech. error code=' . $status . "<hr/>\n");
		}
	}

	function straightname ($filename)
	{
		$pattern [] = "/\//";	$replacement [] = "_";
		$pattern [] = "/\?/";	$replacement [] = "_";
		$pattern [] = "/\"/";	$replacement [] = "_";
		$pattern [] = "/:/";	$replacement [] = "_";
		$pattern [] = "/#/";	$replacement [] = "_";
		
		$pattern [] = "/\+/";	$replacement [] = "_plus_";
		$pattern [] = "/\&/";	$replacement [] = "and";
		
		$pattern [] ='/[àáâã]/';        $replacement [] = 'a'; 
		$pattern [] ='/[ä]/';           $replacement [] = 'ae';
		$pattern [] ='/[ÀÁÂÃÅ]/';       $replacement [] = 'A'; 
		$pattern [] ='/[Ä]/';           $replacement [] = 'Ae';
		$pattern [] ='/[ç]/';           $replacement [] = 'c'; 
		$pattern [] ='/[Ç]/';           $replacement [] = 'C'; 
		$pattern [] ='/[èéê]/';         $replacement [] = 'e'; 
		$pattern [] ='/[ë]/';           $replacement [] = 'e'; 
		$pattern [] ='/[ÈÉÊ]/';         $replacement [] = 'E'; 
		$pattern [] ='/[Ë]/';           $replacement [] = 'Ee'; 
		$pattern [] ='/[ñ]/';           $replacement [] = 'n'; 
		$pattern [] ='/[Ñ]/';           $replacement [] = 'N'; 
		$pattern [] ='/[ìíî]/';         $replacement [] = 'i'; 
		$pattern [] ='/[ï]/';           $replacement [] = 'i';
		$pattern [] ='/[ÌÍÎ]/';         $replacement [] = 'I'; 
		$pattern [] ='/[Ï]/';           $replacement [] = 'I';
		$pattern [] ='/[òóôõø]/';       $replacement [] = 'o'; 
		$pattern [] ='/[ö]/';           $replacement [] = 'oe';
		$pattern [] ='/[ÒÓÔÕØ]/';       $replacement [] = 'O'; 
		$pattern [] ='/[Ö]/';           $replacement [] = 'Oe';
		$pattern [] ='/[ß]/';           $replacement [] = 'ss';
		$pattern [] ='/[ùúû]/';         $replacement [] = 'u';
		$pattern [] ='/[ÙÚÛ]/';         $replacement [] = 'U';
		$pattern [] ='/[ü]/';           $replacement [] = 'ue';
		$pattern [] ='/[Ü]/';           $replacement [] = 'Ue';
		$pattern [] ='/[^a-z0-9\-_]/i'; $replacement [] = '_'; 
		
		$path_parts = pathinfo($filename);
		$basename  = $path_parts['filename'];
		$extension = $path_parts['extension'];
		while (substr($basename,-1)==' ' || substr($basename,-1)=='.'){
			$basename = substr($basename, 0, -1);
		}
		if ($filename[0] == "_"){
			$filename = preg_replace ($pattern, $replacement,$basename) . ".$extension";
		}else{
			$filename = strtolower (preg_replace ($pattern, $replacement,$basename)) . ".$extension";
		}
		return $filename;
	}

	function extension ($filename)
	{
		return (substr(strrchr($filename,"."),1));
	}

	function base_name ($path)
	{
		$file = basename ($path);
		return (substr ($file, 0, -strlen(strrchr($file,"."))));
	}
?>
