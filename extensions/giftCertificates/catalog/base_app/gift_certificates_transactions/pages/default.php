<?php
    $QGiftCertificatesTransactionsHistory = Doctrine_Query::create()
        ->from('GiftCertificatesTransactionsHistory')
        ->where('customers_id = ?', $userAccount->getCustomerId())
        ->orderBy('gift_certificates_transaction_history_id DESC')
        ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

    $htmlTable = htmlBase::newElement('table')
            ->setCellPadding(2)
            ->setCellSpacing(0)
            ->addClass('ui-widget')
            ->css(array(
                       'width' => '100%'
                  ));
    
    $htmlTable->addBodyRow(array(
                                'columns' => array(
                                    array('addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_GIFT_CERTIFICATES_BALANCE')),
                                    array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_GIFT_CERTIFICATES_PURCHASE_TYPE_VALUES')),
                                    array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_DATE')),
                                    array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_ORDER')),
                                    array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_VALUTEC_CARD_NUMBER')),
                                    array('addCls' => 'ui-widget-header', 'css' => array('border-left' => 0), 'text' => sysLanguage::get('TABLE_HEADING_GIFT_CERTIFICATES')),
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
                                            array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0, 'border-left' => 0), 'text' => ((int)$gcInfo['gift_certificates_id'] > 0 ?'Yes' : 'No')),
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

    $GiftCertificatesCustomersBalance = Doctrine::getTable('GiftCertificatesCustomersBalance')->findByCustomersId((int)$userAccount->getCustomerId());
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
            $columns[] = array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0), 'text' => $purchaseTypeNames[$GiftCertificatesCustomersBalanceDetails['purchase_type']]);
            $columns[] = array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 0), 'text' => $currencies->format($GiftCertificatesCustomersBalanceDetails['value']));

        }
	    if(count($columns) >0){
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
    $pageTitle = sysLanguage::get('TABLE_HEADING_GIFT_CERTIFICATES_TRANSACTIONS_HISTORY');
    $pageContents = $htmlTable->draw();

    $pageButtons = htmlBase::newElement('button')
        ->usePreset('back')
        ->setHref(itw_app_link(null,'account','default'))
        ->draw();

    $pageContent->set('pageTitle', $pageTitle);
    $pageContent->set('pageContent', $pageContents);
    $pageContent->set('pageButtons', $pageButtons);