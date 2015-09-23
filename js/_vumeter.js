	// check if the default naming is enabled, if not use the chrome one.
	if (! window.AudioContext) {
		if (! window.webkitAudioContext) {
			alert('no audiocontext found');
		}
		window.AudioContext = window.webkitAudioContext;
	}
	var context = new AudioContext();
	var audioBuffer;
	var sourceNode;
	var splitter;
	var analyser, analyser2;
	var javascriptNode;
	var vu_max = 0;
	var vu_min = 160;
	var stopped = false;

	// get the context from the canvas to draw on
	var ctx = $("#canvas").get()[0].getContext("2d");

	// create a gradient for the fill. Note the strange
	// offset, since the gradient is calculated based on
	// the canvas, not the specific element we draw
	var gradient = ctx.createLinearGradient(0,0,0,200);
	gradient.addColorStop(1,'#bb0000');
	gradient.addColorStop(0.50,'#009900');
	//gradient.addColorStop(0.25,'#ffff00');
	gradient.addColorStop(0,'#ff8800');

	// load the sound
	setupAudioNodesMono();
	loadSound("test/rec/countDp.wav");
	// loadSound("test/wagner-short.ogg");

	function setupAudioNodesMono() {
		// setup a javascript node
		javascriptNode = context.createScriptProcessor(2048, 1, 1);
		// connect to destination, else it isn't called
		javascriptNode.connect(context.destination);

		// setup a analyzer
		analyser = context.createAnalyser();
		analyser.smoothingTimeConstant = 0.5;
		analyser.fftSize = 128;

		// create a buffer source node
		sourceNode = context.createBufferSource();

		// connect the source to the analyser
		sourceNode.connect(analyser);

		// we use the javascript node to draw at a specific interval.
		analyser.connect(javascriptNode);

		// and connect to destination, if you want audio
		sourceNode.connect(context.destination);
	}


	// load the specified sound
	function loadSound(url) {
		var request = new XMLHttpRequest();
		request.open('GET', url, true);
		request.responseType = 'arraybuffer';

		// When loaded decode the data
		request.onload = function() {
			// decode the data
			context.decodeAudioData(request.response, function(buffer) {
				// when the audio is decoded play the sound
				playSound(buffer);
			}, onError);
		}
		request.send();
	}


	function playSound(buffer) {
		sourceNode.buffer = buffer;
		sourceNode.start(0);
		sourceNode.onended = function() {
			// console.log('Your audio has finished playing');
			stopped = true;
		}	
	}

	// log if an error occurs
	function onError(e) {
		console.log(e);
	}

	// when the javascript node is called
	// we use information from the analyzer node
	// to draw the volume

	javascriptNode.onaudioprocess = function() {
		// get the average for the first channel
		if (stopped){
			// console.log('end playback');
			ctx.clearRect(0, 0, 60, 200);
			return;
		}

		var array =	new Uint8Array(analyser.frequencyBinCount);
		analyser.getByteFrequencyData(array);
		var average = getAverageVolume(array);

		// clear the current state
		ctx.clearRect(0, 0, 40, 100);

		// set the fill style
		ctx.fillStyle=gradient;

		// create the meters
		h = 200-average;
		if (h<0) h=0;
		ctx.fillRect(0,h/2,40,100);
		// console.log('volume: '+average);
		if (average > vu_max){
			vu_max = average;
			document.getElementById("vu_max").innerHTML = average;
		}else{
			vu_max -= .5;
		}
		ctx.fillRect(0,200-vu_max,40,1);
		if (average < vu_min){
			vu_min = average;
			document.getElementById("vu_min").innerHTML = average;
		}
	}

	function getAverageVolume(array) {
		var values = 0;
		var average;
		var length = array.length;

		if (stopped){
			return 0;
		}
		// get all the frequency amplitudes
		for (var i = 0; i < length; i++) {
			values += array[i];
		}

		average = values / length;
		return average;
	}

