(function($, undefined) {

	$.fn.backgroundBuilder.global.solid = function (o){
		this.helper = o.helper;
		this.engine = o.engine;
		this.sCont = o.sCont;
		this.activeEl = o.activeEl;
		this.layoutBuilder = o.layoutBuilder;

		var userBrowser = this.layoutBuilder.getBrowserInfo();

		var inputVals = this.activeEl.data('inputs');
		if (!inputVals.background){
			inputVals.background = {};
		}

		if (!inputVals.background.global){
			inputVals.background.global = {};
		}

		if (!inputVals.background.global.solid){
			inputVals.background.global.solid = {
				config: {}
			};
		}

		this.getInputsData = function (){
			return this.activeEl.data('inputs');
		};

		this.setInputsData = function (data){
			this.activeEl.data('inputs', data);
		};

		this.getData = function (){
			return this.activeEl.data('inputs').background.global.solid;
		};

		this.updateActiveElementData = function (elData){
			var inputVals = this.getInputsData();
			inputVals.background.global.solid.config = elData;
			this.setInputsData(inputVals);
		};

		this.showSettings = function (){
			var self = this;
			var inputVals = this.getData();

			$(this.sCont).html(self.settings);

			$(this.sCont).find('input').each(function (){
				if (inputVals.config[$(this).attr('name')]){
					$(this).val(inputVals.config[$(this).attr('name')]);
				}
			});

			this.layoutBuilder.buildBackgroundColorPicker_RGBA($(this.sCont).find('.makeColorPicker_RGBA'), {
				inputR: $(this.sCont).find('input[name=background_r]'),
				inputG: $(this.sCont).find('input[name=background_g]'),
				inputB: $(this.sCont).find('input[name=background_b]'),
				inputA: $(this.sCont).find('input[name=background_a]')
			});

			$(this.sCont).find('.makeColorPicker_RGBA').bind('onChange', function (){
				var colorStr = 'rgba(' +
					$(self.sCont).find('input[name=background_r]').val() + ', ' +
					$(self.sCont).find('input[name=background_g]').val() + ', ' +
					$(self.sCont).find('input[name=background_b]').val() + ', ' +
					(parseInt($(self.sCont).find('input[name=background_a]').val())/100) +
					')';

				self.layoutBuilder.updateStylesVal('background', colorStr);

				var config = {
					background_r : $(self.sCont).find('input[name=background_r]').val(),
					background_g : $(self.sCont).find('input[name=background_g]').val(),
					background_b : $(self.sCont).find('input[name=background_b]').val(),
					background_a : $(self.sCont).find('input[name=background_a]').val()
				};
				self.updateActiveElementData(config);
			});
		};

		this.settings = '<table cellpadding="0" cellspacing="0" border="0" style="margin:.5em;width:200px;">' +
			'<tr>' +
			'<td>Color: </td>' +
			'<td colspan="2" align="center" class="makeColorPicker_RGBA">Click For Color Picker</td>' +
			'</tr>' +
			'<tr>' +
			'<td>Red</td>' +
			'<td>( 0 - 255 )</td>' +
			'<td><input type="text" name="background_r" size="3" maxlength="3" value="255"></td>' +
			'</tr>' +
			'<tr>' +
			'<td>Green</td>' +
			'<td>( 0 - 255 )</td>' +
			'<td><input type="text" name="background_g" size="3" maxlength="3" value="255"></td>' +
			'</tr>' +
			'<tr>' +
			'<td>Blue</td>' +
			'<td>( 0 - 255 )</td>' +
			'<td><input type="text" name="background_b" size="3" maxlength="3" value="255"></td>' +
			'</tr>' +
			'<tr>' +
			'<td>Alpha</td>' +
			'<td>( 0 - 100 )</td>' +
			'<td><input type="text" name="background_a" size="3" maxlength="3" value="100">%</td>' +
			'</tr>' +
			'</table>';
	};

})(jQuery);
