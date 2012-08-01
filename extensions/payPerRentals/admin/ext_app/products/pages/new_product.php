<?php
/*
	Pay Per Rentals Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class payPerRentals_admin_products_new_product extends Extension_payPerRentals {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'ProductEditAppendProductTypes',
			'NewProductTabHeader',
			'NewProductTabBody'
		), null, $this);
	}
	
	public function ProductEditAppendProductTypes(&$productTypes){
		$productTypes[]['reservation'] = sysLanguage::get('TEXT_PURCHASE_TYPE_PAY_PER_RENTAL');
	}
	
	public function NewProductTabHeader(){
		return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . sysLanguage::get('TAB_PAY_PER_RENTAL') . '</span></a></li>';
	}
	
	public function NewProductTabBody(&$Product){
	    $currencies = new currencies();
		//todo separate price by store
		if ($Product['products_id'] > 0){
			$payPerRental = $Product['ProductsPayPerRental'];
		}

		$QPayPerRentalTypes = Doctrine_Query::create()
		->from('PayPerRentalTypes')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		
		if (sysConfig::get('EXTENSION_CUSTOMER_GROUPS_ENABLED') == 'True') {
			$QGroups = Doctrine_Query::create()
				->from('CustomerGroups')
				->orderBy('customer_groups_id')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		}
		
		$overbookingInput = htmlBase::newElement('checkbox')->setName('reservation_overbooking')->setValue('1');
        $consumptionInput = htmlBase::newElement('checkbox')->setName('reservation_consumption')->setValue('1');
		//$monthsInput = htmlBase::newElement('input')->setName('reservation_max_months');
		$maxInput = htmlBase::newElement('input')->setName('reservation_max_period');
		//$authMethodInput = htmlBase::newElement('selectbox')->setName('products_auth_method')->addOption('auth', 'Authorization Charge')->addOption('rental', 'Rental Fee');
		$depositChargeInput = htmlBase::newElement('input')->setName('reservation_deposit_amount');
		$insuranceInput = htmlBase::newElement('input')->setName('reservation_insurance');
       	$minInput = htmlBase::newElement('input')->setName('reservation_min_period');

		$htypeMax = htmlBase::newElement('selectbox')
		->setName('reservation_max_type');
		foreach($QPayPerRentalTypes as $iType){
			$htypeMax->addOption($iType['pay_per_rental_types_id'], $iType['pay_per_rental_types_name']);
		}

		$htypeMin = htmlBase::newElement('selectbox')
		->setName('reservation_min_type');
		foreach($QPayPerRentalTypes as $iType){
			$htypeMin->addOption($iType['pay_per_rental_types_id'], $iType['pay_per_rental_types_name']);
		}
		
		if (sysConfig::get('EXTENSION_CUSTOMER_GROUPS_ENABLED') == 'True') {
				$hiddenCustGroup = '<div style="float:left;width:450px;"><input type="hidden" id="custGroupEnabled" value="true" />';
			}
			else 
				$hiddenCustGroup = '<input type="hidden" id="custGroupEnabled" value="false" />';

		/*Period Metrics*/

		$Qcheck = Doctrine_Query::create()
		->select('MAX(price_per_rental_per_products_id) as nextId')
		->from('PricePerRentalPerProducts')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		//begin pricing table
		$Table = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->addClass('ui-widget ui-widget-content pprPriceTable')
		->css(array(
			'width' => '100%'
		))
		->attr('data-next_id', $Qcheck[0]['nextId'] + 1)
		->attr('language_id', Session::get('languages_id'));

		if (sysConfig::get('EXTENSION_CUSTOMER_GROUPS_ENABLED') == 'True') {
		$Table->addHeaderRow(array(
				'addCls' => 'ui-state-hover pprPriceTableHeader',
				'columns' => array(
					array('text' => '<div style="float:left;width:80px;">' .sysLanguage::get('TABLE_HEADING_NUMBER_OF').'</div>'.'<div style="float:left;width:100px;">'.sysLanguage::get('TABLE_HEADING_TYPE').'</div>'.'<div style="float:left;width:80px;">'.sysLanguage::get('TABLE_HEADING_PRICE').'</div>'.'<div style="float:left;width:150px;">'.sysLanguage::get('TABLE_HEADING_DETAILS').'</div>'.'<div style="float:left;width:450px;">'.sysLanguage::get('TABLE_HEADING_CUSTOMER_GROUP').'</div>'.'<div style="float:left;width:40px;">'.htmlBase::newElement('icon')->setType('insert')->addClass('insertIcon')->draw().'</div><br style="clear:both"/>')
				)
		));
		}
		else {
		$Table->addHeaderRow(array(
				'addCls' => 'ui-state-hover pprPriceTableHeader',
				'columns' => array(
					array('text' => '<div style="float:left;width:80px;">' .sysLanguage::get('TABLE_HEADING_NUMBER_OF').'</div>'.'<div style="float:left;width:100px;">'.sysLanguage::get('TABLE_HEADING_TYPE').'</div>'.'<div style="float:left;width:80px;">'.sysLanguage::get('TABLE_HEADING_PRICE').'</div>'.'<div style="float:left;width:150px;">'.sysLanguage::get('TABLE_HEADING_DETAILS').'</div>'.'<div style="float:left;width:40px;">'.htmlBase::newElement('icon')->setType('insert')->addClass('insertIcon')->draw().'</div><br style="clear:both"/>')
				)
		));
		}

		$deleteIcon = htmlBase::newElement('icon')->setType('delete')->addClass('deleteIcon')->draw();

		$QPricePerRentalProducts = Doctrine_Query::create()
		->from('PricePerRentalPerProducts pprp')
		->leftJoin('pprp.PricePayPerRentalPerProductsDescription pprpd')
		->where('pay_per_rental_id =?',$Product['ProductsPayPerRental']['pay_per_rental_id'])
		->orderBy('price_per_rental_per_products_id')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	
		$htype = htmlBase::newElement('selectbox')
		->attr('id','types_select');
		foreach($QPayPerRentalTypes as $iType){
			$htype->addOption($iType['pay_per_rental_types_id'], $iType['pay_per_rental_types_name']);
		}
		if (sysConfig::get('EXTENSION_CUSTOMER_GROUPS_ENABLED') == 'True') {
			//	get customer group names
			$htype2 = htmlBase::newElement('selectbox')
				->attr('id','groups_select')
				->attr('style','display:none');
		
		    foreach($QGroups as $iGroup) {
		     $htype2->addOption($iGroup['customer_groups_id'], $iGroup['customer_groups_name']);
	    	}
		}
		$sortableList = htmlBase::newElement('sortable_list');
		foreach($QPricePerRentalProducts as $iPrice){
		
			$pprid = $iPrice['price_per_rental_per_products_id'];
			$Text = htmlBase::newElement('div');
			$br = htmlBase::newElement('br');
			foreach(sysLanguage::getLanguages() as $lInfo){
				$Textl = htmlBase::newElement('input')
				->addClass('ui-widget-content')
				//->setLabel($lInfo['showName']())
				->setLabelPosition('before')
				->setName('pprp[' . $pprid . '][details]['.$lInfo['id'].']')
				->css(array(
					'width' => '100%'
				));
				foreach($iPrice['PricePayPerRentalPerProductsDescription'] as $desc){
					if($lInfo['id'] == $desc['language_id']){
						$Textl->val($desc['price_per_rental_per_products_name']);
						break;
					}
				}

				$Text->append($Textl)->append($br);
			}

			$numberOf = htmlBase::newElement('input')
			->addClass('ui-widget-content')
			->setName('pprp[' . $pprid . '][number_of]')
			->attr('size', '8')
			->val($iPrice['number_of']);
	
			$price = htmlBase::newElement('input')
			->addClass('ui-widget-content')
			->setName('pprp[' . $pprid . '][price]')
			->attr('size', '6')
			->val($iPrice['price']);

			$type = htmlBase::newElement('selectbox')
			->addClass('ui-widget-content')
			->setName('pprp[' . $pprid . '][type]')
			->selectOptionByValue($iPrice['pay_per_rental_types_id']);
			
			
			if (sysConfig::get('EXTENSION_CUSTOMER_GROUPS_ENABLED') == 'True') {
				$group = htmlBase::newElement('selectbox')
					->addClass('ui-widget-content')
					->setName('pprp[' . $pprid . '][customer_group]')
					->selectOptionByValue($iPrice['customer_group']);
					foreach($QGroups as $iGroup) {
						$group->addOption($iGroup['customer_groups_id'], $iGroup['customer_groups_name']);
					}
			}

			foreach($QPayPerRentalTypes as $iType){
				$type->addOption($iType['pay_per_rental_types_id'], $iType['pay_per_rental_types_name']);
			}

			$divLi1 = '<div style="float:left;width:80px;">'.$numberOf->draw().'</div>';
			$divLi2 = '<div style="float:left;width:100px;">'.$type->draw().'</div>';
			$divLi3 = '<div style="float:left;width:80px;">'.$price->draw().'</div>';
			$divLi4 = '<div style="float:left;width:150px;">'.$Text->draw().'</div>';
			if (sysConfig::get('EXTENSION_CUSTOMER_GROUPS_ENABLED') == 'True') {
			  $divLi5 = '<div style="float:left;width:450px;"><input type="hidden" id="custGroupEnabled" value="true" />'.$group->draw().'</div>';
		    }
		    else 
		      $divLi5 = '<input type="hidden" id="custGroupEnabled" value="false" />';
			$divLi6 = '<div style="float:left;width:40px;">'.$deleteIcon.'</div>';

			$liObj = new htmlElement('li');
			$liObj->css(array(
				'font-size' => '.8em',
				'line-height' => '1.1em',
				'border-bottom' => '1px solid #cccccc',
				'cursor' => 'crosshair'
			))
			->html($divLi1.$divLi2.$divLi3.$divLi4.$divLi5.$divLi6.'<br style="clear:both;"/>');//<input name="sortvprice[]" type="hidden">
			$sortableList->addItemObj($liObj);

				/*array('align' => 'center', 'text' => $numberOf->draw(),'addCls' => 'pricePPR'),
						array('align' => 'center', 'text' => $type->draw(),'addCls' => 'pricePPR'),
						array('align' => 'center', 'text' => $price->draw(),'addCls' => 'pricePPR'),
						array('align' => 'center', 'text' => $Text->draw(),'addCls' => 'pricePPR'),
						array('align' => 'center', 'text' => $deleteIcon)*/
		}
		$Table->addBodyRow(array(
				'columns' => array(
					array('align' => 'center', 'text' => $sortableList->draw(),'addCls' => 'pricePPR')
				)
		));
		
		


		/*End Metrics*/

		/*time periods*/
		$Qperiods = Doctrine_Query::create()
		->from('PayPerRentalPeriods p')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if(count($Qperiods) > 0){
			$pricingPeriods = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);

			$pricingPeriods->addBodyRow(array(
				'columns' => array(
					array('text' => '&nbsp;'),
					array(
						'addCls' => 'main',
						'text' => '<h3>' . sysLanguage::get('TEXT_PAY_PER_RENTAL_PRICING_PERIODS') . '</h3>',
						'css' => array(
							'color' => '#ff0000'
						)
					)
				)
			));

			foreach($Qperiods as $iPeriod){
				$periodPrice = htmlBase::newElement('input')
				->setName('reservation_price_period['.$iPeriod['period_id'].']')
				->setLabel($iPeriod['period_name'])
				->setLabelPosition('before');

				if (isset($payPerRental)){
					$Qprice = Doctrine_Query::create()
					->from('ProductsPayPerPeriods p')
					->where('p.period_id=?', $iPeriod['period_id'])
					->andWhere('p.products_id=?',$Product['products_id'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

					$periodPrice->setValue($Qprice[0]['price']);
				}

				$pricingPeriods->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main', 'text' => $periodPrice->draw()),
					)
				));
			}
		}
		/*end time periods*/

		/*Start Hidden Dates*/

		$Qcheck = Doctrine_Query::create()
		->select('MAX(hidden_dates_id) as nextId')
		->from('PayPerRentalHiddenDates')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$TableHidden = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->addClass('ui-widget ui-widget-content pprHiddenTable')
		->css(array(
			'width' => '100%'
		))
		->attr('data-next_id', $Qcheck[0]['nextId'] + 1)
		->attr('language_id', Session::get('languages_id'));

		$TableHidden->addHeaderRow(array(
				'addCls' => 'ui-state-hover pprHiddenTableHeader',
				'columns' => array(
					array('text' => '<div style="float:left;width:80px;">' .sysLanguage::get('TABLE_HEADING_HIDDEN_START_DATE').'</div>'.
						  '<div style="float:left;width:150px;">'.sysLanguage::get('TABLE_HEADING_HIDDEN_END_DATE').'</div>'.
						  '<div style="float:left;width:40px;">'.htmlBase::newElement('icon')->setType('insert')->addClass('insertIconHidden')->draw().
						  '</div><br style="clear:both"/>'
					)
				)
		));

		$deleteIcon = htmlBase::newElement('icon')->setType('delete')->addClass('deleteIconHidden')->draw();
		$QhiddenDates = Doctrine_Query::create()
		->from('PayPerRentalHiddenDates')
		->where('products_id=?', $Product['products_id'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$hiddenList = htmlBase::newElement('list')
		->addClass('hiddenList');

		foreach($QhiddenDates as $iHidden){
			$hiddenid = $iHidden['hidden_dates_id'];
			$hiddenStartDate = htmlBase::newElement('input')
			->addClass('ui-widget-content date_hidden')
			->setName('pprhidden[' . $hiddenid . '][start_date]')
			->attr('size', '15')
			->val(strftime('%Y-%m-%d', strtotime($iHidden['hidden_start_date'])));

			$hiddenEndDate = htmlBase::newElement('input')
			->addClass('ui-widget-content date_hidden')
			->setName('pprhidden[' . $hiddenid . '][end_date]')
			->attr('size', '15')
			->val(strftime('%Y-%m-%d', strtotime($iHidden['hidden_end_date'])));

			$divLi1 = '<div style="float:left;width:80px;">'.$hiddenStartDate->draw().'</div>';
			$divLi2 = '<div style="float:left;width:80px;">'.$hiddenEndDate->draw().'</div>';
			$divLi5 = '<div style="float:left;width:40px;">'.$deleteIcon.'</div>';

			$liObj = new htmlElement('li');
			$liObj->css(array(
				'font-size' => '.8em',
				'list-style' => 'none',
				'line-height' => '1.1em',
				'border-bottom' => '1px solid #cccccc',
				'cursor' => 'crosshair'
			))
			->html($divLi1.$divLi2.$divLi5.'<br style="clear:both;"/>');
			$hiddenList->addItemObj($liObj);
		}
		$TableHidden->addBodyRow(array(
				'columns' => array(
					array('align' => 'center', 'text' => $hiddenList->draw(),'addCls' => 'hiddenDatesPPR')
				)
		));

		/*End Hidden Dates*/

		if (isset($payPerRental)){
			$overbookingInput->setChecked(($payPerRental['overbooking'] == 1));
                        $consumptionInput->setChecked(($payPerRental['consumption'] == 1));
			//$monthsInput->val($payPerRental['max_months']);
			$maxInput->val($payPerRental['max_period']);
			$htypeMax->selectOptionByValue($payPerRental['max_type']);
			$depositChargeInput->val($payPerRental['deposit_amount']);
			$insuranceInput->val($payPerRental['insurance']);
            $minInput->val($payPerRental['min_period']);
			$htypeMin->selectOptionByValue($payPerRental['min_type']);
			//$authMethodInput->selectOptionByValue($Product['products_auth_method']);
		}
		
		$shippingInputs = array(array(
			'id' => 'noShip',
			'value' => 'false',
			'label' => sysLanguage::get('TEXT_PAY_PER_RENTAL_DONT_SHOW_SHIPPING'),
			'labelPosition' => 'after',
			'checked' => (isset($payPerRental['shipping']) && $payPerRental['shipping'] == 'false')
		));
		
		$shippingInputs[] = array(
			'id' => 'storeMethods',
			'value' => 'store',
			'label' => 'Use Store Methods',
			'labelPosition' => 'after',
			'checked' => (isset($payPerRental['shipping']) && $payPerRental['shipping'] == 'store')
		);
		
		$methods = array();
		if (isset($payPerRental['shipping'])){
			$methods = explode(',', $payPerRental['shipping']);
		}

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_UPS_RESERVATION') == 'False'){
			$module = OrderShippingModules::getModule('zonereservation');
		} else{
			$module = OrderShippingModules::getModule('upsreservation');
		}

		if(isset($module) && is_object($module)){
			$quotes = $module->quote();
			for ($i = 0, $n = sizeof($quotes['methods']); $i < $n; $i++) {
				$shippingInputs[] = array(
					'value' => $quotes['methods'][$i]['id'],
					'label' => 'Reservation: ' . $quotes['methods'][$i]['title'],
					'labelPosition' => 'after'
				);
			}
		}


		$shippingGroup = htmlBase::newElement('checkbox')->addGroup(array(
			'separator' => '<br />',
			'name' => 'reservation_shipping[]',
			'checked' => $methods,
			'data' => $shippingInputs
		));
		
		$currentTypes = explode(',', $Product['products_type']);
		$productTypeEnabled = htmlBase::newElement('checkbox')
				->setName('products_type[]')
				->setValue('reservation');

		if (isset($currentTypes) && in_array('reservation', $currentTypes)){
			$productTypeEnabled->setChecked(true);
		}

		$mainTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_PRODUCTS_ENABLED')),
				array('addCls' => 'main', 'text' => $productTypeEnabled)
			)
		));
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_PAY_PER_RENTAL_OVERBOOKING')),
				array('addCls' => 'main', 'text' => $overbookingInput)
			)
		));
                
                $mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_PAY_PER_RENTAL_CONSUMPTION')),
				array('addCls' => 'main', 'text' => $consumptionInput)
			)
		));
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_PAY_PER_RENTAL_DEPOSIT_AMOUNT')),
				array('addCls' => 'main', 'text' => $depositChargeInput->draw())
			)
		));

		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_PAY_PER_RENTAL_INSURANCE')),
				array('addCls' => 'main', 'text' => $insuranceInput->draw())
			)
		));

        $mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_PAY_PER_RENTAL_MIN_RENTAL_DAYS')),
				array('addCls' => 'main', 'text' => $minInput->draw() .$htypeMin->draw())
			)
		));

		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_PAY_PER_RENTAL_SHIPPING'), 'valign' => 'top'),
				array('addCls' => 'main', 'text' => $shippingGroup)
			)
		));
		
		/*$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => TEXT_PAY_PER_RENTAL_AUTH_CHARGE),
				array('addCls' => 'main', 'text' => $authChargeInput)
			)
		));*/
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_PAY_PER_RENTAL_MAX_DAYS')),
				array('addCls' => 'main', 'text' => $maxInput->draw(). $htypeMax->draw())
			)
		));
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sysLanguage::get('TEXT_PAY_PER_RENTAL_HIDDEN_DATES')),
				array('addCls' => 'main', 'text' => $TableHidden)
			)
		));
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('TEXT_PAY_PER_RENTAL_MAX_INFO') . '</b>', 'colspan' => '2')
			)
		));
		
		$pricingTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
		
		$pricingTable->addBodyRow(array(
			'columns' => array(
				
				array(
					'addCls' => 'main',
					'colspan' => '3',
					'text' => '<h3>' . sysLanguage::get('TEXT_PAY_PER_RENTAL_PRICING') . '</h3>',
					'css' => array(
						'color' => '#ff0000'
					)
				)
			)
		));
 		
		$pricingTable->addBodyRow(array(
			'columns' => array(
				array('text' => '&nbsp;'),
			)
		));
 		
		$pricingTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'mainPricePPR', 'text' => '<b>' . $Table->draw().$htype->draw() . '</b>'),
			)
		));
		
 		
		return '<div id="tab_' . $this->getExtensionKey() . '">' . 
			$mainTable->draw() . 
			'<hr />' . 
			(isset($pricingPeriods)?$pricingPeriods->draw():'') .
			$pricingTable->draw() . 
		'</div>' . $hiddenCustGroup;
	}
}
?>