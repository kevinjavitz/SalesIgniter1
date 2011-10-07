<?php
/**
 * Product manager for the order class
 *
 * @package Order
 * @author Stephen Walker <stephen@itwebexperts.com>
 * @copyright Copyright (c) 2010, I.T. Web Experts
 */

require(dirname(__FILE__) . '/Product.php');
require(dirname(__FILE__) . '/RentalMembershipProduct.php');

class OrderProductManager {
	protected $orderId = null;
	protected $Contents = array();

	public function __construct($orderedProducts = null){
		if (is_null($orderedProducts) === false){
			foreach($orderedProducts as $i => $pInfo){

				if(isset($pInfo['purchase_type']) && $pInfo['purchase_type'] != 'membership'){
					$orderedProduct = new OrderProduct($pInfo);
				}else{
					$orderedProduct = new OrderRentalMembershipProduct($pInfo);
				}
				$this->add($orderedProduct);
			}
		}
	}

	public function init(){
		foreach($this->getContents() as $cartProduct){
			$cartProduct->init();
		}
	}

	public function setOrderId($val){
		$this->orderId = $val;
	}
	
	public function getContents($id = null){
		if (is_null($id) === false){
			if (array_key_exists($id, $this->Contents)){
				return $this->Contents[$id];
			}
		}else{
			return $this->Contents;
		}
		return null;
	}

	public function add($orderedProduct){
		$this->Contents[$orderedProduct->getId()] = $orderedProduct;
	}

	public function get($id){
		return $this->getContents($id);
	}
	
	public function getTotalWeight(){
		$total_weight = 0;
		foreach($this->Contents as $Product){
			$total_weight += $Product->getWeight();
		}
		return $total_weight;
	}

	public function listProducts($showTableHeading = true, $showQty = true, $showBarcode = true, $showModel = true, $showName = true, $showExtraInfo = true, $showPrice = true, $showPriceWithTax = true, $showTotal = true, $showTotalWithTax = true, $showTax = true) {
		global  $currencies, $typeNames, $Order;
		$productsTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0)->css('width', '100%');

		$productTableHeaderColumns = array();
		if($showQty){
			$productTableHeaderColumns[] = array('text' => sysLanguage::get('TABLE_HEADING_PRODUCTS_QTY'));
		}
		if($showName){
			$productTableHeaderColumns[] = array('text' => sysLanguage::get('TABLE_HEADING_PRODUCTS_NAME'));
		}
		if($showBarcode){
			$productTableHeaderColumns[] = array('text' => sysLanguage::get('TABLE_HEADING_PRODUCTS_BARCODE'));
		}
		if($showModel){
			$productTableHeaderColumns[] = array('text' => sysLanguage::get('TABLE_HEADING_PRODUCTS_MODEL'));
		}
		if($showTax){
			$productTableHeaderColumns[] = array('text' => sysLanguage::get('TABLE_HEADING_TAX'));
		}
		if($showPrice){
			$productTableHeaderColumns[] = array('text' => sysLanguage::get('TABLE_HEADING_PRICE_EXCLUDING_TAX'));
		}
		if($showPriceWithTax){
			$productTableHeaderColumns[] = array('text' => sysLanguage::get('TABLE_HEADING_PRICE_INCLUDING_TAX'));
		}
		if($showTotal){
			$productTableHeaderColumns[] = array('text' => sysLanguage::get('TABLE_HEADING_TOTAL_EXCLUDING_TAX'));
		}
		if($showTotalWithTax){
			$productTableHeaderColumns[] = array('text' => sysLanguage::get('TABLE_HEADING_TOTAL_INCLUDING_TAX'));
		}


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

		foreach($this->getContents() as $orderedProduct){
			$orderedProductId = $orderedProduct->getOrderedProductId();
			$purchaseType = $orderedProduct->getPurchaseType();
			$finalPrice = $orderedProduct->getPrice();
			$finalPriceWithTax = $orderedProduct->getPrice(true);
			$taxRate = $orderedProduct->getTaxRate();
			$productQty = $orderedProduct->getQuantity();
			$productModel = $orderedProduct->getModel();
			$i = 0;
			$barcode = '';
			while (true){
				$barcodeName = $orderedProduct->getBarcode($i);
				if ($barcodeName ==  false){
					break;
				}
				$barcode .= $barcodeName . '<br/>';
				$i++;
			}

			$productsName = $orderedProduct->getNameHtml($showExtraInfo);

			$bodyColumns = array();
			if($showQty){
				$bodyColumns[] = array(
					'align' => 'right',
					'text' => $productQty . '&nbsp;x'
				);
			}
			if($showName){
				$bodyColumns[] = array(
					'text' => $productsName
				);
			}
			if($showBarcode){
				$bodyColumns[] = array(
					'text' => $barcode
				);
			}
			if($showModel){
				$bodyColumns[] = array(
					'text' => $productModel
				);
			}
			if($showTax){
				$bodyColumns[] = array(
					'align' => 'right',
					'text' => $taxRate . '%'
				);
			}
			if($showPrice){
				$bodyColumns[] = array(
					'align' => 'right',
					'text' => '<b>' . $currencies->format($finalPrice, true, $Order->getCurrency(), $Order->getCurrencyValue()) . '</b>'
				);
			}
			if($showPriceWithTax){
				$bodyColumns[] = array(
					'align' => 'right',
					'text' => '<b>' . $currencies->format($finalPriceWithTax, true, $Order->getCurrency(), $Order->getCurrencyValue()) . '</b>'
				);
			}
			if($showTotal){
				$bodyColumns[] = array(
					'align' => 'right',
					'text' => '<b>' . $currencies->format($finalPrice * $productQty, true, $Order->getCurrency(), $Order->getCurrencyValue()) . '</b>'
				);
			}
			if($showTotalWithTax){
				$bodyColumns[] = array(
					'align' => 'right',
					'text' => '<b>' . $currencies->format($finalPriceWithTax * $productQty, true, $Order->getCurrency(), $Order->getCurrencyValue()) . '</b>'
				);
			}

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
				'columns' => $bodyColumns
			));
		}

		return $productsTable;
	}
}
?>