function checkUser()
{
	var user = urldecode (readCookie ("user"));
	var role = readCookie ("role");
	// alert ("checkUser\nuser: "+user+"\nrole: "+role);
	jQuery.getJSON("/api/check_user.php", {user: user, role: role, cb: Math.random()}, function(result){
	  // alert ("result: "+result.check);
	  if (result.check!="ok"){
			// alert ("login not ok, result: "+result.check+"\nuser: "+user+"\nrole: "+role);
			eraseCookie ("user");
			eraseCookie ("role");
			eraseCookie ("pw");
	  	deblur ("");
	  	// location.reload();
	  }else{
			// alert ("result: "+result.check+"\nuser: "+user+"\nrole: "+role);
			deblur(user);
		}
	});
	menu_item = readCookie ("menu_item");
	// alert ("menu_item: "+menu_item + ", role: "+role);
	switch (role){
		case "super":
		case "admin":
			$(".menu_item_text1").text("Audio");
			$(".menu_item_text2").text("Modules");
			$(".menu_item_text3").text("Logopedisten");
			$(".menu_item_text4").text("Instituten");
			$(".info").css("visibility", "hidden");
			$(".contact").css("visibility", "hidden");
			$(".est").css("visibility", "hidden");
			switch (menu_item){
				case "0":
					$('#content').load('/content/admin/index.php');
					break;
				case "1":
					$('#content').load('/content/admin/audio.php');
					break;
				case "2":
					$('#content').load('/content/admin/modules.php');
					break;
				case "3":
					$('#content').load('/content/admin/logopedisten.php');
					break;
				case "4":
					$('#content').load('/content/admin/instituten.php');
					break;
			}
			break;
		case "logopedist":
			// alert ("logopedist");
			$(".menu_item_text1").text("Patiënt zoeken");
			$(".menu_item_text2").text("Patiënt toevoegen");
			$(".menu_item_text3").text("Handleiding");
			$(".menu_item_text4").text("");
			$(".info").css("visibility", "hidden");
			$(".contact").css("visibility", "hidden");
			$(".est").css("visibility", "hidden");
			// alert ("menu_item: "+menu_item);
			// switch (menu_item){
			switch (Number(menu_item)){
				case 0:
					// alert ("load /content/logopedist/index.php");
					$('#content').load('/content/logopedist/index.php');
					break;
				case 1:
					$('#content').load('/content/logopedist/patienten.php');
					break;
				case 2:
					$('#content').load('/content/logopedist/patient.php');
					break;
				case 3:
					break;
				case 4:
					break;
			}
		break;
		case "patient":
			$(".menu_item_text1").text("Wat is EST?");
			$(".menu_item_text2").text("Gebruiksaanwijzing");
			$(".menu_item_text3").text("Microfoon aanzetten");
			$(".menu_item_text4").text("Help");
			switch (Number(menu_item)){
				case 0:
					$('#content').load('/content/patient/index.php');
					break;
				case 1:
					$('#content').load('/content/patient/nop.php');
					break;
				case 2:
					$('#content').load('/content/patient/nop.php');
					break;
				case 3:
					$('#content').load('/content/patient/index.php');
					break;
				case 4:
					$('#content').load('/content/patient/nop.php');
					break;
			}
		break;
	}
}

function logOff()
{
	// alert ("logOff");
	eraseCookie ("user");
	eraseCookie ("role");
	jQuery.getJSON("/api/logoff.php", {cb: Math.random()}, function(result){});
	alert ("U bent nu uitgelogd");
	location.reload();
	// checkUser();
}

function handleMenuTabs (id)
{
	inactive_bgcolor = "#d1e5e5";
	active_bgcolor   = "#91b7b7";
	inactive_tcolor  = "#91b7b7";
	active_tcolor    = "#ffffff";
	
	for (i=1; i<=4; i++){
		el = document.getElementById("item"+i);
		if (i==id){
			el.style.backgroundColor = active_bgcolor;
			el.style.color = active_tcolor;
		}else{
			el.style.backgroundColor = inactive_bgcolor;
			el.style.color = inactive_tcolor;
		}
	}
	createCookie ("menu_item", id);
	checkUser();
}

$.fn.setCursorPosition = function(pos) {
  this.each(function(index, elem) {
    if (elem.setSelectionRange) {
      elem.setSelectionRange(pos, pos);
    } else if (elem.createTextRange) {
      var range = elem.createTextRange();
      range.collapse(true);
      range.moveEnd('character', pos);
      range.moveStart('character', pos);
      range.select();
    }
  });
  return this;
};

function login (type)
{
	// alert ('login '+type);
	$(".login_popup").css("visibility", "visible");
	$(".login_title_text").text('Login '+type);
	$(".inlogtype").text(type);
	$("#user").val("gebruikersnaam");
	$("#pw").val("wachtwoord");
	$("#pw").prop("type", "text");
	$('#user').focus();
	$('#user').setCursorPosition(0);
	$(".blur_area1").css("-webkit-filter", "blur(5px)");
  $(".blur_area1").css("-moz-filter", "blur(5px)");
  $(".blur_area1").css("-o-filter", "blur(5px)");
  $(".blur_area1").css("-ms-filter", "blur(5px)");
  $(".blur_area1").css("filter", "blur(5px)");
	$(".blur_area2").css("-webkit-filter", "blur(5px)");
  $(".blur_area2").css("-moz-filter", "blur(5px)");
  $(".blur_area2").css("-o-filter", "blur(5px)");
  $(".blur_area2").css("-ms-filter", "blur(5px)");
  $(".blur_area2").css("filter", "blur(5px)");
}

function deblur (user)
{
	$(".login_popup").css("visibility", "hidden");
	$(".blur_area1").css("-webkit-filter", "");
  $(".blur_area1").css("-moz-filter", "");
  $(".blur_area1").css("-o-filter", "");
  $(".blur_area1").css("-ms-filter", "");
  $(".blur_area1").css("filter", "");
	$(".blur_area2").css("-webkit-filter", "");
  $(".blur_area2").css("-moz-filter", "");
  $(".blur_area2").css("-o-filter", "");
  $(".blur_area2").css("-ms-filter", "");
  $(".blur_area2").css("filter", "");
  if (user.length>0){
		// alert ('deblur user 0');
		$(".titel").text("welkom "+user);
		$(".video").css("visibility", "hidden");
		$(".login_button_logopedist").css("visibility", "hidden");
		$(".login_button_patient").css("visibility", "hidden");
		$(".titel").css("visibility", "visible");
		$(".logoff").css("visibility", "visible");
		$(".menu").css("visibility", "visible");
		$(".menu_line").css("visibility", "visible");
	}
}

function handleUser (obj)
{
	// alert ("handle_user");
	if (obj.val() == "inlognaam" || obj.val() == "gebruikersnaam"){
		obj.val("");
	}
}

function handleInstrTxt (id)
{
	// alert ("handleInstrTxt - "+$("#instruction"+id).val());
	if ($("#instruction"+id).val() == "instructietekst"){
		$("#instruction"+id).val("");
	}
}

function handlePw (obj)
{
	//if (obj.val() == "password"){
		obj.val("");
	//}
	obj.prop("type", "password");
}

function handleSearch (obj)
{
	// alert ("handle_user");
	if (obj.val() == "zoeken"){
		obj.val("");
	}
}

function pwSubmit()
{
	// alert ("pwSubmit: "+$("#user").val()+" - "+$("#pw").val());
	jQuery.getJSON("/api/login.php", {user: $("#user").val(), pw: $("#pw").val()}, function(result){
	  // alert ("result: "+result.login);
	  if (result.login=="super" || result.login=="admin" || result.login=="logopedist" || result.login=="patient"){
			deblur ($("#user").val());
			// alert ("role: "+result.login);
			checkUser ();
	  }else{
	  	$(".msg").text("login fout");
	  }
	});
	createCookie ("menu_item", 0);
}

function instrSubmit(id, name)
{
	// alert ("instrSubmit: "+$("#instruction"+id).val());
	jQuery.getJSON("/api/instrTxt.php", {id: id, text: $("#instruction"+id).val()}, function(result){
		if (result.query=="ok"){
			// alert ("instructietekst voor '"+name+"' opgeslagen.");
		}
	});
}

function nameSubmit(id)
{
	// alert ("instrSubmit: "+$("#instruction"+id).val());
	newname = $("#name"+id).val()
	jQuery.getJSON("/api/nameTxt.php", {id: id, name: newname}, function(result){
		if (result.query=="ok"){
			// alert ("nieuwe naam '"+newname+"' opgeslagen.");
		}
	});
}

function micOn ()
{
	// alert ("Mic on");
	$.getScript("js/simple_vu.js");
	$('#content').load('/content/patient/vu.php');
	$(".menu_item_text3").text("Microfoon aanzetten");
}

function createCookie(name,value,days) 
{
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}else{ 
		var expires = "";
	}
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) 
{
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' '){
			c = c.substring(1,c.length);
		}
		if (c.indexOf(nameEQ) == 0) {
			return c.substring(nameEQ.length,c.length);
		}
	}
	return null;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}

function urldecode(str) {
   return decodeURIComponent((str+'').replace(/\+/g, '%20'));
}