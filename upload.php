<?php
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	$logfile = "/var/www1/est/live/writable/log.log";
	require_once ("include/functions.php");

 	// exit;	
	// pull the raw binary data from the POST array
	$data = substr($_POST['data'], strpos($_POST['data'], ",") + 1);
	// decode it
	$decodedData = base64_decode($data);
	// print out the raw data, 
	// echo ($decodedData);
	$usrdir    = urldecode($_POST['usrdir']);
	$filename  = urldecode($_POST['fname']);
	$module_id = urldecode($_POST['module_id']);
	$audio_id  = urldecode($_POST['audio_id']);
	$user_id   = urldecode($_POST['user_id']);
 	file_put_contents ($logfile, "upload $filename\n", FILE_APPEND);		
	// write the data out to the file
	mkdir ("/var/www1/est/live/writable/recordings/$usrdir");
	$fp = fopen("/var/www1/est/live/writable/recordings/$usrdir/".$filename, 'wb');
	fwrite($fp, $decodedData);
	fclose($fp);

	$anim_width              = 552;
	$anim_height             = 300;
	$xmarge                  = 40;
	$intensitythreshold      = 50;
	$pitchthreshold          = 175;
	$praatexe                = "/var/www1/est/live/praat_5.2.17";
	$praatscript             = "/var/www1/est/live/vest.praat";
	$disccol_r               = 165; // decimal value for red
	$disccol_g               = 175; // decimal value for green
	$disccol_b               = 150; // decimal value for blue
	$r_deviation             = 20;
	$uploaddir               = "/var/www1/est/live/writable/recordings/$usrdir";
	$pitch_high              = 186;
	$intensity_low           = 8 + $intensitythreshold; // 60
	$drawguides              = 50;
	$preframes               = 1;
	$postframes              = 1;
	$background_color        = "eeffff";
	$background_gradient     = "666688";
	$graph_start_color       = "c8c8c8";
	$default_int_thresh_perc = -10;
	$default_pit_thresh_perc = 50;

	include ("VEST_class_lib.php");

	$pitch_threshold_perc = $default_pit_thresh_perc;
	$intensity_threshold_perc = $default_int_thresh_perc;
	$type   = "error_analytic";
	$errvis = "continuous";
	$color_errvis = "continuous";
	$color_errvis = "discrete";
	$shape_errvis = "continuous";
	$xmarge = 31;
	$render = "render";
	$low_y = -102;
	$height_scale = .5;
	$high_max_pitch = 212;
	$createxml = true;
	$background_type = "gradient";
	$background_var1 = $background_color;
	$background_var2 = $background_gradient;
	chdir ($uploaddir);
	$basename = base_name ($filename);
	$visualization = new VestVisualization ($basename, $praatexe, $praatscript);
 	file_put_contents ($logfile, "-$basename- done\n", FILE_APPEND);			
	//print ("ok en exit\n");
	//exit;

	$visualization->set_ffmpeg            ("/usr/local/ffmpeg/bin/ffmpeg");
	$visualization->set_qtfaststart       ("/usr/bin/qt-faststart");
	$visualization->set_logfile           ("/var/www1/est/live/writable/log.log");
	$visualization->set_usrdir            ($usrdir);
	$visualization->set_opacity           (.5);
	$visualization->set_type              ($type);
	$visualization->set_colorerrvis       ($color_errvis);
	$visualization->set_shapeerrvis       ($shape_errvis);
	$visualization->set_animbasename      ();
	$visualization->set_width             ($anim_width);
	$visualization->set_height            ($anim_height);
	$visualization->set_xmarge            ($xmarge);
	$visualization->set_intensitythreshold($intensitythreshold);
	$visualization->set_pitchthreshold    ($pitchthreshold);
	$visualization->set_disccol_r         ($disccol_r);
	$visualization->set_disccol_g         ($disccol_g);
	$visualization->set_disccol_b         ($disccol_b);
	$visualization->set_rdeviation        ($r_deviation);
	$visualization->set_pitchhigh         ($pitch_high);
	$visualization->set_intensitylow      ($intensity_low);
	$visualization->set_heightscale       ($height_scale);
	$visualization->set_lowy              ($low_y);
	$visualization->set_textfont ("/var/www1/est/live/fonts/verdana.ttf");
	$visualization->set_boldfont ("/var/www1/est/live/fonts/verdanab.ttf");
	$visualization->set_pitcherrvis (true);
	$visualization->set_intensityerrvis (true);
	$visualization->set_postframes        ($postframes);
	$visualization->set_background ($background_type, $background_var1, $background_var2);
	$visualization->set_render (true);
	$visualization->set_shadow (false);
	$visualization->set_imgformat ("bmp");
	$visualization->create_xml();
	// $visualization->writelog("create_xml() done");
	$visualization->create_animation();
	
	$user_audio_id = insert_audio ($user_id, $audio_id, $module_id, $usrdir, $basename, get_visualizationname($visualization));

	function get_visualizationname ($visualization) {
		$type         = $visualization->get_type();
		$color_errvis = $visualization->get_colorerrvis();
		$shape_errvis = $visualization->get_shapeerrvis();
		return ("$type-$color_errvis-$shape_errvis");
	}

  function base_name_local ($path)
  {
    $file = basename ($path);
    return (substr ($file, 0, -strlen(strrchr($file,"."))));
  }
?>
<?=$user_audio_id?>