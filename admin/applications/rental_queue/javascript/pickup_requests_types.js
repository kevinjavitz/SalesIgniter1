$(document).ready(function (){
	$('.deleteIconPickup').live('click', function (){
		$(this).parent().parent().remove();
	});

	$(this).find('.insertIconPickup').click(function () {
		var nextId = $(this).parent().parent().parent().parent().parent().attr('data-next_id');
		var langId = $(this).parent().parent().parent().parent().parent().attr('language_id');
		$(this).parent().parent().parent().parent().parent().attr('data-next_id', parseInt(nextId) + 1);


		var $td2 = $('<div style="float:left;width:150px;"></div>').attr('align', 'center').append('<input class="ui-widget-content pickup_type" size="15" type="text" name="pickup[' + nextId + '][type_name]">');
		var $td9 = $('<div style="float:left;width:40px;"></div>').attr('align', 'center').append('<a class="ui-icon ui-icon-closethick deleteIconPickup"></a>');
		var $newTr = $('<li style="list-style:none"></li>').append($td2).append($td9).append('<br style="clear:both;"/>');//<input type="hidden" name="sortvprice[]">
		$(this).parent().parent().parent().parent().parent().find('.pickupList').append($newTr);

	});
});