<?php
class rentalQueue_base {
	var $contents, $queueID;

	function rentalQueue_base($cID = false){
		$userAccount = &$this->getUserAccount();
		$this->customerID = ($userAccount->isLoggedIn() === false ? false : (int)$userAccount->getCustomerId());
		if ($cID !== false){
			$this->customerID = (int)$cID;
		}

		if ($this->customerID === false){
			unset($this);
			return false;
		}
	}

	private function &getUserAccount(){
		global $userAccount;
		if (Session::exists('userAccount') === true){
			$userAccount = &Session::getReference('userAccount');
		}
		return $userAccount;
	}

	function restore_contents() {
		$this->emptyQueue(false);
		$this->_getQueueFromDatabase();

		$this->cleanup();
	}

	function cleanup() {
	}

	function remove_all() {
		$this->emptyQueue();
	}

	function fixPriorities(){
		$priorityReorder = array();
		$priorityOriginal = array();
		foreach($this->contents as $index => $pInfo){
			$insert = new QueueReorderTemp();
			$insert->customers_id = $this->customerID;
			$insert->products_id = $index;
			$insert->old_pty = (isset($pInfo['prevPriority']) ? $pInfo['prevPriority'] : '0');
			$insert->new_pty = $pInfo['priority'];
			$insert->save();
		}

		$newPriority = 1;
		$Qproduct = Doctrine_Query::create()
		->from('QueueReorderTemp')
		->where('customers_id = ?', $this->customerID)
		->orderBy('new_pty ASC, old_pty DESC')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach($Qproduct as $qInfo){
			$this->contents[$qInfo['products_id']]['priority'] = $newPriority;
			$newPriority++;
		}

		$this->_putQueueInDatabase();
		Doctrine_Query::create()
		->delete('QueueReorderTemp')
		->where('customers_id = ?', $this->customerID)
		->execute();
	}

	function updateProduct($pID_string, $pInfo){
		$new_pInfo = $this->contents[$pID_string];
		foreach($pInfo as $key => $val){
			$new_pInfo[$key] = $val;
		}
		$this->addToContents($pID_string, $new_pInfo, 'update');
	}

	function addToContents($pID_string, $pInfo, $action = 'insert'){
		$this->contents[$pID_string] = $pInfo;

		$this->insertQueue(array(
			'product_id' => $pID_string,
			'priority'   => $pInfo['priority']
		), $action);

		/*foreach($pInfo['attributes'] as $oID => $vID){
			$this->insertQueueAttribute(array(
				'product_id'              => $pID_string,
				'product_option_id'       => (int)$oID,
				'product_option_value_id' => (int)$vID
			), $action);
		}*/
		$this->_finishAdd();
	}

	function updatePriority($products_id, $priority = '') {
		if (empty($priority)) return true; // nothing needs to be updated if theres no priority, so we return true..

		$this->addtoContents($products_id, array(
			'priority' => $priority
		), 'update');
	}

	function _finishAdd(){
		$this->cleanup();
		$this->queueID = $this->generate_queue_id();
	}

	function removeFromQueue($products_id, $fixPriorities = true) {
		unset($this->contents[$products_id]);
		// remove from database
		Doctrine_Query::create()
		->delete('RentalQueueTable')
		->where('customers_id = ?', $this->customerID)
		->andWhere('products_id = ?', $products_id)
		->execute();

		if ($fixPriorities === true){
			$this->fixPriorities();
		}
		
		// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
		$this->queueID = $this->generate_queue_id();
	}

	function emptyQueue($reset_database = false) {
		$this->contents = array();

		if ($reset_database == true) {
			Doctrine_Query::create()
			->delete('RentalQueueTable')
			->where('customers_id = ?', $this->customerID)
			->execute();
		}
		unset($this->queueID);
		Session::remove('queueID');
		//$this->customerID = false;
	}

	function insertQueue($pInfo, $action){
		$RentalQueue = Doctrine_Core::getTable('RentalQueueTable');
		if ($action == 'insert'){
			$Item = new RentalQueueTable();
			$Item->customers_id = $this->customerID;
			$Item->products_id = $pInfo['product_id'];
		}else{
			$Item = $RentalQueue->findOneByCustomersIdAndProductsId($this->customerID, $pInfo['product_id']);
		}
		$Item->priority = $pInfo['priority'];
		$Item->save();
	}

	function insertQueueAttribute($pInfo, $action){
		return true;
		$Attributes = Doctrine_Core::getTable('RentalQueueAttributes');
		if ($action == 'insert'){
			$Attribute = new RentalQueueAttributes();
			$Attribute->customers_id = $this->customerID;
			$Attribute->products_id = $pInfo['product_id'];
			$Attribute->products_options_id = $pInfo['product_option_id'];
		}else{
			$Attribute = $Attributes->findOneByProductsIdAndCustomersIdAndProductsOptionsId($pInfo['product_id'], $this->customerID, $pInfo['product_option_id']);
		}
	}

	function _putQueueInDatabase(){
		if (is_array($this->contents)) {
			reset($this->contents);
			while (list($products_id, ) = each($this->contents)) {
				$action = 'update';
				$Qcheck = Doctrine_Query::create()
				->select('products_id')
				->from('RentalQueueTable')
				->where('customers_id = ?', $this->customerID)
				->andWhere('products_id = ?', $products_id)
				->execute();
				if ($Qcheck->count() <= 0){
					$action = 'insert';
				}

				$this->insertQueue(array(
					'product_id' => $products_id,
					'priority'   => $this->contents[$products_id]['priority']
				), $action);

				/*if (isset($this->contents[$products_id]['attributes'])) {
				reset($this->contents[$products_id]['attributes']);
				while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
				$this->insertQueueAttribute(array(
				'product_id'              => $products_id,
				'product_option_id'       => (int)$option,
				'product_option_value_id' => (int)$value
				), $action);
				}
				}*/
			}
		}
	}

	function _getQueueFromDatabase(){
		$QrentalQueue = Doctrine_Query::create()
		->from('RentalQueueTable')
		->where('customers_id = ?', $this->customerID)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($QrentalQueue){
			foreach($QrentalQueue as $qInfo){
				$this->contents[$qInfo['products_id']] = array(
					'priority' => $qInfo['priority']
				);

				// attributes
				/* May be supported later on
				$Qattribtue = dataAccess::setQuery('select products_options_id, products_options_value_id from {queue_attributes} where customers_id = {customer_id} and products_id = {product_id}');
				$Qattribute->setTable('{queue_attributes}', TABLE_RENTAL_QUEUE_ATTRIBUTES);
				$Qattribute->setTable('{customer_id}', $this->customerID);
				$Qattribute->setTable('{product_id}', $Qproduct->getVal('products_id'));
				while ($Qattribute->next() !== false){
				$this->contents[$Qproduct->getVal('products_id')]['attributes'][$Qattribute->getVal('products_options_id')] = $Qattribute->getVal('products_options_value_id');
				}
				*/
			}
		}
	}

	function removeSentItems(){
		if (sizeof($this->contents) > 0){
			$userAccount = &$this->getUserAccount();
			foreach($this->contents as $pID_string => $qInfo){
				$Qcheck = Doctrine_Query::create()
				->select('products_id')
				->from('RentedQueue')
				->where('products_id = ?', $pID_string)
				->andWhere('customers_id = ?', $userAccount->getCustomerId())
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				if ($Qcheck){
					$this->removeFromQueue($pID_string, false);
				}
			}
			$this->fixPriorities();
		}
	}

	function unserialize($broken) {
		for(reset($broken);$kv=each($broken);) {
			$key=$kv['key'];
			if (gettype($this->$key)!="user function")
			$this->$key=$kv['value'];
		}
	}

	function generate_queue_id($length = 5) {
		return tep_create_random_value($length, 'digits');
	}
}
?>