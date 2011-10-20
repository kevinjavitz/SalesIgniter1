<?php
	$QGiftCertificatesTransactionsHistory = Doctrine_Query::create()
	->from('GiftCertificatesTransactionsHistory')
	->where('customers_id = ?', $cID)
	->orderBy('gift_certificates_transaction_history_id DESC')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$htmlTable = htmlBase::newElement('table')
	->setCellPadding(2)
	->setCellSpacing(0)
	->addClass('ui-widget')
	->css(array(
		'width' => '100%'
	));
	$saveForm = htmlBase::newElement('form')
			->attr('name', 'addRemoveGCBalance')
			->attr('id', 'frmaddRemoveGCBalance')
			->attr('method', 'post');
	$gcBalance = htmlBase::newElement('input')
			->setName('gcBalance')
			->setType('text');

	$actionType = htmlBase::newElement('selectbox')
			->setName('actionAddRemove')
			->setId('actionAddRemove');
	$actionType->addOption('', ' Select Action ');
	$actionType->addOption('add', 'ADD');
	$actionType->addOption('deduct', 'DEDUCT');

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

	$TableHidden = htmlBase::newElement('table')
			->setCellPadding(3)
			->setCellSpacing(0)
			->addClass('ui-widget ui-widget-content')
			->css(array(
					   'width' => '100%'
				  ))
			->attr('customers_id', $cID)
			->attr('id', 'manageGCBalanceTable');

	$TableHidden->addBodyRow(array(
								  'columns' => array(
									  array('addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_GIFT_CERTIFICATES_BALANCE')),
									  array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TEXT_PURCHASE_TYPE'))
								  )
							 ));
	$TableHidden->addBodyRow(array(
								  'columns' => array(
									  array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0),'text' => $gcBalance->draw()),
									  array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0),'text' => $purchaseType->draw())
								  )
							 ));
	$addGiftCertificatesBalanceButton = htmlBase::newElement('button')->setText('Add Gift Certificates Balance')->addClass('newButton')->setType('button')->setId('btnAddGCBalance');
	$deductGiftCertificatesBalanceButton = htmlBase::newElement('button')->setText('Deduct Gift Certificates Balance')->addClass('newButton')->setType('button')->setId('btnDeductGCBalance');
	$saveForm->append($TableHidden);
	$saveForm->append($addGiftCertificatesBalanceButton);
	$saveForm->append($deductGiftCertificatesBalanceButton);

	$htmlTable->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main',
						      'css'=>array('text-align:center'),
						      'colspan' => 7,
						      'text' => $saveForm->draw()
                        )
						)));
	$htmlTable->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'pageStackContainer',
						      'css'=>array('text-align:center'),
						      'colspan' => 7,
						      'text' => sysLanguage::get('TABLE_HEADING_GIFT_CERTIFICATES_EARNINGS'))
						)));

	$htmlTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_GIFT_CERTIFICATES_BALANCE')),
			array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_GIFT_CERTIFICATES_PURCHASE_TYPE_VALUES')),
			array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_DATE')),
			array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_ORDER')),
			array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_VALUTEC_CARD_NUMBER')),
			array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_GIFT_CERTIFICATES_ID')),
			array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_TRANSACTION_TYPE'))
		)
	));
	if($QGiftCertificatesTransactionsHistory){
        foreach($QGiftCertificatesTransactionsHistory as $gcInfo){
            $pTypeValues = json_decode($gcInfo['purchase_type_values']);
            $valuesTable = htmlBase::newElement('table')
                    ->setCellPadding(2)
                    ->setCellSpacing(0)
                    ->css(array('width' => '100%'));
            foreach($pTypeValues as $pType => $pTypeValue) {
                $valuesTable->addBodyRow(array(
                                              'columns' => array(
                                                  array('text' => ucwords($pType)),
                                                  array('text' => $currencies->format($pTypeValue))
                                              )
                                         ));
            }
            $htmlTable->addBodyRow(array(
                                        'columns' => array(
                                            array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0), 'text' => $gcInfo['amount']),
                                            array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => $valuesTable->draw()),
                                            array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => date('Y-m-d H:i:s',strtotime($gcInfo['date_added']))),
                                            array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => $gcInfo['orders_id']),
                                            array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => $gcInfo['valutec_card_number']),
                                            array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => $gcInfo['gift_certificates_id']),
                                            array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => $gcInfo['transaction_type'])
                                        )
                                   ));

        }
    }else{
        $htmlTable->addBodyRow(array(
                                    'columns' => array(
                                        array('colspan' => 7, 'addCls' => 'ui-widget-content', 'align' => 'center', 'css' => array('border-top' => 0), 'text' => sysLanguage::get('TEXT_INFO_NO_HISTORY'))
                                    )
                               ));
    }

    $GiftCertificatesCustomersBalance = Doctrine::getTable('GiftCertificatesCustomersBalance')->findByCustomersId((int)$cID);
    if($GiftCertificatesCustomersBalance){
        $htmlTable->addBodyRow(array(
                                    'columns' => array(
                                        array('colspan' => 7, 'text' => '')
                                    )
                               ));
        $htmlTable->addBodyRow(array(
                                    'columns' => array(
                                        array('colspan' => 7,
                                              'addCls' => '',
                                              'align' => 'center',
                                              'css' => array('border-top' => 0,
                                                             'font-size' => '14',
                                                             'font-weight' => 'bold'),
                                              'text' => sysLanguage::get('TABLE_HEADING_GIFT_CERTIFICATES_CURRENT_BALANCE'))
                                    )
                               ));
        $customersBalanceTable = htmlBase::newElement('table')
                ->setCellPadding(2)
                ->setCellSpacing(0)
                ->addClass('ui-widget')
                ->css(array(
                           'width' => '100%'
                      ));
        $customersBalanceTable->addBodyRow(array(
                                                'columns' => array(
                                                    array('addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_PURCHASE_TYPE')),
                                                    array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_PURCHASE_TYPE_VALUE'))
                                                )
                                           ));
	    $columns = array();
        foreach($GiftCertificatesCustomersBalance as $GiftCertificatesCustomersBalanceDetails)
        {
            $columns[] = array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0), 'text' => $GiftCertificatesCustomersBalanceDetails['purchase_type']);
            $columns[] = array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0), 'text' => $GiftCertificatesCustomersBalanceDetails['value']);

        }
	    if(count($columns) > 0){
            $customersBalanceTable->addBodyRow(array(
                                                'columns' => $columns
                                           ));
	    }
        $htmlTable->addBodyRow(array(
                                    'columns' => array(
                                        array('addCls' => 'main',
                                              'css'=>array('text-align:center'),
                                              'colspan' => 7,
                                              'text' => $customersBalanceTable->draw()
                                        )
                                    )));
    }

?>
<div id="page-1"><?php echo $htmlTable->draw(); ?></div>