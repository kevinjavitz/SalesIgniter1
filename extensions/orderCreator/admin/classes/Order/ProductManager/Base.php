<?php
require(dirname(__FILE__) . '/Product.php');

class OrderCreatorProductManager extends OrderProductManager implements Serializable {
	public function __construct($orderedProducts = null){
		if (is_null($orderedProducts) === false){
			foreach($orderedProducts as $i => $pInfo){

				if(isset($pInfo['purchase_type']) && $pInfo['purchase_type'] != 'membership'){
					$orderedProduct = new OrderCreatorProduct($pInfo);
				}else{
					$orderedProduct = new OrderCreatorRentalMembershipProduct($pInfo);
				}
				$this->add($orderedProduct);
			}
		}
	}
	
	public function serialize(){
		$data = array(
			'orderId' => $this->orderId,
			'Contents' => $this->Contents,
			'removedElements' => $this->removedElements
		);
		return serialize($data);
	}

	public function unserialize($data){
		$data = unserialize($data);
		foreach($data as $key => $dInfo){
			$this->$key = $dInfo;
		}
	}
	
	public function updateFromPost(){
		global $currencies, $Editor;
		foreach($_POST['product'] as $id => $pInfo){
			$Product = $this->getContents($id);
			if (is_null($Product)){
				$Product = new OrderCreatorProduct($pInfo);
			}
			
			$Product->setQuantity($pInfo['qty']);
			$Product->setPrice($pInfo['price']);
			$Product->setTaxRate($pInfo['tax_rate']);

			if (isset($pInfo['barcode_id'])){
				$Product->setBarcodeId($pInfo['barcode_id']);
			}
			
			if (isset($pInfo['attributes'])){
				$Product->updateInfo(array(
					'attributes' => $pInfo['attributes']
				));
			}
			/*
product[85544][qty]:1
product[85544][purchase_type]:new
product[85544][attributes][1][value]:2
product[85544][attributes][1][prefix]: 
product[85544][attributes][1][price]:0
product[85544][tax_rate]:0
product[85544][price]:17.99
*/
		}
	}
	
	public function addAllToCollection($CollectionObj){
		global $Editor;
		$CollectionObj->clear();
		$langId = Session::get('languages_id');
		foreach($this->Contents as $id => $Product){

			//this part somehow need to be moved
			$allInfo = $Product->getPInfo();
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True' && isset($allInfo['reservationInfo']['multiple_dates']) && $allInfo['purchase_type'] == 'reservation'){
				$i1 = 0;
				$totalPrice = $allInfo['final_price'];
				$nrCount = count($allInfo['reservationInfo']['multiple_dates']);
				foreach($allInfo['reservationInfo']['multiple_dates'] as $iDate){
					$allInfo['reservationInfo']['start_date'] = $iDate;
					$allInfo['reservationInfo']['end_date'] = $iDate;
					$allInfo['reservationInfo']['event_date'] = $iDate;
					$allInfo['final_price'] = $totalPrice/$nrCount;
					$allInfo['products_price'] = $totalPrice/$nrCount;
					if($i1 == 0){
						$Product->setPInfo($allInfo);
						$i1++;
						continue;
					}

					$OrderProduct = new OrderCreatorProduct();
					$OrderProduct->setProductsId($allInfo['products_id']);

					$OrderProduct->setPurchaseType('reservation');

					$OrderProduct->setQuantity(1);
					$OrderProduct->setPInfo($allInfo);

					$this->add($OrderProduct);
					$i1++;
				}
			}
			//end of to be moved part

		}
		foreach($this->Contents as $id => $Product){
			$OrderedProduct = new OrdersProducts();
			$QProductCheck = Doctrine_Query::create()
			->from('Products p')
			->leftJoin('p.ProductsDescription pd')
			->where('pd.language_id = ?', Session::get('languages_id'))
			->where('products_id = ?', $Product->getProductsId())
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if(count($QProductCheck) > 0){
			$OrderedProduct->products_id = $Product->getProductsId();
			$OrderedProduct->products_quantity = $Product->getQuantity();
			$OrderedProduct->products_name = $Product->getName();
			$OrderedProduct->products_model = $Product->getModel();
			$OrderedProduct->products_price = $Product->getFinalPrice(false, false);
			$OrderedProduct->final_price = $Product->getFinalPrice(false, false);
			$OrderedProduct->products_tax = $Product->getTaxRate();
			$OrderedProduct->purchase_type = $Product->getPurchaseType();

			$Product->onAddToCollection($OrderedProduct);

			$CollectionObj->add($OrderedProduct);
			}else{
				$Editor->addErrorMessage('Your Order has a deleted product:'.$Product->getName().'. Please remove or replace this product and then update order again.');
				break;
			}
		}
		foreach($this->removedElements as $removed){
			$allInfo = $removed->getPInfo();
			Doctrine_Query::create()
			->delete('OrdersProductsReservation')
			->where('orders_products_id = ?', $allInfo['orders_products_id'])
			->execute();
		}
	}

	public function add($orderedProduct){
		/*
		 * Ensure that there are no duplicate identifiers
		 */
		while(array_key_exists($orderedProduct->getId(), $this->Contents)){
			$orderedProduct->regenerateId();
		}
		$this->Contents[$orderedProduct->getId()] = $orderedProduct;
		$this->cleanUp();
	}

	private function cleanUp(){
		foreach($this->getContents() as $cartProduct){
			if ($cartProduct->getQuantity() < 1){
				$this->removeFromContents($cartProduct->getId());
			}
		}
	}

	public function remove($id){
		$this->removeFromContents($id);
	}
	
	private function removeFromContents($id){
		if (array_key_exists($id, $this->Contents)){
			$this->removedElements[] = $this->Contents[$id];
			unset($this->Contents[$id]);
		}
	}

	public function editProducts(){
		global $currencies, $typeNames;
		$productsTable = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->addClass('productTable')
		->attr('data-product_entry_method', sysConfig::get('EXTENSION_ORDER_CREATOR_PRODUCT_FIND_METHOD'))
		->css('width', '100%');

		$buttonAdd = htmlBase::newElement('button')
		->addClass('insertProductIcon')
		->setText('Add');

		$productTableHeaderColumns = array(
			array('text' => 'Qty'),
			array('text' => sysLanguage::get('TABLE_HEADING_PRODUCTS')),
			array('text' => 'Barcode'),
			array('text' => 'Start Date'),
			array('text' => 'Start Time'),
			array('text' => 'End Date'),
			array('text' => 'End Time'),
			//array('text' => sysLanguage::get('TABLE_HEADING_PRODUCTS_MODEL')),
			array('text' => sysLanguage::get('TABLE_HEADING_TAX')),
			array('text' => sysLanguage::get('TABLE_HEADING_PRICE_EXCLUDING_TAX')),
			//array('text' => sysLanguage::get('TABLE_HEADING_PRICE_INCLUDING_TAX')),
			//array('text' => sysLanguage::get('TABLE_HEADING_TOTAL_EXCLUDING_TAX')),
			//array('text' => sysLanguage::get('TABLE_HEADING_TOTAL_INCLUDING_TAX')),
			array('text' => '')
		);
		
		foreach($productTableHeaderColumns as $i => $cInfo){
			$productTableHeaderColumns[$i]['addCls'] = 'main ui-widget-header';
			if ($i > 0){
				$productTableHeaderColumns[$i]['css'] = array(
					'border-left' => 'none'
				);
			}

			if ($i > 1){
				$productTableHeaderColumns[$i]['align'] = 'right';
			}
		}

		$productsTable->addHeaderRow(array(
			'columns' => $productTableHeaderColumns
		));

		$startTime = htmlBase::newElement('selectbox')
		->setName('start_time')
		->addClass('start_time');
		$endTime = htmlBase::newElement('selectbox')
		->setName('end_time')
		->addClass('end_time');

		for($i = sysConfig::get('EXTENSION_PAY_PER_RENTALS_START_TIME');$i <= sysConfig::get('EXTENSION_PAY_PER_RENTALS_END_TIME');$i++){
			if($i == 12){
				$startTime->addOption($i,'12:00 PM');
				$endTime->addOption($i,'12:00 PM');
			}else{
				$startTime->addOption($i,($i % 12) . ($i<12?':00 AM':':00 PM'));
				$endTime->addOption($i,($i % 12) . ($i<12?':00 AM':':00 PM'));
			}

		}

		$bodyColumns = array(
			array('align' => 'right', 'text' => '<input type="number" class="ui-widget-content productQty" size="3" value="1" style="width:50px;" name="qty" >'),
			array('text' => '<input class="productSearch" name="product_search">'),
			array('text' => '<input class="barcodeName" name="barcodeName" size="8">'),
			array('text' => '<input class="start_date" name="start_date">'),
			array('text' => $startTime->draw()),
			array('text' => '<input class="end_date" name="end_date">'),
			array('text' => $endTime->draw()),
			//array('text' => $orderedProduct->getModel()),
			//array('align' => 'right', 'text' => '<input class="taxRate" size="4" style="width:70px;" name="taxRate">'),
			//array('align' => 'right', 'text' => '<input class="price" style="width:70px;" size="5" name="price">'),
			//array('align' => 'right', 'text' => $orderedProduct->getPriceEdit(false, true)),
			//array('align' => 'right', 'text' => $orderedProduct->getPriceEdit(true, false)),
			//array('align' => 'right', 'text' => $orderedProduct->getPriceEdit(true, true)),
			array('align' => 'right','colspan'=>'3', 'text' => $buttonAdd->draw())
		);

		$sizeOf = sizeof($bodyColumns);
		foreach($bodyColumns as $idx => $colInfo){
			$bodyColumns[$idx]['addCls'] = 'ui-widget-content';
			$bodyColumns[$idx]['valign'] = 'top';
			$bodyColumns[$idx]['css'] = array(
				'border-top' => 'none'
			);

			if ($idx > 0 && $idx < $sizeOf){
				$bodyColumns[$idx]['css']['border-left'] = 'none';
			}
		}

		$productsTable->addBodyRow(array(
			'attr' => array(
				'data-id' => 'none',
				'data-product_id' => 'none'
			),
			'addCls' => 'firstRow',
			'columns' => $bodyColumns
		));


		foreach($this->getContents() as $orderedProduct){
			//$productsName = '<input type="text" style="width:90%" class="ui-widget-content" name="product[' . $orderedProductId . '][name]" value="' . $orderedProduct->getName() . '">';

			$bodyColumns = array(
				array('align' => 'right', 'text' => $orderedProduct->getQuantityEdit()),
				array('text' => $orderedProduct->getNameEdit()),
				array('text' => $orderedProduct->getBarcodeEdit()));
			if($orderedProduct->getPurchaseType() == 'reservation'){
				$bodyColumns[] = array('text' => $orderedProduct->getStartDateEdit());
				$bodyColumns[] = array('text' => $orderedProduct->getStartTimeEdit());
				$bodyColumns[] = array('text' => $orderedProduct->getEndDateEdit());
				$bodyColumns[] = array('text' => $orderedProduct->getEndTimeEdit());

			}else{
				$bodyColumns[] = array('text' => 'Not available','colspan' => 4);

			}
				//array('text' => $orderedProduct->getModel()),
			$bodyColumns[] = array('align' => 'right', 'text' => $orderedProduct->getTaxRateEdit());
			$bodyColumns[] = array('align' => 'right', 'text' => $orderedProduct->getPriceEdit());
			//array('align' => 'right', 'text' => $orderedProduct->getPriceEdit(false, true)),
			//array('align' => 'right', 'text' => $orderedProduct->getPriceEdit(true, false)),
			//array('align' => 'right', 'text' => $orderedProduct->getPriceEdit(true, true)),
			$bodyColumns[] = array('align' => 'right', 'text' => '<span class="ui-icon ui-icon-closethick deleteProductIcon"></span>');


			$sizeOf = sizeof($bodyColumns);
			foreach($bodyColumns as $idx => $colInfo){
				$bodyColumns[$idx]['addCls'] = 'ui-widget-content';
				$bodyColumns[$idx]['valign'] = 'top';
				$bodyColumns[$idx]['css'] = array(
					'border-top' => 'none'
				);

				if ($idx > 0 && $idx < $sizeOf){
					$bodyColumns[$idx]['css']['border-left'] = 'none';
				}
			}

			$productsTable->addBodyRow(array(
                'attr' => array(
                    'data-id' => $orderedProduct->getId(),
                    'data-product_id' => $orderedProduct->getProductsId(),
					'data-product_type' => $orderedProduct->getPurchaseType()

                ),
                'columns' => $bodyColumns
            ));



		}
		return $productsTable;
	}

	/*function taking care that the same product cannot be twice with the same purchase type*/
	public function getExcludedPurchaseTypes($Product){
		$excludedTypes = array();
		foreach($this->getContents() as $orderedProduct){
			if($Product->getProductsId() == $orderedProduct->getProductsId() && $Product->getId() != $orderedProduct->getId()){
				if($orderedProduct->getPurchaseType() == 'reservation'){
					$excludedTypes[] ='reservation';
				}
			}
		}
		return $excludedTypes;
	}
	/*end function*/

}
?>