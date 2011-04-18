<?php
/*
	Pay Per Rentals Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class payPerRentals_admin_orders_default extends Extension_payPerRentals {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		global $appExtension;
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'AdminOrdersListingBeforeExecute',
            'OrdersProductsExportQueryFileLayoutHeader',
            'OrderProductsExportBeforeFileLineCommit',
            'OrdersExportQueryBeforeExecute',
			'OrderDetailsTabPaneInsideComments',
			'AdminOrdersListingSearchForm',
            'OrdersListingAddGridHeader',
            'OrdersListingAddGridBody',
			'CancelOrderAfterExecute',
			'AdminOrdersListingExportFields'
		), null, $this);
	}

	public function AdminOrdersListingExportFields(&$fieldsArray){
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_EVENT_AS_COLUMN_ON_ORDER_PAGE')== 'True'){
			$fieldsArray[] = 'v_orders_products_event_name';
			$fieldsArray[] = 'v_orders_products_event_date';
		}
		 $fieldsArray[] = 'v_orders_products_semester_name';
		 $fieldsArray[] = 'v_orders_products_insurance';
         $fieldsArray[] = 'v_orders_products_start_date';
         $fieldsArray[] = 'v_orders_products_end_date';
         $fieldsArray[] = 'v_orders_products_shipping_method_title';
         $fieldsArray[] = 'v_orders_products_shipping_cost';
         $fieldsArray[] = 'v_orders_products_shipping_days_after';
         $fieldsArray[] = 'v_orders_products_shipping_days_before';
	}

    public function OrdersProductsExportQueryFileLayoutHeader(&$dataExport, $i){
        if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_EVENT_AS_COLUMN_ON_ORDER_PAGE')== 'True'){
			$fields = array();
			if (isset($_POST['v_orders_products_event_name'])){
				$fields[] = 'v_orders_products_event_name_'.$i;
			}
			if (isset($_POST['v_orders_products_event_date'])){
				$fields[] = 'v_orders_products_event_date_'.$i;
			}
			if (sizeof($fields) > 0){
            	$dataExport->setHeaders($fields);
			}
        }
		$fieldsp = array();
		if (isset($_POST['v_orders_products_semester_name'])){
			$fieldsp[] = 'v_orders_products_semester_name_'.$i;
		}
		if (isset($_POST['v_orders_products_insurance'])){
			$fieldsp[] = 'v_orders_products_insurance_'.$i;
		}
		if (isset($_POST['v_orders_products_start_date'])){
			$fieldsp[] = 'v_orders_products_start_date_'.$i;
		}
		if (isset($_POST['v_orders_products_end_date'])){
			$fieldsp[] = 'v_orders_products_end_date_'.$i;
		}
		if (isset($_POST['v_orders_products_shipping_method_title'])){
			$fieldsp[] = 'v_orders_products_shipping_method_title_'.$i;
		}
		if (isset($_POST['v_orders_products_shipping_cost'])){
			$fieldsp[] = 'v_orders_products_shipping_cost_'.$i;
		}
		if (isset($_POST['v_orders_products_shipping_days_after'])){
			$fieldsp[] = 'v_orders_products_shipping_days_after_'.$i;
		}
		if (isset($_POST['v_orders_products_shipping_days_before'])){
			$fieldsp[] = 'v_orders_products_shipping_days_before_'.$i;
		}
		if (sizeof($fieldsp) > 0){
        	$dataExport->setHeaders($fieldsp);
		}
    }

    public function OrderProductsExportBeforeFileLineCommit(&$pInfo, &$opInfo, $i){
         if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_EVENT_AS_COLUMN_ON_ORDER_PAGE')== 'True'){
			if (isset($_POST['v_orders_products_event_name'])){
            	$pInfo['v_orders_products_event_name_'. $i] = $opInfo['OrdersProductsReservation'][0]['event_name'];
			}
			if (isset($_POST['v_orders_products_event_date'])){
            	$pInfo['v_orders_products_event_date_'. $i] = $opInfo['OrdersProductsReservation'][0]['event_date'];
			}
         }
		  if (isset($_POST['v_orders_products_semester_name'])){
         	$pInfo['v_orders_products_semester_name_'. $i] = $opInfo['OrdersProductsReservation'][0]['semester_name'];
		 }
		 if (isset($_POST['v_orders_products_insurance'])){
         	$pInfo['v_orders_products_insurance_'. $i] = $opInfo['OrdersProductsReservation'][0]['insurance'];
		 }
		 if (isset($_POST['v_orders_products_start_date'])){
         	$pInfo['v_orders_products_start_date_'. $i] = $opInfo['OrdersProductsReservation'][0]['start_date'];
		 }
		 if (isset($_POST['v_orders_products_end_date'])){
         	$pInfo['v_orders_products_end_date_'. $i] = $opInfo['OrdersProductsReservation'][0]['end_date'];
		 }
		 if (isset($_POST['v_orders_products_shipping_method_title'])){
         	$pInfo['v_orders_products_shipping_method_title_'. $i] = $opInfo['OrdersProductsReservation'][0]['shipping_method_title'];
		 }
		 if (isset($_POST['v_orders_products_shipping_cost'])){
         	$pInfo['v_orders_products_shipping_cost_'. $i] = $opInfo['OrdersProductsReservation'][0]['shipping_cost'];
		 }
		 if (isset($_POST['v_orders_products_shipping_days_after'])){
         	$pInfo['v_orders_products_shipping_days_after_'. $i] = $opInfo['OrdersProductsReservation'][0]['shipping_days_after'];
		 }
		 if (isset($_POST['v_orders_products_shipping_days_before'])){
         	$pInfo['v_orders_products_shipping_days_before_'. $i] = $opInfo['OrdersProductsReservation'][0]['shipping_days_before'];
		 }
		 if (isset($_POST['v_orders_products_barcode'])){
			 if (isset($opInfo['OrdersProductsReservation'][0]['barcode_id']) && !empty($opInfo['OrdersProductsReservation'][0]['barcode_id'])){
					$barcodeTable = Doctrine_Core::getTable('ProductsInventoryBarcodes')->findOneByBarcodeId($opInfo['OrdersProductsReservation'][0]['barcode_id']);
					$pInfo['v_orders_products_barcode_'. $i] = $barcodeTable->barcode;
			 }else{
					$pInfo['v_orders_products_barcode_'. $i] = '';
			 }
		 }
    }

    public function OrdersExportQueryBeforeExecute(&$QfileLayout){
        $QfileLayout->leftJoin('op.OrdersProductsReservation ops');
    }   

	public function AdminOrdersListingBeforeExecute(&$Qorders){
        $Qorders->addSelect('ops.event_name, ops.shipping_method_title, op.orders_products_id')
                ->leftJoin('o.OrdersProducts op')
                ->leftJoin('op.OrdersProductsReservation ops');
        if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_EVENT_AS_COLUMN_ON_ORDER_PAGE')== 'True'){
            if (isset($_GET['event_name']) && (!empty($_GET['event_name'])) && ($_GET['event_name'] != 'None')){
                $Qorders->andWhere('ops.event_name = ?', $_GET['event_name']);
            }
        }
        if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_LOS_AS_COLUMN_ON_ORDER_PAGE') == 'True'){
            if (isset($_GET['shipping_name']) && (!empty($_GET['shipping_name'])) && ($_GET['shipping_name'] != '0')){
                $Qorders->andWhere('ops.shipping_method = ?', $_GET['shipping_name']);
            }
        }
		if(isset($_GET['rental_notes_filter']) && $_GET['rental_notes_filter'] != -1){
			$Qorders->addSelect('o.rental_notes');
			if($_GET['rental_notes_filter'] == 'view_only_rental_notes'){
				$Qorders->andWhere('o.rental_notes<>?','');
			}
		}

	}

	public function AdminOrdersListingSearchForm(&$searchForm){
        if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_EVENT_AS_COLUMN_ON_ORDER_PAGE') == 'True'){
            $htmlEvents = htmlBase::newElement('selectbox')
		            ->setName('event_name')
		            ->setLabel('Events: ')
		            ->setLabelPosition('before');

            $Qevent = Doctrine_Query::create()
			->from('PayPerRentalEvents')
			->orderBy('events_date')
			->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

            $htmlEvents->addOption('None', 'Select an Event');

             if (isset($_GET['event_name']) && (!empty($_GET['event_name']))){
                $htmlEvents->selectOptionByValue($_GET['event_name']);
             }

		    if($Qevent){
                foreach($Qevent as $eInfo){
                    $htmlEvents->addOption($eInfo['events_name'],$eInfo['events_name']);
                }
		    }
            $searchForm->append($htmlEvents);         
        }
        if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_LOS_AS_COLUMN_ON_ORDER_PAGE') == 'True'){
			$module = OrderShippingModules::getModule('zonereservation');
		    $quotes = $module->quote();
            $htmlShips = htmlBase::newElement('selectbox')
		            ->setName('shipping_name')
		            ->setLabel('Level of Service: ')
		            ->setLabelPosition('before');
            $htmlShips->addOption('0', 'Select Level of Service');
            for($i=0, $n=sizeof($quotes['methods']); $i<$n; $i++){
				$htmlShips->addOption($quotes['methods'][$i]['id'],$quotes['methods'][$i]['title']);
		    }
            if (isset($_GET['shipping_name']) && (!empty($_GET['shipping_name']))){
                $htmlShips->selectOptionByValue($_GET['shipping_name']);
             }
              $searchForm->append($htmlShips);  
        }
		$htmlRentalNotes = htmlBase::newElement('selectbox')
		->setName('rental_notes_filter')
		->setLabel('Rental Notes Filter: ')
		->setLabelPosition('before');
		$htmlRentalNotes->addOption('-1',sysLanguage::get('SELECT_OPTION_RENTAL_NOTES'));
		$htmlRentalNotes->addOption('view_rental_notes',sysLanguage::get('TEXT_VIEW_RENTAL_NOTES'));
		$htmlRentalNotes->addOption('view_only_rental_notes',sysLanguage::get('TEXT_VIEW_ONLY_RENTAL_NOTES'));
		if(isset($_GET['rental_notes_filter'])){
			$htmlRentalNotes->selectOptionByValue($_GET['rental_notes_filter']);
		}
		$searchForm->append($htmlRentalNotes);
	}

    public function OrdersListingAddGridHeader(&$gridHeaderColumns){
        if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_EVENT_AS_COLUMN_ON_ORDER_PAGE') == 'True'){
            $gridHeaderColumns[] = array('text' => 'Event');
        }

        if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_LOS_AS_COLUMN_ON_ORDER_PAGE') == 'True'){
            $gridHeaderColumns[] = array('text' => 'Shipping');
        }
	    if(isset($_GET['rental_notes_filter']) && $_GET['rental_notes_filter'] != -1){
	        $gridHeaderColumns[] = array('text' => 'Rental Notes');
	    }

    }

    public function OrdersListingAddGridBody(&$order, &$gridBodyColumns){
        if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_EVENT_AS_COLUMN_ON_ORDER_PAGE') == 'True'){
			if (isset($order['OrdersProducts'][0]['OrdersProductsReservation'][0]['event_name'])){
				$val = $order['OrdersProducts'][0]['OrdersProductsReservation'][0]['event_name'];
			}else{
				$val = '';
			}
            $gridBodyColumns[] = array('text' => $val, 'align' => 'left');
        }

        if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_LOS_AS_COLUMN_ON_ORDER_PAGE') == 'True'){
			if (isset($order['OrdersProducts'][0]['OrdersProductsReservation'][0]['shipping_method_title'])){
				$val = $order['OrdersProducts'][0]['OrdersProductsReservation'][0]['shipping_method_title'];
			}else{
				$val = '';
			}
            $gridBodyColumns[] = array('text' => $val, 'align' => 'left');
        }
	    if(isset($_GET['rental_notes_filter']) && $_GET['rental_notes_filter'] != -1){
		     $gridBodyColumns[] = array('text' => $order['rental_notes'], 'align' => 'left');
	    }
    }
	public function CancelOrderAfterExecute($oID){
		$QOrdersQuery = Doctrine_Query::create()
		->from('Orders o')
		->leftJoin('o.OrdersAddresses oa')
		->leftJoin('o.OrdersProducts op')
		->leftJoin('op.OrdersProductsReservation opr')
		->leftJoin('opr.ProductsInventoryBarcodes ib')
		->leftJoin('ib.ProductsInventory ibi')
		->leftJoin('opr.ProductsInventoryQuantity iq')
		->leftJoin('iq.ProductsInventory iqi')
		->where('o.orders_id = ?', $oID)
		->andWhere('oa.address_type = ?', 'customer')
		->andWhere('parent_id IS NULL');

		$Qorders = $QOrdersQuery->execute();
		foreach($Qorders as $oInfo){
			foreach($oInfo->OrdersProducts as $opInfo){
				foreach ($opInfo->OrdersProductsReservation as $oprInfo) {
					$reservationId = $oprInfo->orders_products_reservations_id;
					$trackMethod = $oprInfo->track_method;

					if ($trackMethod == 'barcode') {
						$oprInfo->ProductsInventoryBarcodes->status = 'A';
					} elseif ($trackMethod == 'quantity') {
						$oprInfo->ProductsInventoryQuantity->qty_out--;
						$oprInfo->ProductsInventoryQuantity->available++;
					}
					$oprInfo->save();
				}
				$opInfo->OrdersProductsReservation->delete(); //delete OrdersProducts to?
			}
		}
	}
	

}
?>