$(document).ready(function (){
	$('.addToFavorites').click(function(){
		$.ajax({
				cache: false,
				url: js_app_link('appExt=customerFavorites&app=account_addon&appPage=manage_favorites&action=addToFavorites'),
				dataType: 'json',
				type: 'POST',
				data: $(this).closest('form').serialize(),
				success: function (data){

					if (data.success == true){
						js_redirect(data.redirect);
					}else{
						if(data.redirect == 'no_attributes'){
							alert('You need to select an attribute');
						}else{
							alert('Product Already in Favorites');
						}
					}
				}
		});

		return false;
	});
});