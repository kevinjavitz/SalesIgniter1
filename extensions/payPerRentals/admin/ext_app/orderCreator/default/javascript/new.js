$(document).ready(function (){
	var dayShortNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
	var allowSelection = true;
	var productsID = 0;
	var selected = '';
	var autoChanged = false;

	$('.reservationDates').live('click', function (){
        var $Row = $(this).parent().parent().parent().parent();
        var $self = $Row.find('.selectDialog');
		$self.html('');
        var $selfInput = $(this);
		var $AttrInput = $Row.find('.productAttribute');
		var $qtyInput = $Row.find('.productQty');
		var $purchaseTypeSelected = $Row.find('.purchaseType option:selected');

		productsID = $Row.attr('data-id');
		var attrParams = 'pID=' + $Row.attr('data-id');
		if ($AttrInput) {
			attrParams = attrParams + '&id[reservation][' + $AttrInput.attr('attrval') + ']=' + $AttrInput.val();
		}

		showAjaxLoader($selfInput, 'small');
		$.ajax({
			cache: false,
			dataType: 'json',
			type:'post',
			data: attrParams,
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=loadReservationData'),
			success: function (data) {
				var $closeBut = $('<div><a class="closeBut" href="#"><span class="ui-icon ui-icon-closethick">close</span></a></div>');
				$self.html($closeBut.html() + data.calendar);

				$('.closeBut').click(function() {
					$self.html('');
					$self.hide();
					hideAjaxLoader($selfInput);
					return false;
				});

				$('.inCart').live('click', function() {
					showAjaxLoader($self, 'large');
					$.ajax({
						cache: false,
						dataType: 'json',
						type:'post',
						data: attrParams,
						url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo&id=' + $Row.attr('data-id') + '&start_date=' + $self.find('.start_date').val() + '&end_date=' + $self.find('.end_date').val() + '&shipping=' + $self.find('input[name="rental_shipping"]:checked').val() + '&qty=' + $self.find('.rental_qty').val() + '&purchase_type=' + $purchaseTypeSelected.val()),
						success: function (data) {
							//update priceEx

							$selfInput.val($self.find('.start_date').val() + ',' + $self.find('.end_date').val());
							$qtyInput.val($self.find('.rental_qty').val());
							var $shippingInput = $Row.find('.reservationShipping');
							var $shippingText = $Row.find('.reservationShippingText');
							var $shipRadio = $self.find('input[name="rental_shipping"]:checked');
							if ($shipRadio.size() > 0) {
								var valShip = $shipRadio.val().split('_');
								$shippingInput.val(valShip[1]);
								$shippingText.html($shipRadio.parent().parent().find('td:eq(0)').html());
							}
							$Row.find('.priceEx').val(data.price).trigger('keyup');
							hideAjaxLoader($self);
							$self.hide();
							hideAjaxLoader($selfInput);
						}
					});
				});

				$self.css('background-color', '#ffffff');
				$self.css('border', '1px solid #000000');
				$self.css('padding', '10px');
				$self.css('width', '600px');
				$self.css('position', 'absolute');
				var posi = $selfInput.offset();
				$self.css('top', (posi.top - 200) + 'px');
				$self.css('left', (posi.left + 100) + 'px');

				$('.closeBut').css('position', 'relative');
				$('.closeBut').css('left', '570px');
				$('.closeBut').css('top', '10px');
				$self.show();
				$self.focus();

			}
		})

	});


	$('.productAttribute').live('change', function (){
        var $Row = $(this).parent().parent().parent().parent();
		$Row.find('.reservationDates').val('');
	});


	$('.eventf').live('change', function(){
		var $self = $(this);
		var $Row = $(this).parent().parent().parent().parent();
		var $ShippingInput = $Row.find('.reservationShipping option:selected');
		var $qtyInput = $Row.find('.productQty');
		var selectedQty = $qtyInput.val();

        var $purchaseTypeSelected = $Row.find('.purchaseType option:selected');
		var eventS = $Row.find('.eventf option:selected');
		showAjaxLoader($self, 'x-large');
		$.ajax({
			cache: false,
			dataType: 'json',
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo&id=' + $Row.attr('data-id')+ '&event=' + eventS.val() + '&shipping=' + $ShippingInput.val() + '&qty=' + selectedQty+'&purchase_type='+$purchaseTypeSelected.val()),
			success: function (data) {

				if(data.success == true){
					$Row.find('.priceEx').val(data.price).trigger('keyup');
				}else{
					alert('There is no available item for the selected event. Make your Selection again.');
				}
				removeAjaxLoader($self);
			}
		});
	});

});