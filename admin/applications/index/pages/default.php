<?php
//latest customers table
//latest orders
//sales statistics
//my favorite links
//every admin account should have a list of links separated by comma,
//add new field to admin
//change header to home(index), my accounbt(admin_account/default, logoff, and right add this page to my favorites... which will do an ajax action to add the page... so the function must be in general.js.

/*Latest Orders*/

	$Qorders = Doctrine_Query::create()
	->select('o.orders_id, a.entry_name, o.date_purchased, o.customers_id, o.last_modified, o.currency, o.currency_value, s.orders_status_id, sd.orders_status_name, ot.text as order_total, o.payment_module')
	->from('Orders o')
	->leftJoin('o.OrdersTotal ot')
	->leftJoin('o.OrdersAddresses a')
	->leftJoin('o.OrdersStatus s')
	->leftJoin('s.OrdersStatusDescription sd')
	->where('sd.language_id = ?', Session::get('languages_id'))
	/*
	 * @TODO: Change to only look for "total" after a while, when client upgrades will no longer be affected
	 */
	->andWhereIn('ot.module_type', array('total', 'ot_total'))
	->andWhere('a.address_type = ?', 'customer')
	->orderBy('o.date_purchased desc')
	->limit(10);

	EventManager::notify('AdminOrdersListingBeforeExecute', $Qorders);

	$tableGridOrders = htmlBase::newElement('grid')
	->usePagination(false)
	->setPageLimit(10)
	->setQuery($Qorders);

	$gridHeaderColumns = array(
		array('text' => 'ID'),
		array('text' => sysLanguage::get('TABLE_HEADING_CUSTOMERS')),
		array('text' => sysLanguage::get('TABLE_HEADING_ORDER_TOTAL')),
		array('text' => sysLanguage::get('TABLE_HEADING_DATE_PURCHASED')),
		array('text' => sysLanguage::get('TABLE_HEADING_STATUS'))
	);

	$gridHeaderColumns[] = array('text' => sysLanguage::get('TABLE_HEADING_ACTION'));

	$tableGridOrders->addHeaderRow(array(
		'columns' => $gridHeaderColumns
	));

	$orders = &$tableGridOrders->getResults();
	$noOrders = false;
	if ($orders){
		foreach($orders as $order){
			$orderId = $order['orders_id'];
		    $onClickLink = '<a href="' . itw_app_link('oID=' . $orderId, 'orders', 'details') . '">View Order</a>';
			$gridBodyColumns = array(
				array('text' => $orderId),
				array('text' => $order['OrdersAddresses']['customer']['entry_name']),
				array('text' => strip_tags($order['order_total']), 'align' => 'right'),
				array('text' => tep_datetime_short($order['date_purchased']), 'align' => 'center'),
				array('text' => $order['OrdersStatus']['OrdersStatusDescription'][Session::get('languages_id')]['orders_status_name'], 'align' => 'center'),
				array('text' => $onClickLink)
			);
			$tableGridOrders->addBodyRow(array(
				'columns' => $gridBodyColumns
			));
		}
	}else{
		$noOrders = true;
	}


/*End Latest Orders*/

/*Latest Customers*/
	$noCustomers = false;
     $Qcustomers = Doctrine_Query::create()
	->from('Customers c')
	->leftJoin('c.CustomersMembership cm')
	->leftJoin('c.MembershipBillingReport mu ON (mu.customers_id = c.customers_id AND mu.date = "' . sysConfig::get('LAST_CRON_DATE') . '")')
	->leftJoin('c.CustomersInfo i')
	->leftJoin('c.AddressBook a on (c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id)')
	->leftJoin('a.Countries co')
	->orderBy('i.customers_info_date_account_created desc, c.customers_lastname, c.customers_firstname')
	->limit(10);


	$tableGridCustomers = htmlBase::newElement('newGrid')
	->usePagination(false)
	->setPageLimit(10)
	->setQuery($Qcustomers);

	$tableGridCustomers->addButtons(array(
		htmlBase::newElement('button')->setText('Edit')->addClass('editButton')->disable(),
		htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton')->disable(),
		htmlBase::newElement('button')->setText('Orders')->addClass('ordersButton')->disable(),
		htmlBase::newElement('button')->setText('Email')->addClass('emailButton')->disable()
	));

	$tableGridCustomers->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_LASTNAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_FIRSTNAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_MEMBER_OR_USER')),
			array('text' => sysLanguage::get('TABLE_HEADING_MEMBERSHIP_STATUS')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACCOUNT_CREATED')),
			array('text' => 'info')
		)
	));

	$customers = &$tableGridCustomers->getResults();
	if ($customers){
		foreach($customers as $customer){
			$customerId = $customer['customers_id'];
			$cInfo = new objectInfo($customer);
			$arrowIcon = htmlBase::newElement('icon')->setType('info');
			$Qorders = Doctrine_Query::create()
			->select('count(*) as total')
			->from('Orders')
			->where('customers_id = ?', $customerId)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if (empty($customer['MembershipBillingReport'])){
			}elseif ($customer['MembershipBillingReport']['status'] == 'A'){
				$addCls .= ' dataTableRowA';
			}elseif ($customer['MembershipBillingReport']['status'] == 'D'){
				$addCls .= ' dataTableRowD';
			}

			if (!isset($customer['CustomersMembership']) || $customer['CustomersMembership']['ismember'] == 'U'){
				$member = 'User';
			}elseif ($customer['CustomersMembership']['ismember'] == 'M'){
				$member = 'Member';
			}else{
				$member = 'Unknown';
			}

			if (!isset($customer['CustomersMembership'])){
				$activate = '';
			}elseif ($customer['CustomersMembership']['activate'] == 'Y'){
				$activate = 'Active';
			}elseif ($customer['CustomersMembership']['activate'] == 'N'){
				$activate = 'InActive';
			}

			if (Session::exists('customer_login_allowed') && Session::get('customer_login_allowed') === true){
					$loginAsCustomerLink = '<a href="' . itw_app_link('action=loginAs&cID=' . $customerId,'customers','default') . '"><b>'.sysLanguage::get('LOGIN_AS_CUSTOMER').'</b></a>';
			}

			$tableGridCustomers->addBodyRow(array(
				'rowAttr' => array(
					'data-customer_id'    => $customerId,
					'data-customer_email' => $customer['customers_email_address'],
					'data-has_orders'     => ($Qorders[0]['total'] > 0 ? 'true' : 'false')
				),
				'columns' => array(
					array('text' => $customer['customers_lastname']),
					array('text' => $customer['customers_firstname']),
					array('text' => $member, 'align' => 'center'),
					array('text' => $activate, 'align' => 'center'),
					array('text' => tep_date_short($customer['CustomersInfo']['customers_info_date_account_created']), 'align' => 'center'),
					array('text' => $arrowIcon->draw(), 'align' => 'center')
				)
			));
			$tableGridCustomers->addBodyRow(array(
				'addCls' => 'gridInfoRow',
				'columns' => array(
					array('colspan' => 6, 'text' => '<table cellpadding="1" cellspacing="0" border="0" width="100%">' .
						'<tr>' .
							'<td><b>' . sysLanguage::get('TEXT_DATE_ACCOUNT_CREATED') . '</b></td>' .
							'<td> ' . tep_date_short($cInfo->CustomersInfo['customers_info_date_account_created']) . '</td>' .
							'<td><b>' . sysLanguage::get('TEXT_DATE_ACCOUNT_LAST_MODIFIED') . '</b></td>' .
							'<td>' . tep_date_short($cInfo->CustomersInfo['customers_info_date_account_last_modified']) . '</td>' .
							'<td></td>' .
						'</tr>' .
						'<tr>' .
							'<td><b>' . sysLanguage::get('TEXT_INFO_DATE_LAST_LOGON') . '</b></td>' .
							'<td>'  . tep_date_short($cInfo->CustomersInfo['customers_info_date_of_last_logon']) . '</td>' .
							'<td><b>' . sysLanguage::get('TEXT_INFO_NUMBER_OF_LOGONS') . '</b></td>' .
							'<td>' . $cInfo->CustomersInfo['customers_info_number_of_logons'] . '</td>' .
							'<td></td>' .
						'</tr>' .
						'<tr>' .
							'<td><b>' . sysLanguage::get('TEXT_INFO_COUNTRY') . '</b></td>' .
							'<td>' . $cInfo->AddressBook[0]['Countries']['countries_name'] . '</td>' .
							'<td><b></b></td>' .
							'<td></td>' .
							'<td>' . $loginAsCustomerLink . '</td>' .
						'</tr>' .
					'</table>')
				)
			));
		}
	}else{
		$noCustomers = true;
	}
/*End Latest Customers*/

/*Start Monthly Sales*/


	// detect whether this is monthly detail request
	$sel_month = 0;
	// get list of orders_status names for dropdown selection
	$orders_statuses = array();
	$orders_status_array = array();
	$QordersStatus = Doctrine_Query::create()
	->select('orders_status_id, sd.orders_status_name')
	->from('OrdersStatus o')
	->leftJoin('o.OrdersStatusDescription sd')
	->where('sd.language_id = ?', Session::get('languages_id'))
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	foreach($QordersStatus as $osInfo){
		$orders_statuses[] = array(
			'id'   => $osInfo['orders_status_id'],
			'text' => $osInfo['OrdersStatusDescription'][0]['orders_status_name']
		);
		$orders_status_array[$osInfo['orders_status_id']] = $osInfo['OrdersStatusDescription'][0]['orders_status_name'];
	}

	// name of status selection
	$orders_status_text = sysLanguage::get('TEXT_ALL_ORDERS');
	// determine if loworder fee is enabled in configuration, include/omit the column
	$QlowOrder = sysConfig::get('MODULE_ORDER_TOTAL_LOWORDERFEE_LOW_ORDER_FEE');
	$loworder = false;
	if ($QlowOrder != '') {
		if ($QlowOrder == 'true') $loworder = true;
	}

	//
	// if there are extended class values in orders_table
	// create extra column so totals are comprehensively correct
	$classValueArr = array(
		'ot_subtotal',
		'ot_tax',
		'ot_shipping',
		'total',
		'tax',
		'shipping',
		'ot_loworderfee',
		'ot_total',
		'total',
		'shipping',
		'tax',
		'subtotal',
		'loworderfee'
	);
	$QclassCheck = Doctrine_Query::create()
	->select('value')
	->from('OrdersTotal')
	->whereNotIn('module_type', $classValueArr)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$extra_class = false;
	if ($QclassCheck){
		$extra_class = true;
	}

	$reportTable = htmlBase::newElement('table')
	->setCellPadding(2)
	->setCellSpacing(0)
	->css(array(
		'width' => '100%'
	))
	->addClass('ui-widget ui-widget-content');

	$col2Text = ($sel_month == 0 ? TABLE_HEADING_YEAR : sysLanguage::get('TABLE_HEADING_DAY'));
	$headerCols = array(
		array('align' => 'left', 'valign' => 'bottom', 'width' => '45', 'text' => (sysLanguage::get('TABLE_HEADING_MONTH'))),
		array('align' => 'left', 'valign' => 'bottom', 'width' => '35', 'text' => ($col2Text)),
		array('align' => 'right', 'valign' => 'bottom', 'width' => '70', 'text' => (sysLanguage::get('TABLE_HEADING_INCOME'))),
		array('align' => 'right', 'valign' => 'bottom', 'width' => '70', 'text' => (sysLanguage::get('TABLE_HEADING_SALES'))),
		array('align' => 'right', 'valign' => 'bottom', 'width' => '70', 'text' => (sysLanguage::get('TABLE_HEADING_TAXED'))),
		array('align' => 'right', 'valign' => 'bottom', 'width' => '70', 'text' => (sysLanguage::get('TABLE_HEADING_SHIPHNDL')))
	);

	if ($loworder){
		$headerCols[] = array('align' => 'right', 'valign' => 'bottom', 'width' => '70', 'text' => (sysLanguage::get('TABLE_HEADING_LOWORDER')));
	}

	if ($extra_class){
		$headerCols[] = array('align' => 'right', 'valign' => 'bottom', 'width' => '70', 'text' => (sysLanguage::get('TABLE_HEADING_OTHER')));
	}

	$reportTable->addHeaderRow(array(
		'addCls' => 'ui-widget-header',
		'columns' => $headerCols
	));

	// clear footer totals
	$footer_gross = 0;
	$footer_sales = 0;
	$footer_sales_nontaxed = 0;
	$footer_sales_taxed = 0;
	$footer_tax_coll = 0;
	$footer_shiphndl = 0;
	$footer_shipping_tax = 0;
	$footer_loworder = 0;
	$footer_other = 0;

	// order totals, the driving force
	$status = '';

	$Qsales = Doctrine_Query::create()
	->select('
		o.orders_id,
		SUM(ot.value) as gross_sales,
		MONTHNAME(o.date_purchased) as row_month,
		YEAR(o.date_purchased) as row_year,
		MONTH(o.date_purchased) as i_month,
		DAYOFMONTH(o.date_purchased) as row_day,
	')
	->from('Orders o')
	->leftJoin('o.OrdersTotal ot')
	->andWhereIn('ot.module_type', array('total', 'ot_total'))
	->groupBy('YEAR(o.date_purchased) , MONTH(o.date_purchased)')
	->orderBy('o.date_purchased desc');

	$Result = $Qsales->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Result){
		$rows = 0;
		$lastRowNum = sizeof($Result);
		foreach($Result as $sInfo){
			$rows++;
			if ($rows > 1 && $sInfo['row_year'] <> $last_row_year){
				$col1Text = '';
				if ($sInfo['row_year'] == date('Y')){
					$col1Text = sysLanguage::get('TABLE_FOOTER_YTD');
				}elseif ($sel_month==0){
					$col1Text = sysLanguage::get('TABLE_FOOTER_YEAR');
				}else{
					$col1Text = strtoupper(substr($sInfo['row_month'],0,3));
				}

				$cssLeft = array(
					'text-align' => 'left',
					'font-weight' => 'bold'
				);
				$cssRight = array(
					'text-align' => 'right',
					'font-weight' => 'bold'
				);

				$bodyRowCols = array(
					array('css' => $cssLeft, 'text' => ($col1Text)),
					array('css' => $cssLeft, 'text' => ($last_row_year)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => ($footer_gross)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => ($footer_sales)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => ($footer_sales_taxed)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => ($footer_shiphndl))
				);

				if ($loworder){
					$bodyRowCols[] = array('css' => $cssRight, 'format' => 'currency', 'text' => ($footer_loworder));
				}

				if ($extra_class){
					$bodyRowCols[] = array('css' => $cssRight, 'format' => 'currency', 'text' => ($footer_other));
				}

				$reportTable->addBodyRow(array(
					'addCls' => 'ui-state-hover',
					'columns' => $bodyRowCols
				));

				// clear footer totals
				$footer_gross = 0;
				$footer_sales = 0;
				$footer_sales_nontaxed = 0;
				$footer_sales_taxed = 0;
				$footer_tax_coll = 0;
				$footer_shiphndl = 0;
				$footer_shipping_tax = 0;
				$footer_loworder = 0;
				$footer_other = 0;
			}

			$Queries = array();
			$Queries['netNoTax'] = Doctrine_Query::create()
			->select('SUM(op.final_price) as total')
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->where('op.products_tax = ?', '0');

			$Queries['netTax'] = Doctrine_Query::create()
			->select('SUM(op.final_price) as total')
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->where('op.products_tax > ?', '0');

			$Queries['grossSales'] = Doctrine_Query::create()
			->select('SUM(op.final_price * (1 + (op.products_tax / 100.0))) as total')
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->where('op.products_tax > ?', '0');

			$Queries['salesTax'] = Doctrine_Query::create()
			->select('SUM((op.final_price * (1 + (op.products_tax / 100.0))) - (op.final_price)) as total')
			->from('Orders o')
			->leftJoin('o.OrdersProducts op')
			->where('op.products_tax > ?', '0');

			$Queries['taxCollected'] = Doctrine_Query::create()
			->select('SUM(ot.value) as total')
			->from('Orders o')
			->leftJoin('o.OrdersTotal ot')
			->whereIn('ot.module_type = ?', array('ot_tax','tax'));

			$Queries['shippingCollected'] = Doctrine_Query::create()
			->select('SUM(ot.value) as total')
			->from('Orders o')
			->leftJoin('o.OrdersTotal ot')
			->whereIn('ot.module_type', array('ot_shipping','shipping'));

			if ($loworder) {
				$Queries['lowOrderFees'] = Doctrine_Query::create()
				->select('SUM(ot.value) as total')
				->from('Orders o')
				->leftJoin('o.OrdersTotal ot')
				->where('ot.module_type = ?', 'ot_loworderfee');
			}

			if ($extra_class){
				$Queries['otherOrderFees'] = Doctrine_Query::create()
				->select('SUM(ot.value) as total')
				->from('Orders o')
				->leftJoin('o.OrdersTotal ot')
				->whereNotIn('ot.module_type', $classValueArr);
			}

			foreach($Queries as $finalVarName => $queryObj){
				$queryObj->andWhere('MONTH(o.date_purchased) = ?', $sInfo['i_month'])
				->andWhere('YEAR(o.date_purchased) = ?', $sInfo['row_year']);

				if ($status <> ''){
					$queryObj->andWhere('o.orders_status = ?', $status);
				}

				if ($sel_month <> 0){
					$queryObj->andWhere('DAYOFMONTH(o.date_purchased) = ?', $sInfo['row_day']);
				}

				$$finalVarName = $queryObj->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			}

			$zero_rated_net_sales_this_row = $netNoTax[0]['total'];
			$net_sales_this_row = $netTax[0]['total'];
			$gross_sales_this_row = $grossSales[0]['total'];
			$sales_tax_this_row = $salesTax[0]['total'];
			$tax_this_row = $taxCollected[0]['total'];
			$shiphndl_this_row = $shippingCollected[0]['total'];
			if ($loworder){
				$loworder_this_row = $QlowOrderFees[0]['total'];
			}
			if ($extra_class){
				$other_this_row = $otherOrderFees[0]['total'];
			}

			// Correct any rounding errors
			$net_sales_this_row = (floor(($net_sales_this_row * 100) + 0.5)) / 100;
			$sales_tax_this_row = (floor(($sales_tax_this_row * 100) + 0.5)) / 100;
			$zero_rated_net_sales_this_row = (floor(($zero_rated_net_sales_this_row * 100) + 0.5)) / 100;
			$tax_this_row = (floor(($tax_this_row * 100) + 0.5)) / 100;

			// accumulate row results in footer
			$footer_gross += $sInfo['gross_sales']; // Gross Income
			$footer_sales += $net_sales_this_row + $zero_rated_net_sales_this_row; // Product Sales
			$footer_sales_nontaxed += $zero_rated_net_sales_this_row; // Nontaxed Sales
			$footer_sales_taxed += $net_sales_this_row; // Taxed Sales
			$footer_tax_coll += $sales_tax_this_row; // Taxes Collected
			$footer_shiphndl += $shiphndl_this_row; // Shipping & handling
			$footer_shipping_tax += ($tax_this_row - $sales_tax_this_row); // Shipping Tax
			if ($loworder) $footer_loworder += $loworder_this_row;
			if ($extra_class) $footer_other += $other_this_row;

			$col1Text = (substr($sInfo['row_month'],0,3));

			if ($sel_month == 0){
				$col1Text = htmlBase::newElement('a')
				->setHref(itw_app_link('month=' . $sInfo['i_month'] . '&year=' . $sInfo['row_year'],'statistics','monthlySales'))
				->html($col1Text)
				->draw();
			}

			if ($sel_month==0){
				$col2Text = ($sInfo['row_year']);
			}else{
				$col2Text = ($sInfo['row_day']);
			}
			$last_row_year = $sInfo['row_year']; // save this row's year to check for annual footer

			$col7Text = (number_format($sales_tax_this_row,2));
			if ($sales_tax_this_row > 0){
				$col7Text = htmlBase::newElement('a')
				->setHRef(itw_app_link(tep_get_all_get_params(array('action', 'month', 'year', 'show')) . 'show=ot_tax&month=' . $sInfo['i_month'] . ($sel_month<>0 ? '&day=' . $sInfo['row_day'] : '') . ($status <> '' ? '&status=' . $status : '')))
				->html($col7Text)
				->attr('onclick', 'window.open(this.href, \'detail\',config=\'height=200,width=400,scrollbars=1,resizable=1\')')
				->draw();
			}

			$sh_tax = $tax_this_row - $sales_tax_this_row;
			$col9Text =  ($sh_tax <= 0) ? 0 : $sh_tax;

			$bodyRowCols = array(
				array('align' => 'left', 'text' => $col1Text),
				array('align' => 'left', 'text' => $col2Text),
				array('align' => 'right', 'format' => 'currency', 'text' => ($sInfo['gross_sales'])),
				array('align' => 'right', 'format' => 'currency', 'text' => ($net_sales_this_row + $zero_rated_net_sales_this_row)),
				array('align' => 'right', 'format' => 'currency', 'text' => ($net_sales_this_row)),
				array('align' => 'right', 'format' => 'currency', 'text' => ($shiphndl_this_row))
			);

			if ($loworder){
				$bodyRowCols[] = array('align' => 'right', 'format' => 'currency', 'text' => ($loworder_this_row));
			}

			if ($extra_class){
				$bodyRowCols[] = array('align' => 'right', 'format' => 'currency', 'text' => ($other_this_row));
			}

			$reportTable->addBodyRow(array(
				'columns' => $bodyRowCols
			));

			if ($rows == $lastRowNum){
				if ($sel_month <> 0){
					$col1Text = strtoupper(substr($sInfo['row_month'],0,3));
				}elseif ($sInfo['row_year'] == date('Y')){
					$col1Text = sysLanguage::get('TABLE_FOOTER_YTD');
				}else{
					$col1Text = sysLanguage::get('TABLE_FOOTER_YEAR');
				}

				$cssLeft = array(
					'text-align' => 'left',
					'font-weight' => 'bold'
				);
				$cssRight = array(
					'text-align' => 'right',
					'font-weight' => 'bold'
				);

				$bodyRowCols = array(
					array('css' => $cssLeft, 'text' => ($col1Text)),
					array('css' => $cssLeft, 'text' => ($sInfo['row_year'])),
					array('css' => $cssRight, 'format' => 'currency', 'text' => ($footer_gross)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => ($footer_sales)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => ($footer_sales_taxed)),
					array('css' => $cssRight, 'format' => 'currency', 'text' => ($footer_shiphndl))

				);

				if ($loworder){
					$bodyRowCols[] = array('css' => $cssRight, 'format' => 'currency', 'text' => ($footer_loworder));
				}

				if ($extra_class){
					$bodyRowCols[] = array('css' => $cssRight, 'format' => 'currency', 'text' => ($footer_other));
				}

				$reportTable->addBodyRow(array(
					'addCls' => 'ui-state-hover',
					'columns' => $bodyRowCols
				));

				$footer_gross = 0;
				$footer_sales = 0;
				$footer_sales_nontaxed = 0;
				$footer_sales_taxed = 0;
				$footer_tax_coll = 0;
				$footer_shiphndl = 0;
				$footer_shipping_tax = 0;
				$footer_loworder = 0;
				$footer_other = 0;

			}
		}
	}else{
		$reportTable->addBodyRow(array(
			'columns' => array(
				array('colspan' => sizeof($headerCols), 'text' => sysLanguage::get('TEXT_NOTHING_FOUND'))
			)
		));
	}

/*End Monthly Sales*/

/*Favorites Links*/

	$Admin = Doctrine_Core::getTable('Admin')->findOneByAdminId((int)Session::get('login_id'));
	$favorites_links = explode(';', $Admin->favorites_links);
	$favorites_names = explode(';', $Admin->favorites_names);

	$favoritesTable = htmlBase::newElement('div')
	->css(array(
		'width' => '100%'
	))
	->addClass('ui-widget ui-widget-content favoritesLinks');

	/*$fheaderCols = array(
		array('align' => 'center', 'valign' => 'bottom', 'text' => 'Link Name'),
		array('align' => 'center', 'valign' => 'bottom', 'text' => 'Action'),
	);

	$favoritesTable->addHeaderRow(array(
		'addCls' => 'ui-widget-header',
		'columns' => $fheaderCols
	));*/
    $favoritesList = htmlBase::newElement('list')
					->css(array(
						'list-style' => 'none',
						'margin' => '0',
						'padding' => '0'
					))
					->addClass('favoritesLinks');
	for($i = 0;$i < sizeof($favorites_links); $i++){
		if(!empty($favorites_links[$i])){
			$myItem = '<a href="' . sysConfig::get('DIR_WS_ADMIN') . $favorites_links[$i] . '">' . $favorites_names[$i] . '</a>'. '<a href="'. itw_app_link('action=removeFromFavorites&url='. $favorites_links[$i],'index','default') .'"><span class="ui-icon ui-icon-closethick"></span></a>';
			$favoritesList->addItem('',$myItem);

			/*$favoritesTable->addBodyRow(array(
				'columns' => $fbodyRowCols
			));*/
		}
	}
 $favoritesTable->append($favoritesList);

/*End Favorites Links*/

?>
<div id="columns">

        <ul id="column1" class="column">
            <li class="widget color-white" id="favorites">
                <div class="widget-head">
                    <h3>Favorites Links</h3>
                </div>
                <div class="widget-content">
                    <p>
                    <?php
                    	echo $favoritesTable->draw();
                    ?>
                    </p>
	                <br/>
                </div>
            </li>
	        <li class="widget color-white" id="monthlySales">
                <div class="widget-head">
                    <h3>Sales Statistics</h3>
                </div>
                <div class="widget-content">
                    <p>
                    <?php
                    	echo $reportTable->draw();
                    ?>
                    </p>
	                <br/>
                </div>
            </li>
        </ul>

        <ul id="column2" class="column">
	        <li class="widget color-white" id="latestOrders">
                <div class="widget-head">
                    <h3>Latest Orders</h3>
                </div>
                <div class="widget-content">
                    <p>
	                  <?php
	                    if ($noOrders === false){
	                        echo $tableGridOrders->draw();
	                    }else{
		                  echo 'No orders';
	                  }
	                  ?>
                    </p>
	                <br/>
                </div>
            </li>
            <li class="widget color-white" id="latestCustomers">
                <div class="widget-head">
                    <h3>Latest Customers</h3>
                </div>
                <div class="widget-content">
                    <p>
                    <?php
	                    if ($noCustomers === false){
                    	    echo $tableGridCustomers->draw();
                        }else{
	                        echo 'No customers';
                        }
                    ?>

                    </p>
	                <br/>
                </div>
            </li>


        </ul>

    </div>
<?php
?>