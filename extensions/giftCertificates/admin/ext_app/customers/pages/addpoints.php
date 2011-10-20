<?php
$saveForm = htmlBase::newElement('form')
        ->attr('name', 'addRemoveGCBalance')
        ->attr('action', itw_app_link('appExt=giftCertificates&action=save', 'update_gc_balance', 'default'))
        ->attr('method', 'post');

    $Qcustomers =Doctrine_Query::create()
            ->select('customers_id, CONCAT(customers_firstname, " ", customers_lastname) as customers_name, customers_email_address')
            ->from('Customers')
            ->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
    $customer = htmlBase::newElement('selectbox')
            ->setName('customerID')
            ->setId('customerID')
    ;
    $customer->addOption('', ' Select Customer ');
    foreach($Qcustomers as $cInfo){
        $customer->addOption($cInfo['customers_id'], '('.$cInfo['customers_id'].') '.$cInfo['customers_name'].' ['.$cInfo['customers_email_address'].']');
    }
    $gcBalance = htmlBase::newElement('input')
            ->setName('gcBalance');

    $action = htmlBase::newElement('selectbox')
            ->setName('actionAddRemove')
            ->setId('actionAddRemove')
    ;
    $action->addOption('', ' Select Action ');
    $action->addOption('add', 'ADD');
    $action->addOption('deduct', 'DEDUCT');

    $purchaseType = htmlBase::newElement('selectbox')
            ->setName('purchaseType');
    //$purchaseType->addOption('', ' Select purchase type ');
    foreach($purchaseTypeNames as $pType => $pTypeName){
        switch($ptype){
            case 'reservation':
                if (defined('EXTENSION_PAY_PER_RENTALS_ENABLED') && EXTENSION_PAY_PER_RENTALS_ENABLED == 'True'){
                    $purchaseType->addOption($pType, $pTypeName);
                }
                break;
            case 'stream':
                if (defined('EXTENSION_STREAMPRODUCTS_ENABLED') && EXTENSION_STREAMPRODUCTS_ENABLED == 'True'){
                    $purchaseType->addOption($pType, $pTypeName);
                }
                break;
            case 'download':
                if (defined('EXTENSION_DOWNLOADPRODUCTS_ENABLED') && EXTENSION_DOWNLOADPRODUCTS_ENABLED == 'True'){
                    $purchaseType->addOption($pType, $pTypeName);
                }
                break;
            default:
                $purchaseType->addOption($pType, $pTypeName);
                break;
        }
    }
    $inputTable = htmlBase::newElement('table')
            ->setCellPadding(2)
            ->setCellSpacing(0);

    $inputTable->addBodyRow(array(
                                 'columns' => array(
                                     array('text' => sysLanguage::get('TEXT_SELECT_CUSTOMER')),
                                     array('text' => $customer->draw())
                                 )
                            ));
    $inputTable->addBodyRow(array(
                                 'columns' => array(
                                     array('text' => sysLanguage::get('TEXT_SELECT_ACTION')),
                                     array('text' => $action->draw())
                                 )
                            ));
    $inputTable->addBodyRow(array(
                                 'columns' => array(
                                     array('text' => sysLanguage::get('TEXT_GIFT_CERTIFICATES_BALANCE_ADD')),
                                     array('text' => $gcBalance->draw())
                                 )
                            ));
    $inputTable->addBodyRow(array(
                                 'columns' => array(
                                     array('text' => sysLanguage::get('TEXT_PURCHASE_TYPE')),
                                     array('text' => $purchaseType->draw())
                                 )
                            ));

    $saveButton = htmlBase::newElement('button')->setText('Save')->addClass('newButton')->setType('submit');
    $saveForm->append($inputTable);
    $saveForm->append($saveButton);

?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
<br />
<div style="text-align:right;" class="gridContainer">
    <?php echo $saveForm->draw(); ?>
</div>