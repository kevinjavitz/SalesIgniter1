<?php
	class giftCertificates_catalog_account_default extends Extension_giftCertificates {
    public function __construct(){
        global $App;
        parent::__construct();

        if ($App->getAppName() != 'account' || ($App->getAppName() == 'account' && $App->getPageName() != 'default')){
            $this->enabled = false;
        }
    }

    public function load(){
        if ($this->isEnabled() === false) return;

        EventManager::attachEvents(array(
                                        'AccountDefaultMyAccountAddLink',
                                        'AccountDefaultAddLinksBlock'
                                   ), null, $this);
    }

    public function AccountDefaultMyAccountAddLink(){
        global $userAccount;
        $Customer = Doctrine::getTable('Customers')->find($userAccount->getCustomerId());

        $links[] = htmlBase::newElement('a')
                ->html(sysLanguage::get('VIEW_GIFT_CERTIFICATES_TRANSACTIONS_HISTORY'))
                ->setHref(itw_app_link('appExt=giftCertificates', 'gift_certificates_transactions', 'default', 'SSL'))
                ->draw();



        return $links;
    }

    public function AccountDefaultAddLinksBlock(&$pageContents){
        global $appExtension,$currencies, $userAccount, $typeNames;
        $pTypeNames = $typeNames;
        $pTypeNames['global'] = 'All Products';

        $extGiftCertificates = $appExtension->getExtension('giftCertificates');
        $GiftCertificatesCustomersBalance = Doctrine::getTable('GiftCertificatesCustomersBalance')->findByCustomersId((int)$userAccount->getCustomerId());
	    $html = '';
        if($GiftCertificatesCustomersBalance){
            $customersBalanceTable = htmlBase::newElement('table')
                    ->setCellPadding(6)
                    ->setCellSpacing(0)
                    ->addClass('ui-widget ui-widget-content ui-corner-all')
                    ->css(array(
                            'width' => '100%',
                            'margin-top' => '1em'));
            $customersBalanceTable->addHeaderRow(array(
                                                    'columns' => array(
                                                        array('text' => sysLanguage::get('TABLE_HEADING_PURCHASE_TYPE')),
                                                        array('text' => sysLanguage::get('TABLE_HEADING_PURCHASE_TYPE_VALUE'))
                                                    )
                                               ));
	        $columns = array();
            foreach($GiftCertificatesCustomersBalance as $GiftCertificatesCustomersBalanceDetails)
            {
                $columns[] = array('text' => ucwords($pTypeNames[$GiftCertificatesCustomersBalanceDetails['purchase_type']]));
                $columns[] = array('text' => $currencies->format($GiftCertificatesCustomersBalanceDetails['value']));

            }
	        if(count($columns) > 0){
                $customersBalanceTable->addBodyRow(array(
                                                    'columns' => $columns
                                               ));
				$headingDiv = htmlBase::newElement('div')
					->addClass('main')
					->css(array(
						'font-weight' => 'bold',
						'margin-top' => '1em'
					))
					->html(sysLanguage::get('BOX_HEADING_GIFT_CERTICICATES'));

				$contentDiv = htmlBase::newElement('div')
					->addClass('ui-widget ui-widget-content ui-corner-all')
					->append($customersBalanceTable);

				$html = $headingDiv->draw() . $contentDiv->draw();
	        }
        }
        $pageContents .=  $html;
    }
}
?>