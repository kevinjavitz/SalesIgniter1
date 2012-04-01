<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Listing
 *
 * @author Stephen
 */

	namespace Applications\OrdersManager\Pages;

	use \htmlBase;
	use \EventManager;
	use \objectInfo;
	use \sysLanguage;
	use \Session;
	use \Doctrine_Query;
	
	class Listing {

		public function __construct(){
			
		}

		public function output(){
			$Qorders = Doctrine_Query::create()
			->select('o.orders_id, a.entry_name, o.date_purchased, o.customers_id, o.last_modified, o.currency, o.currency_value, s.orders_status_id, sd.orders_status_name, ot.text as order_total, o.payment_module')
			->from('Orders o')
			->leftJoin('o.OrdersTotal ot')
			->leftJoin('o.OrdersAddresses a')
			->leftJoin('o.OrdersStatus s')
			->leftJoin('s.OrdersStatusDescription sd')
			->where('sd.language_id = ?', Session::get('languages_id'))
			->andWhereIn('ot.module_type', array('total', 'ot_total'))
			->andWhere('a.address_type = ?', 'customer')
			->orderBy('o.date_purchased desc');

			EventManager::notify('AdminOrdersListingBeforeExecute', &$Qorders);

			if (isset($_GET['cID'])){
				$Qorders->andWhere('o.customers_id = ?', (int) $_GET['cID']);
			}elseif (isset($_GET['status']) && is_numeric($_GET['status']) && $_GET['status'] > 0){
				$Qorders->andWhere('s.orders_status_id = ?', (int) $_GET['status']);
			}

			if (isset($_GET['start_date']) && !empty($_GET['start_date'])){
				$datetime = date('Y-m-d h:i:s', strtotime($_GET['start_date']));
				$Qorders->andWhere('o.date_purchased >= ?', $datetime);
			}

			if (isset($_GET['end_date']) && !empty($_GET['end_date'])){
				$datetime = date('Y-m-d h:i:s', strtotime($_GET['end_date']));
				$Qorders->andWhere('o.date_purchased < ?', $datetime);
			}

			$tableGrid = htmlBase::newElement('newGrid')
					->usePagination(true)
					->setPageLimit((isset($_GET['limit']) ? (int) $_GET['limit'] : 25))
					->setCurrentPage((isset($_GET['page']) ? (int) $_GET['page'] : 1))
					->setQuery($Qorders);

			$gridHeaderColumns = array(
				array('text' => '&nbsp;'),
				array('text' => 'ID'),
				array('text' => sysLanguage::get('TABLE_HEADING_CUSTOMERS')),
				array('text' => sysLanguage::get('TABLE_HEADING_ORDER_TOTAL')),
				array('text' => sysLanguage::get('TABLE_HEADING_DATE_PURCHASED')),
				array('text' => sysLanguage::get('TABLE_HEADING_STATUS'))
			);

			EventManager::notify('OrdersListingAddGridHeader', &$gridHeaderColumns);

			$gridHeaderColumns[] = array('text' => sysLanguage::get('TABLE_HEADING_ACTION'));

			$tableGrid->addHeaderRow(array(
				'columns' => $gridHeaderColumns
			));

			$orders = &$tableGrid->getResults();
			$noOrders = false;
			if ($orders){
				foreach($orders as $order){
					$orderId = $order['orders_id'];

					if ((!isset($_GET['oID']) || (isset($_GET['oID']) && ($_GET['oID'] == $orderId))) && !isset($oInfo)){
						$oInfo = new objectInfo($order);
					}

					$arrowIcon = htmlBase::newElement('icon')
							->setHref(itw_app_link(tep_get_all_get_params(array('action', 'oID')) . 'oID=' . $orderId));

					$onClickLink = itw_app_link(tep_get_all_get_params(array('action', 'oID')) . 'oID=' . $orderId);
					if (isset($oInfo) && $orderId == $oInfo->orders_id){
						$addCls = 'ui-state-default';
						$onClickLink = itw_app_link('oID=' . $orderId, 'orders', 'details');
						$arrowIcon->setType('circleTriangleEast');
					}else{
						$addCls = '';
						$arrowIcon->setType('info');
					}

					$htmlCheckbox = htmlBase::newElement('checkbox')
							->setName('selectedOrder[]')
							->addClass('selectedOrder')
							->setValue($orderId);

					$gridBodyColumns = array(
						array('text' => $htmlCheckbox->draw(), 'align' => 'center', 'click' => ''),
						array('text' => $orderId, 'click' => 'document.location=\'' . $onClickLink . '\''),
						array('text' => $order['OrdersAddresses']['customer']['entry_name'], 'click' => 'document.location=\'' . $onClickLink . '\''),
						array('text' => strip_tags($order['order_total']), 'align' => 'right', 'click' => 'document.location=\'' . $onClickLink . '\''),
						array('text' => tep_datetime_short($order['date_purchased']), 'align' => 'center', 'click' => 'document.location=\'' . $onClickLink . '\''),
						array('text' => $order['OrdersStatus']['OrdersStatusDescription'][Session::get('languages_id')]['orders_status_name'], 'align' => 'center', 'click' => 'document.location=\'' . $onClickLink . '\'')
					);

					EventManager::notify('OrdersListingAddGridBody', &$order, &$gridBodyColumns, $onClickLink);

					$gridBodyColumns[] = array('text' => $arrowIcon->draw(), 'align' => 'right');

					$tableGrid->addBodyRow(array(
						'addCls' => $addCls,
						'columns' => $gridBodyColumns
					));
				}
			}else{
				$noOrders = true;
			}

			$infoBox = htmlBase::newElement('infobox');
			$infoBox->setButtonBarLocation('top');

			switch($action){
				case 'delete':
					$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_DELETE_ORDER') . '</b>');
					$infoBox->setForm(array(
						'name' => 'orders',
						'action' => itw_app_link(tep_get_all_get_params(array('action', 'oID')) . 'action=deleteConfirm&oID=' . $oInfo->orders_id)
					));

					$deleteButton = htmlBase::newElement('button')->setType('submit')->setName('delete')->usePreset('delete')->setText('Delete Order without Reservation and No Restock');
					$deleteButtonReservationRestock = htmlBase::newElement('button')->setType('submit')->setName('deleteReservationRestock')->usePreset('delete')->setText('Delete Order with Reservations and Restock of Reservation');
					$deleteReservationRestockAll = htmlBase::newElement('button')->setType('submit')->setName('deleteReservationRestockAll')->usePreset('delete')->setText('Delete Order with Restock All');
					$deleteButtonNoReservationRestock = htmlBase::newElement('button')->setType('submit')->setName('deleteRestockNoReservation')->usePreset('delete')->setText('Delete Order without Reservations and Restock');
					$cancelButton = htmlBase::newElement('button')->setType('submit')->usePreset('cancel')
							->setHref(itw_app_link(tep_get_all_get_params(array('action', 'oID')) . 'oID=' . $oInfo->orders_id));

					$infoBox->addButton($deleteButton)->addButton($deleteButtonReservationRestock)->addButton($deleteButtonNoReservationRestock)->addButton($deleteReservationRestockAll)->addButton($cancelButton);

					$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_DELETE_INTRO'));
					$infoBox->addContentRow('<b>' . $oInfo->OrdersAddresses['customer']['entry_name'] . '</b>');
					//$infoBox->addContentRow(tep_draw_checkbox_field('restock') . ' ' . sysLanguage::get('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY'));
					break;
				default:
					if (isset($oInfo)){
						$infoBox->setHeader('<b>[' . $oInfo->orders_id . ']&nbsp;&nbsp;' . tep_datetime_short($oInfo->date_purchased) . '</b>');

						$detailsButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_DETAILS'))
								->setHref(itw_app_link(tep_get_all_get_params(array('action', 'oID')) . 'oID=' . $oInfo->orders_id, null, 'details'));

						$deleteButton = htmlBase::newElement('button')->usePreset('delete')
								->setHref(itw_app_link(tep_get_all_get_params(array('action', 'oID')) . 'action=delete&oID=' . $oInfo->orders_id));

						$invoiceButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_INVOICE'))
								->setHref(itw_app_link('oID=' . $oInfo->orders_id, 'orders', 'invoice'));

						$packingSlipButton = htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_PACKINGSLIP'))
								->setHref(itw_app_link('oID=' . $oInfo->orders_id, 'orders', 'packingslip'));

						$infoBox->addButton($detailsButton)->addButton($deleteButton)
							->addButton($invoiceButton)->addButton($packingSlipButton);

						EventManager::notify('AdminOrderDefaultInfoBoxAddButton', $oInfo, $infoBox);

						$infoBox->addContentRow(sysLanguage::get('TEXT_DATE_ORDER_CREATED') . ' ' . tep_date_short($oInfo->date_purchased));
						if (tep_not_null($oInfo->last_modified)){
							$infoBox->addContentRow(sysLanguage::get('TEXT_DATE_ORDER_LAST_MODIFIED') . ' ' . tep_date_short($oInfo->last_modified));
						}
						$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_PAYMENT_METHOD') . ' ' . $oInfo->payment_module);
					}elseif ($noOrders === true){
						$infoBox->addContentRow('There are currently no orders to display');
					}
					break;
			}

			$searchForm = htmlBase::newElement('form')
					->attr('name', 'search')
					->attr('id', 'searchFormOrders')
					->attr('action', itw_app_link(null, 'orders', 'default', 'SSL'))
					->attr('method', 'get');

			$startdateField = htmlBase::newElement('input')
					->setName('start_date')
					->setLabel('Start Date: ')
					->setLabelPosition('before')
					->setId('start_date');

			if (isset($_GET['start_date']) && !empty($_GET['start_date'])){
				$startdateField->val($_GET['start_date']);
			}

			$enddateField = htmlBase::newElement('input')
					->setName('end_date')
					->setLabel('End Date: ')
					->setLabelPosition('before')
					->setId('end_date');

			if (isset($_GET['end_date']) && !empty($_GET['end_date'])){
				$enddateField->val($_GET['end_date']);
			}

			$htmlSelectAll = htmlBase::newElement('checkbox')
					->setName('select_all')
					->setId('selectAllOrders')
					->setLabel('Select All')
					->setLabelPosition('after');

			$limitField = htmlBase::newElement('selectbox')
					->setName('limit')
					->setLabel('Orders per Page: ')
					->setLabelPosition('before');

			$limitField->addOption('25', '25');
			$limitField->addOption('100', '100');
			$limitField->addOption('250', '250');

			if (isset($_GET['limit']) && !empty($_GET['limit'])){
				$limitField->selectOptionByValue($_GET['limit']);
			}

			$submitButton = htmlBase::newElement('button')
					->setType('submit')
					->usePreset('save')
					->setText('Search');

			$searchForm->append($limitField)
				->append($startdateField)
				->append($enddateField);

			EventManager::notify('AdminOrdersListingSearchForm', $searchForm);
			$searchForm->append($submitButton);

			$csvButton = htmlBase::newElement('button')
					->setType('submit')
					->usePreset('save')
					->setText('Save CSV');

			return '<div class="pageHeading">' . sysLanguage::get('HEADING_TITLE') . '</div>
			<br />
			<div style="width:100%">
				' . $searchForm->draw() . '
			</div>
			<form action="' . itw_app_link('action=exportOrders', 'orders', 'default') . '" method="post">
				<div style="width:75%;float:left;">
					<div style="margin-left:30px;display:block;">' . $htmlSelectAll->draw() . '</div>
					<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
						<div style="width:99%;margin:5px;">' . $tableGrid->draw() . '</div>
					</div>' .
					$csvButton->draw() .
					EventManager::notify('AdminOrdersAfterTableDraw') .
				'</div>
			</form>
			<div style="width:25%;float:right;">' . $infoBox->draw() . '</div>';
		}
	}
	$PageContent = new Listing();
	echo $PageContent->output();
?>