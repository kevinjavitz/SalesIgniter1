function showAjaxLoader(){
    $('#ajaxLoader').dialog({
        modal: true,
        resizable: false,
        draggable: false,
        position: 'center'
    }).show();
}

function removeAjaxLoader(){
    $('#ajaxLoader').dialog('close');
}
function updateRes(valType){
    var dataArr = new Object();
    dataArr.start_date = $('#start_date').val();
    dataArr.end_date = $('#end_date').val();
    dataArr.filter_status = $('#filterStatus').val();
    dataArr.filter_pay = $('#filterPay').val();
    dataArr.filter_shipping = $('#filterShipping').val();
    dataArr.filter_category = $('#filterCategory').val();
    if ($('#includeReturned:checked').size() > 0){
        dataArr.include_returned = 1;
    }

    if ($('#includeUnsent:checked').size() > 0){
        dataArr.include_unsent = 1;
    }

    $.ajax({
        cache: false,
        dataType: 'html',
        data: dataArr,
        beforeSend: showAjaxLoader,
        complete: removeAjaxLoader,
        url: js_app_link('appExt=payPerRentals&app=return&appPage=default&action=getReturned'),
        success: function (data){
            $('tbody', $('#reservationsTable')).html(data);

        }
    });
}

function exportData(valType){
    var dataArr = [];
    dataArr.push('appExt=payPerRentals');
    dataArr.push('app=return');
    dataArr.push('appPage=default');
    dataArr.push('action=exportData');
    dataArr.push('export=csv');
    dataArr.push('start_date=' + $('#start_date').val());
    dataArr.push('end_date=' + $('#end_date').val());
    dataArr.push('filter_status=' + $('#filterStatus').val());
    dataArr.push('filter_pay=' + $('#filterPay').val());
    dataArr.push('filter_shipping=' + $('#filterShipping').val());
    dataArr.push('filter_category=' + $('#filterCategory').val());
    if ($('#includeReturned:checked').size() > 0){
        dataArr.push('include_returned=1');
    }
    if ($('#includeUnsent:checked').size() > 0){
        dataArr.push('include_unsent=1');
    }

    window.open(js_app_link(dataArr.join('&')));
}

function submitRes(){

    $.ajax({
        cache: false,
        dataType: 'json',
        data: $('#reservationsTable *').serialize(),
        type:'post',
        beforeSend: showAjaxLoader,
        complete: removeAjaxLoader,
        url: js_app_link('appExt=payPerRentals&app=return&appPage=default&action=return'),
        success: function (data){
            $('.returns', $('#reservationsTable')).each(function (){
                if (this.checked){
                    $(this).parent().parent().remove();
                }
            });
        }
    });
}


var dayShortNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

$(document).ready(function (){
    updateRes();
    $('#get_res').click(updateRes);
    $('#return').click(submitRes);
    $('#export_data').click(exportData);
    $('#DP_startDate').datepicker({
        dateFormat: 'yy-mm-dd',
        gotoCurrent: true,
        altField: '#start_date',
        dayNamesMin: dayShortNames
    });

    $('#DP_endDate').datepicker({
        dateFormat: 'yy-mm-dd',
        gotoCurrent: true,
        altField: '#end_date',
        dayNamesMin: dayShortNames
    });
/*
 $('#DP_startDate').datepicker("setDate", $('#start_date1').val());
 $('#DP_endDate').datepicker("setDate", $('#end_date1').val());
 $('#DP_startDate').datepicker( "refresh" );
 $('#DP_endDate').datepicker( "refresh" );
* */

});


