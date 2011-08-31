    $(document).ready(function (){
	    $('#countryDrop').change(function (){
		    var $stateColumn = $('#stateCol');
		    showAjaxLoader($stateColumn);

		    $.ajax({
			    cache: true,
			    url: js_app_link('appExt=upsLabels&app=ship_ups&appPage=default&rType=ajax&action=getCountryZones'),
			    data: 'cID=' + $(this).val() + '&zName='+$('#stateCol input').val(),
			    dataType: 'html',
			    success: function (data){
				    removeAjaxLoader($stateColumn);
				    $('#stateCol').html(data);
			    }
		    });
	    });

	    $('#countryDrop').trigger('change');
    });