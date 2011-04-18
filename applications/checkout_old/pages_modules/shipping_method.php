<?php
	$showStoreMethods = true;
	$contents = EventManager::notifyWithReturn('CheckoutShippingMethodsBeforeList', &$showStoreMethods);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
	
	if ($showStoreMethods === true && OrderShippingModules::modulesAreInstalled()){

		$quotes = OrderShippingModules::quote();
		if (!isset($onePageCheckout->onePage['info']['shipping']['id']) || (isset($onePageCheckout->onePage['info']['shipping']) && $onePageCheckout->onePage['info']['shipping'] == false && sizeof($quotes) > 1)){
			$onePageCheckout->onePage['info']['shipping'] = OrderShippingModules::getCheapestMethod();
		}
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
if (sizeof($quotes) > 1 && sizeof($quotes[0]) > 1) {
?>
 <tr>
  <td class="main" width="50%" valign="top"><?php echo sysLanguage::get('TEXT_CHOOSE_SHIPPING_METHOD'); ?></td>
 </tr>
<?php
} else {
?>
 <tr>
  <td class="main" width="100%"><?php echo sysLanguage::get('TEXT_ENTER_SHIPPING_INFORMATION'); ?></td>
 </tr>
<?php
}
	$radio_buttons = 0;
	for ($i=0, $n=sizeof($quotes); $i<$n; $i++) {
		if ($quotes[$i]['id'] == 'reservation'){
			continue;
		}

		$contents = EventManager::notifyWithReturn('CheckoutShippingMethodsAfterQuote', &$quotes[$i]);
		if (!empty($contents)){
			foreach($contents as $content){
				echo $content;
			}
		}
?>
 <tr>
  <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
   <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
    <td class="main" colspan="3"><b><?php echo $quotes[$i]['module']; ?></b>&nbsp;<?php if (isset($quotes[$i]['icon']) && tep_not_null($quotes[$i]['icon'])) { echo $quotes[$i]['icon']; } ?></td>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
   </tr>
<?php
if (isset($quotes[$i]['error'])) {
?>
   <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
    <td class="main" colspan="3"><?php echo $quotes[$i]['error']; ?></td>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
   </tr>
<?php
} else {
	for ($j=0, $n2=sizeof($quotes[$i]['methods']); $j<$n2; $j++) {

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

		// set the radio button to be checked if it is the method chosen
		$checked = (isset($onePageCheckout->onePage['info']['shipping']['id']) && $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] == $onePageCheckout->onePage['info']['shipping']['id'] ? true : false);
?>
   <tr class="moduleRow shippingRow<?php echo ($checked ? ' moduleRowSelected' : '');?>">
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
    <td class="main" width="75%"><?php echo $quotes[$i]['methods'][$j]['title'];
		$contents = EventManager::notifyWithReturn('CheckoutShippingMethodsAfterTitle', &$quotes[$i]['methods'][$j]);
		if (!empty($contents)){
			foreach($contents as $content){
					echo $content;
			}
		}
    ?></td>
<?php
if ( ($n > 1) || ($n2 > 1) ) {
?>
    <td class="main"><?php echo $currencies->format(tep_add_tax($quotes[$i]['methods'][$j]['cost'], (isset($quotes[$i]['tax']) ? $quotes[$i]['tax'] : 0))); ?></td>
    <td class="main" align="right"><?php echo tep_draw_radio_field('shipping', $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'], $checked); ?></td>
<?php
} else {
?>
    <td class="main" align="right" colspan="2"><?php echo $currencies->format(tep_add_tax($quotes[$i]['methods'][$j]['cost'], $quotes[$i]['tax'])) . tep_draw_hidden_field('shipping', $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id']); ?></td>
<?php
}
?>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
   </tr>
<?php
$radio_buttons++;
	}
}
?>
  </table></td>
 </tr>
<?php
	}
}
?>
</table>  
<?php

	$contents = EventManager::notifyWithReturn('CheckoutShippingMethodsAfterList');
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
?>