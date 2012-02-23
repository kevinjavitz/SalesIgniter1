function pageY(elem) {
	return elem.offsetParent ? (elem.offsetTop + pageY(elem.offsetParent)) : elem.offsetTop;
}

function resizeIframe(iframeName, iframeSrc) {
	/*var buffer = 10; //scroll bar buffer
	var height = window.innerHeight || document.body.clientHeight || document.documentElement.clientHeight;
	height -= pageY(document.getElementById(iframeName))+ buffer ;
	height = (height < 0) ? 0 : height;
	document.getElementById(iframeName).style.height = height + 'px';*/

}

function createIframe (iframeName, iframeSrc, className, iframeSWF) {
	var transport = new easyXDM.Socket({
		remote: iframeSrc,
		swf: iframeSWF,
		container: iframeName,
		onMessage: function(message, origin){
			this.container.getElementsByTagName("iframe")[0].style.width = "100%";
			this.container.getElementsByTagName("iframe")[0].scrolling = "no";
			this.container.getElementsByTagName("iframe")[0].allowtransparency = "true";
			this.container.getElementsByTagName("iframe")[0].border = "0";

			arrMessage = message.split(':');
			if(arrMessage[0] == 'height'){
				this.container.getElementsByTagName("iframe")[0].style.height = arrMessage[1] + "px";
			}
			if(arrMessage[0] == 'redirect'){
				if(arrMessage[1] != 'undefined'){
					window.location = decodeURIComponent(arrMessage[1]);
				}
			}
		}
	});
}



var SESJSLIBRARY = SESJSLIBRARY || (function(){
	var _args = {}; // private

	return {
		init : function(Args) {
			_args = Args;
			// some other initialising
		},
		createIframe : function() {
			createIframe(_args[0].name, _args[0].src, _args[0].name, _args[0].swf);
		}
	};
}());
