    $(document).ready(function (){
        $('#packageButton').click(function(){
            js_redirect(js_app_link('appExt=fedEX&app=ship_fedex&appPage=default&pkg='+$('#pkg').val()+'&oID='+$('#oID').val()));    
        });
    });