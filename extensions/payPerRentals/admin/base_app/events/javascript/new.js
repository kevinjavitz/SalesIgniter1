
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
});