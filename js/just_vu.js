	var vu_max = 0;
	// audioContext = new AudioContext();
	// analyser = audioContext.createAnalyser();
	javascriptNode = audioContext.createScriptProcessor(2048, 1, 1);

	analyser.smoothingTimeConstant = 0.5;
	analyser.fftSize = 128;

	analyser.connect(javascriptNode);
	javascriptNode.connect(audioContext.destination);

	canvasContext = document.getElementById("canvas");
	try {
		canvasContext= canvasContext.getContext("2d");
		// fancy gradient for vu meter
		var gradient = canvasContext.createLinearGradient(0,0,0,100);
		gradient.addColorStop(1,'#bb0000');
		gradient.addColorStop(0.50,'#009900');
		gradient.addColorStop(0,'#ff8800');

		javascriptNode.onaudioprocess = function() {
			var array =	new Uint8Array(analyser.frequencyBinCount);
			analyser.getByteFrequencyData(array);
			var average = getAverageVolume(array);
 			h = 100-average;
			if (h<0) h=0;
			canvasContext.clearRect(0, 0, 20, 100);
			canvasContext.fillStyle = gradient;
			canvasContext.fillRect(0,h,20,100);
			if (average > vu_max){
				vu_max = average;
			document.getElementById("vu_max").innerHTML = Math.round(vu_max);
			}
			// just for the top line:
			if (average > vu_top){
				vu_top = average;
				vu_to  = vu_timeout;
				vu_decr = .05;
			}else{
				if (vu_to < 0){
					vu_top -= vu_decr;
					vu_decr += .05;
				}else{
					vu_to--;
				}
			}
			canvasContext.fillRect(0,100-vu_top,20,2);
		}
	}catch (err){
		console.log('No canvasContext found');
	}
