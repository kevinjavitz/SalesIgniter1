$(document).ready(function (){
	$('#page-2').tabs();
	$('#tab_container').tabs();
	$('#tab_container').bind('tabsshow', function(event, ui) {
		$('.makeFCK', ui.panel).each(function (){
			if ($(this).is(':hidden')) return;

			CKEDITOR.replace(this, {
				filebrowserBrowseUrl: DIR_WS_ADMIN + 'rentalwysiwyg/editor/filemanager/browser/default/browser.php'
			});
		});
	});
});