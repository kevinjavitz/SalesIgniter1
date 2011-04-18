/*
	Info Pages Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
function disableIt(a){
	document.getElementById(a).disabled=true;
}

function enableIt(a){
	document.getElementById(a).disabled=false;
}

$(document).ready(function (){
	$('.makeFCK').ckeditor(function (){
	}, {
		filebrowserBrowseUrl: DIR_WS_ADMIN + 'rentalwysiwyg/editor/filemanager/browser/default/browser.php'
	});
	
	$('#languageTabs').tabs();
});