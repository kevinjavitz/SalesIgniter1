$(document).ready(function (){
    $('.gridBody > .gridBodyRow').click(function (){
        if ($(this).hasClass('state-active')) return;

        $('.gridButtonBar').find('button').button('enable');
    });
    $('.gridButtonBar').find('.emailButton').click(function (){
        var getVars = [];
        getVars.push('appExt=giftCertificates');
        getVars.push('app=manage_gift_certificates');
        getVars.push('appPage=default');
        getVars.push('action=getActionWindow');
        getVars.push('window=emailGiftCertificates');
        getVars.push('gcID=' + $('.gridBodyRow.state-active').attr('data-gift_certificates_id'));

        gridWindow({
            buttonEl: this,
            gridEl: $('.gridContainer'),
            contentUrl: js_app_link(getVars.join('&')),
            onShow: function (){
                var self = this;

                $(self).find('.cancelButton').click(function (){
                    $(self).effect('fade', {
                        mode: 'hide'
                    }, function (){
                        $('.gridContainer').effect('fade', {
                            mode: 'show'
                        }, function (){
                            $(self).remove();
                        });
                    });
                });

                $(self).find('.sendButton').click(function (){
                    var getVars = [];
                    getVars.push('appExt=giftCertificates');
                    getVars.push('app=manage_gift_certificates');
                    getVars.push('appPage=default');
                    getVars.push('action=sendGiftCertificatesEmail');
                    getVars.push('gcID=' + $('.gridBodyRow.state-active').attr('data-gift_certificates_id'));

                    $.ajax({
                        cache: false,
                        url: js_app_link(getVars.join('&')),
                        dataType: 'json',
                        data: $(self).find('*').serialize(),
                        type: 'post',
                        success: function (data){
                            if (data.success){
                                alert(data.sentTo);
                                $(self).effect('fade', {
                                    mode: 'hide'
                                }, function (){
                                    $('.gridContainer').effect('fade', {
                                        mode: 'show'
                                    }, function (){
                                        $(self).remove();
                                    });
                                });
                            }
                        }
                    });
                });
            }
        });
    });
    $('.gridButtonBar').find('.insertButton, .editButton').click(function (){
        var getVars = [];
        getVars.push('appExt=giftCertificates');
        getVars.push('app=manage_gift_certificates');
        getVars.push('appPage=default');
        getVars.push('action=getActionWindow');
        getVars.push('window=newGiftCertificate');
        if ($(this).hasClass('editButton') && $('.gridBodyRow.state-active').size() > 0){
            getVars.push('gcID=' + $('.gridBodyRow.state-active').attr('data-gift_certificates_id'));
        }

        gridWindow({
            buttonEl: this,
            gridEl: $('.gridContainer'),
            contentUrl: js_app_link(getVars.join('&')),
            onShow: function (){
                var self = this;

                $(self).find('.cancelButton').click(function (){
                    $(self).effect('fade', {
                        mode: 'hide'
                    }, function (){
                        $('.gridContainer').effect('fade', {
                            mode: 'show'
                        }, function (){
                            $(self).remove();
                        });
                    });
                });

                $(self).find('.saveButton').click(function (){
                    var getVars = [];
                    getVars.push('appExt=giftCertificates');
                    getVars.push('app=manage_gift_certificates');
                    getVars.push('appPage=default');
                    getVars.push('action=saveGiftCertificate');
                    if ($('.gridBodyRow.state-active').size() > 0){
                        getVars.push('gcID=' + $('.gridBodyRow.state-active').attr('data-gift_certificates_id'));
                    }

                    $.ajax({
                        cache: false,
                        url: js_app_link(getVars.join('&')),
                        dataType: 'json',
                        data: $(self).find('*').serialize(),
                        type: 'post',
                        success: function (data){
                            if (data.success){
                                if ($('.gridBodyRow.state-active').size() > 0){
                                    $(self).effect('fade', {
                                        mode: 'hide'
                                    }, function (){
                                        $('.gridContainer').effect('fade', {
                                            mode: 'show'
                                        }, function (){
                                            $(self).remove();
                                        });
                                    });
                                }else{
                                    js_redirect(js_app_link('appExt=giftCertificates&app=manage_gift_certificates&appPage=default&gcID=' + data.gcID));
                                }
                            }
                        }
                    });
                });
            }
        });
    });

    $('.gridButtonBar').find('.deleteButton').click(function (){
        var giftCertificatesId = $('.gridBodyRow.state-active').attr('data-gift_certificates_id');
        confirmDialog({
            confirmUrl: js_app_link('appExt=giftCertificates&app=manage_gift_certificates&action=deleteConfirm&gcID=' + giftCertificatesId),
            title: 'Confirm Delete',
            content: 'Are you sure you want to delete this gift certificate?',
            success: function (){
                js_redirect(js_app_link('appExt=giftCertificates&app=manage_gift_certificates&appPage=default'));
            }
        });
    });
});