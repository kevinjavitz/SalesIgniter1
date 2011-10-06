(function($, undefined) {

	$.fn.backgroundBuilder.webkit.solid = function (o){
		this.helper = o.helper;
		this.engine = o.engine;
		this.sCont = o.sCont;
		this.activeEl = o.activeEl;

		var userBrowser = getBrowserInfo();

		var StylesInfo = this.activeEl.data('styles_info');
		var inputVals = StylesInfo.input_vals;
		if (!inputVals.webkit){
			inputVals.webkit = {};
		}

		if (!inputVals.webkit.background){
			inputVals.webkit.background = {};
		}

		if (!inputVals.webkit.background.solid){
			inputVals.webkit.background.solid = {
				config : {},
				color  : ''
			};
		}

		this.settings = '<table cellpadding="0" cellspacing="0" border="0" style="margin:.5em;width:200px;">' +
			'<tr>' +
			'<td>Color: </td>' +
			'<td colspan="2" align="center"><span class="makeColorPicker_RGBA"></span></td>' +
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

		this.showSettings = function (){
			var self = this;
			var inputVals = this.activeEl.data('styles_info').input_vals.webkit.background.solid;

			$(this.sCont).html(self.settings);
		};
	};

})(jQuery);
