<?php
require(dirname(__FILE__) . '/Total.php');

class OrderCreatorTotalManager extends OrderTotalManager {

	public function __construct($orderTotals = null){
		if (is_null($orderTotals) === false){
			foreach($orderTotals as $i => $tInfo){
				$orderTotal = new OrderCreatorTotal($tInfo);
				$this->add($orderTotal);
			}
		}
	}

	public function remove($moduleType){
		$orderTotal = $this->getTotal($moduleType);
		if (is_null($orderTotal) === false){
			$this->detach($orderTotal);
		}
	}
	
	public function updateFromPost(){
		global $currencies, $Editor;
		foreach($_POST['order_total'] as $id => $tInfo){
			$OrderTotal = $this->get($tInfo['type']);
			$addTotal = false;
			if (is_null($OrderTotal) === true){
				$OrderTotal = new OrderCreatorTotal();
				$OrderTotal->setModuleType($tInfo['type']);
				$addTotal = true;
			}
			$OrderTotal->setSortOrder($tInfo['sort_order']);
			$OrderTotal->setTitle($tInfo['title']);
			$OrderTotal->setValue($tInfo['value']);
			$OrderTotal->setText($currencies->format($tInfo['value'], true, $Editor->getCurrency(), $Editor->getCurrencyValue()));
			$OrderTotal->setModule($tInfo['type']);
			$OrderTotal->setMethod(null);
			
			if ($addTotal === true){
				$this->add($OrderTotal);
			}
			
			if ($tInfo['type'] == 'shipping'){
				$shipModule = explode('_', $tInfo['title']);
				$OrderTotal->setModule($shipModule[0]);
				$OrderTotal->setMethod($shipModule[1]);
				
				$Module = OrderShippingModules::getModule($shipModule[0]);
				$Quote = $Module->quote($shipModule[1]);
				$OrderTotal->setTitle($Quote['module'] . ' ( ' . $Quote['methods'][0]['title'] . ' ) ');
				$Editor->setShippingModule($tInfo['title']);
			}
		}
	}
	
	public function addAllToCollection(&$CollectionObj){
		$CollectionObj->clear();
		$this->rewind();
		while($this->valid()){
			$orderTotal = $this->current();
			
			$OrdersTotal = new OrdersTotal();
			$OrdersTotal->title = $orderTotal->getTitle();
			$OrdersTotal->text = $orderTotal->getText();
			$OrdersTotal->value = $orderTotal->getValue();
			$OrdersTotal->module_type = $orderTotal->getModuleType();
			$OrdersTotal->module = $orderTotal->getModule();
			$OrdersTotal->method = $orderTotal->getMethod();
			$OrdersTotal->sort_order = $orderTotal->getSortOrder();

			$CollectionObj->add($OrdersTotal);
			$this->next();
		}
	}

	public function edit(){
		global $Editor, $currencies, $total_weight;
		$orderTotalTable = htmlBase::newElement('table')
		->addClass('orderTotalTable')
		->setCellPadding(2)
		->setCellSpacing(0)
		->css(array(
			'width' => '100%'
		));

		$orderTotalTable->addHeaderRow(array(
			'columns' => array(
				array(
					'addCls' => 'main ui-widget-header',
					'text' => 'Title'
				),
				array(
					'addCls' => 'main ui-widget-header',
					'css' => array(
						'border-left' => 'none',
						'width' => '120px'
					),
					'text' => 'Value'
				),
				array(
					'addCls' => 'main ui-widget-header',
					'css' => array(
						'border-left' => 'none',
						'width' => '200px'
					),
					'text' => 'Type'
				),
				array(
					'addCls' => 'main ui-widget-header',
					'css' => array(
						'border-left' => 'none',
						'width' => '40px'
					),
					'align' => 'center',
					'text' => '<span class="ui-icon ui-icon-plusthick insertTotalIcon"></span>'
				)
			)
		));
		$this->rewind();
		$count = 0;
		$totalTypes = array(
			'subtotal' => 'Sub-Total',
			'tax' => 'Tax',
			'total' => 'Total',
			'shipping' => 'Shipping',
			'coupon' => 'Coupon Discount',
			'gv' => 'Gift Voucher',
			'loworderfee' => 'Low Order Fee',
			'custom' => 'Custom',
		);
		while($this->valid()){
			$orderTotal = $this->current();
			$totalType = $orderTotal->getModuleType();

			$typeMenu = htmlBase::newElement('selectbox')
			->addClass('orderTotalType')
			->setName('order_total[' . $count . '][type]');
			foreach($totalTypes as $k => $v){
				$typeMenu->addOption($k, $v);
			}

			$typeMenu->selectOptionByValue($totalType);

			$hiddenField = '<input type="hidden" name="order_total[' . $count . '][sort_order]" value="' . $orderTotal->getSortOrder() . '">';
			
			if ($orderTotal->hasOrderTotalId()){
				$hiddenField .= '<input type="hidden" name="order_total[' . $count . '][id]" value="' . $orderTotal->getOrderTotalId() . '">';
			}

			if ($totalType == 'shipping'){
				$total_weight = $Editor->ProductManager->getTotalWeight();
				OrderShippingModules::setDeliveryAddress($Editor->AddressManager->getAddress('delivery'));
	
				$titleField = '<select name="order_total[' . $count . '][title]" style="width:98%;">';
				$Quotes = OrderShippingModules::quote();
				//print_r($Quotes);
				foreach($Quotes as $qInfo){
					$titleField .= '<optgroup label="' . $qInfo['module'] . '">';
					foreach($qInfo['methods'] as $mInfo){
						$titleField .= '<option value="' . $qInfo['id'] . '_' . $mInfo['id'] . '"' . ($orderTotal->getModule() == $qInfo['id'] && $orderTotal->getMethod() == $mInfo['id'] ? ' selected="selected"' : '') . '>' . $mInfo['title'] . ' ( Recommended Price: ' . $currencies->format($mInfo['cost']) . ' )</option>';
					}
					$titleField .= '</optgroup>';
				}
	
				$titleField .= '</select>';
			}else{
				$titleField = '<input class="ui-widget-content" type="text" style="width:98%;" name="order_total[' . $count . '][title]" value="' . $orderTotal->getTitle() . '">';
			}
			
			$orderTotalTable->addBodyRow(array(
				'attr' => array(
					'data-count' => $count
				),
				'columns' => array(
					array(
						'addCls' => 'ui-widget-content',
						'css' => array(
							'border-top' => 'none'
						),
						'align' => 'center',
						'text' => $hiddenField . $titleField
					),
					array(
						'addCls' => 'ui-widget-content',
						'css' => array(
							'border-top' => 'none',
							'border-left' => 'none'
						),
						'align' => 'center',
						'text' => '<input class="ui-widget-content orderTotalValue" type="text" size="10" name="order_total[' . $count . '][value]" value="' . $orderTotal->getValue() . '"><input type="hidden" name="order_total[' . $count . '][sort_order]" class="totalSortOrder" value="' . $count . '"></span>'
					),
					array(
						'addCls' => 'ui-widget-content',
						'css' => array(
							'border-top' => 'none',
							'border-left' => 'none'
						),
						'align' => 'right',
						'text' => $typeMenu->draw()
					),
					array(
						'addCls' => 'ui-widget-content',
						'css' => array(
							'border-top' => 'none',
							'border-left' => 'none'
						),
						'align' => 'center',
						'text' => '<span class="ui-icon ui-icon-closethick deleteIcon" tooltip="Remove From Order"></span><span class="ui-icon ui-icon-arrow-4 moveTotalIcon" tooltip="Drag To Reorder"></span>'
					)
				)
			));
			$count++;
			$this->next();
		}
		$orderTotalTable->attr('data-nextId', $count);
		return $orderTotalTable;
	}
}
?>