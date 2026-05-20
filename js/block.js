//////////////////Block keyboard event//////////////////////////
document.onkeydown=function check(e) {
    var keynum;
    var keyCode;
	var target;
	var e = event || evt; // for trans-browser compatibility
    var keynum = e.which || e.keyCode;
	var tar=e.target || e.srcElement;
	// For Internet Explorer
   if (window.event) {
        keynum = e.keyCode;
        
    }
    // For Netscape/Firefox/Opera
    else if (e.which) {
        keynum = e.which;
    }
//List of special characters you want to restrict
	 //(keynum =="8") === (keychar =='backspace')
	 //(keynum =="13") === (keychar =='Enter')
	 //(keynum =="27") === (keychar =='ESC')
	 //(keynum =="116") === (keychar =='F5')
	 //(keynum =="191") === (keychar =='/')
	 //(keynum =="192") === (keychar =='`')
	 //(keynum =="220") === (keychar =='\')
	 //(keynum =="222") === (keychar ==''') or keychar =='"')
    
	if (keynum ==13) {	
 		alert("Enter is not allowed.");
		tar.value = "";
        return false;
    }else if (keynum == 27) {	
 		alert("ESC is not allowed.");
		tar.value = "";
        return false;
    }else if (keynum == 116) {	
 		alert("F5 is not allowed.");
		//tar.value = "";
        return false;
    }else if (keynum == 191 ) {	
 		alert("Backslash is not allowed.");
		tar.value = "";
        return false;
    }else if (keynum == 192 ) {	
 		alert("Special Charectors are not allowed.");
		tar.value = "";
        return false;
    }else if (keynum ==  220 ) {	
 		alert("Forward slash is not allowed.");
		tar.value = "";
        return false;
    }else if (keynum == 222 ) {	
 		alert("Quotes are not allowed.");
		tar.value = "";
        return false;
    }else {
        return true;
    }
}

//////////////////Block keyboard event//////////////////////////

////////////////////////Block mouse event///////////////////////

function disableRightClick( e )
{
 var evt = e || window.event;

 if( (evt.button && evt.button == 2) || ( evt.which && evt.which & 2) )
 {
  alert("Right-click is disabled.");
 }

}
document.onmousedown = disableRightClick;

// JavaScript Document
////////////////////////Block mouse event///////////////////////

////////////////////////Block Windows Event///////////////////////

window.onload=changeHashOnLoad;
	function changeHashOnLoad() {
     window.location.href += '#';
     setTimeout('changeHashAgain()', '50'); 
}function changeHashAgain() {
  window.location.href += '1';
}
 
var storedHash = window.location.hash;
window.setInterval(function () {
    if (window.location.hash != storedHash) {
         window.location.hash = storedHash;
    }
}, 50);




// JavaScript Document\
//function to block right click 
var message="Sorry!, Right-click has been disabled"; 
/////////////////////////////////// 
function clickIE() {if (document.all) {(message);return false;}} 
function clickNS(e) {if 
(document.layers||(document.getElementById&&!document.all)) { 
if (e.which==2||e.which==3) {(message);return false;}}} 
if (document.layers) 
{document.captureEvents(Event.MOUSEDOWN);document.onmousedown=clickNS;} 
else{document.onmouseup=clickNS;document.oncontextmenu=clickIE;} 
document.oncontextmenu=new Function("return false") 
var func=document.onkeydown;
////////////////////////Block Windows Event///////////////////////
