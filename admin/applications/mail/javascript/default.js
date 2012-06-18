$(document).ready(function (){
    $('.sendMail').live('click', function (){
        var errors = '';
        if($('.customerMail option:selected').val() == '') {
            errors += '- Please select Customer email\n';
        }
        if($('.fromEmail').val() == '') {
            errors += '- Please type From email\n';
        }
        if($('.subjectEmail').val() == '') {
            errors += '- Please type Subject email\n';
        }
        if($('.messageEmail').val() == '') {
            errors += '- Please type Message email\n';
        }
        if(errors != '') {
            alert(errors);
            return false;
        }
    });
});