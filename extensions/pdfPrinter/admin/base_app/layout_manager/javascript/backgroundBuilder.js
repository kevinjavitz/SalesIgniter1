(function( $, undefined ) {
	$.fn.backgroundBuilder = function(o){
		var options = {
			layoutBuilder: false,
			previewEl: false,
			activeEl: false,
			forceEngine: false,
			contentHolder: false,
			inputVals: []
		};
		options = $.extend({}, options, o || {});

		return this.each(function (){
			var self = this;

			self.setupBackgroundSettings = function (){
				var $newDiv = $('<div></div>')
				.addClass('backgroundSettings')
				.insertAfter(this);

				return $newDiv;
			};

			options.contentHolder = options.contentHolder || self.setupBackgroundSettings();

			var InputVals = options.layoutBuilder.getElInputData();
			if (!InputVals.backgroundType){
				InputVals.backgroundType = {};
			}
			options.layoutBuilder.setElInputData(InputVals);

			$(self).change(function (event){
				if ($.fn.backgroundBuilder[options.forceEngine] && $.fn.backgroundBuilder[options.forceEngine][$(this).val()]){
					var bgClass = new $.fn.backgroundBuilder[options.forceEngine][$(this).val()]({
						layoutBuilder: options.layoutBuilder,
						sCont: options.contentHolder,
						engine: options.forceEngine,
						previewEl: options.previewEl,
						activeEl: options.activeEl
					});
					bgClass.showSettings();
				}else{
					$(options.contentHolder).html('');
					if (options.forceEngine == 'global'){
						options.activeEl.css('background', 'transparent');
					}
				}

				InputVals.backgroundType[options.forceEngine] = $(this).val();
				options.layoutBuilder.setElInputData(InputVals);
			});

			if (InputVals.backgroundType[options.forceEngine]){
				$(self).val(InputVals.backgroundType[options.forceEngine]);
				$(self).trigger('change');
			}
		});
	};

	$.fn.backgroundBuilder.rgbStringFromCollection = function (coll, alpha){
		alpha = alpha || false;
		var colorStr = '';

		var bgColor = { r: '', g: '', b: '' };
		if (alpha === true){
			bgColor.a = '';
		}

		if (coll.size() > 1){
			coll.each(function (){
				if ($(this).hasClass('colorPickerRGB' + (alpha === true ? 'A' : '') + '_Red')){
					bgColor.r = $(this).val();
				} else if ($(this).hasClass('colorPickerRGB' + (alpha === true ? 'A' : '') + '_Green')){
					bgColor.g = $(this).val();
				} else if ($(this).hasClass('colorPickerRGB' + (alpha === true ? 'A' : '') + '_Blue')){
					bgColor.b = $(this).val();
				}

				if (alpha === true && $(this).hasClass('colorPickerRGBA_Alpha')){
					bgColor.a = (parseInt($(this).val()) / 100);
				}
			});
		}else{
			bgColor.r = coll.find('.colorPickerRGB' + (alpha === true ? 'A' : '') + '_Red').val();
			bgColor.g = coll.find('.colorPickerRGB' + (alpha === true ? 'A' : '') + '_Green').val();
			bgColor.b = coll.find('.colorPickerRGB' + (alpha === true ? 'A' : '') + '_Blue').val();
			if (alpha === true){
				bgColor.a = (parseInt(coll.find('.colorPickerRGBA_Alpha').val()) / 100);
			}
		}

		if (alpha === true){
			colorStr = 'rgba(' + bgColor.r + ', ' + bgColor.g + ', ' + bgColor.b + ', ' + bgColor.a + ')';
		}else{
			colorStr = 'rgb(' + bgColor.r + ', ' + bgColor.g + ', ' + bgColor.b + ')';
		}

		return colorStr;
	};

	$.fn.backgroundBuilder.backgroundUrlStringFromCollection = function (coll, rgba){
		rgba = rgba || false;

		var colorStr = '';
		if (rgba != 'noColor'){
			colorStr = $.fn.backgroundBuilder.rgbStringFromCollection(coll, rgba) + ' ';
		}
		return 'url(' +
			coll.find('input[name=image_source]').val() +
			') ' +
			colorStr +
			coll.find('select[name=image_attachment]').val() + ' ' +
			coll.find('input[name=image_pos_x]').val() + '% ' +
			coll.find('input[name=image_pos_y]').val() + '% ' +
			coll.find('select[name=image_repeat]').val();
	};

	$.fn.backgroundBuilder.updatePreviewElementsStyle = function(previewEl, cssKey, cssString){
		$(previewEl).each(function () {
			$(this).attr('style', 'border:1px solid #cccccc;height:100px;' + cssKey + ': ' + cssString);
		});
	};

	$.fn.backgroundBuilder.updateActiveElementStyle = function(activeEl, cssKey, cssString){
		$(activeEl).each(function () {
			$(this).css(cssKey, cssString);
		});
	};

	$.fn.backgroundBuilder.setupGradientRGBA = function (self, el, values){
		$(el).each(function (){
			var inputR = $(this).parent().next().find('.colorPickerRGBA_Red');
			var inputG = $(this).parent().next().find('.colorPickerRGBA_Green');
			var inputB = $(this).parent().next().find('.colorPickerRGBA_Blue');
			var inputA = $(this).parent().next().find('.colorPickerRGBA_Alpha');
			var pickerEl = $(this);
			var bgColor = {r: 255, g: 255, b: 255, a: 100};

			$.each([inputR, inputG, inputB, inputA], function (){
				$(this).keyup(function (){
					pickerEl.trigger('updateBackground');
					self.updateGradient();
				});

				if (values && values[$(this).attr('name')]){
					var key = $(this).attr('name').substr(-1);
					bgColor[key] = values[$(this).attr('name')];

					$(this).val(values[$(this).attr('name')]);
				}
			});

			LayoutDesigner.buildBackgroundColorPicker_RGBA($(this), {
				inputR: inputR,
				inputG: inputG,
				inputB: inputB,
				inputA: inputA
			});

			$(this).css('background-color', 'rgba(' + bgColor.r + ', ' + bgColor.g + ', ' + bgColor.b + ', ' + bgColor.a + ')');

			$(this).bind('onChange', function (){
				self.updateGradient();
			});
		});
	};

})(jQuery);
