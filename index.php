<?php
    function getUUID4() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0x0fff ) | 0x4000, mt_rand( 0, 0x3fff ) | 0x8000, mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    if(isset($_GET['eval']) && isset($_POST['code'])){
        $res = array();
        $fileName = "temp/" . getUUID4() . ".php";
        
        $code = base64_decode(str_replace(" ", "+", $_POST['code']));

        file_put_contents($fileName, $code);
        $output = file_get_contents($_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'] . str_replace(basename(__FILE__), "", $_SERVER['PHP_SELF']) . $fileName);
        //$res['code'] = $code;
        $res['output'] = (($output !== false) ? $output : "Error!");
        $res['status'] = (($output !== false) ? "ok" : "error");
        unlink($fileName);
        
        echo json_encode($res, true);
        exit();
    }
?>
<!DOCTYPE html>
<html>
    <head>
    	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    	<title>Y.E. Sandbox</title>
    	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<link rel="stylesheet" href="css/w3.css">
    	<link rel="stylesheet" href="css/index.css">

    	<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.6/ace.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.6/ext-language_tools.js"></script>
        <script src="js/FileSaver.js"></script>
    </head>
    <body>
        <ul class="w3-light-grey">
        	<li class="dropdown">
        		<button class="w3-button w3-bar-item" title="Show info" onclick='$Id("about").classList.toggle("show")' onblur='$Id("about").classList.remove("show")'>
        			<svg style="width: 1em;height: 1em;" viewBox="0 0 52 52" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg">
        				<rect height="6" rx="3" transform="translate(52 27.52) rotate(180)" width="6" x="23" y="10.76" />
        				<path d="M27,41.24a2,2,0,0,1-2-2v-13H23a2,2,0,0,1,0-4h4a2,2,0,0,1,2,2v15A2,2,0,0,1,27,41.24Z" />
        				<path d="M26,52A26,26,0,1,1,52,26,26,26,0,0,1,26,52ZM26,4A22,22,0,1,0,48,26,22,22,0,0,0,26,4Z" />
        			</svg>
        		</button>
        		<div class="dropdown-content" id="about">
        			<p><b>About:</b></p>
        			<p>Updated, adapted, and modified by Yehuda Eisenberg.</p>
        			<p>Github: <a href="https://github.com/YehudaEi/PHP-Sandbox">https://github.com/YehudaEi/PHP-Sandbox</a></p>
        			<p>Version: 1.0</p>
        			<p>PHP Version: <?php echo phpversion(); ?></p>
        		</div>
        	</li>
        	<li>
        		<button class="w3-button w3-bar-item w3-hover-text-green" onclick="restack()" title="Change Orientation">
        			<svg style="width: 1em;height: 1em;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" enable-background="new 0 0 512 512" xml:space="preserve">
        				<g>
        					<path d="M479.971,32.18c-21.72,21.211-42.89,43-64.52,64.301c-1.05,1.23-2.26-0.16-3.09-0.85c-24.511-23.98-54.58-42.281-87.221-52.84c-37.6-12.16-78.449-14.07-117.029-5.59c-68.67,14.67-128.811,64.059-156.44,128.609c0.031,0.014,0.062,0.025,0.093,0.039c-2.3,4.537-3.605,9.666-3.605,15.1c0,18.475,14.977,33.451,33.451,33.451c15.831,0,29.084-11.002,32.555-25.773c19.757-41.979,58.832-74.445,103.967-85.527c52.2-13.17,111.37,1.33,149.4,40.041c-22.03,21.83-44.391,43.34-66.33,65.26c59.52-0.32,119.06-0.141,178.59-0.09C480.291,149.611,479.931,90.891,479.971,32.18z" />
        					<path d="M431.609,297.5c-14.62,0-27.041,9.383-31.591,22.453c-0.009-0.004-0.019-0.008-0.027-0.012c-19.11,42.59-57.57,76.219-102.84,88.18c-52.799,14.311-113.45,0.299-152.179-39.051c21.92-21.76,44.369-43.01,66.189-64.869c-59.7,0.049-119.41,0.029-179.11,0.01c-0.14,58.6-0.159,117.189,0.011,175.789c21.92-21.91,43.75-43.91,65.79-65.699c14.109,13.789,29.76,26.07,46.92,35.869c54.739,31.971,123.399,38.602,183.299,17.891c57.477-19.297,106.073-63.178,131.212-118.318c3.645-5.357,5.776-11.824,5.776-18.793C465.06,312.477,450.083,297.5,431.609,297.5z" />
        				</g>
        			</svg>
        		</button>
        	</li>
        	<li>
        		<button class="w3-button w3-bar-item w3-green w3-hover-white w3-hover-text-green" onclick="loadSession()" title="Load file">
        			<svg style="width: 1em;height: 1em;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 490 490" style="enable-background:new 0 0 490 490;" xml:space="preserve">
        				<g>
        					<path d="M262.2,472.9V141.6l58.8,58.8c3.3,3.3,7.7,5,12.1,5c4.4,0,8.8-1.7,12.1-5c6.7-6.7,6.7-17.6,0-24.3L257.1,88c-6.4-6.4-17.8-6.4-24.3,0l-88.1,88.1c-6.7,6.7-6.7,17.6,0,24.3s17.6,6.7,24.3,0l58.8-58.8v331.2c0,9.5,7.7,17.2,17.1,17.2C254.6,490,262.2,482.4,262.2,472.9z" />
        					<path d="M28,17.1v99.7c0,9.5,7.7,17.2,17.1,17.2c9.5,0,17.2-7.7,17.2-17.2V34.3h365.5v82.6c0,9.5,7.7,17.2,17.1,17.2c9.4,0,17.1-7.7,17.1-17.2V17.1C462,7.6,454.3,0,444.9,0H45.2C35.7,0,28,7.6,28,17.1z" />
        				</g>
        			</svg>
        		</button>
        	</li>
        	<li>
        		<input id="sessionName" class="w3-bar-item w3-input" value="Session" onchange="loadSession()" title="Session name"></input>
        	</li>
        	<li>
        		<button class="w3-button w3-bar-item w3-green w3-hover-white w3-hover-text-green" onclick="downloadFile()" title="Download HTML">
        			<svg style="width: 1em;height: 1em;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 485 485" style="enable-background:new 0 0 485 485;" xml:space="preserve">
        				<g>
        					<path d="M426.5,458h-368C51,458,45,464,45,471.5S51,485,58.5,485h368c7.5,0,13.5-6,13.5-13.5S434,458,426.5,458z" />
        					<path d="M233,378.7c2.5,2.5,6,4,9.5,4s7-1.4,9.5-4l107.5-107.5c5.3-5.3,5.3-13.8,0-19.1c-5.3-5.3-13.8-5.3-19.1,0L256,336.5v-323C256,6,250,0,242.5,0S229,6,229,13.5v323l-84.4-84.4c-5.3-5.3-13.8-5.3-19.1,0s-5.3,13.8,0,19.1L233,378.7z" />
        				</g>
        			</svg>
        		</button>
        	</li>
        	<li>
        		<button class="w3-button w3-bar-item w3-green w3-hover-white w3-hover-text-green" onclick="viewSource();" title="View source">
        			<svg style="width: 1em;height: 1em;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 543.232 543.232" style="enable-background:new 0 0 543.232 543.232;" xml:space="preserve">
        				<g>
        					<path d="M85.972,416.447c5.838,9.139,15.716,14.133,25.814,14.133c5.637,0,11.347-1.555,16.444-4.816c14.241-9.102,18.409-28.023,9.309-42.26L66.915,272.953l70.631-110.54c9.1-14.241,4.933-33.158-9.309-42.258c-14.248-9.095-33.158-4.933-42.259,9.309L4.815,256.478c-6.42,10.043-6.42,22.907,0,32.95L85.972,416.447z" />
        					<path d="M415.002,425.756c5.104,3.264,10.808,4.816,16.444,4.816c10.092,0,19.976-4.986,25.813-14.131l81.158-127.014c6.42-10.043,6.42-22.907,0-32.95l-81.151-127.014c-9.095-14.248-28.03-18.416-42.259-9.309c-14.241,9.1-18.409,28.023-9.309,42.258l70.631,110.54l-70.637,110.545C396.593,397.732,400.761,416.656,415.002,425.756z" />
        					<path d="M165.667,492.6c4.272,2.043,8.776,3.018,13.213,3.018c11.401,0,22.35-6.402,27.613-17.375L391.979,91.452c7.307-15.239,0.881-33.519-14.357-40.82c-15.245-7.307-33.52-0.881-40.821,14.357L151.309,451.779C144.002,467.018,150.428,485.299,165.667,492.6z" />
        				</g>
        			</svg>
        		</button>
        	</li>
        	<li>
        		<button class="w3-button w3-green w3-hover-white w3-hover-text-green" onclick="loadFromLocalStorage()" title="Reload HTML from localStorage">Restore</button>
        	</li>
        	<li>
        		<button class="w3-button w3-bar-item w3-green w3-hover-white w3-hover-text-green" onclick="saveToLocalStorage()" title="Save HTML to localStorage">Store</button>
        	</li>
        	<li>
        		<button class="w3-button w3-bar-item w3-green w3-hover-white w3-hover-text-green" onclick="submitTryit(1)" title="Show HTML output">Run &raquo;</button>
        	</li>
        	<li>
        		<button class="w3-button w3-bar-item w3-green w3-hover-white w3-hover-text-green" onclick="reEdited()" title="Copy frame source to textarea">&laquo; Get</button>
        	</li>
        	<!--<li>
        		<label class="switch">
        			<input id="checkedit" checked="checked" type="checkbox" onchange="frameEditable()">Editable preview: <span class="slider"></span><span id="switchflag">ON</span>
        		</label>
        	</li>-->
        	<li style="float: right"><span class="w3-right w3-bar-item" style="padding: 9px 0;display: block;" id="framesize"></span>
        	</li>
        </ul>
      
        <div id="shield"></div>
    
        <div id="container">
        	<div id="textareacontainer">
        		<div id="CodeEditor" contenteditable="true" wrap="logical" spellcheck="false" style="height: 100%; width: 100;"><?php echo htmlspecialchars("<?php\n\techo \"Hello World\";"); ?></div>
        	</div>
        	<div id="dragbar"></div>
        	<div id="iframecontainer">
        		<iframe id="iframeResult"></iframe>
        	</div>
        </div>
        
        <script src="js/index.js"></script>
    </body>
</html>