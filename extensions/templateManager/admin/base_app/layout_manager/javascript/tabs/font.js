(function($, undefined) {

	$.extend($.ui.LayoutDesigner.prototype.tabs, {
		font: {
			init: function () {
				var $Tab = $('#mainTabPanel').find('#font');
				var parentCls = this;
				var thisCls = parentCls.tabs.font;
				var InputVals = parentCls.getElInputData();

				var values = {
					font: {
						family: 'Arial',
						size: '1',
						size_unit: 'em',
						style: 'normal',
						variant: 'normal',
						weight: 'normal'
					},
					text: {
						color: '#000000',
						letter_spacing: '0',
						letter_spacing_unit: 'normal',
						line_height: '1',
						line_height_unit: 'em',
						align: 'left',
						decoration: 'none',
						indent: '0',
						indent_unit: 'px',
						transform: 'none',
						vertical_align: 'baseline',
						white_space: 'normal',
						word_spacing: '0',
						word_spacing_unit: 'normal'
					},
					link: {
						unvisited: {
							color: 'inherit',
							weight: 'normal',
							background_color: 'transparent',
							text_decoration: 'none'
						},
						visited: {
							color: 'inherit',
							weight: 'normal',
							background_color: 'transparent',
							text_decoration: 'none'
						},
						active: {
							color: 'inherit',
							weight: 'normal',
							background_color: 'transparent',
							text_decoration: 'none'
						},
						hover: {
							color: 'inherit',
							weight: 'normal',
							background_color: 'transparent',
							text_decoration: 'none'
						}
					},
					text_shadow: []
				};

				$.extend(true, values, InputVals);

				$Tab.find('select[name=font_family]')
					.val(values.font.family)
					.change(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('input[name=font_size]')
					.val(values.font.size)
					.keyup(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('select[name=font_size_unit]')
					.val(values.font.size_unit)
					.change(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('select[name=font_style]')
					.val(values.font.style)
					.change(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('select[name=font_variant]')
					.val(values.font.variant)
					.change(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('select[name=font_weight]')
					.val(values.font.weight)
					.change(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('input[name=color]')
					.val(values.text.color)
					.keyup(function () { thisCls.processInputs.apply(parentCls); })
					.bind('onChange', function (){ thisCls.processInputs.apply(parentCls); });

				$Tab.find('input[name=letter_spacing]')
					.val(values.text.letter_spacing)
					.keyup(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('select[name=letter_spacing_unit]')
					.val(values.text.letter_spacing_unit)
					.change(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('input[name=line_height]')
					.val(values.text.line_height)
					.keyup(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('select[name=line_height_unit]')
					.val(values.text.line_height_unit)
					.change(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('select[name=text_align]')
					.val(values.text.align)
					.change(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('select[name=text_decoration]')
					.val(values.text.decoration)
					.change(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('input[name=text_indent]')
					.val(values.text.indent)
					.keyup(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('select[name=text_indent_unit]')
					.val(values.text.indent_unit)
					.change(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('select[name=text_transform]')
					.val(values.text.transform)
					.change(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('select[name=vertical_align]')
					.val(values.text.vertical_align)
					.change(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('select[name=white_space]')
					.val(values.text.white_space)
					.change(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('input[name=word_spacing]')
					.val(values.text.word_spacing)
					.keyup(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('select[name=word_spacing_unit]')
					.val(values.text.word_spacing_unit)
					.change(function () { thisCls.processInputs.apply(parentCls); });

				$.each(['unvisited', 'visited', 'hover', 'active'], function (){
					$Tab.find('input[name=link_' + this + '_color]')
						.val(values.link[this].color)
						.keyup(function () { thisCls.processInputs.apply(parentCls); })
						.bind('onChange', function (){ thisCls.processInputs.apply(parentCls); });

					$Tab.find('input[name=link_' + this + '_background_color]')
						.val(values.link[this].background_color)
						.keyup(function () { thisCls.processInputs.apply(parentCls); })
						.bind('onChange', function (){ thisCls.processInputs.apply(parentCls); });

					$Tab.find('select[name=link_' + this + '_text_decoration]')
						.val(values.link[this].text_decoration)
						.change(function () { thisCls.processInputs.apply(parentCls); });

					$Tab.find('select[name=link_' + this + '_font_weight]')
						.val(values.link[this].weight)
						.change(function () { thisCls.processInputs.apply(parentCls); });
				});

				if (values.text_shadow.length > 0){
					$.each(values.text_shadow, function (k, v) {
						thisCls.createShadowConfig.apply(parentCls, [v]);
					});
				}

				$Tab.find('.addTextShadow').click(function () {
					thisCls.createShadowConfig.apply(parentCls);
				});

			},
			createShadowSliders: function ($el){
				var parentCls = this;
				var thisCls = parentCls.tabs.font;
				$el.find('input').each(function (){
					var $InputField = $(this);
					$el.find('.' + $(this).attr('name') + 'Slider').each(function (){
						$(this).slider({
							min: -100,
							max: 100,
							value: parseInt($InputField.val()),
							slide: function (e, ui){
								$InputField.val(ui.value);
								thisCls.processInputs.apply(parentCls);
							}
						});
						$InputField.data('sliderEl', $(this));
					});
				});
			},
			createShadowConfig: function (o) {
				var values = {
					shadow_offset_y: 2,
					shadow_offset_x: 2,
					shadow_blur: 5,
					shadow_color: '#000000'
				};
				$.extend(true, values, o || {});

				var $Tab = $('#mainTabPanel').find('#font');
				var parentCls = this;
				var thisCls = parentCls.tabs.font;

				var $newShadow = $('<tr>')
					.append('<td align="center" valign="top" style="width:150px"><input type="text" name="shadow_offset_y" size="2" value="2"><div class="shadow_offset_ySlider" style="margin-top:5px;"></div></td>')
					.append('<td align="center" valign="top" style="width:150px"><input type="text" name="shadow_offset_x" size="2" value="2"><div class="shadow_offset_xSlider" style="margin-top:5px;"></div></td>')
					.append('<td align="center" valign="top" style="width:150px"><input type="text" name="shadow_blur" size="2" value="1"><div class="shadow_blurSlider" style="margin-top:5px;"></div></td>')
					.append('<td valign="top"><input type="text" name="shadow_color" class="makeColorPicker" value="#000000" size="8"></td>')
					.append('<td valign="top"><span class="ui-icon ui-icon-closethick removeShadow"></span></td>');

				$Tab.find('.textShadowConfigs tbody').append($newShadow);

				$newShadow.find('input[name=shadow_offset_y]')
					.val(values.shadow_offset_y)
					.keyup(function () { $(this).data('sliderEl').slider('value', $(this).val());thisCls.processInputs.apply(parentCls); });

				$newShadow.find('input[name=shadow_offset_x]')
					.val(values.shadow_offset_x)
					.keyup(function () { $(this).data('sliderEl').slider('value', $(this).val());thisCls.processInputs.apply(parentCls); });

				$newShadow.find('input[name=shadow_blur]')
					.val(values.shadow_blur)
					.keyup(function () { $(this).data('sliderEl').slider('value', $(this).val());thisCls.processInputs.apply(parentCls); });

				$newShadow.find('input[name=shadow_color]')
					.val(values.shadow_color)
					.keyup(function () { thisCls.processInputs.apply(parentCls); });

				$newShadow.find('.makeColorPicker').ColorPicker({
					onChange: function (hsb, hex, rgb, el) {
						$(el).val('#' + hex);
						thisCls.processInputs.apply(parentCls);
					}
				});

				thisCls.createShadowSliders.apply(parentCls, [$newShadow]);

				$newShadow.find('.removeShadow')
					.click(function () { $(this).parentsUntil('tbody').remove();thisCls.processInputs.apply(parentCls); });

				thisCls.processInputs.apply(parentCls);
			},
			processInputs: function () {
				var $Tab = $('#mainTabPanel').find('#font');
				var thisCls = this;
				var ActiveEl = thisCls.getCurrentElement();

				var fontData = {
					family    : $Tab.find('select[name=font_family]').val(),
					size      : $Tab.find('input[name=font_size]').val(),
					size_unit : $Tab.find('select[name=font_size_unit]').val(),
					style     : $Tab.find('select[name=font_style]').val(),
					variant   : $Tab.find('select[name=font_variant]').val(),
					weight    : $Tab.find('select[name=font_weight]').val()
				};

				var textData = {
					color               : $Tab.find('input[name=color]').val(),
					letter_spacing      : $Tab.find('input[name=letter_spacing]').val(),
					letter_spacing_unit : $Tab.find('select[name=letter_spacing_unit]').val(),
					line_height         : $Tab.find('input[name=line_height]').val(),
					line_height_unit    : $Tab.find('select[name=line_height_unit]').val(),
					align               : $Tab.find('select[name=text_align]').val(),
					decoration          : $Tab.find('select[name=text_decoration]').val(),
					indent              : $Tab.find('input[name=text_indent]').val(),
					indent_unit         : $Tab.find('select[name=text_indent_unit]').val(),
					transform           : $Tab.find('select[name=text_transform]').val(),
					vertical_align      : $Tab.find('select[name=vertical_align]').val(),
					white_space         : $Tab.find('select[name=white_space]').val(),
					word_spacing        : $Tab.find('input[name=word_spacing]').val(),
					word_spacing_unit   : $Tab.find('select[name=word_spacing_unit]').val()
				};

				var linkData = {
					unvisited: {
						color            : $Tab.find('input[name=link_unvisited_color]').val(),
						weight           : $Tab.find('select[name=link_unvisited_font_weight]').val(),
						background_color : $Tab.find('input[name=link_unvisited_background_color]').val(),
						text_decoration  : $Tab.find('select[name=link_unvisited_text_decoration]').val()
					},
					visited: {
						color            : $Tab.find('input[name=link_visited_color]').val(),
						weight           : $Tab.find('select[name=link_visited_font_weight]').val(),
						background_color : $Tab.find('input[name=link_visited_background_color]').val(),
						text_decoration  : $Tab.find('select[name=link_visited_text_decoration]').val()
					},
					hover: {
						color            : $Tab.find('input[name=link_hover_color]').val(),
						weight           : $Tab.find('select[name=link_hover_font_weight]').val(),
						background_color : $Tab.find('input[name=link_hover_background_color]').val(),
						text_decoration  : $Tab.find('select[name=link_hover_text_decoration]').val()
					},
					active: {
						color            : $Tab.find('input[name=link_active_color]').val(),
						weight           : $Tab.find('select[name=link_active_font_weight]').val(),
						background_color : $Tab.find('input[name=link_active_background_color]').val(),
						text_decoration  : $Tab.find('select[name=link_active_text_decoration]').val()
					}
				};

				var shadows = [];
				var shadowsArr = [];

				$Tab.find('.textShadowConfigs tbody tr').each(function () {
					var shadowOffsetY = $(this).find('input[name=shadow_offset_y]').val();
					var shadowOffsetX = $(this).find('input[name=shadow_offset_x]').val();
					var shadowBlur = $(this).find('input[name=shadow_blur]').val();
					var shadowColor = $(this).find('input[name=shadow_color]').val();

					shadows.push({
						shadow_offset_y : shadowOffsetY,
						shadow_offset_x : shadowOffsetX,
						shadow_blur     : shadowBlur,
						shadow_color    : shadowColor
					});

					shadowsArr.push(
							shadowOffsetY + 'px ' +
							shadowOffsetX + 'px ' +
							shadowBlur + 'px ' +
							shadowColor);
				});


				thisCls.updateInputVal('font', fontData);
				thisCls.updateStylesVal('font', fontData, true);
				thisCls.updateInputVal('text', textData);
				thisCls.updateStylesVal('text', textData, true);
				thisCls.updateInputVal('link', linkData);
				thisCls.updateStylesVal('link', linkData, true);
				thisCls.updateInputVal('text_shadow', shadows);
				thisCls.updateStylesVal('text_shadow', shadows, true);

				ActiveEl.css('text-shadow', shadowsArr.join(','));

				ActiveEl.css('font-family', fontData.family);
				ActiveEl.css('font-style', fontData.style);
				ActiveEl.css('font-variant', fontData.variant);
				ActiveEl.css('font-weight', fontData.weight);
				if (fontData.size_unit == 'inherit'){
					ActiveEl.css('font-size', fontData.size_unit);
				}else{
					ActiveEl.css('font-size', fontData.size + fontData.size_unit);
				}

				ActiveEl.css('color', textData.color);
				ActiveEl.css('text-align', textData.align);
				ActiveEl.css('text-decoration', textData.decoration);
				ActiveEl.css('text-transform', textData.transform);
				ActiveEl.css('vertical-align', textData.vertical_align);
				ActiveEl.css('white-space', textData.white_space);
				if (textData.letter_spacing_unit == 'normal' || textData.letter_spacing_unit == 'inherit'){
					ActiveEl.css('letter-spacing', textData.letter_spacing_unit);
				}else{
					ActiveEl.css('letter-spacing', textData.letter_spacing + textData.letter_spacing_unit);
				}

				if (textData.line_height_unit == 'inherit'){
					ActiveEl.css('line-height', textData.line_height_unit);
				}else{
					ActiveEl.css('line-height', textData.line_height + textData.line_height_unit);
				}

				if (textData.indent_unit == 'inherit'){
					ActiveEl.css('text-indent', textData.indent_unit);
				}else{
					ActiveEl.css('text-indent', textData.indent + textData.indent_unit);
				}

				if (textData.word_spacing_unit == 'normal' || textData.word_spacing_unit == 'inherit'){
					ActiveEl.css('word-spacing', textData.word_spacing_unit);
				}else{
					ActiveEl.css('word-spacing', textData.word_spacing + textData.word_spacing_unit);
				}
			}
		}
	});

})(jQuery);
