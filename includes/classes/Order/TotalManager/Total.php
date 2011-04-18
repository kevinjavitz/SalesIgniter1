<?php
class OrderTotal {
	protected $totalInfo = array();
	
	public function __construct($tInfo = null){
		if (is_null($tInfo) === false){
			$this->totalInfo = $tInfo;
		}
	}

	public function hasOrderTotalId(){
		return array_key_exists('orders_total_id', $this->totalInfo);
	}
	
	public function getOrderTotalId(){
		return $this->totalInfo['orders_total_id'];
	}

	public function getModuleType(){
		return $this->totalInfo['module_type'];
	}

	public function getTitle(){
		return $this->totalInfo['title'];
	}

	public function getText(){
		return $this->totalInfo['text'];
	}

	public function getValue(){
		return $this->totalInfo['value'];
	}

	public function getSortOrder(){
		return $this->totalInfo['sort_order'];
	}

	public function getModule(){
		return $this->totalInfo['module'];
	}

	public function getMethod(){
		return $this->totalInfo['method'];
	}
}
?>