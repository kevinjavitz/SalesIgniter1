(function($, undefined) {

	$.extend($.ui.LayoutDesigner.prototype.tabs, {
		shadow: {
			init: function () {
				var $Tab = $('#mainTabPanel').find('#shadow');
				var parentCls = this;
				var thisCls = parentCls.tabs.shadow;

				var InputVals = parentCls.getElInputData();
				if (typeof InputVals.shadows == 'undefined'){
					InputVals.shadows = [];
					parentCls.setElInputData(InputVals);
				}else if (InputVals.shadows.length > 0){
					$.each(InputVals.shadows, function (k, v) {
						thisCls.createShadowConfig.apply(parentCls, [v]);
					});
				}

				$Tab.find('.addShadow').click(function () {
					thisCls.createShadowConfig.apply(parentCls);
				});
			},
			createShadowSliders: function ($el){
				var parentCls = this;
				var thisCls = parentCls.tabs.shadow;
				$el.find('input').each(function (){
					var $InputField = $(this);
					$el.find('.' + $(this).attr('name') + 'Slider').each(function (){
						$(this).slider({
							min: -100,
							max: 100,
							value: parseInt($InputField.val()),
							slide: function (e, ui){
								$InputField.val(ui.value);
								thisCls.processInputs.apply(parentCls);
							}
						});
						$InputField.data('sliderEl', $(this));
					});
				});
			},
			createShadowConfig: function (o) {
				var values = {
					shadow_offset_y: 2,
					shadow_offset_x: 2,
					shadow_blur: 5,
					shadow_spread: 0,
					shadow_color: '#000000',
					shadow_inset: false
				};
				$.extend(true, values, o || {});

				var $Tab = $('#mainTabPanel').find('#shadow');
				var parentCls = this;
				var thisCls = parentCls.tabs.shadow;

				var $newShadow = $('<tr>')
					.append('<td align="center" valign="top" style="width:150px"><input type="text" name="shadow_offset_y" size="2" value="2"><div class="shadow_offset_ySlider" style="margin-top:5px;"></div></td>')
					.append('<td align="center" valign="top" style="width:150px"><input type="text" name="shadow_offset_x" size="2" value="2"><div class="shadow_offset_xSlider" style="margin-top:5px;"></div></td>')
					.append('<td align="center" valign="top" style="width:150px"><input type="text" name="shadow_blur" size="2" value="1"><div class="shadow_blurSlider" style="margin-top:5px;"></div></td>')
					.append('<td align="center" valign="top" style="width:150px"><input type="text" name="shadow_spread" size="2" value="1"><div class="shadow_spreadSlider" style="margin-top:5px;"></div></td>')
					.append('<td valign="top"><input type="text" name="shadow_color" class="makeColorPicker" value="#000000" size="8"></td>')
					.append('<td valign="top"><input type="checkbox" name="shadow_inset"></td>')
					.append('<td valign="top"><span class="ui-icon ui-icon-closethick removeShadow"></span></td>');

				$Tab.find('.shadowConfigs tbody').append($newShadow);

				$newShadow.find('input[name=shadow_offset_y]')
					.val(values.shadow_offset_y)
					.keyup(function () { $(this).data('sliderEl').slider('value', $(this).val());thisCls.processInputs.apply(parentCls); });

				$newShadow.find('input[name=shadow_offset_x]')
					.val(values.shadow_offset_x)
					.keyup(function () { $(this).data('sliderEl').slider('value', $(this).val());thisCls.processInputs.apply(parentCls); });

				$newShadow.find('input[name=shadow_blur]')
					.val(values.shadow_blur)
					.keyup(function () { $(this).data('sliderEl').slider('value', $(this).val());thisCls.processInputs.apply(parentCls); });

				$newShadow.find('input[name=shadow_spread]')
					.val(values.shadow_spread)
					.keyup(function () { $(this).data('sliderEl').slider('value', $(this).val());thisCls.processInputs.apply(parentCls); });

				$newShadow.find('input[name=shadow_color]')
					.val(values.shadow_color)
					.keyup(function () { thisCls.processInputs.apply(parentCls); });

				$newShadow.find('input[name=shadow_inset]:checkbox')
					.click(function () { thisCls.processInputs.apply(parentCls); });
				if (values.shadow_inset){
					$newShadow.find('input[name=shadow_inset]:checkbox').attr('checked', 'checked');
				}else{
					$newShadow.find('input[name=shadow_inset]:checkbox').removeAttr('checked');
				}

				$newShadow.find('.makeColorPicker').ColorPicker({
					onChange: function (hsb, hex, rgb, el) {
						$(el).val('#' + hex);
						thisCls.processInputs.apply(parentCls);
					}
				});

				thisCls.createShadowSliders.apply(parentCls, [$newShadow]);

				$newShadow.find('.removeShadow')
					.click(function () { $(this).parentsUntil('tbody').remove();thisCls.processInputs.apply(parentCls); });

				thisCls.processInputs.apply(parentCls);
			},
			processInputs: function () {
				var $Tab = $('#mainTabPanel').find('#shadow');
				var parentCls = this;
				//var thisCls = parentCls.tabs.shadow;
				var shadows = [];
				var shadowsArr = [];

				$Tab.find('.shadowConfigs tbody tr').each(function () {
					var shadowOffsetY = $(this).find('input[name=shadow_offset_y]').val();
					var shadowOffsetX = $(this).find('input[name=shadow_offset_x]').val();
					var shadowBlur = $(this).find('input[name=shadow_blur]').val();
					var shadowSpread = $(this).find('input[name=shadow_spread]').val();
					var shadowColor = $(this).find('input[name=shadow_color]').val();
					var shadowInset = false;
					$(this).find('input[name=shadow_inset]:checkbox').each(function () {
						shadowInset = this.checked;
					});

					shadows.push({
						shadow_offset_y : shadowOffsetY,
						shadow_offset_x : shadowOffsetX,
						shadow_blur     : shadowBlur,
						shadow_spread   : shadowSpread,
						shadow_color    : shadowColor,
						shadow_inset    : shadowInset
					});

					shadowsArr.push(
						(shadowInset === true ? 'inset ' : '') +
							shadowOffsetY + 'px ' +
							shadowOffsetX + 'px ' +
							shadowBlur + 'px ' +
							shadowSpread + 'px ' +
							shadowColor);
				});

				var InputVals = parentCls.getElInputData();
				InputVals.shadows = shadows;
				parentCls.setElInputData(InputVals);

				this.updateStylesVal('box-shadow', shadowsArr.join(','));
				this.updateStylesVal('-webkit-box-shadow', shadowsArr.join(','));
				this.updateStylesVal('-moz-box-shadow', shadowsArr.join(','));
				this.updateStylesVal('behavior', 'url(/ext/ie_behave/PIE.htc)');
			}
		}
	});

})(jQuery);
