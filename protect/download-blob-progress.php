<?php 

  include "download2.php";
 ?>

<link rel="stylesheet" type="text/css" href="bootstrap-darkly.min.css">

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <p>
                <button id="download-button" class="btn btn-primary" style="display:none;">Download</button>
            </p>

            <p><a id="save-file"  style="display:none;">Save File</a></p>

            <!--p><span id="download-progress-percentual">Downloading <?php echo $dist["name"]; ?> ...</span></p-->
            <p><span id="progress-text">downloading...</span></p>
            <p>
                <div class="progress">
                  <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" id="progress" aria-valuemin="0" aria-valuemax="100">
                    
                  </div>
                </div>
            </p>

            <p>
                <button onclick="abort();" type="button" class="btn btn-danger"  style="display:none;">Abort</button>
            </p>

            <p><span id="download-progress-text"></span></p>
        </div>
    </div>
</div>

<style type="text/css">
    .container {
        position: relative;
        top: 30%;
    }
    .progress {
        height: 30px;
    }
    #progress {
        font-size: 16px;
    }
    p {
        text-align: center;
    }
</style>

<script type="text/javascript">
var fileName = "<?php echo $downBase.$downName; ?>";

var progress = document.getElementById("progress");
var progressText = document.getElementById("progress-text");
var downloadProgressText = document.getElementById("download-progress-text");

function abort() {
	request.abort();
}

document.querySelector('#download-button').addEventListener('click', function() {
                                                            
    var startTime = new Date().getTime();
                                                            
	request = new XMLHttpRequest();

	request.responseType = 'blob';
    request.open('get', fileName, true);
    request.send();
    
    request.onreadystatechange = function() {
    	if(this.readyState == 4 && this.status == 200) {
    		var obj = window.URL.createObjectURL(this.response);

    		document.getElementById('save-file').setAttribute('href', obj);
    		document.getElementById('save-file').setAttribute('download', "<?php echo $downName; ?>");
            document.getElementById('save-file').click();
            console.log('downloaded!@!!');


    		setTimeout(function() {
    			window.URL.revokeObjectURL(obj);
    		}, 60 * 1000);
    	}
    };
    
    request.onprogress = function(e) {
    	var percent_complete = (e.loaded / e.total)*100;
    	percent_complete = Math.floor(percent_complete);

    	var duration = ( new Date().getTime() - startTime ) / 1000;
    	var bps = e.loaded / duration;
        var kbps = bps / 1024;
        kbps = Math.floor(kbps);
        
        var time = (e.total - e.loaded) / bps;
        var seconds = time % 60;
        var minutes = time / 60;
        
        seconds = Math.floor(seconds);
        minutes = Math.floor(minutes);

        progress.setAttribute("aria-valuemax", e.total);
        progress.setAttribute("aria-valuenow", e.loaded);
        progress.style.width = percent_complete + "%";
        //progress.innerHTML = percent_complete + "%";

        progressText.innerHTML = percent_complete + "%";


    	downloadProgressText.innerHTML = kbps + " KB/s" + "<br>" + minutes + " min " + seconds + " sec remaining.";
    };
});


document.querySelector('#download-button').click();
</script>
