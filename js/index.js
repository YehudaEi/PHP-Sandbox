var layout = "horizontal";
var leftwidthperc = 50 ; var leftheightperc = 50 ;
var framecontentedit = true;


function $Id(id) {return document.getElementById(id)};
function $Tag(tag) {return document.getElementsByTagName(tag)};

if (window.addEventListener) {
	window.addEventListener("resize", browserResize);
} else if (window.attachEvent) {
	window.attachEvent("onresize", browserResize);
}

function showFrameSize() {
	$Id("framesize").innerHTML = "Result Size: <span>" + $Id("iframecontainer")["clientWidth"] + " x " + $Id("iframecontainer")["clientHeight"] + "</span>";
}

function browserResize() {
	if (window.screen.availWidth <= 768) {
		restack(window.innerHeight > window.innerWidth);
	}
	showFrameSize();    
}

function submitTryit(n) {
	var text = window.editor.getSession().getValue().trim();

    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "?eval", true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var response = JSON.parse(this.responseText);
            if(response.status == "ok"){
                var ifr = $Id("iframeResult");

        		var ifrw = (ifr.contentWindow) ? ifr.contentWindow : (ifr.contentDocument.document) ? ifr.contentDocument.document : ifr.contentDocument;
        		ifrw.document.open();
        		ifrw.document.write(response.output);  
        		ifrw.document.close();
        		if (ifrw.document.body && !ifrw.document.body.isContentEditable) {
        			ifrw.document.body.contentEditable = true;
        			ifrw.document.body.contentEditable = false;
        		}
        		ifrw.document.body.contentEditable = framecontentedit;
            }
            else{
                alert("Error");
            }
        }
    };
    xhttp.send("code=" + btoa(text));
}

function reEdited() {
	var text = frameHTML();
    window.editor.getSession().setValue(text);
}

function dragBalance(balancer) {
	if (window.addEventListener) {
		balancer.addEventListener("mousedown", function(e) {dragstart(e);});
		balancer.addEventListener("touchstart", function(e) {dragstart(e);});
		window.addEventListener("mousemove", function(e) {dragmove(e);});
		window.addEventListener("touchmove", function(e) {dragmove(e);});
		window.addEventListener("mouseup", dragend);
		window.addEventListener("touchend", dragend);
	}

	var dragging = false;
	var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
	function dragstart(e) {
		e.preventDefault();
		e = e || window.event;
		// get the mouse cursor position at startup:
		pos3 = e.clientX;
		pos4 = e.clientY;
		dragging = true;
	}
	function dragmove(e) {
		var perc;
		if (dragging) {
			// show overlay to avoid interfering of mouse moving with textarea
			$Id("shield").style.display = "block";        
			e = e || window.event;
			// calculate the new cursor position:
			pos1 = pos3 - e.clientX;
			pos2 = pos4 - e.clientY;
			pos3 = e.clientX;
			pos4 = e.clientY;
			// set the element's new size:
			if (layout == "vertical") {
				var pos = pos2;
				var axe1 = "clientHeight";
				var axe2 = "height";
				perc = (balancer.previousElementSibling[axe1] + (balancer[axe1] / 2) - pos) * 100 / balancer.parentElement[axe1];
				leftheightperc = perc;
			} else {
				var pos = pos1;
				var axe1 = "clientWidth";
				var axe2 = "width";
				perc = (balancer.previousElementSibling[axe1] + (balancer[axe1] / 2) - pos) * 100 / balancer.parentElement[axe1];
				leftwidthperc = perc;
			}
			if (perc > 5 && perc < 95) {
				balancer.previousElementSibling.style[axe2] = "calc(" + (perc) + "% - " + (balancer[axe1] / 2) + "px)";
				balancer.nextElementSibling.style[axe2] = "calc(" + (100 - perc) + "% - " + (balancer[axe1] / 2) + "px)";
			}
			showFrameSize();
		}
	}
	function dragend() {
		$Id("shield").style.display = "none";
		dragging = false;
		if (window.editor) {
			//window.editor.refresh();
		}
	}
}

function restack() {
	var l = $Id("textareacontainer");
	var c = $Id("dragbar");
	var r = $Id("iframecontainer");
	if (layout == "vertical") {
		l.style["height"] = c.style["height"] = r.style["height"] = "100%";
		l.style["width"] = "calc(" + leftwidthperc + "% - 6px)";
		c.style["width"] = "12px";
		c.style["cursor"] = "col-resize";
		r.style["width"] = "calc(" + (100 - leftwidthperc) + "% - 6px)";
		layout = "horizontal"
	} else {
		l.style["width"] = c.style["width"] = r.style["width"] = "100%";
		l.style["height"] = "calc(" + leftheightperc + "% - 6px)";
		c.style["height"] = "12px";
		c.style["cursor"] = "row-resize";
		r.style["height"] = "calc(" + (100 - leftheightperc) + "% - 6px)";
		layout = "vertical"		
	}
	showFrameSize();
}

function keypressed(e) {
	if (e.key != "ArrowLeft" && e.key != "ArrowRight" && e.key != "ArrowUp" && e.key != "ArrowDown") {/*submitTryit(1)*/};
}
function keypressedinframe(e) {
	if (e.key != "ArrowLeft" && e.key != "ArrowRight" && e.key != "ArrowUp" && e.key != "ArrowDown") {reEdited()};
	setTimeout(reEdited,100);
}

function colorcoding() {
    ace.require("ace/ext/language_tools");
	var editor = ace.edit("CodeEditor");
	editor.getSession().setMode( {path:"ace/mode/php"/*, inline:true*/} );
    editor.setTheme("ace/theme/monokai"); //Dark Theme
    editor.setOptions({
        enableBasicAutocompletion: true,
        enableSnippets: true,
        enableLiveAutocompletion: true
    });
    editor.commands.addCommands([
        {
            name: 'save', bindKey: {win: 'Ctrl-S',  mac: 'Command-S'},
            exec: function(editor) { saveToLocalStorage(); }
        },
        {
            name: 'load', bindKey: {win: 'Ctrl-Shift-S',  mac: 'Command-Shift-S'},
            exec: function(editor) { loadFromLocalStorage(); }
        },
        {
            name: 'run', bindKey: {win: 'Ctrl-B',  mac: 'Command-B'},
            exec: function(editor) { submitTryit(); }
        },
    ]);
    window.editor = editor;
}

function frameWindow(){
	var ifr = $Id("iframeResult");
	var ifrw = (ifr.contentWindow) ? ifr.contentWindow : (ifr.contentDocument.document) ? ifr.contentDocument.document : ifr.contentDocument;
	return ifrw;
}
function frameHTML() {
	var ifrw = frameWindow();
	ifrw.document.body.removeAttribute("contentEditable");
	var text = "<!DOCTYPE html>\n<html>\n" + ifrw.document.documentElement.innerHTML.replace(/^\n+|\n+$/g,'').trim() + "\n</html>";
	ifrw.document.body.contentEditable = framecontentedit;
	return text;
}
function getCode() {
	var text = window.editor.getSession().getValue();
	return text;
}
function loadSession() {
	var name = $Id("sessionName").value == "" ? "Session" : $Id("sessionName").value;
	loadFromLocalStorage();
}
function getName() {
	var name = $Id("sessionName").value == "" ? "Session" : $Id("sessionName").value;
	return name = name.slice(name.lastIndexOf("/") + 1);		
}
function downloadFile() {
	var text = getCode();
	var blob = new Blob([text], {type: "text/html;charset=utf-8"});
	saveAs(blob, getName() + ".php");
}
function loadFromLocalStorage() {
	var text = localStorage.getItem(getName());
	if (text != null) {
		window.editor.getSession().setValue(text);
		submitTryit();
	}
}
function saveToLocalStorage() {
	if (typeof(Storage) !== "undefined") {
		var sHTML = getCode();
		localStorage.setItem(getName(), sHTML);
		alert('Saved Successfully');
	} else {
		alert("No localStorage available")
	}
}
function viewSource() {
	var source = getCode();
	source = source.replace(/</g, "&lt;");
	source = "<pre>" + source + "</pre>";
	var sourceWindow = window.open('Nice Title','Source of page','');
	sourceWindow.document.write(source);
	sourceWindow.document.close();
}
function frameEditable() {
	$Id("checkedit").value = ~ $Id("checkedit").value;
	if ($Id("checkedit").value == 0) {
		framecontentedit = true;
		$Id("switchflag").innerHTML = "ON";
	} else {
		framecontentedit = false;
		$Id("switchflag").innerHTML = "OFF";
	}
	submitTryit();
	reEdited();
}


if ((window.screen.availWidth <= 768 && window.innerHeight > window.innerWidth) ) {restack();}

colorcoding();
loadFromLocalStorage();
submitTryit();

dragBalance($Id(("dragbar")));

if (window.addEventListener) {
	window.addEventListener("load", showFrameSize);
	$Id("textareacontainer").addEventListener("keyup", function(e) {keypressed(e);});
}

frameWindow().addEventListener("keyup", keypressedinframe);
