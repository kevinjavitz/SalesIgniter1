<?php
class productPlugin_box_sets {
	function __construct($pId, &$productQuery){
		$this->productId = $pId;
	}
	
	public function loadProductInfo(){
		$QboxSet = Doctrine_Query::create()
		->select('box_id, products_id')
		->from('ProductsToBox');
		if ($this->isBox()){
			$QboxSet->where('box_id = ?', $this->productId);
		}elseif ($this->isInBox()){
			$QboxSet->where('products_id = ?', $this->productId);
		}
		$boxInfo = $QboxSet->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		
		if ($boxInfo){
			$Qname = Doctrine_Query::create()
			->select('products_name')
			->from('ProductsDescription pd')
			->where('products_id = ?', $boxInfo[0]['box_id'])
			->andWhere('language_id = ?', Session::get('languages_id'))
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);			
			if(!(empty($Qname[0]))){
				$this->boxProduct = array(
					'box_name' => $Qname[0]['products_name'],
					'box_id'   => $boxInfo[0]['box_id']
				);
			}
		}
	}

	function isNeeded(){
		if ($this->isBox() === true || $this->isInBox() === true){
			return true;
		}
		return false;
	}

	function isBox(){
		$Qcheck = Doctrine_Query::create()
		->select('count(*) as total')
		->from('ProductsToBox p2b')
		->where('box_id = ?', $this->productId)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qcheck[0]['total'] > 0){
			return true;
		}
		return false;
	}

	function isInBox(){
		$Qcheck = Doctrine_Query::create()
		->select('count(*) as total')
		->from('ProductsToBox p2b')
		->where('products_id = ?', $this->productId)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qcheck[0]['total'] > 0){
			return true;
		}
		return false;
	}

	function getBoxID(){
		return $this->boxProduct['box_id'];
	}

	function getName(){
		return $this->boxProduct['box_name'];
	}

	function getTotalDiscs(){
		$QtotalDiscs = Doctrine_Query::create()
		->select('count(products_id) as total')
		->from('ProductsToBox p2b')
		->where('box_id = ?', $this->boxProduct['box_id'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		return $QtotalDiscs[0]['total'];
	}

	function getDiscNumber($pID = false){
		if ($pID === false){
			return 0;
		}
		$Qcheck = Doctrine_Query::create()
		->select('disc')
		->from('ProductsToBox')
		->where('box_id = ?', $this->getBoxID())
		->andWhere('products_id = ?', $pID)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		return $Qcheck[0]['disc'];
	}

	function getDiscs($exclude = false, $onlyIds = false){
		$discs = array();
		if ($exclude !== false){
			if (!is_array($exclude)) $exclude = array($exclude);
		}
		
		$Qbox = Doctrine_Query::create()
		->select('products_id, disc')
		->from('ProductsToBox')
		->where('box_id = ?', $this->getBoxID())
		->orderBy('disc');

		if ($exclude !== false){
			$Qbox->andWhereNotIn('products_id',  $exclude);
		}
		$Result = $Qbox->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		
		if ($Result){
			foreach($Result as $pInfo){
				if ($onlyIds === true){
					$discs[] = $pInfo['products_id'];
				}else{

					$discs[] = array(
						'products_id' => $pInfo['products_id'],
						'disc_number'  => $pInfo['disc']
					);
				}
			}
		}
		return $discs;
	}
}
?>