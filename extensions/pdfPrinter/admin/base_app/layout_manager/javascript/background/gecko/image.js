(function($, undefined) {

	$.fn.backgroundBuilder.gecko.image = function (o) {
		this.helper = o.helper;
		this.engine = o.engine;
		this.sCont = o.sCont;
		this.activeEl = o.activeEl;
		this.layoutBuilder = o.layoutBuilder;

		var userBrowser = this.layoutBuilder.getBrowserInfo();

		var StylesInfo = this.activeEl.data('styles_info');
		var inputVals = StylesInfo.input_vals;
		if (!inputVals.gecko){
			inputVals.gecko = {};
		}

		if (!inputVals.gecko.background){
			inputVals.gecko.background = {};
		}

		if (!inputVals.gecko.background.image){
			inputVals.gecko.background.image = {
				config  : {},
				images  : []
			};
		}

		this.settings = '<table cellpadding="0" cellspacing="0" border="0" style="margin:.5em;">' +
		'<tr>' +
		'<td valign="top"><table cellpadding="2" cellspacing="0" border="0" style="margin:0;">' +
		'<tr>' +
		'<td>Fallback Color: </td>' +
		'<td><input class="makeColorPicker" type="text" name="background_color"></td>' +
		'</tr>' +
		'<tr>' +
		'<td>Image: </td>' +
		'<td><input type="text" name="background_image" class="BrowseServerField" currentFolder=""></td>' +
		'</tr>' +
		'<tr>' +
		'<td>Repeat: </td>' +
		'<td><select name="background_repeat">' +
		'<option value="no-repeat">No Repeat</option>' +
		'<option value="repeat">Tile</option>' +
		'<option value="repeat-x">Repeat Horizontal</option>' +
		'<option value="repeat-y">Repeat Vertical</option>' +
		'</select></td>' +
		'</tr>' +
		'</table></td>' +
		'<td valign="top"><table cellpadding="2" cellspacing="0" border="0" style="margin:0;margin-left:2em;">' +
		'<tr>' +
		'<td valign="top">Position Horizontal: </td>' +
		'<td width="150">' +
		'<input type="text" name="background_position_x" class="percentSliderVal" size="3" maxlength="3">%&nbsp;&nbsp;' +
		'<div class="backgroundPositionX"></div>' +
		'</td>' +
		'</tr>' +
		'<tr>' +
		'<td valign="top">Position Vertical: </td>' +
		'<td width="150">' +
		'<input type="text" name="background_position_y" class="percentSliderVal" size="3" maxlength="3">%&nbsp;&nbsp;' +
		'<div class="backgroundPositionY"></div>' +
		'</td>' +
		'</tr>' +
		'</table></td>' +
		'</tr>' +
		'</table>';

		this.showSettings = function () {
			var self = this;
			var inputVals = this.activeEl.data('styles_info').input_vals.gecko.background.image;

			$(this.sCont).html(self.settings);
		};
	};

})(jQuery);
