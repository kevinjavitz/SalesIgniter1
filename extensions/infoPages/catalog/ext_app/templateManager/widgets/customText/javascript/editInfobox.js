$(document).ready(function (){
    $('#page_name').change(function(){
        $("#page_name option:selected").each(function () {
               var go = $(this).attr('go');
                $('#edit_cms_page').attr('href',go);
        });
    });
    $('#page_name').trigger('change');
})