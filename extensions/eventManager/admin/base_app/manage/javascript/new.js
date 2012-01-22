
$(document).ready(function (){
	$('#tab_container').tabs();
	$('#page-2').tabs();
	$('#events_start_date').datepicker({dateFormat: 'yy-mm-dd'});
	$('#events_end_date').datepicker({dateFormat: 'yy-mm-dd'});
	$('.makeFCK').each(function (){
		CKEDITOR.replace(this, {
			filebrowserBrowseUrl: DIR_WS_ADMIN + 'rentalwysiwyg/editor/filemanager/browser/default/browser.php'
		});
	});

});