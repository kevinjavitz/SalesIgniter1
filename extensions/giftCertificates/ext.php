<?php
/*
	Royalties System Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
    class Extension_giftCertificates extends ExtensionBase {

        public function __construct(){
            parent::__construct('giftCertificates');
        }

        public function init(){
            global $appExtension;

            if ($appExtension->isAdmin()){
                //EventManager::attachEvents(array('BoxMarketingAddLink'), null, $this);
            }

            EventManager::attachEvents(array(
                                            'NewOrderBeforeSave',
                                            'CheckoutPreInit',
                                            'CheckoutProcessPostProcess',
                                            'CheckoutAddBlockAfterCart',
                                            'CheckoutAddBlockBeforeOrderTotalsTop',
                                       ), null, $this);
        }

        public function CheckoutPreInit(){
            if(Session::exists('giftCertificate_balance'))
                Session::remove('giftCertificate_balance');
        }

        public function CheckoutAddBlockBeforeOrderTotalsTop(){
            global $onePageCheckout, $currencies;
            if ($onePageCheckout->isGiftCertificateCheckout()){
                $giftCertificatesDiv = htmlBase::newElement('div')->setId('giftCertificates');

                $productTable = htmlBase::newElement('table')->css('width', '100%')->setCellPadding(3)->setCellSpacing(0);

                $productTableHeader = array(
                    array('text' => '&nbsp;'),
                    array('text' => '<b>' . sysLanguage::get('TABLE_HEADING_GIFT_CERTIFICATES_NAME') . '</b>'),
                    array('text' => '<b>' . sysLanguage::get('TABLE_HEADING_GIFT_CERTIFICATES_DESCRIPTION') . '</b>'),
                    array('text' => '<b>' . sysLanguage::get('TABLE_HEADING_GIFT_CERTIFICATES_PRICE') . '</b>'),
                    array('text' => '<b>' . sysLanguage::get('TABLE_HEADING_GIFT_CERTIFICATES_VALUE') . '</b>')
                );

                $productTable->addBodyRow(array(
                                               'columns' => $productTableHeader
                                          ));

                $giftCertificates = Doctrine_Query::create()
                        ->from('GiftCertificates gc')
                        ->leftJoin('gc.GiftCertificatesDescription gcd')
                        ->leftJoin('gc.GiftCertificatesToPurchaseTypes gcpt')
                        ->leftJoin('gc.TaxRates tt')
                        ->where('gcd.language_id=?', (int)Session::get('languages_id'))
                        ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
                if ($giftCertificates){
                    foreach($giftCertificates as $gcInfo){
                        $valuesTable = htmlBase::newElement('table')
                                ->setCellPadding(2)
                                ->setCellSpacing(0)
                                ->css(array('width' => '100%'));
                        foreach($gcInfo['GiftCertificatesToPurchaseTypes'] as $giftCertificatesPurchaseType) {
                            $valuesTable->addBodyRow(array(
                                                          'columns' => array(
                                                              array('text' => ucwords($giftCertificatesPurchaseType['purchase_type'])),
                                                              array('text' => $currencies->format($giftCertificatesPurchaseType['gift_certificates_value']))
                                                          )
                                                     ));
                        }

                        $checkBox = htmlBase::newElement('radio')
                                ->addClass('giftCertificates')
                                ->setName('gcID')
                                ->val($gcInfo['gift_certificates_id']);
                        $productTableBody = array(
                            array('text' => $checkBox),
                            array('text' => $gcInfo['GiftCertificatesDescription'][0]['gift_certificates_name']),
                            array('text' => $gcInfo['GiftCertificatesDescription'][0]['gift_certificates_description']),
                            array('text' => $currencies->format($gcInfo['gift_certificates_price']), 'align' => 'center'),
                            array('text' => $valuesTable->draw())

                        );

                        $productTableBody[] = array('addCls' => 'last', 'text' => '&nbsp');

                        $productTable->addBodyRow(array('columns' => $productTableBody));
                    }
                }
                $giftCertificatesDiv->html($productTable->draw());
                return $giftCertificatesDiv->draw();
            }
            return '';
        }

        function CheckoutAddBlockAfterCart($rType = false){
            global $userAccount, $onePageCheckout, $ShoppingCart, $currencies;
            if($onePageCheckout->isGiftCertificateCheckout() === true){
                return '';
            }
            ###############################

            $Module = OrderTotalModules::getModule('giftCertificate');
            if ($Module !== false && $Module->isEnabled() === true){
                $purchaseTypes['global'] = 0;
                foreach($ShoppingCart->getProducts() as $cartProduct) {
                    $purchaseType = $cartProduct->getPurchaseType();
                    $purchaseTypes[$purchaseType] += $cartProduct->getPrice();
                    $purchaseTypes['global'] += $cartProduct->getPrice();
                }

                if (is_object($onePageCheckout) && count($onePageCheckout->onePage['info']['shipping']) > 0){
	                $purchaseTypes['global'] += $onePageCheckout->onePage['info']['shipping']['cost'];
                }

                $htmlTable = htmlBase::newElement('table')
                        ->setCellPadding(0)
                        ->setCellSpacing(0)
                        ->addClass('ui-widget')
                        ->css(array(
                                   'width' => '100%'
                              ));
                $inputValutec = htmlBase::newElement('input')
                        ->setName('redeem_gift_certificate_code');
                $btnRedeemCertificate = htmlBase::newElement('div')
                        ->attr('id','gcRedeem')
                        ->html(sysLanguage::get('REDEEM'))
                        ->setName('gcRedeem');
                        //->css(array('padding' => '.5em'));
                $htmlTable->addBodyRow(array(
                    'columns' => array(
                        array('addCls' => 'main',
                              'align' => 'left',
                              'text' => sysLanguage::get('TEXT_REDEEM_VALUTEC_COUPON'))
                )));
                $htmlTable->addBodyRow(array(
                    'columns' => array(
                        array('addCls' => 'main',
                              'align' => 'left',
                              'text' => $inputValutec->draw() . '&nbsp;' . $btnRedeemCertificate->draw())
                )));
                if($purchaseTypes){
                    foreach($purchaseTypes as $purchaseType => $sum){
                        $userGiftCertificateBalance = $this->getCustomersBalance($userAccount->getCustomerId(),$purchaseType);
                        if($userGiftCertificateBalance > 0){
                            $userGiftCertificateBalanceEarned = sprintf(sysLanguage::get('TEXT_GIFT_CERTIFICATES_USE_'.strtoupper($purchaseType)),$currencies->format($userGiftCertificateBalance));

                            $redeemGiftCertificateBalance = htmlBase::newElement('checkbox')
                                    ->setName('redeem_gift_certificate_balance[]')
                                    ->setValue(($sum >= $userGiftCertificateBalance ? $userGiftCertificateBalance : $sum))
                                    ->attr('purchase_type',$purchaseType);
                            $htmlTable->addBodyRow(array(
                                'columns' => array(
                                    array('addCls' => 'main',
                                        'css'=>array('text-align:left'),
                                        'text' => $redeemGiftCertificateBalance->draw() . $userGiftCertificateBalanceEarned)
                            )));
                        }
                    }
                }
            } else {
                return false;
            }
            ###############################
            if(!$rType){
                $giftCertificatesTableDiv = htmlBase::newElement('div')->setId('giftCertificatesTable');
                echo $giftCertificatesTableDiv->html($htmlTable->draw())
                    ->draw();
            }else{
                return $htmlTable->draw();
            }
        }

        public function NewOrderBeforeSave( &$order, &$newOrder){
            global $onePageCheckout;
            if (!isset($onePageCheckout) || !is_object($onePageCheckout)){
                return false;
            }
            if($onePageCheckout->isGiftCertificateCheckout() !== true){
                return false;
            }
            $order->info['is_gift_certificate'] = 1;
            $newOrder->is_gift_certificate = 1;

        }

        public function CheckoutProcessPostProcess(&$order, &$products_ordered){
            global $currencies, $onePageCheckout, $userAccount;

            if (!isset($onePageCheckout) || !is_object($onePageCheckout)){
                return false;
            }
            if($onePageCheckout->isGiftCertificateCheckout() === true){
                $gift_certificates_id = $onePageCheckout->onePage['giftCertificate']['gift_certificates_id'];

                for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
                    //$order->insertMembershipProduct($order->products[$i], &$products_ordered);
                    $pInfo = $order->products[$i];
                    $products_ordered .= sprintf("%s x %s (%s) = %s\n",
                                                 $pInfo['quantity'],
                                                 $pInfo['name'],
                                                 $pInfo['model'],
                                                 $currencies->display_price(
                                                     $pInfo['final_price'],
                                                     $pInfo['tax'],
                                                     $pInfo['quantity']
                                                 )
                    );

                    $newOrdersProduct = new OrdersProducts();
                    $newOrdersProduct->orders_id = $order->newOrder['orderID'];
                    $newOrdersProduct->products_id = $pInfo['id'];
                    $newOrdersProduct->gift_certificates_id = (int)$gift_certificates_id;
                    $newOrdersProduct->products_model = $pInfo['model'];
                    $newOrdersProduct->products_name = $pInfo['name'];
                    $newOrdersProduct->products_price = $pInfo['price'];
                    $newOrdersProduct->final_price = $pInfo['final_price'];
                    $newOrdersProduct->products_tax = $pInfo['tax'];
                    $newOrdersProduct->products_quantity = $pInfo['quantity'];
                    $newOrdersProduct->purchase_type = $pInfo['purchase_type'];
                    $newOrdersProduct->save();
                }
                $transactionData = array(
                    'type' => 'order',
                    'orders_id' => $order->newOrder['orderID'],
                    'customers_id' => $userAccount->getCustomerId(),
                    'gift_certificates_id' => $gift_certificates_id
                );
                $this->insertGiftCertificateTransaction($transactionData);
            } else {
                if(!Session::exists('giftCertificate_balance') || count(Session::get('giftCertificate_balance')) <= 0)
                    return false;
                $amountTotal = 0;

                $transactionData = array(
                    'type' => 'applied',
                    'orders_id' => $order->newOrder['orderID'],
                    'values' => Session::get('giftCertificate_balance'),
                    'transaction_type' => '-',
                    'customers_id' => $userAccount->getCustomerId()
                );
                foreach($transactionData['values'] as $total){
                    $amountTotal += $total;
                }
                $transactionData['amount'] = $amountTotal;
                $this->insertGiftCertificateTransaction($transactionData);
                Session::remove('giftCertificate_balance');
            }
        }

        public function insertGiftCertificateTransaction($transactionData){
            switch($transactionData['type']){
                case 'order':
                    $GiftCertificatesInfo = Doctrine::getTable('GiftCertificates')
                            ->findOneByGiftCertificatesId((int)$transactionData['gift_certificates_id']);
                    $transactionData['values'] = false;
                    if(count($GiftCertificatesInfo->GiftCertificatesToPurchaseTypes) > 0){
                        foreach($GiftCertificatesInfo->GiftCertificatesToPurchaseTypes as $GiftCertificatesToPurchaseType){
                            $transactionData['values'][$GiftCertificatesToPurchaseType->purchase_type] = $GiftCertificatesToPurchaseType->gift_certificates_value;
                        }
                    }

                    $newGiftCertificatesTransactionsHistory = new GiftCertificatesTransactionsHistory();
                    $newGiftCertificatesTransactionsHistory->orders_id = $transactionData['orders_id'];
                    $newGiftCertificatesTransactionsHistory->customers_id = $transactionData['customers_id'];
                    $newGiftCertificatesTransactionsHistory->gift_certificates_id = $transactionData['gift_certificates_id'];
                    $newGiftCertificatesTransactionsHistory->amount = $GiftCertificatesInfo['gift_certificates_price'];
                    $newGiftCertificatesTransactionsHistory->purchase_type_values = json_encode($transactionData['values']);
                    $newGiftCertificatesTransactionsHistory->save();
                    $this->updateCustomersBalance($transactionData);
                    break;

                case 'valutec':
                    $newGiftCertificatesTransactionsHistory = new GiftCertificatesTransactionsHistory();
                    $newGiftCertificatesTransactionsHistory->valutec_card_number = $transactionData['valutec_card_number'];
                    $newGiftCertificatesTransactionsHistory->customers_id = $transactionData['customers_id'];
                    $newGiftCertificatesTransactionsHistory->amount = $transactionData['amount'];
                    $newGiftCertificatesTransactionsHistory->purchase_type_values = json_encode($transactionData['values']);
                    $newGiftCertificatesTransactionsHistory->save();
                    $this->updateCustomersBalance($transactionData);
                    break;
                case 'applied':

                    $newGiftCertificatesTransactionsHistory = new GiftCertificatesTransactionsHistory();
                    $newGiftCertificatesTransactionsHistory->orders_id = $transactionData['orders_id'];
                    $newGiftCertificatesTransactionsHistory->customers_id = $transactionData['customers_id'];
                    $newGiftCertificatesTransactionsHistory->amount = $transactionData['amount'];
                    $newGiftCertificatesTransactionsHistory->purchase_type_values = json_encode($transactionData['values']);
                    $newGiftCertificatesTransactionsHistory->transaction_type = $transactionData['transaction_type'];

                    $newGiftCertificatesTransactionsHistory->save();
                    $this->updateCustomersBalance($transactionData);
                    break;
                case 'giftCertificateSentByEmail':
                    $newGiftCertificatesTransactionsHistory = new GiftCertificatesTransactionsHistory();
                    $newGiftCertificatesTransactionsHistory->gift_certificates_id = $transactionData['gift_certificates_id'];
                    $newGiftCertificatesTransactionsHistory->customers_id = $transactionData['customers_id'];
                    $newGiftCertificatesTransactionsHistory->amount = $transactionData['amount'];
                    $newGiftCertificatesTransactionsHistory->purchase_type_values = json_encode($transactionData['values']);
                    $newGiftCertificatesTransactionsHistory->transaction_type = $transactionData['transaction_type'];

                    $newGiftCertificatesTransactionsHistory->save();
                    $this->updateCustomersBalance($transactionData);
                    break;
                case 'redeemGV':

                    $newGiftCertificatesTransactionsHistory = new GiftCertificatesTransactionsHistory();
                    $newGiftCertificatesTransactionsHistory->customers_id = $transactionData['customers_id'];
                    $newGiftCertificatesTransactionsHistory->gift_certificates_id = $transactionData['gift_certificates_id'];
                    $newGiftCertificatesTransactionsHistory->amount = $transactionData['gift_certificates_price'];
                    $newGiftCertificatesTransactionsHistory->purchase_type_values = json_encode($transactionData['values']);
                    $newGiftCertificatesTransactionsHistory->save();
                    $this->updateCustomersBalance($transactionData);
                    $this->InsertCustomersRedeemTrack($transactionData);
                    break;
                default:
                    break;
            }
        }

        public function InsertCustomersRedeemTrack($transactionData){
            $newGiftCertificatesRedeemTrack = new GiftCertificatesRedeemTrack;
            $newGiftCertificatesRedeemTrack->customers_id = $transactionData['customers_id'];
            $newGiftCertificatesRedeemTrack->gift_certificates_id = $transactionData['gift_certificates_id'];
            $newGiftCertificatesRedeemTrack->save();
        }

        public function updateCustomersBalance($transactionData){
            if(count($transactionData['values']) > 0){
                foreach($transactionData['values'] as $puchase_type => $value){
                    $GiftCertificatesInfo = Doctrine::getTable('GiftCertificatesCustomersBalance')
                            ->findOneByCustomersIdAndPurchaseType((int)$transactionData['customers_id'], $puchase_type);

                    if($GiftCertificatesInfo){
                        if(!isset($transactionData['transaction_type']) || $transactionData['transaction_type'] == '+'){
                            $GiftCertificatesInfo->value += $value;
                        } else {
                            $GiftCertificatesInfo->value -= $value;
                        }

                        $GiftCertificatesInfo->save();
                    } else {
                        $newGiftCertificatesCustomersBalance = new GiftCertificatesCustomersBalance();
                        $newGiftCertificatesCustomersBalance->customers_id = (int)$transactionData['customers_id'];
                        $newGiftCertificatesCustomersBalance->purchase_type = $puchase_type;
                        if(!isset($transactionData['transaction_type']) || $transactionData['transaction_type'] == '+'){
                            $newGiftCertificatesCustomersBalance->value = $value;
                        } else {
                            $newGiftCertificatesCustomersBalance->value -= $value;
                        }
                        $newGiftCertificatesCustomersBalance->save();
                    }
                }
            }
        }

        public function getCustomersBalance($customers_id, $purchase_type = null){
            $GiftCertificatesCustomersBalanceTable = Doctrine::getTable('GiftCertificatesCustomersBalance');
            $return = false;
            if($purchase_type !== null){
                $GiftCertificatesCustomersBalance = $GiftCertificatesCustomersBalanceTable->findOneByCustomersIdAndPurchaseType($customers_id, $purchase_type);
                return $GiftCertificatesCustomersBalance->value;
            } else {
                $GiftCertificatesCustomersBalance = $GiftCertificatesCustomersBalanceTable->findByCustomersId($customers_id);
                foreach($GiftCertificatesCustomersBalance as $GiftCertificatesCustomersBalanceData){
                    $return[$GiftCertificatesCustomersBalanceData->purchase_type] = $GiftCertificatesCustomersBalanceData->value;
                }
            }
            return $return;
        }

        public function redeemGiftVoucher($code){
            global $userAccount;
            if(empty($code) || strlen($code) < 10)
                return array('errorMessage' => 'Invalid gift certificate please recheck the coupon code and try again');

            $GiftCertificatesInfo = Doctrine::getTable('GiftCertificates')->findOneByGiftCertificatesCodeAndGiftCertificatesRedeemedAndGiftCertificatesStatus($code,'N','Y');
            if(!$GiftCertificatesInfo){
                return $this->redeemValutecGiftVoucher($code);
            }else{
                $transactionData = array(
                    'type' => 'redeemGV',
                    'transaction_type' => '+',
                    'customers_id' => $userAccount->getCustomerId(),
                    'gift_certificates_id' => $GiftCertificatesInfo->gift_certificates_id,
                    'amount' => $GiftCertificatesInfo->gift_certificates_price,
                );
                if(count($GiftCertificatesInfo->GiftCertificatesToPurchaseTypes) > 0){
                    foreach($GiftCertificatesInfo->GiftCertificatesToPurchaseTypes as $GiftCertificatesToPurchaseType){
                        $transactionData['values'][$GiftCertificatesToPurchaseType->purchase_type] = $GiftCertificatesToPurchaseType->gift_certificates_value;
                    }
                }
                $this->insertGiftCertificateTransaction($transactionData);
                $GiftCertificatesInfo->gift_certificates_redeemed = 'Y';
                $GiftCertificatesInfo->gift_certificates_status = 'N';
                $GiftCertificatesInfo->save();
                return $transactionData;
            }
        }

        public function redeemValutecGiftVoucher($cardNumber){
            global $userAccount;
            $identifier = substr(sha1(time()),0,10);
            $input = array
            (
                'ClientKey' => sysConfig::get('EXTENSION_GIFT_CERTIFICATES_VALUTEC_CLIENT_KEY'),
                'TerminalID' => sysConfig::get('EXTENSION_GIFT_CERTIFICATES_VALUTEC_TID'),
                'ProgramType' => 'Gift',
                'CardNumber' => $cardNumber,
                'ServerID' => sysConfig::get('EXTENSION_GIFT_CERTIFICATES_SERVER_ID'),
                'Identifier' => $identifier
            );
            $client = new SoapClient('http://ws.valutec.net/Valutec.asmx?WSDL', array('trace' => 1));
            $trans = $client->Transaction_CardBalance($input);
            $data = $trans->Transaction_CardBalanceResult;
            /*
            echo "\n###################### Input ##################\n";
            print_r($input);
            echo "\n###################### Trans ##################\n";
            print_r($trans);
            die();
            */

            if($identifier == $data->Identifier){
                if(!empty($data->Balance) && is_numeric($data->Balance) && (double)$data->Balance > 0){
                    $input['Amount'] = $data->Balance;
                    $saleResponse = $client->Transaction_Sale($input);
                    $saleData = $saleResponse->Transaction_SaleResult;
                    if($identifier == $saleData->Identifier && is_numeric($saleData->Balance) && (double)$data->Balance > 0 ){
                        $transactionData = array(
                            'type' => 'valutec',
                            'transaction_type' => '+',
                            'customers_id' => $userAccount->getCustomerId(),
                            'valutec_card_number' => $cardNumber,
                            'amount' => $data->Balance,
                            'values' => array('global' => $data->Balance)
                        );
                        $this->insertGiftCertificateTransaction($transactionData);
                        return $transactionData;
                    }
                }
                if(empty($data->ErrorMsg)){
                    return array('errorMessage' => 'Not enough balance to load from this gift certificate');
                }
                return array('errorMessage' => ucfirst(strtolower($data->ErrorMsg)));
            } else {
                return array('errorMessage' => 'Security error, transaction has been terminated');
            }
        }

        public function sendGiftCertificatesBalanceByEmail($data = array()){
            global $messageStack, $currencies, $appExtension;
            $id1 = $this->create_code($data['email']);

            $currentBalance = $this->getCustomersBalance($data['customers_id'], 'global');
            $new_amount = $currentBalance - (float)$data['amount'];
            if ($new_amount < 0 && !$appExtension->isAdmin()) {
                $error = true;
                $messageStack->addSession('pageStack', sysLanguage::get('ERROR_ENTRY_AMOUNT_CHECK'), 'error');
                $action = 'send';
            }else if($appExtension->isAdmin() || (int)$data['customers_id'] > 0){

                $newGiftCertificate = new GiftCertificates();
                $newGiftCertificate->gift_certificates_code = $id1;
                $newGiftCertificate->gift_certificates_price = (float)$data['amount'];
                $newGiftCertificate->GiftCertificatesDescription[0]->gift_certificates_name = 'auto generated and sent to ' . $data['email'] . ' by Customer Number: ' . $data['customers_id'];
                $newGiftCertificate->GiftCertificatesDescription[0]->gift_certificates_description = 'auto generated and sent to ' . $data['email'] . ' by Customer Number: ' . $data['customers_id'];
                $newGiftCertificate->GiftCertificatesDescription[0]->language_id = (int)Session::get('languages_id');
                $newGiftCertificate->GiftCertificatesToPurchaseTypes[0]->purchase_type = 'global';
                $newGiftCertificate->GiftCertificatesToPurchaseTypes[0]->gift_certificates_value = (float)$data['amount'];
                $newGiftCertificate->save();

                $newEmailTrack = new GiftCertificatesEmailTrack();
                $newEmailTrack->gift_certificates_id = $newGiftCertificate->gift_certificates_id;
                $newEmailTrack->customer_id_sent = (int)$data['customers_id'];
                $newEmailTrack->sent_firstname = addslashes($data['customers_first_name']);
                $newEmailTrack->sent_lastname = addslashes($data['customers_last_name']);
                $newEmailTrack->emailed_to = $data['email'];
                $newEmailTrack->save();
                if(!$appExtension->isAdmin()){
                    $transactionData = array(
                        'type' => 'giftCertificateSentByEmail',
                        'gift_certificates_id' => $newGiftCertificate->gift_certificates_id,
                        'values' => array('global'=>$data['amount']),
                        'transaction_type' => '-',
                        'amount' => $data['amount'],
                        'customers_id' => $data['customers_id']
                    );
                    $this->insertGiftCertificateTransaction($transactionData);
                }

                if($appExtension->isAdmin()){
                    return $newGiftCertificate;
                }

                $email_event = new emailEvent('gift_voucher_send');
                $email_event->setVars(array(
                                           'voucherAmount' => $currencies->format($data['amount']),
                                           'voucherID' => $newGiftCertificate->gift_certificates_code,
                                           'voucherLink' => itw_app_link('appExt=giftCertificates&gc_no=' . $newGiftCertificate->gift_certificates_code, 'gift_certificates_send', 'redeem', 'NONSSL', false),
                                           'sentFrom' => stripslashes($data['send_name']),
                                           'sentTo' => stripslashes($data['to_name']),
                                           'message' => stripslashes($data['message']),
                                      ));
                $email_event->sendEmail(array(
                                             'email' => $data['email'],
                                             'name' => stripslashes($data['to_name'])
                                        ));
                return $newGiftCertificate->gift_certificates_code;
            }
            return false;
        }
        public function create_code($salt="secret", $length = 10) {
            $ccid = md5(uniqid("","salt"));
            $ccid .= md5(uniqid("","salt"));
            $ccid .= md5(uniqid("","salt"));
            $ccid .= md5(uniqid("","salt"));
            srand((double)microtime()*1000000); // seed the random number generator
            $random_start = @rand(0, (128-$length));
            $good_result = 0;
            $GiftCertificatesTable = Doctrine::getTable('GiftCertificates');
            while ($good_result == 0) {
                $id1=substr($ccid, $random_start,$length);
                $GiftCertificatesCheck = $GiftCertificatesTable->findOneByGiftCertificatesCode($id1);
                if(!$GiftCertificatesCheck){
                    $good_result = 1;
                }
            }
            return $id1;
        }
    }
?>