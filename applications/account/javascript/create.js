$(document).ready(function (){
    $('#loginButton').button();
    $('select[name=country]').live('change', function (){

            var stateType = 'state';
            var $stateColumn = $('#'+stateType);
            if($stateColumn.size() > 0){
                showAjaxLoader($stateColumn, 'large');
                $.ajax({
                    url: js_app_link('app=account&appPage=create&action=getCountryZones'),
                    cache: false,
                    dataType: 'html',
                    data: 'cID=' + $(this).val()+'&state_type='+stateType+'&state='+$stateColumn.val(),
                    success: function (data){
                        removeAjaxLoader($stateColumn);
                        $('#'+stateType).replaceWith(data);
                    }
                });
            }
        });
      $('select[name=country]').trigger('change');
});