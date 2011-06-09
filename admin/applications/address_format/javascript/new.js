
$(document).ready(function (){

	$('#tab_container').tabs();

	$('.makeFCK').each(function (){
			CKEDITOR.replace(this, {
				filebrowserBrowseUrl: DIR_WS_ADMIN + 'rentalwysiwyg/editor/filemanager/browser/default/browser.php'
		});
	});

});