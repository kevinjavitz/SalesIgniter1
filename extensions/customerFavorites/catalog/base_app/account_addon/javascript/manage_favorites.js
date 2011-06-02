$(document).ready(function (){
	$('#removeFavorites').click(function(){
		$.ajax({
				cache: false,
				url: js_app_link('appExt=customerFavorites&app=account_addon&appPage=manage_favorites&action=deleteFavorites'),
				dataType: 'json',
				type: 'POST',
				data: $(this).closest('form').serialize(),
				success: function (data){
					if (data.success == true){
						js_redirect(data.redirect);
					}
				}
		});
		return false;
	});

	$('#addCartFavorites').click(function(){
		var $self = $('.selectDialog');
		$.ajax({
				cache: false,
				url: js_app_link('appExt=customerFavorites&app=account_addon&appPage=manage_favorites&action=addToCart'),
				dataType: 'json',
				type: 'POST',
				data: $(this).closest('form').serialize(),
				success: function (data){
					$self.html(data.calendar);
					$('.pprButttons').append(data.customerFavoritesReservation);
					var $closeBut = $('<div style=""><a class="closeBut" href="#"><span class="ui-icon ui-icon-closethick">close</span></a></div>');
					$closeBut.insertBefore($self);

					$('.closeBut').click(function() {
						$self.html('');
						$self.hide();
						$closeBut.hide();
						return false;
					});

					$('.inCart').live('click', function(event) {
						showAjaxLoader($self, 'large');

						//here I need to load productrow for every product

						$.ajax({
							cache: false,
							dataType: 'json',
							type:'post',
							data: $('.custReservation').serialize()+ '&start_date=' + $self.find('.start_date').val() + '&end_date=' + $self.find('.end_date').val() + '&shipping=' + $self.find('input[name="rental_shipping"]:checked').val() + '&qty=' + $self.find('.rental_qty').val() + '&purchase_type=reservation',
							url: js_app_link('appExt=customerFavorites&app=account_addon&appPage=manage_favorites&action=saveReservation' ),
							success: function (data) {
								//update priceEx
								$('.closeBut').trigger('click');
								js_redirect(js_app_link('app=shoppingCart&appPage=default'));
							}
						});

						event.stopImmediatePropagation();
					});
					$self.css('background-color', '#ffffff');
					$self.css('border', '1px solid #000000');
					$self.css('padding', '10px');
					$self.css('width', '600px');
					$self.css('position', 'absolute');
					$self.css('z-index', '999');
				 	var posi = $('form[name=manage_favorites]').offset();
					$self.css('top', (posi.top -100) + 'px');
					$self.css('left', (posi.left + 100) + 'px');

					$('.closeBut').css('position', 'relative');
					$('.closeBut').css('z-index', '1000');
					$('.closeBut').css('left', '600px');
					$('.closeBut').css('top', '-220px');
					$self.show();
					$self.focus();
				}
		});
		return false;
	});

});