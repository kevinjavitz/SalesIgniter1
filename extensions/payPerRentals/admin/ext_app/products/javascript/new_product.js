$(document).ready(function (){
	$('input[name="reservation_shipping[]"]').click(function (){
		if ($(this).attr('id') != 'noShip' && $(this).attr('id') != 'storeMethods'){
			if ($('#noShip').is(':checked')){
				$('#noShip').get(0).checked = false;
			}
			if ($('#storeMethods').is(':checked')){
				$('#storeMethods').get(0).checked = false;
			}
		}
	});

	$('#noShip').click(function (){
		var $this = $(this);
		if ($this.get(0).checked == true){
			if ($('input[name="reservation_shipping[]"][id!="noShip"]:checked').size() > 0){
				$('input[name="reservation_shipping[]"][id!="noShip"]:checked').each(function (){
					this.checked = false;
				});
			}
		}
	});

	$('#storeMethods').click(function (){
		var $this = $(this);
		if ($this.get(0).checked == true){
			if ($('input[name="reservation_shipping[]"][id!="storeMethods"]:checked').size() > 0){
				$('input[name="reservation_shipping[]"][id!="storeMethods"]:checked').each(function (){
					this.checked = false;
				});
			}
		}
	});
    $('.deleteIcon').live('click', function (){
		$(this).parent().parent().remove();
	});

	$('.deleteIconHidden').live('click', function (){
		$(this).parent().parent().remove();
	});

    $('#types_select').hide();
    $(this).find('.insertIcon').click(function () {
        var nextId = $(this).parent().parent().parent().parent().parent().attr('data-next_id');
        var langId = $(this).parent().parent().parent().parent().parent().attr('language_id');
        $(this).parent().parent().parent().parent().parent().attr('data-next_id', parseInt(nextId) + 1);

        var $td1 = $('<div style="float:left;width:150px;"></div>').append('<input class="ui-widget-content" style="width:100%;" type="text" name="pprp[' + nextId + '][details][' + langId + ']">');
        var $td2 = $('<div style="float:left;width:80px;"></div>').attr('align', 'center').append('<input class="ui-widget-content" size="8" type="text" name="pprp[' + nextId + '][number_of]">');
        var $td5 = $('<div style="float:left;width:80px;"></div>').attr('align', 'center').append('<input size="3" class="ui-widget-content" type="text" name="pprp[' + nextId + '][price]">');
        var sb =$('<select name="pprp[' + nextId + '][type]"></select>');
        $('#types_select option').clone().appendTo(sb);
        var $td51 = $('<div style="float:left;width:100px;"></div>').attr('align', 'center').append(sb);
        var $td9 = $('<div style="float:left;width:40px;"></div>').attr('align', 'center').append('<a class="ui-icon ui-icon-closethick deleteIcon"></a>');
        var $newTr = $('<li></li>').append($td2).append($td51).append($td5).append($td1).append($td9).append('<br style="clear:both;"/>');//<input type="hidden" name="sortvprice[]">
        $(this).parent().parent().parent().parent().parent().find('.sortableList').append($newTr);
    });

	  $(this).find('.insertIconHidden').click(function () {
        var nextId = $(this).parent().parent().parent().parent().parent().attr('data-next_id');
        var langId = $(this).parent().parent().parent().parent().parent().attr('language_id');
        $(this).parent().parent().parent().parent().parent().attr('data-next_id', parseInt(nextId) + 1);


        var $td2 = $('<div style="float:left;width:80px;"></div>').attr('align', 'center').append('<input class="ui-widget-content date_hidden" size="15" type="text" name="pprhidden[' + nextId + '][start_date]">');
        var $td5 = $('<div style="float:left;width:80px;"></div>').attr('align', 'center').append('<input class="ui-widget-content date_hidden" size="15" type="text" name="pprhidden[' + nextId + '][end_date]">');
        var $td9 = $('<div style="float:left;width:40px;"></div>').attr('align', 'center').append('<a class="ui-icon ui-icon-closethick deleteIconHidden"></a>');
        var $newTr = $('<li style="list-style:none"></li>').append($td2).append($td5).append($td9).append('<br style="clear:both;"/>');//<input type="hidden" name="sortvprice[]">
        $(this).parent().parent().parent().parent().parent().find('.hiddenList').append($newTr);
		$('.date_hidden').datepicker({
					dateFormat: 'yy-mm-dd',
					gotoCurrent: true
		});
    });
	$('.date_hidden').datepicker({
					dateFormat: 'yy-mm-dd',
					gotoCurrent: true
	});

    //$('.pricePPR').sortable('refresh');
    $('.sortableList').sortable();
});