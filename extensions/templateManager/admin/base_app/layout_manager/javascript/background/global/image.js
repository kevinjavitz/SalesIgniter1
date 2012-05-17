(function($, undefined) {



	$.fn.backgroundBuilder.global.image = function (o){

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



		if (!inputVals.background.global.image){

			inputVals.background.global.image = {

				config  : {},

				images  : []

			};

		}



		this.getInputsData = function (){

			return this.activeEl.data('inputs');

		};



		this.setInputsData = function (data){

			this.activeEl.data('inputs', data);

		};



		this.getData = function (){

			return this.activeEl.data('inputs').background.global.image;

		};



		this.updateActiveElementData = function (elData){

			var inputVals = this.getInputsData();

			inputVals.background.global.image = elData;

			this.setInputsData(inputVals);

		};



		this.showSettings = function (){

			var self = this,

				$sCont = $(this.sCont);

			var inputVals = this.getData();



			$sCont.html(self.settings);







			$sCont.find('input[name=background_color], input[name=background_image], select[name=background_repeat]').each(function (){



					if (inputVals.config[$(this).attr('name')]){

						$(this).val(inputVals.config[$(this).attr('name')]);

					}

				});



			$sCont.find('input[name=background_image]').blur(function (){

				self.updateConfig();

			});



			$sCont.find('select[name=background_repeat]').change(function (){

					self.updateConfig();

				});



			LayoutDesigner.buildColorPicker_RGB($sCont.find('.makeColorPicker'));



			$sCont.find('input[name=background_color]').bind('onChange', function (){

				self.updateConfig();

			});



			$sCont.find('input[name=background_position_x], input[name=background_position_y]').each(function (){

					var inputEl = this;

					$(inputEl).keyup(function (){

							$sCont.find('#' + $(this).attr('name') + 'Slider').slider('value', $(this).val());

							self.updateConfig();

						});



					if (inputVals.config[$(this).attr('name')]){

						$(this).val(inputVals.config[$(this).attr('name')]);

					}



					self.layoutBuilder.createPercentSlider($sCont.find('#' + $(this).attr('name') + 'Slider'), {

							value: parseInt($(this).val()),

							slide: function (e, ui){

								$(inputEl).val(ui.value);

								self.updateConfig();

							}

						});

				});

		};



		this.updatePreview = function (){

			var self = this,

				backgroundKey = 'background',

				$sCont = $(this.sCont);



			var backgroundStr = '';

			backgroundStr += $sCont.find('input[name=background_color]').val() + ' ';

			backgroundStr += 'url(' + $sCont.find('input[name=background_image]').val() + ') ';

			backgroundStr += $sCont.find('input[name=background_position_x]').val() + '% ';

			backgroundStr += $sCont.find('input[name=background_position_y]').val() + '% ';

			backgroundStr += $sCont.find('select[name=background_repeat]').val();



			$.fn.backgroundBuilder.updateActiveElementStyle(this.activeEl, backgroundKey, backgroundStr);

		};



		this.updateConfig = function (){

			var self = this,

				$sCont = $(this.sCont);



			self.updatePreview();



			inputVals.background.global.image.config = {

				background_color      : $sCont.find('input[name=background_color]').val(),

				background_image      : $sCont.find('input[name=background_image]').val(),

				background_position_x : $sCont.find('input[name=background_position_x]').val(),

				background_position_y : $sCont.find('input[name=background_position_y]').val(),

				background_repeat     : $sCont.find('select[name=background_repeat]').val()

			};



			this.updateActiveElementData(inputVals.background.global.image);

		};



		this.settings = '<table cellpadding="0" cellspacing="0" border="0" style="margin:.5em;">' +

			'<tr>' +

			'<td valign="top"><table cellpadding="2" cellspacing="0" border="0" style="margin:0;">' +

			'<tr>' +

			'<td>Fallback Color: </td>' +

			'<td><input class="makeColorPicker" type="text" name="background_color"></td>' +

			'</tr>' +

			'<tr>' +

			'<td>Image: </td>' +

			'<td><input type="text" name="background_image" class="BrowseServerField"></td>' +

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

			'<input type="text" name="background_position_x" class="percentSliderVal" value="0" size="3" maxlength="3">%&nbsp;&nbsp;' +

			'<div id="background_position_xSlider"></div>' +

			'</td>' +

			'</tr>' +

			'<tr>' +

			'<td valign="top">Position Vertical: </td>' +

			'<td width="150">' +

			'<input type="text" name="background_position_y" class="percentSliderVal" value="0" size="3" maxlength="3">%&nbsp;&nbsp;' +

			'<div id="background_position_ySlider"></div>' +

			'</td>' +

			'</tr>' +

			'</table></td>' +

			'</tr>' +

			'</table>';

	};



})(jQuery);

