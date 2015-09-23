<?php session_start(); ?>
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
	<!--script src="js/simple_vu.js"></script-->
</head>
<body>
	<div class="blur_area1" id="blur_area1">
		<div class="logo_area" id="logo_area">
			<div class="logo"   id="logo"><img src="images/logo-trans-mt.png"/></div>
			<div class="titel"  id="titel">e-learning speech therapy</div>
			<div class="logoff" id="logoff" onClick="logOff()">afmelden</div>
		</div>
		<div class="menu" id="menu">
			<div class="item1" id="item1" onClick="handleMenuTabs(1)"><span class="menu_item_text1">Wat is EST?</span></div>
			<div class="item2" id="item2" onClick="handleMenuTabs(2)"><span class="menu_item_text2">Gebruiksaanwijzing</span></div>
			<div class="item3" id="item3" onClick="handleMenuTabs(3)"><span class="menu_item_text3">Oefeningen</span></div>
			<div class="item4" id="item4" onClick="handleMenuTabs(4)"><span class="menu_item_text4">Help</span></div>
		</div>
		<div class="menu_line" id="menu_line">
		</div>
	</div>

	<div class="content" id="content">

		<div class="blur_area2" id="blur_area2">
			<div class="video" id="video">
				<span class="video_text">INSTRUCTIE VIDEO</span>
			</div>
	
			<div class="login_button_logopedist" id="login_button_logopedist" onClick="login('logopedist')">
				<div class="logopedist_title" id="logopedist_title">
					<span class="logopedist_title_text">Login logopedist</span>
				</div>
				<div class="logopedist_img" id="logopedist_img">
				</div>
			</div>
			<div class="login_button_patient" id="login_button_patient" onClick="login('patient')">
				<div class="patient_title" id="patient_title">
					<span class="patient_title_text">Login pati&euml;nt</span>
				</div>
				<div class="patient_img" id="patient_img">
				</div>
			</div>
	
			<div class="info" id="info">
				<div class="info_title" id="info_title">
					<span class="info_title_text">Info</span>
				</div>
				<div class="info_img" id="info_img">
				</div>
			</div>
	
			<div class="contact" id="contact">
				<div class="contact_title" id="contact_title">
					<span class="contact_title_text">Contact</span>
				</div>
				<div class="contact_img" id="contact_img">
				</div>
			</div>
	
			<div class="est" id="est">
				E-learning<br/>
				Speech-based Therapy
			</div>
		</div>

		<div class="login_popup" id="login_popup">
			<div class="login_title" id="login_title">
				<span class="login_title_text">Login</span>
				<div class="close_button" id="close_button" onClick="deblur()">
				</div>
			</div>
			<div class="login_form" id="login_form">
				<table style="width:100%">
					<tr><td style="height:20px;"><br/></td></tr>
					<tr><td style="height:110px;"><img src="/images/person.png" alt=""></td></tr>
					<tr><td style="height: 60px; font-weight:bold;" class="inlogtype">persoon</td></tr>
					<tr><td style="height: 60px;"><input type="text" name="user" id="user" value="gebruikersnaam"        onkeydown="handleUser($('#user'))" onClick="handleUser($('#user'))" /></td></tr>
					<tr><td style="height: 60px;"><input type="text" name="pw"   id="pw"   value="wachtwoord"            onFocus="handlePw($('#pw'))"                                        /></td></tr>
					<tr><td style="height: 60px; font-weight:bold;"><input type="button" name="action" value="aanmelden" onClick="pwSubmit()"                                                /></td></tr>
					<tr><td style="height: 60px;" class="msg">wachtwoord vergeten? klik <i>hier</i></td></tr>
				</table>
			</div>
		</div>
	</div>
	<!--div class="lang" id="lang">
		<br/>
	</div-->
	<div class="webaudio" id="webaudio"></div>
</body>
	<script>
		$(".menu").css("visibility", "hidden");
		$(".menu_line").css("visibility", "hidden");
		createCookie ("menu_item", 0);
		checkUser();

		$("#user").keyup(function(event){
	    if(event.keyCode == 13){
	    	document.getElementById("pw").focus();
	    }
		});

		$("#pw").keyup(function(event){
	    if(event.keyCode == 13){
	    	// alert ("enter");
	    	pwSubmit();
	    }
		});
	</script>
</html>

