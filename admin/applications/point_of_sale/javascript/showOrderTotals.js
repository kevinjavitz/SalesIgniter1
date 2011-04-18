(function($) {
	$.extend($.ui.pointOfSale.prototype, {
		showOrderTotals: function ($el){
			var self = this;

			showAjaxLoader($el, 'large');
			$.ajax({
				url: self._getURL('order'),
				dataType: 'html',
				type: 'get',
				data: 'action=getOrderTotals',
				cache: false,
				success: function (data){
					removeAjaxLoader($el);
					$el.html(data);
					
					$('li', $('#orderTotalsList', $el)).hover(function (){
						this.style.cursor = 'move';
					}, function (){
						this.style.cursor = 'default';
					});
					
					$('#orderTotalsList', $el).sortable({
						axis: 'y',
						helper: 'clone',
						revert: true,
						tolerance: 'pointer',
						containment: 'parent',
						cursor: 'move',
						handle: '.ui-icon-arrow-4'
					});
				}
			});
		}
	});
})(jQuery);