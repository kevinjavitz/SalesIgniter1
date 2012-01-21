<?php
class OrderTotalGiftCertificate extends OrderTotalModule {
    public function __construct(){
        /*
           * Default title and description for modules that are not yet installed
           */
        $this->setTitle('Gift Certificate');
        $this->setDescription('Gift Certificate');
        $this->init('giftCertificate');
        if ($this->isInstalled() === true) {
            $this->credit_class = true;
            $this->user_prompt = '';
            $this->header = $this->getConfigData('MODULE_ORDER_TOTAL_GIFT_CERTIFICATES_HEADER');
        }
    }

    public function process(){
        global $order, $onePageCheckout;
        $ShoppingCart = &Session::getReference('ShoppingCart');
        $userAccount = &Session::getReference('userAccount');

        if(!Session::exists('giftCertificate_balance') || count(Session::get('giftCertificate_balance')) <= 0)
            return false;

        //$order->info['total'] = $order->info['total'];
        $gvBalanceApply = Session::get('giftCertificate_balance');
        $purchaseTypes = false;
        $discountAmount = 0;
        $purchaseTypes['global'] = 0;
        foreach ($ShoppingCart->getProducts() as $cartProduct) {
            $purchaseType = $cartProduct->getPurchaseType();
            $purchaseTypes[$purchaseType] += $cartProduct->getPrice();
            $purchaseTypes['global'] += $cartProduct->getPrice();
        }
        if (is_object($onePageCheckout) && count($onePageCheckout->onePage['info']['shipping']) > 0){
	        $purchaseTypes['global'] += $onePageCheckout->onePage['info']['shipping']['cost'];
        }
        $discountAmount = 0;
        if ($purchaseTypes) {
            foreach ($gvBalanceApply as $purchaseType => $sum) {
                if($purchaseTypes[$purchaseType] >= $sum){
                    $discountAmount = $sum;
                } else {
                    $discountAmount = $this->getCustomersBalance($userAccount->getCustomerId(), $purchaseType);
                }
            }
        }

        if ( $discountAmount > 0 && $discountAmount <= $order->info['total']) {
            $order->info['total'] = $order->info['total']- $discountAmount;
            Session::set('giftCertificate_balance', array($purchaseType => (float)$discountAmount));
            $this->addOutput(array(
                                  'title' => $this->getTitle() . ':',
                                  'text' => '<b>-' . $this->formatAmount($discountAmount) . '</b>',
                                  'value' => $order->info['total']
                             ));
        }
    }

    public function selection_test(){
        return true;
    }

    public function getCustomersBalance($customers_id, $purchase_type){
        $GiftCertificatesCustomersBalance = Doctrine::getTable('GiftCertificatesCustomersBalance')
                ->findOneByCustomersIdAndPurchaseType($customers_id, $purchase_type);
        return $GiftCertificatesCustomersBalance->value;
    }
}
?>