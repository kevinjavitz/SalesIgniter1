<?php
	$QwhosOnline = Doctrine_Query::create()
	->select('customer_id, full_name, ip_address, time_entry, time_last_click, last_page_url, http_referer, user_agent, session_id')
	->from('WhosOnline')
	->orderBy('time_last_click DESC');

	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(false)
	->setPageLimit((isset($_GET['limit']) ? (int)$_GET['limit']: 25))
	->setCurrentPage((isset($_GET['page']) ? (int)$_GET['page'] : 1))
	->setQuery($QwhosOnline);
	
	$tableGrid->addButtons(array(
		htmlBase::newElement('icon')->addClass('refreshButton')->setType('refresh')
	));

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_ONLINE'), 'colspan' => 2, 'align' => 'center'),
			array('text' => sysLanguage::get('TABLE_HEADING_FULL_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_IP_ADDRESS')),
			array('text' => sysLanguage::get('TABLE_HEADING_ENTRY_TIME')),
			array('text' => sysLanguage::get('TABLE_HEADING_LAST_CLICK')),
			array('text' => sysLanguage::get('TABLE_HEADING_LAST_PAGE_URL')),
			array('text' => sysLanguage::get('TABLE_HEADING_USER_SESSION'), 'align' => 'center'),
			array('text' => sysLanguage::get('TABLE_HEADING_HTTP_REFERER'), 'align' => 'center'),
			array('text' => sysLanguage::get('TABLE_HEADING_INFO'))
		)
	));
	
	$total_bots = 0;
	$total_admin = 0;
	$total_guests = 0;
	$total_loggedon = 0;
	$total_sess = 0;
	$total_dupes = 0;
	$total_cust = 0;
	
	$WhosOnline = &$tableGrid->getResults();
	if ($WhosOnline){
		$old_array = array(
			'ip_address' => null
		);
		foreach($WhosOnline as $oInfo){
			$customerId = $oInfo['customer_id'];
			$fullName = $oInfo['full_name'];
			$ipAddress = $oInfo['ip_address'];
			$timeEntry = $oInfo['time_entry'];
			$timeLastClick = $oInfo['time_last_click'];
			$lastPageUrl = $oInfo['last_page_url'];
			$httpReferer = $oInfo['http_referer'];
			$userAgent = $oInfo['user_agent'];
			$sessionId = $oInfo['session_id'];
			
			$time_online = ($timeLastClick - $timeEntry);
			$total_sess++;
			
			if ($old_array['ip_address'] == $ipAddress) {
				$total_dupes++;
			}
			
			// Display Status
			//   Check who it is and set values
			$is_bot = $is_admin = $is_guest = $is_account = false;
			
			// Bot detection
			if ($customerId < 0){
				$total_bots++;
				$is_bot = true;
				// Admin detection
			}elseif ($ipAddress == tep_get_ip_address()){
				$total_admin++;
				$is_admin = true;
				// Guest detection (may include Bots not detected by Prevent Spider Sessions/spiders.txt)
			}elseif ($customerId == 0){
				$is_guest = true;
				$total_guests++;
				// Everyone else (should only be account holders)
			}else{
				$is_account = true;
				$total_loggedon++;
			}
			
			// WOL 1.6 Restructured to Check for Guest or Admin
			if ($is_bot){
				// Tokenize UserAgent and try to find Bots name
				$tok = strtok($fullName, " ();/");
				while($tok){
					if (strlen($tok) > 3){
						if (
							!strstr($tok, "mozilla") && 
							!strstr($tok, "compatible") && 
							!strstr($tok, "msie") && 
							!strstr($tok, "windows")
						){
							$fullName = $tok;
							break;
						}
					}
					$tok = strtok(" ();/");
				}
				// Check for Account
			}elseif ($is_account){
				$fullName = '<a HREF="customers.php?selected_box=customers&cID=' . $customerId . '&action=edit">' . $fullName . '</a>';
			}else{
				$fullName = sysLanguage::get('TEXT_ERROR');
			}
			
			if ($is_admin){
				$ipAddress = sysLanguage::get('TEXT_ADMIN');
			}else{
				// Show IP with link to IP checker
				$ipAddress = '<a HREF="http://www.showmyip.com/?ip=' . $ipAddress . '" target="_blank">' . $ipAddress . '</a>';
			}
			
			$temp_url_link = $lastPageUrl;
			if (preg_match('/(.*)' . Session::getSessionName() . '=[a-f,0-9]+[&]*(.*)/', $lastPageUrl, $array)){
				$temp_url_display =  $array[1] . $array[2];
			}else{
				$temp_url_display = $lastPageUrl;
			}
			
			// WOL 1.6 - Removes osCid from the Last Click URL and the link
			if ($osCsid_position = strpos($temp_url_display, "osCID")){
				$temp_url_display = substr_replace($temp_url_display, "", $osCsid_position - 1 );
			}
			
			if ($osCsid_position = strpos($temp_url_link, "osCID")){
				$temp_url_link = substr_replace($temp_url_link, "", $osCsid_position - 1 );
			}
			
			// alteration for last url product name  eof
			if (strpos($temp_url_link,'product_info.php')) {
				$temp=str_replace('product_info.php','',$temp_url_link);
				$temp=str_replace('/?','',$temp);
				$temp=str_replace('?','',$temp);
				$parameters=explode("&",$temp);
				
				$i=0;
				while($i < count($parameters)){
					$a=explode("=",$parameters[$i]);
					if ($a[0]="products_id") { $products_id=$a[1]; }
					$i++;
				}
				
				$product_query=tep_db_query("select products_name from products_description where products_id='" . $products_id . "' and language_id = '" . Session::get('languages_id') . "'");
				$product = tep_db_fetch_array($product_query);
				
				$display_link = $product['products_name'].' <I>(Product)</I>';
			}elseif (strpos($temp_url_link,'?cPath=')){
				$temp=str_replace('index.php?','',$temp_url_link);
				$temp=str_replace('?','',$temp);
				$temp=str_replace('/','',$temp);
				$parameters=explode("&",$temp);
				
				$i=0;
				$cat ='';
				while($i < count($parameters)){
					$a=explode("=",$parameters[$i]);
					if ($a[0]=="cPath") { $cat=$a[1]; }
					$i++;
				}
				
				$parameters=explode("_",$cat);
				
				$i=0;
				$cat_list = '';
				while($i < count($parameters)){
					$category_query=tep_db_query("select categories_name from categories_description where categories_id='" . $parameters[$i] . "' and language_id = '" . Session::get('languages_id') . "'");
					$category = tep_db_fetch_array($category_query);
					if ($i > 0){
						$cat_list.=' / '.$category['categories_name'];
					}else{
						$cat_list=$category['categories_name'];
					}
					$i++;
				}
				$display_link = $cat_list.' <I>(Category)</I>';
			}else{
				$display_link = $temp_url_display;
			}
			
			// alteration for last url product name  eof
			$lastPageUrl = '<a HREF="' . (($request_type == 'SSL') ? sysConfig::get('HTTPS_SERVER') : sysConfig::get('HTTP_SERVER')) . $temp_url_link . '" target="_blank">' . $display_link . '</a>';
			

			if ($sessionId != $ipAddress){
				$sessionStatus = sysLanguage::get('TEXT_IN_SESSION');
			}else{
				$sessionStatus = sysLanguage::get('TEXT_NO_SESSION');
			}

			if ($httpReferer == ""){
				$refererStatus = sysLanguage::get('TEXT_HTTP_REFERER_NOT_FOUND');
			}else{
				$refererStatus = sysLanguage::get('TEXT_HTTP_REFERER_FOUND');
			}
			
			$cartInfo = tep_check_cart($sessionId, $timeLastClick, $customerId);
			
			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-sess_id' => $oInfo['session_id']
				),
				'columns' => array(
					array('text' => $cartInfo['icon']),
					array('text' => gmdate('H:i:s', $time_online)),
					array('text' => $fullName),
					array('text' => $ipAddress),
					array('text' => date('H:i:s', $timeEntry)),
					array('text' => date('H:i:s', $timeLastClick)),
					array('text' => $lastPageUrl),
					array('text' => $sessionStatus),
					array('text' => $refererStatus),
					array('align' => 'center', 'text' => htmlBase::newElement('icon')->setType('info')->draw())
				)
			));
			
			$tableGrid->addBodyRow(array(
				'addCls' => 'gridInfoRow',
				'columns' => array(
					array(
						'colspan' => 10,
						'text' => '<table cellpadding="1" cellspacing="0" border="0" width="90%">' . 
							'<tr>' . 
								'<td><b>' . sysLanguage::get('TEXT_INFO_FULL_NAME') . '</b></td>' . 
								'<td>' . $oInfo['full_name'] . '</td>' . 
							'</tr>' . 
							'<tr>' . 
								'<td><b>' . sysLanguage::get('TEXT_INFO_CUSTOMER_ID') . '</b></td>' . 
								'<td>' . ($oInfo['customer_id'] > 0 ? $oInfo['customer_id'] : 'N/A') . '</td>' . 
							'</tr>' . 
							'<tr>' . 
								'<td><b>' . sysLanguage::get('TEXT_INFO_IP_ADDRESS') . '</b></td>' . 
								'<td>' . $oInfo['ip_address'] . '</td>' . 
							'</tr>' . 
							'<tr>' . 
								'<td><b>' . sysLanguage::get('TEXT_INFO_USER_AGENT') . '</b></td>' . 
								'<td>' . $oInfo['user_agent'] . '</td>' . 
							'</tr>' . 
							'<tr>' . 
								'<td><b>' . sysLanguage::get('TEXT_INFO_SESSION_ID') . '</b></td>' . 
								'<td>' . ($oInfo['session_id'] != $oInfo['ip_address'] ? $oInfo['session_id'] : 'N/A') . '</td>' . 
							'</tr>' . 
							'<tr>' . 
								'<td><b>' . sysLanguage::get('TEXT_INFO_HTTP_REFERER') . '</b></td>' . 
								'<td>' . (!empty($oInfo['http_referer']) ? $oInfo['http_referer'] : 'N/A') . '</td>' . 
							'</tr>' . 
							(is_null($cartInfo['products']) === false ? '<tr>' . 
								'<td valign="top"><b>' . sysLanguage::get('TEXT_INFO_CART_PRODUCTS') . '</b></td>' . 
								'<td>' . $cartInfo['products'] . '</td>' . 
							'</tr>' : '') . 
						'</table>'
					)
				)
			));
			
			$old_array = $oInfo;
		}
		
		// Subtract Bots and Me from Real Customers.  Only subtract me once as Dupes will remove others
		$total_cust = $total_sess - $total_dupes - $total_bots - ($total_admin > 1 ? 1 : $total_admin);
		
		/*
		$tableGrid->addBodyRow(array(
			'columns' => array(
				array('colspan' => 9, 'text' => '<b>' . sysLanguage::get('TEXT_HTTP_REFERER_URL') . '</b> <a href=' . $http_referer_url . ' target=_blank>' . $http_referer_url . '</a>')
			)
		));
		*/
	}
	
	EventManager::attachActionResponse(array(
		'grid'       => $tableGrid->draw(),
		'sessions'   => (int) $total_sess,
		'duplicates' => (int) $total_dupes,
		'bots'       => (int) $total_bots,
		'admin'      => (int) $total_admin,
		'customers'  => (int) $total_cust
	), 'json');
?>