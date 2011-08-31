$(document).ready(function (){
	var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);

	$(".productListingColBoxContent_image").each(function() {
		var $thisImage = $(this);
		if(!$('#desc' + $(this).attr('pID')).html()){
			$.ajax({
				url: js_app_link(linkParams + 'rType=ajax&app=products&appPage=all&action=getTooltipWindow'),
				data: 'pID=' + $thisImage.attr('pID'),
				type: 'post',
				dataType: 'json',
				success: function (data) {
					$('body').append('<div id="'+'desc' + $thisImage.attr('pID')+'" style="display:none">'+data.pageHtml+'</div>');
					//$('#desc' + $(this).attr('pID')).html(data.pageHtml);
					$thisImage.mopTip({'w':350,'style':"overClick",'get':"#desc" + $thisImage.attr('pID')});
				}
			});
		}

	});
});