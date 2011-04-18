<?php
/**
 * Totals manager for the order class
 *
 * @package Order
 * @author Stephen Walker <stephen@itwebexperts.com>
 * @copyright Copyright (c) 2010, I.T. Web Experts
 */

require(dirname(__FILE__) . '/Total.php');

class OrderTotalManager extends SplObjectStorage {

	public function __construct($orderTotals = null){
		if (is_null($orderTotals) === false){
			foreach($orderTotals as $i => $tInfo){
				$orderTotal = new OrderTotal($tInfo);
				$this->add($orderTotal);
			}
		}
	}

	public function setOrderId($val){
		$this->orderId = $val;
	}

	public function add($orderTotal){
		$this->attach($orderTotal);
	}

	public function getTotalValue($type){
		$OrderTotal = $this->get($type);
		if (is_null($OrderTotal) === false){
			return $OrderTotal->getValue();
		}
		return null;
	}

	public function get($moduleType){
		$orderTotal = null;
		$this->rewind();
		while($this->valid()){
			$orderTotal = $this->current();
			if ($orderTotal->getModuleType() == $moduleType){
				break;
			}
			$this->next();
			$orderTotal = null;
		}
		return $orderTotal;
	}

	public function show(){
		$orderTotalTable = htmlBase::newElement('table')
		->setCellPadding(2)
		->setCellSpacing(0);

		$this->rewind();
		while($this->valid()){
			$orderTotal = $this->current();

			$orderTotalTable->addBodyRow(array(
				'columns' => array(
					array('align' => 'right', 'text' => $orderTotal->getTitle()),
					array('align' => 'right', 'text' => $orderTotal->getText())
				)
			));
			$this->next();
		}

		return $orderTotalTable;
	}
}
?>