$(document).ready(function (){
	$('#storeTabs').tabs();
	makeTabsVertical('#storeTabs');
	$('.removeButton').click(function (e){
		$(this).parent().remove();
		return false;
	});

	$('#moveRight').click(function (){
		if ($('option:selected').val() != ''){
			var $selected = $('option:selected', $('#countryList'));
			var productID = $selected.val();
			var productName = $selected.html();

			var exists = false;
			$('input[type="hidden"]', $('#countries')).each(function (){
				if ($(this).val() == productID){
					exists = true;
				}
			});

			if (exists == true){
				return false;
			}

			var newHTML = $('<div><a href="Javascript:void()" class="ui-icon ui-icon-circle-close removeButton"></a><span class="main">' + productName + '</span><input type="hidden" name="stores_countries[]" value="' + productID + '"></div>');
			newHTML.appendTo('#countries');

			$('.removeButton', newHTML).click(function (e){
				$(this).parent().remove();
				return false;
			});
		}
	}).button();
	$('.makeFCK').ckeditor(function (){
	}, {
		//filebrowserBrowseUrl: DIR_WS_ADMIN + 'rentalwysiwyg/editor/filemanager/browser/default/browser.php'
		filebrowserBrowseUrl: DIR_WS_ADMIN + 'rental_wysiwyg/filemanager/index.php'
	});
});