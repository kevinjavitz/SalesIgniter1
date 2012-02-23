<!doctype html>
<html>
<head>
	<title>easyXDM</title>
	<script type="text/javascript" src="../js/easyXDM.min.js"></script>
	<script type="text/javascript">
		var iframe;
		var socket = new easyXDM.Socket({
			swf : "../js/easyxdm.swf",
			onReady : function() {
				iframe = document.createElement("iframe");
				iframe.width="100%";
				iframe.align="left";
				iframe.scrolling="no";
				iframe.frameBorder="0";
				iframe.allowtransparency="true";
				iframe.border="0";
				iframe.marginWidth ="0";
				iframe.marginHeight ="0";

				document.body.appendChild(iframe);

				iframe.src = easyXDM.query.url;

				var timer;
				iframe.onload = function() {
					var d = iframe.contentWindow.document;
					var originalHeight = d.body.clientHeight || d.body.offsetHeight || d.body.scrollHeight;

					if(!timer) {
						timer = setInterval(function() {
							try {
								var d = iframe.contentWindow.document;
								var newHeight = d.body.clientHeight || d.body.offsetHeight || d.body.scrollHeight;
								if(newHeight != originalHeight) {
									// The height has changed since last we checked
									originalHeight = newHeight;
									socket.postMessage('height:'+originalHeight);
									if(iframe.contentWindow['myRedirect'] != ''){
										socket.postMessage('redirect:'+encodeURIComponent(myRedirect));
									}
								}
							} catch(e) {
								// We tried to read the property at some point when it wasn't available
							}
						}, 300);
					}
					// Send the first message
					socket.postMessage('height:'+originalHeight);
					if(iframe.contentWindow['myRedirect'] != ''){
						//alert(myRedirect);
						socket.postMessage('redirect:'+encodeURIComponent(iframe.contentWindow['myRedirect']));
					}
				};
			},
			onMessage : function(url, origin) {
				iframe.src = url;
			}
		});

	</script>
	<style type="text/css">
		html, body {
			overflow: hidden;
			margin: 0px;
			padding: 0px;
			width: 100%;
			height: 100%;
		}
		iframe {
			width: 100%;
			height: 100%;
			border: 0px;
		}
	</style>
</head>
<body></body>
</html>