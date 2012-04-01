<?php
	$ShoppingCart->updateProduct($_POST['pID'], array(
					'quantity'      => $_POST['qty'],
					'purchase_type' => $_POST['type']
	));

 	ob_start();
	require(sysConfig::getDirFsCatalog() . 'applications/checkout/pages/cart.php');
	$pageHtml = ob_get_contents();
	ob_end_clean();

if ($onePageCheckout->onePage['shippingEnabled'] === true){
	if ($onePageCheckout->isNormalCheckout() === true){
		$total_weight = $ShoppingCart->showWeight();
		$total_count = $ShoppingCart->countContents();
	}else{
		$total_weight = 1;
		$total_count = 1;
	}

	OrderShippingModules::calculateWeight();
}

	$shippingTable = '';
	ob_start();
?>
<?php
	if ($onePageCheckout->onePage['shippingEnabled'] === true){
	$showStoreMethods = true;
	$contents = EventManager::notifyWithReturn('CheckoutShippingMethodsBeforeList', &$showStoreMethods);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}

	if ($showStoreMethods === true && OrderShippingModules::hasModules() === true && !$onePageCheckout->isMembershipCheckout()){
		$quotes = OrderShippingModules::quote();
		if (!isset($onePageCheckout->onePage['info']['shipping']['id']) || (isset($onePageCheckout->onePage['info']['shipping']) && $onePageCheckout->onePage['info']['shipping'] == false && sizeof($quotes) > 1)){
			$onePageCheckout->onePage['info']['shipping'] = OrderShippingModules::getCheapestMethod();
		}
		?>

		<div class="ui-widget-content ui-corner-all">
			<div class="ui-widget-header ui-corner-all">
			<span class="ui-widget-header-text">&nbsp;<?php
				if (sizeof($quotes) > 1 && sizeof($quotes[0]) > 1){
				echo sysLanguage::get('TEXT_CHOOSE_SHIPPING_METHOD');
			}else{
				echo sysLanguage::get('TEXT_ENTER_SHIPPING_INFORMATION');
			}
				?></span>
			</div>
			<div class="ui-widget-text"><?php
			$radio_buttons = 0;
				for ($i=0, $n=sizeof($quotes); $i<$n; $i++) {

					$contents = EventManager::notifyWithReturn('CheckoutShippingMethodsAfterQuote', &$quotes[$i]);
					if (!empty($contents)){
						foreach($contents as $content){
							echo $content;
						}
					}
					if(sizeof($quotes[$i]['methods']) > 0){
						echo '<div class="smallText">';
						echo '<b>' . $quotes[$i]['module'] . '</b>';
						if (isset($quotes[$i]['icon']) && tep_not_null($quotes[$i]['icon'])){
							echo $quotes[$i]['icon'];
						}
						echo '</div>';
					}

					if (isset($quotes[$i]['error'])){
						echo '<div>' . $quotes[$i]['error'] . '</div>';
					}else{
						echo '<table cellpadding="3" cellspacing="0" border="0" width="100%">';
						for($j=0, $n2=sizeof($quotes[$i]['methods']); $j<$n2; $j++){
							$contents = EventManager::notifyWithReturn('CheckoutShippingMethodsAfterQuoteMethod', &$quotes[$i]['methods'][$j]);
							$show_method = true;
							if (!empty($contents)){
								foreach($contents as $content){
									if ($content === false){
										$show_method = false;
										break;
									}
								}
							}
							if ($show_method === false)   continue;

							$checked = (isset($onePageCheckout->onePage['info']['shipping']['id']) && $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] == $onePageCheckout->onePage['info']['shipping']['id'] ? true : false);

							$addClass = ' ui-state-default';
							if (isset($onePageCheckout->onePage['info']['shipping']['id']) && $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] == $onePageCheckout->onePage['info']['shipping']['id']){
								$addClass = ' ui-state-active';
							}

							$methodTitle = $quotes[$i]['methods'][$j]['title'];
							$contents = EventManager::notifyWithReturn('CheckoutShippingMethodsAfterTitle', &$quotes[$i]['methods'][$j]);
							if (!empty($contents)){
								foreach($contents as $content){
									$methodTitle .= $content;
								}
							}

							if ( ($n > 1) || ($n2 > 1) ) {
								$methodSelector = tep_draw_radio_field('shipping_method', $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'], $checked);
							}else{
								$methodSelector = tep_draw_hidden_field('shipping_method', $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id']);
							}

							echo '<tr class="moduleRow shippingRow' . $addClass . '">
							<td class="smallText">' . $methodSelector . $methodTitle . '</td>
							<td class="smallText" align="right">' . $currencies->format(tep_add_tax($quotes[$i]['methods'][$j]['cost'], (isset($quotes[$i]['tax']) ? $quotes[$i]['tax'] : 0))) . '</td>
						</tr>';
						}
						echo '</table>';
					}
				}

				$contents = EventManager::notifyWithReturn('CheckoutShippingMethodsAfterList');
				if (!empty($contents)){
					foreach($contents as $content){
						echo $content;
					}
				}
				?></div>
		</div>

	                                                  <?php
 		}
}
?>
<?php
$shippingTable = ob_get_contents();
ob_end_clean();

	EventManager::attachActionResponse(array(
		'success' => true,
		'pageHtml' => $pageHtml,
		'shippingTable' => $shippingTable
	), 'json');

?>