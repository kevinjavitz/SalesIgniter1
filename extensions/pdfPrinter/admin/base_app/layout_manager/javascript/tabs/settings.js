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
					width_unit: 'mm',
					isheader:'false',
					isfooter:'false',
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

				var IsHeaderChange = function (){
					thisCls.processInputs.apply(parentCls);
				};

				$Tab.find('select[name=width_unit]')
					.val(values.width_unit)
					.change(WidthUnitChange);

				$Tab.find('select[name=isheader]')
					.val(values.isheader)
					.change(IsHeaderChange);
				$Tab.find('select[name=isfooter]')
					.val(values.isfooter)
					.change(IsHeaderChange);

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
					}
					else {
						$Tab.find('.widthSlider').slider('option', 'min', 30);
						$Tab.find('.widthSlider').slider('option', 'max', parentCls.getCurrentElement().parent().innerWidth());
					}
					$Tab.find('.widthSlider').slider('value', values.width);
				}
			},
			processInputs: function () {
				var $Tab = $('#mainTabPanel').find('#settings');
				var parentCls = this;

				this.updateInputVal('id', $Tab.find('input[name=id]').val());
				this.updateInputVal('width', $Tab.find('input[name=width]').val());
				this.updateInputVal('width_unit', $Tab.find('select[name=width_unit]').val());
				this.updateInputVal('isheader', $Tab.find('select[name=isheader]').val());
				this.updateInputVal('isfooter', $Tab.find('select[name=isfooter]').val());

				if ($Tab.find('select[name=isheader]').val() == 'true'){
					this.updateStylesVal('position-header', 'fixed');
					//this.updateStylesVal('height', '100px');
					//this.updateStylesVal('', '100px');
				}
				if ($Tab.find('select[name=isfooter]').val() == 'true'){
					this.updateStylesVal('position-footer', 'fixed');
					//this.updateStylesVal('height', '100px');
					//this.updateStylesVal('', '100px');
				}

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
