$(document).ready(function (){
	$('.deleteIconGallery').live('click', function (){
		$(this).parent().parent().remove();
	});

	$('.BrowseServerField').live('click focusout', function (e) {
		if (e.type == 'click'){
			browserField = $(this);
			currentFolder = 'images';
			window.open(
				DIR_WS_ADMIN + 'rental_wysiwyg/filemanager/index.php',
				"myWindow",
				"status = 1, height = 600, width = 800, resizable = 1"
				);
		}
	});

	$('.makeCommentFCK').each(function (){
		$(this).data('editorInstance', CKEDITOR.replace(this, {
			filebrowserBrowseUrl: DIR_WS_ADMIN + 'rentalwysiwyg/editor/filemanager/browser/default/browser.php'
		}));
	});

    $(this).find('.insertIconGallery').click(function () {
        var nextId = $(this).parent().parent().parent().parent().parent().attr('data-next_id');
        var langId = $(this).parent().parent().parent().parent().parent().attr('language_id');
        $(this).parent().parent().parent().parent().parent().attr('data-next_id', parseInt(nextId) + 1);
        var $td2 = $('<div style="float:left;width:280px;"></div>').attr('align', 'center').append('<input class="ui-widget-content BrowseServerField" type="text" name="gallery[' + nextId + '][image]">');
        var $td5 = $('<div style="float:left;width:480px;"></div>').attr('align', 'center').append('<textarea class="ui-widget-content makeCommentFCK" rows="10" cols="20" wrap="soft" name="gallery[' + nextId + '][comments]"></textarea>');
        var $td9 = $('<div style="float:left;width:40px;"></div>').attr('align', 'center').append('<a class="ui-icon ui-icon-closethick deleteIconGallery"></a>');
        var $newTr = $('<li style="list-style:none"></li>').append($td2).append($td5).append($td9).append('<br style="clear:both;"/>');//<input type="hidden" name="sortvprice[]">
        $(this).parent().parent().parent().parent().parent().find('.galleryList').append($newTr);
	    $('.BrowseServerField').live('click focusout', function (e) {
			if (e.type == 'click'){
				browserField = $(this);
				currentFolder = 'images';
				window.open(
					DIR_WS_ADMIN + 'rental_wysiwyg/filemanager/index.php',
					"myWindow",
					"status = 1, height = 600, width = 800, resizable = 1"
					);
			}
		});



		$('.makeCommentFCK').each(function (){
			if($(this).attr('name') == 'gallery[' + nextId + '][comments]'){
				$(this).data('editorInstance', CKEDITOR.replace(this, {
					filebrowserBrowseUrl: DIR_WS_ADMIN + 'rentalwysiwyg/editor/filemanager/browser/default/browser.php'
				}));
			}
		});

    });
});