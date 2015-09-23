window.AudioContext    =  window.AudioContext || window.webkitAudioContext; // new
window.URL             =  window.URL || window.webkitURL; // new

var recorder; // new

function __log(e, data) {                         // new
  // log.innerHTML = "\n" + e + " " + (data || ''); // new
  log.innerHTML = e; // new
}                                                 // new

function getCookie(name) {
  var value = "; " + document.cookie;
  var parts = value.split("; " + name + "=");
  if (parts.length == 2) return parts.pop().split(";").shift();
}

  function startRecording(button) {
    recorder && recorder.record();
    button.disabled = true;
    button.nextElementSibling.disabled = false;
    __log('Opname...');
  }

  function stopRecording(button) {
    recorder && recorder.stop();
    button.disabled = true;
    button.previousElementSibling.disabled = false;
    // __log('Stopped recording.');
    __log('Even geduld svp, het resultaat verschijnt zo...');

    // create WAV download link using audio data blob
    createDownloadLink();
    
    recorder.clear();
  }

  function createDownloadLink() {
    recorder && recorder.exportWAV(function(blob) {
      var url = URL.createObjectURL(blob);
      var li = document.createElement('li');
      var au = document.createElement('audio');
      var hf = document.createElement('a');
      
      au.controls = true;
      au.src = url;
      //hf.href = url;
      //hf.download = new Date().toISOString() + '.wav';
      //hf.innerHTML = hf.download;
      //li.appendChild(au);
      //li.appendChild(hf);
      //recordingslist.appendChild(li);
    });
  }




(function(window){

  var WORKER_PATH = 'js/recWorker.js';

  var Recorder = function(source, cfg){
    var config = cfg || {};
    var bufferLen = config.bufferLen || 4096;
    var numChannels = config.numChannels || 1;
    this.context = source.context;
    this.node = (this.context.createScriptProcessor ||
                 this.context.createJavaScriptNode).call(this.context,
                 bufferLen, numChannels, numChannels);
    var worker = new Worker(config.workerPath || WORKER_PATH);
    worker.postMessage({
      command: 'init',
      config: {
        sampleRate: this.context.sampleRate,
        numChannels: numChannels
      }
    });
    var recording = false,
      currCallback;

		// mono
		this.node.onaudioprocess = function(e){
 			if (!recording) return;
			worker.postMessage({
				command: 'record',
				buffer: [e.inputBuffer.getChannelData(0)]
			});
		}

    this.configure = function(cfg){
      for (var prop in cfg){
        if (cfg.hasOwnProperty(prop)){
          config[prop] = cfg[prop];
        }
      }
    }

    this.record = function(){
      recording = true;
    }

    this.stop = function(){
      recording = false;
    }

    this.clear = function(){
      worker.postMessage({ command: 'clear' });
    }

    this.getBuffer = function(cb) {
      currCallback = cb || config.callback;
      worker.postMessage({ command: 'getBuffer' })
    }

    this.exportWAV = function(cb, type){
      currCallback = cb || config.callback;
      type = type || config.type || 'audio/wav';
      if (!currCallback) throw new Error('Callback not set');
      worker.postMessage({
        command: 'exportWAV',
        type: type
      });
    }

    worker.onmessage = function(e){
      var blob = e.data;
			uploadAudio(blob);
      currCallback(blob);
    }

    source.connect(this.node);
    this.node.connect(this.context.destination);    //this should not be necessary
  };

  Recorder.forceDownload = function(blob, filename){
    var url = (window.URL || window.webkitURL).createObjectURL(blob);
    var link = window.document.createElement('a');
    link.href = url;
    link.download = filename || 'output.wav';
    var click = document.createEvent("Event");
    click.initEvent("click", true, true);
    link.dispatchEvent(click);
  }

	function uploadAudio(wavData){
		var reader = new FileReader();
		reader.onload = function(event){
			var fd = new FormData();
			now = new Date();
			var dt = now.format("yyyymmdd_HHMMss");
			var userId = getCookie ("id");
			var usrdir = 'user_'+userId+'_'+dt;
			var wavName = encodeURIComponent(dt + '.wav');
			var mp4Name = 'rec/'+usrdir+'/' + encodeURIComponent(dt + '-error_analytic-discrete-continuous.mp4');
			console.log("wavname = " + wavName);
			fd.append('fname', wavName);
			fd.append('usrdir', usrdir);
			fd.append('module_id', moduleId);
			fd.append('audio_id', audioId);
			fd.append('user_id', userId);
			fd.append('data', event.target.result);
			$.ajax({
				type: 'POST',
				url: 'upload.php',
				data: fd,
				processData: false,
				contentType: false
			}).done(function(data) {
				console.log('data:');
				console.log(data);
				// log.innerHTML  = "\n" + data;
				// log.innerHTML += "<br/><br/>\n";
				log.innerHTML = "<video width=\"552\" height=\"300\" controls><source src=\""+mp4Name+"\" type=\"video/mp4\"></video>\n"; 
				document.getElementById('user_audio_id').value = data;
				// __log('');
			});
		};      
		reader.readAsDataURL(wavData);
	}

  window.Recorder = Recorder;

})(window);
