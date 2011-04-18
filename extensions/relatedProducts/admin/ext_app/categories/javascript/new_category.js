$(document).ready(function (){
	$('.removeButton').click(function (e){
		$(this).parent().remove();
		return false;
	});
	
	$('#moveRight').click(function (){
		if ($('option:selected', $('#productList')).size() > 0){
			if ($('input[type="hidden"]', $('#related')).size() > 25){
				return false;
			}
			var $selected = $('option:selected', $('#productList'));
			var productID = $selected.val();
			var productName = $selected.html();

			var exists = false;
			$('input[type="hidden"]', $('#related')).each(function (){
				if ($(this).val() == productID){
					exists = true;
				}
			});

			if (exists == true){
				return false;
			}

			var newHTML = $('<div><a href="Javascript:void()" class="ui-icon ui-icon-circle-close removeButton"></a><span class="main">' + productName + '</span><input type="hidden" name="related_products[]" value="' + productID + '"></div>');
			newHTML.appendTo('#related');

			$('.removeButton', newHTML).click(function (e){
				$(this).parent().remove();
				return false;
			});
		}
	}).button();
});