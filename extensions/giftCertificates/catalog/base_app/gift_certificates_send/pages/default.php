<?php
    if (isset($_POST['back_x']) || isset($_POST['back_y'])){
        $_GET['action'] = '';
    }

    $action = (isset($_GET['action']) ? $_GET['action'] : '');

    if (!empty($action)){
        switch($action){
            case 'send':
                $error = false;
                if (!tep_validate_email(trim($_POST['email']))) {
                    $messageStack->addSession('pageStack', sysLanguage::get('ERROR_ENTRY_EMAIL_ADDRESS_CHECK'), 'error');
                    $error = true;
                }

                if ($error === false){
                    $extGiftCertificate = $appExtension->getExtension('giftCertificates');
                    $currentBalance = $extGiftCertificate->getCustomersBalance($userAccount->getCustomerId(), 'global');

                    $gc_amount = (float)trim($_POST['amount']);
                    if (ereg('[^0-9/.]', $gc_amount)){
                        $messageStack->addSession('pageStack', sysLanguage::get('ERROR_ENTRY_AMOUNT_CHECK'), 'error');
                        $error = true;
                    }
                    if ($gc_amount > $currentBalance || $gc_amount == 0) {
                        $messageStack->addSession('pageStack', sysLanguage::get('ERROR_ENTRY_AMOUNT_CHECK'), 'error');
                        $error = true;
                    }
                }
                break;
            case 'process':

                $data = $_POST;
                $data['customers_id'] = (int)$userAccount->getCustomerId();
                $data['customers_email_address'] = (int)$userAccount->getEmailAddress();
                $data['customers_first_name'] = (int)$userAccount->getFirstName();
                $data['customers_last_name'] = (int)$userAccount->getLastName();
                $extGiftCertificate = $appExtension->getExtension('giftCertificates');
                $id1 = !$extGiftCertificate->sendGiftCertificatesBalanceByEmail($data);
                if(!$id1){
                    $error = true;
                    $action = 'send';
                }
                break;
        }
    }
    ob_start();
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<?php
    if($action == 'process'){
?>
        <div class="ui-widget ui-widget-content ui-corner-all" style="padding:.5em;"><?php echo sysLanguage::get('TEXT_SUCCESS'); ?>
            <br/><br/><?php //echo 'gc ' . $id1; ?></div>
        <div class="ui-widget ui-widget-content ui-corner-all pageButtonBar">
            <a href="<?php echo itw_app_link(null, 'index', 'default', 'NONSSL'); ?>"><?php echo htmlBase::newElement('button')->usePreset('continue')->setType('submit')->draw(); ?></a>
        </div>
        <?php
    }
    if($action == 'send' && $error === false){
        // validate entries
        $gc_amount = (double)$gc_amount;
        $send_name = $userAccount->getFullName();
        ?>
        <tr>
            <td>
                <form action="<?php echo itw_app_link('action=process&appExt=giftCertificates', 'gift_certificates_send', 'default', 'NONSSL'); ?>" method="post">
                 <div class="ui-widget ui-widget-content ui-corner-all" style="padding:.5em;">
                     <table border="0" width="100%" cellspacing="0" cellpadding="2">
                         <tr>
                             <td class="main"><?php echo sprintf(sysLanguage::get('MAIN_MESSAGE'), $currencies->format($_POST['amount']), stripslashes($_POST['to_name']), $_POST['email'], stripslashes($_POST['to_name']), $currencies->format($_POST['amount']), $send_name); ?></td>
                         </tr>
                         <?php
                        if($_POST['message']){
                         ?>
                         <tr>
                             <td class="main"><?php echo sprintf(sysLanguage::get('PERSONAL_MESSAGE'), $userAccount->getFirstName()); ?></td>
                         </tr>
                         <tr>
                             <td class="main"><?php echo stripslashes($_POST['message']); ?></td>
                         </tr>
                         <?php

                     }
                         ?>
                     </table>
                 </div>
                 <?php echo tep_draw_hidden_field('send_name', $send_name) . tep_draw_hidden_field('to_name', stripslashes($_POST['to_name'])) . tep_draw_hidden_field('email', $_POST['email']) . tep_draw_hidden_field('amount', $gc_amount) . tep_draw_hidden_field('message', stripslashes($_POST['message'])); ?>
                    <div class="ui-widget ui-widget-content ui-corner-all pageButtonBar">
                     <?php
                        echo htmlBase::newElement('button')
                        ->setType('submit')
                        ->usePreset('continue')
                        ->setText(sysLanguage::get('TEXT_BUTTON_SEND'))
                        ->draw();
                        echo htmlBase::newElement('button')
                            ->usePreset('back')
                            ->setType('submit')
                            ->setName('back')
                            ->css(array(
                                       'float' => 'left'
                                  ))
                            ->draw();
                     ?></div>
                </form>
            </td>
        </tr>
        <?php
        } else if($action == '' || (isset($error) && $error === true)) {
        $toNameInput = htmlBase::newElement('input')
            ->setName('to_name');
        $toEmailInput = htmlBase::newElement('input')
            ->setName('email');
        $amountInput = htmlBase::newElement('input')
            ->setName('amount');
        $messageInput = htmlBase::newElement('textarea')
            ->setName('message')
            ->attr('cols', '50')
            ->attr('rows', '15');
        if(isset($_POST) && !empty($_POST)){
            $toNameInput->setValue($_POST['to_name']);
            $toEmailInput->setValue($_POST['email']);
            $amountInput->setValue($_POST['amount']);
            $messageInput->html($_POST['message']);
        }
        ?>
        <tr>
         <td>
             <form action="<?php echo itw_app_link('action=send&appExt=giftCertificates', 'gift_certificates_send', 'default', 'NONSSL'); ?>" method="post">
                 <table border="0" width="100%" cellspacing="0" cellpadding="2">
                     <tr>
                         <td class="main"><?php echo sysLanguage::get('ENTRY_NAME'); ?>
                             <br><?php echo $toNameInput->draw();?></td>
                     </tr>
                     <tr>
                         <td class="main"><?php echo sysLanguage::get('ENTRY_EMAIL'); ?>
                             <br><?php echo $toEmailInput->draw(); ?></td>
                     </tr>
                     <tr>
                         <td class="main"><?php echo sysLanguage::get('ENTRY_AMOUNT'); ?>
                             <br><?php echo $amountInput->draw(); ?></td>
                     </tr>
                     <tr>
                         <td class="main"><?php echo sysLanguage::get('ENTRY_MESSAGE'); ?>
                             <br><?php echo $messageInput->draw(); ?></td>
                     </tr>
                 </table>
                 <table border="0" width="100%" cellspacing="0" cellpadding="2">
                     <tr>
                         <td class="main"><?php
                             $back = sizeof($navigation->path) - 2;
                             echo htmlBase::newElement('button')
                                 ->usePreset('back')
                                 ->setHref(itw_app_link( tep_array_to_string($navigation->path[$back]['get'],array('action')), $navigation->path[$back]['page'], '', $navigation->path[$back]['mode']))
                                 ->draw();
                         ?></td>
                         <td class="main" align="right"><?php
                             echo htmlBase::newElement('button')
                                 ->usePreset('continue')
                                 ->setType('submit')
                                 ->draw();
                             ?>
                         </td>
                     </tr>
                 </table>
             </form>
         </td>
        </tr>
     <?php
    }
         ?>
</table>
<?php
$pageContents = ob_get_contents();
ob_end_clean();
$pageContent->set('pageTitle', sysLanguage::get('HEADING_TITLE'));
$pageContent->set('pageContent', $pageContents);
$pageContent->set('pageButtons', $pageButtons);
?>