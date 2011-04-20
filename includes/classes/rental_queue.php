<?php
/*
  $Id: shopping_cart.php,v 1.35 2003/06/25 21:14:33 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  class rentalQueue {
      var $contents, $queueID;
      
      function rentalQueue() {
          $this->userAccount = &Session::getReference('userAccount');
          $this->rentalQueueBase = &Session::getReference('rentalQueueBase');
      }
      
      function _notify($pID){
          Session::set('new_products_id_in_queue', $pID);
      }
      
      function rentalAllowed($cID){
	      $Qcheck = Doctrine_Query::create()
		      ->select('activate')
		      ->from('CustomersMembership')
		      ->where('ismember = ?', 'M')
		      ->andWhere('customers_id = ?', $cID)
		      ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
          if ($Qcheck){
              if ($Qcheck[0]['activate'] == 'Y'){
                  return true;
              }else{
                  return 'inactive';
              }
          }
        return 'membership';
      }
      
      function updatePriority($pID_string, $priority, $prevPriority = '', $attributes = ''){
          $this->rentalQueueBase->updateProduct($pID_string, array(
              'priority'     => $priority,
              'prevPriority' => $prevPriority
          ));
      }
      
      function addToQueue($products_id, $attributes = '') {
        global $messageStack, $userAccount;
          $products_id_string = tep_get_uprid($products_id, $attributes);
          $products_id = tep_get_prid($products_id_string);
          /*if ($notify == true) {
              $this->_notify($products_id_string);
          }*/
	      $currentPlan = $userAccount->plugins['membership']->getPlanId();
	      $QProduct = Doctrine_Query::create()
		  ->select('membership_enabled')
		  ->from('Products')
		  ->where('products_id=?',$products_id)
		  ->fetchOne();
		  $notEnabledMemberships = explode(';',$QProduct->membership_enabled);

		  if(in_array($currentPlan, $notEnabledMemberships)){
		    Session::set('add_to_queue_product_id', $products_id);
			Session::set('add_to_queue_product_attrib', $attributes);
			$messageStack->addSession('pageStack',sprintf(sysLanguage::get('TEXT_UPGRADE_PLAN'),itw_app_link(null,'contact_us','default')),'warning');
			tep_redirect( itw_app_link(null,'rentals','queue'));
			//tep_redirect(itw_app_link('checkoutType=rental','checkout','default'));
		  }

          if ($this->in_queue($products_id_string)) {
              $messageStack->addSession('pageStack', sysLanguage::get('TEXT_DUPLICATION'), 'warning');
              tep_redirect( itw_app_link(null,'rentals','queue'));
          } else {
	          $QmaxPty = Doctrine_Query::create()
		          ->select('MAX(priority) as lastPriority')
		          ->from('RentalQueue')
		          ->where('customers_id = ?', $this->userAccount->getCustomerId())
		          ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

              $lastPriority = $QmaxPty[0]['lastPriority'];
              if ($lastPriority > 0) {
                  $priority = $lastPriority + 1;
              } else {
                  $priority = 1;
              }
              $this->rentalQueueBase->addToContents($products_id_string, array(
                  'priority' => $priority,
                  'prevPriority' => $priority
              ));
          }
      }
      
      function addBoxSet($boxID){
	      $Qdisc = Doctrine_Query::create()
		      ->select('products_id')
		      ->from('ProductsToBox')
		      ->where('box_id = ?', $boxID)
		      ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
          if (!$Qdisc){
	          $Qcheck = Doctrine_Query::create()
		          ->select('box_id')
		          ->from('ProductsToBox')
		          ->where('products_id = ?', $boxID)
		          ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
              if (!$Qcheck){
                  tep_redirect(itw_app_link('products_id=' . $boxID . '&msg=noboxset', 'product', 'info'));
              }else{
	              $Qdisc = Doctrine_Query::create()
		              ->select('products_id')
		              ->from('ProductsToBox')
		              ->where('box_id = ?', $Qcheck[0]['box_id'])
		              ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
              }
          }
          
          foreach($Qdisc as $disc){
              $product = new product($disc[0]['products_id']);
              if ($product->isActive() === true){
                  $this->addToQueue($disc[0]['products_id']);
              }
          }
      }
      
      function count_contents() {
          $total_items = 0;
          $contents = $this->rentalQueueBase->contents;
          if (is_array($contents)) {
              reset($contents);
              while (list($products_id, ) = each($contents)) {
                  $total_items++;
              }
          }
        return $total_items;
      }

      function get_priority($products_id) {
          $contents = $this->rentalQueueBase->contents;
          if (isset($contents[$products_id])) {
              return $contents[$products_id]['priority'];
          } else {
              return 0;
          }
      }
      
      function in_queue($products_id) {		  
          if (isset($this->rentalQueueBase->contents[$products_id])) {
              return true;
          } else {
              return false;
          }
      }
      
      function get_product_id_list() {
          $product_id_list = '';
          $contents = $this->rentalQueueBase->contents;
          if (is_array($contents)) {
              reset($contents);
              while (list($products_id, ) = each($contents)) {
                  $product_id_list .= ', ' . $products_id;
              }
          }
        return substr($product_id_list, 2);
      }
      
      function get_products() {
          if (!is_array($this->rentalQueueBase->contents)) return;
          
          $contents = $this->rentalQueueBase->contents;
          
          $products_array = array();
          //reset($contents);

          $sortOrder = array();
          foreach($contents as $index => $pInfo){
              $sortOrder[$index] = $pInfo['priority'];
          }
          asort($sortOrder);

          $index = 0;
          foreach($sortOrder as $products_id => $priority) {
              $pID = tep_get_prid($products_id);
              $product = new product($pID);
              if ($product->isValid()){
              	$purchaseTypeClass = $product->getPurchaseType('rental');
                  $products_array[$index] = array(
                      'productClass' => $product,
                      'id'           => $products_id,
                      'name'         => $product->getName(),
                      'model'        => $product->getModel(),
                      'image'        => $product->getImage(),
                      'priority'     => $contents[$products_id]['priority'],
                      'availability' => $purchaseTypeClass->getAvailabilityName()
                  );
                  
                  if ($product->isInBox() === true){
                      $products_array[$index]['totalDiscs'] = $product->getTotalDiscs();
                      $products_array[$index]['discNumber'] = $product->getDiscNumber($pID);
                      $products_array[$index]['boxName'] = $product->getBoxName();
                  }
                  $index++;
              }
          }
        return $products_array;
      }
      
      function unserialize($broken) {
          for(reset($broken);$kv=each($broken);) {
              $key=$kv['key'];
              if (gettype($this->$key)!="user function"){
                  $this->$key=$kv['value'];
              }
          }
      }
  }
?>