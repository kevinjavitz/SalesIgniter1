$(document).ready(function (){
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;

		$('.gridButtonBar').find('button').button('enable');
	});
	
	$('.gridButtonBar').find('.emailButton').click(function (){
		var getVars = [];
		getVars.push('app=coupons');
		getVars.push('appPage=default');
		getVars.push('action=getActionWindow');
		getVars.push('window=emailCoupon');
		getVars.push('cID=' + $('.gridBodyRow.state-active').attr('data-coupon_id'));

		gridWindow({
			buttonEl: this,
			gridEl: $('.gridContainer'),
			contentUrl: js_app_link(getVars.join('&')),
			onShow: function (){
				var self = this;
				
				$(self).find('.cancelButton').click(function (){
					$(self).effect('fade', {
						mode: 'hide'
					}, function (){
						$('.gridContainer').effect('fade', {
							mode: 'show'
						}, function (){
							$(self).remove();
						});
					});
				});
				
				$(self).find('.sendButton').click(function (){
					var getVars = [];
					getVars.push('app=coupons');
					getVars.push('appPage=default');
					getVars.push('action=sendCouponEmail');
					getVars.push('cID=' + $('.gridBodyRow.state-active').attr('data-coupon_id'));
					
					$.ajax({
						cache: false,
						url: js_app_link(getVars.join('&')),
						dataType: 'json',
						data: $(self).find('*').serialize(),
						type: 'post',
						success: function (data){
							if (data.success){
								alert(data.sentTo);
								$(self).effect('fade', {
									mode: 'hide'
								}, function (){
									$('.gridContainer').effect('fade', {
										mode: 'show'
									}, function (){
										$(self).remove();
									});
								});
							}
						}
					});
				});
			}
		});
	});
	
	$('.gridButtonBar').find('.insertButton, .editButton').click(function (){
		var getVars = [];
		getVars.push('app=coupons');
		getVars.push('appPage=default');
		getVars.push('action=getActionWindow');
		getVars.push('window=newCoupon');
		if ($(this).hasClass('editButton') && $('.gridBodyRow.state-active').size() > 0){
			getVars.push('cID=' + $('.gridBodyRow.state-active').attr('data-coupon_id'));
		}

		gridWindow({
			buttonEl: this,
			gridEl: $('.gridContainer'),
			contentUrl: js_app_link(getVars.join('&')),
			onShow: function (){
				var self = this;
				
				$(self).find('.cancelButton').click(function (){
					$(self).effect('fade', {
						mode: 'hide'
					}, function (){
						$('.gridContainer').effect('fade', {
							mode: 'show'
						}, function (){
							$(self).remove();
						});
					});
				});
				
				$(self).find('.saveButton').click(function (){
					var getVars = [];
					getVars.push('app=coupons');
					getVars.push('appPage=default');
					getVars.push('action=saveCoupon');
					if ($('.gridBodyRow.state-active').size() > 0){
						getVars.push('cID=' + $('.gridBodyRow.state-active').attr('data-coupon_id'));
					}
					
					$.ajax({
						cache: false,
						url: js_app_link(getVars.join('&')),
						dataType: 'json',
						data: $(self).find('*').serialize(),
						type: 'post',
						success: function (data){
							if (data.success){
								if ($('.gridBodyRow.state-active').size() > 0){
									$(self).effect('fade', {
										mode: 'hide'
									}, function (){
										$('.gridContainer').effect('fade', {
											mode: 'show'
										}, function (){
											$(self).remove();
										});
									});
								}else{
									js_redirect(js_app_link('app=coupons&appPage=default&cID=' + data.cID));
								}
							}
						}
					});
				});
				
				$('input[name=coupon_start_date]').datepicker({
					dateFormat: 'yy-mm-dd',
					defaultDate: '+0'
				});
				
				$('input[name=coupon_expire_date]').datepicker({
					dateFormat: 'yy-mm-dd',
					defaultDate: '+365'
				});
                $('.removeButton').click(function (e){
                    $(this).parent().remove();
                    return false;
                });
                $('#moveRightAddon').click(function (){
                    if ($('option:selected', $('#productListAddons')).size() > 0){

                        $('option:selected', $('#productListAddons')).each(function(){
                            var $selected = $(this);
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
                        var newHTML = $('<div><a href="Javascript:void()" class="ui-icon ui-icon-circle-close removeButton"></a><span class="main">' + productName + '</span><input type="hidden" name="products_excluded[]" value="' + productID + '"></div>');
                        newHTML.appendTo('#addons');
                        });
                        $('.removeButton', newHTML).click(function (e){
                            $(this).parent().remove();
                            return false;
                        });
                    }
                }).button();
			}
		});
	});
	
	$('.gridButtonBar').find('.deleteButton').click(function (){
		var couponId = $('.gridBodyRow.state-active').attr('data-coupon_id');
		confirmDialog({
			confirmUrl: js_app_link('app=coupons&appPage=default&action=deleteConfirm&cID=' + couponId),
			title: 'Confirm Delete',
			content: 'Are you sure you want to delete this coupon?',
			success: function (){
				js_redirect(js_app_link('app=coupons&appPage=default'));
			}
		});
	});
	
	$('.gridButtonBar').find('.reportButton').click(function (){
		var getVars = [];
		getVars.push('app=coupons');
		getVars.push('appPage=default');
		getVars.push('action=getActionWindow');
		getVars.push('window=report');
		getVars.push('cID=' + $('.gridBodyRow.state-active').attr('data-coupon_id'));

		gridWindow({
			buttonEl: this,
			gridEl: $('.gridContainer'),
			contentUrl: js_app_link(getVars.join('&')),
			onShow: function (){
				var self = this;
				
				$(self).find('.backButton').click(function (){
					$(self).effect('fade', {
						mode: 'hide'
					}, function (){
						$('.gridContainer').effect('fade', {
							mode: 'show'
						}, function (){
							$(self).remove();
						});
					});
				});
			}
		});
	});
});