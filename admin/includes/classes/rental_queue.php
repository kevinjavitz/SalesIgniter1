<?php
  include('../includes/classes/rental_queue-base.php');
  
  class rentalQueue_admin extends rentalQueue_base {
      function rentalQueue_admin($cID){
          parent::__construct($cID);
          parent::restore_contents();
      }
      
      function isEmpty(){
        return (sizeof($this->contents) <= 0);
      }
      
      function count_contents(){
          $total_items = 0;
          $contents = $this->contents;
          if (is_array($contents)) {
              reset($contents);
              while (list($products_id, ) = each($contents)) {
                  $total_items++;
              }
          }
        return $total_items;
      }
      
      function count_rented(){
          $QtotalRented = tep_db_query('select count(customers_id) as total from ' . TABLE_RENTED_QUEUE . ' where customers_id = "' . $this->customerID . '"');
          $totalRented = tep_db_fetch_array($QtotalRented);
        return $totalRented['total'];
      }
      
      function incrementTopRentals($pID){
	    $QrentalsTop = Doctrine_Core::getTable('RentalTop');
		$Qrental = $QrentalsTop->findOneByRentalTopId($pID);
		if ($Qrental){
			$Qrental->top += 1;
		}else{
			$Qrental = new RentalTop();
			$Qrental->products_id = $pID;
			$Qrental->top = 1;
		}
		$Qrental->save();
      }
      
      function getProducts(){
          $contents = $this->contents;
          
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
              	$purchaseTypeCls = $product->getPurchaseType('rental');
                  $products_array[$index] = array(
                      'productClass' => $product,
                      'id'       => $products_id,
                      'name'     => $product->getName(),
                      'model'    => $product->getModel(),
                      'image'    => $product->getImage(),
                      'priority' => $contents[$products_id]['priority'],
                      'canSend'  => $purchaseTypeCls->hasInventory(),
                      'isRare'   => false,
                      'availability' => $purchaseTypeCls->getAvailabilityName()
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
  }
?>