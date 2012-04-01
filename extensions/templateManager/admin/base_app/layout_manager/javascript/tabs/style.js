(function($, undefined) {

	$.extend($.ui.LayoutDesigner.prototype.tabs, {
		style: {
			tabId: 'style',
			init: function () {
				var parentCls = this;
				var thisCls = parentCls.tabs.style;

				var $Tab = parentCls.TabPanel.find('#' + thisCls.tabId);
				var InputVals = parentCls.getElInputData();

				var values = {
				//	classes: '',
					custom_css: ''
				};

				$.extend(true, values, InputVals);

				//$Tab.find('textarea[name=classes]')
				//	.val(values.classes)
				//	.keyup(function () { thisCls.processInputs.apply(parentCls); });

				$Tab.find('textarea[name=custom_css]')
					.val(values.custom_css)
					.blur(function () { thisCls.processInputs.apply(parentCls); });
			},
			processInputs: function () {
				var parentCls = this;
				var thisCls = parentCls.tabs.settings;
				var $Tab = parentCls.TabPanel.find('#style');

				//this.updateInputVal('classes', $Tab.find('textarea[name=classes]').val());
				parentCls.updateInputVal('custom_css', $Tab.find('textarea[name=custom_css]').val());

				parentCls.updateStylesVal('custom_css', $Tab.find('textarea[name=custom_css]').val());
			}
		}
	});

})(jQuery);
