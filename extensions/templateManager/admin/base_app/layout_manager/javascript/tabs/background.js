(function($, undefined) {

	$.extend($.ui.LayoutDesigner.prototype.tabs, {
		background: {
			init: function () {
				var $Tab = $('#mainTabPanel').find('#background');
				var parentCls = this;
				var thisCls = parentCls.tabs.background;

				$Tab.find('select[name=background_type]').each(function () {
					var $tabPanel = $(this).parentsUntil('#backgroundTabs').last();
					$(this).backgroundBuilder({
						layoutBuilder: parentCls,
						activeEl: parentCls.getCurrentElement(),
						forceEngine: $tabPanel.attr('data-engine'),
						contentHolder: $tabPanel.find('.backgroundSettings')
					});
				});

				$Tab.find('#backgroundTabs').tabs();

				/* @TODO: Tabs disabled until the feature can be tested 100%, do not enable */
				$Tab.find('#backgroundTabs').tabs('disable', 1);
				$Tab.find('#backgroundTabs').tabs('disable', 2);
				$Tab.find('#backgroundTabs').tabs('disable', 3);
				$Tab.find('#backgroundTabs').tabs('disable', 4);
				$Tab.find('#backgroundTabs').tabs('disable', 5);
			},
			processInputs: function () {
			}
		}
	});

})(jQuery);
