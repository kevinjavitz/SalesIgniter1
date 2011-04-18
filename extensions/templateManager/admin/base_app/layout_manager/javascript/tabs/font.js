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
					}
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
			},
			processInputs: function () {
				var $Tab = $('#mainTabPanel').find('#font');
				var thisCls = this;
				var ActiveEl = thisCls.getCurrentElement();

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
					}
				};

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

				thisCls.updateInputVal('font', fontData);
				thisCls.updateStylesVal('font', fontData, true);
				thisCls.updateInputVal('text', textData);
				thisCls.updateStylesVal('text', textData, true);

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
