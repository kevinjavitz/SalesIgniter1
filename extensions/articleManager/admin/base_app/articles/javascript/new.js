$(document).ready(function (){
	$('.makeFCK').each(function (){
		CKEDITOR.replace(this, {
			filebrowserBrowseUrl: DIR_WS_ADMIN + 'rentalwysiwyg/editor/filemanager/browser/default/browser.php'
		});
	});
	$('.useDatepicker').datepicker();
	$('#page-2').tabs();
	$('#tab_container').tabs();
});