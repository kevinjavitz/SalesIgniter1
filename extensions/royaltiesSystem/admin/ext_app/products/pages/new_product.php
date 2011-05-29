<?php
/*
	Royalties System Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class royaltiesSystem_admin_products_new_product extends Extension_royaltiesSystem {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->enabled === false) return;

		EventManager::attachEvents(array(
			'NewProductStreamingTableAddHeaderCol',
			'NewProductStreamingTableAddBodyCol',
			'NewProductStreamingTableAddInputRow',
			'NewProductDownloadsTableAddHeaderCol',
			'NewProductDownloadsTableAddBodyCol',
			'NewProductDownloadsTableAddInputRow',
			'NewProductPricingTabBottom',
			'NewProductPricingTabsComplete'
		), null, $this);
	}

	public function exemptedPurchaseTypes(){
		return array('stream', 'download');
	}
	
	public function NewProductStreamingTableAddBodyCol($sInfo, &$BodyColumns){
		$Cselectbox = htmlBase::newElement('selectbox')
		->hide()
		->setName('stream_content_provider_id[' . $sInfo['stream_id'] . ']')
		->selectOptionByValue($sInfo['content_provider_id']);

		$Cselectbox->addOption('0', sysLanguage::get('TEXT_PLEASE_SELECT'), false);

		$providerName = '';
		$Qproviders = Doctrine_Query::create()
				->select('customers_id, CONCAT(customers_firstname," ", customers_lastname) as customers_name')
		->from('Customers')
		->where('is_content_provider = ?', '1')
		->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
		if ($Qproviders){
			foreach($Qproviders as $pInfo){
				$Cselectbox->addOption(
					$pInfo['customers_id'],
					$pInfo['customers_name']
				);
				
				if ($pInfo['customers_id'] == $sInfo['content_provider_id']){
					$providerName = $pInfo['customers_firstname'] . ' ' . $pInfo['customers_lastname'];
				}
			}
		}
		
		$royaltyFeeInput = htmlBase::newElement('input')
		->hide()
		->attr('size', 6)
		->setName('stream_royalty_fee[' . $sInfo['stream_id'] . ']')
		->val($sInfo['royalty_fee']);
		
		$BodyColumns[] = array(
			'text' => '<span class="streamInfoText">' . $providerName . '</span>' . $Cselectbox->draw()
		);
		
		$BodyColumns[] = array(
			'text' => '<span class="streamInfoText">' . $sInfo['royalty_fee'] . '</span>' . $royaltyFeeInput->draw()
		);
	}

	public function NewProductStreamingTableAddHeaderCol(&$headerColumns){
		$headerColumns[] = array(
			'text' => 'Stream Owner'
		);
		$headerColumns[] = array(
			'text' => 'Royalty Fee'
		);
	}
	
	public function NewProductStreamingTableAddInputRow(&$inputRow){
		$Cselectbox = htmlBase::newElement('selectbox')
		->setName('new_stream_content_provider_id');

		$Cselectbox->addOption('0', sysLanguage::get('TEXT_PLEASE_SELECT'), false);

		$Qproviders = Doctrine_Query::create()
				->select('customers_id, CONCAT(customers_firstname," ", customers_lastname) as customers_name')
		->from('Customers')
		->where('is_content_provider = ?', '1')
		->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
		if ($Qproviders){
			foreach($Qproviders as $pInfo){
				$Cselectbox->addOption(
					$pInfo['customers_id'],
					$pInfo['customers_name']
				);
			}
		}
		
		$royaltyFeeInput = htmlBase::newElement('input')
		->setName('new_stream_royalty_fee')
		->attr('size', 6);
		
		$inputRow[] = array(
			'text' => $Cselectbox->draw()
		);
		
		$inputRow[] = array(
			'text' => $royaltyFeeInput->draw()
		);
	}
	
	public function NewProductDownloadsTableAddBodyCol($dInfo, &$BodyColumns){
		$Cselectbox = htmlBase::newElement('selectbox')
		->hide()
		->setName('download_content_provider_id[' . $dInfo['download_id'] . ']')
		->selectOptionByValue($dInfo['content_provider_id']);

		$Cselectbox->addOption('0', sysLanguage::get('TEXT_PLEASE_SELECT'), false);

		$providerName = '';
		$Qproviders = Doctrine_Query::create()
				->select('customers_id, CONCAT(customers_firstname," ", customers_lastname) as customers_name')
		->from('Customers')
		->where('is_content_provider = ?', '1')
		->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
		if ($Qproviders){
			foreach($Qproviders as $pInfo){
				$Cselectbox->addOption(
					$pInfo['customers_id'],
					$pInfo['customers_name']
				);
				
				if ($pInfo['customers_id'] == $dInfo['content_provider_id']){
					$Cselectbox->selectOptionByValue($dInfo['content_provider_id']);
					$providerName = $pInfo['customers_name'];
				}
			}
		}
		
		$royaltyFeeInput = htmlBase::newElement('input')
		->hide()
		->attr('size', 6)
		->setName('download_royalty_fee[' . $dInfo['download_id'] . ']')
		->val($dInfo['royalty_fee']);
		
		$BodyColumns[] = array(
			'text' => '<span class="downloadInfoText">' . $providerName . '</span>' . $Cselectbox->draw()
		);
		
		$BodyColumns[] = array(
			'text' => '<span class="downloadInfoText">' . $dInfo['royalty_fee'] . '</span>' . $royaltyFeeInput->draw()
		);
	}

	public function NewProductDownloadsTableAddHeaderCol(&$headerColumns){
		$headerColumns[] = array(
			'text' => 'Download Owner'
		);
		$headerColumns[] = array(
			'text' => 'Royalty Fee'
		);
	}
	
	public function NewProductDownloadsTableAddInputRow(&$inputRow){
		$Cselectbox = htmlBase::newElement('selectbox')
		->setName('new_download_content_provider_id');

		$Cselectbox->addOption('0', sysLanguage::get('TEXT_PLEASE_SELECT'), false);

		$Qproviders = Doctrine_Query::create()
				->select('customers_id, CONCAT(customers_firstname," ", customers_lastname) as customers_name')
		->from('Customers')
		->where('is_content_provider = ?', '1')
		->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
		if ($Qproviders){
			foreach($Qproviders as $pInfo){
				$Cselectbox->addOption(
					$pInfo['customers_id'],
					$pInfo['customers_name']
				);
			}
		}
		
		$royaltyFeeInput = htmlBase::newElement('input')
		->setName('new_download_royalty_fee')
		->attr('size', 6);
		
		$inputRow[] = array(
			'text' => $Cselectbox->draw()
		);
		
		$inputRow[] = array(
			'text' => $royaltyFeeInput->draw()
		);
	}

	public function NewProductPricingTabBottom(&$Product, &$inputTable, &$typeName){
		if(in_array($typeName,$this->exemptedPurchaseTypes()))
			return false;
		if ($Product !== false && $Product['products_id'] > 0){
			$ProductsRoyaltiesTable = Doctrine_Core::getTable('RoyaltiesSystemProductsRoyalties');
			$ProductsRoyalties = $ProductsRoyaltiesTable->findOneByProductsIdAndPurchaseType($Product['products_id'],$typeName);
		}


		$Cselectbox = htmlBase::newElement('selectbox')
				->setName('content_provider_id[' . $typeName . ']');

		$Cselectbox->addOption('0', sysLanguage::get('TEXT_PLEASE_SELECT'), false);

		$Qproviders = Doctrine_Query::create()
				->select('customers_id, CONCAT(customers_firstname," ", customers_lastname) as customers_name')
				->from('Customers')
				->where('is_content_provider = ?', '1')
				->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

		if ($Qproviders){
			foreach($Qproviders as $pInfo){
				$Cselectbox->addOption(
					$pInfo['customers_id'],
					$pInfo['customers_name']
				);
			}
		}
		$Cselectbox->selectOptionByValue($ProductsRoyalties->content_provider_id);

		$royaltyFeeInput = htmlBase::newElement('input')
				->setName('royalty_fee[' . $typeName . ']')
				->attr('size', 6)
				->val($ProductsRoyalties->royalty_fee);
		if($typeName == 'rental'){
			$inputNet = htmlBase::newElement('input')->addClass('netPricing');
			$inputNet->setName('products_price_rental')
					->setId('products_price_rental')
					->val((isset($Product) ? $ProductsRoyalties->products_price_rental : ''));
			$inputTable->addBodyRow(array(
			                             'columns' => array(
				                             array('text' => 'Price Net(will only be used to calculate the %age for royalties):'),
				                             array('text' => $inputNet->draw())
			                             )
			                        ));
		}
		$inputTable->addBodyRow(array(
		                             'columns' => array(
			                             array('text' => 'Content Provider:'),
			                             array('text' => $Cselectbox->draw())
		                             )
		                        ));
		$inputTable->addBodyRow(array(
		                             'columns' => array(
			                             array('text' => 'Royalty:'),
			                             array('text' => $royaltyFeeInput->draw())
		                             )
		                        ));
	}
}
?>