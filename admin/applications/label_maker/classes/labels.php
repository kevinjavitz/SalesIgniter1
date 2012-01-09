<?php
	class labelMaker {
		public function __construct(){
			$this->products = array();
			$this->settings = array();
			$this->labelFormat = null;
			$this->labelType = null;
		}
		
		public function setStartDate($val){ $this->settings['startDate'] = $val; }
		public function setEndDate($val){ $this->settings['endDate'] = $val; }
		public function setFilter($val){ $this->settings['filter'] = $val; }
		public function setLocation($val){ $this->settings['labelLocation'] = $val; }
		public function setInventoryCenter($val){ $this->settings['invCenter'] = $val; }
		
		public function setType($type){
			$this->labelType = $type;
			$className = 'labelMaker_' . $this->labelType;
			if (!class_exists($className)){
				require(dirname(__FILE__) . '/label_types/' . $this->labelType . '.php');
			}
			$this->labelFormat = new $className;
		}
		
		public function addProduct($productId, $type){
			if (!array_key_exists($type, $this->products)){
				$this->products[$type] = array();
			}
			
			$this->products[$type][] = $productId;
		}
		
		public function getData(){
			$data = array();
			foreach($this->products as $type => $products){
				$className = 'labelMaker_' . $type;
				if (!class_exists($className)){
					require(dirname(__FILE__) . '/purchase_types/' . $type . '.php');
				}
				$class = new $className;
				foreach($class->toArray($products, $this->settings) as $pInfo){
					$data[] = $pInfo;
				}
			}
			return $data;
		}
		
		public function draw($type){
			$data = $this->getData();
			//echo 'DATA::';print_r($data);
			return $this->labelFormat->draw($data, $type);
		}
	}
?>