(function ($) {

	$.widget("ui.newGrid", {
		GridElement: null,
		GridButtonElement: null,
		GridPagerElement: null,
		options: {
			onRowClick: null
		},
		_create: function (){
			this.GridElement = $(this.element).find('.grid');
			this.GridButtonElement = $(this.element).find('.gridButtonBar');
			this.GridPagerElement = $(this.element).find('gridPagerBar');

			var self = this;

			$('.gridInfoRow').hide();
		},
		_init: function (){
			var self = this;

			$(this.GridElement).find('.gridBodyRow').live('mouseover mouseout click refresh', function (e, isRefresh) {
				switch(e.type){
					case 'mouseover':
						if (!$(this).hasClass('state-active')){
							$(this).addClass('state-hover');
							this.style.cursor = 'pointer';
						}
						break;
					case 'mouseout':
						if (!$(this).hasClass('state-active')){
							$(this).removeClass('state-hover');
							this.style.cursor = 'default';
						}
						break;
					case 'click':
						$(self.GridButtonElement).find('button').button('enable');

						if (e.ctrlKey && e.type == 'click'){
							if ($(this).hasClass('state-active')){
								$(this).removeClass('state-active');
							}
							else {
								$(this).removeClass('state-hover').addClass('state-active');
							}
						}
						else {
							if ($(this).hasClass('state-active')){
								return;
							}

							$(this).parent().find('.state-active').removeClass('state-active');
							$(this).removeClass('state-hover').addClass('state-active');
						}

						if (self.options.onRowClick){
							self.options.onRowClick.apply(this);
						}
						showInfoBox($(this).attr('infobox_id'));
						break;
					case 'refresh':
						$(this).trigger('click', [true]);
						break;
				}
			});

			$(this.GridElement).find('.ui-icon-info').live('click', function () {
				if ($(this).hasClass('active')){
					$('.gridInfoRow').hide();
					$(this).removeClass('active');
				}
				else {
					$('.gridInfoRow').hide();

					$(this).addClass('active');
					$(this).parentsUntil('tbody').next().show();
				}
			});

			$(this.GridElement).find('tr.gridSearchHeaderRow').each(function () {
				$(this).find('.clearFilterIcon').click(function () {
					$(this).parent().find('input').val('');
					$(this).parent().find('select').val('');
					$('.applyFilterButton').click();
				});

				$(this).find('.applyFilterButton').click(function () {
					var getVars = [];
					var ignoreParams = ['action'];
					$(this).parent().parent().find('input, select').each(function () {
						if ($(this).val() != ''){
							getVars.push($(this).attr('name') + '=' + $(this).val());
						}
						ignoreParams.push($(this).attr('name'));
					});
					js_redirect(js_app_link(js_get_all_get_params(ignoreParams) + getVars.join('&')));
				});

				$(this).find('.resetFilterButton').click(function () {
					var ignoreParams = ['action'];
					$(this).parent().parent().find('input, select').each(function () {
						ignoreParams.push($(this).attr('name'));
					});
					js_redirect(js_app_link(js_get_all_get_params(ignoreParams)));
				});
			});

			$(this.GridElement).find('th.ui-grid-sortable-header').each(function () {
				var sortKey = $(this).parent().parent().parent().attr('data-sort_key');
				var sortDirKey = $(this).parent().parent().parent().attr('data-sort_dir_key');

				var sortDir = 'none';
				if ($(this).attr('data-current_sort_direction') == 'desc'){
					sortDir = 'asc';
				} else if ($(this).attr('data-current_sort_direction') == 'asc'){
					sortDir = 'desc';
				}

				var getVars = [];
				getVars.push(sortKey + '=' + $(this).attr('data-sort_by'));
				getVars.push(sortDirKey + '=' + (sortDir == 'none' ? 'desc' : sortDir));

				var sortArrow = $('<a></a>')
					.attr('href', js_app_link(js_get_all_get_params(['action', sortKey, sortDirKey]) + getVars.join('&')))
					.addClass('ui-icon')
					.css({
						'float' : 'right'
					});
				if ($(this).attr('data-current_sort_direction') == 'desc'){
					sortArrow.addClass('ui-icon-triangle-1-s');
				} else if ($(this).attr('data-current_sort_direction') == 'asc'){
					sortArrow.addClass('ui-icon-triangle-1-n');
				}
				else {
					sortArrow.addClass('ui-icon-triangle-2-n-s');
				}

				$(this).append(sortArrow);
			});
		},
		addBodyRow: function (data){
			var $Row = $('<tr class="gridBodyRow"></tr>');
			$.each(data.columns, function () {
				$Row.append('<td class="gridBodyRowColumn">' + this.text + '</td>');
			});

			$(this.GridElement).find('tbody').append($Row);
		},
		getSelectedData: function (dataName){
			return $(this.GridElement).find('.gridBodyRow.state-active').data(dataName);
		}
	});
})(jQuery);