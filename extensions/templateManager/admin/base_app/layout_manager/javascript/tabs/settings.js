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


var test = {
	"type": "linear",
	"h_pos_start": "0",
	"v_pos_start": "0",
	"h_pos_end": "0",
	"v_pos_end": "100",
	"h_pos_start_unit": "%",
	"v_pos_start_unit": "%",
	"h_pos_end_unit": "%",
	"v_pos_end_unit": "%",
	"colorStops": [{
		"color": {
			"r": 104,
			"g": 103,
			"b": 104,
			"a": 1
		},
		"position": 0
	},{
		"color": {
			"r": 53,
			"g": 52,
			"b": 53,
			"a": 1
		},
		"position": .5
	},{
		"color": {
			"r": 0,
			"g": 0,
			"b": 0,
			"a": 1
		},
		"position": .5
	},{
		"color": {
			"r": 30,
			"g": 30,
			"b": 30,
			"a": 1
		},
		"position": .75
	},{
		"color": {
			"r": 34,
			"g": 34,
			"b": 34,
			"a": 1
		},
		"position": 1
	}]
}
