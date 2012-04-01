(function($, undefined) {

	$.extend($.ui.LayoutDesigner.prototype.tabs, {
		settings: {
			init: function () {
				var $Tab = $('#mainTabPanel').find('#settings');
				var parentCls = this;
				var thisCls = parentCls.tabs.settings;
				var InputVals = parentCls.getElInputData();

				var values = {
					id: '',
					width: '1024',
					width_unit: 'px',
					vertical_align: 'baseline'
				};

				$.extend(true, values, InputVals);

				$Tab.find('input[name=id]')
					.val(values.id)
					.keyup(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('input[name=width]')
					.val(values.width)
					.keyup(function () { $Tab.find('.widthSlider').slider('value', $(this).val());thisCls.processInputs.apply(parentCls); });

				var WidthUnitChange = function (){
					if ($(this).val() == 'auto'){
						$Tab.find('input[name=width]').attr('disabled', 'disabled');
						$Tab.find('.widthSlider').slider('disable');
					}else if ($(this).val() == '%'){
						$Tab.find('input[name=width]').removeAttr('disabled');
						$Tab.find('.widthSlider').slider('enable');
						$Tab.find('.widthSlider').slider('option', 'min', 0);
						$Tab.find('.widthSlider').slider('option', 'max', 100);
						$Tab.find('.widthSlider').slider('value', $Tab.find('input[name=width]').val());
					}else{
						$Tab.find('input[name=width]').removeAttr('disabled');
						$Tab.find('.widthSlider').slider('enable');
						$Tab.find('.widthSlider').slider('option', 'min', 30);
						$Tab.find('.widthSlider').slider('option', 'max', parentCls.getCurrentElement().parent().innerWidth());
					}
					thisCls.processInputs.apply(parentCls);
				};

				$Tab.find('select[name=width_unit]')
					.val(values.width_unit)
					.change(WidthUnitChange);

				$Tab.find('.widthSlider').slider({
					value: 0,
					max: 100,
					min: 30,
					step: 1,
					slide: function (e, ui) {
						$Tab.find('input[name=width]').val(ui.value);
						thisCls.processInputs.apply(parentCls);
					}
				});

				if (values.width_unit == 'auto'){
					$Tab.find('input[name=width]').attr('disabled', 'disabled');
					$Tab.find('.widthSlider').slider('disable');
				}
				else {
					if (values.width_unit == '%'){
						$Tab.find('.widthSlider').slider('option', 'min', 0);
						$Tab.find('.widthSlider').slider('option', 'max', 100);
						$Tab.find('.widthSlider').slider('value', values.width);
					}
					else {
						var parentWidth = parentCls.getCurrentElement().parent().innerWidth();
						$Tab.find('.widthSlider').slider('option', 'min', 30);
						$Tab.find('.widthSlider').slider('option', 'max', parentWidth);
						if (values.width > parentWidth){
							$Tab.find('input[name=width]').val(parentWidth);
							$Tab.find('.widthSlider').slider('value', parentWidth);
						}else{
							$Tab.find('.widthSlider').slider('value', values.width);
						}
					}
				}
			},
			processInputs: function () {
				var $Tab = $('#mainTabPanel').find('#settings');
				var parentCls = this;

				this.updateInputVal('id', $Tab.find('input[name=id]').val());
				this.updateInputVal('width', $Tab.find('input[name=width]').val());
				this.updateInputVal('width_unit', $Tab.find('select[name=width_unit]').val());

				if ($Tab.find('select[name=width_unit]').val() == 'auto'){
					this.updateStylesVal('width', 'auto');
				}
				else {
					this.updateStylesVal('width', $Tab.find('input[name=width]').val() + $Tab.find('select[name=width_unit]').val());
				}
			}
		}
	});

})(jQuery);
