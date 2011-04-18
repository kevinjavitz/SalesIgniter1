(function($, undefined) {

	$.extend($.ui.LayoutDesigner.prototype.tabs, {
		spacing: {
			init: function () {
				var $Tab = $('#mainTabPanel').find('#margin');
				var parentCls = this;
				var thisCls = parentCls.tabs.spacing;
				var InputVals = parentCls.getElInputData();

				var values = {
					margin: {
						left: '0',
						top: '0',
						right: '0',
						bottom: '0',
						left_unit: 'px',
						top_unit: 'px',
						right_unit: 'px',
						bottom_unit: 'px'
					},
					padding: {
						left: '0',
						top: '0',
						right: '0',
						bottom: '0',
						left_unit: 'px',
						top_unit: 'px',
						right_unit: 'px',
						bottom_unit: 'px'
					}
				};

				$.extend(true, values, InputVals);

				$.each(['top', 'bottom', 'left', 'right'], function (k, pos) {
					$Tab.find('input[name=margin_' + pos + ']')
						.val(values.margin[pos])
						.keyup(function () { thisCls.processInputs.apply(parentCls); });

					$Tab.find('select[name=margin_' + pos + '_unit]')
						.val(values.margin[pos + '_unit'])
						.change(function () {
						thisCls.checkDisable.apply(parentCls, [$Tab.find('input[name=margin_' + pos + ']'), $(this)]);
						thisCls.processInputs.apply(parentCls);
					});

					$Tab.find('input[name=padding_' + pos + ']')
						.val(values.padding[pos])
						.keyup(function () { thisCls.processInputs.apply(parentCls); });

					$Tab.find('select[name=padding_' + pos + '_unit]')
						.val(values.padding[pos + '_unit'])
						.change(function () {
						thisCls.checkDisable.apply(parentCls, [$Tab.find('input[name=padding_' + pos + ']'), $(this)]);
						thisCls.processInputs.apply(parentCls);
					});

					thisCls.checkDisable.apply(parentCls, [$Tab.find('input[name=margin_' + pos + ']'), $Tab.find('select[name=margin_' + pos + '_unit]')]);
					thisCls.checkDisable.apply(parentCls, [$Tab.find('input[name=padding_' + pos + ']'), $Tab.find('select[name=padding_' + pos + '_unit]')]);
				});
			},
			checkDisable: function ($el, $unitEl){
				if ($unitEl.val() == 'auto'){
					$el.attr('disabled', 'disabled');
				}else{
					$el.removeAttr('disabled');
				}
			},
			processInputs: function () {
				var $Tab = $('#mainTabPanel').find('#margin');
				var thisCls = this;

				var ActiveEl = thisCls.getCurrentElement();

				var marginData = {};
				var paddingData = {};
				$.each(['top', 'bottom', 'left', 'right'], function (k, pos) {
					var marginVal = $Tab.find('input[name=margin_' + pos + ']').val();
					var marginUnitVal = $Tab.find('select[name=margin_' + pos + '_unit]').val();
					var paddingVal = $Tab.find('input[name=padding_' + pos + ']').val();
					var paddingUnitVal = $Tab.find('select[name=padding_' + pos + '_unit]').val();

					marginData[pos] = marginVal;
					marginData[pos + '_unit'] = marginUnitVal;

					paddingData[pos] = paddingVal;
					paddingData[pos + '_unit'] = paddingUnitVal;

					if (marginUnitVal == 'auto'){
						ActiveEl.css('margin-' + pos, marginUnitVal);
					}else{
						ActiveEl.css('margin-' + pos, marginVal + marginUnitVal);
					}

					if (paddingUnitVal == 'auto'){
						ActiveEl.css('padding-' + pos, paddingUnitVal);
					}else{
						ActiveEl.css('padding-' + pos, paddingVal + paddingUnitVal);
					}
				});
				thisCls.updateInputVal('margin', marginData);
				thisCls.updateInputVal('padding', paddingData);

				thisCls.updateStylesVal('margin', marginData, true);
				thisCls.updateStylesVal('padding', paddingData, true);
			}
		}
	});

})(jQuery);
