(function($, undefined) {

	$.extend($.ui.LayoutDesigner.prototype.tabs, {
		border: {
			init: function () {
				var $Tab = $('#mainTabPanel').find('#border');
				var parentCls = this;
				var thisCls = parentCls.tabs.border;
				var InputVals = parentCls.getElInputData();

				var values = {
					border: {
						left: {
							width: '0',
							width_unit: 'px',
							color: '#000000',
							style: 'solid'
						},
						top: {
							width: '0',
							width_unit: 'px',
							color: '#000000',
							style: 'solid'
						},
						right: {
							width: '0',
							width_unit: 'px',
							color: '#000000',
							style: 'solid'
						},
						bottom: {
							width: '0',
							width_unit: 'px',
							color: '#000000',
							style: 'solid'
						}
					},
					border_radius: {
						border_top_left_radius: '0',
						border_top_right_radius: '0',
						border_bottom_left_radius: '0',
						border_bottom_right_radius: '0',
						border_top_left_radius_unit: 'px',
						border_top_right_radius_unit: 'px',
						border_bottom_left_radius_unit: 'px',
						border_bottom_right_radius_unit: 'px'
					}
				};

				$.extend(true, values, InputVals);

				$.each(['top', 'bottom', 'left', 'right'], function (k, pos) {
					$Tab.find('input[name=border_' + pos + '_width]')
						.val(values.border[pos].width)
						.keyup(function () { thisCls.processInputs.apply(parentCls); });

					$Tab.find('select[name=border_' + pos + '_width_unit]')
						.val(values.border[pos].width_unit)
						.change(function () { thisCls.processInputs.apply(parentCls); });

					$Tab.find('input[name=border_' + pos + '_color]')
						.val(values.border[pos].color)
						.keyup(function () { thisCls.processInputs.apply(parentCls); })
						.bind('onChange', function (){ thisCls.processInputs.apply(parentCls); });

					$Tab.find('select[name=border_' + pos + '_style]')
						.val(values.border[pos].style)
						.change(function () { thisCls.processInputs.apply(parentCls); });
				});

				$.each(['top_left', 'top_right', 'bottom_left', 'bottom_right'], function (k, pos) {
					$Tab.find('input[name=border_' + pos + '_radius]')
						.val(values.border_radius['border_' + pos + '_radius'])
						.keyup(function () { thisCls.processInputs.apply(parentCls); });

					$Tab.find('select[name=border_' + pos + '_radius_unit]')
						.val(values.border_radius['border_' + pos + '_radius_unit'])
						.change(function () { thisCls.processInputs.apply(parentCls); });
				});
			},
			processInputs: function () {
				var $Tab = $('#mainTabPanel').find('#border');
				var thisCls = this;

				var ActiveEl = thisCls.getCurrentElement();

				var borderData = {
					top: {},
					right: {},
					bottom: {},
					left: {}
				};
				$.each(['top', 'bottom', 'left', 'right'], function (k, pos) {
					var widthVal = $Tab.find('input[name=border_' + pos + '_width]').val();
					var widthUnitVal = $Tab.find('select[name=border_' + pos + '_width_unit]').val();
					var colorVal = $Tab.find('input[name=border_' + pos + '_color]').val();
					var styleVal = $Tab.find('select[name=border_' + pos + '_style]').val();

					borderData[pos].width = widthVal;
					borderData[pos].width_unit = widthUnitVal;
					borderData[pos].color = colorVal;
					borderData[pos].style = styleVal;

					ActiveEl.css('border-' + pos + '_width', widthVal + widthUnitVal);
					ActiveEl.css('border-' + pos + '_color', colorVal);
					ActiveEl.css('border-' + pos + '_style', styleVal);
				});
				thisCls.updateInputVal('border', borderData);
				thisCls.updateStylesVal('border', borderData, true);

				var radiusData = {};
				$.each(['top_left', 'top_right', 'bottom_left', 'bottom_right'], function (k, pos) {
					radiusData['border_' + pos + '_radius'] = $Tab.find('input[name=border_' + pos + '_radius]').val();
					radiusData['border_' + pos + '_radius_unit'] = $Tab.find('select[name=border_' + pos + '_radius_unit]').val();
				});

				thisCls.updateInputVal('border_radius', radiusData);
				thisCls.updateStylesVal('border_radius', radiusData, true);

				var userBrowser = thisCls.getBrowserInfo();
				var currentEl = thisCls.getCurrentElement();
				switch(userBrowser.engine){
					case 'presto':
						currentEl.css(
							'border-radius',
							radiusData.border_top_left_radius + radiusData.border_top_left_radius_unit + ' ' +
								radiusData.border_top_right_radius + radiusData.border_top_right_radius_unit + ' ' +
								radiusData.border_bottom_right_radius + radiusData.border_bottom_right_radius_unit + ' ' +
								radiusData.border_bottom_left_radius + radiusData.border_bottom_left_radius_unit
							);
						break;
					case 'webkit':
						currentEl.css(
							'-webkit-border-radius',
							radiusData.border_top_left_radius + radiusData.border_top_left_radius_unit + ' ' +
								radiusData.border_top_right_radius + radiusData.border_top_right_radius_unit + ' ' +
								radiusData.border_bottom_right_radius + radiusData.border_bottom_right_radius_unit + ' ' +
								radiusData.border_bottom_left_radius + radiusData.border_bottom_left_radius_unit
							);
						break;
					case 'gecko':
						currentEl.css(
							'-moz-border-radius',
							radiusData.border_top_left_radius + radiusData.border_top_left_radius_unit + ' ' +
								radiusData.border_top_right_radius + radiusData.border_top_right_radius_unit + ' ' +
								radiusData.border_bottom_right_radius + radiusData.border_bottom_right_radius_unit + ' ' +
								radiusData.border_bottom_left_radius + radiusData.border_bottom_left_radius_unit
							);
						break;
				}
			}
		}
	});

})(jQuery);
