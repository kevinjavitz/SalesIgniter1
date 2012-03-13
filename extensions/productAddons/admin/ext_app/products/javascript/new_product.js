$(document).ready(function (){
	$('.removeButton').click(function (e){
		$(this).parent().remove();
		return false;
	});

	$('#moveRightAddon').click(function (){
		if ($('option:selected', $('#productListAddons')).size() > 0){
			if ($('input[type="hidden"]', $('#addons')).size() > 25){
				return false;
			}
			var $selected = $('option:selected', $('#productListAddons'));
			var productID = $selected.val();
			var productName = $selected.html();

			var exists = false;
			$('input[type="hidden"]', $('#addons')).each(function (){
				if ($(this).val() == productID){
					exists = true;
				}
			});

			if (exists == true){
				return false;
			}

			var newHTML = $('<div><a href="Javascript:void()" class="ui-icon ui-icon-circle-close removeButton"></a><span class="main">' + productName + '</span><input type="hidden" name="addon_products[]" value="' + productID + '"></div>');
			newHTML.appendTo('#addons');

			$('.removeButton', newHTML).click(function (e){
				$(this).parent().remove();
				return false;
			});
		}
	}).button();

	$('#moveOptionalRightAddon').click(function (){
		if ($('option:selected', $('#productListOptional')).size() > 0){
			if ($('input[type="hidden"]', $('#optionaladdons')).size() > 25){
				return false;
			}
			var $selected = $('option:selected', $('#productListOptional'));
			var productID = $selected.val();
			var productName = $selected.html();

			var exists = false;
			$('input[type="hidden"]', $('#optionaladdons')).each(function (){
				if ($(this).val() == productID){
					exists = true;
				}
			});

			if (exists == true){
				return false;
			}

			var newHTML = $('<div><a href="Javascript:void()" class="ui-icon ui-icon-circle-close removeButton"></a><span class="main">' + productName + '</span><input type="hidden" name="optional_addon_products[]" value="' + productID + '"></div>');
			newHTML.appendTo('#optionaladdons');

			$('.removeButton', newHTML).click(function (e){
				$(this).parent().remove();
				return false;
			});
		}
	}).button();
});