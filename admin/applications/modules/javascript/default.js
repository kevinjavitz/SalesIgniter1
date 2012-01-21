
$(document).ready(function (){
	$('.makeModFCK').each(function (){
			CKEDITOR.replace(this, {
				toolbar :
				       [
					               ['Cut','Copy','Paste','PasteText','PasteFromWord','-'],
					               ['Undo','Redo','-'],
					               ['Image','Table','SpecialChar','PageBreak'],
					               '/',
					               ['Styles','Format'],
					               ['Bold','Italic','Strike'],
					               ['NumberedList','BulletedList','-'],
					               ['Link','Unlink','Anchor']


				       ],

				filebrowserBrowseUrl: DIR_WS_ADMIN + 'rentalwysiwyg/editor/filemanager/browser/default/browser.php'
			});
		});
});