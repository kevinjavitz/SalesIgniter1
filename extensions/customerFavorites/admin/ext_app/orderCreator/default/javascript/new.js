$(document).ready(function (){

	$('.custFavoritesPopup').click(function(){
		var $self = $('.custDialog');
		var $closeBut = $('<div style=""><a class="closeBut2" href="#"><span class="ui-icon ui-icon-closethick">close</span></a></div>');
		$closeBut.insertBefore($self);
		$.ajax({
			cache: false,
			dataType: 'json',
			type:'post',
			data: '',
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=getFavorites'),
			success: function (data) {
				$self.html(data.list);

				$('.closeBut2').click(function() {
					$self.html('');
					$self.hide();
					$closeBut.hide();
					return false;
				});


				$self.css('background-color', '#ffffff');
				$self.css('border', '1px solid #000000');
				$self.css('padding', '10px');
				$self.css('width', '600px');
				$self.css('position', 'absolute');
				var posi = $('.productSection').offset();
				$self.css('top', (posi.top - 200) + 'px');
				$self.css('left', (posi.left + 100) + 'px');

				$('.closeBut2').css('position', 'relative');
				$('.closeBut2').css('z-index', '1000');
				$('.closeBut2').css('left', '720px');
				$('.closeBut2').css('top', '-175px');
				$self.show();
				$self.focus();

				$('#removeFavorites').click(function(){
						$.ajax({
								cache: false,
								url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=deleteFavorites'),
								dataType: 'json',
								type: 'POST',
								data: $('.manageFavorites *').serialize(),
								success: function (data){
									alert('Favorites Removed');
									$('.closeBut2').trigger('click');
								}
						});
						return false;
				});

				$('#addCartFavorites').click(function(){
					var $selfFav = $('.selectDialogFavorites');
					$.ajax({
							cache: false,
							url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=addToCart'),
							dataType: 'json',
							type: 'POST',
							data: $('.manageFavorites *').serialize(),
							success: function (data){
								$selfFav.html(data.calendar).append(data.custFavoritesReservation);
								$('.productTable tbody').append(data.html);
								var $closeButFav = $('<div style=""><a class="closeBut3" href="#"><span class="ui-icon ui-icon-closethick">close</span></a></div>');
								$closeButFav.insertBefore($self);

								$('.closeBut3').click(function() {
									$selfFav.html('');
									$selfFav.hide();
									$closeButFav.hide();
									return false;
								});

								$('.inCart').live('click', function(event) {
									//showAjaxLoader($selfFav, 'large');

									//here I need to load productrow for every product

									$.ajax({
										cache: false,
										dataType: 'json',
										type:'post',
										data: $('.ocProductReservation').serialize()+ '&'+ $('.ocFavoritesSelect').serialize()+'&start_date=' + $selfFav.find('.start_date').val() + '&end_date=' + $selfFav.find('.end_date').val() + '&shipping=' + $selfFav.find('input[name="rental_shipping"]:checked').val() + '&qty=' + $selfFav.find('.rental_qty').val() + '&purchase_type=reservation',
										url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfoCF'),
										success: function (data) {
											//update priceEx

											jQuery.each(data.price, function(i, val) {
												var $tr = $('.productTable').find('tr[data-id='+i+']');
												var $price = $tr.find('.priceEx');
												$price.val(val).trigger('keyup');
												var $reservationPrice = $tr.find('.reservationDates');

												$reservationPrice.val($selfFav.find('.start_date').val()+','+$selfFav.find('.end_date').val());
											});
											$('.closeBut3').trigger('click');
										}
									});

									event.stopImmediatePropagation();
								});
								$selfFav.css('background-color', '#ffffff');
								$selfFav.css('border', '1px solid #000000');
								$selfFav.css('padding', '10px');
								$selfFav.css('width', '600px');
								$selfFav.css('position', 'absolute');
								$selfFav.css('z-index', '999');
								var posi = $('.productSection').offset();
								$selfFav.css('top', (posi.top -100) + 'px');
								$selfFav.css('left', (posi.left + 100) + 'px');

								$('.closeBut3').css('position', 'relative');
								$('.closeBut3').css('z-index', '1000');
								$('.closeBut3').css('left', '700px');
								$('.closeBut3').css('top', '-70px');
								$selfFav.show();
								$selfFav.focus();
							}
					});
					return false;
				});

			}
		})
	});

});