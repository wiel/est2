<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>E-learning SpraakTherapie</title>
	<link href="css/styles.css" type="text/css" rel="stylesheet" />
	<script src="js/jquery-1.11.0.min.js"></script>	
	<script src="js/functions.js"></script>
	<script src="js/dateFormat.js"></script>
	<script src="js/rec.js"></script>
	<script src="js/simple_vu.js"></script>
<script>
	var moduleId = 1;
	var audioId  = 1;
</script>
</head>
<body>
	<div class="logo_area" id="logo_area">
		<div class="logo" id="logo"><img src="images/logo-trans-mt.png" /></div>
		<div class="titel" id="titel">e-learning<br/>speech therapy</div>
	</div>
	<div class="menu" id="menu">
		<div class="item1" id="item1" onClick="handleMenuTabs(1)"><span class="menu_item_text">Wat is EST?</span></div>
		<div class="item2" id="item2" onClick="handleMenuTabs(2)"><span class="menu_item_text">Gebruiksaanwijzing</span></div>
		<div class="item3" id="item3" onClick="handleMenuTabs(3)"><span class="menu_item_text">Oefeningen</span></div>
		<div class="item4" id="item4" onClick="handleMenuTabs(4)"><span class="menu_item_text">Help</span></div>
	</div>
	<div class="menu_line" id="menu_line">
	</div>
	<div class="content" id="content">
		<div class="vu" id="vu">
		<canvas id="canvas" width="20" height="100" style="display: block; background-color:#333333; margin: 3px;"></canvas>
		max <span id="vu_max"></span>
		</div>
		<br/>
		<button onclick="startRecording(this);">record</button>
		<button onclick="stopRecording(this);" disabled>stop</button>
		<h4>Recordings</h4>
		<span id="recordingslist"></span>
		
		<pre id="log"></pre>
	</div>
	
</body>
</html>

	