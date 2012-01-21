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
		var $barcodeName = $Row.find('.barcodeName').attr('barid');
		productsID = $Row.attr('data-id');
		var attrParams = 'pID=' + $Row.attr('data-id')+'&barcode='+$barcodeName;
		if ($AttrInput) {
			attrParams = attrParams + '&id[reservation][' + $AttrInput.attr('attrval') + ']=' + $AttrInput.val();
		}
		var $closeBut = $('<div style=""><a class="closeBut" href="#"><span class="ui-icon ui-icon-closethick">close</span></a></div>');
		$closeBut.insertBefore($self);
		showAjaxLoader($selfInput, 'small');
		$.ajax({
			cache: false,
			dataType: 'json',
			type:'post',
			data: attrParams,
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=loadReservationData'),
			success: function (data) {
				$self.html(data.calendar);

				$('.closeBut').click(function() {
					removeAjaxLoader($selfInput);
					$self.html('');
					$self.hide();
					$(this).hide();
					return false;
				});

				$('.inCart').live('click', function(event) {
					showAjaxLoader($self, 'large');
					var insVal = -1;
					if($self.find('.hasInsurance').attr('checked') == true){
						insVal = 1;
					}
					$.ajax({
						cache: false,
						dataType: 'json',
						type:'post',
						data: attrParams,
						url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo&id=' + $Row.attr('data-id') + '&start_date=' + $self.find('.start_date').val() + '&end_date=' + $self.find('.end_date').val() + '&shipping=' + $self.find('input[name="rental_shipping"]:checked').val() + '&qty=' + $self.find('.rental_qty').val() + '&purchase_type=' + $purchaseTypeSelected.val()+ '&hasInsurance=' + insVal),
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
							removeAjaxLoader($self);
							$('.closeBut').trigger('click');
						}
					});
					event.stopImmediatePropagation();
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
				$('.closeBut').css('z-index', '1000');
				$('.closeBut').css('left', '800px');
				$('.closeBut').css('top', '-120px');
				$self.show();
				$self.focus();

			}
		})

	});


	$('.productAttribute').live('change', function (){
        var $Row = $(this).parent().parent().parent().parent();
		$Row.find('.reservationDates').val('');
	});


	$('.eventf, .reservationShipping, .gatef').live('change', function(){
		var $self = $(this);
		var $Row = $(this).parent().parent().parent().parent();
		var $ShippingInput = $Row.find('.reservationShipping option:selected');
		var $qtyInput = $Row.find('.productQty');
		var selectedQty = $qtyInput.val();

        var $purchaseTypeSelected = $Row.find('.purchaseType option:selected');
		var eventS = $Row.find('.eventf option:selected');
		var gateS = $Row.find('.gatef option:selected');
		showAjaxLoader($self, 'x-large');
		$.ajax({
			cache: false,
			dataType: 'json',
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo&id=' + $Row.attr('data-id')+ '&event=' + eventS.val()+ '&gate=' + gateS.val() + '&shipping=' + $ShippingInput.val() + '&qty=' + selectedQty+'&purchase_type='+$purchaseTypeSelected.val()+'&days_before='+$ShippingInput.attr('days_before')+'&days_after='+$ShippingInput.attr('days_after')),
			success: function (data) {

				if(data.success == true){
					$Row.find('.priceEx').val(data.price).trigger('keyup');
				}else{
					$Row.find('.eventf').val('0');
					alert('There is no available item for the selected event. Make your Selection again.');
				}
				removeAjaxLoader($self);
			}
		});
	});

});