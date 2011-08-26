
$(document).ready(function (){
	$('#countryDrop').change(function (){
		var $stateColumn = $('#stateCol');
		showAjaxLoader($stateColumn, 'icon', 'append');
		
		$.ajax({
			cache: true,
			url: js_app_link('appExt=payPerRentals&app=events&appPage=default&rType=ajax&action=getCountryZones'),
			data: 'cID=' + $(this).val() + '&zName='+$('#ezone').val(),
			dataType: 'html',
			success: function (data){
				removeAjaxLoader($stateColumn);
				$('#stateCol').html(data);
			}
		});
	});
	$('#countryDrop').val('223').trigger('change');
	$('#tab_container').tabs();
	$('#events_date').datepicker({dateFormat: 'yy-mm-dd'});
	$('.makeFCK').each(function (){
		CKEDITOR.replace(this, {
			filebrowserBrowseUrl: DIR_WS_ADMIN + 'rentalwysiwyg/editor/filemanager/browser/default/browser.php'
		});
	});

	$('.deleteIconHidden').live('click', function (){
		$(this).parent().parent().remove();
	});

	$(this).find('.insertIconHidden').click(function () {
		var nextId = $(this).parent().parent().parent().parent().parent().attr('data-next_id');
		var langId = $(this).parent().parent().parent().parent().parent().attr('language_id');
		$(this).parent().parent().parent().parent().parent().attr('data-next_id', parseInt(nextId) + 1);


		var $td2 = $('<div style="float:left;width:80px;"></div>').attr('align', 'center').append('<input class="ui-widget-content prod_model" size="15" type="text" name="event_products[' + nextId + '][products_model]">');
		var $td5 = $('<div style="float:left;width:80px;"></div>').attr('align', 'center').append('<input class="ui-widget-content" size="15" type="text" name="event_products[' + nextId + '][qty]">');
		var $td9 = $('<div style="float:left;width:40px;"></div>').attr('align', 'center').append('<a class="ui-icon ui-icon-closethick deleteIconHidden"></a>');
		var $newTr = $('<li style="list-style:none"></li>').append($td2).append($td5).append($td9).append('<br style="clear:both;"/>');//<input type="hidden" name="sortvprice[]">
		$(this).parent().parent().parent().parent().parent().find('.hiddenList').append($newTr);
		$('.prod_model').keyup(function() {

			var link = js_app_link('appExt=payPerRentals&app=events&appPage=new&action=getModels');
			var $barInput = $(this);
			$(this).autocomplete({
				source: function(request, response) {
					$.ajax({
						url: link,
						data: 'term='+request.term,
						dataType: 'json',
						type: 'POST',
						success: function(data){
							response(data);
						}
					});
				},
				minLength: 0,
				select: function(event, ui) {
					$barInput.val(ui.item.label);
					return false;
				}
			});
		});

		$('.prod_model').focus(function(){
			if($(this).val() == ''){
				$(this).keyup().autocomplete("search", "");
			}
		});
	});

	$('.prod_model').keyup(function() {

		var link = js_app_link('appExt=payPerRentals&app=events&appPage=new&action=getModels');
		var $barInput = $(this);
		$(this).autocomplete({
			source: function(request, response) {
				$.ajax({
					url: link,
					data: 'term='+request.term,
					dataType: 'json',
					type: 'POST',
					success: function(data){
						response(data);
					}
				});
			},
			minLength: 0,
			select: function(event, ui) {
				$barInput.val(ui.item.label);
				return false;
			}
		});
	});

	$('.prod_model').focus(function(){
		if($(this).val() == ''){
			$(this).keyup().autocomplete("search", "");
		}
	});


});