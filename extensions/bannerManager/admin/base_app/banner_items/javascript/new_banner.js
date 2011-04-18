
$(document).ready(function (){
	$('#tab_container').tabs();
	makeTabsVertical('#tab_container');

    $('.useDatepicker').datepicker({
		dateFormat: 'yy-mm-dd'
	});
		$('.makeFCK').each(function (){
			CKEDITOR.replace(this, {
				filebrowserBrowseUrl: DIR_WS_ADMIN + 'rentalwysiwyg/editor/filemanager/browser/default/browser.php'
			});
		});

});