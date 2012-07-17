<?php
	$appContent = $App->getAppContentFile();
	
	require(sysConfig::getDirFsCatalog() . 'includes/classes/currencies.php');
	$currencies = new currencies();
	if(!class_exists('Product')){
		require(sysConfig::getDirFsCatalog() . 'includes/classes/product.php');
	}
	
	require(sysConfig::getDirFsCatalog() . 'includes/classes/shopping_cart.php');
	//$ShoppingCart = new ShoppingCart;
	
/*
 * Configuration Values
 * Set these to easily personalize your Whos Online
 */
	$active_time = 300; // Seconds that a visitor is considered "active"
	$track_time = 900; // Seconds before visitor is removed from display
	
	/*
	 * Automatic refresh times in seconds and display names
	 * Time and Display Text order must match between the arrays
	 * "None" is handled separately in the code
	 */
	$refresh_time = array(     30,    60,     120,     300,    600 );
	$refresh_display = array( '0:30', '1:00', '2:00', '5:00', '10:00' );
	
	$icons = array(
		'inactiveCart'   => '<span class="ui-icon ui-icon-red ui-icon-cart"></span>',
		'inactiveNoCart' => '<span class="ui-icon ui-icon-red ui-icon-cart ui-state-disabled"></span>',
		'activeCart'     => '<span class="ui-icon ui-icon-green ui-icon-cart"></span>',
		'activeNoCart'   => '<span class="ui-icon ui-icon-green ui-icon-cart ui-state-disabled"></span>',
		'botActive'      => '<span class="ui-icon ui-icon-orange ui-icon-gear"></span>',
		'botInactive'    => '<span class="ui-icon ui-icon-orange ui-icon-gear ui-state-disabled"></span>'
	);
	
	// Time to remove old entries
	$xx_mins_ago = (time() - $track_time);
	
	// remove entries that have expired
	Doctrine_Query::create()
	->delete('WhosOnline')
	->where('time_last_click < ?', $xx_mins_ago)
	->execute();
	
	/*
	 * Borrowed from oscommerce 3.0
	 * This function will return objects that contain objects in the session string, such as the shoppingCart class
	 * currently this function is only used in this application
	 */
	function osc_get_serialized_variable(&$serialization_data, $variable_name, $variable_type = 'string'){
		$serialized_variable = '';
		
		switch ($variable_type){
			case 'string':
				$start_position = strpos($serialization_data, $variable_name . '|s');
				
				$serialized_variable = substr(
					$serialization_data,
					strpos($serialization_data, '|', $start_position) + 1,
					strpos($serialization_data, '|', $start_position) - 1
				);
				break;
			case 'array':
			case 'object':
				if ($variable_type == 'array'){
					$start_position = strpos($serialization_data, $variable_name . '|a');
				}else{
					$start_position = strpos($serialization_data, $variable_name . '|O');
				}
				
				$tag = 0;
				for($i=$start_position, $n=sizeof($serialization_data); $i<$n; $i++){
					if ($serialization_data[$i] == '{'){
						$tag++;
					}elseif ($serialization_data[$i] == '}'){
						$tag--;
					}elseif ($tag < 1){
						break;
					}
				}
				
				$serialized_variable = substr(
					$serialization_data,
					strpos($serialization_data, '|', $start_position) + 1,
					$i - strpos($serialization_data, '|', $start_position) - 1
				);
				break;
		}
		return $serialized_variable;
	}

	//Determines status and cart of visitor and displays appropriate icon.
	function tep_check_cart($session_id, $lastClick, $customer_id) {
		global $active_time, $icons, $currencies;

		// Pull Session data from the correct source.
		$Qsession = Doctrine_Query::create()
		->select('value')
		->from('Sessions')
		->where('sesskey = ?', $session_id)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$session_data = '';
		if(count($Qsession) > 0){
			$session_data = trim($Qsession[0]['value']);
		}

		$cartProducts = null;
		if ($length = strlen($session_data)){
			$ShoppingCart = unserialize(osc_get_serialized_variable($session_data, 'ShoppingCart', 'object'));
			$userAccount = unserialize(osc_get_serialized_variable($session_data, 'userAccount', 'object'));
			$currency = unserialize(osc_get_serialized_variable($session_data, 'currency', 'string'));
			
			if (isset($ShoppingCart) && is_object($ShoppingCart)){
				$ShoppingCart->initContents();
				$products = $ShoppingCart->getProducts();
				
				$cartProducts = '<table cellpadding="0" cellspacing="0" border="0">';
				if (sizeof($products) > 0){
					foreach($products as $cartProduct) {
						$cartProducts .= '<tr>' . 
							'<td class="main">' . $cartProduct->getQuantity() . ' x ' . $cartProduct->getName() . '</td>' . 
						'</tr>';
					}
					$cartProducts .= '<tr>' . 
						'<td class="main" align="right">' . sysLanguage::get('TEXT_SHOPPING_CART_SUBTOTAL') . ': ' . $currencies->format($ShoppingCart->showTotal(), true, $currency) . '</td>' . 
					'</tr>';
				}else{
					$cartProducts .= '<tr>' . 
						'<td class="main">' . sysLanguage::get('TEXT_EMPTY') . '</td>' . 
					'</tr>';
				}
				$cartProducts .= '</table>';
			}
		}

		// Determine if visitor active/inactive
		$xx_mins_ago_long = (time() - $active_time);

		// Determine Bot active/inactive
		if ($customer_id < 0){
			// inactive
			if ($lastClick < $xx_mins_ago_long){
				$cartIcon = $icons['botInactive'];
				// active
			}else{
				$cartIcon = $icons['botActive'];
			}
		}

		// Determine active/inactive and cart/no cart status
		// no cart
		if (isset($products) && sizeof($products) == 0){
			// inactive
			if ($lastClick < $xx_mins_ago_long){
				$cartIcon = $icons['inactiveNoCart'];
				// active
			}else{
				$cartIcon = $icons['activeNoCart'];
			}
			// cart
		}else{
			// inactive
			if ($lastClick < $xx_mins_ago_long){
				$cartIcon = $icons['inactiveCart'];
				// active
			}else{
				$cartIcon = $icons['activeCart'];
			}
		}
		
		return array(
			'icon' => $cartIcon,
			'products' => $cartProducts
		);
	}
?>